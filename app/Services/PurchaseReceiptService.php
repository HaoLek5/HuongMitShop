<?php

namespace App\Services;

use App\Models\PurchaseReceipt;
use App\Models\ProductVariant;
use App\Models\InventoryTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseReceiptService
{
    /**
     * Tạo phiếu nhập ở trạng thái draft.
     */
    public function createDraft(array $data, int $createdBy): PurchaseReceipt
    {
        return DB::transaction(function () use ($data, $createdBy) {

            $totalAmount = 0;

            foreach ($data['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['import_price'];
            }

            $receipt = PurchaseReceipt::create([
                'receipt_code' => $this->generateReceiptCode(),
                'created_by' => $createdBy,
                'total_amount' => $totalAmount,
                'status' => 'draft',
                'note' => $data['note'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                $receipt->items()->create([
                    'variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'import_price' => $item['import_price'],
                    'subtotal' => $item['quantity'] * $item['import_price'],
                ]);
            }

            return $receipt;
        });
    }

    /**
     * Hoàn tất phiếu nhập:
     * - cộng tồn kho
     * - tạo lịch sử inventory_transactions
     */
    public function complete(PurchaseReceipt $receipt, int $createdBy): void
    {
        DB::transaction(function () use ($receipt, $createdBy) {

            // Không cho hoàn tất lại
            if ($receipt->status !== 'draft') {
                throw ValidationException::withMessages([
                    'receipt' => 'Chỉ phiếu nhập ở trạng thái Nháp mới được hoàn tất.',
                ]);
            }

            $receipt->load('items');

            foreach ($receipt->items as $item) {

                // Lock variant để tránh cập nhật kho đồng thời
                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->findOrFail($item->variant_id);

                // Cộng tồn
                $variant->increment('stock_quantity', $item->quantity);

                // Tạo lịch sử biến động kho
                InventoryTransaction::create([
                    'variant_id' => $variant->id,
                    'type' => 'import',
                    'quantity' => $item->quantity,
                    'reference_type' => 'purchase_receipt',
                    'reference_id' => $receipt->id,
                    'note' => 'Nhập hàng theo phiếu ' . $receipt->receipt_code,
                    'created_by' => $createdBy,
                    'created_at' => now(),
                ]);
            }

            $receipt->update([
                'status' => 'completed',
            ]);
        });
    }

    /**
     * Hủy phiếu nhập.
     */
    public function cancel(PurchaseReceipt $receipt): void
    {
        if ($receipt->status !== 'draft') {
            throw ValidationException::withMessages([
                'receipt' => 'Chỉ phiếu nhập ở trạng thái Nháp mới được hủy.',
            ]);
        }

        $receipt->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Sinh mã phiếu nhập.
     */
    private function generateReceiptCode(): string
    {
        do {
            $code = 'PN-' . now()->format('YmdHis') . '-' . random_int(1000, 9999);
        } while (
            PurchaseReceipt::where('receipt_code', $code)->exists()
        );

        return $code;
    }
}