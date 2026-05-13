@extends('client.layouts.app')
@section('title', 'Chi tiết hoàn đơn - Apple Store')

@section('content')
<div class="return-details-page">
    <div class="container py-5">
        <!-- Header Section -->
        <div class="header-section mb-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('order.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div>
                            <h1 class="mb-1">Đơn hoàn trả #{{ $return->id }}</h1>
                            <p class="text-secondary mb-0">
                                Đơn hàng: <a href="{{ route('order.tracking', $order->id) }}" class="text-primary fw-medium">#{{ $order->id }}</a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <span class="status-badge status-{{ $return->status }}">
                        {{ $return->status == 'pending' ? 'Chờ xử lý' : 
                           ($return->status == 'approved' ? 'Đã duyệt' : 
                           ($return->status == 'refunded' ? 'Đã hoàn tiền' : $return->status)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Cột trái: Timeline và Thông tin -->
            <div class="col-lg-8">
                <!-- Timeline -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="timeline">
                       zz     <div class="timeline-item {{ in_array($return->status, ['pending', 'approved', 'refunded']) ? 'completed' : '' }}">
                                <div class="timeline-point"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Yêu cầu hoàn trả</h6>
                                    <p class="text-secondary small mb-0">{{ $return->created_at->format('H:i, d/m/Y') }}</p>
                                </div>
                            </div>

                            <div class="timeline-item {{ in_array($return->status, ['approved', 'refunded']) ? 'completed' : '' }}">
                                <div class="timeline-point"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Xác nhận yêu cầu</h6>
                                    @if($return->approved_at)
                                        <p class="text-secondary small mb-0">{{ \Carbon\Carbon::parse($return->approved_at)->format('H:i, d/m/Y') }}</p>
                                    @else
                                        <p class="text-secondary small mb-0">Đang chờ xử lý</p>
                                    @endif
                                </div>
                            </div>

                            <div class="timeline-item {{ in_array($return->status, ['approved', 'refunded']) ? 'completed' : '' }}">
                                <div class="timeline-point"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1">Hoàn tiền</h6>
                                    @if($return->refunded_at)
                                        <p class="text-secondary small mb-0">{{ \Carbon\Carbon::parse($return->refunded_at)->format('H:i, d/m/Y') }}</p>
                                    @else
                                        <p class="text-secondary small mb-0">{{ $return->status == 'approved' ? 'Đã xác nhận hoàn tiền' : 'Chưa hoàn tiền' }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thông tin chi tiết -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Thông tin chi tiết</h5>
                        
                        <div class="info-grid">
                            <div class="info-item">
                                <label class="text-secondary mb-2">Lý do hoàn trả</label>
                                <p class="mb-0">{{ $return->reason }}</p>
                            </div>

                            @if($return->note)
                            <div class="info-item">
                                <label class="text-secondary mb-2">Ghi chú</label>
                                <p class="mb-0">{{ $return->note }}</p>
                            </div>
                            @endif

                            <div class="info-item">
                                <label class="text-secondary mb-2">Số tiền hoàn trả</label>
                                <p class="text-success fw-semibold mb-0">{{ number_format($return->refund_amount) }} VNĐ</p>
                            </div>

                            @if($return->refund_method)
                            <div class="info-item">
                                <label class="text-secondary mb-2">Phương thức hoàn tiền</label>
                                <p class="mb-0">{{ $return->refund_method }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Hình ảnh và Video -->
            <div class="col-lg-4">
                @if($return->status == 'approved' || $return->status == 'refunded')
                    @if($return->refund_proof_image)
                    <div class="card shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4">Chứng từ hoàn tiền</h5>
                            <div class="image-wrapper rounded overflow-hidden">
                                <img src="{{ asset($return->refund_proof_image) }}" alt="Chứng từ hoàn tiền" class="img-fluid rounded">
                            </div>
                            @if($return->refund_note)
                            <div class="refund-note mt-3 p-3 bg-light rounded">
                                <label class="fw-medium mb-2">Ghi chú từ admin</label>
                                <p class="text-secondary mb-0">{{ $return->refund_note }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                @endif

                @if($return->images)
                <div class="card shadow-sm"></div>
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4">Hình ảnh sản phẩm hoàn trả</h5>
                        <div class="row g-3">
                            @foreach(json_decode($return->images) as $image)
                            <div class="col-6">
                                <a href="{{ asset($image) }}" class="image-item" target="_blank">
                                    <img src="{{ asset($image) }}" alt="Hình ảnh sản phẩm" class="img-fluid rounded">
                                    <div class="image-overlay">
                                        <i class="fas fa-search-plus"></i>
                                    </div>
                                </a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.return-details-page {
    background-color: #f8fafc;
    min-height: 100vh;
    padding-top: 80px;
}

/* Header Styles */
.header-section h1 {
    font-size: 1.75rem;
    font-weight: 600;
    color: #1e293b;
}

.btn-back {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background-color: #f1f5f9;
    color: #475569;
    border: none;
    transition: all 0.2s ease;
    text-decoration: none;
}

.btn-back:hover {
    background-color: #e2e8f0;
    color: #1e293b;
}

.status-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
}

.status-pending {
    background-color: #fef3c7;
    color: #92400e;
}

.status-approved {
    background-color: #e0f2fe;
    color: #0369a1;
}

.status-refunded {
    background-color: #dcfce7;
    color: #166534;
}

/* Card Styles */
.card {
    border: none;
    border-radius: 12px;
    background: white;
}

.card-title {
    color: #1e293b;
    font-weight: 600;
    font-size: 1.1rem;
}

/* Timeline Styles */
.timeline {
    position: relative;
    padding: 1rem 0;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 1.5rem;
    padding: 1rem 0;
    position: relative;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 35px;
    bottom: 0;
    width: 2px;
    background-color: #e2e8f0;
}

.timeline-point {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e2e8f0;
    position: relative;
    z-index: 1;
}

.timeline-item.completed .timeline-point {
    background: #22c55e;
}

.timeline-content {
    flex: 1;
}

.timeline-content h6 {
    color: #1e293b;
    font-weight: 600;
}

/* Info Grid */
.info-grid {
    display: grid;
    gap: 2rem;
}

.info-item label {
    font-size: 0.875rem;
    display: block;
}

/* Image Styles */
.image-item {
    display: block;
    position: relative;
    border-radius: 8px;
    overflow: hidden;
}

.image-overlay {
    position: absolute;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.image-item:hover .image-overlay {
    opacity: 1;
}

.video-wrapper {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .header-section h1 {
        font-size: 1.5rem;
    }
    
    .status-badge {
        margin-top: 1rem;
    }
}
</style>
@endsection 