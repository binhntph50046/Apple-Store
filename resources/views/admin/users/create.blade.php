@extends('admin.layouts.app')
@section('title', 'Thêm người dùng mới')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Thêm người dùng mới</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Người dùng</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Thêm người dùng mới</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-body">
                            <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name') }}">

                                        </div>
                                        @error('name')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email') }}">

                                        </div>
                                        @error('email')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror">

                                        </div>
                                        @error('password')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Xác nhận mật khẩu <span
                                                class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="password" name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror">

                                        </div>
                                        @error('password_confirmation')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Số điện thoại</label>
                                        <div class="position-relative">
                                            <input type="text" name="phone"
                                                class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone') }}">

                                        </div>
                                        @error('phone')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Địa chỉ</label>
                                        <div class="position-relative">
                                            <input type="text" name="address"
                                                class="form-control @error('address') is-invalid @enderror"
                                                value="{{ old('address') }}">

                                        </div>
                                        @error('address')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Ngày sinh</label>
                                        <div class="position-relative">
                                            <input type="date" name="dob"
                                                class="form-control @error('dob') is-invalid @enderror"
                                                value="{{ old('dob') }}">

                                        </div>
                                        @error('dob')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Giới tính</label>
                                        <div class="position-relative">
                                            <select name="gender"
                                                class="form-select @error('gender') is-invalid @enderror">
                                                <option value="">Chọn giới tính</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam
                                                </option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>
                                                    Nữ</option>
                                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>
                                                    Khác</option>
                                            </select>

                                        </div>
                                        @error('gender')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Ảnh đại diện</label>
                                        <div class="position-relative">
                                            <input type="file" name="avatar"
                                                class="form-control @error('avatar') is-invalid @enderror">

                                        </div>
                                        @error('avatar')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <select name="status"
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="">Chọn trạng thái</option>
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                    Hoạt động</option>
                                                <option value="inactive"
                                                    {{ old('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                            </select>

                                        </div>
                                        @error('status')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary">Thêm người dùng</button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Hủy</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
