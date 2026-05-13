<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderInvoice;
use App\Events\OrderCreated;
use App\Models\User;
use App\Models\Voucher;
use App\Notifications\AdminDatabaseNotification;

class CheckoutController
{
    /**
     * Hiển thị trang checkout với thông tin sản phẩm đã chọn
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Lấy variant_id và quantity từ request
        $variantId = $request->input('variant_id');
        $quantity = $request->input('quantity', 1);

        // Khởi tạo biến
        $variant = null;
        $attributes = [];

        // Nếu có variant_id thì lấy thông tin biến thể
        if ($variantId) {
            $variant = ProductVariant::with(['product', 'combinations.attributeValue.attributeType'])
                ->find($variantId);

            if ($variant) {
                foreach ($variant->combinations as $comb) {
                    $type = $comb->attributeValue->attributeType->name ?? '';
                    $value = is_array($comb->attributeValue->value)
                        ? $comb->attributeValue->value[0]
                        : (json_decode($comb->attributeValue->value, true)[0] ?? $comb->attributeValue->value);
                    $attributes[$type] = $value;
                }
            }
        }

        // Kiểm tra nếu user là admin hoặc staff
        if (Auth::check() && ($user && ($user->hasRole('admin') || $user->hasRole('staff')))) {
            $slug = $variant && $variant->product ? $variant->product->slug : $request->input('slug');
            if (!$slug) {
                return redirect()->route('home')->with('error', 'Tài khoản admin và nhân viên không được phép mua hàng!');
            }
            return redirect()->route('product.detail', ['slug' => $slug])->with('error', 'Tài khoản admin và nhân viên không được phép mua hàng!');
        }

        // Tính toán subtotal để kiểm tra voucher phù hợp
        $subtotal = 0;
        if ($variant) {
            $subtotal = $variant->selling_price * $quantity;
        } else {
            if (Auth::check()) {
                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cartItems = $cart->cartItems()->with(['product', 'variant'])->get();
                    foreach ($cartItems as $item) {
                        $price = $item->variant ? $item->variant->selling_price : $item->product->selling_price;
                        $subtotal += $price * $item->quantity;
                    }
                }
            }
        }

        // Kiểm tra có sản phẩm khuyến mãi không
        $hasDiscountedProducts = false;
        if ($variant) {
            $hasDiscountedProducts = $variant->product->is_discount;
        } else {
            if (Auth::check()) {
                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                if ($cart) {
                    $cartItems = $cart->cartItems()->with('product')->get();
                    foreach ($cartItems as $item) {
                        if ($item->product->is_discount) {
                            $hasDiscountedProducts = true;
                            break;
                        }
                    }
                }
            }
        }

        // Lấy danh sách voucher phù hợp
        $availableVouchers = collect();
        if (!$hasDiscountedProducts && $subtotal > 0) {
            $availableVouchers = Voucher::where('is_active', 1)
                ->where('expires_at', '>', now())
                ->where(function($query) {
                    $query->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                })
                ->get()
                ->filter(function($voucher) use ($subtotal) {
                    // Kiểm tra giới hạn sử dụng cho từng user
                    if ($voucher->per_user_limit) {
                        $userUsedCount = 0;
                        if (Auth::check()) {
                            $userUsedCount = Order::where('user_id', Auth::id())
                                ->where('voucher_id', $voucher->id)
                                ->whereNotIn('status', ['cancelled']) // Không tính đơn hàng đã hủy
                                ->count();
                        }
                        return $userUsedCount < $voucher->per_user_limit;
                    }
                    return true;
                })
                ->map(function($voucher) use ($subtotal) {
                    // Tính toán số tiền giảm giá cho mỗi voucher
                    $discountAmount = 0;
                    switch ($voucher->type) {
                        case 'percentage':
                            $discountAmount = round(($subtotal * $voucher->value) / 100);
                            break;
                        case 'fixed':
                            $discountAmount = min($voucher->value, $subtotal);
                            break;
                        case 'free_shipping':
                            $discountAmount = 0;
                            break;
                    }
                    
                    // Thêm discount_amount vào voucher 
                    $voucher->discount_amount = $discountAmount;
                    return $voucher;
                });
        }

        // Trả về view với các thông tin cần thiết
        return view('client.checkout.index', compact('variant', 'quantity', 'attributes', 'availableVouchers', 'hasDiscountedProducts', 'subtotal'));
    }

    // Xử lý đặt hàng
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            
            // Validate dữ liệu đầu vào
            $validationRules = [
                'c_fname' => 'required|string|max:255',
                'c_email_address' => 'required|email|max:255',
                'c_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11',
                'c_address' => 'required|string|max:255',
                'payment_method' => 'required|in:cod,bank_transfer,credit_card,vnpay',
                'ship_to_different' => 'nullable|in:1',
            ];

            // Thêm validation rules cho thông tin người nhận nếu đặt hàng hộ
            if ($request->ship_to_different == '1') {
                $validationRules['shipping_name'] = 'required|string|max:255';
                $validationRules['shipping_address'] = 'required|string';
                $validationRules['shipping_phone'] = 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11';
                $validationRules['shipping_email'] = 'nullable|email|max:255';
            }

            $request->validate($validationRules);

            // Tính toán subtotal dựa vào variant hoặc giỏ hàng
            $subtotal = $this->calculateSubtotal($request);

            // Xử lý voucher và tính giá
            $priceInfo = $this->processVoucherAndPrice($request, $subtotal);

            // Kiểm tra số lượng và quyết định có trừ stock hay không
            $shouldDeductStock = $this->shouldDeductStock($request);

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => Auth::check() ? Auth::id() : null,
                'subtotal' => $subtotal,
                'discount' => $priceInfo['discount'],
                'shipping_fee' => $priceInfo['shipping_fee'],
                'total_price' => $priceInfo['total_price'],
                'shipping_name' => $request->ship_to_different == '1' ? $request->shipping_name : $request->c_fname,
                'shipping_email' => $request->ship_to_different == '1' ? ($request->shipping_email ?: $request->c_email_address) : $request->c_email_address,
                'shipping_phone' => $request->ship_to_different == '1' ? $request->shipping_phone : $request->c_phone,
                'shipping_address' => $request->ship_to_different == '1' ? $request->shipping_address : $request->c_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_method_id' => null,
                'status' => 'pending',
                'is_paid' => 0,
                'notes' => $request->c_order_notes ?? '',
                'voucher_id' => $priceInfo['voucher'] ? $priceInfo['voucher']->id : null,
                'voucher_code' => $priceInfo['voucher'] ? $priceInfo['voucher']->code : null,
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

            event(new OrderCreated($order));

            // Lấy tất cả admin
            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new AdminDatabaseNotification([
                    'type' => 'order_created',
                    'title' => !$shouldDeductStock ? 'Đơn hàng cần duyệt' : 'Đơn hàng mới',
                    'message' => 'Khách hàng: ' . ($order->user ? $order->user->name : 'Khách vãng lai') . ', Đơn hàng #' . $order->id . (!$shouldDeductStock ? ' - Cần duyệt số lượng lớn' : ''),
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'url' => route('admin.orders.show', $order->id),
                    'created_at' => now(),
                ]));
            }

            // Tạo chi tiết đơn hàng (order_items)
            if ($request->has('variant_id')) {
                // Lock variant để tránh race condition
                $variant = ProductVariant::lockForUpdate()->with('product')->findOrFail($request->variant_id);
                
                // Kiểm tra số lượng tồn kho
                if ($variant->stock < 1) {
                    throw new \Exception('⚠️ Hết số lượng tồn kho!');
                }
                if ($variant->stock < $request->quantity) {
                    throw new \Exception('⚠️ Hết số lượng tồn kho!');
                }
                if ($request->quantity < 1) {
                    throw new \Exception('⚠️ Số lượng sản phẩm phải lớn hơn 0!');
                }

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $variant->product->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $request->quantity,
                    'price' => $variant->selling_price,
                    'total' => $subtotal,
                ]);
                
                // Chỉ trừ stock khi số lượng không vượt quá giới hạn
                if ($shouldDeductStock) {
                    $variant->decrement('stock', $request->quantity);
                }
              
            } else {
                if (!Auth::check()) {
                    throw new \Exception('Vui lòng đăng nhập để mua hàng!');
                }
                $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                if (!$cart) {
                    throw new \Exception('Giỏ hàng của bạn đang trống!');
                }
                $cartItems = $cart->cartItems()->with(['product', 'variant'])->get();
                foreach ($cartItems as $item) {
                    // Lock variant để tránh khi có nhiều người cùng mua 1 sản phẩm
                    if ($item->variant) {
                        $variant = ProductVariant::lockForUpdate()->find($item->variant->id);
                        if ($variant->stock < 1) {
                            throw new \Exception('⚠️ Hết số lượng tồn kho!');
                        }
                        if ($variant->stock < $item->quantity) {
                            throw new \Exception('⚠️ Hết số lượng tồn kho!');
                        }
                        if ($item->quantity < 1) {
                            throw new \Exception('⚠️ Số lượng sản phẩm phải lớn hơn 0!');
                        }
                    }

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
                    
                    // Chỉ trừ stock khi số lượng không vượt quá giới hạn
                    if ($shouldDeductStock && $item->variant) {
                        $item->variant->decrement('stock', $item->quantity);
                    }
                }
                $cartItems->each->delete();
            }

            // Sau khi tạo order và commit transaction
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
            DB::commit();

            // Gửi email hóa đơn
            try {
                $toAddresses = config('mail.to.addresses');
                foreach ($toAddresses as $email) {
                    try {
                        Mail::to($email)->send(new OrderInvoice($order));
                    } catch (\Exception $e) {
                       
                    }
                }
            } catch (\Exception $e) {
               
            }

            // Chuyển hướng sau khi đặt hàng thành công
            if ($request->payment_method === 'vnpay') {
                return $this->redirectToVNPay($order);
            }

            return $this->redirectAfterOrder($order);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Kiểm tra xem có nên trừ stock ngay hay không
    private function shouldDeductStock($request)
    {
        $maxQuantityPerItem = 10; // Số lượng tối đa cho mỗi sản phẩm
        $maxTotalQuantity = 20; // Tổng số lượng tối đa cho toàn bộ đơn hàng
        $maxTotalItems = 5; // Số lượng sản phẩm tối đa trong đơn hàng
        
        $totalQuantity = 0;
        $totalItems = 0;

        if ($request->has('variant_id')) {
            // Mua ngay từ trang sản phẩm
            $quantity = $request->quantity;
            $totalQuantity = $quantity;
            $totalItems = 1;
            
            // Kiểm tra từng sản phẩm có vượt quá giới hạn không
            if ($quantity > $maxQuantityPerItem) {
                return false;
            }
        } else {
            // Mua từ giỏ hàng
            if (!Auth::check()) {
                throw new \Exception('Vui lòng đăng nhập để mua hàng!');
            }
            
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            if (!$cart) {
                throw new \Exception('Giỏ hàng của bạn đang trống!');
            }

            $cartItems = $cart->cartItems()->with(['product', 'variant'])->get();
            
            // Nếu có selected_items, chỉ tính các sản phẩm được chọn
            if ($request->has('selected_items') && is_array($request->selected_items)) {
                $cartItems = $cartItems->whereIn('id', $request->selected_items);
            }

            $totalItems = $cartItems->count();
            
            foreach ($cartItems as $item) {
                $totalQuantity += $item->quantity;
                
                // Kiểm tra từng sản phẩm có vượt quá giới hạn không
                if ($item->quantity > $maxQuantityPerItem) {
                    return false;
                }
            }
        }
        
        // Kiểm tra tổng số lượng có vượt quá giới hạn không
        if ($totalQuantity > $maxTotalQuantity) {
            return false;
        }
        
        // Kiểm tra tổng số sản phẩm có vượt quá giới hạn không
        if ($totalItems > $maxTotalItems) {
            return false;
        }

        return true;
    }

    public function tracking($orderId)
    {
        // Nếu là request tra cứu từ form
        if ($orderId === 'search') {
            $order_code = request('order_code');
            $order = Order::where('order_code', $order_code)->first();

            if (!$order) {
                return redirect()->route('order.guest.tracking')
                    ->with('error', 'Không tìm thấy đơn hàng. Vui lòng kiểm tra lại mã đơn hàng.');
            }

            return view('client.order.tracking', compact('order'));
        }

        // Nếu là request trực tiếp với ID đơn hàng
        $order = Order::with(['items.product', 'items.variant'])->findOrFail($orderId);
        return view('client.order.tracking', compact('order'));
    }

        // Hiển thị trang checkout cho giỏ hàng
    public function cartCheckout(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        // Kiểm tra nếu user là admin hoặc staff
        if (Auth::check() && ($user && ($user->hasRole('admin') || $user->hasRole('staff')))) {
            return redirect()->route('home')->with('error', 'Tài khoản admin và nhân viên không được phép mua hàng!');
        }

        // Kiểm tra user đã đăng nhập chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để mua hàng!');
        }

        // Lấy giỏ hàng của user
        $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
        if (!$cart) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Lấy các sản phẩm được chọn từ request
        $selectedItems = $request->input('selected_items', []);
        if (empty($selectedItems)) {
            return redirect()->route('cart')->with('error', 'Vui lòng chọn ít nhất một sản phẩm để thanh toán!');
        }

        $cartItems = $cart->cartItems()
            ->whereIn('id', $selectedItems)
            ->with(['product', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Giỏ hàng của bạn đang trống!');
        }

        // Tính toán tổng tiền
        $subtotal = 0;
        foreach ($cartItems as $item) {
            $price = $item->variant ? $item->variant->selling_price : $item->product->selling_price;
            $subtotal += $price * $item->quantity;
        }

        // Kiểm tra có sản phẩm khuyến mãi không
        $hasDiscountedProducts = false;
        foreach ($cartItems as $item) {
            if ($item->product->is_discount) {
                $hasDiscountedProducts = true;
                break;
            }
        }

        // Lấy danh sách voucher phù hợp
        $availableVouchers = collect();
        if (!$hasDiscountedProducts && $subtotal > 0) {
            $availableVouchers = Voucher::where('is_active', 1)
                ->where('expires_at', '>', now())
                ->where(function($query) {
                    $query->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                })
                ->get()
                ->filter(function($voucher) use ($subtotal) {
                    // Kiểm tra giới hạn sử dụng cho từng user
                    if ($voucher->per_user_limit) {
                        $userUsedCount = 0;
                        if (Auth::check()) {
                            $userUsedCount = Order::where('user_id', Auth::id())
                                ->where('voucher_id', $voucher->id)
                                ->whereNotIn('status', ['cancelled']) // Không tính đơn hàng đã hủy
                                ->count();
                        }
                        return $userUsedCount < $voucher->per_user_limit;
                    }
                    return true;
                })
                ->map(function($voucher) use ($subtotal) {
                    // Tính toán số tiền giảm giá cho mỗi voucher
                    $discountAmount = 0;
                    switch ($voucher->type) {
                        case 'percentage':
                            $discountAmount = round(($subtotal * $voucher->value) / 100);
                            break;
                        case 'fixed':
                            $discountAmount = min($voucher->value, $subtotal);
                            break;
                        case 'free_shipping':
                            $discountAmount = 0;
                            break;
                    }
                    
                    // Thêm discount_amount vào voucher object
                    $voucher->discount_amount = $discountAmount;
                    return $voucher;
                });
        }

        return view('client.checkout.index', compact('cartItems', 'subtotal', 'availableVouchers', 'hasDiscountedProducts'));
    }

    // Xử lý đặt hàng từ giỏ hàng
    public function processCartCheckout(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate dữ liệu đầu vào
            $validationRules = [
                'c_fname' => 'required|string|max:255',
                'c_email_address' => 'required|email|max:255',
                'c_phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11',
                'c_address' => 'required|string|max:255',
                'payment_method' => 'required|in:cod,bank_transfer,credit_card,vnpay',
                'selected_items' => 'required|array',
                'ship_to_different' => 'nullable|in:1',
            ];

            // Thêm validation rules cho thông tin người nhận nếu đặt hàng hộ
            if ($request->ship_to_different == '1') {
                $validationRules['shipping_name'] = 'required|string|max:255';
                $validationRules['shipping_address'] = 'required|string';
                $validationRules['shipping_phone'] = 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:11';
                $validationRules['shipping_email'] = 'nullable|email|max:255';
            }

            $request->validate($validationRules);

            // Tính toán subtotal dựa vào giỏ hàng
            $subtotal = $this->calculateSubtotal($request);

            // Xử lý voucher và tính giá
            $priceInfo = $this->processVoucherAndPrice($request, $subtotal);

            // Kiểm tra user đã đăng nhập chưa
            if (!Auth::check()) {
                throw new \Exception('Vui lòng đăng nhập để mua hàng!');
            }

            // Kiểm tra số lượng và quyết định có trừ stock hay không
            $shouldDeductStock = $this->shouldDeductStock($request);

            // Tạo đơn hàng
            $order = Order::create([
                'user_id' => Auth::id(),
                'subtotal' => $subtotal,
                'discount' => $priceInfo['discount'],
                'shipping_fee' => $priceInfo['shipping_fee'],
                'total_price' => $priceInfo['total_price'],
                'shipping_name' => $request->ship_to_different == '1' ? $request->shipping_name : $request->c_fname,
                'shipping_email' => $request->ship_to_different == '1' ? ($request->shipping_email ?: $request->c_email_address) : $request->c_email_address,
                'shipping_phone' => $request->ship_to_different == '1' ? $request->shipping_phone : $request->c_phone,
                'shipping_address' => $request->ship_to_different == '1' ? $request->shipping_address : $request->c_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'shipping_method_id' => null,
                'status' => 'pending',
                'is_paid' => 0,
                'notes' => $request->c_order_notes ?? '',
                'voucher_id' => $priceInfo['voucher'] ? $priceInfo['voucher']->id : null,
                'voucher_code' => $priceInfo['voucher'] ? $priceInfo['voucher']->code : null,
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

            event(new OrderCreated($order));

            // Lấy tất cả admin
            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new AdminDatabaseNotification([
                    'type' => 'order_created',
                    'title' => !$shouldDeductStock ? 'Đơn hàng cần duyệt' : 'Đơn hàng mới',
                    'message' => 'Khách hàng: ' . ($order->user ? $order->user->name : 'Khách vãng lai') . ', Đơn hàng #' . $order->id . (!$shouldDeductStock ? ' - Cần duyệt số lượng lớn' : ''),
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                    'url' => route('admin.orders.show', $order->id),
                    'created_at' => now(),
                ]));
            }

            // Tạo chi tiết đơn hàng và cập nhật tồn kho
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                $cartItems = $cart->cartItems()
                    ->whereIn('id', $request->selected_items)
                    ->with(['product', 'variant'])
                    ->get();
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
                    
                    // Chỉ trừ stock khi số lượng không vượt quá giới hạn
                    if ($shouldDeductStock && $item->variant) {
                        $item->variant->decrement('stock', $item->quantity);
                    }
                }
                $cartItems->each->delete();
            }

            // Gửi email hóa đơn
            try {
                $toAddresses = config('mail.to.addresses');
                foreach ($toAddresses as $email) {
                        Mail::to($email)->send(new OrderInvoice($order));
                    
                }
            } catch (\Exception $e) {
               
            }

            DB::commit();

            return redirect()->route('order.index')
                ->with('success', 'Đặt hàng thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Xử lý voucher và tính toán giá
    private function processVoucherAndPrice($request, $subtotal)
    {
        $voucher = null;
        $voucherDiscount = 0;
        $shipping_fee = 30000; 

        // Kiểm tra chỉ cho phép 1 voucher cho mỗi đơn hàng
        if ($request->filled('voucher_code')) {
            // Kiểm tra xem có voucher nào khác đã được áp dụng chưa
            if ($request->filled('voucher_id') && $request->filled('discount_amount')) {
                // Nếu đã có voucher được áp dụng, chỉ cho phép thay đổi voucher hiện tại
                $currentVoucherId = $request->input('voucher_id');
                $requestedVoucherCode = $request->input('voucher_code');
                
                $currentVoucher = \App\Models\Voucher::find($currentVoucherId);
                if ($currentVoucher && $currentVoucher->code !== $requestedVoucherCode) {
                    throw new \Exception('⚠️ Chỉ có thể áp dụng 1 mã giảm giá cho mỗi đơn hàng!');
                }
            }
            
            $voucher = \App\Models\Voucher::where('code', $request->voucher_code)
                ->where('is_active', 1)
                ->where('expires_at', '>', now())
                ->first();
            
            if ($voucher) {
                // Kiểm tra giới hạn sử dụng cho từng user
                if ($voucher->per_user_limit && Auth::check()) {
                    $userUsedCount = Order::where('user_id', Auth::id())
                        ->where('voucher_id', $voucher->id)
                        ->whereNotIn('status', ['cancelled']) // Không tính đơn hàng đã hủy
                        ->count();
                    
                    if ($userUsedCount >= $voucher->per_user_limit) {
                        throw new \Exception('⚠️ Bạn đã sử dụng hết số lần được phép với mã giảm giá này!');
                    }
                }

                // Kiểm tra xem user có đang sử dụng voucher này cho đơn hàng khác chưa
                if (Auth::check()) {
                    $pendingOrderWithVoucher = Order::where('user_id', Auth::id())
                        ->where('voucher_id', $voucher->id)
                        ->whereIn('status', ['pending', 'confirmed', 'preparing', 'shipping'])
                        ->first();
                    
                    if ($pendingOrderWithVoucher) {
                        throw new \Exception('⚠️ Bạn đã sử dụng mã giảm giá này cho đơn hàng #' . $pendingOrderWithVoucher->order_code . '!');
                    }
                }
                
                // Kiểm tra sản phẩm có đang khuyến mãi không
                if ($request->has('variant_id')) {
                    $variant = ProductVariant::with('product')->find($request->variant_id);
                    if ($variant && $variant->product->is_discount) {
                        throw new \Exception('⚠️ Không thể áp dụng mã giảm giá cho sản phẩm đang khuyến mãi!');
                    }
                } else {
                    if (Auth::check()) {
                        $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                        if ($cart) {
                            // Nếu có selected_items (từ giỏ hàng), chỉ kiểm tra các sản phẩm được chọn
                            if ($request->has('selected_items') && is_array($request->selected_items)) {
                                $cartItems = $cart->cartItems()
                                    ->whereIn('id', $request->selected_items)
                                    ->with('product')
                                    ->get();
                            } else {
                                // Nếu không có selected_items (mua ngay), kiểm tra tất cả sản phẩm trong giỏ hàng
                                $cartItems = $cart->cartItems()->with('product')->get();
                            }
                            
                            foreach ($cartItems as $item) {
                                if ($item->product->is_discount) {
                                    throw new \Exception('⚠️ Không thể áp dụng mã giảm giá cho đơn hàng có sản phẩm đang khuyến mãi!');
                                }
                            }
                        }
                    }
                }

                // Kiểm tra giá trị đơn hàng tối thiểu
                if ($voucher->min_order_amount && $subtotal < $voucher->min_order_amount) {
                    throw new \Exception('⚠️ Đơn hàng phải có giá trị tối thiểu ' . number_format($voucher->min_order_amount, 0, ',', '.') . ' VNĐ để áp dụng mã giảm giá này!');
                }
                
                // Tính số tiền giảm giá
                if ($voucher->type == 'percentage') {
                    $voucherDiscount = round(($subtotal * $voucher->value) / 100);
                } else {
                    // Nếu giá trị voucher lớn hơn tổng tiền hàng
                    if ($voucher->value > $subtotal) {
                        throw new \Exception('⚠️ Không thể áp dụng mã giảm giá này cho đơn hàng vì giá trị voucher lớn hơn tổng tiền hàng!');
                    }
                    $voucherDiscount = $voucher->value;
                }

                // Đảm bảo số tiền giảm không vượt quá tổng đơn hàng
                if ($voucherDiscount >= $subtotal) {
                    throw new \Exception('⚠️ Không thể áp dụng mã giảm giá này cho đơn hàng!');
                }
            }
        }

        $total_price = $subtotal + $shipping_fee - $voucherDiscount;

        return [
            'voucher' => $voucher,
            'discount' => $voucherDiscount,
            'shipping_fee' => $shipping_fee,
            'total_price' => $total_price
        ];
    }

    // Thêm phương thức tính subtotal
    private function calculateSubtotal($request)
    {
        if ($request->has('variant_id')) {
            $variant = ProductVariant::findOrFail($request->variant_id);
            return $variant->selling_price * $request->quantity;
        } else {
            if (!Auth::check()) {
                throw new \Exception('Vui lòng đăng nhập để mua hàng!');
            }
            
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            if (!$cart) {
                throw new \Exception('Giỏ hàng của bạn đang trống!');
            }

            $subtotal = 0;
            
            // Nếu có selected_items (từ giỏ hàng), chỉ tính tổng tiền của các sản phẩm được chọn
            if ($request->has('selected_items') && is_array($request->selected_items)) {
                $cartItems = $cart->cartItems()
                    ->whereIn('id', $request->selected_items)
                    ->with(['product', 'variant'])
                    ->get();
            } else {
                // Nếu không có selected_items (mua ngay), tính tất cả sản phẩm trong giỏ hàng
                $cartItems = $cart->cartItems()->with(['product', 'variant'])->get();
            }
            
            foreach ($cartItems as $item) {
                $price = $item->variant ? $item->variant->selling_price : $item->product->selling_price;
                $subtotal += $price * $item->quantity;
            }
            return $subtotal;
        }
    }

    private function redirectToVNPay($order)
    {
        // Chuyển hướng đến trang thanh toán VNPay với các thông tin cần thiết
        $params = [
            'c_fname' => request('c_fname'),
            'c_email_address' => request('c_email_address'),
            'c_phone' => request('c_phone'),
            'c_address' => request('c_address'),
            'c_order_notes' => request('c_order_notes'),
            'voucher_code' => request('voucher_code')
        ];

        // Thêm thông tin tùy theo loại đặt hàng
        if (request('variant_id')) {
            // Trường hợp mua ngay
            $params['variant_id'] = request('variant_id');
            $params['quantity'] = request('quantity');
            return redirect()->route('vnpay.payment', $params);
        } else {
            // Trường hợp từ giỏ hàng
            $params['selected_items'] = request('selected_items');
            return redirect()->route('cart.checkout.vnpay', $params);
        }
    }

    private function redirectAfterOrder($order)
    {
        // Nếu khách đã đăng nhập
        if (Auth::check()) {
            return redirect()->route('order.index')
                ->with('success', 'Đặt hàng thành công!');
        }

        // Nếu là khách vãng lai (chưa đăng nhập)
        return redirect()->route('order.guest.tracking', [
            'order_code' => $order->order_code,
            'email' => $order->shipping_email
        ])->with('success', 'Đặt hàng thành công! Bạn có thể dùng mã đơn hàng và email để tra cứu đơn hàng sau này.');
    }
}

