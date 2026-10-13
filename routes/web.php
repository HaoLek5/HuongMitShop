<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ProductVariantController;
use App\Http\Controllers\Admin\PurchaseReceiptController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CustomerOrderController;
use App\Http\Controllers\Admin\UserController;

/*
|--------------------------------------------------------------------------
| Quản lý người dùng
|--------------------------------------------------------------------------
| Chỉ Admin mới được truy cập.
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get(
            '/users',
            [UserController::class, 'index']
        )->name('users.index');

        Route::post(
            '/users',
            [UserController::class, 'store']
        )->name('users.store');

        Route::patch(
            '/users/{user}/role',
            [UserController::class, 'updateRole']
        )->name('users.update-role');

        Route::patch(
            '/users/{user}/toggle-status',
            [UserController::class, 'toggleStatus']
        )->name('users.toggle-status');

        Route::delete(
            '/users/{user}',
            [UserController::class, 'destroy']
        )->name('users.destroy');

    });


/*
|--------------------------------------------------------------------------
| Trang chủ Customer / Public
|--------------------------------------------------------------------------
| Khách chưa đăng nhập vẫn được xem sản phẩm.
*/

Route::get('/', [
    ShopController::class,
    'index'
])->name('home');

Route::get('/products/{product:slug}', [
    ShopController::class,
    'show'
])->name('shop.show');


/*
|--------------------------------------------------------------------------
| Authentication - Guest
|--------------------------------------------------------------------------
| Chỉ người chưa đăng nhập mới được truy cập.
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Đăng nhập
    |--------------------------------------------------------------------------
    */

    Route::get('/login', [
        AuthController::class,
        'showLogin'
    ])->name('login');

    Route::post('/login', [
        AuthController::class,
        'login'
    ])->name('login.store');


    /*
    |--------------------------------------------------------------------------
    | Đăng ký
    |--------------------------------------------------------------------------
    */

    Route::get('/register', [
        AuthController::class,
        'showRegister'
    ])->name('register');

    Route::post('/register', [
        AuthController::class,
        'register'
    ])->name('register.store');

});


/*
|--------------------------------------------------------------------------
| User đã đăng nhập
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard cũ
    |--------------------------------------------------------------------------
    |
    | Customer      -> Trang chủ
    | Admin / Staff -> Admin Dashboard
    |
    */

    Route::get('/dashboard', function () {

        $user = Auth::user();

        if (
            $user->role === 'admin'
            || $user->role === 'staff'
        ) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('home');

    })->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | Customer
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:customer')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Tài khoản
        |--------------------------------------------------------------------------
        */

        Route::get('/account', function () {
            return view('dashboard');
        })->name('account');


        /*
    |--------------------------------------------------------------------------
    | Đơn hàng
    |--------------------------------------------------------------------------
    */

    Route::get('/orders', [
        CustomerOrderController::class,
        'index'
    ])->name('customer.orders.index');

    Route::get('/orders/{order}', [
        CustomerOrderController::class,
        'show'
    ])->name('customer.orders.show');


        /*
        |--------------------------------------------------------------------------
        | Giỏ hàng
        |--------------------------------------------------------------------------
        */

        Route::get('/cart', [
            CartController::class,
            'index'
        ])->name('cart.index');

        Route::post('/cart/items', [
            CartController::class,
            'store'
        ])->name('cart.items.store');

        Route::patch('/cart/items/{cartItem}', [
            CartController::class,
            'update'
        ])->name('cart.items.update');

        Route::delete('/cart/items/{cartItem}', [
            CartController::class,
            'destroy'
        ])->name('cart.items.destroy');

        Route::delete('/cart', [
            CartController::class,
            'clear'
        ])->name('cart.clear');


        /*
        |--------------------------------------------------------------------------
        | Checkout
        |--------------------------------------------------------------------------
        */

        Route::get('/checkout', [
            CheckoutController::class,
            'index'
        ])->name('checkout.index');

        Route::post('/checkout', [
            CheckoutController::class,
            'store'
        ])->name('checkout.store');

        Route::get('/checkout/success/{order}', [
            CheckoutController::class,
            'success'
        ])->name('checkout.success');

    });


    /*
    |--------------------------------------------------------------------------
    | Đăng xuất
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [
        AuthController::class,
        'logout'
    ])->name('logout');

});


/*
|--------------------------------------------------------------------------
| Admin / Staff
|--------------------------------------------------------------------------
| Chỉ Admin và Staff mới được truy cập.
*/

