@extends('layouts.admin')

@section('title', 'Thống kê')

@section('page-title', 'Thống kê')

@section('content')

    @php

        /*
        |--------------------------------------------------------------------------
        | FORMAT TIỀN
        |--------------------------------------------------------------------------
        */

        $formatMoney = function ($value) {

            return number_format(
                (float) $value,
                0,
                ',',
                '.'
            ) . '₫';

        };


        /*
        |--------------------------------------------------------------------------
        | CẤU HÌNH TRẠNG THÁI
        |--------------------------------------------------------------------------
        */

        $statusConfig = [

            'pending' => [
                'label' => 'Đơn mới',
            ],

            'processing' => [
                'label' => 'Đang chuẩn bị',
            ],

            'shipping' => [
                'label' => 'Đang giao',
            ],

            'delivered' => [
                'label' => 'Đã giao',
            ],

            'cancelled' => [
                'label' => 'Đã hủy',
            ],

        ];


        /*
        |--------------------------------------------------------------------------
        | TỔNG ĐƠN
        |--------------------------------------------------------------------------
        */

        $statusTotal = array_sum($orderStatuses);

    @endphp


    {{-- ========================================================= --}}
    {{-- DATA DÙNG CHO JAVASCRIPT                                  --}}
    {{-- ========================================================= --}}

    <div
        id="dashboard-data"
        class="hidden"
        data-revenue='{{ json_encode(array_values($revenueData)) }}'
        data-order-status='{{ json_encode(array_values($orderStatuses)) }}'
    ></div>



    {{-- ========================================================= --}}
    {{-- MAIN                                                       --}}
    {{-- ========================================================= --}}

    <div class="space-y-6">


        {{-- ===================================================== --}}
        {{-- HEADER                                                  --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <h1 class="text-2xl font-semibold tracking-tight text-[#0F172A]">
                    Tổng quan
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Theo dõi tình hình hoạt động của Hương Mít Shop.
                </p>

            </div>


            <div
                class="inline-flex items-center gap-2 rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-600"
            >

                <span class="text-base">
                    📅
                </span>

                <span>
                    {{ now()->format('d/m/Y') }}
                </span>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- KPI                                                      --}}
        {{-- ===================================================== --}}

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">


            {{-- ================================================= --}}
            {{-- KHÁCH HÀNG                                         --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Khách hàng
                        </p>

                        <p class="mt-2 text-2xl font-bold text-[#0F172A]">
                            {{ number_format($customerCount) }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">
                            +{{ number_format($newCustomers) }}
                            khách trong 7 ngày
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#DCFCE7] text-lg"
                    >
                        👤
                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- SẢN PHẨM                                           --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Tổng sản phẩm
                        </p>

                        <p class="mt-2 text-2xl font-bold text-[#0F172A]">
                            {{ number_format($productCount) }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">
                            Đang quản lý trên hệ thống
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-50 text-lg"
                    >
                        🛍️
                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- ĐƠN HÀNG                                           --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Tổng đơn hàng
                        </p>

                        <p class="mt-2 text-2xl font-bold text-[#0F172A]">
                            {{ number_format($orderCount) }}
                        </p>

                        <p class="mt-2 text-xs text-amber-600">
                            {{ number_format($processingOrders) }}
                            đơn đang xử lý
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-amber-50 text-lg"
                    >
                        🧾
                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- DOANH THU                                          --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white p-5 shadow-sm"
            >

                <div class="flex items-start justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Doanh thu
                        </p>

                        <p class="mt-2 text-2xl font-bold text-[#16A34A]">
                            {{ $formatMoney($revenue) }}
                        </p>

                        <p class="mt-2 text-xs text-slate-400">
                            Không tính đơn đã hủy
                        </p>

                    </div>


                    <div
                        class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#DCFCE7] text-lg"
                    >
                        💰
                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- CHARTS                                                   --}}
        {{-- ===================================================== --}}

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">


            {{-- ================================================= --}}
            {{-- DOANH THU 7 NGÀY                                  --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white p-6 shadow-sm xl:col-span-2"
            >

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-base font-semibold text-[#0F172A]">
                            Doanh thu 7 ngày gần nhất
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            Tổng doanh thu theo từng ngày
                        </p>

                    </div>


                    <span
                        class="rounded-lg bg-[#F0FDF4] px-3 py-1.5 text-xs font-medium text-[#166534]"
                    >
                        7 ngày
                    </span>

                </div>


                <div class="mt-6">


                    {{-- CHART --}}
                    <div class="relative h-72">


                        {{-- GRID --}}
                        <div class="absolute inset-0 flex flex-col justify-between">

                            <div class="border-t border-dashed border-slate-100"></div>

                            <div class="border-t border-dashed border-slate-100"></div>

                            <div class="border-t border-dashed border-slate-100"></div>

                            <div class="border-t border-dashed border-slate-100"></div>

                            <div class="border-t border-dashed border-slate-100"></div>

                        </div>


                        {{-- SVG --}}
                        <svg
                            id="revenue-chart"
                            viewBox="0 0 700 260"
                            preserveAspectRatio="none"
                            class="relative h-64 w-full overflow-visible"
                        >

                            {{-- AREA --}}
                            <path
                                id="revenue-area"
                                fill="#DCFCE7"
                                opacity="0.7"
                            ></path>


                            {{-- LINE --}}
                            <polyline
                                id="revenue-line"
                                fill="none"
                                stroke="#22C55E"
                                stroke-width="3"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            ></polyline>


                            {{-- POINTS --}}
                            <g
                                id="revenue-points"
                                fill="#22C55E"
                            ></g>

                        </svg>

                    </div>


                    {{-- NGÀY --}}
                    <div
                        class="mt-3 grid grid-cols-7 gap-2 text-center text-[11px] text-slate-400"
                    >

                        @foreach($revenueLabels as $label)

                            <span>
                                {{ $label }}
                            </span>

                        @endforeach

                    </div>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- TRẠNG THÁI ĐƠN                                   --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white p-6 shadow-sm"
            >

                <div>

                    <h2 class="text-base font-semibold text-[#0F172A]">
                        Trạng thái đơn hàng
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Phân bổ theo trạng thái hiện tại
                    </p>

                </div>


                <div class="mt-6 flex flex-col items-center gap-6 sm:flex-row">


                    {{-- DONUT --}}
                    <div class="relative h-40 w-40 shrink-0">

                        <div
                            id="order-donut"
                            class="h-full w-full rounded-full"
                        ></div>


                        <div
                            class="absolute inset-4 flex flex-col items-center justify-center rounded-full bg-white"
                        >

                            <span class="text-2xl font-bold text-[#0F172A]">
                                {{ number_format($statusTotal) }}
                            </span>

                            <span class="text-xs text-slate-400">
                                Tổng đơn
                            </span>

                        </div>

                    </div>


                    {{-- LEGEND --}}
                    <div class="w-full min-w-0 flex-1 space-y-3">


                        {{-- ĐƠN MỚI --}}
                        <div class="flex items-center justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-2">

                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full bg-amber-400"
                                ></span>

                                <span class="truncate text-xs text-slate-600">
                                    Đơn mới
                                </span>

                            </div>


                            <div class="text-right">

                                <div class="text-xs font-semibold text-slate-700">
                                    {{ number_format($orderStatuses['pending'] ?? 0) }}
                                </div>

                                <div class="text-[10px] text-slate-400">

                                    @if($statusTotal > 0)
                                        {{ round((($orderStatuses['pending'] ?? 0) / $statusTotal) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif

                                </div>

                            </div>

                        </div>



                        {{-- ĐANG CHUẨN BỊ --}}
                        <div class="flex items-center justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-2">

                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full bg-blue-500"
                                ></span>

                                <span class="truncate text-xs text-slate-600">
                                    Đang chuẩn bị
                                </span>

                            </div>


                            <div class="text-right">

                                <div class="text-xs font-semibold text-slate-700">
                                    {{ number_format($orderStatuses['processing'] ?? 0) }}
                                </div>

                                <div class="text-[10px] text-slate-400">

                                    @if($statusTotal > 0)
                                        {{ round((($orderStatuses['processing'] ?? 0) / $statusTotal) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif

                                </div>

                            </div>

                        </div>



                        {{-- ĐANG GIAO --}}
                        <div class="flex items-center justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-2">

                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full bg-violet-500"
                                ></span>

                                <span class="truncate text-xs text-slate-600">
                                    Đang giao
                                </span>

                            </div>


                            <div class="text-right">

                                <div class="text-xs font-semibold text-slate-700">
                                    {{ number_format($orderStatuses['shipping'] ?? 0) }}
                                </div>

                                <div class="text-[10px] text-slate-400">

                                    @if($statusTotal > 0)
                                        {{ round((($orderStatuses['shipping'] ?? 0) / $statusTotal) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif

                                </div>

                            </div>

                        </div>



                        {{-- ĐÃ GIAO --}}
                        <div class="flex items-center justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-2">

                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full bg-green-500"
                                ></span>

                                <span class="truncate text-xs text-slate-600">
                                    Đã giao
                                </span>

                            </div>


                            <div class="text-right">

                                <div class="text-xs font-semibold text-slate-700">
                                    {{ number_format($orderStatuses['delivered'] ?? 0) }}
                                </div>

                                <div class="text-[10px] text-slate-400">

                                    @if($statusTotal > 0)
                                        {{ round((($orderStatuses['delivered'] ?? 0) / $statusTotal) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif

                                </div>

                            </div>

                        </div>



                        {{-- ĐÃ HỦY --}}
                        <div class="flex items-center justify-between gap-3">

                            <div class="flex min-w-0 items-center gap-2">

                                <span
                                    class="h-2.5 w-2.5 shrink-0 rounded-full bg-red-500"
                                ></span>

                                <span class="truncate text-xs text-slate-600">
                                    Đã hủy
                                </span>

                            </div>


                            <div class="text-right">

                                <div class="text-xs font-semibold text-slate-700">
                                    {{ number_format($orderStatuses['cancelled'] ?? 0) }}
                                </div>

                                <div class="text-[10px] text-slate-400">

                                    @if($statusTotal > 0)
                                        {{ round((($orderStatuses['cancelled'] ?? 0) / $statusTotal) * 100, 1) }}%
                                    @else
                                        0%
                                    @endif

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- THAO TÁC NHANH                                        --}}
        {{-- ===================================================== --}}

        <div
            class="rounded-xl border border-[#E2E8F0] bg-white p-6 shadow-sm"
        >

            <div>

                <h2 class="text-base font-semibold text-[#0F172A]">
                    Thao tác nhanh
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Truy cập nhanh các chức năng thường dùng
                </p>

            </div>


            <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">


                {{-- THÊM SẢN PHẨM --}}
                <a
                    href="{{ route('admin.products.create') }}"
                    class="rounded-xl border border-[#E2E8F0] bg-[#F0FDF4] p-4 transition hover:border-green-200 hover:bg-[#DCFCE7]"
                >

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-lg"
                    >
                        ➕
                    </div>

                    <div class="mt-3 text-sm font-semibold text-slate-700">
                        Thêm sản phẩm
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        Tạo sản phẩm mới
                    </div>

                </a>



                {{-- TẠO PHIẾU NHẬP --}}
                <a
                    href="{{ route('admin.purchase-receipts.create') }}"
                    class="rounded-xl border border-[#E2E8F0] bg-[#F0FDF4] p-4 transition hover:border-green-200 hover:bg-[#DCFCE7]"
                >

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-lg"
                    >
                        📦
                    </div>

                    <div class="mt-3 text-sm font-semibold text-slate-700">
                        Tạo phiếu nhập
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        Nhập thêm hàng
                    </div>

                </a>



                {{-- XEM ĐƠN --}}
                <a
                    href="{{ route('admin.orders.index') }}"
                    class="rounded-xl border border-[#E2E8F0] bg-[#F0FDF4] p-4 transition hover:border-green-200 hover:bg-[#DCFCE7]"
                >

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-lg"
                    >
                        🧾
                    </div>

                    <div class="mt-3 text-sm font-semibold text-slate-700">
                        Xem đơn hàng
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        Xử lý đơn hàng
                    </div>

                </a>



                {{-- QUẢN LÝ SẢN PHẨM --}}
                <a
                    href="{{ route('admin.products.index') }}"
                    class="rounded-xl border border-[#E2E8F0] bg-[#F0FDF4] p-4 transition hover:border-green-200 hover:bg-[#DCFCE7]"
                >

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-white text-lg"
                    >
                        🛍️
                    </div>

                    <div class="mt-3 text-sm font-semibold text-slate-700">
                        Quản lý sản phẩm
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        Xem danh sách
                    </div>

                </a>

            </div>

        </div>



        {{-- ===================================================== --}}
        {{-- LOWER SECTION                                          --}}
        {{-- ===================================================== --}}

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">


            {{-- ================================================= --}}
            {{-- ĐƠN HÀNG GẦN ĐÂY                                 --}}
            {{-- ================================================= --}}

            <div
                class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm xl:col-span-2"
            >


                {{-- HEADER --}}
                <div
                    class="flex items-center justify-between border-b border-[#E2E8F0] px-6 py-4"
                >

                    <div>

                        <h2 class="text-base font-semibold text-[#0F172A]">
                            Đơn hàng gần đây
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            5 đơn hàng mới nhất
                        </p>

                    </div>


                    <a
                        href="{{ route('admin.orders.index') }}"
                        class="text-xs font-medium text-[#16A34A] hover:underline"
                    >
                        Xem tất cả →
                    </a>

                </div>



                {{-- TABLE --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-[#F8FAFC]">

                            <tr>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500"
                                >
                                    Mã đơn
                                </th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-semibold text-slate-500"
                                >
                                    Khách hàng
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-slate-500"
                                >
                                    Tổng tiền
                                </th>

                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-slate-500"
                                >
                                    Trạng thái
                                </th>

                                <th
                                    class="px-6 py-3 text-right text-xs font-semibold text-slate-500"
                                >
                                    Thời gian
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-[#F1F5F9]">


                            @forelse($recentOrders as $order)


                                <tr class="transition hover:bg-[#F8FAFC]">


                                    {{-- MÃ ĐƠN --}}
                                    <td class="whitespace-nowrap px-6 py-4">

                                        <a
                                            href="{{ route('admin.orders.show', $order) }}"
                                            class="text-sm font-semibold text-[#16A34A] hover:underline"
                                        >
                                            {{ $order->order_code }}
                                        </a>

                                    </td>


                                    {{-- KHÁCH HÀNG --}}
                                    <td class="px-6 py-4">

                                        <div class="text-sm font-medium text-slate-700">
                                            {{ $order->receiver_name }}
                                        </div>

                                        <div class="mt-0.5 text-xs text-slate-400">
                                            {{ $order->receiver_phone }}
                                        </div>

                                    </td>


                                    {{-- TỔNG TIỀN --}}
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-right"
                                    >

                                        <div class="text-sm font-semibold text-slate-700">
                                            {{ $formatMoney($order->total_amount) }}
                                        </div>

                                        <div class="mt-0.5 text-xs text-slate-400">
                                            {{ $order->payment_method }}
                                        </div>

                                    </td>


                                    {{-- ================================= --}}
                                    {{-- STATUS - KHÔNG DÙNG STYLE BLADE    --}}
                                    {{-- ================================= --}}

                                    <td class="px-6 py-4 text-center">


                                        @if($order->order_status === 'pending')

                                            <span
                                                class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-medium text-amber-700"
                                            >
                                                Đơn mới
                                            </span>


                                        @elseif($order->order_status === 'processing')

                                            <span
                                                class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-medium text-blue-700"
                                            >
                                                Đang chuẩn bị
                                            </span>


                                        @elseif($order->order_status === 'shipping')

                                            <span
                                                class="inline-flex rounded-full bg-violet-50 px-2.5 py-1 text-[11px] font-medium text-violet-700"
                                            >
                                                Đang giao
                                            </span>


                                        @elseif($order->order_status === 'delivered')

                                            <span
                                                class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-[11px] font-medium text-green-700"
                                            >
                                                Đã giao
                                            </span>


                                        @elseif($order->order_status === 'cancelled')

                                            <span
                                                class="inline-flex rounded-full bg-red-50 px-2.5 py-1 text-[11px] font-medium text-red-700"
                                            >
                                                Đã hủy
                                            </span>


                                        @else

                                            <span
                                                class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-medium text-slate-600"
                                            >
                                                {{ $order->order_status }}
                                            </span>

                                        @endif

                                    </td>


                                    {{-- THỜI GIAN --}}
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-right text-xs text-slate-400"
                                    >

                                        {{ $order->created_at->format('H:i') }}

                                    </td>

                                </tr>


                            @empty


                                <tr>

                                    <td
                                        colspan="5"
                                        class="px-6 py-12 text-center text-sm text-slate-400"
                                    >
                                        Chưa có đơn hàng.
                                    </td>

                                </tr>


                            @endforelse


                        </tbody>

                    </table>

                </div>

            </div>



            {{-- ================================================= --}}
            {{-- SẢN PHẨM BÁN CHẠY                                --}}
            {{-- ================================================= --}}

            <div
                class="rounded-xl border border-[#E2E8F0] bg-white shadow-sm"
            >


                {{-- HEADER --}}
                <div
                    class="border-b border-[#E2E8F0] px-6 py-4"
                >

                    <h2 class="text-base font-semibold text-[#0F172A]">
                        Sản phẩm bán chạy
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Theo số lượng đã bán
                    </p>

                </div>


                {{-- LIST --}}
                <div class="p-6">


                    @forelse($bestSellingProducts as $index => $item)


                        <div
                            class="flex items-center gap-3 {{ !$loop->last ? 'mb-5' : '' }}"
                        >


                            {{-- ================================= --}}
                            {{-- NUMBER                              --}}
                            {{-- ================================= --}}

                            @if($index === 0)

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#DCFCE7] text-sm font-bold text-[#166534]"
                                >
                                    {{ $index + 1 }}
                                </div>

                            @else

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-500"
                                >
                                    {{ $index + 1 }}
                                </div>

                            @endif


                            {{-- ================================= --}}
                            {{-- PRODUCT INFO                        --}}
                            {{-- ================================= --}}

                            <div class="min-w-0 flex-1">

                                <div
                                    class="truncate text-sm font-medium text-slate-700"
                                >
                                    {{ $item->product_name }}
                                </div>


                                <div class="mt-1 text-xs text-slate-400">

                                    Đã bán:

                                    <span class="font-medium text-slate-600">

                                        {{ number_format($item->total_quantity) }}

                                    </span>

                                </div>

                            </div>


                            {{-- ================================= --}}
                            {{-- REVENUE                             --}}
                            {{-- ================================= --}}

                            <div class="text-right">

                                <div class="text-xs font-semibold text-[#16A34A]">

                                    {{ $formatMoney($item->total_revenue) }}

                                </div>

                            </div>

                        </div>


                    @empty


                        <div
                            class="py-10 text-center text-sm text-slate-400"
                        >
                            Chưa có dữ liệu bán hàng.
                        </div>


                    @endforelse


                </div>

            </div>

        </div>

    </div>



    {{-- ========================================================= --}}
    {{-- JAVASCRIPT                                                 --}}
    {{-- ========================================================= --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /*
                |--------------------------------------------------------------------------
                | LẤY DATA
                |--------------------------------------------------------------------------
                */

                const dashboardData =
                    document.getElementById(
                        'dashboard-data'
                    );


                if (!dashboardData) {
                    return;
                }


                const revenueValues =
                    JSON.parse(
                        dashboardData.dataset.revenue
                    );


                const orderStatusValues =
                    JSON.parse(
                        dashboardData.dataset.orderStatus
                    );



                /*
                |--------------------------------------------------------------------------
                | DOANH THU 7 NGÀY
                |--------------------------------------------------------------------------
                */

                const revenueLine =
                    document.getElementById(
                        'revenue-line'
                    );


                const revenueArea =
                    document.getElementById(
                        'revenue-area'
                    );


                const revenuePoints =
                    document.getElementById(
                        'revenue-points'
                    );


                if (
                    revenueLine
                    && revenueArea
                    && revenuePoints
                ) {


                    const width = 700;

                    const height = 260;

                    const paddingTop = 20;

                    const paddingBottom = 20;

                    const usableHeight =
                        height
                        - paddingTop
                        - paddingBottom;


                    const maxValue =
                        Math.max(
                            ...revenueValues,
                            1
                        );


                    const points =
                        revenueValues.map(
                            function (value, index) {


                                const x =
                                    revenueValues.length === 1
                                        ? width / 2
                                        : (
                                            index
                                            / (
                                                revenueValues.length
                                                - 1
                                            )
                                        )
                                        * width;


                                const ratio =
                                    value / maxValue;


                                const y =
                                    height
                                    - paddingBottom
                                    - (
                                        ratio
                                        * usableHeight
                                    );


                                return {
                                    x: x,
                                    y: y
                                };

                            }
                        );



                    /*
                    |--------------------------------------------------------------------------
                    | LINE
                    |--------------------------------------------------------------------------
                    */

                    const linePoints =
                        points
                            .map(
                                function (point) {

                                    return (
                                        point.x
                                        + ','
                                        + point.y
                                    );

                                }
                            )
                            .join(' ');


                    revenueLine.setAttribute(
                        'points',
                        linePoints
                    );



                    /*
                    |--------------------------------------------------------------------------
                    | AREA
                    |--------------------------------------------------------------------------
                    */

                    let areaPath =
                        'M 0 ' + height;


                    points.forEach(
                        function (point) {

                            areaPath +=
                                ' L '
                                + point.x
                                + ' '
                                + point.y;

                        }
                    );


                    areaPath +=
                        ' L '
                        + width
                        + ' '
                        + height
                        + ' Z';


                    revenueArea.setAttribute(
                        'd',
                        areaPath
                    );



                    /*
                    |--------------------------------------------------------------------------
                    | POINTS
                    |--------------------------------------------------------------------------
                    */

                    revenuePoints.innerHTML = '';


                    points.forEach(
                        function (point) {


                            const circle =
                                document.createElementNS(
                                    'http://www.w3.org/2000/svg',
                                    'circle'
                                );


                            circle.setAttribute(
                                'cx',
                                point.x
                            );


                            circle.setAttribute(
                                'cy',
                                point.y
                            );


                            circle.setAttribute(
                                'r',
                                '4'
                            );


                            revenuePoints.appendChild(
                                circle
                            );

                        }
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | DONUT CHART
                |--------------------------------------------------------------------------
                */

                const donut =
                    document.getElementById(
                        'order-donut'
                    );


                if (donut) {


                    const total =
                        orderStatusValues.reduce(
                            function (sum, value) {

                                return sum + value;

                            },
                            0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | KHÔNG CÓ ĐƠN
                    |--------------------------------------------------------------------------
                    */

                    if (total === 0) {

                        donut.style.background =
                            '#E2E8F0';

                    } else {


                        const colors = [

                            '#FBBF24',

                            '#3B82F6',

                            '#8B5CF6',

                            '#22C55E',

                            '#EF4444',

                        ];


                        let currentAngle = 0;

                        const gradients = [];


                        orderStatusValues.forEach(
                            function (value, index) {


                                if (value <= 0) {
                                    return;
                                }


                                const angle =
                                    (
                                        value
                                        / total
                                    )
                                    * 360;


                                const start =
                                    currentAngle;


                                const end =
                                    currentAngle
                                    + angle;


                                gradients.push(

                                    colors[index]
                                    + ' '
                                    + start
                                    + 'deg '
                                    + end
                                    + 'deg'

                                );


                                currentAngle =
                                    end;

                            }
                        );


                        donut.style.background =
                            'conic-gradient('
                            + gradients.join(', ')
                            + ')';

                    }

                }

            }
        );

    </script>

@endsection