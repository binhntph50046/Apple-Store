<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductView;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductSuggestionService
{
    public function suggestByPrice(Product $product, $range = 200000)
    {
        $variant = $product->variants()->first(); // hoặc logic lấy variant mặc định

        if (!$variant || !$variant->selling_price) {
            return collect(); // Không có giá
        }

        return Product::whereHas('variants', function ($query) use ($variant, $range) {
            $query->whereBetween('selling_price', [
                $variant->selling_price - $range,
                $variant->selling_price + $range
            ]);
        })->where('id', '!=', $product->id)
            ->take(6)
            ->get();
    }

    public function suggestByViewHistory($userId)
    {
        $viewed = ProductView::where('user_id', $userId)->orderByDesc('viewed_at')->limit(5)->pluck('product_id');

        return Product::whereIn('category_id', function ($q) use ($viewed) {
            $q->select('category_id')->from('products')->whereIn('id', $viewed);
        })->whereNotIn('id', $viewed)->limit(6)->get();
    }

    public function suggestBySearchHistory($userId)
    {
        $keyword = SearchHistory::where('user_id', $userId)->latest()->value('keyword');

        if (!$keyword) {
            return collect(); // Không có từ khóa
        }

        // Lấy ID các sản phẩm khớp từ khóa
        $matchedProductIds = Product::where('name', 'like', "%$keyword%")
            ->orWhere('description', 'like', "%$keyword%")
            ->pluck('id');

        // Lấy category của các sản phẩm đã tìm
        $categoryIds = Product::whereIn('id', $matchedProductIds)->pluck('category_id');

        // Trả về sản phẩm cùng danh mục, nhưng không trùng với sản phẩm tìm
        return Product::whereIn('category_id', $categoryIds)
            ->whereNotIn('id', $matchedProductIds)
            ->limit(6)
            ->get();
    }


    public function suggestTrendingThisWeek()
    {
        $variantIds = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed') // ✅ chỉ lấy đơn đã hoàn thành
            ->whereBetween('order_items.created_at', [now()->subDays(7), now()])
            ->select('order_items.product_variant_id', DB::raw('SUM(order_items.quantity) as total'))
            ->groupBy('order_items.product_variant_id')
            ->orderByDesc('total')
            ->pluck('order_items.product_variant_id');


        // Truy ngược từ variant_id → product_id
        $productIds = DB::table('product_variants')
            ->whereIn('id', $variantIds)
            ->pluck('product_id')
            ->unique()
            ->take(6); // Giới hạn 6 sản phẩm

        return Product::whereIn('id', $productIds)->get();
    }
    public function getAllSuggestions(Product $product, $userId = null)
    {
        return [
            'by_price' => $this->suggestByPrice($product),
            'by_view' => $this->suggestByViewHistory($userId),
            'by_search' => $this->suggestBySearchHistory($userId),
            'trending' => $this->suggestTrendingThisWeek(),
        ];
    }
    public function getUniqueSuggestions(Product $product, $userId = null)
    {
        $all = collect([
            $this->suggestByPrice($product),
            $this->suggestByViewHistory($userId),
            $this->suggestBySearchHistory($userId),
            $this->suggestTrendingThisWeek(),
        ])->flatten();

        return $all->unique('id')->values()->take(6); // Loại trùng theo id
    }
}
// This service class provides methods to suggest products based on various criteria such as price, view history, search history, and trending products.