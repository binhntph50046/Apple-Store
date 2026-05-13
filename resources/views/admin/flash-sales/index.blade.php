@extends('admin.layouts.app')
@section('title', 'Danh sách khuyến mãi')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Khuyến mãi</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Khuyến mãi</li>
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
                            <h5>Danh sách khuyến mãi</h5>
                            <div class="card-header-right">
                                <a href="{{ route('admin.flash-sales.create') }}" class="btn btn-primary btn-sm rounded-3 me-2"
                                    title="Thêm khuyến mãi mới">
                                    <i class="ti ti-plus"></i> Thêm khuyến mãi mới
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- Search and Filter Form --}}
                            <form method="GET" action="{{ route('admin.flash-sales.index') }}"
                                class="row g-3 mb-4 align-items-end">
                                <div class="col-md-3">
                                    <label for="name" class="form-label">Tên</label>
                                    <input type="text" name="name" id="name" value="{{ request('name') }}"
                                        class="form-control" placeholder="Tìm kiếm theo tên...">
                                </div>

                                <div class="col-md-3">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Kích hoạt
                                        </option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Không kích hoạt
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label for="start_time" class="form-label">Thời gian bắt đầu (Từ)</label>
                                    <input type="datetime-local" name="start_time" id="start_time"
                                        value="{{ request('start_time') }}" class="form-control">
                                </div>

                                <div class="col-md-3">
                                    <label for="end_time" class="form-label">Thời gian kết thúc (Đến)</label>
                                    <input type="datetime-local" name="end_time" id="end_time"
                                        value="{{ request('end_time') }}" class="form-control">
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">Lọc</button>
                                    <a href="{{ route('admin.flash-sales.index') }}"
                                        class="btn btn-secondary">Đặt lại</a>
                                </div>
                            </form>

                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Tên</th>
                                            <th>Thời gian bắt đầu</th>
                                            <th>Thời gian kết thúc</th>
                                            <th>Trạng thái</th>
                                            <th class="text-center">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($flashSales as $flashSale)
                                            <tr>
                                                <td>{{ $flashSale->id }}</td>
                                                <td>{{ $flashSale->name }}</td>
                                                <td>{{ $flashSale->start_time ? $flashSale->start_time->format('Y-m-d H:i') : '' }}
                                                </td>
                                                <td>{{ $flashSale->end_time ? $flashSale->end_time->format('Y-m-d H:i') : '' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $statusClass = 'bg-secondary';
                                                        $statusText = 'Không xác định';
                                                        if ($flashSale->status == 1) {
                                                            $statusClass = 'bg-success';
                                                            $statusText = 'Kích hoạt';
                                                        } elseif ($flashSale->status == 0) {
                                                            $statusClass = 'bg-danger';
                                                            $statusText = 'Không kích hoạt';
                                                        } elseif ($flashSale->status == 2) {
                                                            $statusClass = 'bg-warning';
                                                            $statusText = 'Đã kết thúc';
                                                        }
                                                    @endphp
                                                    <span class="badge {{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="{{ route('admin.flash-sales.edit', $flashSale->id) }}"
                                                        class="btn btn-warning btn-sm rounded-3 me-2" title="Chỉnh sửa">
                                                        <i class="ti ti-edit"></i>
                                                    </a>
                                                    {{-- <form action="{{ route('admin.flash-sales.destroy', $flashSale->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this flash sale?')">
                                                            <i class="ti ti-trash"></i>
                                                        </button>
                                                    </form> --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $flashSales->appends(request()->all())->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection
