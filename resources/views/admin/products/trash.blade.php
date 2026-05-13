@extends('admin.layouts.app')
@section('title', 'Thùng rác sản phẩm')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }
    .table th, .table td { padding: 0.35rem 0.5rem; }
    .table .text-nowrap { white-space: nowrap; }
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
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Thùng rác sản phẩm</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thùng rác</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Danh sách sản phẩm đã xóa</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary btn-sm rounded-3">
                                <i class="ti ti-arrow-left"></i> Quay lại danh sách
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="table custom-shadow">
                            <table class="table table-hover table-borderless align-middle" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">STT</th>
                                        <th>Hình ảnh</th>
                                        <th class="text-nowrap">Tên sản phẩm</th>
                                        <th class="text-nowrap">Danh mục</th>
                                        <th class="text-nowrap">Giá</th>
                                        <th class="text-nowrap">Kho</th>
                                        <th class="text-nowrap">Trạng thái</th>
                                        <th class="text-nowrap">Nổi bật</th>
                                        <th class="text-center text-nowrap">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $index => $product)
                                        <tr>
                                            <td class="text-nowrap">{{ $products->firstItem() + $index }}</td>
                                            <td>
                                                @if ($product->default_variant_image || $product->variant_image)
                                                    <img src="{{ asset($product->default_variant_image ?? $product->variant_image) }}"
                                                         alt="{{ $product->name }}"
                                                         class="product-img-thumb"
                                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                                @else
                                                    <img src="{{ asset('uploads/default/default.jpg') }}"
                                                         alt="Ảnh mặc định"
                                                         class="product-img-thumb"
                                                         style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                                @endif
                                            </td>
                                            <td class="text-nowrap" style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $product->name }}">
                                                {{ $product->name }}
                                            </td>
                                            <td class="text-nowrap">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                                            <td class="text-nowrap">
                                                @php
                                                    $variant = $product->variants->first();
                                                    $price = $variant ? $variant->selling_price : 0;
                                                    $discountPrice = $variant ? $variant->discount_price : 0;
                                                @endphp
                                                @if ($discountPrice > 0 && $discountPrice < $price)
                                                    <span class="text-decoration-line-through text-muted">{{ number_format($price, 0, ',', '.') }} VNĐ</span>
                                                    <br>
                                                    <span class="text-danger fw-bold">{{ number_format($discountPrice, 0, ',', '.') }} VNĐ</span>
                                                @else
                                                    <span class="text-success fw-bold">{{ number_format($price, 0, ',', '.') }} VNĐ</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">{{ $product->variants->sum('stock') }}</td>
                                            <td class="text-nowrap">
                                                <span class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $product->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                                </span>
                                            </td>
                                            <td class="text-nowrap">
                                                @if ($product->is_featured)
                                                    <span class="badge bg-info">Nổi bật</span>
                                                @else
                                                    <span class="badge bg-secondary">Bình thường</span>
                                                @endif
                                            </td>
                                            <td class="text-center text-nowrap">
                                                <form action="{{ route('admin.products.restore', $product->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục sản phẩm này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm rounded-3" title="Khôi phục">
                                                        <i class="ti ti-arrow-back-up"></i> Khôi phục
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">Không có sản phẩm nào trong thùng rác.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            {{ $products->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection