<div class="space-y-6">

    {{-- SKU --}}
    <div>
        <label
            for="sku"
            class="block text-sm font-medium text-gray-700"
        >
            SKU
        </label>

        <input
            type="text"
            id="sku"
            name="sku"
            value="{{ old('sku', $variant->sku ?? '') }}"
            maxlength="100"
            required
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >

        @error('sku')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Giá bán --}}
    <div>
        <label
            for="price"
            class="block text-sm font-medium text-gray-700"
        >
            Giá bán
        </label>

        <input
            type="number"
            id="price"
            name="price"
            value="{{ old('price', $variant->price ?? $product->base_price) }}"
            min="0"
            step="1000"
            required
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >

        @error('price')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Giá nhập --}}
    <div>
        <label
            for="cost_price"
            class="block text-sm font-medium text-gray-700"
        >
            Giá nhập
        </label>

        <input
            type="number"
            id="cost_price"
            name="cost_price"
            value="{{ old('cost_price', $variant->cost_price ?? '') }}"
            min="0"
            step="1000"
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >

        @error('cost_price')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Tồn kho --}}
    <div>
        <label
            for="stock_quantity"
            class="block text-sm font-medium text-gray-700"
        >
            Tồn kho
        </label>

        <input
            type="number"
            id="stock_quantity"
            name="stock_quantity"
            value="{{ old('stock_quantity', $variant->stock_quantity ?? 0) }}"
            min="0"
            required
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >

        @error('stock_quantity')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Thuộc tính --}}
    <div>

        <h3 class="mb-4 text-base font-semibold text-gray-900">
            Thuộc tính biến thể
        </h3>

        @error('attribute_value_ids')
            <p class="mb-3 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror


        <div class="space-y-4">

            @foreach ($attributes as $attribute)

                <div class="rounded-lg border border-gray-200 p-4">

                    <h4 class="mb-3 font-medium text-gray-900">
                        {{ $attribute->name }}
                    </h4>

                    <div class="flex flex-wrap gap-3">

                        @foreach ($attribute->values as $value)

                            @php
                                $selectedValues =
                                    old(
                                        'attribute_value_ids',
                                        isset($variant)
                                            ? $variant->attributeValues
                                                ->pluck('id')
                                                ->toArray()
                                            : []
                                    );
                            @endphp

                            <label
                                class="inline-flex cursor-pointer items-center
                                       gap-2 rounded-lg border border-gray-300
                                       px-3 py-2 hover:bg-gray-50"
                            >

                                <input
                                    type="checkbox"
                                    name="attribute_value_ids[]"
                                    value="{{ $value->id }}"
                                    @checked(in_array(
                                        $value->id,
                                        $selectedValues
                                    ))
                                    class="rounded border-gray-300
                                           text-green-600
                                           focus:ring-green-500"
                                >

                                <span class="text-sm">
                                    {{ $value->value }}
                                </span>

                            </label>

                        @endforeach

                    </div>

                </div>

            @endforeach

        </div>

    </div>


    {{-- Status --}}
    <div>

        <label class="inline-flex items-center">

            <input
                type="checkbox"
                name="status"
                value="1"
                class="rounded border-gray-300 text-green-600
                       focus:ring-green-500"
                @checked(old('status', $variant->status ?? true))
            >

            <span class="ml-2 text-sm text-gray-700">
                Đang hoạt động
            </span>

        </label>

    </div>


    {{-- Buttons --}}
    <div class="flex gap-3">

        <button
            type="submit"
            class="rounded-lg bg-green-600 px-5 py-2.5
                   text-sm font-medium text-white hover:bg-green-700"
        >
            {{ $submitText }}
        </button>

        <a
            href="{{ route('admin.products.variants.index', $product) }}"
            class="rounded-lg border border-gray-300 bg-white
                   px-5 py-2.5 text-sm font-medium text-gray-700
                   hover:bg-gray-50"
        >
            Hủy
        </a>

    </div>

</div>