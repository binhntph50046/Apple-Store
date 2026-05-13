@extends('admin.layouts.app')
@section('title', 'Danh sách thống báo')

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
                            <h5 class="m-b-10">Thông báo</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item" aria-current="page">Thông báo</li>
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
                        <h5>Danh sách thông báo</h5>
                        <div class="card-header-right">
                        <a href="{{ route('admin.notify.trash') }}" class="btn btn-danger btn-sm rounded-3">
                            <i class="ti ti-trash"></i>Thùng rác
                        </a>
                        </div>
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
                                        <th>Tin nhắn</th>
                                        <th>Trạng thái</th>
                                        <th>Ngày tạo</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($notifications as $noti)
                                        <tr>
                                            <td>{{ $noti->data['title'] ?? '-' }}</td>
                                            <td class="message-preview" title="{{ $noti->data['message'] ?? '' }}">
                                                {{ Str::limit($noti->data['message'] ?? '', 50) }}
                                            </td>
                                            <td>
                                                @if($noti->read_at)
                                                    <span class="badge bg-success">Đã Đọc</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">Mới</span>
                                                @endif
                                            </td>
                                            <td>{{ $noti->created_at->format('d/m/Y H:i') }}</td>
                                            <td class="text-center">
    @if(isset($noti->data['url']))
        <a href="{{ $noti->data['url'] }}" 
           class="btn btn-success btn-sm rounded-3 me-2" 
           title="Xem thông báo">
            <i class="ti ti-eye"></i>
        </a>
    @endif

    @if(!$noti->read_at)
        <form action="{{ route('admin.notify.markAsRead', $noti->id) }}" 
              method="POST" 
              class="d-inline me-2">
            @csrf
            <button type="submit" 
                    class="btn btn-primary btn-sm rounded-3" 
                    title="Đánh dấu đã đọc">
                <i class="ti ti-check"></i>
            </button>
        </form>
    @endif

    <form action="{{ route('admin.notify.destroy', $noti->id) }}" 
          method="POST" 
          class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" 
                class="btn btn-danger btn-sm rounded-3" 
                onclick="return confirm('Bạn có chắc muốn xóa thông báo này?')" 
                title="Xóa thông báo">
            <i class="ti ti-trash"></i>
        </button>
    </form>
</td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Không có thông báo nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $notifications->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection
