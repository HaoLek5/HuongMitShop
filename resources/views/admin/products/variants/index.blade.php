@extends('layouts.admin')

@section('title', 'Biến thể sản phẩm')

@section('page-title', 'Biến thể sản phẩm')

@section('content')

    <div class="mb-6 flex items-center justify-between">

        <div>

            <h1 class="text-2xl font-bold text-gray-900">
                Biến thể sản phẩm
            </h1>

            <p class="mt-1 text-sm text-gray-500">
                {{ $product->name }}
            </p>

        </div>

        <a
            href="{{ route(
                'admin.products.variants.create',
                $product
            ) }}"
            class="rounded-lg bg-green-600 px-5 py-2.5
                   text-sm font-medium text-white
                   hover:bg-green-700"
        >
            + Thêm biến thể
        </a>

    </div>


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


    <div class="overflow-hidden rounded-xl bg-white shadow-sm">

        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            SKU
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Thuộc tính
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Giá bán
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Giá nhập
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-medium
                                   uppercase tracking-wider text-gray-500">
                            Tồn kho
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

                    @forelse ($variants as $variant)

                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                {{ $variant->sku }}
                            </td>


                            <td class="px-6 py-4">

                                <div class="flex flex-wrap gap-2">

                                    @foreach ($variant->attributeValues as $value)

                                        <span
                                            class="rounded-full bg-gray-100
                                                   px-3 py-1 text-xs
                                                   text-gray-700"
                                        >
                                            {{ $value->attribute->name }}:
                                            {{ $value->value }}
                                        </span>

                                    @endforeach

                                </div>

                            </td>


                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ number_format(
                                    $variant->price,
                                    0,
                                    ',',
                                    '.'
                                ) }} ₫
                            </td>


                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $variant->cost_price !== null
                                    ? number_format(
                                        $variant->cost_price,
                                        0,
                                        ',',
                                        '.'
                                    ) . ' ₫'
                                    : '—'
                                }}
                            </td>


                            <td class="px-6 py-4 text-sm font-medium
                                       text-gray-700">
                                {{ $variant->stock_quantity }}
                            </td>


                            <td class="px-6 py-4">

                                @if ($variant->status)

                                    <span
                                        class="rounded-full bg-green-100
                                               px-3 py-1 text-xs
                                               font-medium text-green-700"
                                    >
                                        Hoạt động
                                    </span>

                                @else

                                    <span
                                        class="rounded-full bg-gray-100
                                               px-3 py-1 text-xs
                                               font-medium text-gray-600"
                                    >
                                        Tắt
                                    </span>

                                @endif

                            </td>


                            <td class="px-6 py-4">

                                <div class="flex justify-end gap-2">

                                    <a
                                        href="{{ route(
                                            'admin.products.variants.edit',
                                            [$product, $variant]
                                        ) }}"
                                        class="rounded-lg border
                                               border-gray-300 px-3 py-1.5
                                               text-sm text-gray-700
                                               hover:bg-gray-50"
                                    >
                                        Sửa
                                    </a>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.products.variants.toggle-status',
                                            [$product, $variant]
                                        ) }}"
                                    >
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="rounded-lg border
                                                   border-yellow-300
                                                   px-3 py-1.5 text-sm
                                                   text-yellow-700
                                                   hover:bg-yellow-50"
                                        >
                                            {{ $variant->status
                                                ? 'Tắt'
                                                : 'Bật'
                                            }}
                                        </button>

                                    </form>


                                    <form
                                        method="POST"
                                        action="{{ route(
                                            'admin.products.variants.destroy',
                                            [$product, $variant]
                                        ) }}"
                                        onsubmit="return confirm(
                                            'Bạn có chắc muốn xóa variant này?'
                                        )"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="rounded-lg border
                                                   border-red-300
                                                   px-3 py-1.5 text-sm
                                                   text-red-600
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
                                colspan="7"
                                class="px-6 py-12 text-center
                                       text-sm text-gray-500"
                            >
                                Sản phẩm chưa có biến thể.
                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        @if ($variants->hasPages())

            <div class="border-t border-gray-200 px-6 py-4">
                {{ $variants->links() }}
            </div>

        @endif

    </div>

@endsection