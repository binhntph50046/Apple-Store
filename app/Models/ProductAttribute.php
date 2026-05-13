<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';

    protected $fillable = [
        'product_id',
        'attribute_name',
        'attribute_value',
        'hex',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function attributeType()
    {
        return $this->belongsTo(VariantAttributeType::class, 'attribute_type_id');
    }
}
