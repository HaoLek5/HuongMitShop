@extends('layouts.app')

@section('title', $product->name)

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | Nhóm các giá trị thuộc tính để hiển thị
    |--------------------------------------------------------------------------
    */
    $attributeGroups = $product->variants
        ->flatMap(function ($variant) {
            return $variant->attributeValues;
        })
        ->groupBy(function ($value) {
            return $value->attribute->id;
        });

    /*
    |--------------------------------------------------------------------------
    | Chuẩn bị dữ liệu variant cho JavaScript
    |--------------------------------------------------------------------------
    */
    $variantsData = $product->variants
        ->map(function ($variant) {
            return [
                'id' => $variant->id,
                'price' => (float) $variant->price,
                'stock' => (int) $variant->stock_quantity,

                'attributes' => $variant->attributeValues
                    ->map(function ($value) {
                        return [
                            'attribute_id' => $value->attribute_id,
                            'value_id' => $value->id,
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })
        ->values()
        ->all();
@endphp


<div
    class="max-w-7xl mx-auto px-6 py-10"
    x-data="productDetail()"
>

    {{-- ========================================================= --}}
    {{-- DỮ LIỆU VARIANT CHO JAVASCRIPT --}}
    {{-- ========================================================= --}}

    <div
        id="product-variants-data"
        data-variants="{{ json_encode(
            $variantsData,
            JSON_HEX_TAG |
            JSON_HEX_APOS |
            JSON_HEX_AMP |
            JSON_HEX_QUOT
        ) }}"
        class="hidden"
    ></div>


    {{-- ========================================================= --}}
    {{-- BREADCRUMB --}}
    {{-- ========================================================= --}}

    <div class="mb-6 text-sm text-gray-500">

        <a
            href="{{ route('home') }}"
            class="hover:text-green-600"
        >
            Trang chủ
        </a>

        <span class="mx-2">/</span>

        <span class="text-gray-700">
            {{ $product->name }}
        </span>

    </div>


    {{-- ========================================================= --}}
    {{-- THÔNG BÁO --}}
    {{-- ========================================================= --}}

    @if(session('success'))

        <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>

    @endif


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


    {{-- ========================================================= --}}
    {{-- SẢN PHẨM --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">


        {{-- ===================================================== --}}
        {{-- HÌNH ẢNH --}}
        {{-- ===================================================== --}}

        <div>

            @php
                $mainImage = $product->images->first();
            @endphp


            <div
                class="aspect-square rounded-xl overflow-hidden bg-gray-100 border"
            >

                @if($mainImage && $mainImage->image_url)

                    <img
                        src="{{ $mainImage->image_url }}"
                        alt="{{ $product->name }}"
                        class="w-full h-full object-cover"
                    >

                @else

                    <div
                        class="w-full h-full flex items-center justify-center text-gray-400"
                    >
                        Chưa có ảnh
                    </div>

                @endif

            </div>


            {{-- Ảnh phụ --}}

            @if($product->images->count() > 1)

                <div class="grid grid-cols-4 gap-3 mt-4">

                    @foreach($product->images as $image)

                        <div
                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden border"
                        >

                            <img
                                src="{{ $image->image_url }}"
                                alt="{{ $product->name }}"
                                class="w-full h-full object-cover"
                            >

                        </div>

                    @endforeach

                </div>

            @endif

        </div>


        {{-- ===================================================== --}}
        {{-- THÔNG TIN SẢN PHẨM --}}
        {{-- ===================================================== --}}

        <div>


            {{-- Danh mục --}}

            @if($product->category)

                <div class="text-sm text-green-600 font-medium">
                    {{ $product->category->name }}
                </div>

            @endif


            {{-- Tên sản phẩm --}}

            <h1 class="mt-2 text-3xl font-bold text-gray-800">
                {{ $product->name }}
            </h1>


            {{-- Giá --}}

            <div class="mt-4">

                <span
                    class="text-3xl font-bold text-green-600"
                    x-text="formattedPrice()"
                ></span>

            </div>


            {{-- ================================================= --}}
            {{-- CHỌN THUỘC TÍNH --}}
            {{-- ================================================= --}}

            @foreach($attributeGroups as $attributeId => $values)

                @php
                    $attribute = $values->first()->attribute;
                    $uniqueValues = $values->unique('id');
                @endphp


                <div class="mt-7">

                    <div class="font-semibold text-gray-800 mb-3">
                        {{ $attribute->name }}
                    </div>


                    <div class="flex flex-wrap gap-3">

                        @foreach($uniqueValues as $value)

                            <button
                                type="button"

                                @click="
                                    selectAttribute(
                                        {{ $attribute->id }},
                                        {{ $value->id }}
                                    )
                                "

                                :class="
                                    selectedAttributes[{{ $attribute->id }}] == {{ $value->id }}
                                        ? 'border-green-600 bg-green-50 text-green-700'
                                        : 'border-gray-300 bg-white text-gray-700'
                                "

                                class="px-4 py-2 rounded-lg border hover:border-green-500 transition"
                            >
                                {{ $value->value }}
                            </button>

                        @endforeach

                    </div>

                </div>

            @endforeach


            {{-- ================================================= --}}
            {{-- SỐ LƯỢNG --}}
            {{-- ================================================= --}}

            <div class="mt-7">

                <div class="font-semibold text-gray-800 mb-3">
                    Số lượng
                </div>


                <div class="flex items-center gap-3">

                    <button
                        type="button"
                        @click="decreaseQuantity()"
                        class="w-10 h-10 rounded-lg border hover:bg-gray-50"
                    >
                        −
                    </button>


                    <input
                        type="number"
                        x-model.number="quantity"
                        min="1"
                        :max="currentStock"
                        class="w-20 h-10 border-gray-300 rounded-lg text-center"
                    >


                    <button
                        type="button"
                        @click="increaseQuantity()"
                        class="w-10 h-10 rounded-lg border hover:bg-gray-50"
                    >
                        +
                    </button>

                </div>

            </div>


            {{-- ================================================= --}}
            {{-- TỒN KHO --}}
            {{-- ================================================= --}}

            <div class="mt-4 text-sm">


                <template x-if="selectedVariant">

                    <div>

                        <template x-if="currentStock > 0">

                            <span class="text-green-600">

                                Còn
                                <span x-text="currentStock"></span>
                                sản phẩm

                            </span>

                        </template>


                        <template x-if="currentStock <= 0">

                            <span class="text-red-500">
                                Hết hàng
                            </span>

                        </template>

                    </div>

                </template>


                <template x-if="!selectedVariant">

                    <span class="text-gray-400">
                        Vui lòng chọn đầy đủ thuộc tính
                    </span>

                </template>

            </div>


            {{-- ================================================= --}}
            {{-- THÊM VÀO GIỎ --}}
            {{-- ================================================= --}}

            <form
                method="POST"
                action="{{ route('cart.items.store') }}"
                class="mt-6"
                @submit="beforeSubmit($event)"
            >

                @csrf


                <input
                    type="hidden"
                    name="variant_id"
                    :value="selectedVariant?.id ?? ''"
                >


                <input
                    type="hidden"
                    name="quantity"
                    :value="quantity"
                >


                <button
                    type="submit"

                    :disabled="!canAddToCart()"

                    :class="
                        canAddToCart()
                            ? 'bg-green-600 hover:bg-green-700'
                            : 'bg-gray-300 cursor-not-allowed'
                    "

                    class="w-full py-3 rounded-lg text-white font-semibold transition"
                >

                    <span
                        x-text="
                            selectedVariant
                                ? (
                                    currentStock > 0
                                        ? 'Thêm vào giỏ hàng'
                                        : 'Hết hàng'
                                )
                                : 'Chọn biến thể'
                        "
                    ></span>

                </button>

            </form>


            {{-- ================================================= --}}
            {{-- MÔ TẢ --}}
            {{-- ================================================= --}}

            @if($product->description)

                <div class="mt-10 pt-8 border-t">

                    <h2 class="text-lg font-semibold text-gray-800">
                        Mô tả sản phẩm
                    </h2>


                    <div
                        class="mt-3 text-gray-600 leading-7 whitespace-pre-line"
                    >
                        {{ $product->description }}
                    </div>

                </div>

            @endif

        </div>

    </div>

</div>


{{-- ============================================================= --}}
{{-- JAVASCRIPT / ALPINE --}}
{{-- ============================================================= --}}

<script>

function productDetail() {

    /*
     * Lấy dữ liệu variant từ HTML
     */
    const dataElement =
        document.getElementById('product-variants-data');

    let variantsData = [];

    if (dataElement) {

        try {

            variantsData = JSON.parse(
                dataElement.dataset.variants
            );

        } catch (error) {

            console.error(
                'Không thể đọc dữ liệu product variants:',
                error
            );

        }

    }


    return {

        /*
         * Danh sách các variant của sản phẩm
         */
        variants: variantsData,


        /*
         * Các thuộc tính customer đã chọn
         *
         * Ví dụ:
         *
         * {
         *     1: 3,
         *     2: 6
         * }
         *
         * 1 = attribute Màu
         * 3 = value Đỏ
         *
         * 2 = attribute Size
         * 6 = value M
         */
        selectedAttributes: {},


        /*
         * Variant đang được chọn
         */
        selectedVariant: null,


        /*
         * Số lượng
         */
        quantity: 1,


        /*
         * Tồn kho hiện tại
         */
        currentStock: 0,


        /*
         * Giá hiện tại
         */
        currentPrice: 0,


        /*
         * ---------------------------------------------------------
         * KHỞI TẠO
         * ---------------------------------------------------------
         *
         * Nếu sản phẩm chỉ có đúng 1 variant,
         * tự động chọn variant đó.
         */
        init() {

            /*
             * Chỉ có đúng 1 variant
             */
            if (this.variants.length === 1) {

                const variant =
                    this.variants[0];


                /*
                 * Tự động chọn variant
                 */
                this.selectedVariant =
                    variant;


                /*
                 * Cập nhật giá
                 */
                this.currentPrice =
                    Number(
                        variant.price
                    );


                /*
                 * Cập nhật tồn kho
                 */
                this.currentStock =
                    Number(
                        variant.stock
                    );


                /*
                 * Nếu variant có thuộc tính
                 * thì tự động đánh dấu các thuộc tính
                 * là đã chọn.
                 */
                if (
                    Array.isArray(
                        variant.attributes
                    )
                ) {

                    variant.attributes.forEach(
                        attribute => {

                            this.selectedAttributes[
                                attribute.attribute_id
                            ] =
                                attribute.value_id;

                        }
                    );

                }


                /*
                 * Nếu quantity đang lớn hơn tồn kho
                 * thì đưa về mức hợp lệ.
                 */
                if (
                    this.currentStock > 0
                    &&
                    this.quantity > this.currentStock
                ) {

                    this.quantity =
                        this.currentStock;

                }

            }

        },


        /*
         * ---------------------------------------------------------
         * CUSTOMER CHỌN THUỘC TÍNH
         * ---------------------------------------------------------
         */
        selectAttribute(
            attributeId,
            valueId
        ) {

            this.selectedAttributes[
                attributeId
            ] = valueId;


            this.findVariant();

        },


        /*
         * ---------------------------------------------------------
         * TÌM VARIANT
         * ---------------------------------------------------------
         */
        findVariant() {

            const selected =
                Object.entries(
                    this.selectedAttributes
                );


            /*
             * Chưa chọn thuộc tính nào
             *
             * Trường hợp này chỉ xảy ra khi
             * sản phẩm có nhiều variant.
             */
            if (
                selected.length === 0
            ) {

                this.selectedVariant =
                    null;

                this.currentStock =
                    0;

                this.currentPrice =
                    0;

                return;

            }


            /*
             * Tìm variant có đúng tổ hợp
             * attribute/value mà customer chọn.
             */
            const variant =
                this.variants.find(
                    variant => {

                        /*
                         * Số lượng attribute phải bằng
                         * số attribute customer đã chọn.
                         */
                        if (
                            variant.attributes.length
                            !==
                            selected.length
                        ) {

                            return false;

                        }


                        /*
                         * Kiểm tra từng attribute/value.
                         */
                        return selected.every(
                            (
                                [
                                    attributeId,
                                    valueId
                                ]
                            ) => {

                                return variant.attributes.some(
                                    attribute => {

                                        return (

                                            String(
                                                attribute.attribute_id
                                            )
                                            ===
                                            String(
                                                attributeId
                                            )

                                            &&

                                            String(
                                                attribute.value_id
                                            )
                                            ===
                                            String(
                                                valueId
                                            )

                                        );

                                    }
                                );

                            }
                        );

                    }
                );


            /*
             * Lưu variant tìm được.
             */
            this.selectedVariant =
                variant ?? null;


            /*
             * Có variant phù hợp.
             */
            if (variant) {

                this.currentStock =
                    Number(
                        variant.stock
                    );

                this.currentPrice =
                    Number(
                        variant.price
                    );


                /*
                 * Nếu quantity lớn hơn tồn kho
                 * thì đưa quantity về mức hợp lệ.
                 */
                if (
                    this.currentStock <= 0
                ) {

                    this.quantity =
                        1;

                } else if (
                    this.quantity >
                    this.currentStock
                ) {

                    this.quantity =
                        this.currentStock;

                }

            }


            /*
             * Không có variant phù hợp.
             */
            else {

                this.currentStock =
                    0;

                this.currentPrice =
                    0;

            }

        },


        /*
         * ---------------------------------------------------------
         * KIỂM TRA CÓ THỂ THÊM VÀO GIỎ
         * ---------------------------------------------------------
         */
        canAddToCart() {

            return (

                this.selectedVariant !== null

                &&

                this.currentStock > 0

                &&

                this.quantity >= 1

                &&

                this.quantity <=
                    this.currentStock

            );

        },


        /*
         * ---------------------------------------------------------
         * TĂNG SỐ LƯỢNG
         * ---------------------------------------------------------
         */
        increaseQuantity() {

            if (
                this.quantity <
                this.currentStock
            ) {

                this.quantity++;

            }

        },


        /*
         * ---------------------------------------------------------
         * GIẢM SỐ LƯỢNG
         * ---------------------------------------------------------
         */
        decreaseQuantity() {

            if (
                this.quantity > 1
            ) {

                this.quantity--;

            }

        },


        /*
         * ---------------------------------------------------------
         * HIỂN THỊ GIÁ
         * ---------------------------------------------------------
         */
        formattedPrice() {

            /*
             * Chưa chọn variant.
             *
             * Trường hợp này thường xảy ra
             * khi sản phẩm có nhiều variant.
             */
            if (
                !this.selectedVariant
            ) {

                return 'Vui lòng chọn biến thể';

            }


            return new Intl.NumberFormat(
                'vi-VN'
            ).format(
                this.currentPrice
            )
            + ' ₫';

        },


        /*
         * ---------------------------------------------------------
         * KIỂM TRA TRƯỚC KHI SUBMIT
         * ---------------------------------------------------------
         */
        beforeSubmit(event) {

            if (
                !this.canAddToCart()
            ) {

                event.preventDefault();

                alert(
                    'Vui lòng chọn đầy đủ biến thể và số lượng hợp lệ.'
                );

            }

        }

    };

}

</script>

@endsection