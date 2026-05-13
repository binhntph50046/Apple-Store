@extends('admin.layouts.app')
@section('title', 'Danh sách thông số kỹ thuật')

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
                                <h5 class="m-b-10">Danh sách thông số kỹ thuật</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Thông số kỹ thuật</li>
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
                                <h5>Danh sách thông số kỹ thuật</h5>
                                <div class="card-header-right">
                                    <a href="{{ route('admin.specifications.create') }}"
                                        class="btn btn-primary btn-sm rounded-3 me-2">
                                        <i class="ti ti-plus"></i> Thêm mới
                                    </a>
                                    <a href="{{ route('admin.specifications.trash') }}"
                                        class="btn btn-danger btn-sm rounded-3">
                                        <i class="ti ti-trash"></i> Thùng rác
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
                                        <form action="{{ route('admin.specifications.index') }}" method="GET"
                                            class="row g-3 mb-3">
                                            <div class="col-md-3">
                                                <input type="text" class="form-control" name="search"
                                                    placeholder="Tìm kiếm..." value="{{ request('search') }}">
                                            </div>
                                            <div class="col-md-3">

                                                <select class="form-select" name="category_id">
                                                    <option value="">Tất cả danh mục</option>
                                                    @foreach ($categories as $category)
                                                        <option value="{{ $category->id }}"
                                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                            {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>

                                            </div>
                                            <div class="col-md-3">

                                                <select class="form-select" name="status">
                                                    <option value="">Tất cả trạng thái</option>
                                                    <option value="active"
                                                        {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động
                                                    </option>
                                                    <option value="inactive"
                                                        {{ request('status') == 'inactive' ? 'selected' : '' }}>Không
                                                        hoạt
                                                        động
                                                    </option>
                                                </select>

                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary">Lọc</button>
                                                <a href="{{ route('admin.specifications.index') }}"
                                                    class="btn btn-secondary">Đặt lại</a>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="table custom-shadow">
                                    <table class="table table-hover table-borderless align-middle">
                                        <thead>
                                            <tr>
                                                <th>STT</th>
                                                <th>Tên</th>
                                                <th>Danh mục</th>
                                                <th>Mô tả</th>
                                                <th>Trạng thái</th>
                                                <th class="text-center">Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($specifications as $specification)
                                                <tr>
                                                    <td>{{ $specification->id }}</td>
                                                    <td>{{ $specification->name }}</td>
                                                    <td>
                                                        @if ($specification->category_ids && is_array($specification->category_ids))
                                                            @foreach ($specification->category_ids as $categoryId)
                                                                @php
                                                                    $category = $categories->firstWhere(
                                                                        'id',
                                                                        $categoryId,
                                                                    );
                                                                @endphp
                                                                @if ($category)
                                                                    <span
                                                                        class="badge bg-info me-1">{{ $category->name }}</span>
                                                                @endif
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">Không có danh mục</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $specification->description }}</td>
                                                    <td>
                                                        <span
                                                            class="badge bg-{{ $specification->status == 'active' ? 'success' : 'danger' }}">
                                                            {{ $specification->status == 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center text-nowrap">
                                                        <a href="{{ route('admin.specifications.edit', $specification) }}"
                                                            class="btn btn-warning btn-sm rounded-3 me-2">
                                                            <i class="ti ti-edit"></i>
                                                        </a>
                                                        <form
                                                            action="{{ route('admin.specifications.destroy', $specification) }}"
                                                            method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm rounded-3"
                                                                onclick="return confirm('Bạn có chắc chắn muốn xóa?')">
                                                                <i class="ti ti-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Không có dữ liệu</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end mt-4">
                                    {{ $specifications->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ Main Content ] end -->
            </div>
        </div>
    @endsection
