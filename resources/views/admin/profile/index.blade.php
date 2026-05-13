@extends('admin.layouts.app')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card p-3 mb-4">
                                    <div class="text-center">
                                        <div class="position-relative d-inline-block">
                                            <img id="avatarPreview"
                                                src="{{ $admin->avatar ? asset($admin->avatar) : asset('/uploads/default/avatar_default.png') }}"
                                                class="rounded-circle mb-2" style="width: 150px; height: 150px; object-fit: cover;">
                                            <label for="avatar"
                                                class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex justify-content-center align-items-center"
                                                style="cursor: pointer; width: 40px; height: 40px; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.15); font-size: 16px;">
                                                <i class="fas fa-camera"></i>
                                            </label>
                                            <input type="file" id="avatar" name="avatar" class="d-none" accept="image/*"
                                                onchange="previewImage(event)">
                                        </div>
                                        <h5 class="mt-2">{{ $admin->name }}</h5>
                                        <p class="text-muted">{{ $admin->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="card p-4">
                                    <h4 class="mb-4">Thông tin cá nhân</h4>
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <label for="name" class="form-label">Họ và tên</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ $admin->name }}">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email" value="{{ $admin->email }}"
                                                disabled>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="phone" class="form-label">Số điện thoại</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ $admin->phone }}">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="address" class="form-label">Địa chỉ</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="{{ $admin->address }}">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label for="dob" class="form-label">Ngày sinh</label>
                                            <input type="date" class="form-control" id="dob" name="dob"
                                                value="{{ $admin->dob ? (is_string($admin->dob) ? \Carbon\Carbon::parse($admin->dob)->format('Y-m-d') : $admin->dob->format('Y-m-d')) : '' }}">
                                        </div>
                                        <div class="col-6 mb-3">
                                            <label class="form-label">Giới tính</label>
                                            <div class="d-flex gap-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gender" id="male"
                                                        value="male" {{ $admin->gender === 'male' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="male">Nam</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gender" id="female"
                                                        value="female" {{ $admin->gender === 'female' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="female">Nữ</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="gender" id="other"
                                                        value="other" {{ $admin->gender === 'other' ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="other">Khác</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-start gap-2 mt-4">
                                        <button type="submit" class="btn btn-primary px-4">Cập nhật</button>
                                        <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary px-4">Hủy</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function previewImage(event) {
            const file = event.target.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatarPreview');
                img.src = e.target.result;
            }
            if (file) {
                reader.readAsDataURL(file);
            }
        }
    </script>
@endsection
