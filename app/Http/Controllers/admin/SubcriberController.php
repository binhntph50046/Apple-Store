<?php

namespace App\Http\Controllers\Admin;
use App\Models\Subscriber;

use Illuminate\Http\Request;

class SubcriberController
{
    public function index()
    {
        $subscribers = Subscriber::latest()->paginate(10);
        return view('admin.subscribers.index', compact('subscribers'));
    }
    public function trash()
    {
        $subscribers = Subscriber::onlyTrashed()->paginate(12);
        return view('admin.subscribers.trash', compact('subscribers'));
    }
    /**
     * Xóa subscribers vào thùng rác (soft delete)
     */
    public function destroy(Subscriber $subscribers)
    {
        $subscribers->delete();

        return redirect()
            ->route('admin.subscribers.index')
            ->with('success', 'Đã xóa người đăng ký thành công.');
    }
    public function restore($id)
    {
        $subscribers = Subscriber::onlyTrashed()->findOrFail($id);
        $subscribers->restore();

        return redirect()->route('admin.subscribers.trash')->with('success', 'Khôi phục liên hệ thành công!');
    }
}
