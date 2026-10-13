<div class="space-y-6">

    {{-- Tên --}}
    <div>
        <label
            for="name"
            class="block text-sm font-medium text-gray-700"
        >
            Tên danh mục
        </label>

        <input
            type="text"
            id="name"
            name="name"
            value="{{ old('name', $category->name ?? '') }}"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                   focus:border-green-500 focus:ring-green-500"
            required
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
            value="{{ old('slug', $category->slug ?? '') }}"
            placeholder="Tự động tạo nếu để trống"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                   focus:border-green-500 focus:ring-green-500"
        >

        @error('slug')
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
            rows="4"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm
                   focus:border-green-500 focus:ring-green-500"
        >{{ old('description', $category->description ?? '') }}</textarea>

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
                @checked(old('status', $category->status ?? true))
            >

            <span class="ml-2 text-sm text-gray-700">
                Đang hoạt động
            </span>
        </label>
    </div>


    {{-- Button --}}
    <div class="flex items-center gap-3">

        <button
            type="submit"
            class="rounded-lg bg-green-600 px-5 py-2.5 text-sm
                   font-medium text-white hover:bg-green-700"
        >
            {{ $submitText }}
        </button>

        <a
            href="{{ route('admin.categories.index') }}"
            class="rounded-lg border border-gray-300 bg-white px-5 py-2.5
                   text-sm font-medium text-gray-700 hover:bg-gray-50"
        >
            Hủy
        </a>

    </div>

</div>