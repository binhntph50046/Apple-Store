@extends('admin.layouts.app')
@section('title', 'Nhật ký hoạt động người dùng')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
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
                            <h5 class="m-b-10">Nhật ký hoạt động người dùng</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}">Nhật ký hoạt động</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Người dùng #{{ $userId }}</li>
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
                        <h5>Hoạt động của người dùng #{{ $userId }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table custom-shadow ">
                            <table class="table table-hover table-borderless">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        {{-- <th>Session ID</th> --}}
                                        <th>URL</th>
                                        <th>IP Address</th>
                                        {{-- <th>User Agent</th> --}}
                                        <th>Khoảng thời gian (s)</th>
                                        <th>Ngày tạo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($pageViews as $index => $view)
                                        <tr>
                                            <td>{{ $index+1 }}</td>
                                            {{-- <td>{{ $view->session_id }}</td> --}}
                                            <td style="max-width:200px;overflow:auto;">{{ $view->url }}</td>
                                            <td>{{ $view->ip_address }}</td>
                                            {{-- <td style="max-width:200px;overflow:auto;">{{ $view->user_agent }}</td> --}}
                                            <td>
                                                @if($view->duration)
                                                    @php
                                                        $hours = floor($view->duration / 3600);
                                                        $minutes = floor(($view->duration % 3600) / 60);
                                                        $seconds = $view->duration % 60;
                                                    @endphp
                                                    {{ sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $view->created_at }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Không có hoạt động nào của người dùng này.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $pageViews->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection
