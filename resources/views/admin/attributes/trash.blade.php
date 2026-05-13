@extends('admin.layouts.app')
@section('title', 'Thùng rác - Loại thuộc tính')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }
    .category-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .category-item {
        font-weight: 500;
        color: #2196F3;
        padding: 4px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .category-item:last-child {
        border-bottom: none;
    }
    .attribute-value {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 8px;
        background: #f8f9fa;
        border-radius: 4px;
        margin-bottom: 4px;
    }
    .color-preview {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        border: 1px solid #dee2e6;
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
                            <h5 class="m-b-10">Thùng rác - Loại thuộc tính</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Thùng rác - Loại thuộc tính</li>
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
                    <div class="card-header">
                        <h5>Thùng rác loại thuộc tính</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.attributes.index') }}" class="btn btn-primary btn-sm rounded-3">
                                <i class="ti ti-arrow-left"></i> Quay lại loại thuộc tính
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($attributeTypes->isEmpty())
                            <p>Không tìm thấy loại thuộc tính nào đã xóa.</p>
                        @else
                        <div class="table custom-shadow">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Danh mục</th>
                                        <th>Giá trị</th>
                                        <th>Màu sắc</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($attributeTypes as $index => $attributeType)
                                        <tr>
                                            <td>{{ $attributeTypes->firstItem() + $index }}</td>
                                            <td>{{ $attributeType->name }}</td>
                                            <td>
                                                <div class="category-list">
                                                    @if($attributeType->category_ids && is_array($attributeType->category_ids))
                                                        @foreach($attributeType->category_ids as $categoryId)
                                                            @php
                                                                $category = $categories->firstWhere('id', $categoryId);
                                                            @endphp
                                                            @if($category)
                                                                <div class="category-item">{{ $category->name }}</div>
                                                            @endif
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">Không có danh mục</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($attributeType->attributeValues->isNotEmpty())
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($attributeType->attributeValues as $value)
                                                            <div class="attribute-value">
                                                                {{ is_array($value->value) ? implode(', ', $value->value) : $value->value }}
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Không có giá trị</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($attributeType->attributeValues->isNotEmpty())
                                                    <div class="d-flex flex-column gap-1">
                                                        @foreach($attributeType->attributeValues as $value)
                                                            <div class="attribute-value">
                                                                @if(!empty($value->hex) && $value->hex[0] !== '')
                                                                    <div class="color-preview" style="background-color: {{ $value->hex[0] }}"></div>
                                                                    <span class="small">{{ $value->hex[0] }}</span>
                                                                @else
                                                                    <span class="text-muted">Không có màu sắc</span>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-muted">Không có màu sắc</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $attributeType->status == 'active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ ucfirst($attributeType->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <form action="{{ route('admin.attributes.restore', $attributeType) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục loại thuộc tính này không?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Khôi phục</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $attributeTypes->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection