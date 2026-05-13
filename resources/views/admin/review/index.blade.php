{{-- filepath: c:\laragon\www\DATN\resources\views\admin\review\index.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Danh sách đánh giá sản phẩm')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Đánh giá sản phẩm</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Đánh giá sản phẩm</li>
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
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Danh sách đánh giá biến thể sản phẩm</h5>
                    </div>
                    <div class="card-body">
                        <!-- Bộ lọc tìm kiếm -->
                        <div class="card custom-shadow mb-4">
                            <div class="card-body">
                                <form action="{{ route('admin.reviews.index') }}" method="GET" class="row g-3">
                                    <div class="col-md-4">
                                        <label for="product_id" class="form-label mb-1">Sản phẩm</label>
                                        <select name="product_id" id="product_id" class="form-select">
                                            <option value="">-- tất cả --</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="rating" class="form-label mb-1">Số sao</label>
                                        <select name="rating" id="rating" class="form-select">
                                            <option value="">-- tất cả --</option>
                                            @for ($i = 5; $i >= 1; $i--)
                                                <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                                    {{ $i }} sao
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Lọc</button>
                                        <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">Đặt lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Table danh sách -->
                        <div class="table-responsive custom-shadow">
                            <table class="table table-hover table-borderless align-middle">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Sản phẩm</th>
                                        <th>Biến thể</th>
                                        <th>Hình ảnh biến thể</th>
                                        <th>Số sao</th>
                                        <th>Ngày đánh giá</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($reviews as $index => $review)
                                        <tr>
                                            <td>{{ $reviews->firstItem() + $index }}</td>
                                            <td>{{ $review->product->name ?? '-' }}</td>
                                            <td>{{ $review->variant->name ?? '-' }}</td>
                                            <td>
                                                @php
                                                    $variantImages = [];
                                                    if ($review->variant && $review->variant->images) {
                                                        $variantImages = is_array($review->variant->images)
                                                            ? $review->variant->images
                                                            : (json_decode($review->variant->images, true) ?: []);
                                                    }
                                                    $variantImage = !empty($variantImages[0])
                                                        ? asset($variantImages[0])
                                                        : asset('uploads/default/no-image.jpg');
                                                @endphp
                                                <img src="{{ $variantImage }}" alt="Ảnh biến thể" style="width:60px;height:60px;object-fit:cover;">
                                            </td>
                                            <td>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : '-o text-muted' }}"></i>
                                                @endfor
                                                ({{ $review->rating }})
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Không có đánh giá nào phù hợp.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $reviews->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection