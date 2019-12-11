<?php

namespace AlexS\GuzzleDynamicPool;

use ArrayIterator;

/**
 * @internal
 */
// Do not extend IteratorIterator, because it cashes the return values somehow!
class MapIterator implements \Iterator
{
    /**
     * @var ArrayIterator
     */
    private $inner;
    private $handler;

    public function __construct(ArrayIterator $inner, callable $handler)
    {
        $this->inner = $inner;
        $this->handler = $handler;
    }

    public function next()
    {
        // Cleanup current (processed) entry. We cannot unset completely, unfortunately, because then indexing will be
        // broken (and the whole execution will be broken).
        $this->valid() && $this->inner->offsetSet($this->inner->key(), null);

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
