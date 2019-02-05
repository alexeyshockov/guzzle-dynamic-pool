<?php

namespace AlexS\GuzzleDynamicPool;

use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Promise\each_limit;

/**
 * @param iterable $initialWorkload
 * @param callable $handler
 * @param int $concurrency
 *
 * @return PromiseInterface
 */
function dynamic_pool($initialWorkload, $handler, $concurrency = 10)
{
    $workload = new \ArrayIterator();
    foreach ($initialWorkload as $item) {
        $workload->append($item);
    }

    // MapIterator is just better for readability
    $generator = new MapIterator(
        // Initial data. This object will be always passed as the second parameter to the callback below.
        $workload,
        $handler
    );

    // The "magic"
    $generator = new ExpectingIterator($generator);

    // And the concurrent runner
    return each_limit($generator, $concurrency);
}
