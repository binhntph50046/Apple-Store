@extends('admin.layouts.app')
@section('title', 'Thùng rác')

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
                            <h5 class="m-b-10">Thùng rác</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Danh sách thùng rác</li>
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
                        <h5>Danh sách thùng rác</h5>
                        <div class="card-header-right">
                            <a href="{{ route('admin.categories.index') }}" class="btn btn-primary btn-sm rounded-3">
                                <i class="ti ti-arrow-left"></i> Quay lại danh mục
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="table custom-shadow">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Ảnh</th>
                                        <th>Icon</th>
                                        <th>Tên danh mục</th>
                                        <th>Slug</th>
                                        <th>Danh mục cha</th>
                                        <th>Loại danh mục</th>
                                        <th>Trạng thái</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        function renderCategoryTree($categories, &$index = 1, $prefix = '') {
                                            foreach ($categories as $category) {
                                                echo '<tr>';
                                                echo '<td>' . $index++ . '</td>';
                                                echo '<td>';
                                                if ($category->image) {
                                                    echo '<img src="' . asset($category->image) . '" alt="' . $category->name . '" class="img-thumbnail" style="max-height: 50px;">';
                                                } else {
                                                    echo '<span class="text-muted">Không có ảnh</span>';
                                                }
                                                echo '</td>';
                                                echo '<td>';
                                                if ($category->icon) {
                                                    echo '<i class="' . $category->icon . '" style="font-size: 1.5em;"></i>';
                                                } else {
                                                    echo '<span class="text-muted">Không có icon</span>';
                                                }
                                                echo '</td>';
                                                $parentName = $category->parent ? $category->parent->name : 'Không có danh mục cha';
                                                echo '<td>' . $prefix . ($category->parent_id ? '└─ ' : '') . $category->name . '</td>';
                                                echo '<td>' . $category->slug . '</td>';
                                                echo '<td>' . $parentName . '</td>';
                                                echo '<td>' . ($category->type == 1 ? 'Danh mục sản phẩm' : 'Danh mục bài viết') . '</td>';
                                                echo '<td><span class="badge ' . ($category->status == 'active' ? 'bg-success' : 'bg-danger') . '">' . ($category->status == 'active' ? 'Hoạt động' : 'Không hoạt động') . '</span></td>';
                                                echo '<td class="text-center">';
                                                echo '<form action="' . route('admin.categories.restore', $category->id) . '" method="POST" class="d-inline">';
                                                echo csrf_field();
                                                echo method_field('POST');
                                                echo '<button type="submit" class="btn btn-success btn-sm rounded-3 me-2" onclick="return confirm(\'Bạn có chắc chắn muốn khôi phục danh mục này không??\')"><i class="ti ti-refresh"></i> Khôi phục</button>';
                                                echo '</form>';
                                                // echo '<form action="' . route('admin.categories.forceDelete', $category->id) . '" method="POST" class="d-inline">';
                                                // echo csrf_field();
                                                // echo method_field('DELETE');
                                                // echo '<button type="submit" class="btn btn-danger btn-sm rounded-3" onclick="return confirm(\'Are you sure you want to permanently delete this category?\')"><i class="ti ti-trash"></i> Delete</button>';
                                                // echo '</form>';
                                                echo '</td>';
                                                echo '</tr>';

                                                if ($category->children && count($category->children)) {
                                                    renderCategoryTree($category->children, $index, $prefix . '&nbsp;&nbsp;&nbsp;&nbsp;  ');
                                                }
                                            }
                                        }

                                        $index = 1;
                                        renderCategoryTree($categories, $index);
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
