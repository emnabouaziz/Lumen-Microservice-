<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class ElasticsearchService 
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->setHosts(['localhost:9200'])->build();
    }
    public function indexExists($index)
    {
        return $this->client->indices()->exists(['index' => $index]);
    }

    public function createIndex($index)
    {
        $params = [
            'index' => $index,
            // Add more settings and mappings if needed
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
            ],
        ];

        return $this->client->indices()->create($params);
    }

    public function indexDocument($index, $id, $data)
    {
        $params = [
            'index' => $index,
            'id' => $id,
            'body' => $data
        ];

        return $this->client->index($params);
    }

    public function deleteDocument($index, $id)
    {
        $params = [
            'index' => $index,
            'id' => $id
        ];

        return $this->client->delete($params);
    }

    public function search($index, $field, $text)
    {
        $params = [
            'index' => $index,
            'body' => [
                'query' => [
                    'match' => [
                        $field => $text
                    ]
                ]
            ]
        ];

        return $this->client->search($params);
    }
}