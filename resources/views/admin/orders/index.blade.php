@extends('admin.layouts.app')
@section('title', 'Quản lý đơn hàng - Apple Store')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Đơn hàng</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Đơn hàng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

           
            <div class="card custom-shadow border-0" style="border-radius:0;">
                <div class="card-body">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 mb-3">
                                <div class="col-md-3">
                                    <input type="text" name="search" class="form-control"
                                        placeholder="Tìm kiếm tên, email, ID..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">-- Tất cả trạng thái --</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                        <option value="preparing" {{ request('status') == 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                        <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Đã hoàn đơn</option>
                                        <option value="partially_returned" {{ request('status') == 'partially_returned' ? 'selected' : '' }}>Đã hoàn trả một phần</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="payment_status" class="form-select">
                                        <option value="">-- Tất cả thanh toán --</option>
                                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                                        <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Chưa thanh toán</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary d-flex align-items-center justify-content-center">Tìm kiếm</button>
                                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary d-flex align-items-center justify-content-center">Đặt lại</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                

                    <div class="table-responsive">
                        <table class="table table-hover align-middle table-modern" style="border-radius:0;">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Ngày đặt</th>
                                    <th>Khách hàng</th>
                                    <th>Sản phẩm</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Thanh toán</th>
                                    <th>Trạng thái</th>
                                    <th class="text-center">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="fw-bold text-primary">{{ $order->order_code }}</td>
                                        <td>
                                            <div class="text-dark">{{ $order->created_at->format('d/m/Y') }}</div>
                                            <div class="text-muted small">{{ $order->created_at->format('H:i') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $order->shipping_name }}</div>
                                            <div class="text-muted small">{{ $order->shipping_email }}</div>
                                            <div class="text-muted small">{{ $order->shipping_phone }}</div>
                                            <div class="text-muted small">{{ $order->shipping_address }}</div>
                                        </td>
                                        <td>
                                            @if($order->items && count($order->items) > 0 )
                                                <div class="d-flex gap-2 flex-wrap">
                                                    @foreach($order->items as $item)
                                                        @if($item->product)
                                                            <div class="border rounded" style="width: 80px; height: 80px;">
                                                                @if($item->variant && $item->variant->image)
                                                                    <img src="{{ asset($item->variant->image) }}" 
                                                                        alt="{{ $item->product->name }}" 
                                                                        class="w-100 h-100"
                                                                        style="object-fit: cover;"
                                                                        title="{{ $item->product->name }}">
                                                                @elseif($item->product->default_variant_image)
                                                                    <img src="{{ asset($item->product->default_variant_image) }}" 
                                                                        alt="{{ $item->product->name }}" 
                                                                        class="w-100 h-100"
                                                                        style="object-fit: cover;"
                                                                        title="{{ $item->product->name }}">
                                                                @else
                                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light">
                                                                        <i class="fas fa-image text-muted fs-4"></i>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-muted fst-italic">Không có sản phẩm</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold text-dark">{{ number_format($order->total_price) }} VNĐ</td>
                                        <td>
                                            @switch($order->payment_method)
                                                @case('cod')
                                                    <span class="badge rounded-pill bg-secondary">COD</span>
                                                    @break
                                                @case('vnpay')
                                                    <span class="badge rounded-pill" style="background: #00bcd4">VNPAY</span>
                                                    @break
                                                @default
                                                    <span class="badge rounded-pill bg-secondary">{{ $order->payment_method }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <span
                                                class="badge rounded-pill {{ $order->payment_status == 'paid' ? 'bg-success' : 'bg-danger' }}">
                                                {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusClass =
                                                    [
                                                        'pending' => 'bg-secondary',
                                                        'confirmed' => 'bg-info',
                                                        'preparing' => 'bg-primary',
                                                        'shipping' => 'bg-warning text-dark',
                                                        'delivered' => 'bg-success',
                                                        'completed' => 'bg-success',
                                                        'cancelled' => 'bg-danger',
                                                        'returned' => 'bg-secondary',
                                                        'partially_returned' => 'bg-secondary',
                                                    ][$order->status] ?? 'bg-light';
                                                $statusText =
                                                    [
                                                        'pending' => 'Chờ xử lý',
                                                        'confirmed' => 'Đã xác nhận',
                                                        'preparing' => 'Đang chuẩn bị',
                                                        'shipping' => 'Đang giao hàng',
                                                        'delivered' => 'Đã giao',
                                                        'completed' => 'Đã hoàn thành',
                                                        'cancelled' => 'Đã hủy',
                                                        'returned' => 'Đã hoàn đơn',
                                                        'partially_returned' => 'Hoàn một phần',
                                                    ][$order->status] ?? ucfirst($order->status);
                                            @endphp
                                            <span
                                                class="badge rounded-pill {{ $statusClass }}">{{ $statusText }}</span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-3 me-1" title="Xem chi tiết">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                           
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                                <div>Không có đơn hàng nào phù hợp.</div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-center mt-3">
                            {{ $orders->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-shadow {
            box-shadow: 0 6px 20px rgba(0,0,0,0.10);
            background: #fff;
        }
        .table-modern th,
        .table-modern td {
            vertical-align: middle !important;
        }
        .table-modern,
        .table {
            border-radius: 0 !important;
        }
        .table-modern tbody tr:hover {
            background: #f8f9fa;
            transition: background 0.2s;
        }
        .badge {
            font-size: 0.95em;
            padding: 0.5em 1em;
        }
        .product-img-thumb {
            box-shadow: 0 2px 12px 0 rgba(0,0,0,0.12), 0 1.5px 4px 0 rgba(0,0,0,0.08);
            border: 2px solid #eee;
            transition: transform 0.2s, border-color 0.2s;
        }
        .product-img-thumb:hover {
            transform: scale(1.08);
            border-color: #007bff;
            box-shadow: 0 4px 18px 0 rgba(0,123,255,0.15), 0 3px 8px 0 rgba(0,0,0,0.10);
            z-index: 2;
        }
    </style>
@endsection
