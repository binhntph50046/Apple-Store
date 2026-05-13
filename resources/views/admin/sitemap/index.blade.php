@extends('admin.layouts.app')

@section('title', 'Quản lý Sitemap')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Quản lý Sitemap</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item">Sitemap</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Quản lý Sitemap</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    <h4 class="alert-heading">Thông tin về Sitemap</h4>
                                    <p>Sitemap là file XML chứa danh sách các URL của website. File này giúp các công cụ tìm kiếm như Google hiểu cấu trúc của website và thu thập dữ liệu hiệu quả hơn.</p>
                                    <hr>
                                    <p class="mb-0">Nhấn nút "Tạo Sitemap" để cập nhật file sitemap.xml với các đường dẫn mới nhất của website.</p>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <form action="{{ route('admin.sitemap.generate') }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-primary">
                                                <i class="ti ti-refresh me-1"></i> Tạo Sitemap
                                            </button>
                                        </form>
                                    </div>
                                    <div>
                                        <a href="{{ url('sitemap.xml') }}" target="_blank" class="btn btn-info">
                                            <i class="ti ti-eye me-1"></i> Xem Sitemap
                                        </a>
                                    </div>
                                </div>

                                @if(File::exists(public_path('sitemap.xml')))
                                    <div class="alert alert-success" role="alert">
                                        <strong>Sitemap đã được tạo!</strong><br>
                                        Đường dẫn: <a href="{{ url('sitemap.xml') }}" target="_blank">{{ url('sitemap.xml') }}</a><br>
                                        Dung lượng: {{ number_format(File::size(public_path('sitemap.xml')) / 1024, 2) }} KB<br>
                                        Cập nhật lần cuối: {{ date('d/m/Y H:i:s', File::lastModified(public_path('sitemap.xml'))) }}
                                    </div>
                                @else
                                    <div class="alert alert-warning" role="alert">
                                        Chưa có file sitemap.xml. Vui lòng nhấn nút "Tạo Sitemap" để tạo file.
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection 