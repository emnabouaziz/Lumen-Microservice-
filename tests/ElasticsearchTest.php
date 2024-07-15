<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchTest extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    /** @test */
    public function test_elasticsearch_connection()
    {
        $client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();

        $health = $client->ping();

        $this->assertTrue($health, 'Elasticsearch is not connected');
    }
}
