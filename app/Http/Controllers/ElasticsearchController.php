<?php
// app/Http/Controllers/ElasticsearchController.php

namespace App\Http\Controllers;

use App\Services\ElasticsearchService;
use Illuminate\Http\Request;

class ElasticsearchController extends Controller
{
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function createIndex(Request $request)
    {
        $indexName = $request->input('index_name');

        try {
            $response = $this->elasticsearchService->getClient()->indices()->create(['index' => $indexName]);
            return response()->json(['message' => 'Index created', 'response' => $response]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function indexDocument(Request $request)
    {
        $indexName = $request->input('index_name');
        $document = $request->input('document');

        try {
            $response = $this->elasticsearchService->getClient()->index(['index' => $indexName, 'body' => $document]);
            return response()->json(['message' => 'Document indexed', 'response' => $response]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Ajoutez d'autres méthodes pour les opérations Elasticsearch selon vos besoins
}
