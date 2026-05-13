<?php

namespace App\Http\Controllers\admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\OrderReturn;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Events\OrderStatusUpdated;
use App\Models\User;
use App\Notifications\AdminDatabaseNotification;

class OrderReturnController extends Controller
{
    // Hiển thị danh sách yêu cầu hoàn hàng
    public function index()
    {
        // Lấy tất cả yêu cầu hoàn hàng, mới nhất lên đầu
        $returns = OrderReturn::with(['order', 'user', 'admin'])->latest()->paginate(20);
        return view('admin.order_returns.index', compact('returns'));
    }
    // Hiển thị chi tiết một yêu cầu hoàn hàng
    public function show($id)
    {
        $return = OrderReturn::with(['order', 'user', 'admin'])->findOrFail($id);
        return view('admin.order_returns.show', compact('return'));
    }
    // Duyệt yêu cầu hoàn hàng
    public function approve(Request $request, $id)
    {
        $request->validate([
            'refund_proof_image' => 'required|image|max:2048', // Validate hình ảnh, max 2MB
            'refund_note' => 'nullable|string|max:500',
        ]);

        $return = OrderReturn::with(['items.orderItem.product', 'items.orderItem.variant'])->findOrFail($id);

        // Upload hình ảnh chứng từ
        if ($request->hasFile('refund_proof_image')) {
            $image = $request->file('refund_proof_image');
            $imageName = time() . '_' . $return->id . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/returns'), $imageName);
            $return->refund_proof_image = 'uploads/returns/' . $imageName;
        }

        // Lưu ghi chú hoàn tiền
        if ($request->filled('refund_note')) {
            $return->refund_note = $request->refund_note;
        }

        // Tính tổng tiền hoàn lại
        $refundAmount = 0;
        $restockArr = $request->input('restock', []);

        foreach ($return->items as $item) {
            $refundAmount += $item->orderItem->price * $item->quantity;
            $restock = isset($restockArr[$item->id]) && $restockArr[$item->id] == '1';
            $item->restock = $restock;
            $item->save();

            if ($restock) {
                // Cộng lại stock cho variant tương ứng
                if ($item->orderItem->variant) {
                    $variant = $item->orderItem->variant;
                    $variant->increment('stock', $item->quantity);
                } else {
                    // Fallback: cộng lại stock cho sản phẩm nếu không có variant
                    $item->orderItem->product->increment('stock', $item->quantity);
                }
            }
        }

        // Cập nhật trạng thái hoàn hàng
        $oldStatus = $return->status;
        $return->update([
            'status' => 'approved',
            'admin_id' => Auth::id(),
            'processed_at' => now(),
            'refunded_at' => now(), // Thêm thời gian hoàn tiền
            'refund_amount' => $refundAmount, // Thêm số tiền hoàn trả
        ]);

        // Giảm total_sold cho các sản phẩm bị hoàn
        if ($oldStatus !== 'approved') {
            foreach ($return->items as $item) {
                $product = $item->orderItem->product;
                if ($product) {
                    $product->safeDecrementTotalSold($item->quantity);
                }
            }
        }

        // Cập nhật số tiền đã hoàn vào đơn hàng
        $order = $return->order;
        $order->refunded_amount = ($order->refunded_amount ?? 0) + $refundAmount;
        $order->total_price = $order->total_price - $refundAmount;

        // Tính tổng số lượng sản phẩm trong đơn và số lượng đã hoàn
        $totalOrderQty = $order->items->sum('quantity');
        $totalReturnedQty = 0;
        foreach ($order->returns as $r) {
            if ($r->status == 'approved') {
                foreach ($r->items as $item) {
                    $totalReturnedQty += $item->quantity;
                }
            }
        }

        if ($totalReturnedQty >= $totalOrderQty) {
            $order->status = 'returned';
        } elseif ($totalReturnedQty > 0) {
            $order->status = 'partially_returned';
        }
        $order->save();

        event(new OrderStatusUpdated($order));

        // Gửi thông báo cho người dùng
        if ($user = $order->user) {
            $data = [
                'type' => 'order_return_approved',
                'title' => 'Yêu cầu trả hàng được chấp thuận',
                'message' => "Yêu cầu trả hàng cho đơn hàng #{$order->order_code} đã được chấp thuận.",
                'url' => route('order.returns.show', ['order' => $order->id, 'return' => $return->id]),
                'order_id' => $order->id,
            ];
            $user->notify(new AdminDatabaseNotification($data));
        }

        return redirect()->route('admin.order-returns.index')
            ->with('success', 'Đã duyệt yêu cầu hoàn hàng và lưu chứng từ hoàn tiền. Đã hoàn lại ' . number_format($refundAmount) . ' VNĐ cho khách.');
    }
    // Từ chối yêu cầu hoàn hàng
    public function reject($id)
    {
        $return = OrderReturn::with(['items.orderItem.product', 'items.orderItem.variant'])->findOrFail($id);

        // Nếu đang từ trạng thái approved sang rejected, tăng lại total_sold và trừ lại stock
        $oldStatus = $return->status;
        if ($oldStatus === 'approved') {
            foreach ($return->items as $item) {
                $product = $item->orderItem->product;
                if ($product) {
                    $product->safeIncrementTotalSold($item->quantity);
                }

                // Trừ lại stock nếu trước đó đã cộng lại (restock = true)
                if ($item->restock) {
                    if ($item->orderItem->variant) {
                        $variant = $item->orderItem->variant;
                        $variant->decrement('stock', $item->quantity);
                    } else {
                        // Fallback: trừ lại stock cho sản phẩm nếu không có variant
                        $item->orderItem->product->decrement('stock', $item->quantity);
                    }
                }
            }
        }

        $return->update([
            'status' => 'rejected',
            'admin_id' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Gửi thông báo cho người dùng
        $order = $return->order;
        if ($user = $order->user) {
            $data = [
                'type' => 'order_return_rejected',
                'title' => 'Yêu cầu trả hàng bị từ chối',
                'message' => "Yêu cầu trả hàng cho đơn hàng #{$order->order_code} đã bị từ chối.",
                'url' => route('order.returns.show', ['order' => $order->id, 'return' => $return->id]),
                'order_id' => $order->id,
            ];
            $user->notify(new AdminDatabaseNotification($data));
        }
        return redirect()->route('admin.order-returns.index')->with('success', 'Đã từ chối yêu cầu hoàn hàng.');
    }
}
