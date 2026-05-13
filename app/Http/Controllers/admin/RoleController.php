<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController 
{
    public function edit(User $user)
    {
        // Chỉ lấy role user và staff
        $roles = Role::whereIn('name', ['user', 'staff'])->pluck('name')->toArray();
        $currentRole = $user->getRoleNames()->first() ?? 'None';
        return view('admin.roles.edit', compact('user', 'roles', 'currentRole'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:staff,user',
        ]);

        $user->syncRoles($request->role);
        return redirect()->route('admin.users.index')->with('success', 'Cập nhật vai trò thành công');
    }
}