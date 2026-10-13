<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseReceipt;
use App\Models\ProductVariant;
use App\Services\PurchaseReceiptService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PurchaseReceiptController extends Controller
{
    public function __construct(
        private PurchaseReceiptService $purchaseReceiptService
    ) {
    }

    /**
     * Danh sách phiếu nhập
     */
    public function index()
    {
        $receipts = PurchaseReceipt::with('creator')
            ->withCount('items')
            ->latest()
            ->paginate(10);

        return view('admin.purchase-receipts.index', compact('receipts'));
    }

    /**
     * Form tạo phiếu nhập
     */
    public function create()
    {
        $variants = ProductVariant::with('product', 'attributeValues.attribute')
            ->where('status', true)
            ->orderBy('id')
            ->get();

        return view('admin.purchase-receipts.create', compact('variants'));
    }

    /**
     * Lưu phiếu nhập draft
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],

            'items.*.variant_id' => [
                'required',
                'integer',
                'distinct',
                'exists:product_variants,id',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.import_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'note' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ], [
            'items.required' => 'Phiếu nhập phải có ít nhất một sản phẩm.',
            'items.*.variant_id.distinct' => 'Không được chọn trùng biến thể.',
            'items.*.quantity.min' => 'Số lượng phải lớn hơn 0.',
            'items.*.import_price.min' => 'Giá nhập không được âm.',
        ]);

        $this->purchaseReceiptService->createDraft(
            $validated,
            (int) Auth::id()
        );

        return redirect()
            ->route('admin.purchase-receipts.index')
            ->with('success', 'Tạo phiếu nhập thành công.');
    }

    /**
     * Xem chi tiết phiếu nhập
     */
    public function show(PurchaseReceipt $purchaseReceipt)
    {
        $purchaseReceipt->load([
            'creator',
            'items.variant.product',
            'items.variant.attributeValues.attribute',
        ]);

        return view(
            'admin.purchase-receipts.show',
            compact('purchaseReceipt')
        );
    }

    /**
     * Hoàn tất phiếu nhập
     */
    public function complete(PurchaseReceipt $purchaseReceipt)
    {
        try {
            $this->purchaseReceiptService->complete(
                $purchaseReceipt,
                (int) Auth::id()
            );

            return back()->with(
                'success',
                'Phiếu nhập đã hoàn tất và tồn kho đã được cập nhật.'
            );

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', 'Không thể hoàn tất phiếu nhập.');
        }
    }

    /**
     * Hủy phiếu nhập
     */
    public function cancel(PurchaseReceipt $purchaseReceipt)
    {
        try {
            $this->purchaseReceiptService->cancel($purchaseReceipt);

            return back()->with(
                'success',
                'Phiếu nhập đã được hủy.'
            );

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', 'Không thể hủy phiếu nhập.');
        }
    }
}