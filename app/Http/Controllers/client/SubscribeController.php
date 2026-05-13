<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Subscriber;

class SubscribeController
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:subscribers,email',
        ]);

        Subscriber::create($data);

        return redirect()->back()->with('success', 'Cảm ơn bạn đã đăng ký!');
    }
}
