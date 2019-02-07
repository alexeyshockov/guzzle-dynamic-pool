<?php

namespace AlexS\GuzzleDynamicPool\Example;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use function AlexS\GuzzleDynamicPool\dynamic_pool;

require __DIR__ . '/../vendor/autoload.php';

$initialUrl = $argv[1];
$maxLevel = $argv[2];
$concurrency = $argv[3];
$domains = [
    (new Uri($initialUrl))->getHost()
];

// You can use any config options and middlewares here, if you want
$httpClient = new Client();

$writer = function (Page $page) {
    echo '[Level '. $page->getLevel() .']' . $page->getEffectiveUrl() . ': ' . $page->getStatusCode() . PHP_EOL;
};

// A handler is an arbitrary callable that returns a promise
$handler = new Scraper($domains, $httpClient, $writer);
$handler->setMaxLevel($maxLevel);

$pool = dynamic_pool(
    // It's important to have at least one item initially
    [
        [$initialUrl, 1]
    ],
    $handler,
    $concurrency
);

$pool->wait();
