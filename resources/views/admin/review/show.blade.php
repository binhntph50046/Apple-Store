{{-- filepath: c:\laragon\www\DATN\resources\views\admin\review\show.blade.php --}}
@extends('admin.layouts.app')
@section('title', 'Chi tiết đánh giá sản phẩm')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Đánh giá sản phẩm</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Chi tiết đánh giá sản phẩm</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h5>Chi tiết đánh giá sản phẩm</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Sản phẩm</th>
                        <td>{{ $review->product->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Biến thể</th>
                        <td>{{ $review->variant->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Hình ảnh biến thể</th>
                        <td>
                            @php
                                $variantImages = [];
                                if ($review->variant && $review->variant->images) {
                                    $variantImages = is_array($review->variant->images)
                                        ? $review->variant->images
                                        : (json_decode($review->variant->images, true) ?: []);
                                }
                            @endphp
                            @if(!empty($variantImages))
                                @foreach($variantImages as $img)
                                    <img src="{{ asset($img) }}" alt="Ảnh biến thể" style="width:80px;height:80px;object-fit:cover;margin-right:5px;">
                                @endforeach
                            @else
                                <img src="{{ asset('uploads/default/no-image.jpg') }}" alt="No image" style="width:80px;height:80px;object-fit:cover;">
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tên người đánh giá</th>
                        <td>{{ $review->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Email người đánh giá</th>
                        <td>{{ $review->user->email ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Số sao</th>
                        <td>
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $review->rating ? ' text-warning' : '-o text-muted' }}"></i>
                            @endfor
                            ({{ $review->rating }})
                        </td>
                    </tr>
                    <tr>
                        <th>Nội dung đánh giá</th>
                        <td>{{ $review->review }}</td>
                    </tr>
                    <tr>
                        <th>Ngày đánh giá</th>
                        <td>{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Hình ảnh đánh giá</th>
                        <td>
                            @php
                                $reviewImages = [];
                                if (!empty($review->images)) {
                                    $reviewImages = is_array($review->images)
                                        ? $review->images
                                        : (json_decode($review->images, true) ?: []);
                                }
                            @endphp
                            @if(!empty($reviewImages))
                                @foreach($reviewImages as $img)
                                    <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Ảnh đánh giá" style="width:80px;height:80px;object-fit:cover;margin-right:5px;">
                                    </a>
                                @endforeach
                            @else
                                <span class="text-muted">Không có</span>
                            @endif
                        </td>
                    </tr>
                </table>
                <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary mt-3">Quay lại danh sách</a>
            </div>
        </div>
    </div>
</div>
@endsection