@extends('admin.layouts.app')
@section('title', 'Quản lý người dùng')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }
    .table th, .table td { padding: 0.35rem 0.5rem; }
    .table .text-nowrap { white-space: nowrap; }
</style>

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Người dùng</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Người dùng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Danh sách người dùng</h5>
                            <div class="card-header-right">
                                @can('create users')
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm rounded-3 me-2">
                                        <i class="ti ti-plus"></i> Thêm người dùng
                                    </a>
                                @endcan
                                @can('view users')
                                    <a href="{{ route('admin.users.trash') }}" class="btn btn-danger btn-sm rounded-3 me-2">
                                        <i class="ti ti-trash"></i> Thùng rác
                                    </a>
                                    <a href="{{ route('admin.users.ban-reasons') }}" class="btn btn-warning btn-sm rounded-3">
                                        <i class="ti ti-alert-triangle"></i> Danh sách tài khoản bị khóa
                                    </a>
                                @endcan
                            </div>
                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <!-- Filter -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 mb-3">
                                        <div class="col-md-3">
                                            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên..."
                                                value="{{ request('search') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <select name="role" class="form-select">
                                                <option value="">-- Lọc theo vai trò --</option>
                                                @foreach ($roles as $role)
                                                    <option value="{{ $role }}"
                                                        {{ request('role') == $role ? 'selected' : '' }}>
                                                        {{ ucfirst($role) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="status" class="form-select">
                                                <option value="">-- Lọc theo trạng thái --</option>
                                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-3">
                                            <button type="submit" class="btn btn-primary btn-sm me-2">Lọc</button>
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">Reset</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="table custom-shadow">
                                <table class="table table-hover table-borderless align-middle" style="font-size: 14px;">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Ảnh đại diện</th>
                                            <th>Họ tên</th>
                                            <th>Email</th>
                                            <th>Vai trò</th>
                                            <th>Trạng thái</th>
                                            <th class="text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($users as $user)
                                            <tr>
                                                <td>{{ $user->id }}</td>
                                                <td>
                                                    @if ($user->avatar)
                                                        <img src="{{ asset($user->avatar) }}" alt="Avatar"
                                                             style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                                                    @else
                                                        <span class="text-muted">Không có ảnh</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ ucfirst($user->getRoleNames()->first() ?? 'Không có') }}</td>
                                                <td>
                                                    <span
                                                        class="badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $user->status === 'active' ? 'Hoạt động' : 'Ngừng hoạt động' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @can('edit users')
                                                        <a href="{{ route('admin.users.edit', $user->id) }}"
                                                            class="btn btn-info btn-sm rounded-3 me-2" title="Sửa">
                                                            <i class="ti ti-edit"></i> 
                                                        </a>
                                                    @endcan

                                                    @if(auth()->user()->hasRole('admin'))
                                                        @can('addrole')
                                                            <a href="{{ route('admin.roles.edit', $user->id) }}"
                                                                class="btn btn-warning btn-sm rounded-3 me-2" title="Gán vai trò">
                                                                <i class="ti ti-user-check"></i> 
                                                            </a>
                                                        @endcan
                                                    @endif

                                                    @can('delete users')
                                                        <form action="{{ route('admin.users.destroy', $user->id) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm rounded-3"
                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này không?')" title="Xóa">
                                                                <i class="ti ti-trash"></i> 
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">Không tìm thấy người dùng nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                <!-- Pagination -->
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>


@endsection

@push('styles')
<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px !important;
        background-color: #fff;
        padding: 16px;
    }
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    .table td {
        vertical-align: middle;
    }
    .table-borderless td, .table-borderless th {
        border: 0;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(0,0,0,.075);
    }
</style>
@endpush
