@extends('client.layouts.app')
@section('title', 'Chi tiết đơn hàng - Apple Store')
@section('content')
    <style>
        :root {
            --primary-color: #6b7280;
            --secondary-color: #f3f4f6;
            --accent-color: #4f46e5;
            --text-primary: #374151;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --success-color: #059669;
            --danger-color: #dc2626;
        }

        .order-page {
            max-width: 1200px;
            margin: 120px auto 40px;
            padding: 0 20px;
        }

        .order-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 24px;
        }

        .order-header {
            padding: 32px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--secondary-color);
        }

        .order-title {
            display: flex;
            align-items: center;
            gap: 16px;
            color: var(--text-primary);
        }

        .order-number {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .order-date {
            color: var(--text-secondary);
            font-size: 14px;
            margin-top: 4px;
        }

        .order-status {
            padding: 8px 16px;
            border-radius: 20px;
            background: #fff;
            color: var(--accent-color);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--accent-color);
        }

        .shipping-info {
            padding: 24px 32px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .info-group {
            display: flex;
            gap: 16px;
        }

        .info-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--secondary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-color);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 4px;
        }

        .info-value {
            color: var(--text-primary);
            font-weight: 500;
            font-size: 16px;
        }

        .info-value.highlight {
            color: var(--accent-color);
            font-weight: 600;
        }

        .order-products {
            padding: 24px 32px;
        }

        .product-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-table th {
            text-align: left;
            padding: 12px 16px;
            background: var(--secondary-color);
            color: var(--text-primary);
            font-weight: 500;
            font-size: 14px;
        }

        .product-table td {
            padding: 16px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .product-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .product-image {
            width: 100px;
            height: 100px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--border-color);
        }

        .product-details {
            flex: 1;
        }

        .product-details h3 {
            color: var(--text-primary);
            font-weight: 600;
            margin: 0 0 8px;
            font-size: 16px;
        }

        .product-variant {
            color: var(--text-secondary);
            font-size: 14px;
        }

        .quantity-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            background: var(--secondary-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-weight: 500;
        }

        .price {
            font-weight: 500;
            color: var(--text-primary);
        }

        .order-summary {
            padding: 24px 32px;
            border-top: 1px solid var(--border-color);
            background: var(--secondary-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .summary-row.total {
            color: var(--text-primary);
            font-weight: 600;
            font-size: 18px;
            border-top: 1px solid var(--border-color);
            margin-top: 8px;
            padding-top: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background: var(--accent-color);
            color: #fff;
        }

        .btn-secondary {
            background: var(--secondary-color);
            color: var(--text-primary);
        }

        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-top: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #ecfdf5;
            color: var(--success-color);
            border: 1px solid #a7f3d0;
        }

        .alert-danger {
            background: #fef2f2;
            color: var(--danger-color);
            border: 1px solid #fecaca;
        }

        @media (max-width: 768px) {
            .order-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .shipping-info {
                grid-template-columns: 1fr;
            }

            .product-table {
                display: block;
                overflow-x: auto;
            }

            .product-info {
                min-width: 300px;
            }
        }
    </style>

    <div class="order-page">
        <div class="order-card  ">
            <div class="order-header">
                <div class="order-title">
                    <div>
                        <div class="order-number">
                            <i class="fas fa-shopping-bag"></i>
                            Đơn hàng #{{ $order->id }}
                        </div>
                        <div class="order-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                </div>
                <div class="order-status">
                    <i class="fas {{ $statusIcons[$order->status] ?? 'fa-circle' }}"></i>
                    <span id="order-status-text">{{ $order->getStatusTextAttribute() }}</span>
                </div>
            </div>
            <div class="shipping-info">
                <div class="info-section-title" style="grid-column: 1 / -1; margin-bottom: 16px;">
                    <h3 style="margin: 0; color: var(--text-primary); font-size: 18px; font-weight: 600;">
                        <i class="fas fa-user me-2"></i>Thông tin người nhận
                    </h3>
                </div>
                <div class="info-group">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Người nhận</div>
                        <div class="info-value highlight">{{ $order->shipping_name }}</div>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Số điện thoại</div>
                        <div class="info-value highlight">{{ $order->shipping_phone }}</div>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Email</div>
                        <div class="info-value">{{ $order->shipping_email }}</div>
                    </div>
                </div>
                <div class="info-group">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="info-content">
                        <div class="info-label">Địa chỉ giao hàng</div>
                        <div class="info-value">{{ $order->shipping_address }}</div>
                    </div>
                </div>
            </div>

            <!-- Hiển thị thông tin người đặt nếu có đặt hàng hộ -->
            @if($order->orderAddress)
            <div class="order-card" style="margin-top: 24px;">
                <div class="order-header" style="background: #f8f9fa;">
                    <div class="order-title">
                        <div>
                            <div class="order-number" style="font-size: 18px;">
                                <i class="fas fa-user-friends"></i>
                                Thông tin người đặt hàng 
                            </div>
                            <div class="order-date" style="font-size: 12px;">
                                <i class="fas fa-info-circle"></i>
                                Đơn hàng được đặt hộ
                            </div>
                        </div>
                    </div>
                </div>
                <div class="shipping-info">
                    <div class="info-group">
                        <div class="info-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Người đặt</div>
                            <div class="info-value highlight">{{ $order->orderAddress->full_name }}</div>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Số điện thoại</div>
                            <div class="info-value highlight">{{ $order->orderAddress->phone_number }}</div>
                        </div>
                    </div>
                    <div class="info-group">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Địa chỉ người đặt</div>
                            <div class="info-value">{{ $order->orderAddress->address }}</div>
                        </div>
                    </div>
                    @if($order->orderAddress->note)
                    <div class="info-group">
                        <div class="info-icon">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <div class="info-content">
                            <div class="info-label">Ghi chú</div>
                            <div class="info-value">{{ $order->orderAddress->note }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="order-products">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Số lượng</th>
                            <th>Đơn giá</th>
                            <th>Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
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
                        @foreach ($order->items as $item)
                            <tr>
                                <td>
                                    <div class="product-info">
                                        @php
                                            $images = $item->variant && $item->variant->images ? getImagesArray($item->variant->images) : [];
                                            $imgSrc = isset($images[0]) ? asset($images[0]) : (isset($item->product->image) ? asset($item->product->image) : asset('uploads/default/default.jpg'));
                                        @endphp
                                        <img src="{{ $imgSrc }}" alt="{{ $item->product->name ?? '' }}" class="product-image">
                                        <div class="product-details">
                                            <h3>{{ $item->product->name ?? '' }}</h3>
                                            @if($item->variant && $item->variant->name)
                                                <div class="product-variant">{{ $item->variant->name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="quantity-badge">{{ $item->quantity }}</span>
                                </td>
                                <td class="price">{{ number_format($item->price) }} VNĐ</td>
                                <td class="price">{{ number_format($item->total) }} VNĐ</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="order-summary">
                <div class="summary-row">
                    <span>Tạm tính</span>
                    <span>{{ number_format($order->subtotal) }} VNĐ</span>
                </div>
                @if($order->discount > 0)
                    <div class="summary-row">
                        <span>Giảm giá</span>
                        <span>-{{ number_format($order->discount) }} VNĐ</span>
                    </div>
                @endif
                @if($order->shipping_fee > 0)
                    <div class="summary-row">
                        <span>Phí vận chuyển</span>
                        <span>{{ number_format($order->shipping_fee) }} VNĐ</span>
                    </div>
                @endif
                <div class="summary-row total">
                    <span>Tổng cộng</span>
                    <span>{{ number_format($order->total_price) }} VNĐ</span>
                </div>
            </div>
        </div>

        <div style="text-align: center;">
            <a href="{{ route('shop') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Tiếp tục mua sắm
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script>
        setTimeout(() => {
            let orderId = {{ $order->id }};
            window.Echo.channel('orderStatus.' + orderId)
                .listen('.OrderStatusUpdated', (e) => {
                    const statusText = document.getElementById('order-status-text');
                    if (statusText) {
                        statusText.textContent = e.status_text;
                    }

                    if (e.status === 'cancelled' || e.status === 'returned' || e.status === 'partially_returned') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                });
        }, 200);
    </script>
@endsection
