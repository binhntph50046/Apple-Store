@extends('admin.layouts.app')
@section('title', 'Trang quản trị')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Thống kê</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Thống kê</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->
            <!-- [ Main Content ] start -->
            <div class="row">
                {{-- Hàm bỏ .0% khi thừa ví dụ: 100.0% -> 100% --}}
                @php
                    if (!function_exists('formatPercent')) {
                        function formatPercent($value)
                        {
                            return fmod($value, 1) == 0
                                ? number_format($value, 0) . '%'
                                : number_format($value, 1) . '%';
                        }
                    }
                @endphp
                <!-- [ sample-page ] start -->
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Tổng lượt xem sản phẩm</h6>
                            <h4 class="mb-3">
                                {{ number_format($totalProductViews) }}
                                <span
                                    class="badge bg-light-{{ $productViewsChange >= 0 ? 'success' : 'danger' }} border border-{{ $productViewsChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="ti ti-trending-{{ $productViewsChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ formatPercent(abs($productViewsChange)) }}
                                </span>
                            </h4>
                            <p class="mb-0 text-muted text-sm">
                                {{ $productViewsChange >= 0 ? 'Bạn có thêm' : 'Bạn bị giảm' }}
                                <span class="text-{{ $productViewsChange >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format(abs($totalProductViews - $lastYearProductViews)) }}
                                </span>
                                lượt xem, chiếm
                                <span class="text-{{ $productViewsChange >= 0 ? 'success' : 'danger' }}">
                                    {{ formatPercent(abs($productViewsChange)) }}
                                </span>
                                tổng lượt xem năm nay
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Tổng người dùng</h6>
                            <h4 class="mb-3">
                                {{ number_format($totalUsers) }}
                                <span
                                    class="badge bg-light-{{ $usersChange >= 0 ? 'success' : 'danger' }} border border-{{ $usersChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="ti ti-trending-{{ $usersChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ formatPercent(abs($usersChange)) }}
                                </span>
                            </h4>
                            <p class="mb-0 text-muted text-sm">
                                {{ $usersChange >= 0 ? 'Bạn có thêm' : 'Bạn bị giảm' }}
                                <span class="text-{{ $usersChange >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format(abs($totalUsers - $lastYearUsers)) }}
                                </span>
                                người dùng, chiếm
                                <span class="text-{{ $usersChange >= 0 ? 'success' : 'danger' }}">
                                    {{ formatPercent(abs($usersChange)) }}
                                </span>
                                tổng số người dùng năm nay
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Tổng đơn hàng</h6>
                            <h4 class="mb-3">
                                {{ number_format($totalOrders) }}
                                <span
                                    class="badge bg-light-{{ $ordersChange >= 0 ? 'success' : 'danger' }} border border-{{ $ordersChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="ti ti-trending-{{ $ordersChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ formatPercent(abs($ordersChange)) }}
                                </span>
                            </h4>
                            <p class="mb-0 text-muted text-sm">
                                {{ $ordersChange >= 0 ? 'Bạn có thêm' : 'Bạn bị giảm' }}
                                <span class="text-{{ $ordersChange >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format(abs($totalOrders - $lastYearOrders)) }}
                                </span>
                                đơn hàng, chiếm
                                <span class="text-{{ $ordersChange >= 0 ? 'success' : 'danger' }}">
                                    {{ formatPercent(abs($ordersChange)) }}
                                </span>
                                tổng số đơn hàng năm nay
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="mb-2 f-w-400 text-muted">Tổng doanh thu</h6>
                            <h4 class="mb-3">
                                {{ number_format($totalSales, 0, ',', '.') }}đ
                                <span
                                    class="badge bg-light-{{ $salesChange >= 0 ? 'success' : 'danger' }} border border-{{ $salesChange >= 0 ? 'success' : 'danger' }}">
                                    <i class="ti ti-trending-{{ $salesChange >= 0 ? 'up' : 'down' }}"></i>
                                    {{ formatPercent(abs($salesChange)) }}
                                </span>
                            </h4>
                            <p class="mb-0 text-muted text-sm">
                                {{ $salesChange >= 0 ? 'Bạn có thêm' : 'Bạn bị giảm' }}
                                <span class="text-{{ $salesChange >= 0 ? 'success' : 'danger' }}">
                                    {{ number_format(abs($totalSales - $lastYearSales), 0, ',', '.') }}đ
                                </span>
                                doanh thu, chiếm
                                <span class="text-{{ $salesChange >= 0 ? 'success' : 'danger' }}">
                                    {{ formatPercent(abs($salesChange)) }}
                                </span>
                                tổng doanh thu năm nay
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-7">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Khách truy cập</h5>
                        <ul class="nav nav-pills justify-content-end mb-0" id="chart-tab-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="chart-tab-home-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-home" type="button" role="tab"
                                    aria-controls="chart-tab-home" aria-selected="true">Tháng</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="chart-tab-profile-tab" data-bs-toggle="pill"
                                    data-bs-target="#chart-tab-profile" type="button" role="tab"
                                    aria-controls="chart-tab-profile" aria-selected="false">Tuần</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content" id="chart-tab-tabContent">
                                <div class="tab-pane" id="chart-tab-home" role="tabpanel"
                                    aria-labelledby="chart-tab-home-tab" tabindex="0">
                                    <div id="visitor-chart-1" data-month-visitors='@json($monthlyVisitors)'></div>
                                </div>
                                <div class="tab-pane show active" id="chart-tab-profile" role="tabpanel"
                                    aria-labelledby="chart-tab-profile-tab" tabindex="0">
                                    <div id="visitor-chart" data-week-visitors='@json($weeklyVisitors)'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-5">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-3">Tổng quan về thu nhập</h5>
                        <ul class="nav nav-pills justify-content-end mb-0" id="income-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="income-month-tab" data-bs-toggle="pill"
                                    data-bs-target="#income-month" type="button" role="tab"
                                    aria-controls="income-month" aria-selected="false">Tháng</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="income-week-tab" data-bs-toggle="pill"
                                    data-bs-target="#income-week" type="button" role="tab"
                                    aria-controls="income-week" aria-selected="true">Tuần</button>
                            </li>
                        </ul>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content" id="income-tab-content">
                                <div class="tab-pane fade" id="income-month" role="tabpanel"
                                    aria-labelledby="income-month-tab">
                                    <h6 class="mb-2 f-w-400 text-muted">Thống kê tháng này</h6>
                                    <h3 class="mb-3">{{ number_format($monthlyTotalIncome, 0, ',', '.') }} đ</h3>
                                    <div id="income-chart-month" data-monthly-income='@json($monthlyIncome)'></div>
                                </div>
                                <div class="tab-pane fade show active" id="income-week" role="tabpanel"
                                    aria-labelledby="income-week-tab">
                                    <h6 class="mb-2 f-w-400 text-muted">Thống kê tuần này</h6>
                                    <h3 class="mb-3">{{ number_format($weeklyTotalIncome, 0, ',', '.') }} đ</h3>
                                    <div id="income-chart-week" data-weekly-income='@json($weeklyIncome)'></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-7">
                    <h5 class="mb-3">Đơn hàng gần đây</h5>
                    <div class="card tbl-card">
                        <div class="card-body">
                            <div class="table-responsive" style="max-height: 390px; overflow-y: auto;">
                                <table class="table table-hover table-borderless mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID.</th>
                                            <th>SẢN PHẨM</th>
                                            <th>SỐ LƯỢNG</th>
                                            <th>TRẠNG THÁI</th>
                                            <th class="text-end">TỔNG TIỀN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                                        class="text-muted">
                                                        <strong>#{{ $order->id }}</strong>
                                                    </a>
                                                </td>

                                                <td>
                                                    @foreach ($order->items as $item)
                                                        {{ $item->product->name ?? 'N/A' }} (x{{ $item->quantity }})<br>
                                                    @endforeach
                                                </td>

                                                <td>
                                                    {{ $order->items->sum('quantity') }}
                                                </td>

                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'text-secondary',
                                                            'confirmed' => 'text-primary',
                                                            'preparing' => 'text-warning',
                                                            'shipping' => 'text-info',
                                                            'completed' => 'text-success',
                                                            'cancelled' => 'text-danger',
                                                            'returned' => 'text-muted',
                                                            'partially_returned' => 'text-dark',
                                                        ];

                                                        $statusLabels = [
                                                            'pending' => 'Chờ xử lý',
                                                            'confirmed' => 'Đã xác nhận',
                                                            'preparing' => 'Đang chuẩn bị',
                                                            'shipping' => 'Đang giao hàng',
                                                            'completed' => 'Hoàn thành',
                                                            'cancelled' => 'Đã hủy',
                                                            'returned' => 'Đã trả hàng',
                                                            'partially_returned' => 'Trả hàng một phần',
                                                        ];

                                                        $status = $order->status;
                                                        $colorClass = $statusColors[$status] ?? 'text-muted';
                                                        $label = $statusLabels[$status] ?? 'Không rõ';
                                                    @endphp
                                                    <span class="d-flex align-items-center gap-2">
                                                        <i class="fas fa-circle f-10 m-r-5 {{ $colorClass }}"></i>
                                                        {{ $label }}
                                                    </span>
                                                </td>

                                                <td class="text-end">{{ number_format($order->total_price) }} VNĐ</td>
                                            </tr>
                                        @endforeach
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-5">
                    <h5 class="mb-3">
                        Sản phẩm đã bán theo danh mục <small class="text-muted">({{ $monthLabel }})</small>
                    </h5>
                    <div class="card">
                        <div class="card-body px-2">
                            <div id="analytics-report-chart" data-category-labels='@json($categoryLabels)'
                                data-category-data='@json($categoryData)'>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-md-12 col-xl-8">
                    <h5 class="mb-3">Top 5 sản phẩm bán chạy nhất</h5>
                    <div class="card tbl-card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID.</th>
                                            <th>SẢN PHẨM</th>
                                            <th>SỐ LƯỢNG</th>
                                            <th class="text-end">TỔNG TIỀN</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($topVariants->count())
                                            @foreach ($topVariants as $item)
                                                <tr>
                                                    <td>{{ $item->variant->id ?? 'N/A' }}</td>
                                                    <td>
                                                        @if ($item->variant && $item->variant->product)
                                                            {{ $item->variant->product->name }} -
                                                            {{ $item->variant->name }}
                                                        @elseif ($item->product)
                                                            {{ $item->product->name }}
                                                        @else
                                                            Không rõ
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->total_quantity }}</td>
                                                    <td class="text-end">{{ number_format($item->total_revenue) }} VNĐ
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="text-center text-muted">Không có sản phẩm nào
                                                    được
                                                    bán chạy nhất</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-4">
                    <h5 class="mb-3">Sản phẩm sắp hết hàng</h5>
                    <div class="card">
                        <div class="list-group list-group-flush" style="max-height: 350px; overflow-y: auto">
                            @forelse ($lowStockProducts as $variant)
                                <div class="list-group-item d-flex flex-column">
                                    <strong>{{ $variant->product->name ?? 'Sản phẩm không xác định' }}</strong>
                                    <div class="d-flex justify-content-between mt-1">
                                        <span>Biến thể: {{ $variant->name ?? 'Không có' }}</span>
                                        <span class="badge bg-warning text-dark" style="height: 19px;">Tồn kho: {{ $variant->stock }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="list-group-item text-muted">Không có sản phẩm sắp hết hàng.</div>
                            @endforelse
                        </div>
                        <div class="card-body px-3">
                            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-primary w-25"
                                style="border-radius: 5px">Quản lý
                                kho</a>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 col-xl-12">
                    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap">
                        <h5 class="mb-3 mb-md-0">Số lượng sản phẩm đã bán ra</h5>

                        <div class="d-flex align-items-center gap-3">
                            {{-- Bộ lọc năm --}}
                            <form id="yearFilterForm" method="GET" class="d-flex align-items-center gap-2">
                                <label for="yearFilter" class="form-label mb-0">Năm:</label>
                                <select style="border-radius: 4px" name="year" id="yearFilter" class="form-select form-select-sm w-auto">
                                    <option value="all" {{ $selectedYear === 'all' ? 'selected' : '' }}>Tất cả</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}"
                                            {{ $year == $selectedYear ? 'selected' : '' }}>
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>


                            {{-- Tab tháng / năm --}}
                            <ul class="nav nav-pills" id="sold-tab" role="tablist">
                                <li class="nav-item">
                                    <button class="nav-link" id="sold-year-tab" data-bs-toggle="pill"
                                        data-bs-target="#sold-year">
                                        Theo năm
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link active" id="sold-month-tab" data-bs-toggle="pill"
                                        data-bs-target="#sold-month">
                                        Theo tháng
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content" id="sold-tab-content">
                                {{-- Biểu đồ tháng --}}
                                <div class="tab-pane fade show active" id="sold-month">
                                    <h6 class="mb-2 text-muted">
                                        {{ $selectedYear === 'all' ? 'So sánh các năm' : 'Thống kê năm ' . $selectedYear }}
                                    </h6>
                                    <div id="sold-chart-month" data-monthly-sold='@json($selectedYear === 'all' ? null : array_values($monthlySold))'
                                        data-monthly-by-year='@json($selectedYear === 'all' ? $monthlyByYear : null)'>
                                    </div>
                                </div>

                                {{-- Biểu đồ năm --}}
                                <div class="tab-pane fade" id="sold-year">
                                    <h6 class="mb-2 text-muted">Thống kê theo năm</h6>
                                    <div id="sold-chart-year" data-yearly-sold='@json($yearlySold)'>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
