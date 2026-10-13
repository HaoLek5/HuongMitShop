@extends('layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')

@php
    /*
     * Chỉ lấy dữ liệu cũ của các field được phép chỉnh sửa.
     * Không có attributes trong form submit.
     */
    $oldVariants = old('variants', []);

    $oldVariantById = collect($oldVariants)
        ->filter(function ($row) {
            return isset($row['id']);
        })
        ->keyBy(function ($row) {
            return (int) $row['id'];
        });

    $oldDeletedImageIds = collect(
        old('delete_image_ids', [])
    )
        ->map(function ($id) {
            return (int) $id;
        })
        ->unique()
        ->values()
        ->all();

    $hasVariants = $product->variants->isNotEmpty();
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
                    Sửa sản phẩm
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Cập nhật thông tin sản phẩm, hình ảnh và giá / tồn kho của biến thể.
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

    @if(session('success'))

        <div class="rounded-xl border border-green-200 bg-[#F0FDF4] px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>

    @endif


    @if($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

            <div class="font-semibold">
                Không thể lưu thay đổi
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
    {{-- INLINE CATEGORY MODAL --}}
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


    {{-- ========================================================= --}}
    {{-- FORM --}}
    {{-- ========================================================= --}}

    <form
        id="product-form"
        method="POST"
        action="{{ route('admin.products.update', $product) }}"
        enctype="multipart/form-data"
    >

        @csrf
        @method('PUT')


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
                        Mã hàng không thể chỉnh sửa.
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
                        value="{{ old('name', $product->name) }}"
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
                                    {{ (string) old('category_id', $product->category_id) === (string) $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}

                                    @if(!$category->status)
                                        (Ngừng hoạt động)
                                    @endif
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
                            value="{{ old('base_price', $product->base_price) }}"
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
                        Giá cơ bản của sản phẩm. Giá của từng biến thể được quản lý riêng bên dưới.
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
                        value="{{ old('status', $product->status ? 1 : 0) ? '1' : '0' }}"
                    >

                    <button
                        type="button"
                        id="product-status-toggle"
                        class="inline-flex items-center gap-3"
                    >

                        <span
                            id="product-status-switch"
                            class="relative inline-flex h-7 w-12 items-center rounded-full transition"
                        >

                            <span
                                id="product-status-knob"
                                class="inline-block h-6 w-6 rounded-full bg-white shadow-sm transition"
                            ></span>

                        </span>

                        <span
                            id="product-status-label"
                            class="text-sm font-medium"
                        ></span>

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
                    Xóa ảnh cũ bằng × hoặc thêm ảnh mới. Ảnh đầu tiên còn lại sẽ là ảnh đại diện.
                </p>

            </div>


            <div class="p-6">

                {{-- Ảnh hiện tại --}}
                <div
                    id="existing-image-preview"
                    class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-6"
                >

                    @forelse($product->images as $image)

                        @if(!in_array($image->id, $oldDeletedImageIds, true))

                            <div
                                class="existing-image-card relative overflow-hidden rounded-xl border border-[#E2E8F0] bg-white"
                                data-image-id="{{ $image->id }}"
                            >

                                <button
                                    type="button"
                                    class="remove-existing-image absolute right-2 top-2 z-10 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-lg font-medium text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-red-50 hover:text-red-500"
                                    data-image-id="{{ $image->id }}"
                                    title="Xóa ảnh"
                                    aria-label="Xóa ảnh"
                                >
                                    ×
                                </button>

                                <div class="aspect-square bg-slate-50">
                                    <img
                                        src="{{ $image->image_url }}"
                                        alt="Ảnh sản phẩm"
                                        class="h-full w-full object-cover"
                                    >
                                </div>

                                <div class="px-3 py-2 text-xs text-slate-500">
                                    {{ $image->is_primary ? 'Ảnh đại diện' : 'Ảnh sản phẩm' }}
                                </div>

                            </div>

                        @endif

                    @empty

                        <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-[#F8FAFC] px-6 py-8 text-center text-sm text-slate-500">
                            Sản phẩm chưa có hình ảnh.
                        </div>

                    @endforelse

                </div>


                {{-- Ảnh mới --}}
                <div class="mt-6">

                    <label
                        for="product-images"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border border-dashed border-slate-300 bg-[#F8FAFC] px-6 py-8 text-center transition hover:border-[#22C55E] hover:bg-[#F0FDF4]"
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


                <div
                    id="delete-image-container"
                    class="hidden"
                >

                    @foreach($oldDeletedImageIds as $imageId)
                        <input
                            type="hidden"
                            name="delete_image_ids[]"
                            value="{{ $imageId }}"
                        >
                    @endforeach

                </div>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- THUỘC TÍNH --}}
        {{-- ===================================================== --}}
        {{--
         | CỐ Ý KHÔNG CÓ INPUT Ở ĐÂY.
         | Thuộc tính chỉ xem, không được sửa sau khi sản phẩm đã tạo.
         --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <h2 class="font-semibold text-[#0F172A]">
                    Thuộc tính
                </h2>

                <p class="mt-1 text-xs text-slate-400">
                    Thuộc tính của sản phẩm chỉ được khai báo khi tạo sản phẩm và không thể chỉnh sửa tại đây.
                </p>

            </div>


            <div class="p-6">

                @php
                    $groupedAttributes = [];

                    foreach ($product->variants as $variant) {
                        foreach ($variant->attributeValues as $attributeValue) {
                            $attributeId = (int) $attributeValue->attribute_id;

                            if (!isset($groupedAttributes[$attributeId])) {
                                $groupedAttributes[$attributeId] = [
                                    'name' => $attributeValue->attribute?->name ?? 'Thuộc tính',
                                    'values' => [],
                                ];
                            }

                            $value = trim((string) $attributeValue->value);

                            if (
                                $value !== ''
                                && !collect($groupedAttributes[$attributeId]['values'])
                                    ->contains(function ($existingValue) use ($value) {
                                        return mb_strtolower($existingValue) === mb_strtolower($value);
                                    })
                            ) {
                                $groupedAttributes[$attributeId]['values'][] = $value;
                            }
                        }
                    }
                @endphp

                @if(count($groupedAttributes) > 0)

                    <div class="space-y-4">

                        @foreach($groupedAttributes as $group)

                            <div class="rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] p-5">

                                <div class="text-sm font-semibold text-slate-700">
                                    {{ $group['name'] }}
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">

                                    @foreach($group['values'] as $value)

                                        <span class="inline-flex items-center rounded-full bg-[#F0FDF4] px-3 py-1.5 text-sm font-medium text-[#16A34A]">
                                            {{ $value }}
                                        </span>

                                    @endforeach

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="rounded-xl border border-dashed border-slate-300 bg-[#F8FAFC] px-6 py-8 text-center text-sm text-slate-500">
                        Sản phẩm này không có thuộc tính.
                    </div>

                @endif

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- BẢNG BIẾN THỂ --}}
        {{-- ===================================================== --}}

        <div class="mt-6 rounded-xl border border-[#E2E8F0] bg-white shadow-sm">

            <div class="border-b border-[#E2E8F0] px-6 py-4">

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h2 class="font-semibold text-[#0F172A]">
                            Danh sách biến thể
                        </h2>

                        <p class="mt-1 text-xs text-slate-400">
                            SKU và cấu trúc thuộc tính được giữ nguyên. Chỉ chỉnh giá bán, tồn kho và trạng thái.
                        </p>

                    </div>

                    <div class="rounded-full bg-[#F0FDF4] px-3 py-1 text-xs font-semibold text-[#16A34A]">
                        {{ $product->variants->count() }} biến thể
                    </div>

                </div>

            </div>


            @if($hasVariants)

                <div class="overflow-x-auto">

                    <table class="w-full min-w-[900px] text-sm">

                        <thead>

                            <tr class="border-b border-[#E2E8F0] bg-[#F8FAFC]">

                                <th class="w-44 px-5 py-4 text-left font-semibold text-slate-600">
                                    Mã SKU
                                </th>

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
                        >

                            @foreach($product->variants->sortBy('id') as $variant)

                                @php
                                    $variantOld = $oldVariantById->get((int) $variant->id, []);

                                    $variantPrice = array_key_exists('price', $variantOld)
                                        ? $variantOld['price']
                                        : $variant->price;

                                    $variantStock = array_key_exists('stock_quantity', $variantOld)
                                        ? $variantOld['stock_quantity']
                                        : $variant->stock_quantity;

                                    $variantStatus = array_key_exists('status', $variantOld)
                                        ? (string) $variantOld['status']
                                        : ($variant->status ? '1' : '0');

                                    $variantValues = $variant->attributeValues
                                        ->sortBy(function ($attributeValue) {
                                            return [
                                                $attributeValue->attribute_id,
                                                $attributeValue->sort_order ?? 0,
                                                $attributeValue->id,
                                            ];
                                        })
                                        ->pluck('value')
                                        ->filter()
                                        ->values();

                                    $variantName = $variantValues->isNotEmpty()
                                        ? $variantValues->join(' - ')
                                        : 'Biến thể mặc định';
                                @endphp

                                <tr class="transition hover:bg-slate-50">

                                    <td class="px-5 py-4 align-middle">

                                        <input
                                            type="hidden"
                                            name="variants[{{ $variant->id }}][id]"
                                            value="{{ $variant->id }}"
                                        >

                                        <span class="font-medium text-slate-700">
                                            {{ $variant->sku }}
                                        </span>

                                    </td>


                                    <td class="px-5 py-4 align-middle">

                                        <div class="font-medium text-[#0F172A]">
                                            {{ $product->name }} - {{ $variantName }}
                                        </div>

                                    </td>


                                    <td class="px-5 py-4 align-middle">

                                        <div class="relative">

                                            <input
                                                type="number"
                                                name="variants[{{ $variant->id }}][price]"
                                                value="{{ $variantPrice }}"
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


                                    <td class="px-5 py-4 align-middle">

                                        <input
                                            type="number"
                                            name="variants[{{ $variant->id }}][stock_quantity]"
                                            value="{{ $variantStock }}"
                                            min="0"
                                            step="1"
                                            required
                                            class="variant-stock w-full rounded-lg border border-[#E2E8F0] bg-white px-3 py-2 text-center text-sm text-slate-700 outline-none focus:border-[#22C55E] focus:ring-2 focus:ring-green-100"
                                        >

                                    </td>


                                    <td class="px-5 py-4 align-middle text-center">

                                        <input
                                            type="hidden"
                                            name="variants[{{ $variant->id }}][status]"
                                            value="{{ $variantStatus }}"
                                            class="variant-status"
                                        >

                                        <button
                                            type="button"
                                            class="variant-status-button inline-flex items-center gap-2"
                                            data-variant-id="{{ $variant->id }}"
                                        >

                                            <span class="variant-status-switch relative inline-flex h-6 w-11 items-center rounded-full transition">

                                                <span class="variant-status-knob inline-block h-5 w-5 rounded-full bg-white shadow-sm transition"></span>

                                            </span>

                                            <span class="variant-status-label text-sm font-medium"></span>

                                        </button>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="p-6">

                    <div class="rounded-xl border border-dashed border-slate-300 bg-[#F8FAFC] px-6 py-8 text-center text-sm text-slate-500">
                        Sản phẩm chưa có biến thể để chỉnh sửa.
                    </div>

                </div>

            @endif

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
                >{{ old('description', $product->description) }}</textarea>

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
                class="save-button inline-flex items-center justify-center rounded-lg bg-[#22C55E] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#16A34A]"
            >
                Lưu thay đổi
            </button>

        </div>

    </form>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================= --}}

