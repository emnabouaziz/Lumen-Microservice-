<?php

namespace App\Services;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Log;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $hosts = [env('ELASTICSEARCH_HOSTS', 'localhost:9200')];

        // Création d'une instance de Client Elasticsearch
        $this->client = new Client(['hosts' => $hosts]);
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

    /**
     * Exemple de méthode pour tester la connexion Elasticsearch.
     *
     * @return array
     */
    public function testConnection()
    {
        try {
            $info = $this->client->info();
            return ['success' => true, 'message' => 'Connexion Elasticsearch réussie.', 'info' => $info];
        } catch (\Exception $e) {
            Log::error('Erreur de connexion Elasticsearch: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur de connexion Elasticsearch. Vérifiez les logs pour plus de détails.'];
        }
    }
}
