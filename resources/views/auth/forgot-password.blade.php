@extends('auth.layout_auth')

@section('title', 'Quên mật khẩu - Apple Store')

@section('content')
    <div class="container" id="container">
        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <h1>Quên mật khẩu</h1>
            <p>Nhập địa chỉ email của bạn và chúng tôi sẽ gửi cho bạn một liên kết để đặt lại mật khẩu.</p>
            <input class="forgot-password-input form-control @error('email') is-invalid @enderror" type="text"
                name="email" placeholder="Nhập Email của bạn" value="{{ old('email') }}">
            @error('email')
                <div style="text-align: center;" class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror <button type="submit">Gửi Mail đặt lại mật khẩu</button>
            <a href="{{ route('login') }}" id="back-to-login">Quay lại đăng nhập</a>
        </form>
    </div>
@endsection