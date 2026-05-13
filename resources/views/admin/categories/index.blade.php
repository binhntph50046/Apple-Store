@extends('admin.layouts.app')
@section('title', 'Quản lý danh mục')

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
                            <h5 class="m-b-10">Danh mục</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Danh mục</li>
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
                        <h5>Danh sách danh mục</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-sm rounded-3 me-2">
                                <i class="ti ti-plus"></i> Thêm danh mục
                            </a>
                            <a href="{{ route('admin.categories.trash') }}" class="btn btn-danger btn-sm rounded-3">
                                <i class="ti ti-trash"></i> Thùng rác
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
                                <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3 mb-3">
                                    <div class="col-md-3">
                                        <input type="text" name="name" class="form-control" placeholder="Tìm kiếm theo tên..." value="{{ request('name') }}">
                                    </div>
                                
                                    <div class="col-md-3">
                                        <select name="type" class="form-select">
                                            <option value="">-- Lọc theo kiểu danh mục --</option>
                                            <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>Danh mục sản phẩm</option>
                                            <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>Danh mục bài viết</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <select id="category_id" name="category_id" class="form-select">
                                            <option value="">-- Tất cả --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">-- Lọc theo trạng thái --</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                                        </select>
                                    </div>
                                
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary">Lọc</button>
                                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Đặt lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table custom-shadow ">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ảnh</th>
                                        <th>Icon</th>
                                        <th>Tên</th>
                                        <th>Slug</th>
                                        <th>Loại danh mục</th>
                                        <th>Trạng thái</th>
                                        <th>Vị trí</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $productCategories = $categories->filter(function($cat) {
                                            return $cat->type == 1;
                                        });

                                        $postCategories = $categories->filter(function($cat) {
                                            return $cat->type == 2;
                                        });

                                        $index = 1;

                                        function renderCategoryTree($categories, &$index = 1, $prefix = '') {
                                            foreach ($categories as $category) {
                                                echo '<tr>';
                                                echo '<td>' . $index++ . '</td>';
                                                echo '<td>';
                                                if ($category->image) {
                                                    echo '<img src="' . asset($category->image) . '" alt="' . $category->name . '" class="img-thumbnail" style="max-height: 80px;">';
                                                } else {
                                                    echo '<span class="text-muted">Không có ảnh</span>';
                                                }
                                                echo '</td>';
                                                echo '<td>';
                                                if ($category->icon) {
                                                    echo '<i class="' . $category->icon . '" style="font-size: 1.5em;"></i>';
                                                } else {
                                                    echo '<span class="text-muted">No icon</span>';
                                                }
                                                echo '</td>';
                                                echo '<td>' . $prefix . ($category->parent_id ? '└─ ' : '') . $category->name . '</td>';
                                                echo '<td>' . $category->slug . '</td>';
                                                echo '<td>' . ($category->type == 1 ? 'Danh mục sản phẩm' : 'Danh mục bài viết') . '</td>';
                                                echo '<td><span class="badge ' . ($category->status == 'active' ? 'bg-success' : 'bg-danger') . '">'
                                                    . ($category->status == 'active' ? 'Hoạt động' : 'Không hoạt động') .
                                                    '</span></td>';

                                                echo '<td>';
                                                if (!$category->parent_id) {
                                                    echo '<form action="' . route('admin.categories.changeOrder') . '" method="POST" style="display:inline">';
                                                    echo csrf_field();
                                                    echo '<input type="hidden" name="category_id" value="' . $category->id . '">';
                                                    echo '<input type="hidden" name="direction" value="up">';
                                                    echo '<button type="submit" class="btn btn-warning btn-sm" title="Move Up"><i class="ti ti-arrow-up"></i></button>';
                                                    echo '</form>';

                                                    echo '<form action="' . route('admin.categories.changeOrder') . '" method="POST" style="display:inline">';
                                                    echo csrf_field();
                                                    echo '<input type="hidden" name="category_id" value="' . $category->id . '">';
                                                    echo '<input type="hidden" name="direction" value="down">';
                                                    echo '<button type="submit" class="btn btn-warning btn-sm ms-1" title="Move Down"><i class="ti ti-arrow-down"></i></button>';
                                                    echo '</form>';
                                                }
                                                echo '</td>';

                                                echo '<td class="text-center">';
                                                echo '<a href="' . route('admin.categories.edit', $category->id) . '" class="btn btn-warning btn-sm rounded-3 me-2"><i class="ti ti-edit"></i></a>';
                                                echo '<form action="' . route('admin.categories.destroy', $category->id) . '" method="POST" class="d-inline">';
                                                echo csrf_field();
                                                echo method_field('DELETE');
                                                echo '<button type="submit" class="btn btn-danger btn-sm rounded-3" onclick="return confirm(\'Bạn có chắc chắn muốn xóa danh mục này không??\')"><i class="ti ti-trash"></i></button>';
                                                echo '</form>';
                                                echo '</td>';
                                                echo '</tr>';

                                                if ($category->children && count($category->children)) {
                                                    renderCategoryTree($category->children, $index, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;  ');
                                                }
                                            }
                                        }

                                        renderCategoryTree($productCategories, $index);
                                        renderCategoryTree($postCategories, $index);
                                    @endphp
                                </tbody>

                            </table>
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection
