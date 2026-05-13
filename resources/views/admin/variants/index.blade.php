@extends('admin.layouts.app')
@section('title', 'Quản lý biến thể sản phẩm')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }
    .variant-img-thumb {
        box-shadow: 0 2px 12px 0 rgba(0,0,0,0.12), 0 1.5px 4px 0 rgba(0,0,0,0.08);
        border: 2px solid #eee;
        transition: transform 0.2s, border-color 0.2s;
    }
    .variant-img-thumb:hover {
        transform: scale(1.08);
        border-color: #007bff;
        box-shadow: 0 4px 18px 0 rgba(0,123,255,0.15), 0 3px 8px 0 rgba(0,0,0,0.10);
        z-index: 2;
    }
</style>

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Biến thể sản phẩm</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Biến thể sản phẩm</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Danh sách biến thể</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.variants.trash') }}" class="btn btn-danger btn-sm rounded-3">
                                <i class="ti ti-trash"></i> Thùng rác
                                @if(isset($trashCount) && $trashCount > 0)
                                    <span class="badge bg-light text-dark ms-1">{{ $trashCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.variants.index') }}" class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <input type="text" name="name" class="form-control" placeholder="Tìm theo tên biến thể..." value="{{ request('name') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="product_id" class="form-select">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">-- Chọn trạng thái --</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang bán</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                                        <a href="{{ route('admin.variants.index') }}" class="btn btn-secondary">Đặt lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table custom-shadow">
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
                                        <th class="text-center" style="width: 120px">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($variants as $index => $variant)
                                        <tr @if($variant->is_default) style="background:#e6f9e6" @endif>
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
                                                <button type="button" class="btn btn-warning btn-sm rounded-3 me-2" data-bs-toggle="modal" data-bs-target="#editVariantModal{{ $variant->id }}" title="Sửa">
                                                    <i class="ti ti-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.variants.destroy', $variant->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa biến thể này?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Xóa"><i class="ti ti-trash"></i></button>
                                                </form>
                                                <!-- Modal Sửa Biến Thể -->
                                                <div class="modal fade" id="editVariantModal{{ $variant->id }}" tabindex="-1" aria-labelledby="editVariantModalLabel{{ $variant->id }}" aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.variants.update', $variant->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PUT')
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="editVariantModalLabel{{ $variant->id }}">Sửa biến thể</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body row g-3">
                                                                    <div class="col-md-6 mb-2">
                                                                        <label class="form-label">Tên biến thể</label>
                                                                        <input type="text" class="form-control" value="{{ $variant->name }}" disabled>
                                                                    </div>
                                                                    <div class="col-md-6 mb-2">
                                                                        <label class="form-label">Sản phẩm</label>
                                                                        <input type="text" class="form-control" value="{{ $variant->product ? $variant->product->name : 'N/A' }}" disabled>
                                                                    </div>
                                                                    <div class="col-md-12 mb-2">
                                                                        <label class="form-label">Thuộc tính</label>
                                                                        <input type="text" class="form-control" value="@foreach($variant->attributeValues as $attr){{ is_array($attr->value) ? implode(' - ', $attr->value) : $attr->value }} @endforeach" disabled>
                                                                    </div>
                                                                    <div class="col-md-4 mb-2">
                                                                        <label class="form-label">Giá nhập</label>
                                                                        <input type="number" name="purchase_price" class="form-control" value="{{ $variant->purchase_price }}" min="0" required>
                                                                    </div>
                                                                    <div class="col-md-4 mb-2">
                                                                        <label class="form-label">Giá bán</label>
                                                                        <input type="number" name="selling_price" class="form-control" value="{{ $variant->selling_price }}" min="0" required>
                                                                    </div>
                                                                    <div class="col-md-4 mb-2">
                                                                        <label class="form-label">Tồn kho</label>
                                                                        <input type="number" name="stock" class="form-control" value="{{ $variant->stock }}" min="0" required>
                                                                    </div>
                                                                    <div class="col-md-6 mb-2">
                                                                        <label class="form-label">Trạng thái</label>
                                                                        <select name="status" class="form-select">
                                                                            <option value="active" {{ $variant->status == 'active' ? 'selected' : '' }}>Đang bán</option>
                                                                            <option value="inactive" {{ $variant->status == 'inactive' ? 'selected' : '' }}>Ngừng bán</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-md-6 mb-2">
                                                                        <label class="form-label">Ảnh biến thể (có thể chọn nhiều)</label>
                                                                        <input type="file" name="images[]" class="form-control variant-images" multiple accept="image/*" data-variant-id="{{ $variant->id }}">
                                                                        <div class="mt-2 image-preview-container" id="preview-{{ $variant->id }}">
                                                                            @php
                                                                                $images = is_string($variant->images) ? json_decode($variant->images, true) : $variant->images;
                                                                            @endphp
                                                                            @if(is_array($images))
                                                                                @foreach($images as $img)
                                                                                    <div class="image-preview-wrapper">
                                                                                        <img src="{{ asset($img) }}" class="image-preview" style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:4px;">
                                                                                        <button type="button" class="delete-image" data-image="{{ $img }}" data-variant="{{ $variant->id }}">×</button>
                                                                                    </div>
                                                                                @endforeach
                                                                            @endif
                                                                        </div>
                                                                        <input type="hidden" name="images_to_delete" id="images_to_delete_{{ $variant->id }}" value="">
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">Không có biến thể nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            {{ $variants->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý xóa ảnh cũ
    document.querySelectorAll('.delete-image').forEach(button => {
        button.addEventListener('click', function() {
            const imagePath = this.dataset.image;
            const variantId = this.dataset.variant;
            const input = document.getElementById('images_to_delete_' + variantId);
            let imagesToDelete = [];
            if (input.value) {
                try { imagesToDelete = JSON.parse(input.value); } catch(e) { imagesToDelete = []; }
            }
            imagesToDelete.push(imagePath);
            input.value = JSON.stringify(imagesToDelete);
            this.closest('.image-preview-wrapper').remove();
        });
    });

    // Xử lý preview ảnh mới
    document.querySelectorAll('.variant-images').forEach(input => {
        input.addEventListener('change', function() {
            const variantId = this.dataset.variantId;
            const previewContainer = document.getElementById('preview-' + variantId);
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'image-preview-wrapper';
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'image-preview';
                        img.style.width = '40px';
                        img.style.height = '40px';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '6px';
                        img.style.marginRight = '4px';
                        const deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.className = 'delete-image';
                        deleteBtn.textContent = '×';
                        deleteBtn.onclick = function() {
                            wrapper.remove();
                        };
                        wrapper.appendChild(img);
                        wrapper.appendChild(deleteBtn);
                        previewContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    });
});
</script>
@endpush 