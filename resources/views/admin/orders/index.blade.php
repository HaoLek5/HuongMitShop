@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                Quản lý đơn hàng
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Quản lý đơn chờ đi, đơn đã đi và quá trình giao hàng
            </p>
        </div>

    </div>


    {{-- THÔNG BÁO --}}
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


    {{-- BỘ LỌC --}}
    <div class="bg-white rounded-xl border shadow-sm p-5">

        <form
            method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-3"
        >

            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Mã đơn, tên hoặc SĐT..."
                class="md:col-span-2 border-gray-300 rounded-lg"
            >


            <select
                name="status"
                class="border-gray-300 rounded-lg"
            >

                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="pending"
                    {{ request('status') === 'pending' ? 'selected' : '' }}
                >
                    Chờ shop đi đơn
                </option>

                <option
                    value="processing"
                    {{ request('status') === 'processing' ? 'selected' : '' }}
                >
                    Đã đi đơn
                </option>

                <option
                    value="shipping"
                    {{ request('status') === 'shipping' ? 'selected' : '' }}
                >
                    Đang giao
                </option>

                <option
                    value="delivered"
                    {{ request('status') === 'delivered' ? 'selected' : '' }}
                >
                    Đã giao
                </option>

                <option
                    value="cancelled"
                    {{ request('status') === 'cancelled' ? 'selected' : '' }}
                >
                    Đã hủy
                </option>

            </select>


            <button
                type="submit"
                class="px-5 py-2.5 rounded-lg bg-green-600 hover:bg-green-700 text-white"
            >
                Tìm kiếm
            </button>

        </form>

    </div>


    {{-- DANH SÁCH --}}
    <form
    method="POST"
    action="{{ route('admin.orders.print') }}"
    onsubmit="return validatePrintForm(this);"
    >

        @csrf


        <div class="bg-white rounded-xl border shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b flex items-center justify-between">

                <div>
                    <h2 class="font-semibold text-gray-800">
                        Danh sách đơn hàng
                    </h2>

                    <p class="text-xs text-gray-500 mt-1">
                        Chọn các đơn đủ điều kiện rồi bấm "In đơn đã chọn".
                    </p>
                </div>


                <button
                    type="submit"
                    class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium"
                >
                    🖨 In đơn đã chọn
                </button>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-gray-50 border-b">

                        <tr>

                            <th class="px-6 py-4 text-center">
                                <input
                                    type="checkbox"
                                    id="checkAll"
                                    class="rounded"
                                >
                            </th>

                            <th class="text-left px-6 py-4">
                                Mã đơn
                            </th>

                            <th class="text-left px-6 py-4">
                                Khách hàng
                            </th>

                            <th class="text-left px-6 py-4">
                                Người nhận
                            </th>

                            <th class="text-right px-6 py-4">
                                Tổng tiền
                            </th>

                            <th class="text-center px-6 py-4">
                                Thanh toán
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

                        @forelse($orders as $order)

                            <tr class="hover:bg-gray-50">


                                {{-- CHECKBOX --}}

                                <td class="px-6 py-4 text-center">

                                    @if(
                                        $order->order_status === 'pending'
                                        && $order->can_be_processed
                                    )

                                        <input
                                            type="checkbox"
                                            name="ids[]"
                                            value="{{ $order->id }}"
                                            class="order-checkbox rounded"
                                        >

                                    @elseif($order->order_status === 'pending')

                                        <span
                                            title="Đơn chưa đủ điều kiện đi đơn"
                                            class="text-gray-300"
                                        >
                                            🔒
                                        </span>

                                    @endif

                                </td>


                                {{-- MÃ ĐƠN --}}

                                <td class="px-6 py-4 font-medium">

                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="text-green-600 hover:text-green-700"
                                    >
                                        {{ $order->order_code }}
                                    </a>

                                </td>


                                {{-- KHÁCH --}}

                                <td class="px-6 py-4">

                                    <div class="font-medium">
                                        {{ $order->user->name ?? '—' }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $order->user->phone ?? '—' }}
                                    </div>

                                </td>


                                {{-- NGƯỜI NHẬN --}}

                                <td class="px-6 py-4">

                                    <div>
                                        {{ $order->receiver_name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $order->receiver_phone }}
                                    </div>

                                </td>


                                {{-- TỔNG TIỀN --}}

                                <td class="px-6 py-4 text-right font-medium">

                                    {{ number_format(
                                        $order->total_amount,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                    ₫

                                </td>


                                {{-- PAYMENT --}}

                                <td class="px-6 py-4 text-center">

                                    @if(
                                        $order->payment_method === 'cod'
                                        && !$order->deposit_required
                                    )

                                        <span class="px-2.5 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                            COD
                                        </span>

                                    @elseif(
                                        $order->payment_status === 'deposit_pending'
                                    )

                                        <span class="px-2.5 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                            Chờ cọc
                                        </span>

                                    @elseif(
                                        $order->payment_status === 'deposit_paid'
                                    )

                                        <span class="px-2.5 py-1 rounded-full text-xs bg-orange-100 text-orange-700">
                                            Đã cọc
                                        </span>

                                    @elseif(
                                        $order->payment_status === 'paid'
                                    )

                                        <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                            Đã thanh toán
                                        </span>

                                    @else

                                        <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                            Chưa thanh toán
                                        </span>

                                    @endif

                                </td>


                                {{-- STATUS --}}

                                <td class="px-6 py-4 text-center">

                                    @switch($order->order_status)

                                        @case('pending')

                                            @if($order->can_be_processed)

                                                <span class="px-2.5 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">
                                                    Chờ shop đi đơn
                                                </span>

                                            @else

                                                <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                                    Chờ thanh toán
                                                </span>

                                            @endif

                                            @break


                                        @case('processing')

                                            <span class="px-2.5 py-1 rounded-full text-xs bg-blue-100 text-blue-700">
                                                Đã đi đơn
                                            </span>

                                            @break


                                        @case('shipping')

                                            <span class="px-2.5 py-1 rounded-full text-xs bg-orange-100 text-orange-700">
                                                Đang giao
                                            </span>

                                            @break


                                        @case('delivered')

                                            <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">
                                                Đã giao
                                            </span>

                                            @break


                                        @case('cancelled')

                                            <span class="px-2.5 py-1 rounded-full text-xs bg-red-100 text-red-700">
                                                Đã hủy
                                            </span>

                                            @break


                                        @default

                                            <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-600">
                                                {{ $order->order_status }}
                                            </span>

                                    @endswitch

                                </td>


                                {{-- THAO TÁC --}}

                                <td class="px-6 py-4 text-right">

                                    <a
                                        href="{{ route('admin.orders.show', $order) }}"
                                        class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200"
                                    >
                                        Xem
                                    </a>


                                    @if($order->order_status === 'processing')

                                        <a
                                            href="{{ route('admin.orders.print-view', ['ids[]' => $order->id]) }}"
                                            target="_blank"
                                            class="ml-1 px-3 py-1.5 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200"
                                        >
                                            In lại
                                        </a>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="px-6 py-12 text-center text-gray-500"
                                >
                                    Chưa có đơn hàng.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </form>


    {{-- PAGINATION --}}

    <div>
        {{ $orders->links() }}
    </div>

</div>


<script>

function validatePrintForm(form) {

    const checked =
        form.querySelectorAll(
            'input[name="ids[]"]:checked'
        );

    if (checked.length === 0) {

        alert(
            'Vui lòng chọn ít nhất một đơn có thể đi đơn.'
        );

        return false;
    }

    return confirm(
        'Xác nhận in các đơn đã chọn? Các đơn này sẽ chuyển sang trạng thái Đã đi đơn.'
    );
}


const checkAll =
    document.getElementById('checkAll');


if (checkAll) {

    checkAll.addEventListener(
        'change',
        function () {

            const checkboxes =
                document.querySelectorAll(
                    '.order-checkbox'
                );

            checkboxes.forEach(function (checkbox) {

                checkbox.checked =
                    checkAll.checked;

            });

        }
    );

}

</script>

@endsection