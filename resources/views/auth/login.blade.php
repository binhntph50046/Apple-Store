@extends('auth.layout_auth')

@section('title', 'Đăng nhập - Apple Store')

@section('content')
    <div style="position: absolute; top: 20px; left: 20px;">
        <a href="{{ url('/') }}" class="text-dark text-decoration-none" style="text-decoration: none; color: black" title="Về trang chủ">
            <i class="fa-solid fa-right-from-bracket"></i> Về trang chủ
        </a>
    </div>
    <div class="container" id="container">
        <div class="form-container sign-in">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <h1>Đăng nhập</h1>
                <div class="social-icons">
                    <a href="{{ route('auth.google.redirect') }}" class="icon"><i
                            class="fa-brands fa-google-plus-g"></i></a>
                    <a href="{{ route('auth.facebook.redirect') }}" class="icon"><i
                            class="fa-brands fa-facebook-f"></i></a>
                </div>
                <span>hoặc sử dụng mật khẩu email của bạn</span>
                <input type="text" name="email" placeholder="Email"
                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <input type="password" name="password" placeholder="Mật khẩu"
                    class="form-control @error('password') is-invalid @enderror">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div class="remember-forgot">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Nhớ mật khẩu</label>
                    </div>
                    <a href="{{ route('password.request') }}" id="forgot-password-link">Quên mật khẩu?</a>
                </div>
                <button type="submit">Đăng nhập</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Chào mừng trở lại!</h1>
                    <p>Đăng ký thông tin cá nhân của bạn để sử dụng tất cả các tính năng của trang web</p>
                    <button class="hidden" id="register"><a href="{{ route('register') }}" style="color: white" class="text-white">Đăng
                            kí</a></button>
                </div>
            </div>
        </div>
    </div>
@endsection
