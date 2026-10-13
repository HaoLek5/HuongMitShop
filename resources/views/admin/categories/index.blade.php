@extends('layouts.admin')

@section('title', 'Danh mục')

@section('page-title', 'Danh mục')

@section('content')

    <div class="flex items-center justify-between mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Danh mục sản phẩm
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                Quản lý các danh mục của shop.
            </p>
        </div>

        <a
            href="{{ route('admin.categories.create') }}"
            class="rounded-lg bg-green-600 px-5 py-2.5 text-sm
                   font-medium text-white hover:bg-green-700"
        >
            + Thêm danh mục
        </a>

    </div>


    {{-- Thông báo --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-50 p-4 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 rounded-lg bg-red-50 p-4 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif


    {{-- Bảng --}}
    <div class="overflow-hidden rounded-xl bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            ID
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Tên
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Slug
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Trạng thái
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Thao tác
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-200">

                    @forelse ($categories as $category)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $category->id }}
                            </td>

                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $category->name }}
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $category->slug }}
                            </td>

                            <td class="px-6 py-4">

                                @if ($category->status)
                                    <span
                                        class="rounded-full bg-green-100 px-3 py-1
                                               text-xs font-medium text-green-700"
                                    >
                                        Hoạt động
                                    </span>
                                @else
                                    <span
                                        class="rounded-full bg-gray-100 px-3 py-1
                                               text-xs font-medium text-gray-600"
                                    >
                                        Tắt
                                    </span>
                                @endif

                            </td>

                            <td class="px-6 py-4">

                                <div class="flex justify-end gap-2">

                                    {{-- Sửa --}}
                                    <a
                                        href="{{ route('admin.categories.edit', $category) }}"
                                        class="rounded-lg border border-gray-300
                                               px-3 py-1.5 text-sm text-gray-700
                                               hover:bg-gray-50"
                                    >
                                        Sửa
                                    </a>


                                    {{-- Bật/tắt --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.categories.toggle-status',
                                            $category
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded-lg border border-yellow-300
                                                   px-3 py-1.5 text-sm text-yellow-700
                                                   hover:bg-yellow-50"
                                        >
                                            {{ $category->status ? 'Tắt' : 'Bật' }}
                                        </button>

                                    </form>


                                    {{-- Xóa --}}
                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.categories.destroy',
                                            $category
                                        ) }}"
                                        onsubmit="return confirm(
                                            'Bạn có chắc muốn xóa danh mục này?'
                                        )"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg border border-red-300
                                                   px-3 py-1.5 text-sm text-red-600
                                                   hover:bg-red-50"
                                        >
                                            Xóa
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="5"
                                class="px-6 py-12 text-center text-sm
                                       text-gray-500"
                            >
                                Chưa có danh mục nào.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- Pagination --}}
        @if ($categories->hasPages())

            <div class="border-t border-gray-200 px-6 py-4">
                {{ $categories->links() }}
            </div>

        @endif

    </div>

@endsection