@extends('client.layouts.app')
@section('title', 'Đơn hàng - Apple Store')

@section('content')
<div class="order-management">
    <div class="container px-4 py-5">
        <!-- Header Section with Gradient Background -->
        <div class="header-section mb-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="header-content">
                        <h1 class="display-6 fw-bold text-gray mb-2">Đơn hàng của tôi</h1>
                        <p class="text-gray-50 mb-0 fs-5">Theo dõi và quản lý tất cả đơn hàng của bạn một cách dễ dàng</p>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="header-stats">
                        <div class="stat-card bg-white bg-opacity-20 backdrop-blur">
                            <div class="stat-number text-gray fw-bold">{{ $orders->total() }}</div>
                            <div class="stat-label text-gray">Tổng đơn hàng</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Status Tabs -->
        <div class="status-tabs-container mb-4">
            <div class="status-tabs-wrapper">
                <nav class="status-tabs">
                    <a class="status-tab {{ request('status') === null ? 'active' : '' }}" 
                       href="{{ route('order.index') }}">
                        <i class="fas fa-list-ul"></i>
                        <span>Tất cả</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'pending' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'pending']) }}">
                        <i class="fas fa-clock text-warning"></i>
                        <span>Chờ xử lý</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'confirmed' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'confirmed']) }}">
                        <i class="fas fa-circle-check text-info"></i>
                        <span>Đã xác nhận</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'preparing' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'preparing']) }}">
                        <i class="fas fa-box text-primary"></i>
                        <span>Đang chuẩn bị</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'shipping' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'shipping']) }}">
                        <i class="fas fa-truck text-info"></i>
                        <span>Đang giao</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'delivered' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'delivered']) }}">
                        <i class="fas fa-circle-check text-success"></i>
                        <span>Đã giao</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'completed' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'completed']) }}">
                        <i class="fas fa-circle-check text-success"></i>
                        <span>Đã hoàn thành</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'returned' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'returned']) }}">
                        <i class="fas fa-undo text-secondary"></i>
                        <span>Đã hoàn đơn</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'partially_returned' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'partially_returned']) }}">
                        <i class="fas fa-undo text-secondary"></i>
                        <span>Hoàn một phần</span>
                        <div class="tab-indicator"></div>
                    </a>
                    <a class="status-tab {{ request('status') == 'cancelled' ? 'active' : '' }}" 
                       href="{{ route('order.index', ['status' => 'cancelled']) }}">
                        <i class="fas fa-ban text-danger"></i>
                        <span>Đã hủy</span>
                        <div class="tab-indicator"></div>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Alert Messages -->
        {{-- @if(session('success'))
            <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
                <div class="alert-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="alert-content">
                    <strong>Thành công!</strong>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="alert-content">
                    <strong>Lỗi!</strong>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif --}}


        @if(session('warning'))
            <div class="alert alert-warning alert-modern alert-dismissible fade show" role="alert">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="alert-content">
                    <strong>Lưu ý!</strong>
                    <div class="mb-0">{!! session('warning') !!}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif


        <!-- Modern Orders Table -->
        <div class="table-responsive">
            <table class="table table-modern">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Sản phẩm</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Phương thức</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr data-order-id="{{ $order->id }}" data-status="{{ $order->status }}" data-created-at="{{ $order->created_at->format('Y-m-d\\TH:i:sP') }}">
                            <td>
                                <span class="order-number">#{{ $order->order_code }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @php
                                        // hiển thị ảnh sản phẩm theo array hoặc mảng
                                        if (!function_exists('getImagesArray')) {
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
                                        }
                                    @endphp
                                    @php
                                        $firstItem = $order->items->first();
                                        $images = $firstItem && $firstItem->variant && $firstItem->variant->images ? getImagesArray($firstItem->variant->images) : [];
                                        $imgSrc = isset($images[0]) ? asset($images[0]) : (isset($firstItem->product->image) ? asset($firstItem->product->image) : asset('uploads/default/default.jpg'));
                                    @endphp
                                    <img src="{{ $imgSrc }}" alt="Product Image" class="product-image me-3">
                                    <div>
                                        <h6 class="mb-0">{{ $firstItem->product->name ?? 'Sản phẩm' }}</h6>
                                        @if($order->items->count() > 1)
                                            <small class="text-muted">+{{ $order->items->count() - 1 }} sản phẩm khác</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <span class="price-amount">{{ number_format($order->total_price) }} VNĐ</span>
                            </td>
                            <td>
                                @switch($order->payment_method)
                                    @case('cod')
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-money-bill-wave me-1"></i>COD
                                        </span>
                                        @if($order->status == 'cancelled')
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                </small>
                                            </div>
                                        @elseif($order->payment_status == 'paid')
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                                </small>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>Chưa thanh toán
                                                </small>
                                            </div>
                                        @endif
                                        @break
                                    @case('vnpay')
                                        <span class="badge" style="background: #00bcd4">
                                            <i class="fas fa-credit-card me-1"></i>VNPAY
                                        </span>
                                        @if($order->status == 'cancelled')
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                </small>
                                            </div>
                                        @elseif($order->payment_status == 'paid')
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                                </small>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>Chưa thanh toán
                                                </small>
                                            </div>
                                        @endif
                                        @break
                                    @default
                                        <span class="badge bg-secondary">{{ $order->payment_method }}</span>
                                        @if($order->status == 'cancelled')
                                            <div class="mt-1">
                                                <small class="text-danger">
                                                    <i class="fas fa-times-circle me-1"></i>Đã hủy
                                                </small>
                                            </div>
                                        @elseif($order->payment_status == 'paid')
                                            <div class="mt-1">
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                                </small>
                                            </div>
                                        @else
                                            <div class="mt-1">
                                                <small class="text-warning">
                                                    <i class="fas fa-clock me-1"></i>Chưa thanh toán
                                                </small>
                                            </div>
                                        @endif
                                @endswitch
                            </td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'preparing' => 'primary',
                                        'shipping' => 'info',
                                        'delivered' => 'delivered',
                                        'completed' => 'success',
                                        'cancelled' => 'danger',
                                        'returned' => 'secondary',
                                        '' => 'secondary',
                                    ][$order->status] ?? 'secondary';

                                    $statusIcons = [
                                        'pending' => 'fa-clock',
                                        'confirmed' => 'fa-circle-check',
                                        'preparing' => 'fa-box',
                                        'shipping' => 'fa-truck',
                                        'completed' => 'fa-circle-check',
                                        'cancelled' => 'fa-ban',
                                        'returned' => 'fa-undo',
                                        '' => 'fa-circle',
                                    ][$order->status] ?? 'fa-circle';
                                @endphp
                                <div class="status-container">
                                    <span class="status-badge status-{{ $statusClass }}">
                                        <i class="fas {{ $statusIcons }}"></i>
                                        {{ $order->getStatusTextAttribute() }}
                                    </span>
                                    @if($order->status === 'cancelled' && $order->cancel_reason)
                                        <div class="cancel-reason mt-2">
                                            <small class="text-danger">
                                                <i class="fas fa-info-circle me-1"></i>
                                                <strong>Lý do hủy:</strong> {{ $order->cancel_reason }}
                                            </small>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="order-actions" id="order-actions-{{ $order->id }}">
                                    @if(in_array($order->status, ['returned', 'partially_returned']))
                                        <button type="button" 
                                           onclick="window.location.href='{{ route('order.returns.show', [$order->id, $order->returns->first()->id]) }}'"
                                           class="btn btn-action btn-soft-info" 
                                           data-bs-toggle="tooltip" 
                                           title="Xem chi tiết hoàn đơn">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @else
                                        <button type="button"
                                           onclick="window.location.href='{{ route('order.tracking', $order->id) }}'"
                                           class="btn btn-action btn-soft-primary" 
                                           data-bs-toggle="tooltip" 
                                           title="Xem chi tiết đơn hàng">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    @endif

                                    @if(in_array($order->status, ['pending', 'confirmed']))
                                        <button type="button" 
                                                class="btn btn-action btn-soft-danger btn-cancel-order" 
                                                data-bs-toggle="tooltip" 
                                                title="Hủy đơn hàng" 
                                                data-order-id="{{ $order->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($order->status == 'delivered')
                                        <button type="button" 
                                                class="btn btn-action btn-soft-success btn-confirm-received" 
                                                data-bs-toggle="tooltip" 
                                                title="Xác nhận đã nhận hàng" 
                                                data-order-id="{{ $order->id }}">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif

                                    @if($order->status == 'delivered')
                                        @php
                                            $orderDate = \Carbon\Carbon::parse($order->created_at);
                                            $now = \Carbon\Carbon::now();
                                            $daysDiff = $now->diffInDays($orderDate);
                                        @endphp
                                        @if($daysDiff <= 7)
                                            <button type="button"
                                                onclick="window.location.href='{{ route('order.returns.create', $order->id) }}'"
                                                class="btn btn-action btn-soft-warning"
                                                data-bs-toggle="tooltip" 
                                                title="Yêu cầu hoàn hàng">
                                                <i class="fas fa-history"></i>
                                            </button>
                                        @endif
                                    @endif

                                    @if($order->status == 'completed')
                                        <button type="button"
                                            onclick="window.location.href='{{ route('order.review', $order->id) }}'"
                                            class="btn btn-action btn-soft-success"
                                            data-bs-toggle="tooltip" 
                                            title="Đánh giá">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-box-open text-secondary"></i>
                                    </div>
                                    <h3>
                                        @php
                                            $statusText = match(request('status')) {
                                                'pending' => 'Chờ xử lý',
                                                'confirmed' => 'Đã xác nhận',
                                                'preparing' => 'Đang chuẩn bị',
                                                'shipping' => 'Đang giao',
                                                'delivered' => 'Đã giao',
                                                'completed' => 'Đã hoàn thành',
                                                'cancelled' => 'Đã hủy',
                                                'returned' => 'Đã hoàn đơn',
                                                'partially_returned' => 'Hoàn một phần',
                                                default => 'Tất cả trạng thái'
                                            };
                                        @endphp
                                        Không có đơn hàng nào ở trạng thái: <span class="text-primary">{{ $statusText }}</span>
                                    </h3>
                                    <p>Bạn chưa có đơn hàng nào thuộc trạng thái này.</p>
                                    <a href="#" class="btn btn-primary btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i>
                                        Mua sắm ngay
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="pagination-container">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<style>
/* Reset button styles */
.btn {
    background: none !important;
    border: none !important;
    box-shadow: none !important;
    padding: 0 !important;
}

