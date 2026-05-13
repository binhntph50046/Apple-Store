@extends('admin.layouts.app')

@section('title', 'Quản lý Robots.txt')

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Quản lý Robots.txt</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item">Robots.txt</li>
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
                        <h5>Quản lý Robots.txt</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info" role="alert">
                                    <h4 class="alert-heading">Thông tin về Robots.txt</h4>
                                    <p>File robots.txt cho phép bạn kiểm soát cách các công cụ tìm kiếm truy cập và lập chỉ mục cho website của bạn.</p>
                                    <hr>
                                    <p class="mb-0">Một số quy tắc cơ bản:</p>
                                    <ul>
                                        <li><code>User-agent: *</code> - Áp dụng cho tất cả các bot</li>
                                        <li><code>Allow: /path/</code> - Cho phép bot truy cập đường dẫn</li>
                                        <li><code>Disallow: /path/</code> - Chặn bot truy cập đường dẫn</li>
                                        <li><code>Sitemap: URL</code> - Chỉ định vị trí file sitemap</li>
                                    </ul>
                                    <p class="mt-2 mb-0">Lưu ý khi nhập đường dẫn:</p>
                                    <ul class="mb-0">
                                        <li>Đường dẫn phải bắt đầu bằng dấu /</li>
                                        <li>Không được chứa khoảng trắng</li>
                                        <li>Chỉ được dùng các ký tự: chữ cái, số, dấu -, _, ., / và *</li>
                                    </ul>
                                </div>

                                <form action="{{ route('admin.robots.update') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <!-- User Agent Section -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">User Agent</label>
                                        <input type="text" name="user_agent" class="form-control @error('user_agent') is-invalid @enderror" value="{{ old('user_agent', $robotsData['user_agent'] ?? '*') }}" required>
                                        <small class="form-text text-muted">Mặc định là * (áp dụng cho tất cả các bot)</small>
                                        @error('user_agent')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Allow Rules -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Cho phép truy cập (Allow) - Mỗi đường dẫn một dòng</label>
                                        <textarea name="allow" rows="5" class="form-control font-monospace @error('allow') is-invalid @enderror">{{ old('allow', implode("\n", $robotsData['allow'] ?? [])) }}</textarea>
                                        <small class="form-text text-muted">Ví dụ: /wp-admin/admin-ajax.php</small>
                                        @error('allow')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Disallow Rules -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Chặn truy cập (Disallow) - Mỗi đường dẫn một dòng</label>
                                        <textarea name="disallow" rows="5" class="form-control font-monospace @error('disallow') is-invalid @enderror">{{ old('disallow', implode("\n", $robotsData['disallow'] ?? [])) }}</textarea>
                                        <small class="form-text text-muted">Ví dụ: /wp-admin/, /search/</small>
                                        @error('disallow')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Sitemap URL -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Sitemap URL</label>
                                        <input type="url" name="sitemap" class="form-control @error('sitemap') is-invalid @enderror" value="{{ old('sitemap', url('sitemap.xml')) }}" readonly>
                                        <small class="form-text text-muted">URL sitemap sẽ tự động cập nhật theo cấu hình của website</small>
                                        @error('sitemap')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="ti ti-device-floppy me-1"></i> Lưu thay đổi
                                        </button>
                                        <a href="{{ url('robots.txt') }}" target="_blank" class="btn btn-info ms-2">
                                            <i class="ti ti-eye me-1"></i> Xem Robots.txt
                                        </a>
                                    </div>
                                </form>
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

@section('styles')
<style>
    .font-monospace {
        font-family: monospace;
        font-size: 14px;
        line-height: 1.5;
    }
</style>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Add new Allow rule
    $('#add-allow').click(function() {
        const newRule = `
            <div class="input-group mb-2">
                <input type="text" name="allow[]" class="form-control" placeholder="/path/">
                <button type="button" class="btn btn-danger btn-sm delete-rule">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
        $('#allow-rules').append(newRule);
    });

    // Add new Disallow rule
    $('#add-disallow').click(function() {
        const newRule = `
            <div class="input-group mb-2">
                <input type="text" name="disallow[]" class="form-control" placeholder="/path/">
                <button type="button" class="btn btn-danger btn-sm delete-rule">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
        `;
        $('#disallow-rules').append(newRule);
    });

    // Delete rule
    $(document).on('click', '.delete-rule', function() {
        const parent = $(this).closest('.input-group');
        const container = parent.parent();
        
        // Only delete if there's more than one rule
        if (container.children().length > 1) {
            parent.remove();
        } else {
            // If it's the last one, just clear the input
            parent.find('input').val('');
        }
    });
});
</script>
@endsection 