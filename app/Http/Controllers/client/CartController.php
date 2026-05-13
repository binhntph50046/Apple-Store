<?php

namespace App\Http\Controllers\client;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;


class CartController
{

    /**
     * Thêm sản phẩm vào giỏ hàng
     */
    public function add(Request $request)
    {
        // Kiểm tra user đã đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);
        // dd($request);
        try {
            DB::transaction(function () use ($request) {
                $user = Auth::user();
                $cart = Cart::firstOrCreate(['user_id' => $user->id]);

                // Kiểm tra sản phẩm và variant
                $product = Product::findOrFail($request->product_id);
                if ($request->variant_id) {
                    $variant = ProductVariant::where('id', $request->variant_id)
                        ->where('product_id', $request->product_id)
                        ->firstOrFail();

                    // Kiểm tra tồn kho
                    if ($variant->stock < $request->quantity) {
                        throw new \Exception('Số lượng sản phẩm trong kho không đủ');
                    }
                } else {
                    // Kiểm tra tồn kho nếu không có variant
                    if ($product->stock < $request->quantity) {
                        throw new \Exception('Số lượng sản phẩm trong kho không đủ');
                    }
                }

                // Tìm item trong giỏ hàng
                $cartItem = CartItem::where('cart_id', $cart->id)
                    ->where('product_id', $request->product_id)
                    ->where('variant_id', $request->variant_id)
                    ->first();

                if ($cartItem) {
                    // Cập nhật số lượng
                    $newQuantity = $cartItem->quantity + $request->quantity;
                    // Kiểm tra tồn kho với số lượng mới
                    $availableStock = $request->variant_id ? $variant->stock : $product->stock;
                    if ($newQuantity > $availableStock) {
                        throw new \Exception('Số lượng sản phẩm trong kho không đủ');
                    }
                    $cartItem->quantity = $newQuantity;
                    $cartItem->save();
                } else {
                    // Thêm mới item
                    CartItem::create([
                        'cart_id' => $cart->id,
                        'product_id' => $request->product_id,
                        'variant_id' => $request->variant_id,
                        'quantity' => $request->quantity,
                    ]);
                }
            });

            // Lấy slug của sản phẩm
            $product = Product::findOrFail($request->product_id);
            $slug = $product->slug;

            // Chuyển hướng với slug
            return redirect()->route('product.detail', ['slug' => $slug])
                ->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');

            // return redirect()->route('cart')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
            // return redirect()->route('product.detail', ['id' => $request->product_id])->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cập nhật số lượng sản phẩm trong giỏ hàng
     */
    public function update(Request $request, $itemId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
        ]);

        try {
            DB::transaction(function () use ($validated, $itemId) {
                $user = Auth::user();
                $cart = Cart::where('user_id', $user->id)->first();

                if (!$cart) {
                    throw new \Exception('Giỏ hàng không tồn tại');
                }

                $cartItem = CartItem::where('id', $itemId)
                    ->where('cart_id', $cart->id)
                    ->first();

                if (!$cartItem) {
                    throw new \Exception('Sản phẩm không có trong giỏ hàng');
                }

                // Kiểm tra tồn kho
                $product = $cartItem->product;
                $variant = $cartItem->variant;
                $availableStock = $variant ? $variant->stock : $product->stock;

                if ($validated['quantity'] > $availableStock) {
                    throw new \Exception("Chỉ còn {$availableStock} sản phẩm trong kho");
                }

                $cartItem->update(['quantity' => $validated['quantity']]);
                $cart->touch();
            });

            return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function updateQuantity(Request $request)
    {
        $itemId = $request->input('item_id');
        $quantity = $request->input('quantity');

        // Tìm item trong giỏ hàng
        $cartItem = CartItem::find($itemId);
        if ($cartItem) {
            $cartItem->quantity = $quantity;
            $cartItem->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Item not found'], 404);
        }
    }

    /**
     * Xóa sản phẩm khỏi giỏ hàng
     */
    public function remove($itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        try {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->first();

            if (!$cart) {
                return back()->with('error', 'Giỏ hàng không tồn tại');
            }

            Log::info('Trying to remove item', ['itemId' => $itemId, 'cartId' => $cart->id]);
            $cartItem = CartItem::where('id', $itemId)
                ->where('cart_id', $cart->id)
                ->first();

            if (!$cartItem) {
                Log::warning('Cart item not found', ['itemId' => $itemId, 'cartId' => $cart->id]);
                return back()->with('error', 'Sản phẩm không có trong giỏ hàng');
            }

            $cartItem->delete();
            $cart->touch();

            return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng');
        } catch (\Exception $e) {
            Log::error('Error removing item from cart: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }
    }

    /**
     * Hiển thị giỏ hàng
     */
    public function index()
    {
        // Bắt buộc user login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Load giỏ hàng + product + variant (kèm withTrashed ở CartItem model)
        $cart = Cart::with(['items.product', 'items.variant'])
            ->where('user_id', $user->id)
            ->first();

        // Nếu chưa có giỏ hàng thì items là collection rỗng
        $cartItems = $cart ? $cart->items : collect();

        //  Gắn cờ is_invalid cho mỗi item nếu variant đã xóa mềm
        $cartItems->each(function ($item) {
            $item->is_invalid = $item->variant && $item->variant->trashed();
        });

        // Log ra để debug
        Log::info('Cart Data:', ['cart' => $cart ? $cart->toArray() : null]);

        return view('client.cart.index', compact('cartItems'));
    }

    /**
     * Xóa toàn bộ giỏ hàng
     */
    public function clear()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->touch();
            }

            return back()->with('success', 'Đã xóa toàn bộ giỏ hàng');
        } catch (\Exception $e) {
            Log::error('Error clearing cart: ' . $e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại');
        }
    }
}
