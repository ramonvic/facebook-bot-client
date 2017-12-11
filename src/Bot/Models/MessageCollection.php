<?php

namespace Umobi\Bot\Models;


class MessageCollection implements \IteratorAggregate
{
    protected $items = [];

    protected $delayInterval = 0.1;
    protected $initialDelay = 0.5;
    protected $payload = [];

    public function __construct($items, $initialDelay = 0.5, $delayInterval = 0.1, $payload = [])
    {
        $this->items = $items;
        $this->delayInterval = $initialDelay;
        $this->initialDelay = $delayInterval;
        $this->payload = $payload;
    }

    public function getDelayInterval()
    {
        return $this->delayInterval;
    }

    public function getInitialDelay()
    {
        return $this->initialDelay;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getFormattedPayload()
    {
        return $this->payload;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}