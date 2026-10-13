<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Trang chủ / danh sách sản phẩm.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->with([
                'category',

                'images' => function ($query) {
                    $query
                        ->orderByDesc('is_primary')
                        ->orderBy('sort_order');
                },

                'variants' => function ($query) {
                    $query->where('status', true);
                },
            ])
            ->where('status', true);

        /*
        |--------------------------------------------------------------------------
        | Tìm kiếm theo tên sản phẩm
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->input('search'));

            $query->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Phân trang
        |--------------------------------------------------------------------------
        */
        $products = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view(
            'shop.index',
            compact('products')
        );
    }


    /**
     * Chi tiết sản phẩm.
     */
    public function show(Product $product)
    {
        abort_unless(
            $product->status,
            404
        );

        $product->load([
            'category',

            'images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order');
            },

            'variants' => function ($query) {
                $query
                    ->where('status', true)
                    ->with('attributeValues.attribute');
            },
        ]);

        return view(
            'shop.show',
            compact('product')
        );
    }
}