<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Redis;

class RedisTest extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php'; // Chemin vers votre fichier app.php de bootstrap
    }

    /** @test */
    public function it_can_set_and_get_key_from_redis()
    {
        // Set a key
        Redis::set('test_key', 'test_value');

        // Get the key
        $value = Redis::get('test_key');

        // Assert that the retrieved value is correct
        $this->assertEquals('test_value', $value);
    }
}
