<?php

namespace AlexS\GuzzleDynamicPool\Example;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DomCrawler\Crawler;

class Page
{
    private $level;

    /** @var TransferStats */
    private $stats;

    /** @var Crawler */
    private $domCrawler;

    /** @var string */
    private $html;

    /** @var ResponseInterface */
    private $response;

    /**
     * @param int $level
     * @param ResponseInterface $response
     * @param string $html
     * @param TransferStats $stats
     */
    public function __construct($level, ResponseInterface $response, $html, TransferStats $stats)
    {
        $this->level = $level;
        $this->stats = $stats;
        $this->response = $response;
        $this->html = $html;

        $this->domCrawler = new Crawler($html, $this->getEffectiveUrl());
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return TransferStats
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @return Generator string[]
     */
    public function getLinks()
    {
        $links = $this->domCrawler->filterXPath('//a')->links();
        foreach ($links as $link) {
            // TODO Filter # links
            yield $link->getUri();
        }
    }

    public function getEffectiveUrl()
    {
        return $this->stats->getEffectiveUri();
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->html;
    }
}