<style>
    .variant-status-button {
        cursor: pointer;
    }
</style>


<script>

(function () {

    const form =
        document.getElementById('product-form');

    const categoryModal =
        document.getElementById('category-modal');

    const categorySelect =
        document.getElementById('category-select');

    const addCategoryInlineButton =
        document.getElementById('add-category-inline');

    const inlineCategoryForm =
        document.getElementById('inline-category-form');

    const inlineCategoryNameInput =
        document.getElementById('inline-category-name');

    const inlineCategoryError =
        document.getElementById('inline-category-error');

    const inlineCategorySubmit =
        document.getElementById('inline-category-submit');

    const cancelButton =
        document.getElementById('cancel-button');

    const productStatus =
        document.getElementById('product-status');

    const productStatusToggle =
        document.getElementById('product-status-toggle');

    const productStatusSwitch =
        document.getElementById('product-status-switch');

    const productStatusKnob =
        document.getElementById('product-status-knob');

    const productStatusLabel =
        document.getElementById('product-status-label');

    const existingImagePreview =
        document.getElementById('existing-image-preview');

    const deleteImageContainer =
        document.getElementById('delete-image-container');

    const imagesInput =
        document.getElementById('product-images');

    const imagePreview =
        document.getElementById('image-preview');


    let isDirty = false;
    let selectedImageFiles = [];


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
    | Product status
    |--------------------------------------------------------------------------
    */

    function renderProductStatus() {

        const active =
            productStatus.value === '1';

        productStatusSwitch.classList.toggle(
            'bg-[#22C55E]',
            active
        );

        productStatusSwitch.classList.toggle(
            'bg-slate-300',
            !active
        );

        productStatusKnob.classList.toggle(
            'translate-x-5',
            active
        );

        productStatusKnob.classList.toggle(
            'translate-x-0',
            !active
        );

        productStatusLabel.textContent =
            active
                ? 'Đang bán'
                : 'Ngừng bán';

        productStatusLabel.classList.toggle(
            'text-[#16A34A]',
            active
        );

        productStatusLabel.classList.toggle(
            'text-slate-500',
            !active
        );

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
    | Variant status buttons
    |--------------------------------------------------------------------------
    */

    function renderVariantStatusButton(
        button,
        active
    ) {

        const switchElement =
            button.querySelector(
                '.variant-status-switch'
            );

        const knob =
            button.querySelector(
                '.variant-status-knob'
            );

        const label =
            button.querySelector(
                '.variant-status-label'
            );

        switchElement.classList.toggle(
            'bg-[#22C55E]',
            active
        );

        switchElement.classList.toggle(
            'bg-slate-300',
            !active
        );

        knob.classList.toggle(
            'translate-x-5',
            active
        );

        knob.classList.toggle(
            'translate-x-0',
            !active
        );

        label.textContent =
            active
                ? 'Đang bán'
                : 'Ngừng bán';

        label.classList.toggle(
            'text-[#16A34A]',
            active
        );

        label.classList.toggle(
            'text-slate-500',
            !active
        );

    }


    document
        .querySelectorAll('.variant-status-button')
        .forEach(function (button) {

            const row =
                button.closest('tr');

            const input =
                row.querySelector('.variant-status');

            renderVariantStatusButton(
                button,
                input.value === '1'
            );

            button.addEventListener(
                'click',
                function () {

                    input.value =
                        input.value === '1'
                            ? '0'
                            : '1';

                    renderVariantStatusButton(
                        button,
                        input.value === '1'
                    );

                    markDirty();

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | Existing image delete
    |--------------------------------------------------------------------------
    */

    function addDeleteImageInput(imageId) {

        if (!deleteImageContainer) {
            return;
        }

        const alreadyExists =
            Array.from(
                deleteImageContainer.querySelectorAll(
                    'input[name="delete_image_ids[]"]'
                )
            ).some(function (input) {
                return input.value === String(imageId);
            });

        if (alreadyExists) {
            return;
        }

        const input =
            document.createElement('input');

        input.type = 'hidden';
        input.name = 'delete_image_ids[]';
        input.value = imageId;

        deleteImageContainer.appendChild(input);

    }


    existingImagePreview
        ?.querySelectorAll('.remove-existing-image')
        .forEach(function (button) {

            button.addEventListener(
                'click',
                function () {

                    const imageId =
                        this.dataset.imageId;

                    const card =
                        this.closest('.existing-image-card');

                    addDeleteImageInput(imageId);

                    card?.remove();

                    markDirty();

                }
            );

        });


    /*
    |--------------------------------------------------------------------------
    | New image selection
    |--------------------------------------------------------------------------
    */

    function syncImageInput() {

        const dataTransfer =
            new DataTransfer();

        selectedImageFiles.forEach(function (file) {
            dataTransfer.items.add(file);
        });

        imagesInput.files =
            dataTransfer.files;

    }


    function renderImagePreview() {

        imagePreview.innerHTML = '';

        selectedImageFiles.forEach(
            function (file, index) {

                const reader =
                    new FileReader();

                reader.onload =
                    function (event) {

                        const wrapper =
                            document.createElement('div');

                        wrapper.className =
                            'relative overflow-hidden rounded-xl border border-[#E2E8F0] bg-white';

                        wrapper.innerHTML = `
                            <button
                                type="button"
                                class="remove-new-image absolute right-2 top-2 z-10 inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/95 text-lg font-medium text-slate-500 shadow-sm ring-1 ring-slate-200 transition hover:bg-red-50 hover:text-red-500"
                                data-image-index="${index}"
                                title="Xóa ảnh"
                                aria-label="Xóa ảnh"
                            >
                                ×
                            </button>

                            <div class="aspect-square bg-slate-50">
                                <img
                                    src="${event.target.result}"
                                    alt="Ảnh mới ${index + 1}"
                                    class="h-full w-full object-cover"
                                >
                            </div>

                            <div class="px-3 py-2 text-xs text-slate-500">
                                Ảnh mới ${index + 1}
                            </div>
                        `;

                        wrapper
                            .querySelector('.remove-new-image')
                            .addEventListener(
                                'click',
                                function () {

                                    const removeIndex =
                                        Number(
                                            this.dataset.imageIndex
                                        );

                                    selectedImageFiles.splice(
                                        removeIndex,
                                        1
                                    );

                                    syncImageInput();
                                    renderImagePreview();
                                    markDirty();

                                }
                            );

                        imagePreview.appendChild(wrapper);

                    };

                reader.readAsDataURL(file);

            }
        );

    }


    imagesInput.addEventListener(
        'change',
        function () {

            Array.from(imagesInput.files)
                .forEach(function (file) {

                    const duplicate =
                        selectedImageFiles.some(
                            function (currentFile) {
                                return (
                                    currentFile.name === file.name
                                    && currentFile.size === file.size
                                    && currentFile.lastModified === file.lastModified
                                );
                            }
                        );

                    if (!duplicate) {
                        selectedImageFiles.push(file);
                    }

                });

            syncImageInput();
            renderImagePreview();
            markDirty();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Inline category
    |--------------------------------------------------------------------------
    */

    function openModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');

    }


    function closeModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');

    }


    document
        .querySelectorAll('[data-close-modal]')
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


    categoryModal?.addEventListener(
        'click',
        function (event) {

            if (event.target === categoryModal) {
                closeModal(categoryModal);
            }

        }
    );


    addCategoryInlineButton?.addEventListener(
        'click',
        function () {

            inlineCategoryForm.reset();
            inlineCategoryError.textContent = '';
            inlineCategoryError.classList.add('hidden');

            openModal(categoryModal);

            setTimeout(function () {
                inlineCategoryNameInput.focus();
            }, 50);

        }
    );


    inlineCategoryForm?.addEventListener(
        'submit',
        async function (event) {

            event.preventDefault();

            const name =
                inlineCategoryNameInput.value.trim();

            if (!name) {
                return;
            }

            inlineCategoryError.textContent = '';
            inlineCategoryError.classList.add('hidden');

            inlineCategorySubmit.disabled = true;
            inlineCategorySubmit.classList.add(
                'cursor-not-allowed',
                'opacity-60'
            );

            try {

                const response =
                    await fetch(
                        '{{ url("/admin/products/inline-category") }}',
                        {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                name: name,
                            }),
                        }
                    );

                const data =
                    await response.json();

                if (!response.ok) {
                    throw new Error(
                        data.message
                        || 'Không thể thêm danh mục.'
                    );
                }

                const option =
                    new Option(
                        data.category.name,
                        data.category.id,
                        true,
                        true
                    );

                categorySelect.appendChild(option);
                categorySelect.value = data.category.id;

                closeModal(categoryModal);
                markDirty();

            } catch (error) {

                inlineCategoryError.textContent =
                    error.message;

                inlineCategoryError.classList.remove(
                    'hidden'
                );

            } finally {

                inlineCategorySubmit.disabled = false;
                inlineCategorySubmit.classList.remove(
                    'cursor-not-allowed',
                    'opacity-60'
                );

            }

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
                '{{ route("admin.products.index") }}';

            if (!isDirty) {
                window.location.href = url;
                return;
            }

            const confirmed =
                window.confirm(
                    'Bạn có dữ liệu chưa lưu. Bạn có chắc muốn hủy?'
                );

            if (confirmed) {
                isDirty = false;
                window.location.href = url;
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Submit
    |--------------------------------------------------------------------------
    */

    form.addEventListener(
        'submit',
        function () {
            /*
             * Không có attributes trong form.
             * Không có code JS nào có thể sinh / thay đổi cấu trúc variant.
             */
            isDirty = false;
        }
    );

})();

</script>

@endsection
