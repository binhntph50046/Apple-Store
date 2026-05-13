<?php

namespace App\Events;

use App\Models\OrderReturn;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class OrderReturnCreated implements ShouldBroadcastNow
{
    public $return;

    public function __construct(OrderReturn $return)
    {
        $this->return = $return;
    }

    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }

    public function broadcastAs()
    {
        return 'return.created';
    }

    public function broadcastWith()
    {
        return [
            'return_id' => $this->return->id,
            'user' => $this->return->user->name,
            'order_code' => $this->return->order->order_code ?? ('ĐH' . $this->return->order->id),
            'reason' => $this->return->reason,
            'url' => route('admin.order-returns.show', $this->return->id),
            'created_at' => $this->return->created_at->toDateTimeString(),
        ];
    }

    public function broadcastVia()
    {
        return ['pusher-notify'];
    }
}