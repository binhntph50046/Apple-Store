@extends('admin.layouts.app')
@section('title', 'Yêu cầu hoàn hàng')
@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Hoàn hàng</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item" aria-current="page">Yêu cầu hoàn hàng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Đơn hàng</th>
                                            <th>Khách hàng</th>
                                            <th>Trạng thái</th>
                                            <th>Ngày yêu cầu</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($returns as $return)
                                            <tr>
                                                <td>{{ $return->id }}</td>
                                                <td>#{{ $return->order->id }}</td>
                                                <td>{{ $return->user->name }} ({{ $return->user->email }})</td>
                                                <td>
                                                    @php
                                                        $badgeClass = match ($return->status) {
                                                            'approved' => 'bg-success',
                                                            'rejected' => 'bg-danger',
                                                            'refunded' => 'bg-primary',
                                                            default => 'bg-warning',
                                                        };

                                                        $statusText = match ($return->status) {
                                                            'approved' => 'Đã xác nhận',
                                                            'rejected' => 'Không xác nhận',
                                                            'refunded' => 'Đã hoàn tiền',
                                                            default => 'Đang xử lý',
                                                        };
                                                    @endphp

                                                    <span class="badge {{ $badgeClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>

                                                <td>{{ $return->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('admin.order-returns.show', $return->id) }}"
                                                        class="btn btn-sm btn-outline-primary rounded-3 me-1"
                                                        title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if ($return->status == 'pending')
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $returns->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
