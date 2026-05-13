<?php

namespace App\Http\Controllers\client;

use App\Models\Banner;
use App\Models\Blog;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\ProductVariant;
use App\Models\ProductReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class HomeController
{
    public function index()
    {
        // banners
        $banners = Banner::where('status', 'active')
            ->orderBy('order')
            ->get();

        // Lấy sản phẩm theo nhóm 3 sản phẩm lượt view cao xuống thấp
        $mostViewedProducts = Product::where('status', 'active')
            ->with(['variants' => function ($query) {
                $query->orderByDesc('is_default');
            }])
            ->orderBy('views', 'desc')
            ->take(9) // Lấy 9 sản phẩm (3 hàng)
            ->get();

        // Lấy 3 sản phẩm mới nhất trong tháng hiện tại
        $latestProducts = Product::where('status', 'active')
            ->with(['variants' => function ($query) {
                $query->orderByDesc('is_default');
            }])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->orderBy('created_at', 'desc')
            ->take(9)
            ->get();

        // Lấy ID sản phẩm trong wishlist của người dùng
        $wishlistProductIds = [];
        if (Auth::check()) {
            $wishlistProductIds = Wishlist::where('user_id', Auth::id())
                ->pluck('product_id')
                ->toArray();
        }

        $products = Product::where('status', 'active')->get();

        // Lấy 3 bài viết mới nhất
        $latestBlogs = Blog::with(['category', 'author'])
            ->where('status', 'active')
            ->latest()
            ->take(3)
            ->get();

        $topRatedVariants = ProductVariant::with(['product'])
            ->withCount(['reviews as reviews_count' => function ($query) {
                $query->whereNotNull('order_id')->whereNull('deleted_at');
            }])
            ->withAvg(['reviews as avg_rating' => function ($query) {
                $query->whereNotNull('order_id')->whereNull('deleted_at');
            }], 'rating')
            ->having('reviews_count', '>', 0) // CHỈ lấy biến thể có ít nhất 1 đánh giá
            ->orderByDesc('avg_rating')
            ->orderByDesc('reviews_count')
            ->take(9)
            ->get();


        return view('client.index', [
            'banners' => $banners,
            'mostViewedProducts' => $mostViewedProducts,
            'latestProducts' => $latestProducts,
            'wishlistProductIds' => $wishlistProductIds,
            'products' => $products,
            'latestBlogs' => $latestBlogs,
            'topRatedVariants' => $topRatedVariants
        ]);
    }


    public function incrementView($id)
    {
        $product = Product::findOrFail($id);
        $product->increment('views');
        return response()->json(['success' => true, 'views' => $product->views]);
    }

    public function getProduct($id)
    {
        $product = Product::with(['variants' => function ($query) {
            $query->whereNull('deleted_at')
                ->with(['combinations' => function ($query) {
                    $query->with(['attribute_value' => function ($query) {
                        $query->whereNull('deleted_at')
                            ->with('attribute_type');
                    }]);
                }]);
        }])
            ->where('status', 'active')
            ->findOrFail($id);

        return response()->json($product);
    }

    public function getVariant($id)
    {
        $variant = ProductVariant::with(['combinations' => function ($query) {
            $query->with(['attribute_value' => function ($query) {
                $query->whereNull('deleted_at')
                    ->with('attribute_type');
            }]);
        }])
            ->whereNull('deleted_at')
            ->findOrFail($id);

        return response()->json($variant);
    }
}
