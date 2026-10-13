<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {
    }

    /**
     * Danh sách đơn hàng.
     */
    public function index(Request $request)
    {
        $query = Order::with('user');

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'order_code',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'receiver_name',
                    'like',
                    "%{$search}%"
                )
                ->orWhere(
                    'receiver_phone',
                    'like',
                    "%{$search}%"
                );

            });
        }

        if ($request->filled('status')) {

            $query->where(
                'order_status',
                $request->status
            );
        }

        $orders = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        /*
         * Đánh dấu đơn nào có thể đi đơn.
         */
        foreach ($orders as $order) {

            $order->can_be_processed =
                $this->orderService
                    ->canBeProcessed($order);
        }

        return view(
            'admin.orders.index',
            compact('orders')
        );
    }

    /**
     * Chi tiết đơn.
     */
    public function show(Order $order)
    {
        $order->load([
            'user',
            'address',
            'items',
            'payments',
        ]);

        $order->can_be_processed =
            $this->orderService
                ->canBeProcessed($order);

        return view(
            'admin.orders.show',
            compact('order')
        );
    }

    /**
     * In nhiều đơn.
     *
     * Đồng thời chuyển:
     *
     * pending -> processing
     */
    public function print(Request $request)
    {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:orders,id',
            ],
        ], [
            'ids.required' =>
                'Vui lòng chọn ít nhất một đơn hàng.',

            'ids.min' =>
                'Vui lòng chọn ít nhất một đơn hàng.',
        ]);

        try {

            $orders = $this->orderService->markAsProcessing(
                array_map(
                    'intval',
                    $validated['ids']
                ),
                (int) Auth::id()
            );

            $orderIds = collect($orders)
                ->pluck('id')
                ->values()
                ->all();

            return redirect()->route(
                'admin.orders.print-view',
                [
                    'ids' => $orderIds,
                ]
            );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with(
                    'error',
                    'Không thể đi đơn các đơn đã chọn.'
                );
        }
    }

    /**
     * Trang in.
     *
     * Chỉ hiển thị các đơn đã chuyển
     * sang processing.
     */
    public function printView(Request $request)
    {
        $validated = $request->validate([
            'ids' => [
                'required',
                'array',
                'min:1',
            ],

            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:orders,id',
            ],
        ]);

        $orders = Order::query()
            ->with([
                'user',
                'items',
                'payments',
            ])
            ->whereIn(
                'id',
                $validated['ids']
            )
            ->where(
                'order_status',
                'processing'
            )
            ->get();

        if (
            $orders->count() !==
            count($validated['ids'])
        ) {
            abort(404);
        }

        return view(
            'admin.orders.print',
            compact('orders')
        );
    }

    /**
     * Cập nhật trạng thái sau khi đi đơn.
     */
    public function updateStatus(
        Request $request,
        Order $order
    ) {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:processing,shipping,delivered,cancelled',
            ],
        ]);

        try {

            $this->orderService->updateStatus(
                $order,
                $validated['status'],
                (int) Auth::id()
            );

            return back()->with(
                'success',
                'Đã cập nhật trạng thái đơn hàng.'
            );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->with(
                    'error',
                    'Không thể cập nhật trạng thái.'
                );
        }
    }
}