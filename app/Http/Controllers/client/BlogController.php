<?php

namespace App\Http\Controllers\client;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController
{
    public function index()
    {
        $blogs = Blog::with(['category', 'author'])
            ->where('status','active')
            ->latest()
            ->get();
        return view('client.blog.index',compact('blogs'));
    }

    public function show($slug)
    {
        $blog = Blog::with('author', 'category')->where('slug', $slug)->firstOrFail();
        $relatedBlogs = Blog::where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->take(3)
            ->get();
        return view('client.blog.show', compact('blog', 'relatedBlogs'));
    }


}
