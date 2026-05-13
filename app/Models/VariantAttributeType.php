<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantAttributeType extends Model
{
    use SoftDeletes;

    protected $table = 'variant_attribute_types';

    protected $fillable = [
        'name',
        'status',
        'category_ids',
    ];

    protected $casts = [
        'category_ids' => 'array',
    ];

    public function attributeValues()
    {
        return $this->hasMany(VariantAttributeValue::class, 'attribute_type_id');
    }

    public function variants()
    {
        return $this->hasManyThrough(
            ProductVariant::class,
            VariantAttributeValue::class,
            'attribute_type_id',
            'id',
            'variant_id'
        );
    }
}
