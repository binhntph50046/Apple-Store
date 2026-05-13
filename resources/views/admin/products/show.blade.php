@extends('admin.layouts.app')
@section('title', 'Xem chi tiết sản phẩm')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 20px;
        transition: all 0.3s ease;
    }

    .custom-shadow:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }

    .product-image {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        object-fit: cover;
    }

    .default-image {
        width: 100%;
        height: 300px;
        object-fit: cover;
        border-radius: 8px;
        background-color: #f8f9fa;
    }

    .spec-item,
    .feature-item {
        background: #f8f9fa;
        padding: 10px 14px;
        border-radius: 6px;
        margin-bottom: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .spec-item strong {
        color: #495057;
        min-width: 120px;
        font-weight: 600;
    }

    .spec-item .hex-color {
        display: inline-flex;
        align-items: center;
    }

    .spec-item .hex-color span.color-box {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1px solid #ddd;
        margin-left: 8px;
    }

    .feature-item i {
        color: #0d6efd;
        margin-right: 10px;
    }

    .info-section {
        border-bottom: 1px solid #eee;
        padding-bottom: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-section:last-child {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .product-info-card {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 2rem;
    }

    .product-basic-info {
        border-right: 1px solid #eee;
        padding-right: 24px;
    }

    .product-specs-features {
        padding-left: 24px;
    }

    .variant-table img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
    }

    .badge-featured {
        background-color: #17a2b8;
        color: white;
    }

    .content-section img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
    }

    .btn-action {
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .price-section {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin: 10px 0;
    }

    .price-original {
        text-decoration: line-through;
        color: #6c757d;
    }

    .price-discount {
        color: #dc3545;
        font-weight: bold;
        font-size: 1.2em;
    }

    .stock-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
    }

    .stock-in {
        background-color: #d4edda;
        color: #155724;
    }

    .stock-out {
        background-color: #f8d7da;
        color: #721c24;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: 500;
    }

    .status-active {
        background-color: #d4edda;
        color: #155724;
    }

    .status-inactive {
        background-color: #f8d7da;
        color: #721c24;
    }

    .specification-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .specification-title {
        color: #2c3e50;
        font-size: 1.2em;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    .specification-item {
        display: flex;
        justify-content: space-between;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .specification-item:last-child {
        border-bottom: none;
    }

    .specification-label {
        font-weight: 500;
        color: #495057;
    }

    .specification-value {
        color: #6c757d;
    }

    .variant-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .variant-title {
        color: #2c3e50;
        font-size: 1.2em;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    .variant-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .variant-table th {
        background: #f8f9fa;
        padding: 12px;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
    }

    .variant-table td {
        padding: 12px;
        border-bottom: 1px solid #dee2e6;
        vertical-align: middle;
    }

    .variant-table tr:hover {
        background-color: #f8f9fa;
    }

    .variant-attribute {
        display: inline-block;
        margin: 2px;
        padding: 4px 8px;
        background: #e9ecef;
        border-radius: 4px;
        font-size: 0.9em;
    }

    .variant-attribute-name {
        color: #6c757d;
        font-size: 0.8em;
    }

    .variant-attribute-value {
        color: #495057;
        font-weight: 500;
    }

    .content-section {
        position: relative;
    }

    .content-wrapper {
        max-height: 200px;
        overflow: hidden;
        transition: max-height 0.3s ease;
        position: relative;
    }

    .content-wrapper.expanded {
        max-height: none;
    }

    .content-fade {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 60px;
        background: linear-gradient(transparent, #fff);
        pointer-events: none;
        transition: opacity 0.3s ease;
    }

    .content-wrapper.expanded .content-fade {
        opacity: 0;
    }

    .toggle-content-btn {
        background: #0d6efd;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 10px;
    }

    .toggle-content-btn:hover {
        background: #0b5ed7;
        transform: translateY(-1px);
    }

    .toggle-content-btn i {
        margin-left: 5px;
        transition: transform 0.3s ease;
    }

    .toggle-content-btn.expanded i {
        transform: rotate(180deg);
    }

    /* F. Reviews Section */
    .reviews-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .rating-summary {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .average-rating {
        text-align: center;
    }

    .average-rating .rating-number {
        font-size: 2.5em;
        font-weight: bold;
        color: #ffc107;
    }

    .rating-stars {
        color: #ffc107;
        font-size: 1.2em;
    }

    .rating-bars {
        flex: 1;
    }

    .rating-bar {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .rating-bar .star-label {
        min-width: 60px;
        font-size: 0.9em;
    }

    .rating-bar .progress {
        flex: 1;
        height: 8px;
    }

    .rating-bar .count {
        min-width: 30px;
        text-align: right;
        font-size: 0.9em;
        color: #6c757d;
    }

    .review-item {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .reviewer-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .reviewer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #6c757d;
    }

    .review-date {
        color: #6c757d;
        font-size: 0.9em;
    }

    /* G. Charts Section */
    .charts-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .chart-container {
        height: 300px;
        margin-bottom: 20px;
    }

    .chart-title {
        color: #2c3e50;
        font-size: 1.2em;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    /* H. Activity Log Section */
    .activity-section {
        background: #fff;
        border-radius: 8px;
        padding: 20px;
        margin-top: 20px;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 15px 0;
        border-bottom: 1px solid #f1f3f4;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2em;
        color: white;
        flex-shrink: 0;
    }

    .activity-icon.created {
        background: #28a745;
    }

    .activity-icon.updated {
        background: #17a2b8;
    }

    .activity-icon.variant_updated {
        background: #ffc107;
    }

    .activity-content {
        flex: 1;
    }

    .activity-action {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .activity-description {
        color: #6c757d;
        font-size: 0.9em;
        margin-bottom: 5px;
    }

    .activity-timestamp {
        color: #adb5bd;
        font-size: 0.8em;
    }
</style>

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Chi tiết sản phẩm</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                                <li class="breadcrumb-item" aria-current="page">{{ $product->name }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Thông tin sản phẩm</h5>
                            <div>
                                <a href="{{ route('admin.products.edit', $product) }}"
                                    class="btn btn-info btn-sm btn-action rounded-3 me-2">
                                    <i class="ti ti-edit"></i> Sửa sản phẩm
                                </a>
                                <a href="{{ route('admin.products.index') }}"
                                    class="btn btn-secondary btn-sm btn-action rounded-3">
                                    <i class="ti ti-arrow-left"></i> Quay lại
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center justify-content-center mb-4">
                                <!-- Left: Product Image -->
                                <div
                                    class="col-md-6 text-center d-flex flex-column align-items-center justify-content-center">
                                    <div class="custom-shadow" style="background: none; box-shadow: none;">
                                        @if ($product->default_variant_image || $product->variant_image)
                                            <img src="{{ asset($product->default_variant_image ?? $product->variant_image) }}"
                                                alt="{{ $product->name }}" class="product-img-thumb"
                                                style="max-width:300px;max-height:300px;object-fit:cover">
                                        @else
                                            <img src="{{ asset('uploads/default/default.jpg') }}" alt="Ảnh mặc định"
                                                class="product-img-thumb"
                                                style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                        @endif
                                    </div>
                                </div>
                                <!-- Right: Product Info + Specifications -->
                                <div class="col-md-6">
                                    <div class="custom-shadow p-4 h-100 d-flex flex-column justify-content-center">
                                        <h2 class="fw-bold mb-3">
                                            {{ is_array($product->name) ? implode(', ', $product->name) : $product->name }}
                                        </h2>
                                        <div class="mb-2"><strong>Danh mục:</strong>
                                            {{ is_array($product->category && $product->category->name) ? implode(', ', $product->category->name) : ($product->category ? $product->category->name : 'N/A') }}
                                        </div>
                                        <div class="mb-2"><strong>Bảo hành:</strong>
                                            {{ $product->warranty_months ?? 'N/A' }} tháng</div>
                                        <div class="mb-2"><strong>Trạng thái:</strong>
                                            <span
                                                class="status-badge {{ $product->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                                {{ $product->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                            </span>
                                            @if ($product->is_featured)
                                                <span class="badge badge-featured ms-2">Nổi bật</span>
                                            @endif
                                        </div>
                                        <!-- Specifications -->
                                        @if ($product->specifications && $product->specifications->isNotEmpty())
                                            <div class="mt-4">
                                                <h5 class="mb-3">Thông số kỹ thuật</h5>
                                                @foreach ($product->specifications as $spec)
                                                    @if($spec->specification)
                                                        <div class="spec-item mb-2">
                                                            <strong>{{ is_array($spec->specification->name) ? implode(', ', $spec->specification->name) : $spec->specification->name }}:</strong>
                                                            <span>{{ is_array($spec->value) ? implode(', ', $spec->value) : $spec->value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Features -->
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    @if ($product->features)
                                        <div class="info-section">
                                            <h5 class="mb-3">Tính năng</h5>
                                            @if (is_array($product->features))
                                                @foreach ($product->features as $feature)
                                                    <div class="feature-item">
                                                        <i class="ti ti-check"></i>
                                                        {{ is_array($feature) ? implode(', ', $feature) : $feature }}
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="feature-item">
                                                    <i class="ti ti-check"></i>
                                                    {{ $product->features }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Description -->
                            @if ($product->description)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="custom-shadow">
                                            <h5 class="mb-3">Mô tả</h5>
                                            <p class="text-muted">{!! is_array($product->description) ? implode(', ', $product->description) : nl2br(e($product->description)) !!}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Content -->
                            @if ($product->content)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="custom-shadow content-section">
                                            <h5 class="mb-3">Nội dung chi tiết</h5>
                                            <div class="content-wrapper" id="contentWrapper">
                                                <div class="content">{!! is_array($product->content) ? implode(', ', $product->content) : $product->content !!}</div>
                                                <div class="content-fade" id="contentFade"></div>
                                            </div>
                                            <button class="toggle-content-btn" id="toggleContentBtn" onclick="toggleContent()">
                                                <span id="toggleText">Xem thêm</span>
                                                <i class="ti ti-chevron-down" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Product Variants -->
                            @if ($product->variants && $product->variants->isNotEmpty())
                                <div class="variant-section mt-4">
                                    <h5 class="variant-title">Biến thể sản phẩm</h5>
                                    <div class="table-responsive">
                                        <table class="table variant-table align-middle text-center"
                                            style="font-size: 0.95em;">
                                            <thead>
                                                <tr>
                                                    <th>STT</th>
                                                    <th style="width:90px;">Ảnh biến thể</th>
                                                    <th>Tên biến thể</th>
                                                    <th>Thuộc tính</th>
                                                    <th>Giá nhập</th>
                                                    <th>Giá bán</th>
                                                    <th>Tồn kho</th>
                                                    <th>Trạng thái</th>
                                                    <th>Mặc định</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($product->variants as $variant)
                                                    @if ($variant->deleted_at === null)
                                                        <tr class="{{ $variant->is_default ? 'table-success' : '' }}">
                                                            <td>{{ $variant->id }}</td>
                                                            <!-- Variant Image -->
                                                            <td>
                                                                @if ($variant->images)
                                                                    @php
                                                                        $images = null;
                                                                        if (is_string($variant->images)) {
                                                                            $images = json_decode($variant->images, true);
                                                                        } elseif (is_array($variant->images)) {
                                                                            $images = $variant->images;
                                                                        }
                                                                        $firstImage = null;
                                                                        if (is_array($images) && !empty($images)) {
                                                                            $firstImage = $images[0];
                                                                        }
                                                                    @endphp
                                                                    @if ($firstImage)
                                                                        <img src="{{ asset($firstImage) }}" alt="Ảnh biến thể">
                                                                    @else
                                                                        <img src="{{ asset('uploads/default/default.jpg') }}"
                                                                            alt="Ảnh mặc định">
                                                                    @endif
                                                                @else
                                                                    <img src="{{ asset('uploads/default/default.jpg') }}"
                                                                        alt="Ảnh mặc định">
                                                                @endif
                                                            </td>
                                                            <td style="max-width:140px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                                                {{ is_array($variant->name) ? implode(', ', $variant->name) : $variant->name }}
                                                            </td>
                                                            <td>
                                                                <div style="display: flex; flex-direction: column; gap: 8px; align-items: flex-start;">
                                                                    @foreach ($variant->combinations as $combination)
                                                                        <div class="variant-attribute mb-1" style="font-size:0.95em; display: flex; align-items: center; gap: 8px; background: #f4f6f8; padding: 8px 14px; border-radius: 8px;">
                                                                            <span class="variant-attribute-name" style="color: #6c757d; min-width: 60px;">{{ is_array($combination->attributeValue->attributeType->name) ? implode(', ', $combination->attributeValue->attributeType->name) : $combination->attributeValue->attributeType->name }}:</span>
                                                                            <span class="variant-attribute-value" style="color: #222; font-weight: 500;">
                                                                                @if (is_array($combination->attributeValue->value))
                                                                                    {{ implode(', ', $combination->attributeValue->value) }}
                                                                                @else
                                                                                    {{ $combination->attributeValue->value }}
                                                                                @endif
                                                                            </span>
                                                                            @if ($combination->attributeValue->hex)
                                                                                <span class="color-box ms-2" style="display:inline-block;width:20px;height:20px;border-radius:50%;border:1px solid #ccc;box-shadow:0 1px 4px rgba(0,0,0,0.10);background:{{ is_array($combination->attributeValue->hex) ? implode(', ', $combination->attributeValue->hex) : $combination->attributeValue->hex }};"></span>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td>{{ number_format($variant->purchase_price, 0, ',', '.') }} VNĐ
                                                            </td>
                                                            <td><span
                                                                    class="text-success fw-bold">{{ number_format($variant->selling_price, 0, ',', '.') }}
                                                                    VNĐ</span></td>
                                                            <td>
                                                                <span
                                                                    class="stock-badge {{ $variant->stock > 0 ? 'stock-in' : 'stock-out' }}">
                                                                    {{ $variant->stock }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="status-badge {{ $variant->status === 'active' ? 'status-active' : 'status-inactive' }}">
                                                                    {{ ucfirst(is_array($variant->status) ? implode(', ', $variant->status) : $variant->status) }}
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <span
                                                                    class="badge bg-{{ $variant->is_default ? 'success' : 'secondary' }}">
                                                                    {{ $variant->is_default ? 'Mặc định' : 'Không' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- F. Đánh giá & phản hồi khách hàng -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-header">
                            <h5 class="mb-0">Đánh giá & phản hồi khách hàng</h5>
                        </div>
                        <div class="card-body">
                            <!-- Rating Summary -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="text-center">
                                        <div class="display-4 text-warning fw-bold">{{ number_format($averageRating, 1) }}</div>
                                        <div class="mb-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star{{ $i <= $averageRating ? ' text-warning' : '-o text-muted' }}"></i>
                                            @endfor
                                        </div>
                                        <div class="text-muted">{{ $reviews->count() }} đánh giá</div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    @for($i = 5; $i >= 1; $i--)
                                        @php
                                            $percentage = $reviews->count() > 0 ? ($ratingStats[$i] / $reviews->count()) * 100 : 0;
                                        @endphp
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="me-2" style="min-width: 30px;">{{ $i }} ⭐</span>
                                            <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                <div class="progress-bar bg-warning" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-muted" style="min-width: 30px;">{{ $ratingStats[$i] }}</span>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <!-- Top 3 Reviews -->
                            @if($topReviews->count() > 0)
                                <h6 class="mb-3">Top 3 đánh giá gần nhất</h6>
                                @foreach($topReviews as $review)
                                    <div class="border rounded p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                    {{ strtoupper(substr($review->user->name ?? 'A', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $review->user->name ?? 'Khách hàng' }}</div>
                                                    <div>
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : '-o text-muted' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-muted small">
                                                @if($review->created_at)
                                                    {{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}
                                                @else
                                                    N/A
                                                @endif
                                            </div>
                                        </div>
                                        @if($review->review)
                                            <div class="text-muted">
                                                {{ $review->review }}
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>Chưa có đánh giá nào cho sản phẩm này</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- G. Biểu đồ trực quan -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-header">
                            <h5 class="mb-0">Biểu đồ trực quan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Biểu đồ doanh số và số lượng đã bán theo tháng -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Doanh số & Số lượng đã bán theo tháng ({{ now()->year }})</h6>
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="salesChart"></canvas>
                                        @if(empty($monthlySales))
                                            <div class="text-center text-muted py-4">
                                                <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                                <p>Chưa có dữ liệu doanh số</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Biểu đồ lượt xem theo thời gian -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">Lượt xem 7 ngày gần nhất</h6>
                                    <div class="chart-container" style="height: 300px;">
                                        <canvas id="viewsChart"></canvas>
                                        @if($dailyViews->isEmpty())
                                            <div class="text-center text-muted py-4">
                                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                                <p>Chưa có dữ liệu lượt xem</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Chi tiết doanh số theo tháng -->
                            @if($monthlySalesDetail->count() > 0)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">Chi tiết doanh số theo tháng</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Tháng</th>
                                                        <th>Biến thể</th>
                                                        <th>Giá đơn vị</th>
                                                        <th>Số lượng</th>
                                                        <th>Doanh số</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($monthlySalesDetail as $detail)
                                                        <tr>
                                                            <td><strong>T{{ $detail->month }}</strong></td>
                                                            <td>{{ $detail->variant_name ?? 'N/A' }}</td>
                                                            <td>{{ number_format($detail->unit_price, 0, ',', '.') }} VNĐ</td>
                                                            <td>{{ $detail->total_quantity }}</td>
                                                            <td><strong>{{ number_format($detail->total_sales, 0, ',', '.') }} VNĐ</strong></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="alert alert-info mt-3">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Lưu ý:</strong> Doanh số khác nhau dù cùng số lượng có thể do:
                                            <ul class="mb-0 mt-2">
                                                <li>Biến thể sản phẩm khác nhau (ví dụ: 128GB vs 256GB)</li>
                                                <li>Giá sản phẩm thay đổi theo thời gian</li>
                                                <li>Khuyến mãi, flash sale, hoặc discount</li>
                                                <li>Phương thức thanh toán khác nhau</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- H. Nhật ký hoạt động -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-header">
                            <h5 class="mb-0">Nhật ký hoạt động</h5>
                        </div>
                        <div class="card-body">
                            @if($activityLogs->count() > 0)
                                @foreach($activityLogs as $log)
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-{{ $log['type'] === 'created' ? 'success' : ($log['type'] === 'updated' ? 'info' : 'warning') }} text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                            @if($log['type'] === 'created')
                                                <i class="fas fa-plus"></i>
                                            @elseif($log['type'] === 'updated')
                                                <i class="fas fa-edit"></i>
                                            @elseif($log['type'] === 'variant_updated')
                                                <i class="fas fa-cog"></i>
                                            @else
                                                <i class="fas fa-info"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold">{{ $log['action'] }}</div>
                                            {{-- <div class="text-muted small">{{ $log['description'] }}</div> --}}
                                            <div class="text-muted small">
                                                <i class="fas fa-user"></i> {{ $log['user'] ?? 'System' }} | 
                                                <i class="fas fa-clock"></i> {{ $log['timestamp'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <p>Chưa có hoạt động nào được ghi nhận</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>

    <script>
        function toggleContent() {
            const wrapper = document.getElementById('contentWrapper');
            const btn = document.getElementById('toggleContentBtn');
            const text = document.getElementById('toggleText');
            const icon = document.getElementById('toggleIcon');
            const fade = document.getElementById('contentFade');
            
            if (wrapper.classList.contains('expanded')) {
                // Thu gọn
                wrapper.classList.remove('expanded');
                btn.classList.remove('expanded');
                text.textContent = 'Xem thêm';
                icon.className = 'ti ti-chevron-down';
                fade.style.opacity = '1';
            } else {
                // Mở rộng
                wrapper.classList.add('expanded');
                btn.classList.add('expanded');
                text.textContent = 'Thu gọn';
                icon.className = 'ti ti-chevron-up';
                fade.style.opacity = '0';
            }
        }

        // Kiểm tra xem có cần hiển thị nút toggle không
        document.addEventListener('DOMContentLoaded', function() {
            const wrapper = document.getElementById('contentWrapper');
            const btn = document.getElementById('toggleContentBtn');
            
            if (wrapper && btn) {
                const contentHeight = wrapper.scrollHeight;
                const maxHeight = 200; // Chiều cao tối đa khi thu gọn
                
                if (contentHeight <= maxHeight) {
                    btn.style.display = 'none';
                }
            }

            // Initialize Charts
            initializeCharts();
        });

        function initializeCharts() {
            // Check if Chart.js is loaded
            if (typeof Chart === 'undefined') {
                console.error('Chart.js is not loaded');
                return;
            }

            // Sales Chart Data
            const salesData = @json($monthlySales);
            const viewsData = @json($dailyViews);
            
            // Hide canvas if no data
            const salesCanvas = document.getElementById('salesChart');
            const viewsCanvas = document.getElementById('viewsChart');
            
            if (salesCanvas && (!salesData || Object.keys(salesData).length === 0)) {
                salesCanvas.style.display = 'none';
            }
            
            if (viewsCanvas && (!viewsData || viewsData.length === 0)) {
                viewsCanvas.style.display = 'none';
            }
            
            const salesLabels = [];
            const salesValues = [];
            const quantityValues = [];
            
            for (let i = 1; i <= 12; i++) {
                const monthNames = ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'];
                salesLabels.push(monthNames[i-1]);
                
                if (salesData[i]) {
                    salesValues.push(parseFloat(salesData[i].total_sales) || 0);
                    quantityValues.push(parseInt(salesData[i].total_quantity) || 0);
                } else {
                    salesValues.push(0);
                    quantityValues.push(0);
                }
            }

            // Views Chart Data
            const viewsLabels = [];
            const viewsValues = [];
            
            // Generate last 7 days
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                const dateStr = date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                viewsLabels.push(dateStr);
                
                const found = viewsData.find(item => item.date === date.toISOString().split('T')[0]);
                viewsValues.push(found ? parseInt(found.views) : 0);
            }

            // Sales Chart with dual axes
            const salesCtx = document.getElementById('salesChart');
            if (salesCtx && salesData && Object.keys(salesData).length > 0) {
                new Chart(salesCtx, {
                    type: 'bar',
                    data: {
                        labels: salesLabels,
                        datasets: [
                            {
                                label: 'Doanh số (VNĐ)',
                                data: salesValues,
                                backgroundColor: 'rgba(54, 162, 235, 0.8)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1,
                                yAxisID: 'y'
                            },
                            {
                                label: 'Số lượng đã bán',
                                data: quantityValues,
                                backgroundColor: 'rgba(255, 99, 132, 0.8)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1,
                                yAxisID: 'y1',
                                type: 'line'
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        scales: {
                            y: {
                                type: 'linear',
                                display: true,
                                position: 'left',
                                title: {
                                    display: true,
                                    text: 'Doanh số (VNĐ)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return new Intl.NumberFormat('vi-VN').format(value) + ' VNĐ';
                                    }
                                }
                            },
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                title: {
                                    display: true,
                                    text: 'Số lượng'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.dataset.label === 'Doanh số (VNĐ)') {
                                            return context.dataset.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VNĐ';
                                        } else {
                                            return context.dataset.label + ': ' + context.parsed.y + ' sản phẩm';
                                        }
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Views Chart
            const viewsCtx = document.getElementById('viewsChart');
            if (viewsCtx && viewsData && viewsData.length > 0) {
                new Chart(viewsCtx, {
                    type: 'line',
                    data: {
                        labels: viewsLabels,
                        datasets: [{
                            label: 'Lượt xem',
                            data: viewsValues,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        }
    </script>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
@endsection
