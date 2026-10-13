<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    /**
     * Lấy hoặc tạo giỏ hàng của user.
     */
    public function getOrCreateCart(int $userId): Cart
    {
        return Cart::firstOrCreate([
            'user_id' => $userId,
        ]);
    }

    /**
     * Thêm variant vào giỏ.
     */
    public function addItem(
        int $userId,
        ProductVariant $variant,
        int $quantity
    ): void {
        DB::transaction(function () use (
            $userId,
            $variant,
            $quantity
        ) {
            if (!$variant->status) {
                throw ValidationException::withMessages([
                    'variant' => 'Biến thể này hiện không được bán.',
                ]);
            }

            if (!$variant->product || !$variant->product->status) {
                throw ValidationException::withMessages([
                    'variant' => 'Sản phẩm này hiện không được bán.',
                ]);
            }

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng phải lớn hơn 0.',
                ]);
            }

            if ($variant->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng sản phẩm trong kho không đủ.',
                ]);
            }

            $cart = $this->getOrCreateCart($userId);

            $item = $cart->items()
                ->where('variant_id', $variant->id)
                ->first();

            if ($item) {
                $newQuantity = $item->quantity + $quantity;

                if ($newQuantity > $variant->stock_quantity) {
                    throw ValidationException::withMessages([
                        'quantity' => 'Số lượng trong giỏ vượt quá tồn kho.',
                    ]);
                }

                $item->update([
                    'quantity' => $newQuantity,
                    'price' => $variant->price,
                ]);

                return;
            }

            $cart->items()->create([
                'variant_id' => $variant->id,
                'quantity' => $quantity,
                'price' => $variant->price,
            ]);
        });
    }

    /**
     * Cập nhật số lượng.
     */
    public function updateItem(
        int $userId,
        CartItem $item,
        int $quantity
    ): void {
        DB::transaction(function () use (
            $userId,
            $item,
            $quantity
        ) {
            $cart = $this->getOrCreateCart($userId);

            if ($item->cart_id !== $cart->id) {
                abort(403);
            }

            if ($quantity < 1) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng phải lớn hơn 0.',
                ]);
            }

            $variant = ProductVariant::findOrFail($item->variant_id);

            if ($quantity > $variant->stock_quantity) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng vượt quá tồn kho hiện tại.',
                ]);
            }

            $item->update([
                'quantity' => $quantity,
                'price' => $variant->price,
            ]);
        });
    }

    /**
     * Xóa item khỏi giỏ.
     */
    public function removeItem(int $userId, CartItem $item): void
    {
        $cart = $this->getOrCreateCart($userId);

        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        $item->delete();
    }

    /**
     * Xóa toàn bộ giỏ.
     */
    public function clear(int $userId): void
    {
        $cart = $this->getOrCreateCart($userId);

        $cart->items()->delete();
    }
}