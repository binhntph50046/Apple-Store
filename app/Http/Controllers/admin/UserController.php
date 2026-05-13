<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController
{
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = User::query();
        $query->where('id', '!=', Auth::id());

        if ($currentUser && $currentUser->hasRole('admin')) {
            $roles = ['user', 'staff'];
            $query->whereHas('roles', function ($q) {
                $q->whereIn('name', ['user', 'staff']);
            });
        } elseif ($currentUser && $currentUser->hasRole('staff')) {
            $roles = ['user'];
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'user');
            });
        } else {
            $roles = [];
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'dob' => 'required|date',
            'gender' => 'nullable|string|max:255|in:male,female,other',
            'status' => 'required|string|max:255|in:active,inactive',
        ], [
            'name.required' => 'Tên người dùng là bắt buộc.',
            'name.max' => 'Tên người dùng không được vượt quá 255 ký tự.',
            'email.required' => 'Email là bắt buộc.',
            'email.email' => 'Vui lòng nhập địa chỉ email hợp lệ.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'status.required' => 'Trạng thái tài khoản là bắt buộc.',
            'status.in' => 'Trạng thái tài khoản không hợp lệ.',
            'avatar.image' => 'Avatar phải là hình ảnh.',
            'avatar.mimes' => 'Avatar phải là file loại: jpeg, png, jpg, gif.',
            'avatar.max' => 'Kích thước avatar không được vượt quá 2MB.',
            'address.required' => 'Địa chỉ là bắt buộc.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'phone.required' => 'Số điện thoại là bắt buộc.',
            'dob.required' => 'Ngày sinh là bắt buộc.',
            'dob.date' => 'Vui lòng nhập ngày sinh hợp lệ.',
        ]);

        $validated['password'] = bcrypt($request->password);
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $avatarName = time() . '.' . $avatar->getClientOriginalExtension();
            $destinationPath = public_path('uploads/users');
            $avatar->move($destinationPath, $avatarName);
            $validated['avatar'] = 'uploads/users/' . $avatarName;
        }

        $user = User::create($validated);
        $user->assignRole('user'); // Gán role mặc định là 'user'

        return redirect()->route('admin.users.index')->with('success', 'Tạo tài khoản thành công');
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        // Chỉ validate status và ban_reason vì form chỉ gửi 2 trường này
        $validated = $request->validate([
            'status' => 'required|string|max:255|in:active,inactive',
            'ban_reason' => 'nullable|string|max:1000',
        ], [
            'status.required' => 'Trạng thái tài khoản là bắt buộc.',
            'status.in' => 'Trạng thái tài khoản không hợp lệ.',
            'ban_reason.max' => 'Lý do khóa không được vượt quá 1000 ký tự.',
        ]);

        // Kiểm tra nếu status là inactive thì ban_reason phải có
        if ($validated['status'] === 'inactive' && empty($request->ban_reason)) {
            return back()->withErrors(['ban_reason' => 'Lý do khóa tài khoản là bắt buộc khi chọn trạng thái Inactive.'])->withInput();
        }

        // Debug: Log dữ liệu để kiểm tra
        \Log::info('Cập nhật dữ liệu người dùng:', [
            'user_id' => $user->id,
            'status' => $validated['status'],
            'ban_reason' => $request->ban_reason,
            'request_data' => $request->all(),
            'validation_passed' => true
        ]);

        // Chỉ cập nhật status và ban_reason
        $dataToUpdate = [
            'status' => $validated['status'],
        ];

        // Xử lý ban_reason
        if ($validated['status'] === 'inactive') {
            $dataToUpdate['ban_reason'] = $request->ban_reason;
        } else {
            // Nếu chuyển về active thì xóa ban_reason
            $dataToUpdate['ban_reason'] = null;
        }



        $user->update($dataToUpdate);
        
        $message = $validated['status'] === 'inactive' 
            ? 'Đã khóa tài khoản thành công' 
            : 'Đã kích hoạt tài khoản thành công';
            
        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function trash()
    {
        $users = User::onlyTrashed()->paginate(10);
        return view('admin.users.trash', compact('users'));
    }

    public function banReasons()
    {
        $bannedUsers = User::where('status', 'inactive')
                           ->whereNotNull('ban_reason')
                           ->orderBy('updated_at', 'desc')
                           ->paginate(10);
        return view('admin.users.ban-reasons', compact('bannedUsers'));
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')->with('success', 'Khôi phục tài khoản thành công');
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $user->forceDelete();
        return redirect()->route('admin.users.trash')->with('success', 'Xóa vĩnh viễn tài khoản thành công');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa tài khoản thành công');
    }
}
