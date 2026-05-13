<?php

namespace App\Http\Controllers\client;

use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\ProductVariant;

class VoucherController
{
    /**
     * Lấy danh sách voucher phù hợp cho select option
     */
    public function getAvailableVouchers(Request $request)
    {
        $request->validate([
            'subtotal' => 'required|numeric|min:0',
            'email' => 'nullable|email'
        ]);

        $subtotal = $request->subtotal;
        $email = $request->email;

        // Lấy danh sách voucher phù hợp
        $vouchers = Voucher::where('is_active', 1)
            ->where('expires_at', '>', now())
            ->where(function($query) use ($subtotal) {
                $query->whereNull('min_order_amount')
                      ->orWhere('min_order_amount', '<=', $subtotal);
            })
            ->where(function($query) {
                $query->whereNull('usage_limit')
                      ->orWhereRaw('used_count < usage_limit');
            })
            ->get()
            ->filter(function($voucher) use ($subtotal, $email) {
                // Kiểm tra giới hạn sử dụng cho từng user
                if ($voucher->per_user_limit) {
                    $userUsedCount = 0;
                    
                    if (Auth::check()) {
                        // Đếm số lần sử dụng cho user đã đăng nhập
                        $userUsedCount = Order::where('user_id', Auth::id())
                            ->where('voucher_id', $voucher->id)
                            ->count();
                    } else {
                        // Đếm số lần sử dụng cho khách vãng lai dựa trên email
                        if (!$email) {
                            return false; // Không có email thì không thể sử dụng
                        }
                        $userUsedCount = Order::whereNull('user_id')
                            ->where('voucher_id', $voucher->id)
                            ->where('shipping_email', $email)
                            ->count();
                    }

                    return $userUsedCount < $voucher->per_user_limit;
                }
                return true;
            })
            ->map(function($voucher) use ($subtotal) {
                // Tính toán số tiền giảm giá
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

                return [
                    'id' => $voucher->id,
                    'code' => $voucher->code,
                    'type' => $voucher->type,
                    'value' => $voucher->value,
                    'discount_amount' => $discountAmount,
                    'min_order_amount' => $voucher->min_order_amount,
                    'description' => $voucher->description,
                    'display_text' => $this->getVoucherDisplayText($voucher, $discountAmount)
                ];
            });

        return response()->json([
            'success' => true,
            'vouchers' => $vouchers->values()
        ]);
    }

    /**
     * Tạo text hiển thị cho voucher
     */
    private function getVoucherDisplayText($voucher, $discountAmount)
    {
        $text = $voucher->code;
        
        if ($voucher->type === 'percentage') {
            $text .= " - Giảm {$voucher->value}%";
        } elseif ($voucher->type === 'fixed') {
            $text .= " - Giảm " . number_format($voucher->value, 0, ',', '.') . " VNĐ";
        } elseif ($voucher->type === 'free_shipping') {
            $text .= " - Miễn phí vận chuyển";
        }

        if ($voucher->min_order_amount) {
            $text .= " (Tối thiểu " . number_format($voucher->min_order_amount, 0, ',', '.') . " VNĐ)";
        }

        if ($discountAmount > 0) {
            $text .= " - Tiết kiệm " . number_format($discountAmount, 0, ',', '.') . " VNĐ";
        }

        return $text;
    }

