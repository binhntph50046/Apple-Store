@extends('admin.layouts.app')
@section('title', 'Chi tiết liên hệ')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.contacts.index') }}">Liên hệ</a></li>
                            <li class="breadcrumb-item">Chi tiết</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Chi tiết liên hệ</h5>
                        <a href="{{ route('admin.contacts.index') }}" class="btn btn-secondary btn-sm rounded-3">
                            <i class="ti ti-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="mb-4">
                            <h6>Thông tin cơ bản</h6>
                            <hr>
                            <p><strong>Họ tên:</strong> {{ $contact->first_name }} {{ $contact->last_name }}</p>
                            <p><strong>Email:</strong> <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
                            <p><strong>Điện thoại:</strong> {{ $contact->phone ?? 'Không có' }}</p>
                            <p><strong>Ngày gửi:</strong> {{ $contact->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        <div class="mb-4">
                            <h6>Nội dung tin nhắn</h6>
                            <hr>
                            <div class="p-3 bg-light rounded">
                                {!! nl2br(e($contact->message)) !!}
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <form action="{{ route('admin.contacts.delete', $contact->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa liên hệ này không?')">
                                    <i class="ti ti-trash"></i> Xóa liên hệ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection