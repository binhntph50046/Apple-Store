@extends('admin.layouts.app')
@section('title', 'User Details')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }

    .user-avatar {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .info-label {
        font-weight: 600;
        color: #666;
        min-width: 120px;
        display: inline-block;
    }

    .info-value {
        color: #333;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
    }

    .info-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .section-title {
        color: #2c3e50;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }
</style>

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Chi tiết người dùng</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chi tiết người dùng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- [ main content ] -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Thông tin người dùng</h5>
                            <div>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-info btn-sm me-2">
                                    <i class="ti ti-edit"></i> Sửa người dùng
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="ti ti-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif

                            <div class="row">
                                <!-- User Avatar Section -->
                                <div class="col-md-4 text-center mb-4">
                                    <div class="custom-shadow p-4">
                                        @if ($user->avatar)
                                            <img src="{{ asset($user->avatar) }}" alt="User Avatar"
                                                class="user-avatar mb-3">
                                        @else
                                            <div
                                                class="user-avatar mb-3 bg-light d-flex align-items-center justify-content-center">
                                                <i class="ti ti-user" style="font-size: 4rem; color: #666;"></i>
                                            </div>
                                        @endif
                                        <h4 class="mb-2">{{ $user->name }}</h4>
                                        <span
                                            class="status-badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- User Details Section -->
                                <div class="col-md-8">
                                    <div class="custom-shadow">
                                        <!-- Basic Information -->
                                        <div class="info-section">
                                            <h6 class="section-title">Thông tin cơ bản</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <span class="info-label">ID người dùng:</span>
                                                        <span class="info-value">{{ $user->id }}</span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Họ và tên:</span>
                                                        <span class="info-value">{{ $user->name }}</span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Email:</span>
                                                        <span class="info-value">{{ $user->email }}</span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Số điện thoại:</span>
                                                        <span
                                                            class="info-value">{{ $user->phone ?? 'Not provided' }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <span class="info-label">Giới tính:</span>
                                                        <span
                                                            class="info-value">{{ ucfirst($user->gender ?? 'Not specified') }}</span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Ngày sinh:</span>
                                                        <span class="info-value">
                                                            {{ $user->dob ? date('M d, Y', strtotime($user->dob)) : 'Not provided' }}
                                                        </span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Vai trò:</span>
                                                        <span class="info-value">{{ ucfirst($user->role) }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Address Information -->
                                        <div class="info-section">
                                            <h6 class="section-title">Thông tin địa chỉ</h6>
                                            <p class="mb-2">
                                                <span class="info-label">Địa chỉ:</span>
                                                <span class="info-value">{{ $user->address ?? 'Not provided' }}</span>
                                            </p>
                                        </div>

                                        <!-- Account Information -->
                                        <div class="info-section">
                                            <h6 class="section-title">Thông tin tài khoản</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <span class="info-label">Ngày tạo:</span>
                                                        <span
                                                            class="info-value">{{ $user->created_at->format('M d, Y H:i') }}</span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Ngày cập nhật:</span>
                                                        <span
                                                            class="info-value">{{ $user->updated_at->format('M d, Y H:i') }}</span>
                                                    </p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-2">
                                                        <span class="info-label">Lần đăng nhập cuối:</span>
                                                        <span class="info-value">
                                                            {{ $user->last_login ? \Carbon\Carbon::parse($user->last_login)->format('M d, Y H:i') : 'Never logged in' }}
                                                        </span>
                                                    </p>
                                                    <p class="mb-2">
                                                        <span class="info-label">Trạng thái tài khoản:</span>
                                                        <span class="info-value">{{ ucfirst($user->status) }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activity History Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="custom-shadow">
                                        <div class="info-section">
                                            <h6 class="section-title">Recent Activity</h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover table-borderless">
                                                    <thead>
                                                        <tr>
                                                            <th>Activity</th>
                                                            <th>Date</th>
                                                            <th>Details</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($user->activities ?? [] as $activity)
                                                            <tr>
                                                                <td>{{ $activity->type }}</td>
                                                                <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                                                                <td>{{ $activity->description }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="3" class="text-center">No activity records
                                                                    found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
