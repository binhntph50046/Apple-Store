@extends('admin.layouts.app')
@section('title', 'Hóa đơn #' . $invoice->invoice_code)
@section('content')
    <div class="pc-container card shadow-sm rounded-3 border-0 custom-shadow">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Hóa đơn #{{ $invoice->invoice_code }}</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.invoices.index') }}">Hóa đơn</a></li>
                                <li class="breadcrumb-item" aria-current="page">#{{ $invoice->invoice_code }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5>Chi tiết hóa đơn</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th>Mã hóa đơn:</th>
                                    <td>{{ $invoice->invoice_code }}</td>
                                </tr>
                                <tr>
                                    <th>Mã đơn hàng:</th>
                                    <td>#{{ $invoice->order_id }}</td>
                                </tr>
                                <tr>
                                    <th>Tổng tiền:</th>
                                    <td>{{ number_format($invoice->total) }} VNĐ</td>
                                </tr>
                                {{-- <tr>
                                    <th>Người xuất:</th>
                                    <td>{{ optional($invoice->issued_by ? App\Models\User::find($invoice->issued_by) : null)->name ?? '-' }}
                                    </td>
                                </tr> --}}
                                <tr>
                                    <th>Ngày xuất:</th>
                                    <td>{{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : '' }}</td>
                                </tr>
                                <tr>
                                    <th>File hóa đơn:</th>
                                    <td>
                                        @if ($invoice->file_path)
                                            <a href="{{ asset($invoice->file_path) }}" class="btn btn-info btn-sm"
                                                target="_blank">Tải file</a>
                                        @else
                                            <span class="text-muted">Chưa có file</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <a href="{{ route('admin.orders.show', $invoice->order_id) }}"
                                class="btn btn-outline-primary">Xem đơn hàng</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
