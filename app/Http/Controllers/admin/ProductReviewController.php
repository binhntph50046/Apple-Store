<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

class ProductReviewController
{

    public function index(Request $request)
    {
        $query = \App\Models\ProductReview::with(['user', 'product', 'variant']);

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        $reviews = $query->latest()->paginate(10);

        // Lấy cả sản phẩm đã bị xóa mềm (soft deleted)
        $products = \App\Models\Product::withTrashed()->orderBy('name')->get();

        return view('admin.review.index', compact('reviews', 'products'));
    }
    public function show($id)
    {
        $review = \App\Models\ProductReview::with(['user', 'product', 'variant'])
            ->findOrFail($id);

        return view('admin.review.show', compact('review'));
    }
}
