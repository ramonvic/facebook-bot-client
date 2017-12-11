<?php

namespace Umobi\Bot\Models;

class Message
{
    const NOTIFY_REGULAR = "REGULAR";
    const NOTIFY_SILENT_PUSH = "SILENT_PUSH";
    const NOTIFY_NO_PUSH = "NO_PUSH";

    protected $recipient = null;

    protected $text = null;

    protected $user_ref = false;

    protected $notification_type = null;

    protected $quick_replies = null;

    protected $payload = [];

    public function __construct($recipient, $text, $user_ref = false, $notification_type = self::NOTIFY_REGULAR, $payload = [])
    {
        $this->recipient = $recipient;
        $this->text = $text;
        $this->user_ref = $user_ref;
        $this->notification_type = $notification_type;
        $this->payload = $payload;
    }

    public function setFormattedPayload($payload)
    {
        $this->payload = $payload;
    }

    public function getFormattedPayload()
    {
        return $this->payload;
    }

    public function getData()
    {
        return [
            'recipient' => $this->user_ref ? ['user_ref' => $this->recipient] : ['id' => $this->recipient],
            'message' => [
                'text' => $this->text
            ],
            'notification_type'=> $this->notification_type
        ];
    }
}