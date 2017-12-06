<?php

namespace Umobi\Bot\Handlers;


use Umobi\Bot\Models\Message;

interface HandlerContract
{
    /**
     * @param $sendeId
     * @param $message
     * @param $entity
     * @return Message
     */
    public function handle($sendeId, $message, $entity);
}