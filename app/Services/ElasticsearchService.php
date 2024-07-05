<?php

namespace App\Services;

use Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $hosts = [env('ELASTICSEARCH_HOSTS')];
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    public function getClient()
    {
        return $this->client;
    }
}
