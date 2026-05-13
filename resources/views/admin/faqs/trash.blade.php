<!-- resources/views/admin/faqs/trash.blade.php -->
@extends('admin.layouts.app')
@section('title', 'Câu hỏi đã xóa')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }
    
    .answer-preview {
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
                            <h5 class="m-b-10">Câu hỏi đã xóa</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.faqs.index') }}">Câu hỏi thường gặp</a></li>
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
                        <h5>Danh sách câu hỏi đã xóa</h5>
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
                                        <th>STT</th>
                                        <th>Câu hỏi</th>
                                        <th>Câu trả lời</th>
                                        <th>Thời điểm xóa</th>
                                        <th class="text-center">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($faqs as $faq)
                                    <tr>
                                        <td>{{ $faq->id }}</td>
                                        <td>{{ $faq->question }}</td>
                                        <td class="answer-preview" title="{{ $faq->answer }}">
                                            {{ Str::limit($faq->answer, 50) }}
                                        </td>
                                        <td>{{ $faq->deleted_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-center">
                                            <form action="{{ route('admin.faqs.restore', $faq->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm rounded-3 me-2" onclick="return confirm('Bạn có chắc muốn khôi phục FAQ này không?')">
                                                    <i class="ti ti-restore"></i> Khôi phục
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.faqs.forceDelete', $faq->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm rounded-3" onclick="return confirm('Bạn có chắc muốn xóa vĩnh viễn FAQ này không?')">
                                                    <i class="ti ti-trash"></i> Xóa vĩnh viễn
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Không tìm thấy câu hỏi đã xóa nào.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $faqs->links() }}
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.faqs.index') }}" class="btn btn-secondary">Quay lại danh sách câu hỏi</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection