@extends('layouts.admin')

@section('title', 'Chi tiết phiếu nhập')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                {{ $purchaseReceipt->receipt_code }}
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Tạo bởi:
                {{ $purchaseReceipt->creator->name ?? '—' }}
                ·
                {{ $purchaseReceipt->created_at->format('d/m/Y H:i') }}
            </p>
        </div>

        <a
            href="{{ route('admin.purchase-receipts.index') }}"
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

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

        <div class="px-6 py-4 border-b flex items-center justify-between">

            <h2 class="font-semibold">
                Chi tiết nhập hàng
            </h2>

            @if($purchaseReceipt->status === 'draft')

                <div class="flex gap-2">

                    <form
                        method="POST"
                        action="{{ route('admin.purchase-receipts.complete', $purchaseReceipt) }}"
                        onsubmit="return confirm('Hoàn tất phiếu nhập và cộng tồn kho?')"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white"
                        >
                            Hoàn tất phiếu
                        </button>
                    </form>

                    <form
                        method="POST"
                        action="{{ route('admin.purchase-receipts.cancel', $purchaseReceipt) }}"
                        onsubmit="return confirm('Bạn có chắc muốn hủy phiếu nhập này?')"
                    >
                        @csrf
                        @method('PATCH')

                        <button
                            class="px-4 py-2 rounded-lg bg-red-100 hover:bg-red-200 text-red-700"
                        >
                            Hủy phiếu
                        </button>
                    </form>

                </div>

            @endif

        </div>

        <div class="p-6">

            <table class="w-full text-sm">

                <thead class="border-b">
                    <tr>
                        <th class="text-left pb-3">Sản phẩm</th>
                        <th class="text-left pb-3">SKU</th>
                        <th class="text-left pb-3">Biến thể</th>
                        <th class="text-center pb-3">SL</th>
                        <th class="text-right pb-3">Giá nhập</th>
                        <th class="text-right pb-3">Thành tiền</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @foreach($purchaseReceipt->items as $item)

                        @php
                            $variantName = $item->variant->attributeValues
                                ->map(fn($value) => $value->value)
                                ->join(' / ');
                        @endphp

                        <tr>

                            <td class="py-4">
                                {{ $item->variant->product->name }}
                            </td>

                            <td class="py-4">
                                {{ $item->variant->sku }}
                            </td>

                            <td class="py-4">
                                {{ $variantName ?: '—' }}
                            </td>

                            <td class="py-4 text-center">
                                {{ $item->quantity }}
                            </td>

                            <td class="py-4 text-right">
                                {{ number_format($item->import_price, 0, ',', '.') }} ₫
                            </td>

                            <td class="py-4 text-right font-medium">
                                {{ number_format($item->subtotal, 0, ',', '.') }} ₫
                            </td>

                        </tr>

                    @endforeach

                </tbody>

                <tfoot class="border-t">

                    <tr>
                        <td
                            colspan="5"
                            class="pt-5 text-right font-semibold"
                        >
                            Tổng cộng:
                        </td>

                        <td
                            class="pt-5 text-right text-xl font-bold text-green-600"
                        >
                            {{ number_format($purchaseReceipt->total_amount, 0, ',', '.') }}
                            ₫
                        </td>
                    </tr>

                </tfoot>

            </table>

            @if($purchaseReceipt->note)
                <div class="mt-6 p-4 rounded-lg bg-gray-50">
                    <div class="font-medium mb-1">Ghi chú</div>
                    <div class="text-gray-600">
                        {{ $purchaseReceipt->note }}
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection