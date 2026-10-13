<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryTransaction;
use App\Models\ProductVariant;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    public function __construct(
        private InventoryService $inventoryService
    ) {
    }

    /**
     * Danh sách tồn kho.
     */
    public function index(Request $request)
    {
        $query = ProductVariant::with([
            'product.category',
            'attributeValues.attribute',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($productQuery) use ($search) {
                        $productQuery->where(
                            'name',
                            'like',
                            "%{$search}%"
                        );
                    });
            });
        }

        $variants = $query
            ->orderBy('stock_quantity')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.inventory.index',
            compact('variants')
        );
    }

    /**
     * Xem lịch sử biến động của một variant.
     */
    public function show(ProductVariant $variant)
    {
        $variant->load([
            'product',
            'attributeValues.attribute',
        ]);

        $transactions = InventoryTransaction::with('creator')
            ->where('variant_id', $variant->id)
            ->latest('created_at')
            ->paginate(15);

        return view(
            'admin.inventory.show',
            compact('variant', 'transactions')
        );
    }

    /**
     * Điều chỉnh tồn kho.
     */
    public function adjust(Request $request, ProductVariant $variant)
    {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'between:-100000,100000',
                'not_in:0',
            ],

            'note' => [
                'required',
                'string',
                'max:1000',
            ],
        ], [
            'quantity.required' => 'Vui lòng nhập số lượng điều chỉnh.',
            'quantity.not_in' => 'Số lượng điều chỉnh không được bằng 0.',
            'note.required' => 'Vui lòng nhập lý do điều chỉnh.',
        ]);

        try {
            $this->inventoryService->adjust(
                $variant,
                (int) $validated['quantity'],
                $validated['note'],
                (int) Auth::id()
            );

            return back()->with(
                'success',
                'Đã điều chỉnh tồn kho thành công.'
            );

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', 'Không thể điều chỉnh tồn kho.');
        }
    }
}