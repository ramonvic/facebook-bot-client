<?php

namespace Umobi\Bot\Models;

class Message
{
    const NOTIFY_REGULAR = "REGULAR";
    const NOTIFY_SILENT_PUSH = "SILENT_PUSH";
    const NOTIFY_NO_PUSH = "NO_PUSH";

    /**
     * @var null|string
     */
    protected $recipient = null;

    /**
     * @var null|string
     */
    protected $text = null;

    /**
     * @var bool
     */
    protected $user_ref = false;

    /**
     * @var null|string
     */
    protected $notification_type = null;

    /**
     * @var null|array
     */
    protected $quick_replies = null;

    /**
     * Message constructor.
     *
     * @param string $recipient
     * @param string $text
     * @param string $notification_type - REGULAR, SILENT_PUSH, or NO_PUSH
     * https://developers.facebook.com/docs/messenger-platform/send-api-reference
     */
    public function __construct($recipient, $text, $user_ref = false, $notification_type = self::NOTIFY_REGULAR)
    {
        $this->recipient = $recipient;
        $this->text = $text;
        $this->user_ref = $user_ref;
        $this->notification_type = $notification_type;
    }

    /**
     * Get message data
     *
     * @return array
     */
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