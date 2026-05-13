@extends('auth.layout_auth')

@section('title', 'Đăng kí - Apple Store')

@section('content')
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="{{ url('/') }}" class="text-dark text-decoration-none" style="text-decoration: none; color: black" title="Về trang chủ">
            <i class="fa-solid fa-right-from-bracket"></i> Về trang chủ
        </a>
    </div>
    <div class="container active" id="container">
        <div class="form-container sign-up" style="height: 100%; overflow-y: auto">
            <form style="height: auto;padding: 18px 40px" action="{{ route('register') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <h1>Tạo tài khoản</h1>
                <div class="social-icons">
                    <a href="{{ route('auth.google.redirect') }}" class="icon"><i
                            class="fa-brands fa-google-plus-g"></i></a>
                    <a href="{{ route('auth.facebook.redirect') }}" class="icon"><i
                            class="fa-brands fa-facebook-f"></i></a>
                </div>
                <span>hoặc sử dụng email của bạn để đăng ký</span>

                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                    placeholder="Họ và tên" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input type="text" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email" value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input type="text" name="address" class="form-control @error('address') is-invalid @enderror"
                    placeholder="Địa chỉ" value="{{ old('address') }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    placeholder="Số điện thoại" value="{{ old('phone') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                {{-- <div class="row-inputs"> --}}
                    <div class="gender-container w-100">
                        <select name="gender" id="gender" class="form-control @error('gender') is-invalid @enderror">
                            <option value="">Chọn giới tính</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                        </select>
                        @error('gender')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <input type="date" name="dob" id="dob"
                        class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}">
                    @error('dob')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                {{-- </div> --}}

                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                    placeholder="Mật khẩu">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <button type="submit">Đăng kí</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Chào mừng trở lại!</h1>
                    <p>Nhập thông tin cá nhân của bạn để sử dụng tất cả các tính năng của trang web</p>
                    <button class="hidden" id="login"><a href="{{ route('login') }}" style="color: white" class="text-white">Đăng
                            nhập</a></button>
                </div>
            </div>
        </div>
    </div>
@endsection
