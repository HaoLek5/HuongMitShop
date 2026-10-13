@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $order->order_code }}
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Đặt ngày
                {{ $order->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <a
            href="{{ route('admin.orders.index') }}"
            class="px-4 py-2 rounded-lg border hover:bg-gray-50"
        >
            ← Quay lại
        </a>

    </div>

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-lg bg-red-50 text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ================================================= --}}
        {{-- THÔNG TIN ĐƠN --}}
        {{-- ================================================= --}}

        <div class="lg:col-span-2 space-y-6">

            {{-- Người nhận --}}
            <div class="bg-white rounded-xl border shadow-sm p-6">

                <h2 class="font-semibold text-gray-800 mb-5">
                    Thông tin giao hàng
                </h2>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between gap-6">
                        <span class="text-gray-500">
                            Người nhận
                        </span>

                        <span class="font-medium text-right">
                            {{ $order->receiver_name }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-6">
                        <span class="text-gray-500">
                            Số điện thoại
                        </span>

                        <span class="font-medium text-right">
                            {{ $order->receiver_phone }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-6">
                        <span class="text-gray-500">
                            Địa chỉ
                        </span>

                        <span class="font-medium text-right max-w-md">
                            {{ $order->shipping_address }}
                        </span>
                    </div>

                    <div class="flex justify-between gap-6">
                        <span class="text-gray-500">
                            Người đặt
                        </span>

                        <span class="font-medium text-right">
                            {{ $order->user->name ?? '—' }}
                        </span>
                    </div>

                </div>

            </div>

            {{-- Danh sách sản phẩm --}}
            <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

                <div class="px-6 py-4 border-b">
                    <h2 class="font-semibold text-gray-800">
                        Sản phẩm
                    </h2>
                </div>

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b">

                        <tr>
                            <th class="text-left px-6 py-4">
                                Sản phẩm
                            </th>

                            <th class="text-left px-6 py-4">
                                SKU
                            </th>

                            <th class="text-left px-6 py-4">
                                Biến thể
                            </th>

                            <th class="text-center px-6 py-4">
                                SL
                            </th>

                            <th class="text-right px-6 py-4">
                                Đơn giá
                            </th>

                            <th class="text-right px-6 py-4">
                                Thành tiền
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        @foreach($order->items as $item)

                            <tr>

                                <td class="px-6 py-4 font-medium">
                                    {{ $item->product_name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item->sku }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item->variant_name ?: '—' }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    {{ $item->quantity }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    {{ number_format($item->price, 0, ',', '.') }}
                                    ₫
                                </td>

                                <td class="px-6 py-4 text-right font-medium">
                                    {{ number_format($item->subtotal, 0, ',', '.') }}
                                    ₫
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ================================================= --}}
        {{-- CỘT PHẢI --}}
        {{-- ================================================= --}}

        <div class="space-y-6">

            {{-- Trạng thái đơn --}}
            <div class="bg-white rounded-xl border shadow-sm p-6">

                <h2 class="font-semibold text-gray-800 mb-5">
                    Trạng thái đơn hàng
                </h2>

                @if($order->order_status !== 'delivered'
                    && $order->order_status !== 'cancelled')

                    <form
                        method="POST"
                        action="{{ route('admin.orders.update-status', $order) }}"
                        class="space-y-4"
                    >

                        @csrf
                        @method('PATCH')

                        <select
                            name="status"
                            class="w-full border-gray-300 rounded-lg"
                        >

                            <option
                                value="pending"
                                {{ $order->order_status === 'pending' ? 'selected' : '' }}
                            >
                                Chờ xác nhận
                            </option>

                            <option
                                value="confirmed"
                                {{ $order->order_status === 'confirmed' ? 'selected' : '' }}
                            >
                                Đã xác nhận
                            </option>

                            <option
                                value="processing"
                                {{ $order->order_status === 'processing' ? 'selected' : '' }}
                            >
                                Đang xử lý
                            </option>

                            <option
                                value="shipping"
                                {{ $order->order_status === 'shipping' ? 'selected' : '' }}
                            >
                                Đang giao
                            </option>

                            <option
                                value="delivered"
                                {{ $order->order_status === 'delivered' ? 'selected' : '' }}
                            >
                                Đã giao
                            </option>

                            <option
                                value="cancelled"
                                {{ $order->order_status === 'cancelled' ? 'selected' : '' }}
                            >
                                Hủy đơn
                            </option>

                        </select>

                        <button
                            type="submit"
                            class="w-full px-4 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white"
                        >
                            Cập nhật trạng thái
                        </button>

                    </form>

                @else

                    <div class="p-4 rounded-lg bg-gray-50 text-gray-600">
                        Đơn hàng đã kết thúc và không thể thay đổi trạng thái.
                    </div>

                @endif

            </div>


            {{-- Tổng tiền --}}
            <div class="bg-white rounded-xl border shadow-sm p-6">

                <h2 class="font-semibold text-gray-800 mb-5">
                    Thanh toán
                </h2>

                <div class="space-y-3 text-sm">

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Tiền hàng
                        </span>

                        <span>
                            {{ number_format($order->subtotal, 0, ',', '.') }}
                            ₫
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Giảm giá
                        </span>

                        <span>
                            {{ number_format($order->discount_amount, 0, ',', '.') }}
                            ₫
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Phí ship
                        </span>

                        <span>
                            {{ number_format($order->shipping_fee, 0, ',', '.') }}
                            ₫
                        </span>
                    </div>

                    <div class="pt-3 border-t flex justify-between text-lg">
                        <span class="font-semibold">
                            Tổng cộng
                        </span>

                        <span class="font-bold text-green-600">
                            {{ number_format($order->total_amount, 0, ',', '.') }}
                            ₫
                        </span>
                    </div>

                    @if($order->deposit_required)

                        <div class="pt-3 border-t">

                            <div class="flex justify-between">
                                <span class="text-gray-500">
                                    Tiền cọc
                                </span>

                                <span class="font-semibold text-orange-600">
                                    {{ number_format($order->deposit_amount, 0, ',', '.') }}
                                    ₫
                                </span>
                            </div>

                            <div class="flex justify-between mt-2">
                                <span class="text-gray-500">
                                    Đã thanh toán
                                </span>

                                <span>
                                    {{ number_format($order->paid_amount, 0, ',', '.') }}
                                    ₫
                                </span>
                            </div>

                            <div class="flex justify-between mt-2">
                                <span class="text-gray-500">
                                    Còn lại
                                </span>

                                <span class="font-semibold">
                                    {{ number_format($order->remaining_amount, 0, ',', '.') }}
                                    ₫
                                </span>
                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Payment --}}
            <div class="bg-white rounded-xl border shadow-sm p-6">

                <h2 class="font-semibold text-gray-800 mb-5">
                    Lịch sử thanh toán
                </h2>

                <div class="space-y-4">

                    @forelse($order->payments as $payment)

                        <div class="p-4 rounded-lg bg-gray-50">

                            <div class="flex justify-between">

                                <span class="font-medium">
                                    {{ $payment->payment_type === 'deposit'
                                        ? 'Tiền cọc'
                                        : 'Thanh toán cuối' }}
                                </span>

                                <span class="font-semibold">
                                    {{ number_format($payment->amount, 0, ',', '.') }}
                                    ₫
                                </span>

                            </div>

                            <div class="text-sm text-gray-500 mt-1">
                                {{ $payment->payment_method === 'cod'
                                    ? 'COD'
                                    : 'Chuyển khoản' }}
                            </div>

                            <div class="text-xs text-gray-400 mt-2">
                                Trạng thái:
                                {{ $payment->status }}
                            </div>

                        </div>

                    @empty

                        <p class="text-sm text-gray-500">
                            Chưa có thanh toán.
                        </p>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection