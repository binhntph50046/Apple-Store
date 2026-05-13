@extends('admin.layouts.app')
@section('title', 'Chi tiết người dùng')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
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
            <div class="row">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-body">
                            <form action="{{ route('admin.users.update', $user->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                
                                <!-- User Avatar Display -->
                                <div class="row mb-4">
                                    <div class="col-md-12 text-center">
                                        <div class="position-relative d-inline-block">
                                            <img src="{{ $user->avatar ? asset($user->avatar) : asset('/uploads/default/avatar_default.png') }}"
                                                class="rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #e9ecef;">
                                            <div class="mt-2">
                                                <h5 class="mb-1">{{ $user->name }}</h5>
                                                <p class="text-muted mb-0">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thông tin khách hàng -->
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="ti ti-user me-2"></i>Thông tin khách hàng</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Tên</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Email</label>
                                                <div class="position-relative">
                                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Số điện thoại</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="{{ $user->phone }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Địa chỉ</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="{{ $user->address }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Ngày sinh</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="{{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d/m/Y') : 'N/A' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Giới tính</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="{{ $user->gender ? ucfirst($user->gender) : 'N/A' }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Ngày tạo</label>
                                                <div class="position-relative">
                                                    <input type="text" class="form-control" value="{{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('d/m/Y H:i') : 'N/A' }}" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                                <div class="position-relative">
                                                    <select name="status" id="userStatus"
                                                        class="form-select @error('status') is-invalid @enderror">
                                                        <option value="">Chọn trạng thái</option>
                                                        <option value="active"
                                                            {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Hoạt động
                                                        </option>
                                                        <option value="inactive"
                                                            {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>
                                                            Không hoạt động</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ban Reason Section -->
                                        <div class="row mb-3" id="banReasonSection" style="display: none;">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Lý do khóa tài khoản <span class="text-danger">*</span></label>
                                                <div class="position-relative">
                                                    <textarea name="ban_reason" rows="4"
                                                        class="form-control @error('ban_reason') is-invalid @enderror"
                                                        placeholder="Nhập lý do khóa tài khoản của khách hàng...">{{ old('ban_reason', $user->ban_reason) }}</textarea>
                                                    @error('ban_reason')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                    <small class="form-text text-muted">
                                                        Lý do này sẽ được lưu lại và có thể thông báo cho khách hàng khi cần thiết.
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Current Ban Reason Display -->
                                        @if($user->ban_reason)
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="alert alert-warning">
                                                    <h6 class="alert-heading"><i class="ti ti-alert-triangle me-2"></i>Lý do khóa hiện tại:</h6>
                                                    <p class="mb-0">{{ $user->ban_reason }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary" id="updateBtn">
                                        <i class="ti ti-device-floppy me-1"></i>Cập nhật
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                        <i class="ti ti-arrow-left me-1"></i>Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // User edit form functionality
        window.addEventListener('load', function() {
            var statusSelect = document.getElementById('userStatus');
            var banReasonSection = document.getElementById('banReasonSection');
            var banReasonTextarea = document.querySelector('textarea[name="ban_reason"]');

            if (statusSelect && banReasonSection && banReasonTextarea) {
                function toggleBanReason() {
                    if (statusSelect.value === 'inactive') {
                        banReasonSection.style.display = 'block';
                        banReasonTextarea.setAttribute('required', 'required');
                    } else {
                        banReasonSection.style.display = 'none';
                        banReasonTextarea.removeAttribute('required');
                        banReasonTextarea.value = '';
                    }
                }

                // Initial check
                toggleBanReason();

                // Listen for changes
                statusSelect.addEventListener('change', toggleBanReason);
            }
        });
    </script>
@endsection
