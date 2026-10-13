@extends('layouts.admin')

@section('title', 'Chi tiết tồn kho')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $variant->product->name }}
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                SKU: {{ $variant->sku }}
            </p>
        </div>

        <a
            href="{{ route('admin.inventory.index') }}"
            class="px-4 py-2 rounded-lg border hover:bg-gray-50"
        >
            ← Quay lại
        </a>

    </div>

    @if(session('success'))
        <div class="p-4 rounded-lg bg-green-50 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-lg bg-red-50 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-lg bg-red-50 text-red-700">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-xl border shadow-sm p-6">

            <h2 class="font-semibold text-gray-800 mb-5">
                Thông tin tồn kho
            </h2>

            <div class="grid grid-cols-2 gap-6">

                <div>
                    <div class="text-sm text-gray-500">
                        Sản phẩm
                    </div>

                    <div class="font-medium mt-1">
                        {{ $variant->product->name }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        SKU
                    </div>

                    <div class="font-medium mt-1">
                        {{ $variant->sku }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Biến thể
                    </div>

                    <div class="font-medium mt-1">
                        {{ $variant->attributeValues->map(fn($value) => $value->value)->join(' / ') ?: '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-sm text-gray-500">
                        Giá bán
                    </div>

                    <div class="font-medium mt-1">
                        {{ number_format($variant->price, 0, ',', '.') }} ₫
                    </div>
                </div>

            </div>

            <div class="mt-8 p-5 rounded-xl bg-gray-50">

                <div class="text-sm text-gray-500">
                    Tồn kho hiện tại
                </div>

                <div class="text-4xl font-bold mt-2 text-green-600">
                    {{ $variant->stock_quantity }}
                </div>

                <div class="text-sm text-gray-500 mt-1">
                    sản phẩm
                </div>

            </div>

        </div>

        <div class="bg-white rounded-xl border shadow-sm p-6">

            <h2 class="font-semibold text-gray-800 mb-2">
                Điều chỉnh tồn kho
            </h2>

            <p class="text-sm text-gray-500 mb-5">
                Nhập số dương để tăng, số âm để giảm.
            </p>

            <form
                method="POST"
                action="{{ route('admin.inventory.adjust', $variant) }}"
                class="space-y-4"
            >
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Số lượng điều chỉnh
                    </label>

                    <input
                        type="number"
                        name="quantity"
                        value="{{ old('quantity') }}"
                        placeholder="Ví dụ: 5 hoặc -3"
                        class="w-full border-gray-300 rounded-lg"
                        required
                    >

                    <p class="mt-1 text-xs text-gray-500">
                        +5 = tăng 5 sản phẩm,
                        -3 = giảm 3 sản phẩm.
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">
                        Lý do
                    </label>

                    <textarea
                        name="note"
                        rows="4"
                        class="w-full border-gray-300 rounded-lg"
                        placeholder="Ví dụ: Kiểm kê thực tế, hàng lỗi..."
                        required
                    >{{ old('note') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="w-full px-4 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white"
                >
                    Xác nhận điều chỉnh
                </button>

            </form>

        </div>

    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b">
            <h2 class="font-semibold">
                Lịch sử biến động kho
            </h2>
        </div>

        <table class="w-full text-sm">

            <thead class="bg-gray-50 border-b">

                <tr>
                    <th class="text-left px-6 py-4">
                        Thời gian
                    </th>

                    <th class="text-center px-6 py-4">
                        Loại
                    </th>

                    <th class="text-right px-6 py-4">
                        Số lượng
                    </th>

                    <th class="text-left px-6 py-4">
                        Tham chiếu
                    </th>

                    <th class="text-left px-6 py-4">
                        Ghi chú
                    </th>

                    <th class="text-left px-6 py-4">
                        Người thực hiện
                    </th>
                </tr>

            </thead>

            <tbody class="divide-y">

                @forelse($transactions as $transaction)

                    <tr>

                        <td class="px-6 py-4 text-gray-500">
                            {{ $transaction->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </td>

                        <td class="px-6 py-4 text-center">

                            @switch($transaction->type)

                                @case('import')
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                        Nhập hàng
                                    </span>
                                    @break

                                @case('sale')
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                        Bán hàng
                                    </span>
                                    @break

                                @case('return')
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-purple-100 text-purple-700">
                                        Trả hàng
                                    </span>
                                    @break

                                @case('adjustment')
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                        Điều chỉnh
                                    </span>
                                    @break

                            @endswitch

                        </td>

                        <td class="px-6 py-4 text-right font-semibold">

                            @if($transaction->quantity > 0)
                                <span class="text-green-600">
                                    +{{ $transaction->quantity }}
                                </span>
                            @else
                                <span class="text-red-600">
                                    {{ $transaction->quantity }}
                                </span>
                            @endif

                        </td>

                        <td class="px-6 py-4">
                            {{ $transaction->reference_type ?? '—' }}

                            @if($transaction->reference_id)
                                #{{ $transaction->reference_id }}
                            @endif
                        </td>

                        <td class="px-6 py-4">
                            {{ $transaction->note ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $transaction->creator->name ?? '—' }}
                        </td>

                    </tr>

                @empty

                    <tr>
                        <td
                            colspan="6"
                            class="px-6 py-12 text-center text-gray-500"
                        >
                            Chưa có lịch sử biến động.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="p-4">
            {{ $transactions->links() }}
        </div>

    </div>

</div>
@endsection