<?php

namespace App\Http\Controllers\auth;

use App\Mail\VerifyEmailCustom;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use App\Events\UserCreated;
use App\Notifications\AdminDatabaseNotification;

class AuthController
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'address' => 'required',
            'phone' => 'required',
            'dob' => 'required|date',
            'gender' => 'required|in:male,female,other',
        ], [
            'name.required' => 'Vui lòng nhập họ tên.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email đã tồn tại.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'address.required' => 'Vui lòng nhập địa chỉ.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'dob.required' => 'Vui lòng chọn ngày sinh.',
            'dob.date' => 'Ngày sinh không hợp lệ.',
            'gender.required' => 'Vui lòng chọn giới tính.',
            'gender.in' => 'Giới tính phải là Nam, Nữ hoặc Khác.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'gender' => $request->gender,
        ]);

        $user->assignRole('user');

        // Tự động login người dùng
        // Auth::login($user);

        // Tạo link xác minh thủ công
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $user->id,
                'hash' => sha1($user->email),
            ]
        );

        // Gửi email xác minh
        Mail::to($user->email)->send(new VerifyEmailCustom($user, $verificationUrl));

        // Gửi event realtime
        event(new UserCreated($user));

        // Gửi notification vào database cho tất cả admin
        $admins = User::role('admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new AdminDatabaseNotification([
                'type' => 'user_created',
                'title' => 'Tài khoản mới',
                'message' => $user->name . ' vừa đăng ký.',
                'user_id' => $user->id,
                'url' => route('admin.users.index'), // link đến trang quản lý người dùng
                'created_at' => now(),
            ]));
        }

        return redirect('/login')->with('success', 'Đăng ký thành công. Vui lòng kiểm tra email để xác minh tài khoản!');
    }
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'password.required' => 'Vui lòng nhập mật khẩu.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);


        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Cập nhật last_login
            $user->update(['last_login' => now()]);

            $hasAccess = $user->hasRole(['admin', 'staff']);

            if ($hasAccess) {
                return redirect()->intended('/admin');
            } else {
                return redirect()->intended('/')->with('success', 'Đăng nhập thành công!');
            }
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không đúng.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
