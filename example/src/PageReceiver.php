<?php

namespace AlexS\GuzzleDynamicPool\Example;

use GuzzleHttp\TransferStats;
use Psr\Http\Message\ResponseInterface;

class PageReceiver
{
    /** @var TransferStats */
    private $stats;

    /** @var ResponseInterface */
    private $response;

    /** @var string */
    private $html;

    private $currentLevel;

    public function __construct($currentLevel)
    {
        $this->currentLevel = $currentLevel;
    }

    public function onStats()
    {
        return function (TransferStats $stats) {
            $this->stats = $stats;
        };
    }

    public function __invoke(ResponseInterface $response)
    {
        $this->response = $response;

        $this->html = $this->response->getBody()->getContents();

        try {
            $this->response->getBody()->rewind();
        } catch (\RuntimeException $exception) {
            // Rewind is not allowed, skipping
        }

        return $this;
    }

    public function generatePage()
    {
        return new Page($this->currentLevel, $this->response, $this->html, $this->stats);
    }
}
