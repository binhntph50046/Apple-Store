<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageView extends Model
{
    protected $table = 'page_views';
    protected $fillable = [
        'session_id',
        'user_id',
        'url',
        'ip_address',
        'user_agent',
        'duration',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
