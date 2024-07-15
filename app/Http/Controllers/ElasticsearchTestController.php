<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ElasticsearchTestController extends Controller
{
    public function testConnection()
    {
        $client = app('Elasticsearch');

        $health = $client->ping();

        return response()->json(['status' => $health ? 'connected' : 'not connected']);
    }

    public function initializeIndex()
    {
        // Initialize the index
        $this->elasticsearchService->initializeIndex('posts');

        // Fetch all existing posts from the database
        $posts = Post::all();

        // Index each post
        foreach ($posts as $post) {
            $this->elasticsearchService->indexDocument('posts', $post->id, $post->toArray());
        }

        return response()->json(['message' => 'Index initialized and data indexed']);
    }
}
