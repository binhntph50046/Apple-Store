@extends('client.layouts.app')
@section('title', 'Tra cứu đơn hàng - Apple Store')
@section('content')
    <style>
        .guest-tracking-wrapper {
            margin-top: 100px;
            /* hoặc giá trị phù hợp với chiều cao header của bạn */
        }

        @media (max-width: 768px) {
            .guest-tracking-wrapper {
                margin-top: 70px;
            }
        }
    </style>
    <div class="container py-5 guest-tracking-wrapper">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Theo dõi đơn hàng </h3>
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('order.guest.tracking') }}" method="GET">
                            <div class="mb-3">
                                <label for="order_code" class="form-label">Mã đơn hàng</label>
                                <input type="text" class="form-control" id="order_code" name="order_code"
                                    placeholder="Nhập mã đơn hàng"
                                    value="{{ request('order_code') }}">
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Nhập email" value="{{ request('email') }}">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-search me-2"></i>Tra cứu đơn hàng
                                </button>
                            </div>
                        </form>

                        <div class="mt-4 text-center">
                            <p class="text-muted mb-0">Bạn đã có tài khoản?</p>
                            <a href="{{ route('login') }}" class="text-primary">Đăng nhập để quản lý đơn hàng dễ dàng
                                hơn</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
