<?php

namespace Umobi\Bot\Models;


class QuickReplyButton extends Button
{

    protected $payload = null;
    protected $image_url = false;

    public function __construct($type, $title = '', $payload = null, $image_url = null)
    {
        $this->type = $type;
        $this->title = $title;
        $this->payload = $payload;
        $this->image_url = $image_url;
    }

    public function getData()
    {
        $result = [
            'content_type' => $this->type
        ];
        switch($this->type)
        {
            case self::TYPE_LOCATION:
                $result['image_url'] = $this->image_url;
                break;
            case self::TYPE_TEXT:
                $result['payload'] = base64_encode(serialize($this->payload));
                $result['title'] = $this->title;
                $result['image_url'] = $this->image_url;
                break;
        }
        return $result;
    }
}