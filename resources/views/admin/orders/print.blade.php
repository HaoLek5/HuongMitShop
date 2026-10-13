<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        In đơn hàng
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 20px;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
            background: #f3f4f6;
        }

        .print-actions {
            max-width: 900px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .print-actions button,
        .print-actions a {
            border: 0;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .print-button {
            background: #16a34a;
            color: white;
        }

        .back-button {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db !important;
        }

        .order-page {
            max-width: 900px;
            margin: 0 auto 24px;
            background: white;
            padding: 35px;
            border: 1px solid #e5e7eb;
        }

        .shop-header {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            border-bottom: 2px solid #111827;
            padding-bottom: 18px;
            margin-bottom: 20px;
        }

        .shop-name {
            font-size: 24px;
            font-weight: 700;
        }

        .order-title {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
        }

        .section {
            margin-top: 22px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px 30px;
            font-size: 14px;
        }

        .info-row {
            display: flex;
            gap: 8px;
        }

        .label {
            color: #6b7280;
            min-width: 110px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 9px;
        }

        th {
            text-align: left;
            background: #f3f4f6;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .summary {
            margin-left: auto;
            margin-top: 18px;
            width: 320px;
            font-size: 14px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
        }

        .summary-total {
            border-top: 2px solid #111827;
            margin-top: 8px;
            padding-top: 10px;
            font-size: 18px;
            font-weight: 700;
        }

        .note {
            margin-top: 20px;
            padding: 12px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .status {
            margin-top: 20px;
            font-weight: 700;
        }

        @media print {

            body {
                background: white;
                padding: 0;
            }

            .print-actions {
                display: none;
            }

            .order-page {
                max-width: none;
                margin: 0;
                border: 0;
                min-height: 100vh;
                page-break-after: always;
            }

            .order-page:last-child {
                page-break-after: auto;
            }

        }

    </style>

</head>


<body>

    <div class="print-actions">

        <a
            href="{{ route('admin.orders.index') }}"
            class="back-button"
        >
            ← Quay lại
        </a>

        <button
            type="button"
            class="print-button"
            onclick="window.print()"
        >
            🖨 In đơn
        </button>

    </div>


    @foreach($orders as $order)

        <div class="order-page">


            {{-- HEADER --}}

            <div class="shop-header">

                <div>

                    <div class="shop-name">
                        Hương Mít Shop
                    </div>

                    <div style="margin-top: 5px; font-size: 13px;">
                        Đơn hàng online
                    </div>

                </div>


                <div>

                    <div class="order-title">
                        PHIẾU ĐI ĐƠN
                    </div>

                    <div style="margin-top: 6px; font-size: 13px;">
                        Mã đơn:
                        <strong>
                            {{ $order->order_code }}
                        </strong>
                    </div>

                    <div style="margin-top: 4px; font-size: 13px;">
                        Ngày:
                        {{ $order->created_at->format('d/m/Y H:i') }}
                    </div>

                </div>

            </div>


            {{-- NGƯỜI NHẬN --}}

            <div class="section">

                <div class="section-title">
                    THÔNG TIN GIAO HÀNG
                </div>


                <div class="info-grid">

                    <div class="info-row">

                        <span class="label">
                            Người nhận:
                        </span>

                        <strong>
                            {{ $order->receiver_name }}
                        </strong>

                    </div>


                    <div class="info-row">

                        <span class="label">
                            Số điện thoại:
                        </span>

                        <strong>
                            {{ $order->receiver_phone }}
                        </strong>

                    </div>


                    <div
                        class="info-row"
                        style="grid-column: 1 / -1;"
                    >

                        <span class="label">
                            Địa chỉ:
                        </span>

                        <strong>
                            {{ $order->shipping_address }}
                        </strong>

                    </div>

                </div>

            </div>


            {{-- SẢN PHẨM --}}

            <div class="section">

                <div class="section-title">
                    SẢN PHẨM
                </div>


                <table>

                    <thead>

                        <tr>

                            <th style="width: 35px;">
                                STT
                            </th>

                            <th>
                                Sản phẩm
                            </th>

                            <th>
                                SKU
                            </th>

                            <th>
                                Biến thể
                            </th>

                            <th
                                class="text-center"
                                style="width: 70px;"
                            >
                                SL
                            </th>

                            <th class="text-right">
                                Đơn giá
                            </th>

                            <th class="text-right">
                                Thành tiền
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($order->items as $index => $item)

                            <tr>

                                <td class="text-center">
                                    {{ $index + 1 }}
                                </td>

                                <td>
                                    {{ $item->product_name }}
                                </td>

                                <td>
                                    {{ $item->sku }}
                                </td>

                                <td>
                                    {{ $item->variant_name ?: '—' }}
                                </td>

                                <td class="text-center">
                                    {{ $item->quantity }}
                                </td>

                                <td class="text-right">
                                    {{ number_format(
                                        $item->price,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                    ₫
                                </td>

                                <td class="text-right">
                                    {{ number_format(
                                        $item->subtotal,
                                        0,
                                        ',',
                                        '.'
                                    ) }}
                                    ₫
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            {{-- THANH TOÁN --}}

            <div class="summary">

                <div class="summary-row">

                    <span>
                        Tiền hàng
                    </span>

                    <span>
                        {{ number_format(
                            $order->subtotal,
                            0,
                            ',',
                            '.'
                        ) }}
                        ₫
                    </span>

                </div>


                <div class="summary-row">

                    <span>
                        Giảm giá
                    </span>

                    <span>
                        {{ number_format(
                            $order->discount_amount,
                            0,
                            ',',
                            '.'
                        ) }}
                        ₫
                    </span>

                </div>


                <div class="summary-row">

                    <span>
                        Phí vận chuyển
                    </span>

                    <span>
                        {{ number_format(
                            $order->shipping_fee,
                            0,
                            ',',
                            '.'
                        ) }}
                        ₫
                    </span>

                </div>


                <div class="summary-row summary-total">

                    <span>
                        Tổng cộng
                    </span>

                    <span>
                        {{ number_format(
                            $order->total_amount,
                            0,
                            ',',
                            '.'
                        ) }}
                        ₫
                    </span>

                </div>


                @if($order->deposit_required)

                    <div
                        class="summary-row"
                        style="margin-top: 8px;"
                    >

                        <span>
                            Đã cọc
                        </span>

                        <span>
                            {{ number_format(
                                $order->paid_amount,
                                0,
                                ',',
                                '.'
                            ) }}
                            ₫
                        </span>

                    </div>


                    <div class="summary-row">

                        <span>
                            Còn thu
                        </span>

                        <strong>
                            {{ number_format(
                                $order->remaining_amount,
                                0,
                                ',',
                                '.'
                            ) }}
                            ₫
                        </strong>

                    </div>

                @endif

            </div>


            {{-- PHƯƠNG THỨC --}}

            <div class="section">

                <div class="section-title">
                    THANH TOÁN
                </div>

                <div style="font-size: 14px;">

                    @if($order->payment_method === 'cod')

                        Thanh toán khi nhận hàng (COD)

                    @else

                        Chuyển khoản ngân hàng

                    @endif

                </div>

            </div>


            {{-- GHI CHÚ --}}

            @if($order->note)

                <div class="note">

                    <strong>
                        Ghi chú:
                    </strong>

                    {{ $order->note }}

                </div>

            @endif


            <div class="status">

                Trạng thái:
                ĐÃ ĐI ĐƠN

            </div>


        </div>

    @endforeach


    <script>

        window.addEventListener(
            'load',
            function () {

                setTimeout(
                    function () {
                        window.print();
                    },
                    400
                );

            }
        );

    </script>

</body>

</html>