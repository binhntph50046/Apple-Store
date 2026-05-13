<?php

namespace App\Http\Controllers\client;

use App\Models\Order;

class OrderTrackingController
{
    // Trang theo dõi/tracking đơn hàng
    public function show($id)
    {
        $order = Order::with(['items.product', 'items.variant'])->findOrFail($id);
        return view('client.order.tracking', compact('order'));
    }

    // Trang in hóa đơn
    public function invoice($id)
    {
        $order = Order::with(['items.product', 'items.variant'])->findOrFail($id);
        return view('client.order.invoice', compact('order'));
    }
} 