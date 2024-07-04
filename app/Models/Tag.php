<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name'];
    protected $primaryKey = 'tag_id';
    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

}
