<!-- resources/views/admin/faqs/edit.blade.php -->
@extends('admin.layouts.app')
@section('title', 'Sửa câu hỏi')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Sửa câu hỏi thường gặp</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">Câu hỏi thường gặp</a></li>
                            <li class="breadcrumb-item" aria-current="page">Chỉnh sửa</li>
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
                        <h5>Sửa câu hỏi thường gặp</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('admin.faqs.update', $faq->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="question" class="form-label">Câu hỏi</label>
                                <input type="text" name="question" id="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question', $faq->question) }}" required>
                                @error('question')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="answer" class="form-label">Câu trả lời</label>
                                <textarea name="answer" id="answer" class="form-control @error('answer') is-invalid @enderror" rows="5" required>{{ old('answer', $faq->answer) }}</textarea>
                                @error('answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="keywords" class="form-label">Từ khóa (tuỳ chọn)</label>
                                <input type="text" name="keywords" id="keywords" class="form-control @error('keywords') is-invalid @enderror" value="{{ old('keywords', $faq->keywords) }}">
                                @error('keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    <option value="active" {{ old('status', $faq->status) == 'active' ? 'selected' : '' }}>Hiển thị</option>
                                    <option value="inactive" {{ old('status', $faq->status) == 'inactive' ? 'selected' : '' }}>Ẩn</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary">Cập nhật câu hỏi</button>
                                <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection