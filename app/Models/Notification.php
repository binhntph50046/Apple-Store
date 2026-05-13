<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\SoftDeletes;


class Notification extends DatabaseNotification
{
    use SoftDeletes;
    protected $table = 'notifications';

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];
}
