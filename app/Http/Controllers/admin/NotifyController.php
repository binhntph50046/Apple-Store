<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;
use App\Models\Notification;

class NotifyController
{
    // Danh sách thông báo
    public function index(Request $request)
    {
        $notifications = DatabaseNotification::whereNull('deleted_at')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.notify.index', compact('notifications'));
    }

    // Xóa mềm 1 thông báo
    public function destroy($id)
    {
        $notification = DatabaseNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notify.index')->with('success', 'Đã xóa thông báo.');
    }

    // Hiển thị thùng rác
    public function trash()
    {
        $notifications = Notification::onlyTrashed()
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('admin.notify.trash', compact('notifications'));
    }

    // Khôi phục thông báo
    public function restore($id)
    {
        $notification = Notification::withTrashed()->findOrFail($id);
        $notification->restore();

        return redirect()->route('admin.notify.index')->with('success', 'Đã khôi phục thông báo.');
    }

    // Xóa vĩnh viễn
    public function forceDelete($id)
    {
        $notification = Notification::onlyTrashed()->findOrFail($id);
        $notification->forceDelete();

        return redirect()->route('admin.notify.trash')->with('success', 'Đã xóa vĩnh viễn thông báo.');
    }

    // Đánh dấu đã đọc
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Thông báo đã được đánh dấu là đã đọc.');
    }
}