<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlashSaleItem extends Model
{
    protected $table = 'flash_sale_items';

    protected $fillable = [
        'flash_sale_id',
        'product_variant_id',
        'count',
        'discount',
        'discount_type',
        'buy_limit',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function flashSale()
    {
        return $this->belongsTo(FlashSale::class, 'flash_sale_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
