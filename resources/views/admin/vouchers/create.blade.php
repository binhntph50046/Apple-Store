@extends('admin.layouts.app')
@section('title', 'Tạo mã giảm giá')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Tạo mã giảm giá</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.vouchers.index') }}">Mã giảm giá</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">Tạo mới</li>
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
                            <h5>Tạo mã giảm giá mới</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.vouchers.store') }}" method="POST">
                                @csrf
                                
                                <!-- Thông tin cơ bản -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="code" class="form-label">Mã giảm giá</label>
                                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                                            id="code" name="code" value="{{ old('code') }}">
                                        @error('code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="description" class="form-label">Mô tả</label>
                                        <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                            rows="1">{{ old('description') }}</textarea>
                                        @error('description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Loại và mục đích -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="type" class="form-label">Loại</label>
                                        <select name="type" id="type"
                                            class="form-select @error('type') is-invalid @enderror" required>
                                            <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Số tiền cố định (VND)</option>
                                            <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Phần trăm (%)</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="purpose" class="form-label">Mục đích</label>
                                        <input type="hidden" name="purpose" value="product_discount">
                                        <input type="text" class="form-control" value="Giảm giá sản phẩm" readonly>
                                    </div>
                                </div>

                                <!-- Giá trị và điều kiện -->
                                <div class="row mb-4">
                                    <div class="col-md-4">
                                        <label for="value" class="form-label">Giá trị giảm giá</label>
                                        <input type="number" step="1" min="0" name="value" id="value"
                                            class="form-control @error('value') is-invalid @enderror"
                                            value="{{ old('value') }}">
                                        @error('value')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="min_order_amount" class="form-label">Giá trị đơn hàng tối thiểu</label>
                                        <input type="number" step="1" min="0" name="min_order_amount"
                                            id="min_order_amount"
                                            class="form-control @error('min_order_amount') is-invalid @enderror"
                                            value="{{ old('min_order_amount') }}">
                                        @error('min_order_amount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="expires_at" class="form-label">Ngày hết hạn</label>
                                        <input type="datetime-local" name="expires_at" id="expires_at"
                                            class="form-control @error('expires_at') is-invalid @enderror"
                                            value="{{ old('expires_at') }}">
                                        @error('expires_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Giới hạn sử dụng -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label for="usage_limit" class="form-label">Giới hạn sử dụng</label>
                                        <input type="number" name="usage_limit" id="usage_limit"
                                            class="form-control @error('usage_limit') is-invalid @enderror"
                                            value="{{ old('usage_limit') }}">
                                        @error('usage_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="per_user_limit" class="form-label">Giới hạn theo người dùng</label>
                                        <input type="number" name="per_user_limit" id="per_user_limit"
                                            class="form-control @error('per_user_limit') is-invalid @enderror"
                                            value="{{ old('per_user_limit') }}">
                                        @error('per_user_limit')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Trạng thái -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <div class="form-check">
                                            <input type="checkbox" name="is_active" value="1" class="form-check-input"
                                                id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">Kích hoạt mã giảm giá</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Tạo mã giảm giá</button>
                                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại</a>
                                    </div>
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
