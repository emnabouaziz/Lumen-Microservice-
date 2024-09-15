<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class RedisController extends BaseController
{
    public function getData(Request $request)
    {
        // Générer une clé de cache unique basée sur les paramètres de la requête
        $param = $request->input('param');
        $cacheKey = 'data_' . $param;

        // Vérifier si la clé existe dans le cache
        if (Cache::has($cacheKey)) {
            // Données existantes en cache
            $cachedData = Cache::get($cacheKey);
            return response()->json([
                'message' => 'Data retrieved from cache',
                'data' => $cachedData,
            ]);
        }

        // Si la clé n'existe pas en cache, récupérer les données et les mettre en cache
        $data = $this->fetchDataFromDatabase($param); // Méthode fictive pour récupérer les données
        Cache::put($cacheKey, $data, 600); // Mettre en cache pour 10 minutes (600 secondes)

        return response()->json([
            'message' => 'Data fetched from database',
            'data' => $data,
        ]);
        Redis::set($key, json_encode($data));
        Redis::expire($key, 3600); 
    }

    private function fetchDataFromDatabase($param)
    {
        // Méthode fictive pour récupérer des données de la base de données en fonction du paramètre
        return [
            'param' => $param,
            'value' => 'Data from database',
        ];
    }
}
