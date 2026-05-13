@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa Bài Viết')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 20px;
    }

    .pc-content {
        padding-top: 180px;
    }

    .card-body {
        padding: 30px;
    }

    .form-label {
        font-size: 1.1rem;
    }

    .form-control,
    .form-select,
    .form-check-input {
        font-size: 1.1rem;
        padding: 10px;
    }

    .card-header h5 {
        font-size: 1.25rem;
    }

    .btn-primary {
        padding: 10px 20px;
        font-size: 1.1rem;
    }

    .invalid-feedback {
        font-size: 0.9rem;
    }
</style>

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header mb-4">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Chỉnh sửa bài viết</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Bài Viết</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Chỉnh sửa bài viết: {{ $blog->title }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="title" class="form-label">Tiêu đề</label>
                                            <input type="text" name="title" id="title"
                                                value="{{ old('title', $blog->title) }}"
                                                class="form-control @error('title') is-invalid @enderror">
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="content" class="form-label">Nội dung</label>
                                            <textarea name="content" id="content" rows="10"
                                                class="snettech-editor form-control @error('content') is-invalid @enderror">{{ old('content', $blog->content) }}</textarea>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="category_id" class="form-label">Loại</label>
                                            <select name="category_id" id="category_id"
                                                class="form-select @error('category_id') is-invalid @enderror">
                                                <option value="">-- Chọn danh mục --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id', $blog->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="image" class="form-label">Hình ảnh</label>

                                            {{-- Chỉ hiện khung preview khi đã có ảnh --}}
                                            @if ($blog->image)
                                                @php
                                                    $filename = basename($blog->image);
                                                    $url = asset('uploads/blogs/' . $filename);
                                                @endphp
                                                <div class="mb-2">
                                                    <img src="{{ $url }}" alt="{{ $blog->title }}"
                                                        class="img-fluid" style="max-width: 150px;">
                                                </div>
                                            @endif

                                            {{-- Luôn hiển thị ô chọn file --}}
                                            <input type="file" name="image" id="image"
                                                class="form-control @error('image') is-invalid @enderror">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label">Trạng thái</label>
                                            <select name="status" id="status"
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="active"
                                                    {{ old('status', $blog->status) == 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ old('status', $blog->status) == 'inactive' ? 'selected' : '' }}>
                                                    Không hoạt động
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="author_id" class="form-label">Tác giả</label>
                                            <select name="author_id" id="author_id"
                                                class="form-select @error('author_id') is-invalid @enderror">
                                                <option value="">Chọn Tác Giả</option>
                                                @foreach ($authors as $author)
                                                    <option value="{{ $author->id }}"
                                                        {{ old('author_id', $blog->author_id) == $author->id ? 'selected' : '' }}>
                                                        {{ $author->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('author_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                                    <a href="{{ route('admin.blogs.index') }}" class="btn btn-secondary ms-2">Quay lại
                                        danh sách</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
