<?php 

namespace Umobi\Bot;

use Umobi\Bot\Handlers\HandlerContract;
use Umobi\Bot\Models\Message;
use Umobi\Bot\Models\MessageCollection;

class BotClient {

    const TYPE_GET = "get";
    const TYPE_POST = "post";
    const TYPE_DELETE = "delete";

    protected $apiUrl = "https://graph.facebook.com/v2.8/";
    protected $pageToken;

    protected $handlers = [];

    public function __construct(array $config)
    {
        $this->pageToken = $config['page_token'];
    }

    public function addHandler($type, $handler) {
        $this->handlers[$type] = $handler;
    }

    public function handleMessage($senderId, $message)
    {
        if (isset($message['quick_reply']['payload'])) {
            return $this->handlePostback($senderId, $message['quick_reply']);
        } else if (isset($message['attachments']) && $message['attachments'][0]['type'] == 'location') {
            $location = $message['attachments'][0];
            if ($this->callHandler('location', $senderId, $message, $location)) {
                return ['location', $location];
            }
        } else if (is_array($message['nlp'])) {
            foreach ($message['nlp']['entities'] as $name => $entities) {
                $entity = $entities[0];
                if ($entity['confidence'] > 0.6 && $this->callHandler($name, $senderId, $message, $entity)) {
                    return [$name, $entity];
                }
            }
        }

        return $this->sendFallbackMessage($senderId);
    }

    public function handlePostback($senderId, $message)
    {
        if ($message['payload'] && ($payload = unserialize(base64_decode($message['payload'])))) {
            if ($this->callHandler($payload['action'], $senderId, $message, $payload)) {
                return [$payload['action'], $payload];
            }
        }

        return $this->sendFallbackMessage($senderId);
    }

    public function sendFallbackMessage($senderId)
    {
        $message = new Message($senderId, "Não entendemos sua mensagem 😳, ainda estamos aprendendo a ler.");
        $this->call('me/messages', $message->getData());
        usleep(500);
        $message = new Message($senderId, "Mas não se preocupe, nosso professor irá te ajudar.");
        $this->call('me/messages', $message->getData());
        return ['general', null];
    }

    public function processhandler($name, $senderId, $message, $payload) {
        $messageResponse = null;
        if (isset($this->handlers[$name])) {
            $handler = $this->handlers[$name];
            if (is_callable($handler)) {
                $messageResponse = $handler($senderId, $message, $payload);
            } elseif ($handler instanceof HandlerContract) {
                $messageResponse = $handler->handle($senderId, $message, $payload);
            }
        }

        return $messageResponse;
    }

    public function callHandler($name, $senderId, $message, $payload) {
        $messageResponse = $this->processhandler($name, $senderId, $message, $payload);

        if ($messageResponse instanceof  MessageCollection) {
            sleep($messageResponse->getInitialDelay());

            foreach ($messageResponse as $message) {
                $this->call('me/messages', $message->getData());
                sleep($messageResponse->getDelayInterval());
            }
        }

        if ($messageResponse instanceof Message) {
            return $this->call('me/messages', $messageResponse->getData());
        }

        return !!$messageResponse;
    }

    public function sendTypingOn($senderId)
    {
        return $this->call('me/messages', [
            'recipient' => [ 'id' => $senderId ],
            'sender_action' => 'typing_on'
        ]);
    }

    public function sendTypingOff($senderId)
    {
        return $this->call('me/messages', [
            'recipient' => [ 'id' => $senderId ],
            'sender_action' => 'typing_off'
        ]);
    }

    public function call($url, $data, $type = self::TYPE_POST)
    {
        $data['access_token'] = $this->pageToken;
        $headers = [
            'Content-Type: application/json',
        ];
        if ($type == self::TYPE_GET) {
            $url .= '?'.http_build_query($data);
        }
        $process = curl_init($this->apiUrl.$url);
        curl_setopt($process, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($process, CURLOPT_HEADER, false);
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        if($type == self::TYPE_POST || $type == self::TYPE_DELETE) {
            curl_setopt($process, CURLOPT_POST, 1);
            curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        if ($type == self::TYPE_DELETE) {
            curl_setopt($process, CURLOPT_CUSTOMREQUEST, "DELETE");
        }
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        $return = curl_exec($process);
        curl_close($process);

        return json_decode($return, true);
    }
}
?>