@extends('admin.layouts.app')
@section('title', 'Deleted Notifications')

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
                            <h5 class="m-b-10">Xóa Thông Báo</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.notify.index') }}">Thông báo</a></li>
                            <li class="breadcrumb-item" aria-current="page">Thùng rác</li>
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
                        <h5>Danh sách thông báo đã xóa</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        <div class="table custom-shadow">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>Tiêu đề</th>
                                        <th>Nội dung</th>
                                        <th>Thời gian xóa</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($notifications as $noti)
                                        <tr>
                                            <td>{{ $noti->data['title'] ?? '' }}</td>
                                            <td class="message-preview" title="{{ $noti->data['message'] ?? '' }}">
                                                {{ Str::limit($noti->data['message'] ?? '', 50) }}
                                            </td>
                                            <td>{{ $noti->deleted_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
                                                <form action="{{ route('admin.notify.restore', $noti->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm rounded-3 me-2"
                                                        onclick="return confirm('Khôi phục thông báo này?')">
                                                        <i class="ti ti-restore"></i> Khôi phục
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.notify.forceDelete', $noti->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm rounded-3"
                                                        onclick="return confirm('Xóa vĩnh viễn thông báo này?')">
                                                        <i class="ti ti-trash"></i> Xóa vv
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">Không có thông báo nào đã xóa.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $notifications->links() }}
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.notify.index') }}" class="btn btn-secondary">Quay lại thông báo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection
