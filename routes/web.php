<?php
/** @var \Laravel\Lumen\Routing\Router $router */
$router->group(['prefix' => 'api/v1'], function () use ($router) {
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
    $router->options('/{any:.*}', function () {
        return response(['status' => 'success'], 200);
    });
    $router->get('/api/documentation', function () {
        return response()->json(\OpenApi\scan(app()->basePath('app')));
    });
    $router->get('/docs', function () {
        return view('swagger-ui');
    });
    $router->get('/posts', 'API\V1\PostController@index');
    //$router->get('/posts/search/{query}' , 'API\V1\TestController@search');
    $router->post('/posts', 'API\V1\PostController@store');
    $router->get('/posts/soft-deleted', 'API\V1\PostController@getSoftDeleted');
    $router->get('/posts/{id:\d+}', 'API\V1\PostController@show');;
    $router->put('/posts/{id:\d+}', 'API\V1\PostController@update');
    $router->delete('/posts/{id:\d+}', 'API\V1\PostController@destroy');
    $router->put('/posts/{id:\d+}/restore', 'API\V1\PostController@restore');
    $router->delete('/posts/{id:\d+}/force', 'API\V1\PostController@forceDelete');
    $router->get('posts/search', 'API\V1\PostController@search');
    $router->get('posts/initialize-index', 'API\V1\PostController@initializeIndex');
    



    $router->get('/tags', 'API\V1\TagController@index');
    $router->get('/tags/{id}', 'API\V1\TagController@show');
    $router->post('/tags/{postId}', 'API\V1\TagController@store');
    $router->put('/tags/{id}', 'API\V1\TagController@update');
    $router->delete('/tags/{id}', 'API\V1\TagController@destroy');
    $router->post('/tags/{id}/restore', 'API\V1\TagController@restore');
    $router->delete('/tags/{id}/force', 'API\V1\TagController@forceDelete');

});


$router->get('/elasticsearch/test', 'ElasticsearchTestController@testConnection');

$router->post('/redis/set', 'RedisController@setValue');
$router->get('/test-redis-cache', 'RedisController@getData');


