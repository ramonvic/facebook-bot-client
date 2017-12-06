<?php

namespace Umobi\Bot\Models;


class StructuredMessage extends Message
{

    const TYPE_BUTTON = "button";
    const TYPE_GENERIC = "generic";
    const TYPE_LIST = "list";


    protected $type = null;
    protected $title = null;
    protected $subtitle = null;
    protected $elements = [];
    protected $buttons = [];
    protected $recipient_name = null;

    protected $top_element_style = 'large';
    protected $image_aspect_ratio = 'horizontal';

    public function __construct($recipient=null, $type, $data, $quick_replies = array(), $notification_type = parent::NOTIFY_REGULAR)
    {
        $this->recipient = $recipient;
        $this->type = $type;
        $this->quick_replies = $quick_replies;
        $this->notification_type = $notification_type;

        switch ($type) {
            case self::TYPE_BUTTON:
                $this->title = $data['text'];
                $this->buttons = $data['buttons'];
                break;
            case self::TYPE_GENERIC:
                $this->elements = $data['elements'];
                if (isset($data['image_aspect_ratio'])) {
                    $this->image_aspect_ratio = $data['image_aspect_ratio'];
                }
                break;
            case self::TYPE_LIST:
                $this->elements = $data['elements'];
                //allowed is a sinle button for the whole list
                if (isset($data['buttons'])) {
                    $this->buttons = $data['buttons'];
                }
                //the top_element_style indicate if the first item is featured or not.
                //default is large
                if (isset($data['top_element_style'])) {
                    $this->top_element_style = $data['top_element_style'];
                }
                //if the top_element_style is large the first element image_url MUST be set.
                if ($this->top_element_style == 'large' && (!isset($this->elements[0]->getData()['image_url']) || $this->elements[0]->getData()['image_url'] == '')) {
                    $message = 'Facbook require the image_url to be set for the first element if the top_element_style is large. set the image_url or change the top_element_style to compact.';
                    throw new \Exception($message);
                }
                break;
        }
    }


    public function getData()
    {
        $result = [
            'attachment' => [
                'type' => 'template',
                'payload' => [
                    'template_type' => $this->type
                ]
            ]
        ];
        if (is_array($this->quick_replies)) {
            foreach ($this->quick_replies as $qr) {
                if ($qr instanceof QuickReplyButton) {
                    $result['quick_replies'][] = $qr->getData();
                }
            }
        }
        switch ($this->type)
        {
            case self::TYPE_BUTTON:
                $result['attachment']['payload']['text'] = $this->title;
                $result['attachment']['payload']['buttons'] = [];
                foreach ($this->buttons as $btn) {
                    $result['attachment']['payload']['buttons'][] = $btn->getData();
                }
                break;
            case self::TYPE_GENERIC:
                $result['attachment']['payload']['elements'] = [];
                $result['attachment']['payload']['image_aspect_ratio'] = $this->image_aspect_ratio;
                foreach ($this->elements as $btn) {
                    $result['attachment']['payload']['elements'][] = $btn->getData();
                }
                break;
            case self::TYPE_LIST:
                $result['attachment']['payload']['elements'] = [];
                $result['attachment']['payload']['top_element_style'] = $this->top_element_style;
                //list items button
                foreach ($this->elements as $btn) {
                    $result['attachment']['payload']['elements'][] = $btn->getData();
                }
                //the whole list button
                foreach ($this->buttons as $btn) {
                    $result['attachment']['payload']['buttons'][] = $btn->getData();
                }
                break;
        }
        if ($this->recipient) {
            return [
                'recipient' =>  [
                    'id' => $this->recipient
                ],
                'message' => $result,
                'notification_type'=> $this->notification_type
            ];
        } else {
            //share_contents only
            return [
                'attachment' => $result['attachment']
            ];
        }
    }
}