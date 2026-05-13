<?php

namespace App\Events;


use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    
    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'user.created';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->user->id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'url' => route('admin.users.index'), // link đến trang quản lý người dùng
            'created_at' => $this->user->created_at->toDateTimeString(),
        ];
    }
    public function broadcastVia()
    {
        return ['pusher-notify'];
    }
}
