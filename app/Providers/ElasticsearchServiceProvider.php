<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ElasticsearchService;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ElasticsearchService::class, function ($app) {
            return new ElasticsearchService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
