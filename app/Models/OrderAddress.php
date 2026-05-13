<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    protected $table = 'order_address';
    
    protected $fillable = [
        'order_id',
        'full_name',
        'phone_number',
        'address',
        'note'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
} 