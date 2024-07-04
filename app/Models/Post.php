<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['title', 'content'];

    protected $primaryKey = 'post_id';

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}