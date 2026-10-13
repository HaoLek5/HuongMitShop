@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')

<div class="min-h-screen bg-[#F8FAFC]">

    {{-- =========================================================
         HERO / SEARCH
    ========================================================== --}}

    <section class="border-b border-[#E2E8F0] bg-white">

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

            <div class="max-w-2xl">

                <h1 class="text-3xl font-bold tracking-tight text-[#0F172A] sm:text-4xl">
                    Khám phá sản phẩm
                </h1>

                <p class="mt-2 text-sm text-slate-500 sm:text-base">
                    Tìm những sản phẩm phù hợp với bạn tại Hương Mít Shop.
                </p>

            </div>


            {{-- TÌM KIẾM --}}

            <form
                method="GET"
                action="{{ route('home') }}"
                class="mt-6"
            >

                <div class="relative max-w-3xl">

                    {{-- Icon search --}}

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    >

                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                        ></circle>

                        <path
                            d="m20 20-3.5-3.5"
                        ></path>

                    </svg>


                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Tìm kiếm sản phẩm..."
                        class="w-full rounded-xl border border-[#E2E8F0] bg-white py-3.5 pl-12 pr-28 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                    >


                    <button
                        type="submit"
                        class="absolute right-2 top-1/2 -translate-y-1/2 rounded-lg bg-[#22C55E] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
                    >
                        Tìm kiếm
                    </button>

                </div>

            </form>

        </div>

    </section>


    {{-- =========================================================
         MAIN
    ========================================================== --}}

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">


        {{-- THÔNG BÁO --}}

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


        {{-- =====================================================
             HEADER PRODUCT
        ====================================================== --}}

        <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">

            <div>

                <h2 class="text-xl font-semibold text-[#0F172A]">
                    Sản phẩm
                </h2>

                <p class="mt-1 text-sm text-slate-500">

                    @if(request()->filled('search'))

                        Kết quả tìm kiếm cho:
                        <span class="font-medium text-slate-700">
                            "{{ request('search') }}"
                        </span>

                    @else

                        Khám phá các sản phẩm đang được bán.

                    @endif

                </p>

            </div>


            @if(request()->filled('search'))

                <a
                    href="{{ route('home') }}"
                    class="text-sm font-medium text-[#16A34A] hover:underline"
                >
                    Xóa tìm kiếm
                </a>

            @endif

        </div>


        {{-- =====================================================
             PRODUCT GRID
        ====================================================== --}}

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4 xl:gap-6">

            @forelse($products as $product)

                @php

                    $image =
                        $product->images->first();

                    $activeVariants =
                        $product->variants;

                    $minPrice =
                        $activeVariants->min('price');

                    $maxPrice =
                        $activeVariants->max('price');

                    $totalStock =
                        $activeVariants->sum('stock_quantity');

                @endphp


                {{-- =================================================
                     PRODUCT CARD
                ================================================== --}}

                <a
                    href="{{ route('shop.show', $product) }}"
                    class="group overflow-hidden rounded-xl border border-[#E2E8F0] bg-white transition duration-200 hover:-translate-y-0.5 hover:border-[#BBF7D0] hover:shadow-lg"
                >

                    {{-- ẢNH --}}

                    <div class="relative aspect-square overflow-hidden bg-slate-100">

                        @if($image && $image->image_url)

                            <img
                                src="{{ $image->image_url }}"
                                alt="{{ $product->name }}"
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                loading="lazy"
                            >

                        @else

                            <div class="flex h-full w-full items-center justify-center text-sm text-slate-400">
                                Chưa có ảnh
                            </div>

                        @endif


                        {{-- Badge hết hàng --}}

                        @if($activeVariants->isNotEmpty() && $totalStock <= 0)

                            <div class="absolute left-3 top-3 rounded-full bg-white/95 px-3 py-1 text-xs font-semibold text-red-500 shadow-sm">
                                Hết hàng
                            </div>

                        @endif

                    </div>


                    {{-- NỘI DUNG --}}

                    <div class="p-4 sm:p-5">

                        {{-- CATEGORY --}}

                        @if($product->category)

                            <div class="text-[11px] font-medium uppercase tracking-wide text-slate-400 sm:text-xs">
                                {{ $product->category->name }}
                            </div>

                        @endif


                        {{-- NAME --}}

                        <h3
                            class="mt-2 line-clamp-2 min-h-[42px] text-sm font-semibold leading-5 text-[#0F172A] sm:text-base"
                        >
                            {{ $product->name }}
                        </h3>


                        {{-- PRICE --}}

                        @if($activeVariants->isNotEmpty())

                            <div class="mt-3">

                                @if($minPrice == $maxPrice)

                                    <span class="text-base font-bold text-[#16A34A] sm:text-lg">
                                        {{ number_format($minPrice, 0, ',', '.') }} ₫
                                    </span>

                                @else

                                    <span class="text-base font-bold text-[#16A34A] sm:text-lg">
                                        {{ number_format($minPrice, 0, ',', '.') }}
                                        -
                                        {{ number_format($maxPrice, 0, ',', '.') }} ₫
                                    </span>

                                @endif

                            </div>


                            {{-- STOCK --}}

                            <div class="mt-2">

                                @if($totalStock > 0)

                                    <span class="text-xs font-medium text-slate-500">
                                        Còn hàng
                                    </span>

                                @else

                                    <span class="text-xs font-medium text-red-500">
                                        Hết hàng
                                    </span>

                                @endif

                            </div>

                        @else

                            <div class="mt-3 text-xs text-slate-400">
                                Chưa có biến thể
                            </div>

                        @endif


                        {{-- ACTION --}}

                        <div class="mt-4">

                            <span
                                class="inline-flex w-full items-center justify-center rounded-lg border border-[#BBF7D0] bg-[#F0FDF4] px-3 py-2 text-xs font-semibold text-[#16A34A] transition group-hover:bg-[#22C55E] group-hover:text-white sm:text-sm"
                            >
                                Xem sản phẩm
                            </span>

                        </div>

                    </div>

                </a>

            @empty


                {{-- EMPTY --}}

                <div class="col-span-full rounded-xl border border-[#E2E8F0] bg-white px-6 py-16 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                        📦
                    </div>

                    <h3 class="mt-4 text-lg font-semibold text-slate-800">
                        Không tìm thấy sản phẩm
                    </h3>

                    <p class="mt-2 text-sm text-slate-500">
                        Thử tìm kiếm với từ khóa khác.
                    </p>

                    @if(request()->filled('search'))

                        <a
                            href="{{ route('home') }}"
                            class="mt-5 inline-flex rounded-lg bg-[#22C55E] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#16A34A]"
                        >
                            Xem tất cả sản phẩm
                        </a>

                    @endif

                </div>

            @endforelse

        </div>


        {{-- =====================================================
             PAGINATION
        ====================================================== --}}

        @if($products->hasPages())

            <div class="mt-8">
                {{ $products->links() }}
            </div>

        @endif

    </main>

</div>

@endsection