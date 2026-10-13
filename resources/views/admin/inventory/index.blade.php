@extends('layouts.admin')

@section('title', 'Quản lý tồn kho')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Quản lý tồn kho
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Theo dõi số lượng tồn của từng biến thể
            </p>
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl border shadow-sm p-5">

        <form method="GET" class="flex gap-3">

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Tìm theo tên sản phẩm hoặc SKU..."
                class="flex-1 border-gray-300 rounded-lg"
            >

            <button
                type="submit"
                class="px-5 py-2.5 rounded-lg bg-green-600 text-white hover:bg-green-700"
            >
                Tìm kiếm
            </button>

            @if(request('search'))
                <a
                    href="{{ route('admin.inventory.index') }}"
                    class="px-5 py-2.5 rounded-lg border hover:bg-gray-50"
                >
                    Xóa
                </a>
            @endif

        </form>

    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-4">
                        Sản phẩm
                    </th>

                    <th class="text-left px-6 py-4">
                        SKU
                    </th>

                    <th class="text-left px-6 py-4">
                        Biến thể
                    </th>

                    <th class="text-right px-6 py-4">
                        Giá bán
                    </th>

                    <th class="text-center px-6 py-4">
                        Tồn kho
                    </th>

                    <th class="text-center px-6 py-4">
                        Trạng thái
                    </th>

                    <th class="text-right px-6 py-4">
                        Thao tác
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse($variants as $variant)

                    @php
                        $variantName = $variant->attributeValues
                            ->map(fn($value) => $value->value)
                            ->join(' / ');
                    @endphp

                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium">
                            {{ $variant->product->name }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $variant->sku }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $variantName ?: '—' }}
                        </td>

                        <td class="px-6 py-4 text-right">
                            {{ number_format($variant->price, 0, ',', '.') }} ₫
                        </td>

                        <td class="px-6 py-4 text-center">

                            @if($variant->stock_quantity <= 0)

                                <span class="px-2.5 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                    Hết hàng
                                </span>

                            @elseif($variant->stock_quantity <= 5)

                                <span class="px-2.5 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                    {{ $variant->stock_quantity }} - Sắp hết
                                </span>

                            @else

                                <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                    {{ $variant->stock_quantity }}
                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-4 text-center">

                            @if($variant->status)

                                <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                    Đang bán
                                </span>

                            @else

                                <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                    Ngừng bán
                                </span>

                            @endif

                        </td>

                        <td class="px-6 py-4 text-right">

                            <a
                                href="{{ route('admin.inventory.show', $variant) }}"
                                class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200"
                            >
                                Chi tiết
                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-gray-500"
                        >
                            Không có biến thể nào.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{ $variants->links() }}

</div>
@endsection