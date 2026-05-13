<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'title', 'slug', 'content', 'image', 'category_id', 'author_id', 'status',
    ];

    // Một blog thuộc về một user (author)
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    // Một blog thuộc về một category
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
