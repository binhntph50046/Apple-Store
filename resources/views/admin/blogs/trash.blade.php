@extends('admin.layouts.app')

@section('title', 'Thùng rác bài viết')

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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5>Danh sách bài viết đã xóa</h5>
                            <div>
                                <a href="{{ route('admin.blogs.index') }}" class="btn btn-primary btn-sm rounded-3">
                                    <i class="ti ti-arrow-left"></i> Quay lại danh sách
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <div class="table-responsive custom-shadow">
                                <table class="table table-hover table-borderless align-middle">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tiêu đề</th>
                                            <th>Hình ảnh</th>
                                            <th>Danh mục</th>
                                            <th>Tác giả</th>
                                            <th>Thời gian xóa</th>
                                            <th class="text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($blogs as $index => $blog)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ Str::limit($blog->title, 50) }}</td>
                                                <td>
                                                    @if ($blog->image)
                                                        @php
                                                            $filename = basename($blog->image);
                                                            $url = asset('uploads/blogs/' . $filename);
                                                        @endphp
                                                        <img src="{{ $url }}" alt="{{ $blog->title }}"
                                                            style="width:80px;height:50px;object-fit:cover;">
                                                    @else
                                                        <span>Không có</span>
                                                    @endif
                                                </td>
                                                <td>{{ $blog->category->name ?? 'Chưa có' }}</td>
                                                <td>{{ $blog->author->name ?? 'Admin' }}</td>
                                                <td>{{ $blog->deleted_at->format('d/m/Y H:i') }}</td>
                                               <td class="text-center">
                                                <form action="{{ route('admin.blogs.restore', $blog->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PUT')
                                                    <button type="submit" class="btn btn-success btn-sm me-1" onclick="return confirm('Are you sure?')" title="Restore">
                                                        <i class="ti ti-restore"></i> Khôi phục
                                                    </button>
                                                </form>
                                               
                                            </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-muted">
                                                    Không có bài viết nào trong thùng rác.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>

                                {{-- nếu cần phân trang --}}
                                {{-- {{ $trashedBlogs->links() }} --}}
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection
