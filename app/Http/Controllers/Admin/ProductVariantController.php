<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductVariantController extends Controller
{
    /**
     * Danh sách variant của một product.
     */
    public function index(Product $product)
    {
        $variants = $product->variants()
            ->with('attributeValues.attribute')
            ->latest()
            ->paginate(10);

        return view('admin.products.variants.index', compact(
            'product',
            'variants'
        ));
    }

    /**
     * Form thêm variant.
     */
    public function create(Product $product)
    {
        $attributes = Attribute::query()
            ->with([
                'values' => function ($query) {
                    $query
                        ->where('status', true)
                        ->orderBy('sort_order')
                        ->orderBy('value');
                }
            ])
            ->orderBy('name')
            ->get();

        return view(
            'admin.products.variants.create',
            compact('product', 'attributes')
        );
    }

    /**
     * Lưu variant mới.
     */
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:product_variants,sku',
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'attribute_value_ids' => [
                'nullable',
                'array',
            ],

            'attribute_value_ids.*' => [
                'integer',
                'exists:attribute_values,id',
            ],

            'status' => [
                'boolean',
            ],
        ]);

        $attributeValueIds = array_values(
            array_unique($validated['attribute_value_ids'] ?? [])
        );

        /*
         * Mỗi attribute chỉ được chọn tối đa một value
         * trong cùng một variant.
         *
         * Ví dụ:
         * color → Đỏ
         * size  → M
         *
         * Không hợp lệ:
         * color → Đỏ + Đen
         */
        if (!empty($attributeValueIds)) {
            $attributeCount = DB::table('attribute_values')
                ->whereIn('id', $attributeValueIds)
                ->distinct('attribute_id')
                ->count('attribute_id');

            if ($attributeCount !== count($attributeValueIds)) {
                throw ValidationException::withMessages([
                    'attribute_value_ids' =>
                        'Mỗi loại thuộc tính chỉ được chọn một giá trị cho một variant.',
                ]);
            }
        }

        /*
         * Không cho phép cùng một tổ hợp thuộc tính
         * xuất hiện 2 lần trong cùng product.
         */
        $existingVariants = $product->variants()
            ->with('attributeValues')
            ->get();

        foreach ($existingVariants as $existingVariant) {
            $existingIds = $existingVariant
                ->attributeValues
                ->pluck('id')
                ->sort()
                ->values()
                ->all();

            $newIds = collect($attributeValueIds)
                ->sort()
                ->values()
                ->all();

            if ($existingIds === $newIds) {
                throw ValidationException::withMessages([
                    'attribute_value_ids' =>
                        'Tổ hợp thuộc tính này đã tồn tại.',
                ]);
            }
        }

        DB::transaction(function () use (
            $product,
            $validated,
            $attributeValueIds,
            $request
        ) {
            $variant = $product->variants()->create([
                'sku' => $validated['sku'],
                'price' => $validated['price'],
                'cost_price' => $validated['cost_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'status' => $request->boolean('status'),
            ]);

            if (!empty($attributeValueIds)) {
                $variant->attributeValues()->attach(
                    $attributeValueIds
                );
            }
        });

        return redirect()
            ->route('admin.products.variants.index', $product)
            ->with('success', 'Thêm variant thành công.');
    }

    /**
     * Form sửa variant.
     */
    public function edit(Product $product, ProductVariant $variant)
    {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );

        $attributes = Attribute::query()
            ->with([
                'values' => function ($query) {
                    $query
                        ->where('status', true)
                        ->orderBy('sort_order')
                        ->orderBy('value');
                }
            ])
            ->orderBy('name')
            ->get();

        $variant->load('attributeValues');

        return view(
            'admin.products.variants.edit',
            compact('product', 'variant', 'attributes')
        );
    }

    /**
     * Cập nhật variant.
     */
    public function update(
        Request $request,
        Product $product,
        ProductVariant $variant
    ) {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );

        $validated = $request->validate([
            'sku' => [
                'required',
                'string',
                'max:100',
                'unique:product_variants,sku,' . $variant->id,
            ],

            'price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'cost_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'attribute_value_ids' => [
                'nullable',
                'array',
            ],

            'attribute_value_ids.*' => [
                'integer',
                'exists:attribute_values,id',
            ],

            'status' => [
                'boolean',
            ],
        ]);

        $attributeValueIds = array_values(
            array_unique($validated['attribute_value_ids'] ?? [])
        );

        if (!empty($attributeValueIds)) {
            $attributeCount = DB::table('attribute_values')
                ->whereIn('id', $attributeValueIds)
                ->distinct('attribute_id')
                ->count('attribute_id');

            if ($attributeCount !== count($attributeValueIds)) {
                throw ValidationException::withMessages([
                    'attribute_value_ids' =>
                        'Mỗi loại thuộc tính chỉ được chọn một giá trị cho một variant.',
                ]);
            }
        }

        /*
         * Kiểm tra trùng tổ hợp với các variant khác.
         */
        $otherVariants = $product->variants()
            ->where('id', '!=', $variant->id)
            ->with('attributeValues')
            ->get();

        foreach ($otherVariants as $existingVariant) {
            $existingIds = $existingVariant
                ->attributeValues
                ->pluck('id')
                ->sort()
                ->values()
                ->all();

            $newIds = collect($attributeValueIds)
                ->sort()
                ->values()
                ->all();

            if ($existingIds === $newIds) {
                throw ValidationException::withMessages([
                    'attribute_value_ids' =>
                        'Tổ hợp thuộc tính này đã tồn tại.',
                ]);
            }
        }

        DB::transaction(function () use (
            $variant,
            $validated,
            $attributeValueIds,
            $request
        ) {
            $variant->update([
                'sku' => $validated['sku'],
                'price' => $validated['price'],
                'cost_price' => $validated['cost_price'] ?? null,
                'stock_quantity' => $validated['stock_quantity'],
                'status' => $request->boolean('status'),
            ]);

            $variant->attributeValues()->sync(
                $attributeValueIds
            );
        });

        return redirect()
            ->route(
                'admin.products.variants.index',
                $product
            )
            ->with('success', 'Cập nhật variant thành công.');
    }

    /**
     * Xóa variant.
     */
    public function destroy(
        Product $product,
        ProductVariant $variant
    ) {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );

        /*
         * Không cho xóa variant đã phát sinh
         * giao dịch nghiệp vụ.
         */
        if (
            $variant->orderItems()->exists() ||
            $variant->purchaseReceiptItems()->exists() ||
            $variant->inventoryTransactions()->exists() ||
            $variant->cartItems()->exists()
        ) {
            return back()->with(
                'error',
                'Không thể xóa variant vì đã phát sinh dữ liệu nghiệp vụ.'
            );
        }

        $variant->delete();

        return back()->with(
            'success',
            'Xóa variant thành công.'
        );
    }

    /**
     * Bật / tắt variant.
     */
    public function toggleStatus(
        Product $product,
        ProductVariant $variant
    ) {
        abort_unless(
            $variant->product_id === $product->id,
            404
        );

        $variant->update([
            'status' => !$variant->status,
        ]);

        return back()->with(
            'success',
            $variant->status
                ? 'Đã kích hoạt variant.'
                : 'Đã tắt variant.'
        );
    }
}