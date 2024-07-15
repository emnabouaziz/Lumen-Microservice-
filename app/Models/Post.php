<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use JeroenG\Explorer\Application\Explored;
use JeroenG\Explorer\Application\IndexSettings;
class Post extends Model 
//implements Explored, IndexSettings
{
    use SoftDeletes;
    use Searchable;

    /**
     * Get the indexable data array for the model.
     *
     * @return array
     */
    protected $fillable = ['title', 'content', 'is_default', 'can_delete'];
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            try {
                $elasticsearchService = App::make(ElasticsearchService::class);
                \Log::info('Indexing document for post ID: ' . $model->id);
                $response = $elasticsearchService->indexDocument('posts', $model->id, $model->toArray());
                \Log::info('Indexing response: ', (array) $response);
            } catch (\Exception $e) {
                \Log::error('Error indexing document: ' . $e->getMessage());
            }
        });
        
        static::deleted(function ($model) {
            try {
                $elasticsearchService = App::make(ElasticsearchService::class);
                \Log::info('Deleting document for post ID: ' . $model->id);
                $response = $elasticsearchService->deleteDocument('posts', $model->id);
                \Log::info('Deleting response: ', (array) $response);
            } catch (\Exception $e) {
                \Log::error('Error deleting document: ' . $e->getMessage());
            }
        });
    }
    public function toSearchableArray()
    {
       

        return ["title"=> $this->title,];
    }
    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }/*
    public function indexSettings(): array
    {
        return [
            'analysis' => [
                'analyzer' => [
                    'standard_lowercase' => [
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => ['lowercase'],
                    ],
                ],
            ],
        ];
    }
   

    public function mappableAs(): array
    {
        return [
            "title" => [
                "type" => "text",
                "analyzer" =>"custom_keyword_analyzer",
                "search_analyzer" => "custom_keyword_analyzer",
                "fields" => [
                    "keywords" => [
                        "type" => "keywords",
                        "ignore_above" => 256,
                    ],
                ],
            ],
           
        ];
    }
*/
   
    
   
    
}