@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')

<div
    class="space-y-6 text-slate-900"
    style="font-family: Inter, ui-sans-serif, system-ui, sans-serif;"
>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-[#0F172A]">
                Quản lý sản phẩm
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Quản lý sản phẩm và các biến thể đang bán
            </p>
        </div>


        <a
            href="{{ route('admin.products.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
        >
            <span class="text-lg leading-none">
                +
            </span>

            Thêm sản phẩm
        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- THÔNG BÁO --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="rounded-lg border border-green-200 bg-[#F0FDF4] px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first() }}
        </div>

    @endif


    {{-- ========================================================= --}}
    {{-- TÌM KIẾM + LỌC --}}
    {{-- ========================================================= --}}

    <div class="rounded-xl border border-[#E2E8F0] bg-white p-4 shadow-sm">

        <form
            method="GET"
            action="{{ route('admin.products.index') }}"
            class="flex flex-col gap-3 lg:flex-row"
        >

            {{-- Tìm kiếm --}}
            <div class="relative flex-1">

                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                >
                    <circle
                        cx="11"
                        cy="11"
                        r="7"
                    ></circle>

                    <path d="m20 20-3.5-3.5"></path>
                </svg>

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Tìm kiếm sản phẩm theo tên..."
                    class="w-full rounded-lg border border-[#E2E8F0] bg-white py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                >

            </div>


            {{-- Lọc danh mục --}}
            <select
                name="category_id"
                class="rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
            >

                <option value="">
                    Tất cả danh mục
                </option>

                @foreach($categories as $category)

                    <option
                        value="{{ $category->id }}"
                        {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}
                    >
                        {{ $category->name }}
                    </option>

                @endforeach

            </select>


            {{-- Lọc trạng thái --}}
            <select
                name="status"
                class="rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
            >

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="1"
                    {{ request('status') === '1' ? 'selected' : '' }}
                >
                    Đang bán
                </option>

                <option
                    value="0"
                    {{ request('status') === '0' ? 'selected' : '' }}
                >
                    Ngừng bán
                </option>

            </select>


            {{-- Nút tìm kiếm --}}
            <button
                type="submit"
                class="rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
            >
                Tìm kiếm
            </button>


            {{-- Xóa bộ lọc --}}
            @if(
                request()->filled('search')
                || request()->filled('category_id')
                || request()->has('status')
            )

                <a
                    href="{{ route('admin.products.index') }}"
                    class="rounded-lg border border-[#E2E8F0] bg-white px-5 py-2.5 text-center text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                >
                    Xóa lọc
                </a>

            @endif

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- DATA TABLE --}}
    {{-- ========================================================= --}}

    <div class="overflow-hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="w-full min-w-[1050px] border-collapse text-sm">

                {{-- ================================================= --}}
                {{-- TABLE HEADER --}}
                {{-- ================================================= --}}

                <thead>

                    <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">

                        {{-- Checkbox --}}
                        <th class="w-14 px-4 py-4 text-center">

                            <input
                                type="checkbox"
                                id="select-all"
                                class="h-4 w-4 rounded border-slate-300 text-[#22C55E] focus:ring-green-200"
                            >

                        </th>


                        {{-- Ảnh --}}
                        <th class="w-24 px-4 py-4 text-left font-semibold text-slate-600">
                            Ảnh
                        </th>


                        {{-- Mã hàng --}}
                        <th class="w-36 px-4 py-4 text-left font-semibold text-slate-600">
                            Mã hàng
                        </th>


                        {{-- Tên hàng --}}
                        <th class="px-4 py-4 text-left font-semibold text-slate-600">
                            Tên hàng
                        </th>


                        {{-- Giá bán --}}
                        <th class="w-40 px-4 py-4 text-right font-semibold text-slate-600">
                            Giá bán
                        </th>


                        {{-- Tồn kho --}}
                        <th class="w-32 px-4 py-4 text-center font-semibold text-slate-600">
                            Tồn kho
                        </th>


                        {{-- Trạng thái --}}
                        <th class="w-40 px-4 py-4 text-center font-semibold text-slate-600">
                            Trạng thái
                        </th>


                        {{-- Sửa --}}
                        <th class="w-20 px-4 py-4 text-center font-semibold text-slate-600">
                            Sửa
                        </th>

                    </tr>

                </thead>


                {{-- ================================================= --}}
                {{-- TABLE BODY --}}
                {{-- ================================================= --}}

                <tbody class="divide-y divide-[#E2E8F0]">

                    @forelse($products as $product)

                        @php

                            /*
                            |--------------------------------------------------------------------------
                            | Ảnh đại diện
                            |--------------------------------------------------------------------------
                            */

                            $image = $product->images->first();


                            /*
                            |--------------------------------------------------------------------------
                            | Tổng tồn kho
                            |--------------------------------------------------------------------------
                            |
                            | Tồn của product cha = tổng tồn của tất cả variant.
                            |
                            */

                            $totalStock = $product->variants->sum(
                                'stock_quantity'
                            );


                            /*
                            |--------------------------------------------------------------------------
                            | Có variant hay không
                            |--------------------------------------------------------------------------
                            */

                            $hasVariants =
                                $product->variants->isNotEmpty();


                            /*
                            |--------------------------------------------------------------------------
                            | URL ảnh
                            |--------------------------------------------------------------------------
                            */

                            $imageUrl = $image
                                ? $image->image_url
                                : null;

                        @endphp


                        {{-- ================================================= --}}
                        {{-- PRODUCT ROW --}}
                        {{-- ================================================= --}}

                        <tr
                            id="product-row-{{ $product->id }}"
                            class="transition hover:bg-slate-50"
                        >


                            {{-- ================================================= --}}
                            {{-- CHECKBOX + EXPAND --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    @if($hasVariants)

                                        <button
                                            type="button"
                                            class="variant-toggle flex h-7 w-7 items-center justify-center rounded-md text-slate-400 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                                            data-product-id="{{ $product->id }}"
                                            title="Xem biến thể"
                                            aria-label="Xem biến thể"
                                        >

                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="2"
                                                class="variant-chevron h-4 w-4 transition-transform duration-200"
                                            >
                                                <path d="m9 18 6-6-6-6"></path>
                                            </svg>

                                        </button>

                                    @else

                                        <span class="h-7 w-7"></span>

                                    @endif


                                    <input
                                        type="checkbox"
                                        name="selected_products[]"
                                        value="{{ $product->id }}"
                                        class="product-checkbox h-4 w-4 rounded border-slate-300 text-[#22C55E] focus:ring-green-200"
                                    >

                                </div>

                            </td>


                            {{-- ================================================= --}}
                            {{-- ẢNH --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4">

                                <div class="h-12 w-12 overflow-hidden rounded-lg border border-[#E2E8F0] bg-slate-50">

                                    @if($imageUrl)

                                        <img
                                            src="{{ $imageUrl }}"
                                            alt="{{ $product->name }}"
                                            class="h-full w-full object-cover"
                                        >

                                    @else

                                        <div class="flex h-full w-full items-center justify-center px-1 text-center text-[10px] text-slate-400">
                                            Không ảnh
                                        </div>

                                    @endif

                                </div>

                            </td>


                            {{-- ================================================= --}}
                            {{-- MÃ HÀNG --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4">

                                <span class="font-medium text-slate-700">

                                    SP-{{ str_pad(
                                        $product->id,
                                        4,
                                        '0',
                                        STR_PAD_LEFT
                                    ) }}

                                </span>

                            </td>


                            {{-- ================================================= --}}
                            {{-- TÊN HÀNG --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4">

                                <div class="font-semibold text-[#0F172A]">
                                    {{ $product->name }}
                                </div>

                            </td>


                            {{-- ================================================= --}}
                            {{-- GIÁ BÁN --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4 text-right">

                                <span class="font-semibold text-slate-900">

                                    {{ number_format(
                                        $product->base_price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                    ₫

                                </span>

                            </td>


                            {{-- ================================================= --}}
                            {{-- TỒN KHO --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4 text-center">

                                @if($totalStock <= 0)

                                    <span class="font-medium text-red-500">
                                        Hết hàng
                                    </span>

                                @elseif($totalStock <= 5)

                                    <span class="font-semibold text-amber-600">
                                        {{ $totalStock }}
                                    </span>

                                @else

                                    <span class="font-semibold text-slate-700">
                                        {{ $totalStock }}
                                    </span>

                                @endif

                            </td>


                            {{-- ================================================= --}}
                            {{-- TRẠNG THÁI --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4 text-center">

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.products.toggle-status',
                                        $product
                                    ) }}"
                                >

                                    @csrf

                                    @method('PATCH')


                                    <button
                                        type="submit"
                                        class="inline-flex items-center gap-2"
                                        title="Bật hoặc tắt trạng thái sản phẩm"
                                    >

                                        {{-- Switch --}}
                                        <span
                                            class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition
                                            {{ $product->status
                                                ? 'bg-[#22C55E]'
                                                : 'bg-slate-300' }}"
                                        >

                                            <span
                                                class="inline-block h-5 w-5 rounded-full bg-white shadow-sm transition
                                                {{ $product->status
                                                    ? 'translate-x-5'
                                                    : 'translate-x-0' }}"
                                            ></span>

                                        </span>


                                        {{-- Text --}}
                                        @if($product->status)

                                            <span class="text-sm font-medium text-[#16A34A]">
                                                Đang bán
                                            </span>

                                        @else

                                            <span class="text-sm font-medium text-slate-500">
                                                Ngừng bán
                                            </span>

                                        @endif

                                    </button>

                                </form>

                            </td>


                            {{-- ================================================= --}}
                            {{-- SỬA PRODUCT --}}
                            {{-- ================================================= --}}

                            <td class="px-4 py-4 text-center">

                                <a
                                    href="{{ route(
                                        'admin.products.edit',
                                        $product
                                    ) }}"
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                                    title="Sửa sản phẩm"
                                >

                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        class="h-4 w-4"
                                    >

                                        <path d="M12 20h9"></path>

                                        <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"></path>

                                    </svg>

                                </a>

                            </td>

                        </tr>


                        {{-- ================================================= --}}
                        {{-- VARIANTS --}}
                        {{-- ================================================= --}}

                        @if($hasVariants)

                            <tr
                                id="variants-{{ $product->id }}"
                                class="variant-row hidden bg-[#F8FAFC]"
                            >

                                <td
                                    colspan="8"
                                    class="px-6 py-4"
                                >

                                    <div class="ml-10 overflow-hidden rounded-lg border border-[#E2E8F0] bg-white">

                                        {{-- ================================================= --}}
                                        {{-- VARIANT HEADER --}}
                                        {{-- ================================================= --}}

                                        <div class="flex items-center justify-between border-b border-[#E2E8F0] px-4 py-3">

                                            <div>

                                                <div class="text-sm font-semibold text-slate-800">
                                                    Các biến thể của {{ $product->name }}
                                                </div>

                                                <div class="mt-1 text-xs text-slate-400">

                                                    {{ $product->variants->count() }}

                                                    biến thể

                                                </div>

                                            </div>

                                        </div>


                                        {{-- ================================================= --}}
                                        {{-- VARIANT TABLE --}}
                                        {{-- ================================================= --}}

                                        {{-- Variant table --}}
<div class="overflow-x-auto">

    <table class="w-full text-sm">

        <thead>

            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">

                <th class="px-4 py-3 text-left font-medium text-slate-500">
                    Mã SKU
                </th>

                <th class="px-4 py-3 text-left font-medium text-slate-500">
                    Tên biến thể
                </th>

                <th class="px-4 py-3 text-right font-medium text-slate-500">
                    Giá bán
                </th>

                <th class="px-4 py-3 text-center font-medium text-slate-500">
                    Tồn kho
                </th>

                <th class="w-20 px-4 py-3 text-center font-medium text-slate-500">
                    Sửa
                </th>

            </tr>

        </thead>


        <tbody
            class="variant-table-body divide-y divide-[#E2E8F0]"
        >

            @foreach($product->variants as $variant)

                @php

                    $variantName = $variant->attributeValues
                        ->map(function ($value) {
                            return $value->value;
                        })
                        ->join(' - ');

                @endphp


                <tr class="variant-item transition hover:bg-slate-50">

                    {{-- SKU --}}
                    <td class="px-4 py-3 font-medium text-slate-700">
                        {{ $variant->sku }}
                    </td>


                    {{-- TÊN BIẾN THỂ --}}
                    <td class="px-4 py-3 font-medium text-slate-800">

                        {{ $product->name }}

                        @if($variantName)

                            <span class="text-slate-500">
                                - {{ $variantName }}
                            </span>

                        @endif

                    </td>


                    {{-- GIÁ --}}
                    <td class="px-4 py-3 text-right font-medium text-slate-800">

                        {{ number_format(
                            $variant->price,
                            0,
                            ',',
                            '.'
                        ) }}

                        ₫

                    </td>


                    {{-- TỒN --}}
                    <td class="px-4 py-3 text-center">

                        @if($variant->stock_quantity <= 0)

                            <span class="font-medium text-red-500">
                                Hết hàng
                            </span>

                        @elseif($variant->stock_quantity <= 5)

                            <span class="font-semibold text-amber-600">
                                {{ $variant->stock_quantity }}
                            </span>

                        @else

                            <span class="font-semibold text-slate-700">
                                {{ $variant->stock_quantity }}
                            </span>

                        @endif

                    </td>


                    {{-- SỬA --}}
                    <td class="px-4 py-3 text-center">

                        <a
                            href="{{ route(
                                'admin.products.variants.edit',
                                [
                                    'product' => $product,
                                    'variant' => $variant
                                ]
                            ) }}"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
                            title="Sửa biến thể"
                        >

                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                class="h-4 w-4"
                            >
                                <path d="M12 20h9"></path>

                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                            </svg>

                        </a>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>

</div>


{{-- Variant pagination --}}
<div
    class="variant-pagination flex flex-col gap-3 border-t border-[#E2E8F0] bg-white px-4 py-3 sm:flex-row sm:items-center sm:justify-end"
    data-product-id="{{ $product->id }}"
>

    <div class="flex items-center gap-1">

        {{-- First --}}
        <button
            type="button"
            data-page-action="first"
            class="variant-page-button inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
            title="Trang đầu"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="h-4 w-4"
            >
                <path d="m11 17-5-5 5-5"></path>
                <path d="M18 17 13 12l5-5"></path>
            </svg>
        </button>


        {{-- Previous --}}
        <button
            type="button"
            data-page-action="prev"
            class="variant-page-button inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
            title="Trang trước"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="h-4 w-4"
            >
                <path d="m15 18-6-6 6-6"></path>
            </svg>
        </button>


        {{-- Current page --}}
        <span
            data-page-number
            class="inline-flex h-8 min-w-12 items-center justify-center rounded-lg border border-[#CBD5E1] bg-white px-3 text-sm font-medium text-slate-700"
        >
            1
        </span>


        {{-- Next --}}
        <button
            type="button"
            data-page-action="next"
            class="variant-page-button inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
            title="Trang sau"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="h-4 w-4"
            >
                <path d="m9 18 6-6-6-6"></path>
            </svg>
        </button>


        {{-- Last --}}
        <button
            type="button"
            data-page-action="last"
            class="variant-page-button inline-flex h-8 w-8 items-center justify-center rounded-md text-slate-400 transition hover:bg-[#F0FDF4] hover:text-[#16A34A]"
            title="Trang cuối"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                class="h-4 w-4"
            >
                <path d="m6 17 5-5-5-5"></path>
                <path d="m13 17 5-5-5-5"></path>
            </svg>
        </button>

    </div>


    {{-- Range --}}
    <div
        data-page-info
        class="text-sm font-medium text-slate-600"
    >
        0 - 0 trong 0 dòng
    </div>

</div>

                                    </div>

                                </td>

                            </tr>

                        @endif


                    @empty

                        {{-- ================================================= --}}
                        {{-- EMPTY --}}
                        {{-- ================================================= --}}

                        <tr>

                            <td
                                colspan="8"
                                class="px-6 py-16 text-center"
                            >

                                <div class="mx-auto flex max-w-sm flex-col items-center">

                                    <div class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-2xl">
                                        📦
                                    </div>

                                    <h3 class="mt-4 font-semibold text-slate-800">
                                        Không tìm thấy sản phẩm
                                    </h3>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Hãy thử thay đổi từ khóa hoặc bộ lọc.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ========================================================= --}}
        {{-- PAGINATION FOOTER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-3 border-t border-[#E2E8F0] bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

            <div class="text-sm text-slate-500">

                Hiển thị

                <span class="font-medium text-slate-700">
                    {{ $products->firstItem() ?? 0 }}
                </span>

                -

                <span class="font-medium text-slate-700">
                    {{ $products->lastItem() ?? 0 }}
                </span>

                trong tổng số

                <span class="font-medium text-slate-700">
                    {{ $products->total() }}
                </span>

                sản phẩm

            </div>


            <div>
                {{ $products->links() }}
            </div>

        </div>

    </div>

</div>


{{-- ========================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ========================================================= --}}

<script>

/*
|--------------------------------------------------------------------------
| Expand / Collapse Product Variant
|--------------------------------------------------------------------------
*/

document
    .querySelectorAll('.variant-toggle')
    .forEach(function (button) {

        button.addEventListener(
            'click',
            function () {

                const productId =
                    this.dataset.productId;


                const variantRow =
                    document.getElementById(
                        'variants-' + productId
                    );


                const chevron =
                    this.querySelector(
                        '.variant-chevron'
                    );


                /*
                |--------------------------------------------------------------------------
                | Không tìm thấy row
                |--------------------------------------------------------------------------
                */

                if (!variantRow) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Kiểm tra đang đóng hay mở
                |--------------------------------------------------------------------------
                */

                const isHidden =
                    variantRow.classList.contains(
                        'hidden'
                    );


                /*
                |--------------------------------------------------------------------------
                | Hiện / ẩn
                |--------------------------------------------------------------------------
                */

                variantRow.classList.toggle(
                    'hidden',
                    !isHidden
                );


                /*
                |--------------------------------------------------------------------------
                | Xoay icon
                |--------------------------------------------------------------------------
                */

                if (chevron) {

                    chevron.classList.toggle(
                        'rotate-90',
                        isHidden
                    );

                }

            }
        );

    });


/*
|--------------------------------------------------------------------------
| Chọn tất cả sản phẩm
|--------------------------------------------------------------------------
*/

const selectAll =
    document.getElementById('select-all');


if (selectAll) {


    /*
    |--------------------------------------------------------------------------
    | Chọn / bỏ chọn tất cả
    |--------------------------------------------------------------------------
    */

    selectAll.addEventListener(
        'change',
        function () {

            document
                .querySelectorAll(
                    '.product-checkbox'
                )
                .forEach(function (checkbox) {

                    checkbox.checked =
                        selectAll.checked;

                });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Nếu bỏ chọn một sản phẩm,
    | checkbox "chọn tất cả" cũng bỏ.
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '.product-checkbox'
        )
        .forEach(function (checkbox) {

            checkbox.addEventListener(
                'change',
                function () {

                    const all =
                        document.querySelectorAll(
                            '.product-checkbox'
                        );


                    const checked =
                        document.querySelectorAll(
                            '.product-checkbox:checked'
                        );


                    selectAll.checked =
                        all.length > 0
                        && all.length === checked.length;

                }
            );

        });

        /*
|--------------------------------------------------------------------------
| Phân trang biến thể
|--------------------------------------------------------------------------
|
| Mỗi sản phẩm có một bộ phân trang riêng.
| Mặc định: 15 biến thể / trang.
|
*/

const VARIANTS_PER_PAGE = 15;


document
    .querySelectorAll('.variant-row')
    .forEach(function (variantRow) {

        const tableBody =
            variantRow.querySelector(
                '.variant-table-body'
            );


        const pagination =
            variantRow.querySelector(
                '.variant-pagination'
            );


        if (!tableBody || !pagination) {
            return;
        }


        const rows =
            Array.from(
                tableBody.querySelectorAll(
                    '.variant-item'
                )
            );


        const firstButton =
            pagination.querySelector(
                '[data-page-action="first"]'
            );


        const prevButton =
            pagination.querySelector(
                '[data-page-action="prev"]'
            );


        const nextButton =
            pagination.querySelector(
                '[data-page-action="next"]'
            );


        const lastButton =
            pagination.querySelector(
                '[data-page-action="last"]'
            );


        const pageNumber =
            pagination.querySelector(
                '[data-page-number]'
            );


        const pageInfo =
            pagination.querySelector(
                '[data-page-info]'
            );


        let currentPage = 1;


        const totalRows =
            rows.length;


        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    totalRows /
                    VARIANTS_PER_PAGE
                )
            );


        function updateButtonState(
            button,
            disabled
        ) {

            if (!button) {
                return;
            }


            button.disabled =
                disabled;


            button.classList.toggle(
                'opacity-40',
                disabled
            );


            button.classList.toggle(
                'cursor-not-allowed',
                disabled
            );


            button.classList.toggle(
                'hover:bg-[#F0FDF4]',
                !disabled
            );


            button.classList.toggle(
                'hover:text-[#16A34A]',
                !disabled
            );

        }


        function renderVariantPage(
            page
        ) {

            currentPage =
                Math.max(
                    1,
                    Math.min(
                        page,
                        totalPages
                    )
                );


            const startIndex =
                (currentPage - 1)
                * VARIANTS_PER_PAGE;


            const endIndex =
                Math.min(
                    startIndex
                    + VARIANTS_PER_PAGE,
                    totalRows
                );


            rows.forEach(
                function (row, index) {

                    const visible =
                        index >= startIndex
                        && index < endIndex;


                    row.classList.toggle(
                        'hidden',
                        !visible
                    );

                }
            );


            const displayStart =
                totalRows === 0
                    ? 0
                    : startIndex + 1;


            const displayEnd =
                totalRows === 0
                    ? 0
                    : endIndex;


            pageNumber.textContent =
                currentPage;


            pageInfo.textContent =
                displayStart
                + ' - '
                + displayEnd
                + ' trong '
                + totalRows
                + ' dòng';


            updateButtonState(
                firstButton,
                currentPage === 1
                    || totalRows === 0
            );


            updateButtonState(
                prevButton,
                currentPage === 1
                    || totalRows === 0
            );


            updateButtonState(
                nextButton,
                currentPage === totalPages
                    || totalRows === 0
            );


            updateButtonState(
                lastButton,
                currentPage === totalPages
                    || totalRows === 0
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Trang đầu
        |--------------------------------------------------------------------------
        */

        firstButton.addEventListener(
            'click',
            function () {

                renderVariantPage(1);

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Trang trước
        |--------------------------------------------------------------------------
        */

        prevButton.addEventListener(
            'click',
            function () {

                renderVariantPage(
                    currentPage - 1
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Trang sau
        |--------------------------------------------------------------------------
        */

        nextButton.addEventListener(
            'click',
            function () {

                renderVariantPage(
                    currentPage + 1
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Trang cuối
        |--------------------------------------------------------------------------
        */

        lastButton.addEventListener(
            'click',
            function () {

                renderVariantPage(
                    totalPages
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Khởi tạo
        |--------------------------------------------------------------------------
        */

        renderVariantPage(1);

    });

}

</script>

@endsection