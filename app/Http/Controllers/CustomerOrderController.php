<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class CustomerOrderController extends Controller
{
    /**
     * Danh sách đơn hàng của Customer.
     */
    public function index()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view(
            'customer.orders.index',
            compact('orders')
        );
    }


    /**
     * Chi tiết một đơn hàng của Customer.
     */
    public function show(Order $order)
    {
        /*
        |--------------------------------------------------------------------------
        | Chỉ cho phép Customer xem đơn của chính mình.
        |--------------------------------------------------------------------------
        */

        abort_unless(
            $order->user_id === Auth::id(),
            404
        );


        /*
        |--------------------------------------------------------------------------
        | Lấy các item của đơn.
        |--------------------------------------------------------------------------
        |
        | order_items là snapshot tại thời điểm mua.
        | Vì vậy giao diện lịch sử dùng dữ liệu từ order_items.
        |
        */

        $order->load('items');


        return view(
            'customer.orders.show',
            compact('order')
        );
    }
}