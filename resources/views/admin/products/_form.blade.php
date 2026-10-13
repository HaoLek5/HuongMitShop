<div class="space-y-6">

    {{-- Danh mục --}}
    <div>
        <label
            for="category_id"
            class="block text-sm font-medium text-gray-700"
        >
            Danh mục
        </label>

        <select
            id="category_id"
            name="category_id"
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
            required
        >
            <option value="">
                -- Chọn danh mục --
            </option>

            @foreach ($categories as $category)
                <option
                    value="{{ $category->id }}"
                    @selected(
                        old('category_id', $product->category_id ?? '')
                        == $category->id
                    )
                >
                    {{ $category->name }}
                </option>
            @endforeach
        </select>

        @error('category_id')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Tên --}}
    <div>
        <label
            for="name"
            class="block text-sm font-medium text-gray-700"
        >
            Tên sản phẩm
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $product->name ?? '') }}"
            maxlength="200"
            required
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >

        @error('name')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Slug --}}
    <div>
        <label
            for="slug"
            class="block text-sm font-medium text-gray-700"
        >
            Slug
        </label>

        <input
            type="text"
            id="slug"
            name="slug"
            value="{{ old('slug', $product->slug ?? '') }}"
            placeholder="Để trống để tự tạo từ tên"
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >

        @error('slug')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Giá cơ bản --}}
    <div>
        <label
            for="base_price"
            class="block text-sm font-medium text-gray-700"
        >
            Giá cơ bản
        </label>

        <div class="relative mt-1">

            <input
                type="number"
                id="base_price"
                name="base_price"
                value="{{ old('base_price', $product->base_price ?? '') }}"
                min="0"
                step="1000"
                required
                class="block w-full rounded-lg border-gray-300
                       pr-12 shadow-sm focus:border-green-500
                       focus:ring-green-500"
            >

            <span
                class="absolute inset-y-0 right-3 flex items-center
                       text-sm text-gray-500"
            >
                ₫
            </span>

        </div>

        @error('base_price')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Mô tả --}}
    <div>
        <label
            for="description"
            class="block text-sm font-medium text-gray-700"
        >
            Mô tả
        </label>

        <textarea
            id="description"
            name="description"
            rows="5"
            class="mt-1 block w-full rounded-lg border-gray-300
                   shadow-sm focus:border-green-500
                   focus:ring-green-500"
        >{{ old('description', $product->description ?? '') }}</textarea>

        @error('description')
            <p class="mt-1 text-sm text-red-600">
                {{ $message }}
            </p>
        @enderror
    </div>


    {{-- Trạng thái --}}
    <div>
        <label class="inline-flex items-center">

            <input
                type="checkbox"
                name="status"
                value="1"
                class="rounded border-gray-300 text-green-600
                       shadow-sm focus:ring-green-500"
                @checked(old('status', $product->status ?? true))
            >

            <span class="ml-2 text-sm text-gray-700">
                Đang hoạt động
            </span>

        </label>
    </div>


    {{-- Button --}}
    <div class="flex gap-3">

        <button
            type="submit"
            class="rounded-lg bg-green-600 px-5 py-2.5
                   text-sm font-medium text-white
                   hover:bg-green-700"
        >
            {{ $submitText }}
        </button>

        <a
            href="{{ route('admin.products.index') }}"
            class="rounded-lg border border-gray-300 bg-white
                   px-5 py-2.5 text-sm font-medium text-gray-700
                   hover:bg-gray-50"
        >
            Hủy
        </a>

    </div>

</div>