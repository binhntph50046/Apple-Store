<?php

namespace App\Http\Controllers\client;

use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PaymentController 
{
    public function vnPay(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate dữ liệu đầu vào
            $validationRules = [
                'variant_id' => 'required|exists:product_variants,id',
                'quantity' => 'required|integer|min:1',
                'c_fname' => 'required|string|max:255',
                'c_email_address' => 'required|email|max:255',
                'c_phone' => 'required|string|max:20',
                'c_address' => 'required|string|max:255',
                'ship_to_different' => 'nullable|in:1',
            ];

            // Thêm validation rules cho thông tin người nhận nếu đặt hàng hộ
            if ($request->ship_to_different == '1') {
                $validationRules['shipping_name'] = 'required|string|max:255';
                $validationRules['shipping_address'] = 'required|string';
                $validationRules['shipping_phone'] = 'required|string|max:20';
                $validationRules['shipping_email'] = 'nullable|email|max:255';
            }

            $request->validate($validationRules);

            // Lấy thông tin biến thể
            $variant = ProductVariant::with('product')->findOrFail($request->variant_id);

            // Kiểm tra số lượng tồn kho
            if ($variant->stock < $request->quantity) {
                throw new \Exception('Số lượng sản phẩm trong kho không đủ!');
            }

            // Tính toán giá trị đơn hàng
            $price = $variant->selling_price;
            $quantity = $request->quantity;
            $subtotal = $price * $quantity;
            $discount = 0;

            // Xử lý voucher nếu có
            if ($request->filled('voucher_code')) {
                $voucher = \App\Models\Voucher::where('code', $request->voucher_code)
                    ->where('is_active', 1)
                    ->where('expires_at', '>', now())
                    ->first();
                
                if ($voucher) {
                    // Tính số tiền giảm giá
                    $discount = $voucher->type == 'percentage' 
                        ? round(($subtotal * $voucher->value) / 100) 
                        : $voucher->value;

                    // Đảm bảo số tiền giảm không vượt quá tổng đơn hàng
                    $discount = min($discount, $subtotal);
                }
            }

            $shipping_fee = 0; 
            $total_price = $subtotal + $shipping_fee - $discount;

            // Tạo đơn hàng mới
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shipping_fee,
                'total_price' => $total_price,
                'shipping_name' => $request->ship_to_different == '1' ? $request->shipping_name : $request->c_fname,
                'shipping_email' => $request->ship_to_different == '1' ? ($request->shipping_email ?: $request->c_email_address) : $request->c_email_address,
                'shipping_phone' => $request->ship_to_different == '1' ? $request->shipping_phone : $request->c_phone,
                'shipping_address' => $request->ship_to_different == '1' ? $request->shipping_address : $request->c_address,
                'payment_method' => 'vnpay',
                'payment_status' => 'pending',
                'shipping_method_id' => null,
                'status' => 'pending',
                'is_paid' => 0,
                'notes' => $request->c_order_notes,
                'voucher_code' => $request->voucher_code,
                'voucher_id' => $voucher->id ?? null,
            ]);

            // Nếu đặt hàng hộ, lưu thông tin người đặt vào bảng order_address
            if ($request->ship_to_different == '1') {
                \App\Models\OrderAddress::create([
                    'order_id' => $order->id,
                    'full_name' => $request->c_fname,
                    'phone_number' => $request->c_phone,
                    'address' => $request->c_address,
                    'note' => "Đơn hàng được đặt hộ cho {$request->shipping_name} - {$request->shipping_phone}"
                ]);
            }

            // Tạo chi tiết đơn hàng
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $variant->product->id,
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $price,
                'total' => $subtotal,
            ]);

            // Cập nhật số lượng tồn kho
            $variant->decrement('stock', $quantity);

            // Gửi mail xác nhận đơn hàng cho cả khách chưa đăng nhập
           

            // Tạo URL thanh toán VNPay
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = "3XK60GH9";//Mã website tại VNPAY 
            $vnp_HashSecret = "MTQQ4ATJAPGQBL7BP4NOK1JRTCD8WZ3V"; //Chuỗi bí mật

            $vnp_TxnRef = $order->id; //Mã đơn hàng
            $vnp_OrderInfo = 'Thanh toan don hang #' . $order->id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $total_price * 100; // Số tiền * 100 (đã bao gồm giảm giá)
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $request->ip();
            $vnp_CreateDate = date('YmdHis');

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            DB::commit();

            // Gửi mail hóa đơn PDF cho khách (giống như bên CheckoutController)
           
                if ($order->shipping_email) {
                    // Tạo hoặc lấy hóa đơn
                    $invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
                    if (!$invoice) {
                        $invoiceCode = 'INV' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
                        $invoice = \App\Models\Invoice::create([
                            'order_id' => $order->id,
                            'invoice_code' => $invoiceCode,
                            'total' => $order->total_price,
                            'issued_by' => null,
                            'issued_at' => now(),
                        ]);
                    }
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoices.pdf', ['invoice' => $invoice]);
                    $pdfContent = $pdf->output();
                    Mail::to($order->shipping_email)->send(new \App\Mail\InvoicePdfMail($invoice, $pdfContent));
                    
                }
          

            return redirect()->away($vnp_Url);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi xử lý thanh toán VNPay: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Có lỗi xảy ra khi xử lý thanh toán: ' . $e->getMessage()]);
        }
    }

    public function vnPayReturn(Request $request)
    {
        try {
            // Kiểm tra chữ ký
            $vnp_SecureHash = $request->vnp_SecureHash;
            $inputData = array();
            foreach ($request->all() as $key => $value) {
                if (substr($key, 0, 4) == "vnp_") {
                    if ($key != 'vnp_SecureHash') {
                        $inputData[$key] = $value;
                    }
                }
            }
            ksort($inputData);
            $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

            $vnp_HashSecret = "MTQQ4ATJAPGQBL7BP4NOK1JRTCD8WZ3V";
            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

            if ($secureHash == $vnp_SecureHash) {
                // Tìm đơn hàng theo order_code (giỏ hàng) hoặc id (mua ngay)
                $order = Order::where('order_code', $request->vnp_TxnRef)->first();
                if (!$order && is_numeric($request->vnp_TxnRef)) {
                $order = Order::find($request->vnp_TxnRef);
                }
                if ($order) {
                    if ($request->vnp_ResponseCode == "00") {
                        // Thanh toán thành công
                        $order->update([
                            'payment_status' => 'paid',
                            'is_paid' => 1,
                            'status' => 'confirmed'
                        ]);
                        // Nếu là khách (không đăng nhập), chuyển về guest tracking
                        if (!$order->user_id) {
                            return redirect()->route('order.guest.tracking', [
                                'order_code' => $order->order_code,
                                'email' => $order->shipping_email
                            ]);
                        }
                        // Nếu là user đã đăng nhập, về trang quản lý đơn hàng
                        return redirect()->route('order.index')
                            ->with('success', 'Thanh toán thành công!');
                    } else {
                        // Thanh toán thất bại
                        $order->update([
                            'payment_status' => 'failed',
                            'status' => 'cancelled'
                        ]);
                        return redirect()->route('order.index')
                            ->with('error', 'Thanh toán thất bại!');
                    }
                }
            }

            return redirect()->route('order.index')
                ->with('error', 'Có lỗi xảy ra trong quá trình xử lý thanh toán!');

        } catch (\Exception $e) {
            Log::error('Lỗi xử lý callback VNPay: ' . $e->getMessage());
            return redirect()->route('order.index')
                ->with('error', 'Có lỗi xảy ra trong quá trình xử lý thanh toán!');
        }
    }

    /**
     * Xử lý thanh toán VNPay cho giỏ hàng
     */
    public function cartVnPay(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate dữ liệu đầu vào
            $request->validate([
                'c_fname' => 'required|string|max:255',
                'c_email_address' => 'required|email|max:255',
                'c_phone' => 'required|string|max:20',
                'c_address' => 'required|string|max:255',
                'payment_method' => 'required|in:vnpay',
                'selected_items' => 'required|array',
            ]);

            // Lấy giỏ hàng của user
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            if (!$cart) {
                throw new \Exception('Giỏ hàng của bạn đang trống!');
            }

            // Lấy các sản phẩm được chọn
            $cartItems = $cart->cartItems()
                ->whereIn('id', $request->selected_items)
                ->with(['product', 'variant'])
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Giỏ hàng của bạn đang trống!');
            }

            // Tính toán giá trị đơn hàng
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $price = $item->variant ? $item->variant->selling_price : $item->product->selling_price;
                $subtotal += $price * $item->quantity;
            }
            $discount = 0;

            // Xử lý voucher nếu có
            if ($request->filled('voucher_code')) {
                $voucher = \App\Models\Voucher::where('code', $request->voucher_code)
                    ->where('is_active', 1)
                    ->where('expires_at', '>', now())
                    ->first();
                
                if ($voucher) {
                    // Tính số tiền giảm giá
                    $discount = $voucher->type == 'percentage' 
                        ? round(($subtotal * $voucher->value) / 100) 
                        : $voucher->value;

                    // Đảm bảo số tiền giảm không vượt quá tổng đơn hàng
                    $discount = min($discount, $subtotal);
                }
            }

            $shipping_fee = 0;
            $total_price = $subtotal + $shipping_fee - $discount;

            // Tạo đơn hàng mới
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shipping_fee,
                'total_price' => $total_price,
                'shipping_address' => $request->c_address,
                'shipping_name' => $request->c_fname,
                'shipping_phone' => $request->c_phone,
                'shipping_email' => $request->c_email_address,
                'payment_method' => 'vnpay',
                'payment_status' => 'pending',
                'shipping_method_id' => null,
                'status' => 'pending',
                'is_paid' => 0,
                'notes' => $request->c_order_notes,
                'voucher_code' => $request->voucher_code,
                'voucher_id' => $voucher->id ?? null,
            ]);

            // Cập nhật mã đơn hàng sau khi có ID
            $order->update([
                'order_code' => 'DH' . $order->id
            ]);

            // Tạo chi tiết đơn hàng và cập nhật tồn kho
            foreach ($cartItems as $item) {
                $price = $item->variant ? $item->variant->selling_price : $item->product->selling_price;
                $total = $price * $item->quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                    'total' => $total,
                ]);

                // Cập nhật số lượng tồn kho
                if ($item->variant) {
                    $item->variant->decrement('stock', $item->quantity);
                }
            }

            // Xóa các sản phẩm đã thanh toán khỏi giỏ hàng
            $cartItems->each->delete();

            // Tạo URL thanh toán VNPay
            $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
            $vnp_Returnurl = route('vnpay.return');
            $vnp_TmnCode = "3XK60GH9";//Mã website tại VNPAY 
            $vnp_HashSecret = "MTQQ4ATJAPGQBL7BP4NOK1JRTCD8WZ3V"; //Chuỗi bí mật

            $vnp_TxnRef = $order->order_code;
            $vnp_OrderInfo = 'Thanh toan don hang ' . $order->order_code;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $total_price * 100; // Số tiền * 100 (đã bao gồm giảm giá)
            $vnp_Locale = 'vn';
            $vnp_IpAddr = request()->ip();
            $vnp_CreateDate = date('YmdHis');

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => $vnp_CreateDate,
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
            );

            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            DB::commit();

            Log::info('Redirecting to VNPay URL: ' . $vnp_Url);
            return redirect()->away($vnp_Url);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('cartVnPay error: ' . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
