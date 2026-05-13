<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class NewChatStarted implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('admin.notifications');
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->user->id,
            'name' => $this->user->name,
            'avatar' => $this->user->avatar,
            'time' => now()->diffForHumans(),
        ];
    }

    public function broadcastAs()
    {
        return 'new-chat';
    }
}
