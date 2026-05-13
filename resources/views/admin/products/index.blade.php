<!-- resources/views/admin/products/index.blade.php -->
@extends('admin.layouts.app')
@section('title', 'Quản lý sản phẩm')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
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
                                <h5 class="m-b-10">Sản phẩm</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Sản phẩm</li>
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
                            <h5>Danh sách sản phẩm</h5>
                            <div class="card-header-right">
                                <a href="{{ route('admin.products.create') }}"
                                    class="btn btn-primary btn-sm rounded-3 me-2">
                                    <i class="ti ti-plus"></i> Thêm sản phẩm
                                </a>
                                <a href="{{ route('admin.products.trash') }}" class="btn btn-danger btn-sm rounded-3">
                                    <i class="ti ti-trash"></i> Thùng rác
                                    @if ($trashedCount > 0)
                                        <span class="badge bg-light text-danger ms-1">{{ $trashedCount }}</span>
                                    @endif
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="card shadow-sm mb-4">
                                <div class="card-body">
                                    <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3 mb-3">
                                        <div class="col-md-3">
                                            <input type="text" name="name" class="form-control"
                                                placeholder="Tìm theo tên..." value="{{ request('name') }}">
                                        </div>

                                        <div class="col-md-3">
                                            <select name="category_id" class="form-select">
                                                <option value="">-- Chọn danh mục --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="status" class="form-select">
                                                <option value="">-- Chọn trạng thái --</option>
                                                <option value="active"
                                                    {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Đặt
                                                lại</a>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="table custom-shadow">
                                <table class="table table-hover table-borderless align-middle" style="font-size: 14px;">
                                    <style>
                                        .table th,
                                        .table td {
                                            padding: 0.35rem 0.5rem;
                                        }

                                        .table .text-nowrap {
                                            white-space: nowrap;
                                        }

                                        .product-img-thumb {
                                            box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.12), 0 1.5px 4px 0 rgba(0, 0, 0, 0.08);
                                            border: 2px solid #eee;
                                            transition: transform 0.2s, border-color 0.2s;
                                        }

                                        .product-img-thumb:hover {
                                            transform: scale(1.08);
                                            border-color: #007bff;
                                            box-shadow: 0 4px 18px 0 rgba(0, 123, 255, 0.15), 0 3px 8px 0 rgba(0, 0, 0, 0.10);
                                            z-index: 2;
                                        }
                                    </style>
                                    <thead>
                                        <tr>
                                            <th class="text-nowrap">STT</th>
                                            <th>Hình ảnh</th>
                                            <th class="text-nowrap">Tên sản phẩm</th>
                                            <th class="text-nowrap">Giá bán ra</th> <!-- Đổi từ Nổi bật sang Giá bán ra -->
                                            <th class="text-nowrap">Danh mục</th>
                                            <th class="text-nowrap">Kho</th>
                                            <th class="text-nowrap">Trạng thái</th>
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
                                                            alt="{{ $product->name }}" class="product-img-thumb"
                                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                                    @else
                                                        <img src="{{ asset('uploads/default/default.jpg') }}"
                                                            alt="Ảnh mặc định" class="product-img-thumb"
                                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                                                    @endif
                                                </td>
                                                <td class="text-nowrap"
                                                    style="max-width: 140px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"
                                                    title="{{ $product->name }}">
                                                    {{ $product->name }}
                                                </td>
                                                <td class="text-nowrap">
                                                    @php
                                                        $defaultVariant = $product->variants
                                                            ->where('is_default', 1)
                                                            ->first();
                                                        $price = $defaultVariant
                                                            ? $defaultVariant->selling_price
                                                            : $product->variants->first()->selling_price ?? null;
                                                    @endphp
                                                    {{ $price !== null ? number_format($price, 0, ',', '.') . ' đ' : 'N/A' }}
                                                </td>
                                                <td class="text-nowrap">
                                                    {{ $product->category ? $product->category->name : 'N/A' }}</td>
                                                <td class="text-nowrap">{{ $product->variants->sum('stock') }}</td>
                                                <td class="text-nowrap">
                                                    <span
                                                        class="badge {{ $product->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $product->status === 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                                    </span>
                                                </td>
                                                <td class="text-center text-nowrap">
                                                    <a href="{{ route('admin.products.show', $product) }}"
                                                        class="btn btn-info btn-sm rounded-3 me-2" title="Xem chi tiết">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.products.edit', $product) }}"
                                                        class="btn btn-warning btn-sm rounded-3 me-2" title="Chỉnh sửa">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.products.destroy', $product) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm rounded-3"
                                                            title="Xóa">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center">Không tìm thấy sản phẩm nào.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
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
