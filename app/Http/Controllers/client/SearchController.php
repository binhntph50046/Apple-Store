<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Blog;
use App\Models\SearchHistory;
use Illuminate\Support\Facades\Auth;

class SearchController
{
    public function index(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return view('client.search.index', ['query' => '', 'products' => collect(), 'blogs' => collect()]);
        }
        if (Auth::check()) {
            SearchHistory::create([
                'user_id' => Auth::id(),
                'keyword' => $query,
            ]);
        }

        // Tìm kiếm sản phẩm
        $products = Product::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->paginate(12);

        // Tìm kiếm bài viết
        $blogs = Blog::where('title', 'like', "%{$query}%")
            ->orWhere('content', 'like', "%{$query}%")
            ->paginate(6);

        return view('client.search.index', compact('query', 'products', 'blogs'));
    }

    public function suggestions(\Illuminate\Http\Request $request)
    {
        $q = $request->input('q');
        if (!$q) {
            return response()->json(['products' => [], 'blogs' => []]);
        }
        $products = \App\Models\Product::where('name', 'like', "%{$q}%")
            ->limit(5)
            ->get(['name', 'slug']);
        $blogs = \App\Models\Blog::where('title', 'like', "%{$q}%")
            ->limit(3)
            ->get(['title', 'slug']);
        return response()->json([
            'products' => $products,
            'blogs' => $blogs,
        ]);
    }
}
