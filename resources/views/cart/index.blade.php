@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="max-w-6xl mx-auto px-6 py-10">

    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800">
            Giỏ hàng
        </h1>

        <p class="text-gray-500 mt-1">
            Kiểm tra sản phẩm trước khi thanh toán
        </p>
    </div>

    @if(session('success'))
        <div class="mb-5 p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-5 p-4 rounded-lg bg-red-50 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-5 p-4 rounded-lg bg-red-50 text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    @if($cart->items->isEmpty())

        <div class="bg-white border rounded-xl p-12 text-center">

            <div class="text-5xl mb-4">
                🛒
            </div>

            <h2 class="text-xl font-semibold text-gray-800">
                Giỏ hàng đang trống
            </h2>

            <p class="text-gray-500 mt-2">
                Hãy chọn sản phẩm để thêm vào giỏ hàng.
            </p>

            <a
                href="{{ route('home') }}"
                class="inline-block mt-6 px-5 py-2.5 rounded-lg bg-green-600 text-white hover:bg-green-700"
            >
                Tiếp tục mua sắm
            </a>

        </div>

    @else

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 bg-white rounded-xl border overflow-hidden">

                <div class="px-6 py-4 border-b">
                    <h2 class="font-semibold">
                        Sản phẩm trong giỏ
                    </h2>
                </div>

                <div class="divide-y">

                    @foreach($cart->items as $item)

                        @php
                            $variantName = $item->variant->attributeValues
                                ->map(fn($value) => $value->value)
                                ->join(' / ');

                            $subtotal = $item->quantity * $item->price;
                        @endphp

                        <div class="p-6">

                            <div class="flex items-center justify-between gap-6">

                                <div class="flex-1">

                                    <h3 class="font-semibold text-gray-800">
                                        {{ $item->variant->product->name }}
                                    </h3>

                                    <div class="text-sm text-gray-500 mt-1">
                                        SKU: {{ $item->variant->sku }}
                                    </div>

                                    @if($variantName)
                                        <div class="text-sm text-gray-500">
                                            {{ $variantName }}
                                        </div>
                                    @endif

                                    <div class="mt-2 text-green-600 font-medium">
                                        {{ number_format($item->price, 0, ',', '.') }} ₫
                                    </div>

                                </div>

                                <div class="flex items-center gap-3">

                                    <form
                                        method="POST"
                                        action="{{ route('cart.items.update', $item) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <input
                                            type="number"
                                            name="quantity"
                                            min="1"
                                            value="{{ $item->quantity }}"
                                            class="w-20 border-gray-300 rounded-lg text-center"
                                        >

                                        <button
                                            type="submit"
                                            class="ml-2 text-sm text-green-600 hover:text-green-700"
                                        >
                                            Cập nhật
                                        </button>
                                    </form>

                                    <div class="w-36 text-right font-semibold">
                                        {{ number_format($subtotal, 0, ',', '.') }} ₫
                                    </div>

                                    <form
                                        method="POST"
                                        action="{{ route('cart.items.destroy', $item) }}"
                                        onsubmit="return confirm('Xóa sản phẩm này khỏi giỏ?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            Xóa
                                        </button>
                                    </form>

                                </div>

                            </div>

                        </div>

                    @endforeach

                </div>

                <div class="p-6 border-t">

                    <form
                        method="POST"
                        action="{{ route('cart.clear') }}"
                        onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            type="submit"
                            class="text-red-500 hover:text-red-700"
                        >
                            Xóa toàn bộ giỏ hàng
                        </button>

                    </form>

                </div>

            </div>

            <div class="bg-white rounded-xl border p-6 h-fit">

                <h2 class="font-semibold text-gray-800">
                    Tổng giỏ hàng
                </h2>

                <div class="mt-5 flex justify-between">
                    <span class="text-gray-500">
                        Tạm tính
                    </span>

                    <span class="font-semibold">
                        {{ number_format($total, 0, ',', '.') }} ₫
                    </span>
                </div>

                <div class="mt-4 pt-4 border-t flex justify-between text-lg">
                    <span class="font-semibold">
                        Tổng cộng
                    </span>

                    <span class="font-bold text-green-600">
                        {{ number_format($total, 0, ',', '.') }} ₫
                    </span>
                </div>

                <a
                    href="{{ route('checkout.index') }}"
                    class="block w-full mt-6 px-5 py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white text-center font-semibold"
                >
                    Thanh toán
                </a>

            </div>

        </div>

    @endif

</div>
@endsection