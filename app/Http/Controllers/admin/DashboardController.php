<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PageView;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController
{
    public function index(Request $request)
    {
        // Tính tống
        $totalProductViews = Product::sum('views');
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalSales = Order::where('payment_status', 'paid')
            ->where('status', 'completed')
            ->sum('total_price');

        // Liệt kê so với năm trước tăng thì hiện xanh, giảm thì hiện đỏ
        $lastYearProductViews = Product::whereYear('created_at', now()->subYear()->year)->sum('views');
        $lastYearUsers = User::whereYear('created_at', now()->subYear()->year)->count();
        $lastYearOrders = Order::whereYear('created_at', now()->subYear()->year)->count();
        $lastYearSales = Order::where('payment_status', 'paid')
            ->where('status', 'completed')
            ->whereYear('created_at', now()->subYear()->year)
            ->sum('total_price');

        // Liệt kê theo năm hiện tại
        $productViewsChange = $totalProductViews > 0
            ? (floatval($totalProductViews - $lastYearProductViews) / floatval($totalProductViews)) * 100
            : 0;

        $usersChange = $totalUsers > 0
            ? (floatval($totalUsers - $lastYearUsers) / floatval($totalUsers)) * 100
            : 0;

        $ordersChange = $totalOrders > 0
            ? (floatval($totalOrders - $lastYearOrders) / floatval($totalOrders)) * 100
            : 0;

        $salesChange = $totalSales > 0
            ? (floatval($totalSales - $lastYearSales) / floatval($totalSales)) * 100
            : 0;


        // Đơn hàng gần đây
        $recentOrders = Order::with('items.product')->orderBy('created_at', 'desc')->take(10)->get();


        // Tạo mảng chứa tổng thu nhập từng ngày trong tuần
        $weeklyIncome = [];

        $startOfWeek = Carbon::now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dailyIncome = Order::whereDate('created_at', $date)
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total_price');

            $weeklyIncome[] = round($dailyIncome);
        }

        // Tổng thu nhập của cả tuần
        $weeklyTotalIncome = array_sum($weeklyIncome);

        // Tạo mảng chứa tổng thu nhập từng ngày trong tháng
        $monthlyIncome = [];
        $currentYear = Carbon::now()->year;

        for ($month = 1; $month <= 12; $month++) {
            $startOfMonth = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endOfMonth = $startOfMonth->copy()->endOfMonth();

            $income = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->where('status', 'completed')
                ->where('payment_status', 'paid')
                ->sum('total_price');

            $monthlyIncome[] = round($income);
        }

        // Tổng thu nhập cả tháng
        $monthlyTotalIncome = array_sum($monthlyIncome);

        // Weekly: 7 ngày gần nhất khách truy cập
        $startOfWeek = now()->startOfWeek();
        $weeklyVisitors = [
            'pageViews' => [],
            'sessions' => [],
            'users' => []
        ];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->toDateString();

            $views = PageView::whereDate('created_at', $date)
                ->whereHas('user.roles', fn($q) => $q->where('name', 'user'))
                ->count();

            $sessions = PageView::whereDate('created_at', $date)
                ->whereHas('user.roles', fn($q) => $q->where('name', 'user'))
                ->distinct('session_id')
                ->count('session_id');

            $users = PageView::whereDate('created_at', $date)
                ->whereHas('user.roles', fn($q) => $q->where('name', 'user'))
                ->distinct('user_id')
                ->count('user_id');

            $weeklyVisitors['pageViews'][] = $views;
            $weeklyVisitors['sessions'][] = $sessions;
            $weeklyVisitors['users'][] = $users;
        }

                // Monthly: 12 tháng khách truy cập
                $monthlyVisitors = [
            'pageViews' => [],
            'sessions' => [],
            'users' => []
        ];

        for ($i = 1; $i <= 12; $i++) {
            $views = PageView::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->whereHas('user.roles', fn($q) => $q->where('name', 'user'))
                ->count();

            $sessions = PageView::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->whereHas('user.roles', fn($q) => $q->where('name', 'user'))
                ->distinct('session_id')
                ->count('session_id');

            $users = PageView::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->whereHas('user.roles', fn($q) => $q->where('name', 'user'))
                ->distinct('user_id')
                ->count('user_id');

            $monthlyVisitors['pageViews'][] = $views;
            $monthlyVisitors['sessions'][] = $sessions;
            $monthlyVisitors['users'][] = $users;
        }

        // Top 5 variant bán chạy nhất
        $topVariants = OrderItem::with(['variant', 'product'])
            ->whereHas('variant', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('product', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->whereHas('order', function ($query) {
                $query->where('status', 'completed')
                    ->where('payment_status', 'paid');
            })
            ->select('product_variant_id', DB::raw('SUM(quantity) as total_quantity'), DB::raw('SUM(quantity * price) as total_revenue'))
            ->groupBy('product_variant_id')
            ->orderByDesc('total_quantity')
            ->take(5)
            ->get();

        // Sản phẩm sắp hết hàng dưới 5 sản phẩm
        $lowStockProducts = ProductVariant::with('product')
            ->where('stock', '<', 10)
            ->orderBy('stock', 'asc')
            ->take(10)
            ->get();

        // Lấy tháng/năm hiện tại
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Truy vấn số lượng sản phẩm đã bán theo danh mục trong tháng hiện tại
        $categorySales = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->select('categories.name as category_name', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->whereMonth('orders.created_at', $currentMonth)
            ->whereYear('orders.created_at', $currentYear)
            ->groupBy('categories.name')
            ->orderByDesc('total_sold')
            ->get();

        $categoryLabels = $categorySales->pluck('category_name')->toArray();
        $categoryData = $categorySales->pluck('total_sold')->toArray();

        // Truyền thêm tháng/năm để hiển thị
        $monthLabel = Carbon::now()->translatedFormat('F Y'); // Ví dụ: "Tháng Tám 2025"

        // So sánh sản phẩm đã bán ra giữa các tháng/năm 
        // Lấy các năm có đơn hàng (chỉ lấy đơn đã hoàn tất + đã thanh toán)
        $years = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->where('orders.payment_status', 'paid')
            ->selectRaw('YEAR(orders.created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        // Lấy năm được chọn hoặc mặc định
        $selectedYear = $request->input('year', now()->year);

        // Dữ liệu tháng
        if ($selectedYear !== 'all') {
            $monthlySold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.quantity) as total')
                ->whereYear('orders.created_at', $selectedYear)
                ->where('orders.status', 'completed')
                ->where('orders.payment_status', 'paid')
                ->groupBy('month')
                ->pluck('total', 'month')
                ->toArray();
            $monthlySold = array_replace(array_fill(1, 12, 0), $monthlySold);
            $monthlyByYear = null;
        } else {
            $monthlyByYear = [];
            foreach ($years as $year) {
                $data = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->selectRaw('MONTH(orders.created_at) as month, SUM(order_items.quantity) as total')
                    ->whereYear('orders.created_at', $year)
                    ->where('orders.status', 'completed')
                    ->where('orders.payment_status', 'paid')
                    ->groupBy('month')
                    ->pluck('total', 'month')
                    ->toArray();
                $monthlyByYear[$year] = array_values(array_replace(array_fill(1, 12, 0), $data));
            }
            $monthlySold = null;
        }

        // Dữ liệu năm
        $yearlySold = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->selectRaw('YEAR(orders.created_at) as year, SUM(order_items.quantity) as total')
            ->where('orders.status', 'completed')
            ->where('orders.payment_status', 'paid')
            ->groupBy('year')
            ->pluck('total', 'year')
            ->toArray();


        return view('admin.dashboard', compact(
            'totalProductViews',
            'totalUsers',
            'totalOrders',
            'totalSales',
            'productViewsChange',
            'usersChange',
            'ordersChange',
            'salesChange',
            'lastYearProductViews',
            'lastYearUsers',
            'lastYearOrders',
            'lastYearSales',
            'recentOrders',
            'weeklyIncome',
            'monthlyIncome',
            'weeklyTotalIncome',
            'monthlyTotalIncome',
            'weeklyVisitors',
            'monthlyVisitors',
            'topVariants',
            'lowStockProducts',
            'categoryLabels',
            'categoryData',
            'monthLabel',
            'monthlySold',
            'monthlyByYear',
            'yearlySold',
            'years',
            'selectedYear',

        ));
    }
}
