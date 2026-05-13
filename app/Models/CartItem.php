<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    public $timestamps = false;
    use HasFactory;

    protected $fillable = ['cart_id', 'product_id', 'variant_id', 'quantity'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        // Dùng withTrashed() để vẫn load ra biến thể đã bị soft delete
        return $this->belongsTo(ProductVariant::class, 'variant_id')->withTrashed();
    }
}
