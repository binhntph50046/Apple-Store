@extends('admin.layouts.app')
@section('title', 'Danh sách mã giảm giá')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Mã giảm</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Mã giảm giá</li>
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
                            <h5>Danh sách mã giảm giá</h5>
                            <a href="{{ route('admin.vouchers.create') }}" class="btn btn-primary btn-sm rounded-3 me-2"
                                title="Thêm mã giảm giá mới">
                                <i class="ti ti-plus"></i> Thêm mã giảm giá mới
                            </a>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <!-- Filter -->
                            <div class="custom-shadow mb-4">
                                <form method="GET" action="{{ route('admin.vouchers.index') }}" class="row g-3">

                                    <div class="col-md-3">
                                        <label class="form-label">Mục đích</label>
                                        <select name="purpose" class="form-select">
                                            <option value="">Tất cả mục đích</option>
                                            <option value="product_discount"
                                                {{ request('purpose') == 'product_discount' ? 'selected' : '' }}>
                                                Giảm giá sản phẩm
                                            </option>
                                            <option value="free_shipping"
                                                {{ request('purpose') == 'free_shipping' ? 'selected' : '' }}>
                                                Giảm giá vận chuyển
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Loại</label>
                                        <select name="type" class="form-select">
                                            <option value="">Tất cả loại</option>
                                            <option value="percentage"
                                                {{ request('type') == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
                                            <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Số
                                                tiền
                                                cố định</option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="status" class="form-select">
                                            <option value="">Tất cả trạng thái</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                                Kích hoạt</option>
                                            <option value="inactive"
                                                {{ request('status') == 'inactive' ? 'selected' : '' }}>Không kích hoạt
                                            </option>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Hết hạn trước</label>
                                        <input type="date" name="expires_before" class="form-control"
                                            value="{{ request('expires_before') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Hết hạn sau</label>
                                        <input type="date" name="expires_after" class="form-control"
                                            value="{{ request('expires_after') }}">
                                    </div>

                                    <div class="col-md-3">
                                        <label class="form-label">Tìm kiếm</label>
                                        <input type="text" name="search" class="form-control"
                                            placeholder="Mã hoặc mô tả" value="{{ request('search') }}">
                                    </div>

                                    <div class="col-md-3 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">Lọc</button>
                                        <a href="{{ route('admin.vouchers.index') }}" class="btn btn-secondary">Đặt lại</a>
                                    </div>
                                </form>
                            </div>


                            <div class="table-responsive">
                                <table class="table table-hover text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th class="text-start">Mã</th>
                                            <th>Loại</th>
                                            <th>Mục đích</th>
                                            <th class="text-start">Mô tả</th>
                                            <th>Giá trị</th>
                                            <th>Hết hạn</th>
                                            <th>Kích hoạt?</th>
                                            <th>Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($vouchers as $voucher)
                                            <tr>
                                                <td class="text-start">{{ $voucher->code }}</td>
                                                <td>{{ ucfirst($voucher->type) }}</td>
                                                <td>
                                                    {{ $voucher->purpose == 'free_shipping' ? 'Miễn phí vận chuyển' : 'Giảm giá sản phẩm' }}
                                                </td>
                                                <td class="text-start">{{ Str::limit($voucher->description ?? '-', 30) }}</td>
                                                <td>
                                                    {{ $voucher->type === 'percentage' ? $voucher->value . '%' : number_format($voucher->value, 0, ',', '.') . ' VND' }}
                                                </td>
                                                <td>
                                                    {{ $voucher->expires_at ? \Carbon\Carbon::parse($voucher->expires_at)->format('d/m/Y H:i') : 'No Limit' }}
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $voucher->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $voucher->is_active ? 'Hoạt động' : 'Không hoạt động' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.vouchers.show', $voucher->id) }}"
                                                        class="btn btn-info btn-sm rounded-3 me-2" title="Xem chi tiết">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.vouchers.edit', $voucher->id) }}"
                                                        class="btn btn-warning btn-sm rounded-3 me-2" title="Chỉnh sửa">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.vouchers.destroy', $voucher->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa cái này không?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm rounded-3"
                                                            title="Xóa">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $vouchers->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection
