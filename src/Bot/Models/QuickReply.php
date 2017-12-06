<?php

namespace Umobi\Bot\Models;


class QuickReply extends Message {

    public function __construct($recipient, $text, $quick_replies = array(), $notification_type = parent::NOTIFY_REGULAR)
    {
        parent::__construct($recipient, $text, false, $notification_type);
        $this->quick_replies = $quick_replies;
    }

    public function getData() {
        $result = [
            'recipient' =>  [
                'id' => $this->recipient
            ],
            'message' => [
                'text' => $this->text
            ],
            'notification_type'=> $this->notification_type
        ];
        foreach ($this->quick_replies as $qr) {
            if($qr instanceof QuickReplyButton){
                $result['message']['quick_replies'][] = $qr->getData();
            } else {
                $result['message']['quick_replies'][] = $qr;
            }
        }
        return $result;
    }
}