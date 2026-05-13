@extends('client.layouts.app')

@section('title', 'Đổi mật khẩu')
@section('content')
    @include('client.profile.partials.notification')
    <div class="container" style="margin-top: 150px; margin-bottom: 50px;">
        <div class="row">
            @include('client.profile.partials.sidebar')

            <!-- Main Content Area -->
            <div class="col-md-9">
                <div class="card p-4" style="background: #ffffff; border: none; border-radius: 8px;">
                    <h4 class="mb-4">Đổi mật khẩu</h4>

                    <form action="{{ route('profile.update-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Kiểm tra nếu người dùng đã có mật khẩu thì mới hiển thị ô "Mật khẩu hiện tại" --}}
                        @if (Auth::user()->password)
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                                <div class="input-group">
                                    <input type="password"
                                        class="form-control @error('current_password') is-invalid @enderror"
                                        id="current_password" name="current_password">
                                    <span class="input-group-text">
                                        <i class="fas fa-eye toggle-password" data-target="#current_password"></i>
                                    </span>
                                    @error('current_password')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                <span class="input-group-text">
                                    <i class="fas fa-eye toggle-password" data-target="#password"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <div class="input-group">
                                <input type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation" name="password_confirmation">
                                <span class="input-group-text">
                                    <i class="fas fa-eye toggle-password" data-target="#password_confirmation"></i>
                                </span>
                                @error('password_confirmation')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <script>
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function() {
                const target = document.querySelector(this.dataset.target);
                const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
                target.setAttribute('type', type);

                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
@endsection
