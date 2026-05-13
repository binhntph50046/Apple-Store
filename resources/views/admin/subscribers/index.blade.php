@extends('admin.layouts.app')
@section('title', 'Người đăng ký')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Người đăng ký</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                            <li class="breadcrumb-item" aria-current="page">Người đăng ký</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh sách</h5>
                <div>
                    <a href="{{ route('admin.subscribers.trash') }}" class="btn btn-danger btn-sm rounded-3">
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
                                        <input type="text" name="name" class="form-control" placeholder="Tìm kiếm theo tên..." value="{{ request('name') }}">
                                    </div>
                                
                                    <div class="col-md-4">
                                        <input type="email" name="email" class="form-control" placeholder="Tìm kiếm theo email..." value="{{ request('email') }}">
                                    </div>

                                    <div class="col-md-4">
                                        <input type="text" name="phone" class="form-control" placeholder="Tìm kiếm theo số điện thoại..." value="{{ request('phone') }}">
                                    </div>
                
                                    <div class="col-12 mt-3">
                                        <button type="submit" class="btn btn-primary btn-sm">Lọc</button>
                                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm">Đặt lại</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                <div class="table custom-shadow">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Tên</th>
                                <th>Email</th>
                                <th>Thời gian đăng ký</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subscribers as $subscriber)
                            <tr>
                                <td>{{ $subscriber->id }}</td>
                                <td>{{ $subscriber->name ?? 'N/A' }}</td>
                                <td>{{ $subscriber->email }}</td>
                                <td>{{ $subscriber->created_at->format('d/m/Y H:i') }}</td>
                                <td class="text-center">
                                            <form action="{{ route('admin.subscribers.delete', $subscriber->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm round-3" onclick="return confirm('Bạn có chắc chắn muốn xóa đăng ký này không??')">
                                                    <i class="ti ti-trash"></i> Xóa
                                                </button>
                                            </form>
                                        </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $subscribers->links() }}
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection
