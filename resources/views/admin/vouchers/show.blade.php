@extends('admin.layouts.app')
@section('title', 'Chi tiết mã giảm giá')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Chi tiết mã giảm giá</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.vouchers.index') }}">Mã giảm giá</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">Chi tiết</li>
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
                            <h5>Thông tin mã giảm giá</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row">
                                <dt class="col-sm-3">Mã giảm giá</dt>
                                <dd class="col-sm-9">{{ $voucher->code }}</dd>

                                <dt class="col-sm-3">Mục đích</dt>
                                <dd class="col-sm-9">
                                    @if ($voucher->purpose === 'product_discount')
                                        Giảm giá sản phẩm
                                    @elseif ($voucher->purpose === 'free_shipping')
                                        Giảm giá vận chuyển
                                    @else
                                        Không xác định
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Mô tả</dt>
                                <dd class="col-sm-9">{{ $voucher->description ?? 'N/A' }}</dd>

                                <dt class="col-sm-3">Loại</dt>
                                <dd class="col-sm-9">
                                    @if ($voucher->type === 'fixed')
                                        Số tiền cố định (VNĐ)
                                    @elseif ($voucher->type === 'percentage')
                                        Phần trăm (%)
                                    @else
                                        Không xác định
                                    @endif
                                </dd>

                                <dt class="col-sm-3">Giá trị giảm giá</dt>
                                <dd class="col-sm-9">{{ $voucher->value }}</dd>

                                <dt class="col-sm-3">Giá trị đơn hàng tối thiểu</dt>
                                <dd class="col-sm-9">{{ $voucher->min_order_amount ?? 'Không có' }}</dd>

                                <dt class="col-sm-3">Ngày hết hạn</dt>
                                <dd class="col-sm-9">
                                    {{ $voucher->expires_at ? $voucher->expires_at->format('d/m/Y H:i') : 'Không có' }}</dd>

                                <dt class="col-sm-3">Giới hạn sử dụng</dt>
                                <dd class="col-sm-9">{{ $voucher->usage_limit ?? 'Không giới hạn' }}</dd>

                                <dt class="col-sm-3">Giới hạn theo người dùng</dt>
                                <dd class="col-sm-9">{{ $voucher->per_user_limit ?? 'Không giới hạn' }}</dd>

                                <dt class="col-sm-3">Trạng thái</dt>
                                <dd class="col-sm-9">
                                    @if ($voucher->is_active)
                                        <span class="badge bg-success">Kích hoạt</span>
                                    @else
                                        <span class="badge bg-danger">Không kích hoạt</span>
                                    @endif
                                </dd>
                            </dl>

                            <a href="{{ route('admin.vouchers.edit', $voucher->id) }}" class="btn btn-warning">Chỉnh sửa</a>
                            <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection
