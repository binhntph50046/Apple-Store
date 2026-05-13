<?php
namespace App\Http\Controllers\Admin;

use Chatify\Http\Controllers\ChatifyMessengerController as BaseController;
use Illuminate\Http\Request;
use Chatify\Models\ChMessage;
use Chatify\Facades\Chatify;
use Auth;
use App\Models\User;
use Config;


use App\Providers\RouteServiceProvider; // Adjust namespace if different

class MessengerController
{
    public function index(Request $request)
    {
        // Kiểm tra xem người dùng đã xác thực chưa
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
        }

        $id = $request->input('id'); // ID người nhận từ request
        if (!$id && Auth::check()) {
            $id = Auth::id(); // Mặc định là ID người dùng hiện tại
        }

        $messengerColor = Config::get('chatify.colors.primary', '#1890ff');
        $dark_mode = Auth::check() ? (Auth::user()->dark_mode ?? 'light') : 'light';

        // Kiểm tra vai trò người dùng
        if (!Auth::user()->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('home') // Chuyển hướng đến trang chủ người dùng
                ->with('error', 'Bạn không có quyền truy cập vào chat admin.');
        }

        return view('admin.livechat.index', compact('id', 'messengerColor', 'dark_mode'));
    }

    // Tùy chọn: Phương thức cho người dùng chat với admin
    public function userChat(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước.');
        }

        $id = Auth::id(); // ID người dùng hiện tại
        $admin = User::role('admin')->first(); // Lấy admin đầu tiên
        if ($admin) {
            $messengerColor = Config::get('chatify.colors.primary', '#1890ff');
            $dark_mode = Auth::check() ? (Auth::user()->dark_mode ?? 'light') : 'light';
            return view('user.chat', compact('id', 'messengerColor', 'dark_mode', 'admin')); 
        }

        return redirect()->route('home')->with('error', 'Không có admin nào để chat.');
    }
    public function fetch(Request $request)
    {
        $me    = Auth::id();
        $other = $request->id;

        // Lấy message hai chiều
        $msgs = ChMessage::where(function($q) use ($me, $other) {
                    $q->where('from_id', $me)
                      ->where('to_id', $other);
                })
                ->orWhere(function($q) use ($me, $other) {
                    $q->where('from_id', $other)
                      ->where('to_id', $me);
                })
                ->orderBy('created_at', 'asc')
                ->get();

        // Chuyển mỗi message thành HTML card
        $html = $msgs
            ->map(fn($m) => Chatify::messageCard(Chatify::parseMessage($m), true))
            ->implode('');

        return response()->json([
            'html' => $html
        ]);
    }

}