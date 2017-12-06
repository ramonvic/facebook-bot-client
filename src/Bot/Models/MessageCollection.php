<?php

namespace Umobi\Bot\Models;


class MessageCollection implements \IteratorAggregate
{
    protected $items = [];

    protected $delayInterval = 100;
    protected $initialDelay = 500;

    public function __construct($items, $initialDelay = 500, $delayInterval = 100)
    {
        $this->items = $items;
        $this->delayInterval = $initialDelay;
        $this->initialDelay = $delayInterval;
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

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}