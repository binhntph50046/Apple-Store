<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;


class OrderCreated implements ShouldBroadcastNow
{
    public $order;
    public function __construct($order)
    {
        $this->order = $order;
    }
    public function broadcastOn()
    {
        return new Channel('admin-notifications');
    }
    public function broadcastAs()
    {
        return 'order.created';
    }
    public function broadcastWith()
    {
        return [
            'order_id' => $this->order->id,
            'user' => $this->order->user->name ?? $this->order->shipping_name,
            'total' => $this->order->total_price,
            'url'=> route('admin.orders.show', $this->order->id),        ];
    }
    public function broadcastVia()
    {
        return ['pusher-notify'];
    }
}