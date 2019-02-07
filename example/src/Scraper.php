<?php

namespace AlexS\GuzzleDynamicPool\Example;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Psr7\Uri;
use Stringy\StaticStringy;

class Scraper
{
    private $processedUrls = [];

    /** @var int */
    private $maxLevel = null;

    /** @var array */
    private $domains;

    /** @var Client */
    private $httpClient;

    /** @var callable */
    private $onPage;

    /**
     * @param array    $domains
     * @param Client   $httpClient
     * @param callable $onPage
     */
    public function __construct(array $domains, Client $httpClient, callable $onPage)
    {
        $this->domains = $domains;
        $this->httpClient = $httpClient;
        $this->onPage = $onPage;
    }

    /**
     * @param int $maxLevel
     */
    public function setMaxLevel($maxLevel)
    {
        $this->maxLevel = $maxLevel;
    }

    public function __invoke($urlEntry, \ArrayIterator $workload)
    {
        list($url, $level) = $urlEntry;

        $uriObject = new Uri($url);

        // Is the domain allowed to crawl?
        if (!StaticStringy::endsWithAny($uriObject->getHost(), $this->domains)) {
            // Some message or event?..
            return new FulfilledPromise(null);
        }

        // Is the nesting level OK?
        if ($level > $this->maxLevel) {
            // Some message or event?..
            return new FulfilledPromise(null);
        }

        // Have we visited this URL already?
        if (in_array($url, $this->processedUrls)) {
            // Some message or event?..
            return new FulfilledPromise(null);
        }

        $pageReceiver = new PageReceiver($level);

        // Is it the right place?..
        $this->processedUrls[] = $url;

        return $this->httpClient->getAsync($url, ['on_stats' => $pageReceiver->onStats()])
            ->then($pageReceiver)
            ->then(function (PageReceiver $pageReceiver) use ($workload) {
                $page = $pageReceiver->generatePage();

                // Add to queue
                foreach ($page->getLinks() as $url) {
                    $workload->append([$url, $page->getLevel() + 1]);
                }

                return $page;
            })
            ->then($this->onPage)
            ->otherwise(function ($error) {
                echo 'Unable to process URL, see the error below' . PHP_EOL;
                echo $error . PHP_EOL;
            })
            ;
    }
}
