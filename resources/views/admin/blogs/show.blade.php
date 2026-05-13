@extends('admin.layouts.app')

@section('title', $blog->title)

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            {{-- Breadcrumb --}}
            <div class="page-header mb-4">
                <h5 class="page-header-title">{{ $blog->title }}</h5>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Bài Viết</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Xem</li>
                </ul>
            </div>

            <div class="card mb-4">
                @if ($blog->image)
                    @php
                        // Lấy tên file từ chuỗi lưu trong DB
                        $filename = basename($blog->image);
                        // Tạo URL thực tế tới public/uploads/blogs
                        $url = asset('uploads/blogs/' . $filename);
                    @endphp
                    <img src="{{ $url }}" class="card-img-top img-fluid" alt="{{ $blog->title }}"
                        style="max-width: 150px;">
                @endif
                <div class="card-body">
                    <h3 class="card-title">{{ $blog->title }}</h3>
                    <p><strong>Danh mục:</strong> {{ $blog->category->name ?? 'N/A' }}</p>
                    <p><strong>Trạng thái:</strong> {{ ucfirst($blog->status) }}</p>
                    <hr>
                    <div class="blog-content">
                        {!! $blog->content !!}
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-outline-primary">Chỉnh sửa</a>
                        <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
