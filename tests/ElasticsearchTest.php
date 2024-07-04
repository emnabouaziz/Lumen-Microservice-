<?php
// tests/ElasticsearchTest.php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

class ElasticsearchTest extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testElasticsearchConnection()
    {
        $response = $this->get('/aa'); // Adjust the route according to your setup

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'PostgreSQL connected successfully', // Adjust the expected message
                 ]);
    }
}
