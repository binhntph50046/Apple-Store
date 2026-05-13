@extends('admin.layouts.app')

@section('title', 'Tạo Bài Viết Mới')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 20px;
    }

    .pc-content {
        padding-top: 180px;
        /* Tăng khoảng cách trên */
    }

    .card-body {
        padding: 30px;
        /* Tăng padding trong card */
    }

    .form-label {
        font-size: 1.1rem;
        /* Tăng kích thước font của label */
    }

    .form-control,
    .form-select,
    .form-check-input {
        font-size: 1.1rem;
        /* Tăng kích thước font của input, select, checkbox */
        padding: 10px;
        /* Tăng padding trong các form element */
    }

    .card-header h5 {
        font-size: 1.25rem;
        /* Tăng kích thước font tiêu đề card */
    }

    .btn-primary {
        padding: 10px 20px;
        /* Tăng kích thước nút */
        font-size: 1.1rem;
    }

    .invalid-feedback {
        font-size: 0.9rem;
        /* Giảm kích thước font của lỗi */
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
                                <h5 class="m-b-10">Tạo Bài Viết Mới</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang Chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index') }}">Bài Viết</a></li>
                                <li class="breadcrumb-item" aria-current="page">Tạo Mới</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tạo Bài Viết Mới</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="form-group mb-3">
                                            <label for="title" class="form-label">Tiêu đề</label>
                                            <input type="text" name="title" id="title"
                                                class="form-control @error('title') is-invalid @enderror"
                                                value="{{ old('title') }}">
                                            @error('title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="content" class="form-label">Nội dung</label>
                                            <textarea class="snettech-editor form-control @error('content') is-invalid @enderror" id="content" name="content"
                                                rows="10">{{ old('content') }}</textarea>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group mb-3">
                                            <label for="category_id" class="form-label">Danh mục</label>
                                            <select name="category_id" id="category_id"
                                                class="form-select @error('category_id') is-invalid @enderror">
                                                <option value="">-- Chọn Danh Mục --</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Trường Image -->
                                        <div class="form-group mb-3">
                                            <label for="image" class="form-label">Hình ảnh (Ảnh bìa bài viết)</label>
                                            <input type="file" name="image" id="image"
                                                class="form-control @error('image') is-invalid @enderror">
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Trường Status -->
                                        <div class="form-group mb-3">
                                            <label for="status" class="form-label">Trạng thái</label>
                                            <select name="status" id="status"
                                                class="form-select @error('status') is-invalid @enderror">
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>
                                                    Hoạt động
                                                </option>
                                                <option value="inactive"
                                                    {{ old('status') == 'inactive' ? 'selected' : '' }}>
                                                    Không hoạt động
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="author" class="form-label">Tác giả</label>
                                            <select name="author_id" id="author_id"
                                                class="form-select @error('author_id') is-invalid @enderror">
                                                <option value="">Chọn Tác Giả</option>
                                                @foreach ($authors as $author)
                                                    <option value="{{ $author->id }}"
                                                        {{ old('author_id') == $author->id ? 'selected' : '' }}>
                                                        {{ $author->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('author')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">Lưu Bài Viết</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
