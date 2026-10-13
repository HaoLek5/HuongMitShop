<?php

namespace App\Services;

use App\Models\InventoryTransaction;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryService
{
    /**
     * Điều chỉnh tồn kho thủ công.
     *
     * change_quantity:
     *  +5 = nhập thêm 5
     *  -3 = giảm 3
     */
    public function adjust(
        ProductVariant $variant,
        int $changeQuantity,
        string $note,
        int $createdBy
    ): void {
        DB::transaction(function () use (
            $variant,
            $changeQuantity,
            $note,
            $createdBy
        ) {
            if ($changeQuantity === 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Số lượng điều chỉnh không được bằng 0.',
                ]);
            }

            if (trim($note) === '') {
                throw ValidationException::withMessages([
                    'note' => 'Vui lòng nhập lý do điều chỉnh tồn kho.',
                ]);
            }

            $lockedVariant = ProductVariant::query()
                ->lockForUpdate()
                ->findOrFail($variant->id);

            $newStock = $lockedVariant->stock_quantity + $changeQuantity;

            if ($newStock < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Tồn kho không thể nhỏ hơn 0.',
                ]);
            }

            $lockedVariant->update([
                'stock_quantity' => $newStock,
            ]);

            InventoryTransaction::create([
                'variant_id' => $lockedVariant->id,
                'type' => 'adjustment',
                'quantity' => $changeQuantity,
                'reference_type' => 'manual',
                'reference_id' => null,
                'note' => $note,
                'created_by' => $createdBy,
                'created_at' => now(),
            ]);
        });
    }
}