Route::middleware([
    'auth',
    'role:admin,staff'
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [
            DashboardController::class,
            'index'
        ])->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Products
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'products',
            ProductController::class
        )->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Product status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/products/{product}/toggle-status',
            [
                ProductController::class,
                'toggleStatus'
            ]
        )->name('products.toggle-status');


        /*
        |--------------------------------------------------------------------------
        | Product Variants
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'products.variants',
            ProductVariantController::class
        )->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | Variant status
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/products/{product}/variants/{variant}/toggle-status',
            [
                ProductVariantController::class,
                'toggleStatus'
            ]
        )->name('products.variants.toggle-status');


        /*
        |--------------------------------------------------------------------------
        | Purchase Receipts
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'purchase-receipts',
            PurchaseReceiptController::class
        )->only([
            'index',
            'create',
            'store',
            'show'
        ]);


        /*
        |--------------------------------------------------------------------------
        | Hoàn tất phiếu nhập
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/purchase-receipts/{purchaseReceipt}/complete',
            [
                PurchaseReceiptController::class,
                'complete'
            ]
        )->name('purchase-receipts.complete');


        /*
        |--------------------------------------------------------------------------
        | Hủy phiếu nhập
        |--------------------------------------------------------------------------
        */

        Route::patch(
            '/purchase-receipts/{purchaseReceipt}/cancel',
            [
                PurchaseReceiptController::class,
                'cancel'
            ]
        )->name('purchase-receipts.cancel');


        /*
        |--------------------------------------------------------------------------
        | Inventory
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/inventory',
            [
                InventoryController::class,
                'index'
            ]
        )->name('inventory.index');

        Route::get(
            '/inventory/{variant}',
            [
                InventoryController::class,
                'show'
            ]
        )->name('inventory.show');

        Route::patch(
            '/inventory/{variant}/adjust',
            [
                InventoryController::class,
                'adjust'
            ]
        )->name('inventory.adjust');


        /*
        |--------------------------------------------------------------------------
        | Orders - Admin / Staff
        |--------------------------------------------------------------------------
        */

        Route::get('/orders', [
            OrderController::class,
            'index'
        ])->name('orders.index');


        /*
        |--------------------------------------------------------------------------
        | Inline Category
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/products/inline-category',
            [
                ProductController::class,
                'storeInlineCategory'
            ]
        )->name('products.inline-category');


        /*
        |--------------------------------------------------------------------------
        | Inline Attribute
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/products/inline-attribute',
            [
                ProductController::class,
                'storeInlineAttribute'
            ]
        )->name('products.inline-attribute');


        /*
        |--------------------------------------------------------------------------
        | In đơn hàng
        |--------------------------------------------------------------------------
        |
        | Phải đặt /orders/print trước /orders/{order}
        |
        */

        Route::post('/orders/print', [
            OrderController::class,
            'print'
        ])->name('orders.print');

        Route::get('/orders/print', [
            OrderController::class,
            'printView'
        ])->name('orders.print-view');


        /*
        |--------------------------------------------------------------------------
        | Chi tiết đơn hàng
        |--------------------------------------------------------------------------
        */

        Route::get('/orders/{order}', [
            OrderController::class,
            'show'
        ])->name('orders.show');


        /*
        |--------------------------------------------------------------------------
        | Cập nhật trạng thái đơn
        |--------------------------------------------------------------------------
        */

        Route::patch('/orders/{order}/status', [
            OrderController::class,
            'updateStatus'
        ])->name('orders.update-status');

    });