<?php
namespace App\Events;

use App\Models\ChMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class ChatCreated implements ShouldBroadcastNow
{
    public $message;

    public function __construct(ChMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'message.created';
    }

    public function broadcastWith()
    {
        return [
            'from_id' => $this->message->from_id,
            'to_id'   => $this->message->to_id,
            'user_name'  => optional($this->message->fromUser)->name,
            'message'      => $this->message->body,
            'url'       => route('admin.livechat.index'),
        ];
    }

    public function broadcastVia()
    {
        return ['pusher-notify'];
    }

}