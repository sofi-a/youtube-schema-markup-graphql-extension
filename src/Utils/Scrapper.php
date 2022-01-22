<?php

namespace WpGraphQL\YoutubeSchemaMarkup\Utils;

use GuzzleHttp\Client;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;

class Scrapper
{
    private $client;
    private $dom;
    private $html;

    public function __construct($url)
    {
        $this->client = new Client;
        $this->dom = new Dom;
        $this->dom->setOptions(
            // this is set as the global option level.
            (new Options())
                ->setRemoveScripts(false)
        );

        $response = $this->client->request('GET', $url);

        if ($response->getStatusCode() === 200) {
            $this->html = $response->getBody()->__toString();
            $this->dom->loadStr($this->html);
        }
    }

    public function getSchemaMarkup()
    {
        return $this->dom->find('script[type=application/ld+json]')->innerHtml;
    }
}