/* Main Container */
.order-management {
    background: white;
    min-height: 100vh;
    padding-top: 80px;
}

/* Header Section */
.header-section {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-card {
    border-radius: 15px;
    padding: 1.5rem;
    text-align: center;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.stat-number {
    font-size: 2rem;
    line-height: 1;
}

.stat-label {
    font-size: 0.9rem;
    margin-top: 0.5rem;
}

/* Status Tabs */
.status-tabs-container {
    background: white;
    border-radius: 15px;
    padding: 0.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.status-tabs-wrapper {
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.status-tabs-wrapper::-webkit-scrollbar {
    display: none;
}

.status-tabs {
    display: flex;
    gap: 0.5rem;
    min-width: max-content;
}

.status-tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    text-decoration: none;
    color: #6c757d;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    white-space: nowrap;
}

.status-tab:hover {
    background: #f8f9fa;
    color: #495057;
    transform: translateY(-2px);
}

.status-tab.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.tab-indicator {
    position: absolute;
    bottom: -0.5rem;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 3px;
    background: #667eea;
    border-radius: 2px;
    transition: width 0.3s ease;
}

.status-tab.active .tab-indicator {
    width: 80%;
}

/* Table Styling */
.table-modern {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.table-modern thead th {
    background: #f8f9fa;
    border-bottom: 2px solid #e9ecef;
    color: #495057;
    font-weight: 600;
    padding: 1rem;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.table-modern tbody tr {
    transition: all 0.3s ease;

}

.table-modern tbody tr:hover {
    background: #f8f9fa;
}

.table-modern td {
    padding: 1rem;
    vertical-align: middle;
    border-bottom: 1px solid #e9ecef;
}

/* Product Image */
.product-image {
    width: 80px;
    height: 80px;
    border-radius: 10px;
    object-fit: cover;
    border: 2px solid #f8f9fa;
    transition: all 0.3s ease;
}

.product-image:hover {
    transform: scale(1.05);
    border-color: #007bff;
    box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
}

/* Status Badge */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

/* Status Container */
.status-container {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
}

/* Cancel Reason */
.cancel-reason {
    background: #fff5f5;
    border: 1px solid #fed7d7;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    max-width: 300px;
    word-wrap: break-word;
}

.cancel-reason small {
    line-height: 1.4;
    display: block;
}

.cancel-reason i {
    color: #e53e3e;
}

.status-warning { background: #fff3cd; color: #856404; }
.status-info { background: #d1ecf1; color: #0c5460; }
.status-primary { background: #d1ecf1; color: #155724; }
.status-success { background: #d4edda; color: #155724; }
.status-delivered { background: #d4edda; color: #155724; }
.status-danger { background: #f8d7da; color: #721c24; }
.status-secondary { background: #e2e3e5; color: #383d41; }

/* Modern Admin Colors */
.btn-soft-primary {
    color: #2196F3 !important;
}

.btn-soft-primary:hover {
    color: #1976D2 !important;
}

.btn-soft-info {
    color: #00ACC1 !important;
}

.btn-soft-info:hover {
    color: #0097A7 !important;
}

.btn-soft-warning {
    color: #FFA000 !important;
}

.btn-soft-warning:hover {
    color: #FF8F00 !important;
}

.btn-soft-danger {
    color: #E53935 !important;
}

.btn-soft-danger:hover {
    color: #D32F2F !important;
}

.btn-soft-success {
    color: #43A047 !important;
}

.btn-soft-success:hover {
    color: #388E3C !important;
}

/* Order Actions Container */
.order-actions {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 4px;
    background: none !important;
}

.btn-action {
    width: 38px;
    height: 38px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    position: relative;
}

.btn-action i {
    font-size: 20px;
    line-height: 1;
}

.btn-action:hover {
    transform: translateY(-1px);
}

/* Star Icon Color */
.btn-action i.fa-star {
    color: #FFD700 !important;
}

.btn-action:hover i.fa-star {
    color: #FFC107 !important;
}

/* Eye Icon Specific Color */
.btn-action i.fa-eye {
    color: #2196F3 !important;
}

.btn-action:hover i.fa-eye {
    color: #1976D2 !important;
}

/* Tooltip Enhancement */
.tooltip {
    --bs-tooltip-bg: #2c3e50;
    --bs-tooltip-color: #ffffff;
    --bs-tooltip-opacity: 0.98;
}

.tooltip .tooltip-inner {
    border-radius: 4px;
    padding: 4px 8px;
    font-size: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Fix button alignment in table cell */
.table-modern td {
    vertical-align: middle;
}

.table-modern td .order-actions {
    margin: -2px 0;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1.5rem;
}

.empty-state h3 {
    color: #2c3e50;
    margin-bottom: 1rem;
}

.empty-state p {
    color: #6c757d;
    margin-bottom: 2rem;
    font-size: 1.1rem;
}

/* Pagination */
.pagination-container {
    display: flex;
    justify-content: center;
    margin-top: 2rem;
}

.pagination-container .pagination {
    background: white;
    border-radius: 15px;
    padding: 0.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Responsive Design */
@media (max-width: 576px) {
    .header-section {
        padding: 1.5rem;
    }
    
    .header-section .row {
        text-align: center;
    }
    
    .header-section .col-md-4 {
        margin-top: 1rem;
    }
    
    .status-tabs {
        gap: 0.25rem;
    }
    
    .status-tab {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }

}

/* Alert Styling */
.alert-modern {
    border: none;
    border-radius: 15px;
    padding: 1.25rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    animation: slideIn 0.5s ease-out;
}

.alert-modern .alert-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}

.alert-modern .alert-content {
    flex-grow: 1;
}

.alert-modern .alert-content strong {
    display: block;
    margin-bottom: 0.5rem;
    font-size: 1.1rem;
}

.alert-modern .btn-close {
    padding: 1rem;
    margin: -1rem -1rem -1rem 0;
    opacity: 0.5;
    transition: opacity 0.3s ease;
}

.alert-modern .btn-close:hover {
    opacity: 1;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
    color: #856404;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.alert-hide {
    animation: slideOut 0.5s ease-out forwards;
}

/* Icon Colors */
.btn-action i.fa-history {
    color: #9C27B0 !important;
}

.btn-action:hover i.fa-history {
    color: #7B1FA2 !important;
}
</style>

@section('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Định nghĩa các route URLs
    const ROUTE_URLS = {
        orderTracking: "{{ route('order.tracking', ':id') }}",
        orderReturnsShow: "{{ route('order.returns.show', [':orderId', ':returnId']) }}",
        orderReturnsCreate: "{{ route('order.returns.create', ':id') }}",
        orderReview: "{{ route('order.review', ':id') }}"
    };

    // Hàm helper để thay thế params trong URL
    function generateUrl(template, params) {
        let url = template;
        for (let key in params) {
            url = url.replace(':' + key, params[key]);
        }
        return url;
    }

document.addEventListener('DOMContentLoaded', function() {
    // Custom styling cho SweetAlert2
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    // Handle session flash messages
    @if(session('success'))
        Toast.fire({
            icon: 'success',
            title: 'Thành công',
            text: '{{ session('success') }}'
        });
    @endif

    @if(session('error'))
        Toast.fire({
            icon: 'error',
            title: 'Lỗi',
            text: '{{ session('error') }}'
        });
    @endif

    @if(session('warning'))
        Toast.fire({
            icon: 'warning',
            title: 'Cảnh báo',
            text: '{{ session('warning') }}'
        });
    @endif

    // Hàm xử lý sự kiện hủy đơn hàng
    function handleCancelButtonClick(event) {
        const orderId = event.currentTarget.dataset.orderId;

        // Hiển thị modal xác nhận hủy đơn
        Swal.fire({
            title: 'Hủy đơn hàng',
            text: 'Vui lòng cho chúng tôi biết lý do bạn muốn hủy đơn hàng này:',
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                maxlength: 255,
                placeholder: 'Nhập lý do hủy đơn hàng'
            },
            showCancelButton: true,
            confirmButtonText: 'Xác nhận hủy',
            cancelButtonText: 'Đóng',
            showLoaderOnConfirm: true,
            width: '32em',
            preConfirm: (reason) => {
                if (!reason) {
                    Swal.showValidationMessage('Vui lòng nhập lý do hủy đơn hàng');
                    return false;
                }

                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                formData.append('cancellation_reason', reason);
                
                return fetch(`/order/cancel/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(async response => {
                    const data = await response.json();
                    
                    if (!response.ok) {
                        if (response.status === 419) {
                            throw new Error('Phiên làm việc đã hết hạn, vui lòng tải lại trang');
                        }
                        throw new Error(data.message || 'Có lỗi xảy ra khi hủy đơn hàng');
                    }
                    
                    return data;
                })
                .catch(error => {
                    if (error.message.includes('Phiên làm việc đã hết hạn')) {
                        Toast.fire({
                            icon: 'warning',
                            title: 'Phiên làm việc đã hết hạn',
                            text: 'Trang sẽ tự động tải lại...'
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        return false;
                    }
                    Swal.showValidationMessage(error.message);
                    return false;
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.success) {
                Toast.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: result.value.message || 'Đã hủy đơn hàng thành công'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }

    // Xử lý sự kiện cho nút hủy đơn
    document.querySelectorAll('.btn-cancel-order').forEach(button => {
        button.addEventListener('click', handleCancelButtonClick);
    });

    // Xử lý sự kiện cho nút xác nhận đã nhận hàng
    function handleConfirmReceivedButtonClick(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const orderId = button.getAttribute('data-order-id');

        Swal.fire({
            title: 'Xác nhận đã nhận hàng?',
            text: 'Bạn có chắc chắn đã nhận được hàng và muốn xác nhận?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy',
            customClass: {
                popup: 'small-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Hiển thị loading
                Swal.fire({
                    title: 'Đang xử lý...',
                    text: 'Vui lòng chờ trong giây lát',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Gửi request
                const formData = new FormData();
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                fetch(`/order/confirm-received/${orderId}`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(async response => {
                    const data = await response.json();
                    
                    if (!response.ok) {
                        if (response.status === 419) {
                            throw new Error('Phiên làm việc đã hết hạn, vui lòng tải lại trang');
                        }
                        throw new Error(data.message || 'Có lỗi xảy ra khi xác nhận nhận hàng');
                    }
                    
                    return data;
                })
                .then(data => {
                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Thành công!',
                            text: data.message || 'Đã xác nhận nhận hàng thành công'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: data.message || 'Có lỗi xảy ra khi xác nhận nhận hàng'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.message.includes('Phiên làm việc đã hết hạn')) {
                        Toast.fire({
                            icon: 'warning',
                            title: 'Phiên làm việc đã hết hạn',
                            text: 'Trang sẽ tự động tải lại...'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: error.message || 'Có lỗi xảy ra khi xác nhận nhận hàng'
                        });
                    }
                });
            }
        });
    }

    // Gắn event listener cho nút xác nhận đã nhận hàng
    document.querySelectorAll('.btn-confirm-received').forEach(button => {
        button.addEventListener('click', handleConfirmReceivedButtonClick);
    });

    // Lắng nghe sự kiện cập nhật trạng thái realtime
    let orderIds = @json($orders->pluck('id'));
    
    if (window.Echo) {
        orderIds.forEach(orderId => {
            const channel = window.Echo.channel(`orderStatus.${orderId}`);

            channel.listen('.OrderStatusUpdated', (event) => {
                try {
                    console.log('Nhận được sự kiện OrderStatusUpdated:', event);
                    
                    const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
                    if (!row) {
                        throw new Error(`Không tìm thấy row cho đơn hàng #${orderId}`);
                    }

                    if (!event || !event.status) {
                        throw new Error('Dữ liệu event không hợp lệ');
                    }

                    row.setAttribute('data-status', event.status);

                    const statusBadge = row.querySelector('.status-badge');
                    if (!statusBadge) {
                        throw new Error('Không tìm thấy status badge');
                    }

                    const statusClass = {
                        'pending': 'warning',
                        'confirmed': 'info',
                        'preparing': 'primary',
                        'shipping': 'info',
                        'delivered': 'delivered',
                        'completed': 'success',
                        'cancelled': 'danger',
                        'returned': 'secondary',
                        'partially_returned': 'secondary'
                    }[event.status] || 'secondary';

                    const statusIcon = {
                        'pending': 'fa-clock',
                        'confirmed': 'fa-circle-check',
                        'preparing': 'fa-box',
                        'shipping': 'fa-truck',
                        'delivered': 'fa-check-circle',
                        'completed': 'fa-circle-check',
                        'cancelled': 'fa-ban',
                        'returned': 'fa-undo',
                        'partially_returned': 'fa-undo'
                    }[event.status] || 'fa-circle';

                    // Cập nhật status container
                    const statusContainer = row.querySelector('.status-container');
                    if (statusContainer) {
                        let statusHtml = `<span class="status-badge status-${statusClass}">
                            <i class="fas ${statusIcon}"></i> ${event.status_text}
                        </span>`;
                        
                        // Thêm lý do hủy nếu có
                        if (event.status === 'cancelled' && event.cancel_reason) {
                            statusHtml += `
                                <div class="cancel-reason mt-2">
                                    <small class="text-danger">
                                        <i class="fas fa-info-circle me-1"></i>
                                        <strong>Lý do hủy:</strong> ${event.cancel_reason}
                                    </small>
                                </div>`;
                        }
                        
                        statusContainer.innerHTML = statusHtml;
                    }

                    // Cập nhật payment status nếu có
                    if (event.payment_status) {
                        const paymentMethodCell = row.querySelector('td:nth-child(5)'); // Cột phương thức thanh toán
                        if (paymentMethodCell) {
                            const paymentStatusDiv = paymentMethodCell.querySelector('.mt-1');
                            if (paymentStatusDiv) {
                                if (event.payment_status === 'paid') {
                                    paymentStatusDiv.innerHTML = `
                                        <small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Đã thanh toán
                                        </small>`;
                                } else {
                                    paymentStatusDiv.innerHTML = `
                                        <small class="text-warning">
                                            <i class="fas fa-clock me-1"></i>Chưa thanh toán
                                        </small>`;
                                }
                            }
                        }
                    }

                    const actionsContainer = document.getElementById(`order-actions-${orderId}`);
                    if (!actionsContainer) {
                        throw new Error('Không tìm thấy actions container');
                    }

                    let actionsHtml = '';

                    if (['returned', 'partially_returned'].includes(event.status)) {
                        if (event.return_id) {
                            actionsHtml += `
                                <button type="button" 
                                   onclick="window.location.href='{{ route('order.returns.show', [':order', ':return']) }}'.replace(':order', ${orderId}).replace(':return', ${event.return_id})"
                                   class="btn btn-action btn-soft-info" 
                                   data-bs-toggle="tooltip" 
                                   title="Xem chi tiết hoàn đơn">
                                    <i class="fas fa-eye"></i>
                                </button>`;
                        }
                    } else {
                        actionsHtml += `
                            <button type="button"
                                onclick="window.location.href='{{ route('order.tracking', ':id') }}'.replace(':id', ${orderId})"
                                class="btn btn-action btn-soft-primary" 
                                data-bs-toggle="tooltip" 
                                title="Xem chi tiết đơn hàng">
                                <i class="fas fa-eye"></i>
                            </button>`;
                    }

                    // Thêm button cho trạng thái delivered
                    if (event.status === 'delivered') {
                        console.log('Trạng thái đã giao được cập nhật cho đơn hàng:', orderId);
                        
                        actionsHtml += `
                            <button type="button" 
                                    class="btn btn-action btn-soft-success btn-confirm-received" 
                                    data-bs-toggle="tooltip" 
                                    title="Xác nhận đã nhận hàng" 
                                    data-order-id="${orderId}">
                                <i class="fas fa-check"></i>
                            </button>`;
                        
                        // Thêm nút hoàn hàng nếu trong vòng 7 ngày
                        const orderDate = new Date(row.getAttribute('data-created-at'));
                        const now = new Date();
                        const daysDiff = Math.floor((now - orderDate) / (1000 * 60 * 60 * 24));
                        
                        console.log('Ngày đặt hàng:', orderDate);
                        console.log('Ngày hiện tại:', now);
                        console.log('Số ngày chênh lệch:', daysDiff);
                        
                        if (daysDiff <= 7) {
                            console.log('Thêm nút hoàn hàng cho đơn hàng:', orderId);
                            actionsHtml += `
                                <button type="button"
                                    onclick="window.location.href='{{ route('order.returns.create', ':id') }}'.replace(':id', ${orderId})"
                                    class="btn btn-action btn-soft-warning"
                                    data-bs-toggle="tooltip" 
                                    title="Yêu cầu hoàn hàng">
                                    <i class="fas fa-history"></i>
                                </button>`;
                        } else {
                            console.log('Đơn hàng quá 7 ngày, không hiển thị nút hoàn hàng');
                        }
                    }

                    if (['pending', 'confirmed'].includes(event.status)) {
                        actionsHtml += `
                            <button type="button" 
                                class="btn btn-action btn-soft-danger btn-cancel-order" 
                                data-bs-toggle="tooltip" 
                                title="Hủy đơn hàng" 
                                data-order-id="${orderId}">
                                <i class="fas fa-times"></i>
                            </button>`;
                    }

                    if (event.status === 'completed') {
                        actionsHtml += `
                            <button type="button"
                                onclick="window.location.href='{{ route('order.review', ':id') }}'.replace(':id', ${orderId})"
                                class="btn btn-action btn-soft-success"
                                data-bs-toggle="tooltip" 
                                title="Đánh giá">
                                <i class="fas fa-star"></i>
                            </button>`;
                    }

                    actionsContainer.innerHTML = actionsHtml;

                    // Khởi tạo lại tooltips
                    const tooltips = [].slice.call(actionsContainer.querySelectorAll('[data-bs-toggle="tooltip"]'));
                    tooltips.map(function (tooltipTriggerEl) {
                        return new bootstrap.Tooltip(tooltipTriggerEl);
                    });

                    // Gắn lại event listener cho nút hủy đơn
                    const newCancelButton = actionsContainer.querySelector('.btn-cancel-order');
                    if (newCancelButton) {
                        newCancelButton.addEventListener('click', handleCancelButtonClick);
                    }

                    // Gắn lại event listener cho nút xác nhận đã nhận hàng
                    const newConfirmReceivedButton = actionsContainer.querySelector('.btn-confirm-received');
                    if (newConfirmReceivedButton) {
                        newConfirmReceivedButton.addEventListener('click', handleConfirmReceivedButtonClick);
                    }

                } catch (error) {
                    console.error('Lỗi khi cập nhật trạng thái đơn hàng:', error);
                }
            });
        });
    }
});
// hàm giúp tải lại trang khi người dùng quay lại từ nút back khi đang realtime
window.addEventListener('pageshow', function(event) {
    if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
        window.location.reload(); // Tải lại trang nếu quay lại từ nút back
    }
});


</script>
<style>
/* Custom styles cho SweetAlert2 */
.swal2-popup.small-popup {
    width: 360px !important;
    font-size: 0.8rem !important;
}

.swal2-popup.small-popup .swal2-title {
    font-size: 1.2rem !important;
}

.swal2-popup.small-popup .swal2-content {
    font-size: 0.9rem !important;
}

.swal2-popup.small-popup .swal2-input {
    height: 2.5em !important;
    font-size: 0.9rem !important;
}

.swal2-popup.small-popup .swal2-actions button {
    font-size: 0.8rem !important;
    padding: 0.5em 1.5em !important;
}

.swal2-toast {
    max-width: 300px !important;
    font-size: 0.875rem !important;
}

.swal2-toast .swal2-title {
    font-size: 1rem !important;
    margin: 0.5em 1em !important;
}

.swal2-toast .swal2-content {
    font-size: 0.875rem !important;
}

.swal2-toast .swal2-icon {
    width: 2em !important;
    height: 2em !important;
    margin: 0.5em !important;
}

.swal2-toast .swal2-icon .swal2-icon-content {
    font-size: 1.5em !important;
}

.swal2-toast .swal2-success-ring {
    width: 2em !important;
    height: 2em !important;
}
</style>
@endsection