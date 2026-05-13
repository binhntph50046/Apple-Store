<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\PageView;

class ActivityController
{
    public function index(Request $request)
    {
        // Lấy danh sách user_id có hoạt động, group theo user_id, phân trang
        $users = PageView::with('user')
            ->whereNotNull('user_id')
            ->whereHas('user', function ($query) {
                $query->role('user');
            })
            ->select('user_id')
            ->groupBy('user_id')
            ->paginate(10);
        return view('admin.activity.index', compact('users'));
    }

    public function show($userId)
    {
        $pageViews = PageView::where('user_id', $userId)->whereNotNull('duration')->latest()->paginate(15);
        return view('admin.activity.show', compact('pageViews', 'userId'));
    }
}
