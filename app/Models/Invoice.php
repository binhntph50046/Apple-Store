<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Invoice extends Model
{
    protected $fillable = [
        'order_id', 'invoice_code', 'total', 'issued_by', 'issued_at', 'file_path'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
} 