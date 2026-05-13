<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;
use App\Mail\InvoicePdfMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\OrderAddress;
use App\Models\User;
use App\Notifications\AdminDatabaseNotification;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('shipping_name', 'like', "%$search%")
                    ->orWhere('shipping_email', 'like', "%$search%")
                    ->orWhere('order_code', 'like', "%$search%");
            });
        }

        // Lọc theo trạng thái đơn hàng
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Lọc theo trạng thái thanh toán
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->with(['items.product' => function ($query) {
            $query->withTrashed();
        }, 'items.variant'])
            ->latest()
            ->paginate(10)
            ->appends($request->all());

        return view('admin.orders.index', compact('orders'));
    }
    public function show($id)
    {
        $order = Order::withTrashed()->with(['user', 'items.product', 'items.variant'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }
    public function updateStatus(Request $request, $id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        // Validate lý do hủy nếu chuyển sang trạng thái cancelled
        if ($request->status === 'cancelled') {
            $request->validate([
                'cancel_reason' => 'required|string|max:255'
            ]);
        }

        // Nếu đơn đã bị huỷ hoặc đã hoàn thành thì không cho cập nhật nữa
        if (in_array($order->status, ['cancelled', 'completed'])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Đơn hàng đã ' . ($order->status === 'cancelled' ? 'bị hủy' : 'hoàn thành') . ', không thể cập nhật trạng thái.'
                ]);
            }
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'Không thể cập nhật trạng thái vì đơn hàng đã ' . ($order->status === 'cancelled' ? 'bị hủy' : 'hoàn thành') . '.');
        }
        // danh sách các trạng thái có thể thay đổi từ trạng thái hiện tại
        $status = [
            "pending" => ["confirmed", "cancelled"],
            "confirmed" => ["preparing"],
            "preparing" => ["shipping"],
            "shipping" => ["delivered"],
            "delivered" => [],
            "completed" => [],
            "cancelled" => []
        ];
        $new_status = $request->status;

        // Ngăn admin chuyển sang trạng thái completed
        if ($new_status === 'completed') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin không thể chuyển trạng thái sang "Đã hoàn thành". Chỉ client mới có thể xác nhận nhận hàng.'
                ]);
            }
            return redirect()->route('admin.orders.show', $order->id)
                ->with('error', 'Admin không thể chuyển trạng thái sang "Đã hoàn thành". Chỉ client mới có thể xác nhận nhận hàng.');
        }

        if (isset($status[$order->status]) && in_array($new_status, $status[$order->status])) {
            $old_status = $order->status;

            // Nếu chuyển sang trạng thái cancelled
            if ($new_status === 'cancelled') {
                // Kiểm tra xem đơn hàng có số lượng lớn không
                if (!$this->hasLargeQuantity($order)) {
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Không thể hủy đơn hàng có số lượng bình thường. Chỉ có thể hủy đơn hàng có số lượng lớn.'
                        ]);
                    }
                    return redirect()->route('admin.orders.show', $order->id)
                        ->with('error', 'Không thể hủy đơn hàng có số lượng bình thường. Chỉ có thể hủy đơn hàng có số lượng lớn.');
                }

                // Chỉ xử lý hủy đơn hàng có số lượng lớn
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        $product->safeDecrementTotalSold($item->quantity);
                    }

                    // Hoàn trả stock cho đơn hàng có số lượng lớn
                    if ($item->product_variant_id) {
                        $variant = ProductVariant::find($item->product_variant_id);
                        if ($variant) {
                            $variant->increment('stock', $item->quantity);
                        }
                    }
                }
            }

            // Nếu chuyển từ pending sang confirmed, kiểm tra và trừ stock cho đơn hàng có số lượng lớn
            if ($old_status === 'pending' && $new_status === 'confirmed') {
                // Kiểm tra xem đơn hàng có số lượng lớn không
                if ($this->hasLargeQuantity($order)) {
                    foreach ($order->items as $item) {
                        if ($item->product_variant_id) {
                            $variant = ProductVariant::find($item->product_variant_id);
                            if ($variant) {
                                // Kiểm tra stock còn đủ không
                                if ($variant->stock < $item->quantity) {
                                    throw new \Exception('Không đủ số lượng tồn kho cho sản phẩm: ' . $variant->name);
                                }
                                $variant->decrement('stock', $item->quantity);
                            }
                        }
                    }
                }
            }

            // Nếu chuyển sang trạng thái delivered, cập nhật payment_status thành paid
            if ($new_status === 'delivered') {
                $order->update([
                    'status' => $new_status,
                    'payment_status' => 'paid'
                ]);
            } else {
                // Cập nhật trạng thái và lý do hủy nếu có
                $updateData = ['status' => $new_status];
                if ($new_status === 'cancelled' && $request->has('cancel_reason')) {
                    $updateData['cancel_reason'] = $request->cancel_reason;
                }
                $order->update($updateData);
            }

            // Gửi email hóa đơn kèm PDF khi chuyển sang trạng thái 'confirmed'
            if ($old_status !== 'confirmed' && $new_status === 'confirmed') {
                try {
                    // Gửi email hóa đơn kèm PDF
                    if ($order->shipping_email) {
                        // Tạo hoặc lấy hóa đơn
                        $invoice = Invoice::where('order_id', $order->id)->first();
                        if (!$invoice) {
                            $invoiceCode = 'INV' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                            $invoice = Invoice::create([
                                'order_id' => $order->id,
                                'invoice_code' => $invoiceCode,
                                'total' => $order->total_price,
                                'issued_by' => Auth::id(),
                                'issued_at' => now(),
                            ]);
                        }
                        // Render PDF
                        $pdf = Pdf::loadView('admin.invoices.pdf', ['invoice' => $invoice]);
                        $pdfContent = $pdf->output();
                        Mail::to($order->shipping_email)->send(new InvoicePdfMail($invoice, $pdfContent));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send invoice email', [
                        'order_id' => $order->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            try {
                Log::info('Bắt đầu gửi sự kiện cập nhật trạng thái', [
                    'order_id' => $order->id,
                    'old_status' => $old_status,
                    'new_status' => $new_status
                ]);

                // Refresh order model để đảm bảo có dữ liệu mới nhất
                $order->refresh();
                event(new OrderStatusUpdated($order));
                // Gửi thông báo cho người dùng
                if ($user = $order->user) {
                    $statusTranslations = [
                        'confirmed' => 'đã được xác nhận',
                        'preparing' => 'đang được chuẩn bị',
                        'shipping' => 'đang được giao',
                        'delivered' => 'đã giao hàng',
                        'cancelled' => 'đã bị hủy',
                    ];
                    $statusText = $statusTranslations[$new_status] ?? $new_status;

                    $data = [
                        'type' => 'order_status_updated',
                        'title' => 'Cập nhật trạng thái đơn hàng',
                        'message' => "Đơn hàng #{$order->order_code} của bạn {$statusText}.",
                        'url' => route('order.tracking', $order->id),
                        'order_id' => $order->id,
                    ];
                    $user->notify(new AdminDatabaseNotification($data));
                }
            } catch (\Exception $e) {
                Log::error('Lỗi khi gửi sự kiện', [
                    'order_id' => $order->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'status' => $order->status]);
            }
            return redirect()->route('admin.orders.show', $order->id)
                ->with('success', 'Cập nhật trạng thái đơn hàng thành công');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false]);
        }
        return redirect()->route('admin.orders.show', $order->id)
            ->with('error', 'Không thể cập nhật trạng thái đơn hàng');
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Lấy các sản phẩm trong giỏ hàng
            $cartItems = [];
            if (!isset($variant)) {
                $cartItems = session()->get('cart', []);
            }

            // Validate dữ liệu
            $request->validate([
                'c_fname' => 'required|string|max:255',
                'c_address' => 'required|string',
                'c_phone' => 'required|string',
                'c_email_address' => 'required|email',
                'shipping_name' => 'required_if:ship_to_different,1|string|max:255',
                'shipping_address' => 'required_if:ship_to_different,1|string',
                'shipping_phone' => 'required_if:ship_to_different,1|string',
                'shipping_email' => 'nullable|email',
            ]);

            // Tạo đơn hàng với thông tin người đặt/nhận tương ứng
            $order = Order::create([
                'user_id' => Auth::id(),
                'shipping_name' => $request->ship_to_different == '1' ? $request->shipping_name : $request->c_fname,
                'shipping_email' => $request->ship_to_different == '1' ? ($request->shipping_email ?: $request->c_email_address) : $request->c_email_address,
                'shipping_phone' => $request->ship_to_different == '1' ? $request->shipping_phone : $request->c_phone,
                'shipping_address' => $request->ship_to_different == '1' ? $request->shipping_address : $request->c_address,
                'payment_method' => $request->payment_method,
                'notes' => $request->c_order_notes,
                'status' => 'pending',
                'payment_status' => 'unpaid'
            ]);

            // Nếu đặt hàng hộ, lưu thông tin người đặt vào bảng order_address
            if ($request->ship_to_different == '1') {
                OrderAddress::create([
                    'order_id' => $order->id,
                    'full_name' => $request->c_fname,
                    'phone_number' => $request->c_phone,
                    'address' => $request->c_address,
                    'note' => "Đơn hàng được đặt hộ cho {$request->shipping_name} - {$request->shipping_phone}"
                ]);
            }

            // Xử lý thêm các items vào đơn hàng
            if (isset($variant)) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product_id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'price' => $variant->selling_price,
                    'total' => $variant->selling_price * $request->quantity
                ]);
            } else {
                foreach ($cartItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_variant_id' => $item->variant_id,
                        'quantity' => $item->quantity,
                        'price' => $item->variant ? $item->variant->selling_price : $item->product->selling_price,
                        'total' => ($item->variant ? $item->variant->selling_price : $item->product->selling_price) * $item->quantity
                    ]);
                }
            }

            DB::commit();

            // Xử lý thanh toán VNPay nếu cần
            if ($request->payment_method === 'vnpay') {
                return $this->processVnPayPayment($order);
            }

            return redirect()->route('order.success', $order->id)
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    // Kiểm tra đơn hàng có số lượng lớn không
    private function hasLargeQuantity($order)
    {
        $maxQuantityPerItem = 10;
        $maxTotalQuantity = 20;
        $maxTotalItems = 5; // Số lượng sản phẩm tối đa trong đơn hàng

        $totalQuantity = $order->items->sum('quantity');
        $totalItems = $order->items->count();

        // Kiểm tra từng sản phẩm có vượt quá giới hạn không
        foreach ($order->items as $item) {
            if ($item->quantity > $maxQuantityPerItem) {
                return true;
            }
        }

        // Kiểm tra tổng số lượng có vượt quá giới hạn không
        if ($totalQuantity > $maxTotalQuantity) {
            return true;
        }

        // Kiểm tra tổng số sản phẩm có vượt quá giới hạn không
        if ($totalItems > $maxTotalItems) {
            return true;
        }

        return false;
    }
}
