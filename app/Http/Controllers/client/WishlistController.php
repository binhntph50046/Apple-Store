<?php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WishlistController 
{
    public function index()
    {
        $products = Auth::user()->wishlists()->with('variants')->get();
        return view('client.wishlist.index', ['wishlists' => $products]);
    }

    public function toggle($productId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng đăng nhập để quản lý danh sách yêu thích!',
                'type' => 'danger'
            ], 401);
        }

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => false,
                'message' => 'Sản phẩm không tồn tại!',
                'type' => 'danger'
            ], 404);
        }

        $isInWishlist = $user->wishlists()->where('product_id', $productId)->exists();

        if ($isInWishlist) {
            // Xóa khỏi wishlist
            $user->wishlists()->detach($productId);
            return response()->json([
                'status' => true,
                'message' => 'Đã xóa sản phẩm "' . $product->name . '" khỏi danh sách yêu thích!',
                'type' => 'success',
                'in_wishlist' => false
            ]);
        } else {
            // Thêm vào wishlist
            $user->wishlists()->attach($productId);
            return response()->json([
                'status' => true,
                'message' => 'Đã thêm sản phẩm "' . $product->name . '" vào danh sách yêu thích!',
                'type' => 'success',
                'in_wishlist' => true
            ]);
        }
    }
}
