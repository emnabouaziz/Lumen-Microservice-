<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'is_default', 'can_delete'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }
}
