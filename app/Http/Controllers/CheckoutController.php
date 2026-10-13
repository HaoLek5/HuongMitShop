<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {
    }

    /**
     * Trang checkout.
     */
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with([
                'items.variant.product',
                'items.variant.attributeValues.attribute',
            ])
            ->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Giỏ hàng đang trống.');
        }

        $addresses = Address::where(
            'user_id',
            Auth::id()
        )
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        if ($addresses->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'Bạn chưa có địa chỉ giao hàng.'
                );
        }

        /*
         * Chỉ dùng để hiển thị.
         * Khi đặt hàng Service sẽ tính lại từ DB.
         */
        $displayTotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->variant->price;
        });

        return view('checkout.index', compact(
            'cart',
            'addresses',
            'displayTotal'
        ));
    }

    /**
     * Đặt hàng.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address_id' => [
                'required',
                'integer',
                'exists:addresses,id',
            ],

            'payment_method' => [
                'required',
                'in:cod,bank_transfer',
            ],

            'note' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);

        try {

            $order = $this->checkoutService->placeOrder(
                (int) Auth::id(),
                (int) $validated['address_id'],
                $validated['payment_method'],
                $validated['note'] ?? null
            );

            return redirect()
                ->route('checkout.success', $order)
                ->with(
                    'success',
                    'Đặt hàng thành công.'
                );

        } catch (ValidationException $e) {

            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with(
                    'error',
                    'Không thể đặt hàng.'
                );
        }
    }

    /**
     * Trang đặt hàng thành công.
     */
    public function success(Order $order)
    {
        abort_unless(
            (int) $order->user_id === (int) Auth::id(),
            403
        );

        $order->load([
            'items',
            'payments',
        ]);

        return view(
            'checkout.success',
            compact('order')
        );
    }
}