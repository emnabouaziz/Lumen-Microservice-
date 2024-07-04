<?php

namespace App\Services;

use Elasticsearch\ClientBuilder; // Importation de la classe ClientBuilder

class ElasticsearchService
{
    /**
     * @var \Elasticsearch\Client
     */
    protected $client; // Propriété protégée pour stocker l'instance du client Elasticsearch

    /**
     * Constructeur du service Elasticsearch.
     */
    public function __construct()
    {
        // Récupération des hôtes Elasticsearch à partir de la variable d'environnement ELASTICSEARCH_HOSTS
        $hosts = [env('ELASTICSEARCH_HOSTS')];
        
        // Création d'une instance de Client Elasticsearch avec les hôtes spécifiés
        $this->client = ClientBuilder::create()->setHosts($hosts)->build();
    }

    /**
     * Récupère l'instance du client Elasticsearch configuré.
     *
     * @return \Elasticsearch\Client
     */
    public function getClient()
    {
        return $this->client;
    }
}