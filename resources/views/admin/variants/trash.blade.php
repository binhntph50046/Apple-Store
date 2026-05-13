@extends('admin.layouts.app')
@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.variants.index') }}">Biến thể sản phẩm</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Thùng rác</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Thùng rác - Biến thể sản phẩm</h5>
                        <a href="{{ route('admin.variants.index') }}" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Quay lại</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-borderless align-middle" style="font-size: 14px;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 60px">STT</th>
                                        <th class="text-center" style="width: 100px">Ảnh</th>
                                        <th style="min-width: 220px; padding-left: 16px">Tên biến thể / Sản phẩm</th>
                                        <th class="text-center" style="min-width: 120px">Thuộc tính</th>
                                        <th class="text-center" style="min-width: 110px">Giá nhập</th>
                                        <th class="text-center" style="min-width: 110px">Giá bán</th>
                                        <th class="text-center" style="width: 90px">Tồn kho</th>
                                        <th class="text-center" style="width: 110px">Trạng thái</th>
                                        <th class="text-center" style="width: 120px">Khôi phục</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($variants as $index => $variant)
                                        <tr>
                                            <td class="text-center align-middle">{{ $variants->firstItem() + $index }}</td>
                                            <td class="text-center align-middle">
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
                                                        <img src="{{ asset($firstImage) }}" alt="Ảnh biến thể"
                                                            style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                                    @else
                                                        <img src="{{ asset('uploads/default/default.jpg') }}"
                                                            alt="Default Image"
                                                            style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                                    @endif
                                                @else
                                                    <img src="{{ asset('uploads/default/default.jpg') }}"
                                                        alt="Default Image"
                                                        style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                                @endif
                                            </td>
                                            <td class="align-middle" style="padding-left: 16px">
                                                <div style="font-weight: 500">{{ $variant->name }}</div>
                                                <div class="text-muted" style="font-size:13px">{{ $variant->product ? $variant->product->name : 'N/A' }}</div>
                                            </td>
                                            <td class="text-center align-middle">
                                                @foreach($variant->attributeValues as $attr)
                                                    <span class="badge bg-light text-dark me-1">{{ is_array($attr->value) ? implode(' - ', $attr->value) : $attr->value }}</span>
                                                @endforeach
                                            </td>
                                            <td class="text-center align-middle">{{ number_format($variant->purchase_price) }} VNĐ</td>
                                            <td class="text-center align-middle text-success fw-bold">{{ number_format($variant->selling_price) }} VNĐ</td>
                                            <td class="text-center align-middle"><span class="badge bg-light text-dark">{{ $variant->stock }}</span></td>
                                            <td class="text-center align-middle">
                                                @if($variant->status === 'active')
                                                    <span class="badge bg-success">Đang bán</span>
                                                @else
                                                    <span class="badge bg-danger">Ngừng bán</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <form action="{{ route('admin.variants.restore', $variant->id) }}" method="POST" style="display:inline-block">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm" title="Khôi phục"><i class="ti ti-restore"></i> Khôi phục</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center">Không có biến thể nào trong thùng rác.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            {{ $variants->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 