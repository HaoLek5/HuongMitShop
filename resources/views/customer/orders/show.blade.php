@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng')

@section('content')

<div class="min-h-screen bg-[#F8FAFC]">

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- =========================================================
             BACK
        ========================================================== --}}

        <a
            href="{{ route('customer.orders.index') }}"
            class="inline-flex items-center text-sm font-medium text-[#16A34A] hover:underline"
        >
            ← Đơn hàng của tôi
        </a>


        {{-- =========================================================
             HEADER
        ========================================================== --}}

        <div class="mt-5 flex flex-col gap-4 rounded-xl border border-[#E2E8F0] bg-white px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-xl font-bold text-[#0F172A]">
                    Đơn hàng {{ $order->order_code }}
                </h1>

                <p class="mt-1 text-sm text-slate-400">
                    Đặt ngày {{ $order->created_at->format('d/m/Y H:i') }}
                </p>

            </div>


            @php

                $statusText = match ($order->order_status) {

                    'pending' =>
                        'Chờ xử lý',

                    'confirmed' =>
                        'Đã xác nhận',

                    'processing' =>
                        'Đang xử lý',

                    'shipping' =>
                        'Đang giao',

                    'delivered' =>
                        'Đã giao',

                    'cancelled' =>
                        'Đã hủy',

                    default =>
                        $order->order_status,

                };


                $statusClass = match ($order->order_status) {

                    'pending' =>
                        'bg-amber-50 text-amber-700 border-amber-200',

                    'confirmed' =>
                        'bg-blue-50 text-blue-700 border-blue-200',

                    'processing' =>
                        'bg-indigo-50 text-indigo-700 border-indigo-200',

                    'shipping' =>
                        'bg-purple-50 text-purple-700 border-purple-200',

                    'delivered' =>
                        'bg-green-50 text-green-700 border-green-200',

                    'cancelled' =>
                        'bg-red-50 text-red-700 border-red-200',

                    default =>
                        'bg-slate-50 text-slate-600 border-slate-200',

                };

            @endphp


            <span
                class="inline-flex w-fit rounded-full border px-4 py-2 text-sm font-semibold {{ $statusClass }}"
            >
                {{ $statusText }}
            </span>

        </div>


        {{-- =========================================================
             THÔNG TIN GIAO HÀNG
        ========================================================== --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white">

            <div class="border-b border-[#E2E8F0] px-6 py-5">

                <h2 class="font-semibold text-slate-800">
                    Thông tin nhận hàng
                </h2>

            </div>


            <div class="grid grid-cols-1 gap-6 px-6 py-5 md:grid-cols-2">

                <div>

                    <div class="text-sm text-slate-400">
                        Người nhận
                    </div>

                    <div class="mt-1 font-medium text-slate-800">
                        {{ $order->receiver_name }}
                    </div>

                </div>


                <div>

                    <div class="text-sm text-slate-400">
                        Số điện thoại
                    </div>

                    <div class="mt-1 font-medium text-slate-800">
                        {{ $order->receiver_phone }}
                    </div>

                </div>


                <div class="md:col-span-2">

                    <div class="text-sm text-slate-400">
                        Địa chỉ giao hàng
                    </div>

                    <div class="mt-1 leading-6 text-slate-800">
                        {{ $order->shipping_address }}
                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             SẢN PHẨM
        ========================================================== --}}

        <div class="mt-6 overflow-hidden rounded-xl border border-[#E2E8F0] bg-white">

            <div class="border-b border-[#E2E8F0] px-6 py-5">

                <h2 class="font-semibold text-slate-800">
                    Sản phẩm
                </h2>

            </div>


            <div class="divide-y divide-[#E2E8F0]">

                @foreach($order->items as $item)

                    <div class="flex flex-col gap-4 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                        <div class="min-w-0">

                            <div class="font-medium text-slate-800">
                                {{ $item->product_name }}
                            </div>


                            @if($item->variant_name)

                                <div class="mt-1 text-sm text-slate-500">
                                    Phân loại: {{ $item->variant_name }}
                                </div>

                            @endif


                            <div class="mt-1 text-xs text-slate-400">
                                SKU: {{ $item->sku }}
                            </div>

                        </div>


                        <div class="flex items-center justify-between gap-8 sm:justify-end">

                            <div class="text-sm text-slate-500">
                                x{{ $item->quantity }}
                            </div>


                            <div class="text-right">

                                <div class="text-sm text-slate-400">
                                    Đơn giá
                                </div>

                                <div class="mt-1 font-medium text-slate-700">
                                    {{ number_format($item->price, 0, ',', '.') }} ₫
                                </div>

                            </div>


                            <div class="text-right">

                                <div class="text-sm text-slate-400">
                                    Thành tiền
                                </div>

                                <div class="mt-1 font-semibold text-slate-800">
                                    {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        {{-- =========================================================
             THANH TOÁN
        ========================================================== --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white">

            <div class="border-b border-[#E2E8F0] px-6 py-5">

                <h2 class="font-semibold text-slate-800">
                    Thanh toán
                </h2>

            </div>


            <div class="px-6 py-5">

                <div class="space-y-3 text-sm">

                    {{-- Tạm tính --}}

                    <div class="flex items-center justify-between">

                        <span class="text-slate-500">
                            Tạm tính
                        </span>

                        <span class="font-medium text-slate-700">
                            {{ number_format($order->subtotal, 0, ',', '.') }} ₫
                        </span>

                    </div>


                    {{-- Giảm giá --}}

                    @if($order->discount_amount > 0)

                        <div class="flex items-center justify-between">

                            <span class="text-slate-500">
                                Giảm giá
                            </span>

                            <span class="font-medium text-green-600">
                                -{{ number_format($order->discount_amount, 0, ',', '.') }} ₫
                            </span>

                        </div>

                    @endif


                    {{-- Phí giao hàng --}}

                    <div class="flex items-center justify-between">

                        <span class="text-slate-500">
                            Phí vận chuyển
                        </span>

                        <span class="font-medium text-slate-700">
                            {{ number_format($order->shipping_fee, 0, ',', '.') }} ₫
                        </span>

                    </div>


                    {{-- Tổng --}}

                    <div class="border-t border-[#E2E8F0] pt-4">

                        <div class="flex items-center justify-between">

                            <span class="font-semibold text-slate-800">
                                Tổng cộng
                            </span>

                            <span class="text-xl font-bold text-[#16A34A]">
                                {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                            </span>

                        </div>

                    </div>

                </div>


                {{-- PAYMENT INFO --}}

                <div class="mt-6 rounded-xl bg-[#F8FAFC] px-4 py-4">

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

                        <div>

                            <div class="text-xs text-slate-400">
                                Phương thức
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">

                                @if($order->payment_method === 'cod')
                                    Thanh toán COD
                                @else
                                    Chuyển khoản ngân hàng
                                @endif

                            </div>

                        </div>


                        <div>

                            <div class="text-xs text-slate-400">
                                Trạng thái thanh toán
                            </div>

                            <div class="mt-1 text-sm font-medium text-slate-700">

                                @switch($order->payment_status)

                                    @case('unpaid')
                                        Chưa thanh toán
                                        @break

                                    @case('deposit_pending')
                                        Chờ thanh toán cọc
                                        @break

                                    @case('deposit_paid')
                                        Đã thanh toán cọc
                                        @break

                                    @case('paid')
                                        Đã thanh toán
                                        @break

                                    @case('refunded')
                                        Đã hoàn tiền
                                        @break

                                    @default
                                        {{ $order->payment_status }}

                                @endswitch

                            </div>

                        </div>


                        @if($order->deposit_required)

                            <div>

                                <div class="text-xs text-slate-400">
                                    Tiền cọc
                                </div>

                                <div class="mt-1 text-sm font-medium text-slate-700">
                                    {{ number_format($order->deposit_amount, 0, ',', '.') }} ₫
                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================================================
             GHI CHÚ
        ========================================================== --}}

        @if($order->note)

            <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white px-6 py-5">

                <h2 class="font-semibold text-slate-800">
                    Ghi chú
                </h2>

                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                    {{ $order->note }}
                </p>

            </div>

        @endif

    </div>

</div>

@endsection