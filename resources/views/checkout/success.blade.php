@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')

<div class="max-w-3xl mx-auto px-6 py-16">

    <div class="bg-white rounded-2xl border p-10 text-center">

        <div class="text-6xl">
            ✅
        </div>

        <h1 class="mt-5 text-3xl font-bold text-gray-800">
            Đặt hàng thành công
        </h1>

        <p class="mt-3 text-gray-500">
            Cảm ơn bạn đã mua hàng tại Hương Mít Shop.
        </p>

        <div class="mt-8 p-5 rounded-xl bg-green-50">

            <div class="text-sm text-gray-500">
                Mã đơn hàng
            </div>

            <div class="mt-2 text-2xl font-bold text-green-600">
                {{ $order->order_code }}
            </div>

        </div>

        <div class="mt-6 text-left">

            <div class="flex justify-between py-2">
                <span class="text-gray-500">
                    Tổng tiền
                </span>

                <span class="font-semibold">
                    {{ number_format($order->total_amount, 0, ',', '.') }} ₫
                </span>
            </div>

            @if($order->deposit_required)

                <div class="flex justify-between py-2">
                    <span class="text-gray-500">
                        Tiền cọc
                    </span>

                    <span class="font-semibold text-orange-600">
                        {{ number_format($order->deposit_amount, 0, ',', '.') }} ₫
                    </span>
                </div>

            @endif

            <div class="flex justify-between py-2">
                <span class="text-gray-500">
                    Người nhận
                </span>

                <span class="font-semibold">
                    {{ $order->receiver_name }}
                </span>
            </div>

            <div class="flex justify-between py-2">
                <span class="text-gray-500">
                    Số điện thoại
                </span>

                <span>
                    {{ $order->receiver_phone }}
                </span>
            </div>

        </div>

        <div class="mt-8 flex justify-center gap-3">

            <a
                href="{{ route('home') }}"
                class="px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white"
            >
                Tiếp tục mua sắm
            </a>

            <a
                href="{{ route('dashboard') }}"
                class="px-5 py-2.5 rounded-lg border hover:bg-gray-50"
            >
                Tài khoản
            </a>

        </div>

    </div>

</div>

@endsection