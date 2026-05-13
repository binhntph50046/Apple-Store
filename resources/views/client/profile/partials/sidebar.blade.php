<!-- Left Sidebar -->
<div class="col-md-3">
    <div class="card p-3 mb-3" style="background: #ffffff; border: none; border-radius: 8px;">
        <div class="d-flex align-items-center mb-3">
            <img src="{{ auth()->user()->avatar ?? '/uploads/default/avatar_default.png' }}" alt="User Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px;">
            <div>
                <h5 class="mb-0">{{ auth()->user()->name }}</h5>
                <small class="text-muted">Sửa hồ sơ</small>
            </div>
        </div>
        <hr style="border: 1px solid #b8b8b8; margin: 0 0 15px 0;">
        <ul class="list-group list-group-flush">
            <li class="list-group-item" style="background: transparent; border: none;">
                <a href="{{ route('profile.index') }}" class="d-flex align-items-center text-decoration-none {{ request()->routeIs('profile.index') ? 'text-danger fw-bold' : 'text-dark' }}">
                    <i class="fas fa-user me-2"></i> Hồ sơ
                </a>
            </li>
            <li class="list-group-item" style="background: transparent; border: none;">
                <a href="{{ route('profile.password') }}" class="d-flex align-items-center text-decoration-none {{ request()->routeIs('profile.password') ? 'text-danger fw-bold' : 'text-dark' }}">
                    <i class="fas fa-lock me-2"></i> Đổi mật khẩu
                </a>
            </li>
            {{-- <li class="list-group-item" style="background: transparent; border: none;">
                <a href="{{ route('profile.orders') }}" class="d-flex align-items-center text-decoration-none {{ request()->routeIs('profile.orders') ? 'text-danger fw-bold' : 'text-dark' }}">
                    <i class="fas fa-receipt me-2"></i> Đơn hàng
                </a>
            </li> --}}
        </ul>
    </div>
</div>
