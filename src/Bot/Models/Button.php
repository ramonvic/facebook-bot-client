<?php

namespace Umobi\Bot\Models;


abstract class Button
{
    const TYPE_WEB = "web_url";
    const TYPE_POSTBACK = "postback";
    const TYPE_CALL = "phone_number";
    const TYPE_SHARE = "element_share";
    const TYPE_ACCOUNT_LINK = "account_link";
    const TYPE_ACCOUNT_UNLINK = "account_unlink";
    const TYPE_TEXT = "text";
    const TYPE_LOCATION = "location";

    protected $type = null;
    protected $title = null;

    abstract public function getData();
}