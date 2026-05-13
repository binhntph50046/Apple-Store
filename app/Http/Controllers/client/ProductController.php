<?php

namespace App\Http\Controllers\client;

use App\Models\Product;
use App\Models\ProductView;
use App\Services\Product\ProductSuggestionService;
use App\Models\ProductVariant;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductController
{
    public function show($slug)
    {
        $product = Product::with([
            'category',
            'variants' => function ($query) {
                $query->with(['combinations.attributeValue.attributeType']);
            },
            'specifications.specification',
            'defaultVariant'
        ])->where('slug', $slug)->firstOrFail();

        // Increment view count
        $product->increment('views');

        // Tính số lượng đã bán của sản phẩm
        $totalSold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $product->id)
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->sum('order_items.quantity');

        // Tính số lượng đã bán theo từng variant
        $variantSoldData = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.product_id', $product->id)
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->select('order_items.product_variant_id', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->groupBy('order_items.product_variant_id')
            ->pluck('total_sold', 'product_variant_id')
            ->toArray();

        // Get related products (same category, excluding current product)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('slug', '!=', $slug)
            ->take(4)
            ->get();
        // Lưu dữ liệu xem sản phẩm
        ProductView::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
        ]);

        // Gợi ý sản phẩm
        $suggestions = [
            'unique' => app(ProductSuggestionService::class)->getUniqueSuggestions($product, Auth::id()),
        ];
        // dd($suggestions);

        return view('client.product.product-detail', compact('product', 'relatedProducts', 'suggestions', 'totalSold', 'variantSoldData'));
    }

    public function getProductDetails($id): JsonResponse
    {
        $product = Product::with([
            'category',
            'variants' => function ($query) {
                $query->with(['combinations.attributeValue.attributeType']);
            },
            'specifications.specification',
            'defaultVariant'
        ])->findOrFail($id);

        return response()->json($product);
    }

    public function getVariant($id): JsonResponse
    {
        $variant = ProductVariant::with(['combinations' => function ($query) {
            $query->with(['attributeValue' => function ($query) {
                $query->whereNull('deleted_at')
                    ->with('attributeType');
            }]);
        }])
            ->whereNull('deleted_at')
            ->findOrFail($id);

        return response()->json($variant);
    }
}
