<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specification extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'status',
        'category_ids'
    ];

    protected $casts = [
        'category_ids' => 'array'
    ];

    public function categories()
    {
        return $this->hasManyThrough(
            Category::class,
            Product::class,
            'id',
            'id',
            'id',
            'category_id'
        );
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_specifications')
            ->withPivot('value')
            ->withTimestamps();
    }
} 