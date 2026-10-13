<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Kiểm tra đơn có đủ điều kiện để shop đi đơn hay chưa.
     *
     * Quy tắc:
     *
     * COD không cọc:
     *     unpaid / paid -> được đi đơn
     *
     * COD có cọc:
     *     deposit_paid / paid -> được đi đơn
     *
     * Chuyển khoản toàn bộ:
     *     paid -> được đi đơn
     */
    public function canBeProcessed(Order $order): bool
    {
        if ($order->order_status !== 'pending') {
            return false;
        }

        /*
         * COD
         */
        if ($order->payment_method === 'cod') {

            /*
             * COD phải cọc
             */
            if ($order->deposit_required) {
                return in_array(
                    $order->payment_status,
                    [
                        'deposit_paid',
                        'paid',
                    ],
                    true
                );
            }

            /*
             * COD không cọc:
             * khách chưa trả tiền nhưng vẫn được đi đơn.
             */
            return in_array(
                $order->payment_status,
                [
                    'unpaid',
                    'paid',
                ],
                true
            );
        }

        /*
         * Chuyển khoản toàn bộ:
         * phải được xác nhận đã thanh toán.
         */
        if ($order->payment_method === 'bank_transfer') {

            return $order->payment_status === 'paid';
        }

        return false;
    }

    /**
     * Chuyển một hoặc nhiều đơn từ:
     *
     * pending
     *     ↓
     * processing
     *
     * processing = Đã đi đơn
     */
    public function markAsProcessing(
        array $orderIds,
        int $updatedBy
    ): array {
        return DB::transaction(function () use (
            $orderIds,
            $updatedBy
        ) {

            $orderIds = array_values(
                array_unique(
                    array_map('intval', $orderIds)
                )
            );

            if (empty($orderIds)) {
                throw ValidationException::withMessages([
                    'orders' => 'Chưa chọn đơn hàng nào.',
                ]);
            }

            /*
             * Khóa tất cả đơn được chọn.
             */
            $orders = Order::query()
                ->whereIn('id', $orderIds)
                ->lockForUpdate()
                ->get();

            /*
             * Nếu thiếu đơn nào thì dừng toàn bộ.
             */
            if ($orders->count() !== count($orderIds)) {
                throw ValidationException::withMessages([
                    'orders' => 'Một hoặc nhiều đơn hàng không tồn tại.',
                ]);
            }

            /*
             * Kiểm tra từng đơn.
             */
            foreach ($orders as $order) {

                if ($order->order_status !== 'pending') {
                    throw ValidationException::withMessages([
                        'orders' =>
                            'Đơn ' . $order->order_code .
                            ' không còn ở trạng thái chờ shop đi đơn.',
                    ]);
                }

                if (!$this->canBeProcessed($order)) {

                    throw ValidationException::withMessages([
                        'orders' =>
                            'Đơn ' . $order->order_code .
                            ' chưa đủ điều kiện để đi đơn.',
                    ]);
                }
            }

            /*
             * Tất cả hợp lệ -> chuyển sang processing.
             */
            foreach ($orders as $order) {

                $order->update([
                    'order_status' => 'processing',
                ]);
            }

            return $orders->values()->all();
        });
    }

    /**
     * Cập nhật trạng thái đơn sau khi đã đi đơn.
     *
     * processing -> shipping
     * shipping   -> delivered
     *
     * Có thể hủy trước khi giao.
     */
    public function updateStatus(
        Order $order,
        string $newStatus,
        int $updatedBy
    ): void {
        DB::transaction(function () use (
            $order,
            $newStatus,
            $updatedBy
        ) {

            $lockedOrder = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            /*
             * ===============================
             * PENDING -> PROCESSING
             * ===============================
             *
             * Không cho đổi bằng dropdown.
             * Phải thông qua nghiệp vụ "In đơn".
             */
            if ($newStatus === 'processing') {

                if (!$this->canBeProcessed($lockedOrder)) {

                    throw ValidationException::withMessages([
                        'status' =>
                            'Đơn chưa đủ điều kiện để đi đơn.',
                    ]);
                }

                $lockedOrder->update([
                    'order_status' => 'processing',
                ]);

                return;
            }

            /*
             * ===============================
             * CANCELLED
             * ===============================
             */
            if ($newStatus === 'cancelled') {

                /*
                 * Chỉ cho hủy khi chưa giao.
                 *
                 * Với đơn đã đi đơn, vẫn có thể hoàn tồn
                 * theo logic hiện tại.
                 */
                if (
                    !in_array(
                        $lockedOrder->order_status,
                        [
                            'pending',
                            'processing',
                        ],
                        true
                    )
                ) {
                    throw ValidationException::withMessages([
                        'status' =>
                            'Không thể hủy đơn ở trạng thái hiện tại.',
                    ]);
                }

                /*
                 * Nếu đơn processing, kho đã bị trừ
                 * từ lúc checkout -> hoàn lại.
                 */
                if ($lockedOrder->order_status === 'processing') {

                    $this->restoreStock(
                        $lockedOrder,
                        $updatedBy
                    );
                }

                $lockedOrder->update([
                    'order_status' => 'cancelled',
                ]);

                return;
            }

            /*
             * ===============================
             * PROCESSING -> SHIPPING
             * ===============================
             */
            if ($newStatus === 'shipping') {

                if ($lockedOrder->order_status !== 'processing') {

                    throw ValidationException::withMessages([
                        'status' =>
                            'Chỉ đơn đã đi đơn mới có thể chuyển sang đang giao.',
                    ]);
                }

                $lockedOrder->update([
                    'order_status' => 'shipping',
                ]);

                return;
            }

            /*
             * ===============================
             * SHIPPING -> DELIVERED
             * ===============================
             */
            if ($newStatus === 'delivered') {

                if ($lockedOrder->order_status !== 'shipping') {

                    throw ValidationException::withMessages([
                        'status' =>
                            'Chỉ đơn đang giao mới có thể chuyển sang đã giao.',
                    ]);
                }

                $lockedOrder->update([
                    'order_status' => 'delivered',
                ]);

                return;
            }

            throw ValidationException::withMessages([
                'status' => 'Trạng thái đơn hàng không hợp lệ.',
            ]);
        });
    }

    /**
     * Hoàn lại tồn kho khi hủy đơn.
     */
    private function restoreStock(
        Order $order,
        int $updatedBy
    ): void {
        $order->load('items');

        foreach ($order->items as $item) {

            /*
             * Không hoàn kho lần thứ hai.
             */
            $alreadyRestored = InventoryTransaction::query()
                ->where('variant_id', $item->variant_id)
                ->where('type', 'return')
                ->where('reference_type', 'order')
                ->where('reference_id', $order->id)
                ->exists();

            if ($alreadyRestored) {
                continue;
            }

            $variant = ProductVariant::query()
                ->lockForUpdate()
                ->find($item->variant_id);

            if (!$variant) {
                throw ValidationException::withMessages([
                    'order' =>
                        'Không tìm thấy biến thể của sản phẩm trong đơn.',
                ]);
            }

            $variant->increment(
                'stock_quantity',
                $item->quantity
            );

            InventoryTransaction::create([
                'variant_id' => $variant->id,
                'type' => 'return',
                'quantity' => $item->quantity,
                'reference_type' => 'order',
                'reference_id' => $order->id,
                'note' =>
                    'Hoàn tồn do hủy đơn ' .
                    $order->order_code,
                'created_by' => $updatedBy,
                'created_at' => now(),
            ]);
        }
    }
}