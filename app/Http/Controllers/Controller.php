<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use App\Services\ElasticsearchService;
use Illuminate\Http\Request;
class Controller extends BaseController
{     /**
    * @OA\Info(
    *   title="Example API",
    *   version="1.0",
    *   @OA\Contact(
    *     email="support@example.com",
    *     name="Support Team"
    *   )
    * )
    */
  /*  protected $elasticsearch;

  
    public function __construct(ElasticsearchService $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    public function index(Request $request)
    {
        $client = $this->elasticsearch->getClient();

        // Exemple de requête Elasticsearch
        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
        ];

        try {
            $response = $client->get($params);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }*/
}
