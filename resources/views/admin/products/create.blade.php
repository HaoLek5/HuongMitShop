@extends('layouts.admin')

@section('title', 'Thêm sản phẩm')

@section('content')

@php
    $oldAttributes = old('attributes', []);
    $oldVariants = old('variants', []);

    $attributeOptions = $attributes
        ->map(function ($attribute) {
            return [
                'id' => $attribute->id,
                'name' => $attribute->name,
            ];
        })
        ->values()
        ->all();
@endphp

<div
    class="mx-auto max-w-7xl space-y-6 text-[#0F172A]"
    style="font-family: Inter, ui-sans-serif, system-ui, sans-serif;"
>

    {{-- ========================================================= --}}
    {{-- HEADER --}}
    {{-- ========================================================= --}}

    <div>

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-2xl font-semibold tracking-tight">
                    Thêm sản phẩm
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Tạo sản phẩm và tự động sinh các biến thể theo thuộc tính.
                </p>

            </div>


            <a
                href="{{ route('admin.products.index') }}"
                class="inline-flex items-center justify-center rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50"
            >
                ← Danh sách sản phẩm
            </a>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- SUCCESS / ERROR --}}
    {{-- ========================================================= --}}

    @if(session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

            <div class="font-semibold">
                Không thể lưu sản phẩm
            </div>

            <ul class="mt-2 list-disc space-y-1 pl-5">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif



    {{-- ========================================================= --}}
    {{-- INLINE MODALS --}}
    {{-- ========================================================= --}}

    <div
        id="category-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4"
    >

        <div
            class="w-full max-w-md rounded-2xl bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
        >

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-lg font-semibold text-[#0F172A]">
                            Thêm danh mục
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            Tạo danh mục mới mà không cần rời khỏi trang.
                        </p>

                    </div>

                    <button
                        type="button"
                        data-close-modal="category-modal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Đóng"
                    >
                        ×
                    </button>

                </div>

            </div>

            <form id="inline-category-form">

                <div class="p-6">

                    <label
                        for="inline-category-name"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Tên danh mục
                    </label>

                    <input
                        id="inline-category-name"
                        name="name"
                        type="text"
                        maxlength="100"
                        required
                        placeholder="Ví dụ: Áo khoác"
                        class="w-full rounded-lg border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                    >

                    <div
                        id="inline-category-error"
                        class="mt-2 hidden text-sm text-red-600"
                    ></div>

                </div>

                <div class="flex justify-end gap-3 border-t border-[#E2E8F0] px-6 py-4">

                    <button
                        type="button"
                        data-close-modal="category-modal"
                        class="rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Hủy
                    </button>

                    <button
                        type="submit"
                        id="inline-category-submit"
                        class="rounded-lg bg-[#22C55E] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#16A34A]"
                    >
                        Lưu danh mục
                    </button>

                </div>

            </form>

        </div>

    </div>


    <div
        id="attribute-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4"
    >

        <div
            class="w-full max-w-md rounded-2xl bg-white shadow-xl"
            role="dialog"
            aria-modal="true"
        >

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="text-lg font-semibold text-[#0F172A]">
                            Thêm tên thuộc tính
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            Tên mới sẽ được lưu vào database và chọn cho nhóm hiện tại.
                        </p>

                    </div>

                    <button
                        type="button"
                        data-close-modal="attribute-modal"
                        class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Đóng"
                    >
                        ×
                    </button>

                </div>

            </div>

            <form id="inline-attribute-form">

                <div class="p-6">

                    <label
                        for="inline-attribute-name"
                        class="mb-2 block text-sm font-medium text-slate-700"
                    >
                        Tên thuộc tính
                    </label>

                    <input
                        id="inline-attribute-name"
                        name="name"
                        type="text"
                        maxlength="100"
                        required
                        placeholder="Ví dụ: Chất liệu"
                        class="w-full rounded-lg border border-[#E2E8F0] px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                    >

                    <div
                        id="inline-attribute-error"
                        class="mt-2 hidden text-sm text-red-600"
                    ></div>

                </div>

                <div class="flex justify-end gap-3 border-t border-[#E2E8F0] px-6 py-4">

                    <button
                        type="button"
                        data-close-modal="attribute-modal"
                        class="rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Hủy
                    </button>

                    <button
                        type="submit"
                        id="inline-attribute-submit"
                        class="rounded-lg bg-[#22C55E] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#16A34A]"
                    >
                        Lưu thuộc tính
                    </button>

                </div>

            </form>

        </div>

    </div>

    {{-- ========================================================= --}}
    {{-- FORM STATE --}}
    {{-- ========================================================= --}}

    <div
        id="form-state"
        class="hidden"
        data-old-attributes='{{ json_encode(
            $oldAttributes,
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_AMP |
            JSON_HEX_QUOT
        ) }}'
        data-old-variants='{{ json_encode(
            $oldVariants,
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_AMP |
            JSON_HEX_QUOT
        ) }}'
        data-available-attributes='{{ json_encode(
            $attributeOptions,
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_AMP |
            JSON_HEX_QUOT
        ) }}'
    ></div>


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        id="product-form"
        method="POST"
        action="{{ route('admin.products.store') }}"
        enctype="multipart/form-data"
    >

        @csrf


        {{-- ===================================================== --}}
        {{-- THÔNG TIN CƠ BẢN --}}
        {{-- ===================================================== --}}

        <div class="rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <h2 class="font-semibold text-[#0F172A]">
                    Thông tin sản phẩm
                </h2>

            </div>


            <div class="grid grid-cols-1 gap-6 p-6 lg:grid-cols-2">


                {{-- MÃ HÀNG --}}

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Mã hàng
                    </label>

                    <input
                        type="text"
                        value="{{ $productCode }}"
                        readonly
                        class="w-full cursor-not-allowed rounded-lg border border-[#E2E8F0] bg-slate-50 px-4 py-2.5 text-sm font-medium text-slate-500"
                    >

                    <p class="mt-1.5 text-xs text-slate-400">
                        Mã được tự sinh theo sản phẩm khi lưu.
                    </p>

                </div>


                {{-- TÊN --}}

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">

                        Tên hàng

                        <span class="text-red-500">*</span>

                    </label>

                    <input
                        id="product-name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        maxlength="200"
                        placeholder="Ví dụ: Áo thun Basic"
                        class="w-full rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                    >

                </div>
                {{-- DANH MỤC --}}

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">

                        Danh mục

                        <span class="text-red-500">*</span>

                    </label>

                    <div class="flex items-center gap-2">

                        <select
                            id="category-select"
                            name="category_id"
                            required
                            class="min-w-0 flex-1 rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                        >

                            <option value="">
                                Chọn danh mục
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        <button
                            type="button"
                            id="add-category-inline"
                            class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[#BBF7D0] bg-[#F0FDF4] text-xl font-medium text-[#16A34A] transition hover:bg-green-100"
                            title="Thêm danh mục"
                            aria-label="Thêm danh mục"
                        >
                            +
                        </button>

                    </div>

                </div>


                {{-- GIÁ BÁN --}}

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">

                        Giá bán

                        <span class="text-red-500">*</span>

                    </label>

                    <div class="relative">

                        <input
                            id="base-price"
                            type="number"
                            name="base_price"
                            value="{{ old('base_price', 0) }}"
                            min="0"
                            step="1000"
                            required
                            class="w-full rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 pr-12 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                        >

                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-sm text-slate-400">
                            ₫
                        </span>

                    </div>

                    <p class="mt-1.5 text-xs text-slate-400">
                        Giá này được dùng làm giá mặc định cho các biến thể.
                    </p>

                </div>


                {{-- TỒN KHO --}}

                <div id="base-stock-wrapper">

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Tồn kho
                    </label>

                    <input
                        id="base-stock"
                        type="number"
                        name="stock_quantity"
                        value="{{ old('stock_quantity', 0) }}"
                        min="0"
                        step="1"
                        class="w-full rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                    >

                    <p class="mt-1.5 text-xs text-slate-400">
                        Chỉ áp dụng khi sản phẩm chưa có thuộc tính/biến thể.
                    </p>

                </div>


                {{-- STATUS --}}

                <div>

                    <label class="mb-2 block text-sm font-medium text-slate-700">
                        Trạng thái
                    </label>

                    <input
                        type="hidden"
                        id="product-status"
                        name="status"
                        value="{{ old('status', 1) ? '1' : '0' }}"
                    >

                    <button
                        type="button"
                        id="product-status-toggle"
                        class="inline-flex items-center gap-3"
                    >

                        <span
                            id="product-status-switch"
                            class="relative inline-flex h-7 w-12 items-center rounded-full bg-[#22C55E] transition"
                        >

                            <span
                                id="product-status-knob"
                                class="inline-block h-6 w-6 translate-x-5 rounded-full bg-white shadow-sm transition"
                            ></span>

                        </span>

                        <span
                            id="product-status-label"
                            class="text-sm font-medium text-[#16A34A]"
                        >
                            Đang bán
                        </span>

                    </button>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- HÌNH ẢNH --}}
        {{-- ===================================================== --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <h2 class="font-semibold text-[#0F172A]">
                    Hình ảnh
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Có thể chọn nhiều ảnh. Ảnh đầu tiên sẽ được dùng làm ảnh đại diện.
                </p>

            </div>


            <div class="p-6">

                <label
                    for="product-images"
                    class="flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-[#F8FAFC] px-6 py-10 text-center transition hover:border-[#22C55E] hover:bg-[#F0FDF4]"
                >

                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white text-2xl shadow-sm">
                        🖼
                    </div>

                    <div class="mt-4 text-sm font-semibold text-slate-700">
                        Thêm hình ảnh sản phẩm
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        JPG, PNG, WEBP · tối đa 5MB mỗi ảnh
                    </div>

                    <span class="mt-4 rounded-lg bg-[#22C55E] px-4 py-2 text-sm font-semibold text-white hover:bg-[#16A34A]">
                        Chọn ảnh
                    </span>

                    <input
                        id="product-images"
                        type="file"
                        name="images[]"
                        multiple
                        accept="image/jpeg,image/png,image/webp"
                        class="hidden"
                    >

                </label>


                <div
                    id="image-preview"
                    class="mt-5 grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6"
                ></div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- THUỘC TÍNH --}}
        {{-- ===================================================== --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-[#E2E8F0] px-6 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="font-semibold text-[#0F172A]">
                        Thuộc tính
                    </h2>

                    <p class="mt-1 text-xs text-slate-400">
                        Thêm bất kỳ loại thuộc tính nào. Nhập giá trị rồi nhấn Enter.
                    </p>

                </div>


                <button
                    type="button"
                    id="add-attribute"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#F0FDF4] px-4 py-2 text-sm font-semibold text-[#16A34A] hover:bg-green-100"
                >
                    + Thêm thuộc tính
                </button>

            </div>


            <div
                id="attributes-container"
                class="space-y-4 p-6"
            >

                <div
                    id="empty-attributes"
                    class="rounded-lg border border-dashed border-slate-300 bg-[#F8FAFC] px-6 py-10 text-center"
                >

                    <div class="text-2xl">
                        ⚙️
                    </div>

                    <div class="mt-2 text-sm font-medium text-slate-700">
                        Chưa có thuộc tính
                    </div>

                    <div class="mt-1 text-xs text-slate-400">
                        Ví dụ: Màu sắc, Kích thước, Chất liệu...
                    </div>

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- BẢNG BIẾN THỂ --}}
        {{-- ===================================================== --}}

        <div
            id="variant-section"
            class="mt-6 hidden rounded-xl border border-[#E2E8F0] bg-white shadow-sm"
        >

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="font-semibold text-[#0F172A]">
                            Danh sách biến thể
                        </h2>

                        <p
                            id="variant-description"
                            class="mt-1 text-xs text-slate-400"
                        >
                            Hệ thống tự sinh các tổ hợp thuộc tính. Mã SKU sẽ được tự sinh khi lưu sản phẩm.
                        </p>

                    </div>

                    <div
                        id="variant-count"
                        class="rounded-full bg-[#F0FDF4] px-3 py-1 text-xs font-semibold text-[#16A34A]"
                    >
                        0 biến thể
                    </div>

                </div>

            </div>


            <div id="variant-warning" class="hidden border-b border-red-200 bg-red-50 px-6 py-3 text-sm text-red-700"></div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[720px] text-sm">

                    <thead>

                        <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">

                            <th class="px-5 py-4 text-left font-semibold text-slate-600">
                                Biến thể
                            </th>

                            <th class="w-40 px-5 py-4 text-right font-semibold text-slate-600">
                                Giá bán
                            </th>

                            <th class="w-36 px-5 py-4 text-center font-semibold text-slate-600">
                                Tồn kho
                            </th>

                            <th class="w-40 px-5 py-4 text-center font-semibold text-slate-600">
                                Trạng thái
                            </th>

                        </tr>

                    </thead>


                    <tbody
                        id="variant-body"
                        class="divide-y divide-[#E2E8F0]"
                    ></tbody>

                </table>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- MÔ TẢ --}}
        {{-- ===================================================== --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <h2 class="font-semibold text-[#0F172A]">
                    Mô tả sản phẩm
                </h2>

            </div>


            <div class="p-6">

                <textarea
                    name="description"
                    rows="6"
                    maxlength="10000"
                    placeholder="Nhập mô tả sản phẩm..."
                    class="w-full rounded-lg border border-[#E2E8F0] px-4 py-3 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                >{{ old('description') }}</textarea>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- ACTION --}}
        {{-- ===================================================== --}}

        <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">

            <button
                type="button"
                id="cancel-button"
                class="rounded-lg border border-[#E2E8F0] bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
            >
                Hủy
            </button>


            <button
                type="submit"
                name="save_action"
                value="save"
                class="save-button rounded-lg border border-[#22C55E] bg-white px-5 py-2.5 text-sm font-semibold text-[#16A34A] transition hover:bg-[#F0FDF4]"
            >
                Lưu
            </button>


            <button
                type="submit"
                name="save_action"
                value="save_and_new"
                class="save-button rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
            >
                Lưu & tạo thêm hàng
            </button>

        </div>

    </form>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<style>
    [data-chip] {
        user-select: none;
    }

    .variant-status-button {
        cursor: pointer;
    }
</style>


<script>

(function () {

    /*
    |--------------------------------------------------------------------------
    | Elements
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById('product-form');

    const attributesContainer =
        document.getElementById(
            'attributes-container'
        );

    const emptyAttributes =
        document.getElementById(
            'empty-attributes'
        );

    const addAttributeButton =
        document.getElementById(
            'add-attribute'
        );

    const variantSection =
        document.getElementById(
            'variant-section'
        );

    const variantBody =
        document.getElementById(
            'variant-body'
        );

    const variantCount =
        document.getElementById(
            'variant-count'
        );

    const variantWarning =
        document.getElementById(
            'variant-warning'
        );

    const baseStockWrapper =
        document.getElementById(
            'base-stock-wrapper'
        );

    const baseStockInput =
        document.getElementById(
            'base-stock'
        );

    const basePriceInput =
        document.getElementById(
            'base-price'
        );

    const productNameInput =
        document.getElementById(
            'product-name'
        );

    const imagesInput =
        document.getElementById(
            'product-images'
        );

    const imagePreview =
        document.getElementById(
            'image-preview'
        );

    const cancelButton =
        document.getElementById(
            'cancel-button'
        );

    const saveButtons =
        document.querySelectorAll(
            '.save-button'
        );

    const formState =
        document.getElementById(
            'form-state'
        );


    /*
    |--------------------------------------------------------------------------
    | State
    |--------------------------------------------------------------------------
    */

    let attributeCounter = 0;

    let isDirty = false;

    const oldAttributes =
        formState.dataset.oldAttributes
            ? JSON.parse(
                formState.dataset.oldAttributes
            )
            : [];

    const oldVariants =
        formState.dataset.oldVariants
            ? JSON.parse(
                formState.dataset.oldVariants
            )
            : [];

    const availableAttributes =
        formState.dataset.availableAttributes
            ? JSON.parse(
                formState.dataset.availableAttributes
            )
            : [];


    /*
    |--------------------------------------------------------------------------
    | Dirty state
    |--------------------------------------------------------------------------
    */

    function markDirty() {
        isDirty = true;
    }

    form.addEventListener(
        'input',
        markDirty
    );

    form.addEventListener(
        'change',
        markDirty
    );


    window.addEventListener(
        'beforeunload',
        function (event) {

            if (!isDirty) {
                return;
            }

            event.preventDefault();

            event.returnValue = '';

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Product status toggle
    |--------------------------------------------------------------------------
    */

    const productStatus =
        document.getElementById(
            'product-status'
        );

    const productStatusToggle =
        document.getElementById(
            'product-status-toggle'
        );

    const productStatusSwitch =
        document.getElementById(
            'product-status-switch'
        );

    const productStatusKnob =
        document.getElementById(
            'product-status-knob'
        );

    const productStatusLabel =
        document.getElementById(
            'product-status-label'
        );


    function renderProductStatus() {

        const active =
            productStatus.value === '1';


        if (active) {

            productStatusSwitch.classList.remove(
                'bg-slate-300'
            );

            productStatusSwitch.classList.add(
                'bg-[#22C55E]'
            );

            productStatusKnob.classList.remove(
                'translate-x-0'
            );

            productStatusKnob.classList.add(
                'translate-x-5'
            );

            productStatusLabel.textContent =
                'Đang bán';

            productStatusLabel.classList.remove(
                'text-slate-500'
            );

            productStatusLabel.classList.add(
                'text-[#16A34A]'
            );

        } else {

            productStatusSwitch.classList.remove(
                'bg-[#22C55E]'
            );

            productStatusSwitch.classList.add(
                'bg-slate-300'
            );

            productStatusKnob.classList.remove(
                'translate-x-5'
            );

            productStatusKnob.classList.add(
                'translate-x-0'
            );

            productStatusLabel.textContent =
                'Ngừng bán';

            productStatusLabel.classList.remove(
                'text-[#16A34A]'
            );

            productStatusLabel.classList.add(
                'text-slate-500'
            );

        }

    }


    productStatusToggle.addEventListener(
        'click',
        function () {

            productStatus.value =
                productStatus.value === '1'
                    ? '0'
                    : '1';

            renderProductStatus();

            markDirty();

        }
    );


    renderProductStatus();


    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    function getAttributeGroups() {

        const groups =
            [];

        document
            .querySelectorAll(
                '.attribute-group'
            )
            .forEach(function (group) {

                const attributeSelect =
                    group.querySelector(
                        '.attribute-name'
                    );

                const selectedOption =
                    attributeSelect?.selectedOptions?.[0];

                const values = [];

                group
                    .querySelectorAll(
                        'input[data-value-hidden]'
                    )
                    .forEach(function (hidden) {

                        values.push(
                            hidden.value
                        );

                    });

                groups.push({
                    element: group,

                    attributeId:
                        attributeSelect
                            ? attributeSelect.value
                            : '',

                    name:
                        selectedOption
                        && attributeSelect.value
                            ? selectedOption.textContent.trim()
                            : '',

                    values:
                        values
                });

            });

        return groups;

    }


    /*
    |--------------------------------------------------------------------------
    | Generate combinations
    |--------------------------------------------------------------------------
    */

    function generateCombinations(
        groups
    ) {

        if (groups.length === 0) {
            return [
                []
            ];
        }


        const invalid =
            groups.some(function (group) {

                return (
                    group.name === ''
                    || group.values.length === 0
                );

            });


        if (invalid) {
            return [];
        }


        let result = [
            []
        ];


        groups.forEach(
            function (group) {

                const next = [];


                result.forEach(
                    function (prefix) {

                        group.values.forEach(
                            function (value) {

                                next.push([
                                    ...prefix,
                                    value
                                ]);

                            }
                        );

                    }
                );


                result = next;

            }
        );


        return result;

    }


    /*
    |--------------------------------------------------------------------------
    | Collect existing variant values
    |--------------------------------------------------------------------------
    */

    function collectCurrentVariants() {

        const values = {};


        variantBody
            .querySelectorAll(
                'tr[data-variant-key]'
            )
            .forEach(function (row) {

                const key =
                    row.dataset.variantKey;


                const price =
                    row.querySelector(
                        '.variant-price'
                    )?.value || '';


                const stock =
                    row.querySelector(
                        '.variant-stock'
                    )?.value || '';


                const status =
                    row.querySelector(
                        '.variant-status'
                    )?.value || '1';


                values[key] = {
                    price,
                    stock,
                    status
                };

            });


        return values;

    }


    /*
    |--------------------------------------------------------------------------
    | Create variant status button
    |--------------------------------------------------------------------------
    */

    function renderVariantStatusButton(
        button,
        active
    ) {

        const knob =
            button.querySelector(
                '.variant-status-knob'
            );

        const label =
            button.querySelector(
                '.variant-status-label'
            );


        if (active) {

            button
                .querySelector(
                    '.variant-status-switch'
                )
                .classList.remove(
                    'bg-slate-300'
                );

            button
                .querySelector(
                    '.variant-status-switch'
                )
                .classList.add(
                    'bg-[#22C55E]'
                );


            knob.classList.remove(
                'translate-x-0'
            );

            knob.classList.add(
                'translate-x-5'
            );


            label.textContent =
                'Đang bán';

            label.classList.remove(
                'text-slate-500'
            );

            label.classList.add(
                'text-[#16A34A]'
            );

        } else {

            button
                .querySelector(
                    '.variant-status-switch'
                )
                .classList.remove(
                    'bg-[#22C55E]'
                );

            button
                .querySelector(
                    '.variant-status-switch'
                )
                .classList.add(
                    'bg-slate-300'
                );


            knob.classList.remove(
                'translate-x-5'
            );

            knob.classList.add(
                'translate-x-0'
            );


            label.textContent =
                'Ngừng bán';

            label.classList.remove(
                'text-[#16A34A]'
            );

            label.classList.add(
                'text-slate-500'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Render variants
    |--------------------------------------------------------------------------
    */

    function renderVariants() {

        const groups =
            getAttributeGroups();


        /*
         * Không có thuộc tính:
         * quản lý tồn kho chung.
         */
        if (groups.length === 0) {

            variantSection.classList.add(
                'hidden'
            );

            baseStockWrapper.classList.remove(
                'hidden'
            );

            variantWarning.classList.add(
                'hidden'
            );

            variantBody.innerHTML = '';

            variantCount.textContent =
                '0 biến thể';

            updateSaveButtons();

            return;

        }


        /*
         * Có group nhưng chưa đủ dữ liệu.
         */
        const hasInvalidGroup =
            groups.some(function (group) {

                return (
                    group.name === ''
                    || group.values.length === 0
                );

            });


        baseStockWrapper.classList.add(
            'hidden'
        );

        variantSection.classList.remove(
            'hidden'
        );


        if (hasInvalidGroup) {

            variantWarning.textContent =
                'Vui lòng nhập tên thuộc tính và ít nhất một giá trị cho mỗi thuộc tính.';

            variantWarning.classList.remove(
                'hidden'
            );

            variantBody.innerHTML = '';

            variantCount.textContent =
                'Chưa tạo';

            updateSaveButtons();

            return;

        }


        variantWarning.classList.add(
            'hidden'
        );


        const combinations =
            generateCombinations(
                groups
            );


        if (combinations.length > 500) {

            variantWarning.textContent =
                'Có quá nhiều tổ hợp biến thể. Vui lòng giảm số lượng giá trị thuộc tính.';

            variantWarning.classList.remove(
                'hidden'
            );

            variantBody.innerHTML = '';

            variantCount.textContent =
                combinations.length +
                ' biến thể';

            updateSaveButtons();

            return;

        }


        const existingVariants =
            collectCurrentVariants();


    variantBody.innerHTML =
        '';


    const productName =
        productNameInput.value.trim()
        || 'Sản phẩm';


    const basePrice =
        basePriceInput.value || '0';


        combinations.forEach(
            function (combination, index) {

                const variantName =
                    [
                        productName,
                        ...combination
                    ].join(' - ');


                const key =
                    combination
                        .map(function (value) {
                            return escapeKey(value);
                        })
                        .join('|||');


                const old =
                    existingVariants[key]
                    || oldVariants[index]
                    || {};


                const price =
                    old.price !== undefined &&
                    old.price !== ''
                        ? old.price
                        : basePrice;


                const stock =
                    old.stock !== undefined &&
                    old.stock !== ''
                        ? old.stock
                        : '0';


                const status =
                    old.status !== undefined
                        ? String(old.status)
                        : '1';


                const row =
                    document.createElement(
                        'tr'
                    );


                row.dataset.variantKey =
                    key;


                row.className =
                    'transition hover:bg-slate-50';


                row.innerHTML = `
                    <td class="px-5 py-4">
                        <div class="font-medium text-[#0F172A]">
                            ${variantName}
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <div class="relative">
                            <input
                                type="number"
                                name="variants[${index}][price]"
                                value="${price}"
                                min="0"
                                step="1000"
                                required
                                class="variant-price w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 pr-10 text-right text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                            >

                            <span class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">
                                ₫
                            </span>
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <input
                            type="number"
                            name="variants[${index}][stock_quantity]"
                            value="${stock}"
                            min="0"
                            step="1"
                            required
                            class="variant-stock w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 text-center text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                        >
                    </td>

                    <td class="px-5 py-4 text-center">

                        <input
                            type="hidden"
                            name="variants[${index}][status]"
                            value="${status}"
                            class="variant-status"
                        >

                        <button
                            type="button"
                            class="variant-status-button inline-flex items-center gap-2"
                        >

                            <span
                                class="variant-status-switch relative inline-flex h-6 w-11 items-center rounded-full transition"
                            >

                                <span
                                    class="variant-status-knob inline-block h-5 w-5 rounded-full bg-white shadow-sm transition"
                                ></span>

                            </span>

                            <span class="variant-status-label text-sm font-medium"></span>

                        </button>

                    </td>
                `;


                const statusButton =
                    row.querySelector(
                        '.variant-status-button'
                    );


                renderVariantStatusButton(
                    statusButton,
                    status === '1'
                );


                statusButton.addEventListener(
                    'click',
                    function () {

                        const input =
                            row.querySelector(
                                '.variant-status'
                            );


                        input.value =
                            input.value === '1'
                                ? '0'
                                : '1';


                        renderVariantStatusButton(
                            statusButton,
                            input.value === '1'
                        );


                        markDirty();

                    }
                );


                row
                    .querySelector(
                        '.variant-price'
                    )
                    .addEventListener(
                        'input',
                        function () {

                            this.dataset.manual =
                                '1';

                            markDirty();

                        }
                    );


                variantBody.appendChild(row);

            }
        );


        variantCount.textContent =
            combinations.length +
            ' biến thể';


        updateSaveButtons();

    }


    /*
    |--------------------------------------------------------------------------
    | Save button state
    |--------------------------------------------------------------------------
    */

    function updateSaveButtons() {

        const groups =
            getAttributeGroups();


        let valid =
            true;


        if (groups.length > 0) {

            const combinations =
                generateCombinations(
                    groups
                );


            valid =
                combinations.length > 0
                && combinations.length <= 500;

        }


        saveButtons.forEach(
            function (button) {

                button.disabled =
                    !valid;


                if (!valid) {

                    button.classList.add(
                        'cursor-not-allowed',
                        'opacity-50'
                    );

                } else {

                    button.classList.remove(
                        'cursor-not-allowed',
                        'opacity-50'
                    );

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Add value chip
    |--------------------------------------------------------------------------
    */

    function addValueChip(
        group,
        value
    ) {

        value =
            value.trim();


        if (!value) {
            return;
        }


        const existing =
            Array.from(
                group.querySelectorAll(
                    'input[data-value-hidden]'
                )
            ).map(
                function (input) {
                    return input.value
                        .toLowerCase();
                }
            );


        if (
            existing.includes(
                value.toLowerCase()
            )
        ) {
            return;
        }


        const groupIndex =
            group.dataset.groupIndex;


        const hidden =
            document.createElement(
                'input'
            );

        hidden.type =
            'hidden';

        hidden.name =
            `attributes[${groupIndex}][values][]`;

        hidden.value =
            value;

        hidden.dataset.valueHidden =
            'true';


        const chip =
            document.createElement(
                'span'
            );


        chip.dataset.chip =
            'true';


        chip.className =
            'inline-flex items-center gap-2 rounded-full bg-[#F0FDF4] px-3 py-1.5 text-sm font-medium text-[#16A34A]';


        const text =
            document.createElement(
                'span'
            );

        text.textContent =
            value;


        const remove =
            document.createElement(
                'button'
            );

        remove.type =
            'button';

        remove.className =
            'font-bold text-green-700 hover:text-red-500';

        remove.textContent =
            '×';


        remove.addEventListener(
            'click',
            function () {

                hidden.remove();

                chip.remove();

                renderVariants();

                markDirty();

            }
        );


        chip.appendChild(
            text
        );

        chip.appendChild(
            remove
        );


        const chipsContainer =
            group.querySelector(
                '.attribute-chips'
            );


        chipsContainer.appendChild(
            chip
        );

        group.appendChild(
            hidden
        );


        renderVariants();

        markDirty();

    }


    /*
    |--------------------------------------------------------------------------
    | Add Attribute Group
    |--------------------------------------------------------------------------
    */

    function buildAttributeOptions(
        selectedId = ''
    ) {

        const options =
            ['<option value="">Chọn thuộc tính</option>'];

        availableAttributes.forEach(
            function (attribute) {

                const selected =
                    String(attribute.id)
                    === String(selectedId)
                        ? 'selected'
                        : '';

                options.push(
                    `<option value="${attribute.id}" ${selected}>${escapeHtml(attribute.name)}</option>`
                );

            }
        );

        return options.join('');

    }


    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }


    function openAttributeModal(
        targetGroup
    ) {

        attributeModalTargetGroup =
            targetGroup;

        inlineAttributeNameInput.value =
            '';

        inlineAttributeError.classList.add(
            'hidden'
        );

        openModal(attributeModal);

        setTimeout(function () {
            inlineAttributeNameInput.focus();
        }, 50);

    }


    function refreshAttributeSelects() {

        const selects =
            Array.from(
                document.querySelectorAll(
                    '.attribute-name'
                )
            );


        const selectedIds =
            selects
                .map(function (select) {
                    return String(select.value || '');
                })
                .filter(function (value) {
                    return value !== '';
                });


        selects.forEach(function (select) {

            const currentId =
                String(select.value || '');


            Array.from(select.options).forEach(function (option) {

                if (!option.value) {
                    option.disabled = false;
                    return;
                }

                const optionId =
                    String(option.value);

                option.disabled =
                    selectedIds.includes(optionId)
                    && optionId !== currentId;

            });

        });

    }


    function addAttributeOptionToAllSelects(
        attribute
    ) {

        const alreadyExists =
            availableAttributes.some(function (item) {
                return String(item.id) === String(attribute.id);
            });


        if (!alreadyExists) {
            availableAttributes.push(attribute);
        }


        document
            .querySelectorAll(
                '.attribute-name'
            )
            .forEach(function (select) {

                const exists =
                    Array.from(
                        select.options
                    ).some(function (option) {

                        return (
                            String(option.value)
                            === String(attribute.id)
                        );

                    });

                if (!exists) {

                    select.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${attribute.id}">${escapeHtml(attribute.name)}</option>`
                    );

                }

            });


        refreshAttributeSelects();

    }


    /*
    |--------------------------------------------------------------------------
    | Add Attribute Group
    |--------------------------------------------------------------------------
    */

    function addAttributeGroup(
        initial = null
    ) {

        const index =
            attributeCounter++;

        emptyAttributes.classList.add(
            'hidden'
        );


        const group =
            document.createElement(
                'div'
            );


        group.dataset.groupIndex =
            index;

        group.className =
            'attribute-group rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-5';


        const selectedAttributeId =
            initial?.attribute_id
                ?? '';


        group.innerHTML = `

            <div class="flex items-start gap-4">

                <div class="flex-1">

                    <label class="mb-2 block text-sm font-semibold text-slate-700">
                        Tên thuộc tính
                    </label>

                    <div class="flex items-center gap-2">

                        <select
                            name="attributes[${index}][attribute_id]"
                            class="attribute-name min-w-0 flex-1 rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                        >
                            ${buildAttributeOptions(selectedAttributeId)}
                        </select>

                        <button
                            type="button"
                            class="add-inline-attribute inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-[#BBF7D0] bg-white text-xl font-medium text-[#16A34A] transition hover:bg-[#F0FDF4]"
                            title="Thêm tên thuộc tính"
                            aria-label="Thêm tên thuộc tính"
                        >
                            +
                        </button>

                    </div>

                </div>

                <button
                    type="button"
                    class="remove-attribute mt-7 rounded-lg px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-50"
                >
                    Xóa
                </button>

            </div>


            <div class="mt-5">

                <label class="mb-2 block text-sm font-semibold text-slate-700">
                    Giá trị
                </label>

                <div class="attribute-chips flex min-h-10 flex-wrap gap-2">
                </div>

                <input
                    type="text"
                    class="attribute-value-input mt-3 w-full rounded-lg border border-[#E2E8F0] bg-white px-4 py-2.5 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                    placeholder="Nhập giá trị rồi nhấn Enter..."
                >

                <p class="mt-1.5 text-xs text-slate-400">
                    Có thể nhập nhiều giá trị. Ví dụ: Đen → Enter, Trắng → Enter.
                </p>

            </div>

        `;


        const attributeSelect =
            group.querySelector(
                '.attribute-name'
            );


        const valueInput =
            group.querySelector(
                '.attribute-value-input'
            );


        const removeButton =
            group.querySelector(
                '.remove-attribute'
            );


        const inlineAddButton =
            group.querySelector(
                '.add-inline-attribute'
            );


        attributeSelect.addEventListener(
            'change',
            function () {

                const selectedId =
                    String(attributeSelect.value || '');


                if (selectedId) {

                    const duplicate =
                        Array.from(
                            document.querySelectorAll(
                                '.attribute-name'
                            )
                        ).some(function (select) {

                            return (
                                select !== attributeSelect
                                && String(select.value || '') === selectedId
                            );

                        });


                    if (duplicate) {

                        alert(
                            'Thuộc tính này đã được chọn ở nhóm khác. Vui lòng chọn thuộc tính khác.'
                        );

                        attributeSelect.value = '';

                    }

                }


                refreshAttributeSelects();

                renderVariants();

                markDirty();

            }
        );


        inlineAddButton.addEventListener(
            'click',
            function () {

                openAttributeModal(
                    group
                );

            }
        );


        valueInput.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Enter'
                    || event.key === ','
                ) {

                    event.preventDefault();

                    const parts =
                        valueInput.value
                            .split(',');

                    parts.forEach(
                        function (part) {

                            addValueChip(
                                group,
                                part
                            );

                        }
                    );

                    valueInput.value = '';

                }

            }
        );


        removeButton.addEventListener(
            'click',
            function () {

                group.remove();

                if (
                    document.querySelectorAll(
                        '.attribute-group'
                    ).length === 0
                ) {

                    emptyAttributes.classList.remove(
                        'hidden'
                    );

                }

                refreshAttributeSelects();

                renderVariants();

                markDirty();

            }
        );


        attributesContainer.appendChild(
            group
        );


        refreshAttributeSelects();


        /*
         * Khôi phục dữ liệu cũ.
         */
        if (initial) {

            let initialAttributeId =
                initial.attribute_id
                    ?? '';


            /*
             * Hỗ trợ dữ liệu cũ còn lưu name.
             */
            if (
                !initialAttributeId
                && initial.name
            ) {

                const found =
                    availableAttributes.find(
                        function (attribute) {

                            return (
                                attribute.name
                                    .trim()
                                    .toLowerCase()
                                ===
                                String(initial.name)
                                    .trim()
                                    .toLowerCase()
                            );

                        }
                    );

                if (found) {
                    initialAttributeId =
                        found.id;
                }

            }


            attributeSelect.value =
                initialAttributeId
                ? String(initialAttributeId)
                : '';


            refreshAttributeSelects();


            const values =
                Array.isArray(
                    initial.values
                )
                    ? initial.values
                    : [];


            values.forEach(
                function (value) {

                    addValueChip(
                        group,
                        value
                    );

                }
            );

        }


        renderVariants();

    }


    /*
    |--------------------------------------------------------------------------
    | Add Attribute button
    |--------------------------------------------------------------------------
    */

    addAttributeButton.addEventListener(
        'click',
        function () {

            addAttributeGroup();

            markDirty();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Product name changes variant labels
    |--------------------------------------------------------------------------
    */

    productNameInput.addEventListener(
        'input',
        function () {

            const groups =
                getAttributeGroups();


            if (
                groups.length > 0
                && variantBody.children.length > 0
            ) {

                const combinations =
                    generateCombinations(
                        groups
                    );


                Array.from(
                    variantBody.children
                ).forEach(
                    function (row, index) {

                        const span =
                            row.querySelector(
                                '.font-medium'
                            );

                        if (!span) {
                            return;
                        }

                        const combination =
                            combinations[index] || [];


                        span.textContent =
                            [
                                productNameInput.value.trim()
                                    || 'Sản phẩm',
                                ...combination
                            ].join(' - ');

                    }
                );

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Base price changes auto prices
    |--------------------------------------------------------------------------
    */

    basePriceInput.addEventListener(
        'input',
        function () {

            variantBody
                .querySelectorAll(
                    '.variant-price'
                )
                .forEach(
                    function (input) {

                        if (
                            input.dataset.manual !== '1'
                        ) {

                            input.value =
                                basePriceInput.value;

                        }

                    }
                );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Image preview
    |--------------------------------------------------------------------------
    */

    let selectedImageFiles =
        [];


    function syncImageInput() {

        const dataTransfer =
            new DataTransfer();

        selectedImageFiles.forEach(
            function (file) {

                dataTransfer.items.add(
                    file
                );

            }
        );

        imagesInput.files =
            dataTransfer.files;

    }


    function renderImagePreview() {

        imagePreview.innerHTML =
            '';


        selectedImageFiles.forEach(
            function (file, index) {

                const reader =
                    new FileReader();


                reader.onload =
                    function (event) {

                        const wrapper =
                            document.createElement(
                                'div'
                            );


                        wrapper.className =
                            'relative overflow-hidden rounded-xl border border-[#E2E8F0] bg-white';


                        wrapper.innerHTML = `
                            <button
                                type="button"
                                class="remove-image absolute right-2 top-2 z-10 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-lg font-medium text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-red-50 hover:text-red-500"
                                data-image-index="${index}"
                                title="Xóa ảnh"
                                aria-label="Xóa ảnh"
                            >
                                ×
                            </button>

                            <div class="aspect-square bg-slate-50">
                                <img
                                    src="${event.target.result}"
                                    alt="Ảnh ${index + 1}"
                                    class="h-full w-full object-cover"
                                >
                            </div>

                            <div class="px-3 py-2 text-xs text-slate-500">
                                ${index === 0 ? 'Ảnh đại diện' : 'Ảnh ' + (index + 1)}
                            </div>
                        `;


                        const removeButton =
                            wrapper.querySelector(
                                '.remove-image'
                            );


                        removeButton.addEventListener(
                            'click',
                            function () {

                                const removeIndex =
                                    Number(
                                        this.dataset.imageIndex
                                    );


                                selectedImageFiles
                                    .splice(
                                        removeIndex,
                                        1
                                    );


                                syncImageInput();

                                renderImagePreview();

                                markDirty();

                            }
                        );


                        imagePreview.appendChild(
                            wrapper
                        );

                    };


                reader.readAsDataURL(
                    file
                );

            }
        );

    }


    imagesInput.addEventListener(
        'change',
        function () {

            const newFiles =
                Array.from(
                    imagesInput.files
                );


            newFiles.forEach(
                function (file) {

                    const exists =
                        selectedImageFiles.some(
                            function (currentFile) {

                                return (
                                    currentFile.name
                                    === file.name
                                    &&
                                    currentFile.size
                                    === file.size
                                    &&
                                    currentFile.lastModified
                                    === file.lastModified
                                );

                            }
                        );


                    if (!exists) {

                        selectedImageFiles.push(
                            file
                        );

                    }

                }
            );


            syncImageInput();

            renderImagePreview();

            markDirty();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Inline category / attribute modals
    |--------------------------------------------------------------------------
    */

    const categoryModal =
        document.getElementById(
            'category-modal'
        );

    const attributeModal =
        document.getElementById(
            'attribute-modal'
        );

    const addCategoryInlineButton =
        document.getElementById(
            'add-category-inline'
        );

    const inlineCategoryForm =
        document.getElementById(
            'inline-category-form'
        );

    const inlineCategoryNameInput =
        document.getElementById(
            'inline-category-name'
        );

    const inlineCategoryError =
        document.getElementById(
            'inline-category-error'
        );

    const inlineCategorySubmit =
        document.getElementById(
            'inline-category-submit'
        );

    const categorySelect =
        document.getElementById(
            'category-select'
        );

    const inlineAttributeForm =
        document.getElementById(
            'inline-attribute-form'
        );

    const inlineAttributeNameInput =
        document.getElementById(
            'inline-attribute-name'
        );

    const inlineAttributeError =
        document.getElementById(
            'inline-attribute-error'
        );

    const inlineAttributeSubmit =
        document.getElementById(
            'inline-attribute-submit'
        );

    let attributeModalTargetGroup =
        null;


    function openModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.remove(
            'hidden'
        );

        modal.classList.add(
            'flex'
        );

    }


    function closeModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.add(
            'hidden'
        );

        modal.classList.remove(
            'flex'
        );

    }


    document
        .querySelectorAll(
            '[data-close-modal]'
        )
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    closeModal(
                        document.getElementById(
                            this.dataset.closeModal
                        )
                    );

                }
            );

        });


    [categoryModal, attributeModal]
        .forEach(function (modal) {

            modal?.addEventListener(
                'click',
                function (event) {

                    if (
                        event.target === modal
                    ) {

                        closeModal(
                            modal
                        );

                    }

                }
            );

        });


    addCategoryInlineButton.addEventListener(
        'click',
        function () {

            inlineCategoryNameInput.value =
                '';

            inlineCategoryError.classList.add(
                'hidden'
            );

            openModal(
                categoryModal
            );

            setTimeout(function () {
                inlineCategoryNameInput.focus();
            }, 50);

        }
    );


    inlineCategoryForm.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();

            inlineCategoryError.classList.add(
                'hidden'
            );

            inlineCategorySubmit.disabled =
                true;

            inlineCategorySubmit.textContent =
                'Đang lưu...';

            try {

                const response =
                    await fetch(
                        "{{ route('admin.products.inline-category') }}",
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'input[name="_token"]'
                                        )
                                        ?.value
                                    || ''
                            },

                            body: new FormData(
                                inlineCategoryForm
                            )
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        data.message
                        || data.errors?.name?.[0]
                        || 'Không thể thêm danh mục.'
                    );

                }


                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    data.category.id;

                option.textContent =
                    data.category.name;


                categorySelect.appendChild(
                    option
                );


                categorySelect.value =
                    String(
                        data.category.id
                    );


                closeModal(
                    categoryModal
                );

                markDirty();

            } catch (error) {

                inlineCategoryError.textContent =
                    error.message;

                inlineCategoryError.classList.remove(
                    'hidden'
                );

            } finally {

                inlineCategorySubmit.disabled =
                    false;

                inlineCategorySubmit.textContent =
                    'Lưu danh mục';

            }

        }
    );


    inlineAttributeForm.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();

            inlineAttributeError.classList.add(
                'hidden'
            );


            if (!attributeModalTargetGroup) {

                inlineAttributeError.textContent =
                    'Không xác định được nhóm thuộc tính.';

                inlineAttributeError.classList.remove(
                    'hidden'
                );

                return;

            }


            inlineAttributeSubmit.disabled =
                true;

            inlineAttributeSubmit.textContent =
                'Đang lưu...';


            try {

                const response =
                    await fetch(
                        "{{ route('admin.products.inline-attribute') }}",
                        {
                            method: 'POST',

                            headers: {
                                'Accept':
                                    'application/json',

                                'X-CSRF-TOKEN':
                                    document
                                        .querySelector(
                                            'input[name="_token"]'
                                        )
                                        ?.value
                                    || ''
                            },

                            body: new FormData(
                                inlineAttributeForm
                            )
                        }
                    );


                const data =
                    await response.json();


                if (!response.ok) {

                    throw new Error(
                        data.message
                        || data.errors?.name?.[0]
                        || 'Không thể thêm thuộc tính.'
                    );

                }


                addAttributeOptionToAllSelects(
                    data.attribute
                );


                const targetSelect =
                    attributeModalTargetGroup
                        .querySelector(
                            '.attribute-name'
                        );


                targetSelect.value =
                    String(
                        data.attribute.id
                    );


                closeModal(
                    attributeModal
                );


                renderVariants();

                markDirty();

            } catch (error) {

                inlineAttributeError.textContent =
                    error.message;

                inlineAttributeError.classList.remove(
                    'hidden'
                );

            } finally {

                inlineAttributeSubmit.disabled =
                    false;

                inlineAttributeSubmit.textContent =
                    'Lưu thuộc tính';

            }

        }
    );


    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key !== 'Escape'
            ) {
                return;
            }

            closeModal(
                categoryModal
            );

            closeModal(
                attributeModal
            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Cancel
    |--------------------------------------------------------------------------
    */

    cancelButton.addEventListener(
        'click',
        function () {

            const url =
                "{{ route('admin.products.index') }}";


            if (!isDirty) {

                window.location.href =
                    url;

                return;

            }


            const confirmed =
                window.confirm(
                    'Bạn có dữ liệu chưa lưu. Bạn có chắc muốn hủy?'
                );


            if (confirmed) {

                isDirty = false;

                window.location.href =
                    url;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Submit validation
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        function (event) {

            const groups =
                getAttributeGroups();


            if (groups.length > 0) {

                const invalid =
                    groups.some(
                        function (group) {

                            return (
                                group.attributeId === ''
                                || group.values.length === 0
                            );

                        }
                    );


                if (invalid) {

                    event.preventDefault();

                    alert(
                        'Vui lòng chọn tên thuộc tính và hoàn thiện giá trị cho tất cả nhóm trước khi lưu.'
                    );

                    return;

                }


                const combinations =
                    generateCombinations(
                        groups
                    );


                if (
                    combinations.length === 0
                    || combinations.length > 500
                ) {

                    event.preventDefault();

                    alert(
                        'Danh sách biến thể không hợp lệ.'
                    );

                    return;

                }

            }


            isDirty =
                false;

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Load old data after validation error
    |--------------------------------------------------------------------------
    */

    if (
        Array.isArray(oldAttributes)
        && oldAttributes.length > 0
    ) {

        oldAttributes.forEach(
            function (attribute) {

                addAttributeGroup(
                    attribute
                );

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | Initial state
    |--------------------------------------------------------------------------
    */

    refreshAttributeSelects();

    renderVariants();

})();

</script>

@endsection