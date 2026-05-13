<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminProfileController
{
    public function edit()
    {
        $admin = Auth::user();
        return view('admin.profile.index', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = User::findOrFail(Auth::id());
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'gender' => 'nullable|in:male,female,other',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $destinationPath = public_path('uploads/users');
            $avatar->move($destinationPath, $avatarName);
            $validated['avatar'] = 'uploads/users/' . $avatarName;
        }

        $admin->update($validated);
        return redirect()->route('admin.profile.index')->with('success', 'Cập nhật thông tin thành công!');
    }

    public function password()
    {
        $admin = Auth::user();
        return view('admin.profile.password', compact('admin'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::where('id', Auth::id())->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Mật khẩu đã được cập nhật thành công.');
    }
} 