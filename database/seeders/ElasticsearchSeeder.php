<?php

namespace Database\Seeders;
use App\Services\ElasticsearchService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ElasticsearchSeeder extends Seeder
{    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        if (!$this->elasticsearchService->indexExists('index')) {
            $this->elasticsearchService->createIndex('index');
        }

        $this->elasticsearchService->indexDocument('posts', 1, ['title' => 'First Post', 'content' => 'This is the first post.']);
        $this->elasticsearchService->indexDocument('posts', 2, ['title' => 'Second Post', 'content' => 'This is the second post.']);
    
    }
}
