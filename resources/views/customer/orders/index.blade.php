@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')

<div class="min-h-screen bg-[#F8FAFC]">

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- =========================================================
             HEADER
        ========================================================== --}}

        <div class="mb-8">

            <h1 class="text-2xl font-bold tracking-tight text-[#0F172A]">
                Đơn hàng của tôi
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Theo dõi các đơn hàng bạn đã đặt tại Hương Mít Shop.
            </p>

        </div>


        {{-- =========================================================
             THÔNG BÁO
        ========================================================== --}}

        @if(session('success'))

            <div class="mb-6 rounded-xl border border-green-200 bg-[#F0FDF4] px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>

        @endif


        @if(session('error'))

            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ session('error') }}
            </div>

        @endif


        {{-- =========================================================
             DANH SÁCH ĐƠN
        ========================================================== --}}

        @forelse($orders as $order)

            <a
                href="{{ route('customer.orders.show', $order) }}"
                class="mb-4 block overflow-hidden rounded-xl border border-[#E2E8F0] bg-white transition hover:border-[#BBF7D0] hover:shadow-md"
            >

                {{-- =================================================
                     ORDER HEADER
                ================================================== --}}

                <div class="flex flex-col gap-3 border-b border-[#E2E8F0] px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <div class="text-sm font-semibold text-slate-800">
                            {{ $order->order_code }}
                        </div>

                        <div class="mt-1 text-xs text-slate-400">
                            {{ $order->created_at->format('d/m/Y H:i') }}
                        </div>

                    </div>


                    {{-- STATUS --}}

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
                        class="inline-flex w-fit rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}"
                    >
                        {{ $statusText }}
                    </span>

                </div>


                {{-- =================================================
                     ORDER BODY
                ================================================== --}}

                <div class="px-5 py-5">

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <div class="text-sm text-slate-500">
                                Số sản phẩm
                            </div>

                            <div class="mt-1 font-medium text-slate-800">
                                {{ $order->items_count ?? $order->items->count() }}
                            </div>

                        </div>


                        <div>

                            <div class="text-sm text-slate-500">
                                Người nhận
                            </div>

                            <div class="mt-1 font-medium text-slate-800">
                                {{ $order->receiver_name }}
                            </div>

                        </div>


                        <div class="sm:text-right">

                            <div class="text-sm text-slate-500">
                                Tổng thanh toán
                            </div>

                            <div class="mt-1 text-lg font-bold text-[#16A34A]">
                                {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                            </div>

                        </div>

                    </div>


                    {{-- Xem chi tiết --}}

                    <div class="mt-5 flex items-center justify-end border-t border-[#E2E8F0] pt-4">

                        <span class="text-sm font-semibold text-[#16A34A]">
                            Xem chi tiết →
                        </span>

                    </div>

                </div>

            </a>

        @empty

            {{-- =================================================
                 EMPTY
            ================================================== --}}

            <div class="rounded-xl border border-[#E2E8F0] bg-white px-6 py-16 text-center">

                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-[#F0FDF4] text-3xl">
                    📦
                </div>

                <h2 class="mt-4 text-lg font-semibold text-slate-800">
                    Bạn chưa có đơn hàng nào
                </h2>

                <p class="mt-2 text-sm text-slate-500">
                    Hãy khám phá sản phẩm và bắt đầu mua sắm.
                </p>

                <a
                    href="{{ route('home') }}"
                    class="mt-6 inline-flex rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
                >
                    Tiếp tục mua sắm
                </a>

            </div>

        @endforelse


        {{-- =========================================================
             PAGINATION
        ========================================================== --}}

        @if($orders->hasPages())

            <div class="mt-6">
                {{ $orders->links() }}
            </div>

        @endif

    </div>

</div>

@endsection