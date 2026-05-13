@extends('client.layouts.app')

@section('title', 'So Sánh Sản Phẩm - Apple Store')

@section('content')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <div class="compare-section py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-dark mb-3">So Sánh Sản Phẩm</h2>
                <p class="text-muted">Chọn lựa thông minh cho quyết định của bạn</p>
            </div>

            @if ($comparison['products']->count() >= 2 && $comparison['products']->count() <= 4)
                <div class="compare-card bg-white rounded-4 shadow-sm overflow-hidden" data-aos="fade-up">
                    <div class="table-responsive">
                        <table class="table table-borderless compare-table mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th class="text-start" style="width: 200px; min-width: 180px; vertical-align: middle;">
                                        <span class="d-block py-3 px-4 fs-5 fw-semibold text-dark">Sản phẩm </span>
                                    </th>
                                    @foreach ($comparison['products'] as $product)
                                        <th class="text-center align-top position-relative border-start product-column">
                                            <div class="d-flex flex-column align-items-center justify-content-start p-4 h-100">
                                                @php
                                                    // Logic lấy ảnh giống trang chủ và shop
                                                    $images = json_decode($product->images, true) ?? [];
                                                    if (empty($images) && $product->variants->isNotEmpty()) {
                                                        $variantWithImage = $product->variants->firstWhere('images', '!=', null);
                                                        if ($variantWithImage) {
                                                            $images = json_decode($variantWithImage->images, true) ?? [];
                                                        }
                                                    }
                                                    $mainImage = $images[0] ?? 'uploads/default/default.jpg';
                                                    if (!empty($mainImage) && !str_starts_with($mainImage, 'uploads/')) {
                                                        $mainImage = 'uploads/products/' . $mainImage;
                                                    }
                                                @endphp
                                                    <img src="{{ asset($mainImage) }}" alt="{{ $product->name }}" 
                                                    class="img-fluid mb-3 product-image"
                                                    style="height: 120px; width: 120px; object-fit: contain;">
                                                <h5 class="mb-2 text-dark fw-bold product-name">{{ $product->name }}</h5>
                                                @if ($product->variants->isNotEmpty())
                                                    @php $variant = $product->variants->first(); @endphp
                                                    <div class="price mb-3">
                                                        @if ($variant->discount_price)
                                                            <span class="text-decoration-line-through text-muted small d-block">
                                                                {{ number_format($variant->selling_price) }}đ
                                                            </span>
                                                            <span class="text-danger fw-bold fs-5">
                                                                {{ number_format($variant->discount_price) }}đ
                                                            </span>
                                                        @else
                                                            <span class="fw-bold fs-5 text-dark">
                                                                {{ number_format($variant->selling_price) }}đ
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif
                                                <a href="{{ route('product.detail', $product->slug) }}"
                                                    class="btn btn-sm btn-primary mt-auto">
                                                    <i class="fas fa-shopping-cart me-1"></i> Xem chi tiết
                                                </a>
                                            </div>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($comparison['specs'] as $specName => $values)
                                    <tr class="border-bottom">
                                        <td class="fw-medium text-start ps-4" style="vertical-align: middle;">
                                            <span class="d-block py-3">{{ $specName }}</span>
                                        </td>
                                        @foreach ($comparison['products'] as $product)
                                            <td class="text-center p-3 position-relative border-start"
                                                style="vertical-align: middle;">
                                                <span
                                                    class="fs-6 @if ($values[$product->id] === max(array_values($values))) text-success fw-bold @endif">
                                                {{ $values[$product->id] ?? 'N/A' }}
                                                </span>
                                                @if ($values[$product->id] === max(array_values($values)))
                                                    <span class="position-absolute top-50 end-0 translate-middle-y me-3">
                                                        <i class="fas fa-check-circle text-success"></i>
                                                    </span>
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="ai-advice mt-4 bg-white rounded-4 shadow-sm overflow-hidden" data-aos="fade-up"
                    data-aos-delay="100">
                    <div class="d-flex align-items-center p-4 border-bottom">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-robot text-primary fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-1">Gợi ý từ hệ thống AI</h4>
                            <p class="small text-muted mb-0">Phân tích thông minh dựa trên thông số kỹ thuật</p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="ai-content bg-light bg-opacity-25 rounded-3 p-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-quote-left text-primary opacity-25 me-3 mt-1"></i>
                                <div>{{ $aiAdvice }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="empty-state text-center py-5 bg-white rounded-4 shadow-sm">
                    <div class="icon-lg text-primary mb-4">
                        <i class="fas fa-balance-scale fa-3x opacity-25"></i>
                    </div>
                    <h4 class="mb-3">Chưa đủ sản phẩm để so sánh</h4>
                    <p class="text-muted mb-4">Bạn cần chọn từ 2 đến 4 sản phẩm để sử dụng tính năng này</p>
                    <a href="{{ route('home') }}" class="btn btn-primary px-4">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại trang chủ
                    </a>
                </div>
            @endif

            <div class="text-center mt-5">
                <a href="{{ route('shop') }}" class="btn btn-outline-primary px-4 me-3">
                    <i class="fas fa-redo me-2"></i>So sánh sản phẩm khác
                </a>
                <a href="{{ route('home') }}" class="btn btn-light px-4">
                    <i class="fas fa-home me-2"></i>Về trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: true
        });
    </script>

    <style>
        .compare-section {
            min-height: calc(100vh - 100px);
        }

        .compare-card {
            border: 1px solid #dee2e6;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .compare-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }
        
        .compare-table {
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .compare-table th {
            border-bottom: 2px solid #f0f0f0;
        }

        .compare-table tbody tr:last-child td {
            border-bottom: none;
        }

        .compare-table td,
        .compare-table th {
            border-right: 1px solid #f0f0f0;
            border-bottom: 1px solid #f0f0f0;
        }

        .compare-table td:last-child,
        .compare-table th:last-child {
            border-right: none;
        }

        .product-column {
            width: calc((100% - 200px) / {{ $comparison['products']->count() }});
        }
        
        .product-image {
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .product-name {
            font-size: 0.9rem;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .ai-advice {
            border-left: 4px solid #0d6efd;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .ai-advice:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .empty-state {
            max-width: 500px;
            margin: 0 auto;
            transition: transform 0.3s ease;
        }

        .empty-state:hover {
            transform: translateY(-5px);
        }

        .icon-lg {
            font-size: 2.5rem;
        }

        @media (max-width: 991px) {
            .product-image {
                height: 100px !important;
                width: 100px !important;
            }

            .product-name {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 768px) {
            .compare-table th,
            .compare-table td {
                padding: 8px 6px;
                font-size: 0.8rem;
            }
            
            .product-image {
                height: 80px !important;
                width: 80px !important;
            }

            .product-name {
                font-size: 0.75rem;
                min-height: 32px;
            }

            .btn-sm {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .compare-table th:first-child {
                position: sticky;
                left: 0;
                background: white;
                z-index: 1;
                box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            }

            .product-image {
                height: 60px !important;
                width: 60px !important;
            }
        }
    </style>
@endsection
