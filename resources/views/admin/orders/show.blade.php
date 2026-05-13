@extends('admin.layouts.app')

@section('title', 'Chi tiết đơn hàng - Apple Store')

@section('content')
{{-- <!-- Bootstrap 5 CSS via CDN -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> --}}

<style>
    .admin-order-details {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 1.5rem;
        margin-left: 250px; /* Điều chỉnh theo độ rộng của sidebar */
        width: calc(100% - 250px);
    }
    
    .card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }
    
    .card-header {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        font-weight: 600;
        color: #343a40;
        padding: 1rem 1.25rem;
    }

    .card-body {
        padding: 1.25rem;
    }
    
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
    
    .cancel-reason {
        background: #f8d7da;
        color: #dc3545;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        display: inline-block;
        font-weight: 500;
    }
    
    .item-image {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
    }
    
    .notes-card {
        border-left: 4px solid #0d6efd;
        background: #f1f3f5;
    }
    
    .invoice-btn {
        transition: all 0.3s ease;
    }
    
    .invoice-btn:hover {
        transform: translateY(-2px);
    }

    @media (max-width: 991px) {
        .admin-order-details {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<div class="admin-order-details">
    <div class="container">
        <!-- Header Section -->
        <div class="mb-4 mt-4">
          
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-light rounded-3 p-2 mb-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                            <i class="fas fa-home"></i> Trang chủ
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.orders.index') }}" class="text-decoration-none">Đơn hàng</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Chi tiết đơn hàng #{{ $order->id }}</li>
                </ol>
            </nav>
        </div>

        <!-- Alert Messages -->
        @if(session('success'))
            <div id="order-alert" class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div id="order-alert" class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <div class="row g-4">
            <!-- Customer Information -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>
                        Thông tin người nhận 
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th class="text-muted" style="width: 35%;">Họ tên:</th>
                                <td>{{ $order->shipping_name }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email:</th>
                                <td>{{ $order->shipping_email }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Số điện thoại:</th>
                                <td>{{ $order->shipping_phone }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Địa chỉ:</th>
                                <td>{{ $order->shipping_address }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Status -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list me-2"></i>
                        Trạng thái đơn hàng
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th class="text-muted" style="width: 35%;">Trạng thái hiện tại:</th>
                                <td>
                                    @php
                                        $statusText = [
                                            'pending' => 'Chờ xử lý',
                                            'confirmed' => 'Đã xác nhận',
                                            'preparing' => 'Đang chuẩn bị',
                                            'shipping' => 'Đang giao hàng',
                                            'delivered' => 'Đã giao',
                                            'completed' => 'Đã hoàn thành',
                                    
                                            'returned' => 'Đã hoàn đơn',
                                            'partially_returned' => 'Đã hoàn trả một phần',
                                            'cancelled' => 'Đã hủy',
                                        ][$order->status] ?? ucfirst($order->status);
                                        
                                        $statusClass = [
                                            'pending' => 'bg-secondary',
                                            'confirmed' => 'bg-info',
                                            'preparing' => 'bg-primary',
                                            'shipping' => 'bg-warning text-dark',
                                            'delivered' => 'bg-success',
                                            'completed' => 'bg-success',
                                            'refunded' => 'bg-secondary',
                                            'partially_returned' => 'bg-secondary',
                                            'cancelled' => 'bg-danger',
                                        ][$order->status] ?? 'bg-light';
                                    @endphp
                                    <span class="badge rounded-pill {{ $statusClass }} mb-2">{{ $statusText }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted" style="width: 35%;">Cập nhật trạng thái:</th>
                                <td>
                                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}" class="d-flex gap-2 align-items-center">
                                        @csrf
                                        @method('PUT')
                                        <select name="status" id="order-status" class="form-select" style="max-width: 140px; height: 45px;">

                                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                            <option value="preparing" {{ $order->status == 'preparing' ? 'selected' : '' }}>Đang chuẩn bị</option>
                                            <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                                            <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }} disabled>Đã hoàn thành (Chỉ client xác nhận)</option>
                                            <option value="refunded" {{ $order->status == 'refunded' ? 'selected' : '' }}>Đã hoàn đơn</option>
                                            <option value="partially_returned" {{ $order->status == 'partially_returned' ? 'selected' : '' }}>Đã hoàn trả một phần</option>
                                            <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Hủy đơn</option>
                                        </select>
                                        <div id="cancel-reason-container" class="d-none">
                                            <input type="text" name="cancel_reason" class="form-control" placeholder="Nhập lý do hủy đơn hàng" style="width: 250px;" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" style="height: 38px; padding: 0.375rem 0.75rem;">
                                            <i class="fas fa-sync-alt me-1"></i> Cập nhật
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Thanh toán:</th>
                                <td>
                                    <span class="status-badge {{ $order->payment_status == 'paid' ? 'bg-success text-white' : 'bg-warning text-white' }}">
                                        {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th class="text-muted">Phương thức:</th>
                                <td>{{ ucfirst($order->payment_method) }}</td>
                            </tr>
                            <tr>
                                <th class="text-muted">Ngày đặt:</th>
                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @if($order->cancel_reason)
                            <tr>
                                <th class="text-muted">Lý do hủy:</th>
                                <td>
                                    <span class="cancel-reason">{{ $order->cancel_reason }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>

            @if($order->orderAddress)
            <!-- Recipient Information -->
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-user-friends me-2"></i>
                        Thông tin khách hàng 
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <th class="text-muted" style="width: 35%;">Họ tên:</th>
                                        <td>{{ $order->orderAddress->full_name }}</td>
                                    </tr>
                                    <tr>
                                        <th class="text-muted">Số điện thoại:</th>
                                        <td>{{ $order->orderAddress->phone_number }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <table class="table table-borderless mb-0">
                                    <tr>
                                        <th class="text-muted" style="width: 35%;">Địa chỉ:</th>
                                        <td>{{ $order->orderAddress->address }}</td>
                                    </tr>
                                    @if($order->orderAddress->note)
                                    <tr>
                                        <th class="text-muted">Ghi chú:</th>
                                        <td>{{ $order->orderAddress->note }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Order Summary -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-calculator me-2"></i>
                        Tổng kết đơn hàng
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Tạm tính:</span>
                            <span>{{ number_format($order->subtotal) }} VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Phí vận chuyển:</span>
                            <span>{{ number_format($order->shipping_fee) }} VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span>Giảm giá:</span>
                            <span>-{{ number_format($order->discount) }} VNĐ</span>
                        </div>
                        <div class="d-flex justify-content-between py-3 mt-2">
                            <span class="fw-bold">Tổng cộng:</span>
                            <span class="fw-bold">{{ number_format($order->total_price) }} VNĐ</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header">
                        <i class="fas fa-shopping-bag me-2"></i>
                        Sản phẩm đã đặt
                    </div>
                    <div class="card-body">
                        @php
                            // Helper function to safely handle both JSON strings and arrays
                            function getImagesArray($images) {
                                if (is_array($images)) {
                                    return $images;
                                }
                                if (is_string($images)) {
                                    $decoded = json_decode($images, true);
                                    return is_array($decoded) ? $decoded : [];
                                }
                                return [];
                            }
                        @endphp
                        @foreach($order->items as $item)
                            @php
                                $images = $item->variant && $item->variant->images ? getImagesArray($item->variant->images) : [];
                                $imgSrc = isset($images[0]) ? asset($images[0]) : asset('uploads/default/default.jpg');
                            @endphp
                            <div class="d-flex align-items-start gap-3 pb-3 mb-3 border-bottom">
                                <img src="{{ $imgSrc }}" alt="{{ $item->variant->name ?? '' }}" class="item-image">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $item->product->name ?? 'N/A' }}</h6>
                                    @if($item->variant->name ?? '')
                                    <p class="text-muted small mb-2">{{ $item->variant->name }}</p>
                                    @endif
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">Số lượng: {{ $item->quantity }}</span>
                                        <span class="fw-bold">{{ number_format($item->total) }} VNĐ</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if($order->notes)
        <!-- Notes Section -->
        <div class="card notes-card h-100 mt-4">
            <div class="card-body">
                <h5 class="card-title d-flex align-items-center gap-2 mb-3">
                    <i class="fas fa-sticky-note"></i>
                    Ghi chú
                </h5>
                <p class="text-muted mb-0">{{ $order->notes }}</p>
            </div>
        </div>
        @endif

        <!-- Invoice Button -->
       
    </div>
</div>

<!-- Bootstrap 5 JS (for alert dismiss functionality) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.getElementById('order-alert');
        if (alert) {
            setTimeout(() => {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 500);
            }, 3000);
        }

        // Ngăn admin chọn trạng thái completed
        const statusSelect = document.querySelector('select[name="status"]');
        if (statusSelect) {
            statusSelect.addEventListener('change', function() {
                if (this.value === 'completed') {
                    alert('Admin không thể chuyển trạng thái sang "Đã hoàn thành". Chỉ client mới có thể xác nhận nhận hàng.');
                    this.value = '{{ $order->status }}'; // Khôi phục giá trị cũ
                }
            });
        }
    });
</script>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('order-status');
    const cancelReasonContainer = document.getElementById('cancel-reason-container');
    const cancelReasonInput = cancelReasonContainer.querySelector('input[name="cancel_reason"]');

    function toggleCancelReason() {
        if (statusSelect.value === 'cancelled') {
            cancelReasonContainer.classList.remove('d-none');
            cancelReasonInput.required = true;
        } else {
            cancelReasonContainer.classList.add('d-none');
            cancelReasonInput.required = false;
            cancelReasonInput.value = '';
        }
    }

    // Kiểm tra trạng thái ban đầu
    toggleCancelReason();

    // Lắng nghe sự kiện thay đổi trạng thái
    statusSelect.addEventListener('change', toggleCancelReason);
});
</script>
@endpush