@extends('layouts.admin')

@section('title', 'Tạo phiếu nhập')

@section('content')
<div
    x-data="purchaseReceiptForm()"
    class="space-y-6"
>

    <div>
        <h1 class="text-2xl font-bold text-gray-800">
            Tạo phiếu nhập hàng
        </h1>

        <p class="text-sm text-gray-500 mt-1">
            Tạo phiếu nháp trước, sau đó xác nhận để cập nhật tồn kho.
        </p>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-lg bg-red-50 text-red-700">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.purchase-receipts.store') }}"
        class="space-y-6"
    >
        @csrf

        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b">
                <h2 class="font-semibold text-gray-800">
                    Danh sách sản phẩm nhập
                </h2>
            </div>

            <div class="p-6">

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left pb-3">Biến thể</th>
                                <th class="text-center pb-3 w-32">Số lượng</th>
                                <th class="text-right pb-3 w-44">Giá nhập</th>
                                <th class="text-right pb-3 w-44">Thành tiền</th>
                                <th class="w-16"></th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            <template
                                x-for="(item, index) in items"
                                :key="index"
                            >
                                <tr>

                                    <td class="py-4 pr-4">
                                        <select
                                            :name="`items[${index}][variant_id]`"
                                            x-model="item.variant_id"
                                            class="w-full border-gray-300 rounded-lg"
                                        >

                                            <option value="">
                                                -- Chọn biến thể --
                                            </option>

                                            @foreach($variants as $variant)

                                                @php
                                                    $variantName = $variant->attributeValues
                                                        ->map(fn($value) =>
                                                            $value->attribute->name . ': ' . $value->value
                                                        )
                                                        ->join(' / ');
                                                @endphp

                                                <option value="{{ $variant->id }}">
                                                    {{ $variant->product->name }}
                                                    -
                                                    {{ $variant->sku }}
                                                    @if($variantName)
                                                        ({{ $variantName }})
                                                    @endif
                                                </option>

                                            @endforeach

                                        </select>
                                    </td>

                                    <td class="py-4 px-2">
                                        <input
                                            type="number"
                                            min="1"
                                            :name="`items[${index}][quantity]`"
                                            x-model.number="item.quantity"
                                            class="w-full border-gray-300 rounded-lg text-center"
                                        >
                                    </td>

                                    <td class="py-4 px-2">
                                        <input
                                            type="number"
                                            min="0"
                                            step="1000"
                                            :name="`items[${index}][import_price]`"
                                            x-model.number="item.import_price"
                                            class="w-full border-gray-300 rounded-lg text-right"
                                        >
                                    </td>

                                    <td class="py-4 px-2 text-right font-medium">
                                        <span
                                            x-text="formatMoney(item.quantity * item.import_price)"
                                        ></span>
                                        ₫
                                    </td>

                                    <td class="py-4 pl-2 text-right">
                                        <button
                                            type="button"
                                            @click="removeItem(index)"
                                            class="text-red-500 hover:text-red-700"
                                        >
                                            Xóa
                                        </button>
                                    </td>

                                </tr>
                            </template>

                        </tbody>
                    </table>
                </div>

                <button
                    type="button"
                    @click="addItem()"
                    class="mt-4 px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200"
                >
                    + Thêm biến thể
                </button>

            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            <div class="bg-white rounded-xl border shadow-sm p-6">
                <label class="block font-medium mb-2">
                    Ghi chú
                </label>

                <textarea
                    name="note"
                    rows="5"
                    class="w-full border-gray-300 rounded-lg"
                    placeholder="Ghi chú cho phiếu nhập..."
                >{{ old('note') }}</textarea>
            </div>

            <div class="bg-white rounded-xl border shadow-sm p-6">

                <div class="flex justify-between items-center">
                    <span class="text-gray-500">
                        Tổng tiền nhập
                    </span>

                    <span
                        class="text-2xl font-bold text-green-600"
                        x-text="formatMoney(total())"
                    ></span>
                    <span class="ml-1 font-bold text-green-600">₫</span>
                </div>

                <div class="mt-6 flex justify-end gap-3">

                    <a
                        href="{{ route('admin.purchase-receipts.index') }}"
                        class="px-5 py-2.5 rounded-lg border hover:bg-gray-50"
                    >
                        Hủy
                    </a>

                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white"
                    >
                        Lưu phiếu nháp
                    </button>

                </div>

            </div>

        </div>

    </form>

</div>

<script>
function purchaseReceiptForm() {
    return {
        items: [
            {
                variant_id: '',
                quantity: 1,
                import_price: 0
            }
        ],

        addItem() {
            this.items.push({
                variant_id: '',
                quantity: 1,
                import_price: 0
            });
        },

        removeItem(index) {
            if (this.items.length === 1) {
                return;
            }

            this.items.splice(index, 1);
        },

        total() {
            return this.items.reduce((total, item) => {
                return total +
                    ((Number(item.quantity) || 0) *
                    (Number(item.import_price) || 0));
            }, 0);
        },

        formatMoney(value) {
            return new Intl.NumberFormat('vi-VN').format(value || 0);
        }
    }
}
</script>
@endsection