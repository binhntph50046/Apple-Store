@extends('admin.layouts.app')
@section('title', 'Lý do khóa tài khoản')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
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
                            <h5 class="m-b-10">Lý do khóa tài khoản</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Lý do khóa</li>
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
                        <h5>Tài khoản bị khóa</h5>
                        <div>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">
                                <i class="ti ti-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <!-- Table -->
                        <div class="table custom-shadow">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Ảnh đại diện</th>
                                        <th>Tên</th>
                                        <th>Email</th>
                                        <th>Vai trò</th>
                                        <th>Lý do khóa</th>
                                        <th>Ngày khóa</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($bannedUsers as $user)
                                        <tr>
                                            <td>{{ $user->id }}</td>
                                            <td>
                                                @if ($user->avatar)
                                                    <img src="{{ asset($user->avatar) }}" alt="Avatar" class="img-thumbnail" style="max-width: 60px;">
                                                @else
                                                    <span class="text-muted">Không có ảnh</span>
                                                @endif
                                            </td>
                                            
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ ucfirst($user->getRoleNames()->first() ?? 'Không có vai trò') }}</td>
                                            <td>
                                                <div class="text-danger fw-semibold" 
                                                     data-bs-toggle="tooltip" 
                                                     data-bs-placement="top" 
                                                     title="{{ $user->ban_reason }}">
                                                    {{ Str::limit($user->ban_reason, 40) }}
                                                    @if(strlen($user->ban_reason) > 40)
                                                        <i class="ti ti-dots text-muted"></i>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted">
                                                    {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : 'N/A' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning btn-sm me-1" title="Sửa">
                                                    <i class="ti ti-edit"></i> Sửa
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Không tìm thấy tài khoản nào bị khóa.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            {{ $bannedUsers->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush 