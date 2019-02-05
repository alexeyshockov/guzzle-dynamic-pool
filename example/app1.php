<?php

use function AlexS\GuzzleDynamicPool\dynamic_pool;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

require __DIR__ . '/../vendor/autoload.php';

// Just for the example, to show the dynamic addition
$next = new \SplDoublyLinkedList();
$next->push('http://yandex.ru');
$next->push('http://yahoo.com');
$next->push('http://rambler.com');
$next->push('http://bbc.co.uk');
$next->push('http://facebook.com');
$next->push('http://feedly.com');
$next->push('http://amazon.com');
$next->push('http://amazon.de');

// You can use any config options and middlewares here, if you want
$httpClient = new Client();

// A handler is an arbitrary callable that returns a promise
$handler = function (
    // The first parameter is your workload's item, can be anything
    $url,
    // The second parameter (optional) is the workload itself. You can add/remove
    // items in it to control the execution flow.
    \ArrayIterator $workload
) use ($httpClient, $next) {
    return $httpClient->getAsync($url)
        ->then(function (ResponseInterface $response) use ($url, $workload, $next) {
            // The status code for example
            echo $url . ': ' . $response->getStatusCode() . PHP_EOL;

            // New requests
            $workload->append($next->shift());
            $workload->append($next->shift());
        });
};

$pool = dynamic_pool(
    // It's important to have at least one item initially
    ['http://google.com'],
    $handler,
    5
);

$pool->wait();
