@extends('layouts.admin')

@section('title', 'Phiếu nhập hàng')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Phiếu nhập hàng
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Quản lý các lần nhập hàng vào kho
            </p>
        </div>

        <a
            href="{{ route('admin.purchase-receipts.create') }}"
            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg"
        >
            + Tạo phiếu nhập
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

    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="text-left px-6 py-4">Mã phiếu</th>
                    <th class="text-left px-6 py-4">Người tạo</th>
                    <th class="text-left px-6 py-4">Số dòng</th>
                    <th class="text-right px-6 py-4">Tổng tiền</th>
                    <th class="text-center px-6 py-4">Trạng thái</th>
                    <th class="text-right px-6 py-4">Ngày tạo</th>
                    <th class="text-right px-6 py-4">Thao tác</th>
                </tr>
            </thead>

            <tbody class="divide-y">
                @forelse($receipts as $receipt)
                    <tr class="hover:bg-gray-50">

                        <td class="px-6 py-4 font-medium">
                            {{ $receipt->receipt_code }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $receipt->creator->name ?? '—' }}
                        </td>

                        <td class="px-6 py-4">
                            {{ $receipt->items_count }}
                        </td>

                        <td class="px-6 py-4 text-right font-medium">
                            {{ number_format($receipt->total_amount, 0, ',', '.') }} ₫
                        </td>

                        <td class="px-6 py-4 text-center">
                            @if($receipt->status === 'draft')
                                <span class="px-2.5 py-1 text-xs rounded-full bg-yellow-100 text-yellow-700">
                                    Nháp
                                </span>
                            @elseif($receipt->status === 'completed')
                                <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Hoàn tất
                                </span>
                            @else
                                <span class="px-2.5 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                    Đã hủy
                                </span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-right text-gray-500">
                            {{ $receipt->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td class="px-6 py-4">
                            <div class="flex justify-end gap-2">

                                <a
                                    href="{{ route('admin.purchase-receipts.show', $receipt) }}"
                                    class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200"
                                >
                                    Xem
                                </a>

                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td
                            colspan="7"
                            class="px-6 py-12 text-center text-gray-500"
                        >
                            Chưa có phiếu nhập nào.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

    <div>
        {{ $receipts->links() }}
    </div>

</div>
@endsection