@extends('admin.layouts.app')
@section('title', 'Quản lý liên hệ')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }

    .message-preview {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Liên hệ</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Liên hệ</li>
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
                        <h5>Danh sách liên hệ</h5>
                        <div>
                            <a href="{{ route('admin.contacts.trash') }}" class="btn btn-danger btn-sm rounded-3">
                                <i class="ti ti-trash"></i> Thùng rác
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <form method="GET" action="{{ route('admin.contacts.index') }}" class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <input type="text" name="name" class="form-control" placeholder="Tìm theo tên..." value="{{ request('name') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <input type="email" name="email" class="form-control" placeholder="Tìm theo email..." value="{{ request('email') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <input type="text" name="phone" class="form-control" placeholder="Tìm theo số điện thoại..." value="{{ request('phone') }}">
                                    </div>

                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">Đặt lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table custom-shadow">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>STT</th>
                                        <th>Họ tên</th>
                                        <th>Email</th>
                                        <th>Điện thoại</th>
                                        <th>Nội dung</th>
                                        <th>Ngày gửi</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($contacts as $contact)
                                    <tr>
                                        <td>{{ $contact->id }}</td>
                                        <td>{{ $contact->first_name }} {{ $contact->last_name }}</td>
                                        <td>{{ $contact->email }}</td>
                                        <td>{{ $contact->phone ?? 'Không có' }}</td>
                                        <td class="message-preview" title="{{ $contact->message }}">
                                            {{ Str::limit($contact->message, 50) }}
                                        </td>
                                        <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.contacts.show', $contact->id) }}" class="btn btn-info btn-sm rounded-3 me-2">
                                                <i class="ti ti-eye"></i> Xem
                                            </a>
                                            <form action="{{ route('admin.contacts.delete', $contact->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-3" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này không?')">
                                                    <i class="ti ti-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            {{ $contacts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection