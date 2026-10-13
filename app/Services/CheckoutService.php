<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Cart;
use App\Models\InventoryTransaction;
use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    /**
     * Tạo đơn hàng từ giỏ hàng.
     */
    public function placeOrder(
        int $userId,
        int $addressId,
        string $paymentMethod,
        ?string $note = null
    ): Order {
        return DB::transaction(function () use (
            $userId,
            $addressId,
            $paymentMethod,
            $note
        ) {
            /*
             * 1. Lấy cart của user
             */
            $cart = Cart::where('user_id', $userId)
                ->with([
                    'items.variant.product',
                    'items.variant.attributeValues.attribute',
                ])
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Giỏ hàng đang trống.',
                ]);
            }

            /*
             * 2. Kiểm tra address thuộc user
             */
            $address = Address::where('id', $addressId)
                ->where('user_id', $userId)
                ->first();

            if (!$address) {
                throw ValidationException::withMessages([
                    'address_id' => 'Địa chỉ giao hàng không hợp lệ.',
                ]);
            }

            /*
             * 3. Kiểm tra payment method
             */
            if (!in_array($paymentMethod, [
                'cod',
                'bank_transfer',
            ], true)) {
                throw ValidationException::withMessages([
                    'payment_method' => 'Phương thức thanh toán không hợp lệ.',
                ]);
            }

            /*
             * 4. Lock tất cả variant và đọc giá/tồn kho THẬT từ DB.
             *
             * Không dùng cart_items.price ở bước này.
             */
            $subtotal = 0;
            $itemsData = [];

            foreach ($cart->items as $cartItem) {

                $variant = ProductVariant::query()
                    ->with([
                        'product',
                        'attributeValues.attribute',
                    ])
                    ->lockForUpdate()
                    ->find($cartItem->variant_id);

                if (!$variant) {
                    throw ValidationException::withMessages([
                        'cart' => 'Một sản phẩm trong giỏ không còn tồn tại.',
                    ]);
                }

                /*
                 * Kiểm tra variant còn được bán
                 */
                if (!$variant->status) {
                    throw ValidationException::withMessages([
                        'cart' => 'Biến thể ' . $variant->sku . ' hiện không còn bán.',
                    ]);
                }

                /*
                 * Kiểm tra product còn hoạt động
                 */
                if (!$variant->product || !$variant->product->status) {
                    throw ValidationException::withMessages([
                        'cart' => 'Sản phẩm của biến thể ' . $variant->sku . ' hiện không còn bán.',
                    ]);
                }

                /*
                 * Kiểm tra tồn kho lần cuối
                 */
                if ($variant->stock_quantity < $cartItem->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Sản phẩm ' . $variant->sku .
                            ' không đủ tồn kho. Tồn hiện tại: ' .
                            $variant->stock_quantity . '.',
                    ]);
                }

                /*
                 * Giá thật từ DB
                 */
                $price = (float) $variant->price;

                $quantity = (int) $cartItem->quantity;

                $itemSubtotal = $price * $quantity;

                /*
                 * Tạo tên variant snapshot
                 * Ví dụ: Đỏ - M
                 */
                $variantName = $variant->attributeValues
                    ->map(fn ($value) => $value->value)
                    ->join(' - ');

                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'variant' => $variant,
                    'product_name' => $variant->product->name,
                    'sku' => $variant->sku,
                    'variant_name' => $variantName,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => $itemSubtotal,
                ];
            }

            /*
             * 5. Phí ship hiện tại = 0
             *
             * Sau này có thể đưa logic phí ship vào Service.
             */
            $discountAmount = 0;
            $shippingFee = 0;

            $totalAmount =
                $subtotal
                - $discountAmount
                + $shippingFee;

            /*
             * 6. Tính tiền cọc
             */
            $depositThreshold = (float) config(
                'shop.deposit_threshold'
            );

            $depositPercent = (float) config(
                'shop.deposit_percent'
            );

            $depositRequired =
                $totalAmount >= $depositThreshold;

            $depositAmount = 0;

            if ($depositRequired) {
                $depositAmount = round(
                    $totalAmount * $depositPercent / 100,
                    0
                );
            }

            $paidAmount = 0;

            $remainingAmount = $totalAmount;

            /*
             * 7. Trạng thái payment ban đầu
             */
            if ($depositRequired) {
                $paymentStatus = 'deposit_pending';
            } else {
                $paymentStatus = 'unpaid';
            }

            /*
             * 8. Snapshot địa chỉ
             */
            $shippingAddress = $this->buildShippingAddress(
                $address
            );

            /*
             * 9. Tạo mã đơn
             */
            $orderCode = $this->generateOrderCode();

            /*
             * 10. Tạo Order
             */
            $order = Order::create([
                'order_code' => $orderCode,
                'user_id' => $userId,
                'address_id' => $address->id,

                'receiver_name' => $address->receiver_name,
                'receiver_phone' => $address->receiver_phone,
                'shipping_address' => $shippingAddress,

                'subtotal' => $subtotal,
                'discount_amount' => $discountAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $totalAmount,

                'payment_method' => $paymentMethod,
                'payment_status' => $paymentStatus,

                'deposit_required' => $depositRequired,
                'deposit_amount' => $depositAmount,

                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,

                'order_status' => 'pending',
                'note' => $note,
            ]);

            /*
             * 11. Tạo Order Items + trừ kho
             */
            foreach ($itemsData as $itemData) {

                $variant = $itemData['variant'];

                $order->items()->create([
                    'variant_id' => $variant->id,
                    'product_name' => $itemData['product_name'],
                    'sku' => $itemData['sku'],
                    'variant_name' => $itemData['variant_name'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'subtotal' => $itemData['subtotal'],
                ]);

                /*
                 * Trừ tồn kho
                 */
                $variant->decrement(
                    'stock_quantity',
                    $itemData['quantity']
                );

                /*
                 * Ghi lịch sử kho
                 */
                InventoryTransaction::create([
                    'variant_id' => $variant->id,
                    'type' => 'sale',
                    'quantity' => -$itemData['quantity'],
                    'reference_type' => 'order',
                    'reference_id' => $order->id,
                    'note' => 'Bán hàng theo đơn ' . $order->order_code,
                    'created_by' => $userId,
                    'created_at' => now(),
                ]);
            }

            /*
             * 12. Tạo Payment ban đầu
             */
            if ($depositRequired) {

                /*
                 * Đơn phải cọc:
                 * tiền cọc luôn qua bank transfer
                 */
                Payment::create([
                    'order_id' => $order->id,
                    'payment_type' => 'deposit',
                    'payment_method' => 'bank_transfer',
                    'amount' => $depositAmount,
                    'status' => 'pending',
                    'transaction_code' => null,
                    'proof_image' => null,
                    'paid_at' => null,
                    'confirmed_by' => null,
                    'confirmed_at' => null,
                    'note' => 'Tiền cọc đơn hàng',
                ]);

            } else {

                /*
                 * Đơn không phải cọc:
                 * thanh toán toàn bộ bằng phương thức khách chọn.
                 */
                Payment::create([
                    'order_id' => $order->id,
                    'payment_type' => 'final',
                    'payment_method' => $paymentMethod,
                    'amount' => $totalAmount,
                    'status' => 'pending',
                    'transaction_code' => null,
                    'proof_image' => null,
                    'paid_at' => null,
                    'confirmed_by' => null,
                    'confirmed_at' => null,
                    'note' => 'Thanh toán đơn hàng',
                ]);
            }

            /*
             * 13. Xóa cart
             */
            $cart->items()->delete();

            return $order->fresh([
                'items',
                'payments',
            ]);
        });
    }

    /**
     * Tạo chuỗi địa chỉ snapshot.
     */
    private function buildShippingAddress(Address $address): string
    {
        return collect([
            $address->detail_address,
            $address->ward,
            $address->district,
            $address->province,
        ])
            ->filter()
            ->join(', ');
    }

    /**
     * Sinh mã đơn.
     */
    private function generateOrderCode(): string
    {
        do {
            $code = 'DH-' . now()->format('YmdHis') .
                '-' . random_int(1000, 9999);

        } while (
            Order::where('order_code', $code)->exists()
        );

        return $code;
    }
}