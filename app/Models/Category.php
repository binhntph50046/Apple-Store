<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;
    protected $table = 'categories';
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'order',
        'status',
        'type',
        'image',
        'icon',
        'deleted_at'
    ];

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id')->withTrashed();
    }

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }

    public function specifications()
    {
        return $this->belongsToMany(Specification::class, 'category_specifications')
            ->withTimestamps();
    }

    public function attributeTypes()
    {
        return $this->belongsToMany(VariantAttributeType::class, 'category_attribute_types')
            ->withTimestamps();
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
