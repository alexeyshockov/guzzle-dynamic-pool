<?php

namespace AlexS\GuzzleDynamicPool;

/**
 * @internal
 */
// Do not extend IteratorIterator, because it cashes the return values somehow!
class MapIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $inner;
    private $handler;

    public function __construct(\Iterator $inner, callable $handler)
    {
        $this->inner = $inner;
        $this->handler = $handler;
    }

    public function next()
    {
        $this->inner->next();
    }

    public function current()
    {
        return call_user_func($this->handler, $this->inner->current(), $this->inner);
    }

    public function rewind()
    {
        $this->inner->rewind();
    }

    public function key()
    {
        return $this->inner->key();
    }

    public function valid()
    {
        return $this->inner->valid();
    }
}
