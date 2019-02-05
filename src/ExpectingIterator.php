<?php

namespace AlexS\GuzzleDynamicPool;

/**
 * @internal
 */
// Do not extend IteratorIterator, because it cashes the return values somehow!
class ExpectingIterator implements \Iterator
{
    /**
     * @var \Iterator
     */
    private $inner;
    private $wasValid;

    public function __construct(\Iterator $inner)
    {
        $this->inner = $inner;
    }

    public function next()
    {
        if (!$this->wasValid && $this->valid()) {
            // Just do nothing, because the inner iterator has became valid
        } else {
            $this->inner->next();
        }

        $this->wasValid = $this->valid();
    }

    public function current()
    {
        return $this->inner->current();
    }

    public function rewind()
    {
        $this->inner->rewind();

        $this->wasValid = $this->valid();
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
