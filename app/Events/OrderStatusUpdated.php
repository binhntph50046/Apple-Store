<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class OrderStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    // biến để lưu thông tin đơn hàng và trạng thái
    public $order;
    public $status;
    public $status_text;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->status = $order->status;
        $this->status_text = $order->getStatusTextAttribute();
        
        Log::info('Cập nhật trạng thái đơn hàng', [
            'order_id' => $order->id,
            'status' => $order->status
        ]);
    }

    public function broadcastOn()
    {
        $channelName = 'orderStatus.'.$this->order->id;
        Log::info('Broadcasting on public channel', [
            'channel' => $channelName,
            'event' => 'OrderStatusUpdated',
            'data' => $this->broadcastWith()
        ]);
        return new Channel($channelName);
    }

    public function broadcastAs()
    {
        return 'OrderStatusUpdated';
    }

    public function broadcastWith()
    {
        $data = [
            'order_id' => $this->order->id,
            'status' => $this->status,
            'status_text' => $this->status_text,
            'payment_status' => $this->order->payment_status
        ];
        
        // Thêm lý do hủy nếu đơn hàng bị hủy
        if ($this->status === 'cancelled' && $this->order->cancel_reason) {
            $data['cancel_reason'] = $this->order->cancel_reason;
        }
        
        // Thêm return_id nếu đơn hàng đã hoàn
        if (in_array($this->status, ['returned', 'partially_returned'])) {
            $latestReturn = $this->order->returns()->where('status', 'approved')->latest()->first();
            if ($latestReturn) {
                $data['return_id'] = $latestReturn->id;
            }
        }
        
        Log::info('Event data being broadcast', $data);
        return $data;
    }

    // Thêm phương thức mới
    public function broadcastWhen()
    {
        return true;
    }
}
