@extends('admin.layouts.app')

@section('title', 'Thùng rác - Thông số kỹ thuật')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Thùng rác - Thông số kỹ thuật</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.specifications.index') }}">Thông số kỹ thuật</a></li>
                            <li class="breadcrumb-item" aria-current="page">Thùng rác</li>
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
                        <div class="d-flex justify-content-between align-items-center">
                            <h5>Danh sách thông số kỹ thuật đã xóa</h5>
                            <a href="{{ route('admin.specifications.index') }}" class="btn btn-primary">
                                <i class="ti ti-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Tên</th>
                                        <th>Danh mục</th>
                                        <th>Mô tả</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày xóa</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($trashedSpecifications as $specification)
                                        <tr>
                                            <td>{{ $specification->id }}</td>
                                            <td>{{ $specification->name }}</td>
                                            <td>{{ $specification->category ? $specification->category->name : 'Không có' }}</td>
                                            <td>{{ Str::limit($specification->description, 50) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $specification->status == 'active' ? 'success' : 'danger' }}">
                                                    {{ $specification->status == 'active' ? 'Hoạt động' : 'Không hoạt động' }}
                                                </span>
                                            </td>
                                            <td>{{ $specification->deleted_at->format('d/m/Y H:i:s') }}</td>
                                            <td>
                                                <form action="{{ route('admin.specifications.restore', $specification->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc chắn muốn khôi phục?')">
                                                        <i class="ti ti-refresh"></i> Khôi phục
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có dữ liệu</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            {{ $trashedSpecifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection 