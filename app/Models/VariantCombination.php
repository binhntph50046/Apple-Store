<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VariantCombination extends Model
{
    use SoftDeletes;

    protected $table = 'variant_combinations';

    protected $fillable = [
        'variant_id',
        'attribute_value_id'
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function attributeValue()
    {
        return $this->belongsTo(VariantAttributeValue::class, 'attribute_value_id');
    }
}
