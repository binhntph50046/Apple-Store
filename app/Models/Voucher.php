<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'expires_at',
        'usage_limit',
        'used_count',
        'is_active',
        'per_user_limit',
        'user_id',
        'purpose',
        'description',
    ];
    protected $casts = [
        'expires_at' => 'datetime',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_voucher')
            ->withPivot('used_times')
            ->withTimestamps();
    }
    public function userVouchers()
    {
        return $this->hasMany(UserVoucher::class);
    }
}
