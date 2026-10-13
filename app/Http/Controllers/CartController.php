<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {
    }

    /**
     * Xem giỏ hàng.
     */
    public function index()
    {
        $cart = $this->cartService->getOrCreateCart(
            (int) Auth::id()
        );

        $cart->load([
            'items.variant.product',
            'items.variant.attributeValues.attribute',
        ]);

        $total = $cart->items->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        return view('cart.index', compact(
            'cart',
            'total'
        ));
    }

    /**
     * Thêm sản phẩm vào giỏ.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => [
                'required',
                'integer',
                'exists:product_variants,id',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            $variant = ProductVariant::with('product')
                ->findOrFail($validated['variant_id']);

            $this->cartService->addItem(
                (int) Auth::id(),
                $variant,
                (int) $validated['quantity']
            );

            return back()->with(
                'success',
                'Đã thêm sản phẩm vào giỏ hàng.'
            );

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', 'Không thể thêm sản phẩm vào giỏ.');
        }
    }

    /**
     * Cập nhật số lượng.
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $validated = $request->validate([
            'quantity' => [
                'required',
                'integer',
                'min:1',
            ],
        ]);

        try {
            $this->cartService->updateItem(
                (int) Auth::id(),
                $cartItem,
                (int) $validated['quantity']
            );

            return back()->with(
                'success',
                'Đã cập nhật giỏ hàng.'
            );

        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->with('error', 'Không thể cập nhật giỏ hàng.');
        }
    }

    /**
     * Xóa một item.
     */
    public function destroy(CartItem $cartItem)
    {
        $this->cartService->removeItem(
            (int) Auth::id(),
            $cartItem
        );

        return back()->with(
            'success',
            'Đã xóa sản phẩm khỏi giỏ hàng.'
        );
    }

    /**
     * Xóa toàn bộ giỏ.
     */
    public function clear()
    {
        $this->cartService->clear(
            (int) Auth::id()
        );

        return back()->with(
            'success',
            'Đã xóa toàn bộ giỏ hàng.'
        );
    }
}