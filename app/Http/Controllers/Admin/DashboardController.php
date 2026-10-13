<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | 1. KPI
        |--------------------------------------------------------------------------
        */

        $customerCount = User::query()
            ->where('role', 'customer')
            ->count();

        $productCount = Product::query()
            ->count();

        $orderCount = Order::query()
            ->count();

        /*
        | Doanh thu:
        | Không tính các đơn đã hủy.
        */
        $revenue = Order::query()
            ->where('order_status', '!=', 'cancelled')
            ->sum('total_amount');


        /*
        |--------------------------------------------------------------------------
        | 2. ĐƠN ĐANG XỬ LÝ
        |--------------------------------------------------------------------------
        */

        $processingOrders = Order::query()
            ->whereIn('order_status', [
                'pending',
                'processing',
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 3. ĐƠN HÀNG THEO TRẠNG THÁI
        |--------------------------------------------------------------------------
        */

        $orderStatuses = [
            'pending' => Order::query()
                ->where('order_status', 'pending')
                ->count(),

            'processing' => Order::query()
                ->where('order_status', 'processing')
                ->count(),

            'shipping' => Order::query()
                ->where('order_status', 'shipping')
                ->count(),

            'delivered' => Order::query()
                ->where('order_status', 'delivered')
                ->count(),

            'cancelled' => Order::query()
                ->where('order_status', 'cancelled')
                ->count(),
        ];


        /*
        |--------------------------------------------------------------------------
        | 4. DOANH THU 7 NGÀY GẦN NHẤT
        |--------------------------------------------------------------------------
        */

        $revenueLabels = [];
        $revenueData = [];

        for ($i = 6; $i >= 0; $i--) {

            $date = Carbon::today()->subDays($i);

            $revenueLabels[] = $date->format('d/m');

            $dailyRevenue = Order::query()
                ->whereDate('created_at', $date)
                ->where('order_status', '!=', 'cancelled')
                ->sum('total_amount');

            $revenueData[] = (float) $dailyRevenue;
        }


        /*
        |--------------------------------------------------------------------------
        | 5. ĐƠN HÀNG GẦN ĐÂY
        |--------------------------------------------------------------------------
        */

        $recentOrders = Order::query()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 6. SẢN PHẨM BÁN CHẠY
        |--------------------------------------------------------------------------
        |
        | Dùng order_items vì đây là dữ liệu snapshot
        | của sản phẩm tại thời điểm mua.
        |
        */

        $bestSellingProducts = DB::table('order_items')
            ->join(
                'orders',
                'orders.id',
                '=',
                'order_items.order_id'
            )
            ->select(
                'order_items.product_name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.subtotal) as total_revenue')
            )
            ->where(
                'orders.order_status',
                '!=',
                'cancelled'
            )
            ->groupBy(
                'order_items.product_name'
            )
            ->orderByDesc(
                'total_quantity'
            )
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | 7. KHÁCH HÀNG MỚI TRONG 7 NGÀY
        |--------------------------------------------------------------------------
        */

        $newCustomers = User::query()
            ->where('role', 'customer')
            ->where('created_at', '>=', Carbon::today()->subDays(6))
            ->count();


        /*
        |--------------------------------------------------------------------------
        | 8. TRẢ VIEW
        |--------------------------------------------------------------------------
        */

        return view('admin.dashboard', compact(
            'customerCount',
            'productCount',
            'orderCount',
            'revenue',
            'processingOrders',
            'orderStatuses',
            'revenueLabels',
            'revenueData',
            'recentOrders',
            'bestSellingProducts',
            'newCustomers'
        ));
    }
}