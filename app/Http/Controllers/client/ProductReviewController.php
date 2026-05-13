<?php

namespace App\Http\Controllers\client;

use App\Models\ProductReview;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductReviewController
{
    public function store(Request $request, Order $order, $variantId)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'completed') {
            abort(403);
        }

        $variant = ProductVariant::withTrashed()->findOrFail($variantId);

        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('order_id', $order->id)
            ->where('variant_id', $variant->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'Bạn đã đánh giá biến thể này rồi.');
        }

        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('reviews', 'public');
                $imagePaths[] = $path;
            }
        }

        ProductReview::create([
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'product_id' => $variant->product_id,
            'variant_id' => $variant->id,
            'rating' => $request->rating,
            'review' => $request->review,
            'images' => $imagePaths,
        ]);

        return back()->with('success', 'Đánh giá của bạn đã được gửi thành công.');
    }

    public function index()
    {
        $reviews = ProductReview::with(['user', 'product', 'variant'])->latest()->paginate(10);
        return view('admin.reviews.index', compact('reviews'));
    }

    public function updateStatus(Request $request, ProductReview $review)
    {
        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        $review->update(['status' => $request->status]);

        return back()->with('success', 'Cập nhật trạng thái đánh giá thành công.');
    }

    public function create(Order $order)
    {
        if ($order->user_id !== Auth::id() || $order->status !== 'completed') {
            abort(403);
        }

        // Lấy tất cả item (biến thể) của đơn hàng, kể cả đã xóa mềm
        $items = $order->items()->with(['variant' => function($q) {
            $q->withTrashed();
        }, 'product'])->get();

        // Lấy các review đã có của user cho đơn này
        $reviews = ProductReview::where('user_id', Auth::id())
            ->where('order_id', $order->id)
            ->with(['product', 'variant'])
            ->get()
            ->keyBy('variant_id');

        return view('client.review.create', compact('order', 'items', 'reviews'));
    }

    // Thêm hàm xem lịch sử đánh giá nếu chưa có
    public function history(Order $order, ProductVariant $variant)
    {
        $review = ProductReview::where('user_id', Auth::id())
            ->where('order_id', $order->id)
            ->where('variant_id', $variant->id)
            ->firstOrFail();

        $product = $variant->product;

        return view('client.review.history', compact('review', 'order', 'product', 'variant'));
    }

    public function historyAll(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Lấy tất cả review của user cho đơn này
        $reviews = ProductReview::where('user_id', Auth::id())
            ->where('order_id', $order->id)
            ->with(['product', 'variant'])
            ->get();

        return view('client.review.history', compact('order', 'reviews'));
    }
}
