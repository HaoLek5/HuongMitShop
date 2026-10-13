@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')

<div class="max-w-7xl mx-auto px-6 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">
            Thanh toán
        </h1>

        <p class="mt-2 text-gray-500">
            Kiểm tra thông tin đơn hàng trước khi đặt.
        </p>
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('checkout.store') }}"
    >

        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ================================================= --}}
            {{-- THÔNG TIN GIAO HÀNG --}}
            {{-- ================================================= --}}

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-xl border overflow-hidden">

                    <div class="px-6 py-4 border-b">
                        <h2 class="font-semibold text-gray-800">
                            Địa chỉ giao hàng
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">

                        @foreach($addresses as $address)

                            <label
                                class="block cursor-pointer"
                            >

                                <div
                                    class="border rounded-xl p-5 hover:border-green-500 transition"
                                >

                                    <div class="flex gap-3">

                                        <input
                                            type="radio"
                                            name="address_id"
                                            value="{{ $address->id }}"
                                            class="mt-1"
                                            {{ old('address_id', $address->is_default ? $address->id : '') == $address->id ? 'checked' : '' }}
                                        >

                                        <div class="flex-1">

                                            <div class="font-semibold text-gray-800">

                                                {{ $address->receiver_name }}

                                                @if($address->is_default)
                                                    <span class="ml-2 text-xs px-2 py-1 rounded-full bg-green-100 text-green-700">
                                                        Mặc định
                                                    </span>
                                                @endif

                                            </div>

                                            <div class="mt-1 text-sm text-gray-500">
                                                {{ $address->receiver_phone }}
                                            </div>

                                            <div class="mt-2 text-sm text-gray-600">
                                                {{ $address->detail_address }},
                                                {{ $address->ward }},
                                                {{ $address->district }},
                                                {{ $address->province }}
                                            </div>

                                        </div>

                                    </div>

                                </div>

                            </label>

                        @endforeach

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- PHƯƠNG THỨC THANH TOÁN --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-xl border overflow-hidden">

                    <div class="px-6 py-4 border-b">
                        <h2 class="font-semibold text-gray-800">
                            Phương thức thanh toán
                        </h2>
                    </div>

                    <div class="p-6 space-y-4">

                        <label class="block cursor-pointer">

                            <div class="border rounded-xl p-5">

                                <div class="flex gap-3">

                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value="cod"
                                        {{ old('payment_method', 'cod') === 'cod' ? 'checked' : '' }}
                                    >

                                    <div>
                                        <div class="font-semibold">
                                            Thanh toán khi nhận hàng
                                        </div>

                                        <div class="text-sm text-gray-500 mt-1">
                                            Thanh toán tiền khi nhận hàng (COD).
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </label>


                        <label class="block cursor-pointer">

                            <div class="border rounded-xl p-5">

                                <div class="flex gap-3">

                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value="bank_transfer"
                                        {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}
                                    >

                                    <div>
                                        <div class="font-semibold">
                                            Chuyển khoản ngân hàng
                                        </div>

                                        <div class="text-sm text-gray-500 mt-1">
                                            Chuyển khoản theo thông tin thanh toán của shop.
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </label>

                    </div>

                </div>


                {{-- ================================================= --}}
                {{-- GHI CHÚ --}}
                {{-- ================================================= --}}

                <div class="bg-white rounded-xl border p-6">

                    <label class="block font-semibold mb-3">
                        Ghi chú đơn hàng
                    </label>

                    <textarea
                        name="note"
                        rows="4"
                        class="w-full border-gray-300 rounded-lg"
                        placeholder="Ví dụ: Giao giờ hành chính..."
                    >{{ old('note') }}</textarea>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- TÓM TẮT ĐƠN --}}
            {{-- ================================================= --}}

            <div>

                <div class="bg-white rounded-xl border p-6 sticky top-6">

                    <h2 class="font-semibold text-gray-800">
                        Đơn hàng của bạn
                    </h2>

                    <div class="mt-5 divide-y">

                        @foreach($cart->items as $item)

                            <div class="py-4">

                                <div class="flex justify-between gap-4">

                                    <div>

                                        <div class="font-medium text-gray-800">
                                            {{ $item->variant->product->name }}
                                        </div>

                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ $item->variant->sku }}
                                        </div>

                                        <div class="text-sm text-gray-500">
                                            SL: {{ $item->quantity }}
                                        </div>

                                    </div>

                                    <div class="text-right font-medium">
                                        {{ number_format(
                                            $item->variant->price * $item->quantity,
                                            0,
                                            ',',
                                            '.'
                                        ) }}
                                        ₫
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                    <div class="mt-5 pt-5 border-t">

                        <div class="flex justify-between">
                            <span class="text-gray-500">
                                Tạm tính
                            </span>

                            <span>
                                {{ number_format($displayTotal, 0, ',', '.') }} ₫
                            </span>
                        </div>

                        <div class="flex justify-between mt-3">
                            <span class="text-gray-500">
                                Phí vận chuyển
                            </span>

                            <span>
                                0 ₫
                            </span>
                        </div>

                        <div class="mt-4 pt-4 border-t flex justify-between text-lg">
                            <span class="font-semibold">
                                Tổng cộng
                            </span>

                            <span class="font-bold text-green-600">
                                {{ number_format($displayTotal, 0, ',', '.') }} ₫
                            </span>
                        </div>

                    </div>

                    <button
                        type="submit"
                        class="w-full mt-6 py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white font-semibold"
                    >
                        Đặt hàng
                    </button>

                    <a
                        href="{{ route('cart.index') }}"
                        class="block text-center mt-4 text-gray-500 hover:text-green-600"
                    >
                        ← Quay lại giỏ hàng
                    </a>

                </div>

            </div>

        </div>

    </form>

</div>

@endsection