<?php

use Illuminate\Support\Facades\DB;

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/



$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->get('/elasticsearch/test', 'Controller@index');

$router->get('/test', function () use ($router) {
    try {
        $results = DB::select('SELECT version()');
        return response()->json(['message' => 'PostgreSQL connected successfully', 'result' => $results]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
$router->get('/api/documentation', function () {
    return view('swagger-lume::index');
});