    /**
     * Kiểm tra và áp dụng mã giảm giá
     */
    public function check(Request $request)
    {
        $request->validate([
            'voucher_code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
            'email' => 'nullable|email',
            'selected_items' => 'nullable|array',
            'selected_items.*' => 'nullable|integer'
        ]);

        $voucher = Voucher::where('code', $request->voucher_code)
            ->where('is_active', 1)
            ->where('expires_at', '>', now())
            ->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => '⚠️ Mã giảm giá không hợp lệ hoặc đã hết hạn!'
            ]);
        }

        if ($voucher->min_order_amount && $request->subtotal < $voucher->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => ' Đơn hàng phải có giá trị tối thiểu ' . number_format($voucher->min_order_amount) . ' VNĐ!'
            ]);
        }

        if ($voucher->usage_limit && $voucher->used_count >= $voucher->usage_limit) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá đã hết lượt sử dụng!'
            ]);
        }

        // Kiểm tra giới hạn sử dụng cho từng user
        if ($voucher->per_user_limit) {
            $userUsedCount = 0;
            
            if (Auth::check()) {
                // Đếm số lần sử dụng cho user đã đăng nhập (bao gồm cả đơn hàng đang chờ)
                $userUsedCount = Order::where('user_id', Auth::id())
                    ->where('voucher_id', $voucher->id)
                    ->whereNotIn('status', ['cancelled']) // Không tính đơn hàng đã hủy
                    ->count();
            } else {
                // Đếm số lần sử dụng cho khách vãng lai dựa trên email
                if (!$request->filled('email')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Vui lòng nhập email để sử dụng mã giảm giá!'
                    ]);
                }
                $userUsedCount = Order::whereNull('user_id')
                    ->where('voucher_id', $voucher->id)
                    ->where('shipping_email', $request->email)
                    ->whereNotIn('status', ['cancelled']) // Không tính đơn hàng đã hủy
                    ->count();
            }

            if ($userUsedCount >= $voucher->per_user_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã sử dụng mã giảm giá này tối đa số lần cho phép!'
                ]);
            }
        }

        // Kiểm tra sản phẩm trong giỏ hàng có đang khuyến mãi không
        $hasDiscountedProducts = false;
        $cartItems = [];

        if ($request->has('variant_id')) {
            // Trường hợp mua ngay
            $variant = ProductVariant::with('product')->find($request->variant_id);
            if ($variant && $variant->product->is_discount) {
                $hasDiscountedProducts = true;
            }
        } else {
            // Trường hợp mua từ giỏ hàng
            $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
            if ($cart) {
                // Nếu có selected_items, chỉ kiểm tra các sản phẩm được chọn
                if ($request->has('selected_items') && is_array($request->selected_items)) {
                    $cartItems = $cart->cartItems()
                        ->whereIn('id', $request->selected_items)
                        ->with(['product', 'variant'])
                        ->get();
                } else {
                    // Nếu không có selected_items, kiểm tra tất cả sản phẩm trong giỏ hàng
                    $cartItems = $cart->cartItems()->with(['product', 'variant'])->get();
                }
                
                foreach ($cartItems as $item) {
                    if ($item->product->is_discount) {
                        $hasDiscountedProducts = true;
                        break;
                    }
                }
            }
        }

        // Nếu có sản phẩm đang khuyến mãi
        if ($hasDiscountedProducts) {
            return response()->json([
                'success' => false,
                'message' => ' Không thể áp dụng mã giảm giá cho đơn hàng có sản phẩm đang khuyến mãi!'
            ]);
        }

        // Kiểm tra xem user có đang sử dụng voucher này cho đơn hàng khác chưa
        if (Auth::check()) {
            $pendingOrderWithVoucher = Order::where('user_id', Auth::id())
                ->where('voucher_id', $voucher->id)
                ->whereIn('status', ['pending', 'confirmed', 'preparing', 'shipping'])
                ->first();
            
            if ($pendingOrderWithVoucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã sử dụng mã giảm giá này cho đơn hàng #' . $pendingOrderWithVoucher->order_code . '!'
                ]);
            }
        } else {
            if ($request->filled('email')) {
                $pendingOrderWithVoucher = Order::whereNull('user_id')
                    ->where('voucher_id', $voucher->id)
                    ->where('shipping_email', $request->email)
                    ->whereIn('status', ['pending', 'confirmed', 'preparing', 'shipping'])
                    ->first();
                
                if ($pendingOrderWithVoucher) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Bạn đã sử dụng mã giảm giá này cho đơn hàng #' . $pendingOrderWithVoucher->order_code . '!'
                    ]);
                }
            }
        }

        $discountAmount = 0;
        switch ($voucher->type) {
            case 'percentage':
                $discountAmount = round(($request->subtotal * $voucher->value) / 100);
                break;
            case 'fixed':
                // Kiểm tra nếu giá trị voucher lớn hơn tổng tiền hàng
                if ($voucher->value > $request->subtotal) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Không thể áp dụng mã giảm giá này cho đơn hàng vì giá trị voucher lớn hơn tổng tiền hàng!'
                    ]);
                }
                $discountAmount = $voucher->value;
                break;
            case 'free_shipping':
                $discountAmount = 0;
                break;
        }

        // Kiểm tra nếu số tiền giảm giá lớn hơn hoặc bằng tổng tiền hàng
        if ($discountAmount >= $request->subtotal) {
            return response()->json([
                'success' => false,
                'message' => ' Không thể áp dụng mã giảm giá này cho đơn hàng!'
            ]);
        }

        $finalTotal = $request->subtotal - $discountAmount;

        return response()->json([
            'success' => true,
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'discount_amount' => $discountAmount,
                'final_total' => $finalTotal
            ],
            'message' => 'Áp dụng mã giảm giá thành công đã giảm giá với số tiền ' . number_format($discountAmount) . ' VNĐ!'
        ]);
    }
}
