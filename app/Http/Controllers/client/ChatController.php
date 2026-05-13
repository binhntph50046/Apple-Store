<?php
// app/Http/Controllers/client/ChatController.php

namespace App\Http\Controllers\client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\User;
use App\Events\MessageSent;
use App\Models\ChMessage;
use Config;
use Illuminate\Support\Facades\Auth;

class ChatController
{
    // Giao diện chat
    public function index()
    {
        // Lấy admin đầu tiên trong hệ thống
        $admin = User::role('admin')->first();

        // Nếu không tìm thấy admin
        if (!$admin) {
            abort(500, 'Không tìm thấy tài khoản admin');
        }

        $id = $admin->id;
        $user = $admin; // ✅ Gán đúng đối tượng User

        $messengerColor = Config::get('chatify.colors.primary', '#1890ff');
        $dark_mode = Auth::check() ? (Auth::user()->dark_mode ?? 'light') : 'light';

        return view('client.chat.index', compact('id', 'user', 'messengerColor', 'dark_mode'));
    }
     

}
