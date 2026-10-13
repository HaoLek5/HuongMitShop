<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute as ProductAttribute;
use App\Models\AttributeValue;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm.
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

                'variants.attributeValues.attribute',
            ])
            ->withCount('variants');

        /*
        |--------------------------------------------------------------------------
        | Tìm kiếm theo tên
        |--------------------------------------------------------------------------
        */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(
                'name',
                'like',
                '%' . $search . '%'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lọc theo danh mục
        |--------------------------------------------------------------------------
        */
        if ($request->filled('category_id')) {
            $query->where(
                'category_id',
                $request->category_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Lọc theo trạng thái
        |--------------------------------------------------------------------------
        */
        if (
            $request->filled('status')
            && in_array(
                $request->status,
                ['0', '1'],
                true
            )
        ) {
            $query->where(
                'status',
                (int) $request->status
            );
        }

        $products = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $categories = Category::query()
            ->orderBy('name')
            ->get();

        return view(
            'admin.products.index',
            compact(
                'products',
                'categories'
            )
        );
    }


    /**
     * Form thêm sản phẩm.
     */
    public function create()
    {
        $categories = Category::query()
            ->where('status', true)
            ->orderBy('name')
            ->get();

        $attributes = ProductAttribute::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Mã hàng hiển thị
        |--------------------------------------------------------------------------
        |
        | products hiện không có cột SKU riêng.
        | Vì vậy mã hàng cha dùng dạng SP-0001, SP-0002...
        |
        */
        $nextId = ((int) Product::max('id')) + 1;

        $productCode = 'SP-' . str_pad(
            $nextId,
            4,
            '0',
            STR_PAD_LEFT
        );

        return view(
            'admin.products.create',
            compact(
                'categories',
                'attributes',
                'productCode'
            )
        );
    }


    /**
     * Lưu sản phẩm mới.
     */
    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Validate request
        |--------------------------------------------------------------------------
        */
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            /*
             * Chỉ dùng khi sản phẩm không có thuộc tính.
             */
            'stock_quantity' => [
                'nullable',
                'integer',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            /*
             * Thuộc tính động.
             */
            'attributes' => [
                'nullable',
                'array',
                'max:20',
            ],

            'attributes.*.attribute_id' => [
                'nullable',
                'integer',
                'exists:attributes,id',
            ],

            'attributes.*.values' => [
                'nullable',
                'array',
                'max:50',
            ],

            'attributes.*.values.*' => [
                'nullable',
                'string',
                'max:100',
            ],

            /*
             * Variant được sinh từ giao diện.
             */
            'variants' => [
                'nullable',
                'array',
                'max:500',
            ],

            'variants.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'variants.*.stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'variants.*.status' => [
                'nullable',
                'boolean',
            ],

            /*
             * Hình ảnh.
             */
            'images' => [
                'nullable',
                'array',
                'max:10',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            /*
             * Nút Lưu / Lưu & tạo thêm.
             */
            'save_action' => [
                'nullable',
                'in:save,save_and_new',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. Chuẩn hóa thuộc tính
        |--------------------------------------------------------------------------
        */
        $attributeGroups = [];

        $rawAttributes = $request->input(
            'attributes',
            []
        );

        foreach ($rawAttributes as $attribute) {

            $attributeId = (int) (
                $attribute['attribute_id'] ?? 0
            );

            $values = collect(
                $attribute['values'] ?? []
            )
                ->map(function ($value) {
                    return trim((string) $value);
                })
                ->filter(function ($value) {
                    return $value !== '';
                })
                ->unique(function ($value) {
                    return mb_strtolower($value);
                })
                ->values()
                ->all();

            /*
            |--------------------------------------------------------------------------
            | Bỏ group hoàn toàn rỗng
            |--------------------------------------------------------------------------
            */
            if (
                $attributeId <= 0
                && empty($values)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Kiểm tra attribute
            |--------------------------------------------------------------------------
            */
            if ($attributeId <= 0) {

                throw ValidationException::withMessages([
                    'attributes' =>
                        'Vui lòng chọn tên thuộc tính.',
                ]);
            }

            $attributeModel =
                ProductAttribute::query()
                    ->find($attributeId);

            if (!$attributeModel) {

                throw ValidationException::withMessages([
                    'attributes' =>
                        'Tên thuộc tính không tồn tại.',
                ]);
            }

            $name =
                trim((string) $attributeModel->name);

            if ($name === '') {

                throw ValidationException::withMessages([
                    'attributes' =>
                        'Tên thuộc tính không hợp lệ.',
                ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Thiếu giá trị
            |--------------------------------------------------------------------------
            */
            if (empty($values)) {

                throw ValidationException::withMessages([
                    'attributes' =>
                        'Thuộc tính "' .
                        $name .
                        '" phải có ít nhất một giá trị.',
                ]);
            }

            $nameKey =
                mb_strtolower($name);

            /*
            |--------------------------------------------------------------------------
            | Không cho trùng tên thuộc tính
            |--------------------------------------------------------------------------
            */
            $duplicateName =
                collect($attributeGroups)
                    ->contains(function ($group) use ($nameKey) {
                        return $group['name_key'] === $nameKey;
                    });

            if ($duplicateName) {

                throw ValidationException::withMessages([
                    'attributes' =>
                        'Không thể có hai thuộc tính cùng tên "' .
                        $name .
                        '".',
                ]);
            }

            $attributeGroups[] = [
                'attribute_id' =>
                    $attributeModel->id,

                'name' =>
                    $name,

                'name_key' =>
                    $nameKey,

                'values' =>
                    $values,
            ];
        }

        $hasAttributes =
            !empty($attributeGroups);


        /*
        |--------------------------------------------------------------------------
        | 3. Lấy variant từ request
        |--------------------------------------------------------------------------
        */
        $rawVariants = array_values(
            $request->input(
                'variants',
                []
            )
        );


        /*
        |--------------------------------------------------------------------------
        | 4. Nếu có thuộc tính thì kiểm tra số tổ hợp
        |--------------------------------------------------------------------------
        */
        if ($hasAttributes) {

            $possibleCount = 1;

            foreach ($attributeGroups as $group) {

                $possibleCount *= count(
                    $group['values']
                );
            }


            /*
             * Không cho quá nhiều variant.
             */
            if ($possibleCount > 500) {

                throw ValidationException::withMessages([
                    'attributes' =>
                        'Số lượng tổ hợp biến thể quá lớn. ' .
                        'Vui lòng giảm số lượng giá trị thuộc tính.',
                ]);
            }


            /*
             * Frontend phải gửi đúng số variant.
             */
            if (
                count($rawVariants)
                !== $possibleCount
            ) {

                throw ValidationException::withMessages([
                    'variants' =>
                        'Danh sách biến thể không khớp với các tổ hợp thuộc tính.',
                ]);
            }

        } else {

            /*
             * Không có thuộc tính:
             * hệ thống sẽ tự tạo 1 variant mặc định.
             */
            $rawVariants = [
                [
                    'price' =>
                        $validated['base_price'],
                    'stock_quantity' =>
                        $validated['stock_quantity'] ?? 0,
                    'status' =>
                        $validated['status'],
                ],
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 5. Upload paths
        |--------------------------------------------------------------------------
        |
        | Nếu DB thất bại sau khi upload ảnh,
        | phải xóa các file đã upload.
        |
        */
        $uploadedPaths = [];


        try {

            $product = DB::transaction(
                function () use (
                    $validated,
                    $attributeGroups,
                    $rawVariants,
                    $hasAttributes,
                    $request,
                    &$uploadedPaths
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | 5.1 Tạo Product
                    |--------------------------------------------------------------------------
                    */

                    $product = Product::create([
                        'category_id' =>
                            $validated['category_id'],

                        'name' =>
                            trim($validated['name']),

                        'slug' =>
                            $this->generateUniqueProductSlug(
                                $validated['name']
                            ),

                        'description' =>
                            $validated['description']
                            ?? null,

                        'base_price' =>
                            $validated['base_price'],

                        'status' =>
                            (bool) $validated['status'],
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | 5.2 Lưu hình ảnh
                    |--------------------------------------------------------------------------
                    */

                    $images =
                        $request->file(
                            'images',
                            []
                        );


                    foreach (
                        $images as $sortOrder => $image
                    ) {

                        $path = $image->store(
                            'products',
                            'public'
                        );

                        $uploadedPaths[] =
                            $path;


                        $product->images()->create([
                            'image_url' =>
                                asset('storage/' . $path),

                            'is_primary' =>
                                $sortOrder === 0,

                            'sort_order' =>
                                $sortOrder,
                        ]);
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 5.3 Tạo Attribute + AttributeValue
                    |--------------------------------------------------------------------------
                    */

                    $resolvedGroups = [];


                    foreach (
                        $attributeGroups as $group
                    ) {

                        /*
                        |--------------------------------------------------------------------------
                        | Lấy Attribute đã chọn từ database
                        |--------------------------------------------------------------------------
                        */
                        $attributeModel =
                            ProductAttribute::query()
                                ->find(
                                    $group['attribute_id']
                                );

                        if (!$attributeModel) {

                            throw ValidationException::withMessages([
                                'attributes' =>
                                    'Tên thuộc tính không tồn tại.',
                            ]);
                        }


                        $valueModels = [];


                        foreach (
                            $group['values']
                            as $sortOrder => $value
                        ) {

                            $valueKey =
                                mb_strtolower($value);


                            /*
                            |--------------------------------------------------------------------------
                            | Tìm AttributeValue hiện có
                            |--------------------------------------------------------------------------
                            */

                            $valueModel =
                                AttributeValue::query()
                                    ->where(
                                        'attribute_id',
                                        $attributeModel->id
                                    )
                                    ->whereRaw(
                                        'LOWER(value) = ?',
                                        [$valueKey]
                                    )
                                    ->first();


                            /*
                            |--------------------------------------------------------------------------
                            | Chưa có -> tạo
                            |--------------------------------------------------------------------------
                            */

                            if (!$valueModel) {

                                $valueModel =
                                    AttributeValue::create([
                                        'attribute_id' =>
                                            $attributeModel->id,

                                        'value' =>
                                            $value,

                                        'sort_order' =>
                                            $sortOrder,

                                        'status' =>
                                            true,
                                    ]);

                            } else {

                                /*
                                |--------------------------------------------------------------------------
                                | Nếu value trước đó bị tắt,
                                | bật lại khi được sử dụng.
                                |--------------------------------------------------------------------------
                                */

                                $valueModel->update([
                                    'status' => true,
                                ]);
                            }


                            $valueModels[] =
                                $valueModel;
                        }


                        $resolvedGroups[] = [
                            'attribute' =>
                                $attributeModel,

                            'values' =>
                                $valueModels,
                        ];
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 5.4 Sinh tất cả tổ hợp variant
                    |--------------------------------------------------------------------------
                    */

                    if ($hasAttributes) {

                        $lists = array_map(
                            function ($group) {
                                return $group['values'];
                            },
                            $resolvedGroups
                        );


                        $combinations =
                            $this->cartesianProduct(
                                $lists
                            );

                    } else {

                        $combinations = [
                            [],
                        ];
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | 5.5 Tạo ProductVariant
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $combinations
                        as $index => $combination
                    ) {

                        $row =
                            $rawVariants[$index]
                            ?? [];


                        /*
                        |--------------------------------------------------------------------------
                        | Giá
                        |--------------------------------------------------------------------------
                        */

                        $variantPrice =
                            isset($row['price'])
                            && $row['price'] !== ''
                                ? (float) $row['price']
                                : (float) $validated['base_price'];


                        /*
                        |--------------------------------------------------------------------------
                        | Tồn kho
                        |--------------------------------------------------------------------------
                        */

                        $variantStock =
                            isset($row['stock_quantity'])
                            && $row['stock_quantity'] !== ''
                                ? (int) $row['stock_quantity']
                                : 0;


                        /*
                        |--------------------------------------------------------------------------
                        | Status
                        |--------------------------------------------------------------------------
                        */

                        $variantStatus =
                            array_key_exists(
                                'status',
                                $row
                            )
                                ? (bool) $row['status']
                                : (bool) $validated['status'];


                        /*
                        |--------------------------------------------------------------------------
                        | SKU
                        |--------------------------------------------------------------------------
                        |
                        | SKU không nhập từ giao diện.
                        | Backend tự sinh sau khi Product đã được tạo,
                        | dựa trên ID sản phẩm + thứ tự biến thể.
                        |
                        | Ví dụ:
                        | SP-0003-01, SP-0003-02, SP-0003-03...
                        |
                        */
                        $sku =
                            'SP-' .
                            str_pad(
                                $product->id,
                                4,
                                '0',
                                STR_PAD_LEFT
                            ) .
                            '-' .
                            str_pad(
                                $index + 1,
                                2,
                                '0',
                                STR_PAD_LEFT
                            );


                        /*
                        |--------------------------------------------------------------------------
                        | Tạo variant
                        |--------------------------------------------------------------------------
                        */

                        $variant =
                            $product
                                ->variants()
                                ->create([
                                    'sku' =>
                                        $sku,

                                    'price' =>
                                        $variantPrice,

                                    'cost_price' =>
                                        null,

                                    'stock_quantity' =>
                                        $variantStock,

                                    'status' =>
                                        $variantStatus,
                                ]);


                        /*
                        |--------------------------------------------------------------------------
                        | Gắn AttributeValue
                        |--------------------------------------------------------------------------
                        */

                        if (
                            !empty(
                                $combination
                            )
                        ) {

                            $variant
                                ->attributeValues()
                                ->attach(
                                    collect(
                                        $combination
                                    )
                                        ->pluck('id')
                                        ->all()
                                );
                        }
                    }


                    return $product;
                }
            );


            /*
            |--------------------------------------------------------------------------
            | 6. Sau khi lưu thành công
            |--------------------------------------------------------------------------
            */

            $message =
                'Đã thêm sản phẩm "' .
                $product->name .
                '" thành công.';


            /*
            |--------------------------------------------------------------------------
            | Lưu & tạo thêm
            |--------------------------------------------------------------------------
            */

            if (
                ($validated['save_action'] ?? 'save')
                === 'save_and_new'
            ) {

                return redirect()
                    ->route(
                        'admin.products.create'
                    )
                    ->with(
                        'success',
                        $message
                    );
            }


            /*
            |--------------------------------------------------------------------------
            | Lưu
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route(
                    'admin.products.index'
                )
                ->with(
                    'success',
                    $message
                );

        } catch (
            ValidationException $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | Xóa ảnh nếu validation nghiệp vụ
            | xảy ra sau khi upload.
            |--------------------------------------------------------------------------
            */

            foreach (
                $uploadedPaths as $path
            ) {

                Storage::disk(
                    'public'
                )->delete($path);
            }


            return back()
                ->withErrors(
                    $e->errors()
                )
                ->withInput();

        } catch (
            \Throwable $e
        ) {

            /*
            |--------------------------------------------------------------------------
            | Xóa ảnh nếu xảy ra lỗi hệ thống.
            |--------------------------------------------------------------------------
            */

            foreach (
                $uploadedPaths as $path
            ) {

                Storage::disk(
                    'public'
                )->delete($path);
            }


            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Không thể thêm sản phẩm. Vui lòng thử lại.'
                );
        }
    }


    /**
     * Tạo danh mục ngay trong form thêm sản phẩm.
     */
    public function storeInlineCategory(
        Request $request
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $name = trim(
            $validated['name']
        );

        $exists =
            Category::query()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [mb_strtolower($name)]
                )
                ->exists();

        if ($exists) {

            return response()->json([
                'message' =>
                    'Danh mục này đã tồn tại.',
            ], 422);
        }

        $category =
            Category::create([
                'name' =>
                    $name,

                'slug' =>
                    $this->generateUniqueCategorySlug(
                        $name
                    ),

                'description' =>
                    null,

                'status' =>
                    true,
            ]);

        return response()->json([
            'message' =>
                'Đã thêm danh mục thành công.',

            'category' => [
                'id' =>
                    $category->id,

                'name' =>
                    $category->name,
            ],
        ], 201);
    }


    /**
     * Tạo tên thuộc tính ngay trong form thêm sản phẩm.
     */
    public function storeInlineAttribute(
        Request $request
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $name = trim(
            $validated['name']
        );

        $exists =
            ProductAttribute::query()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [mb_strtolower($name)]
                )
                ->exists();

        if ($exists) {

            return response()->json([
                'message' =>
                    'Tên thuộc tính này đã tồn tại.',
            ], 422);
        }

        $attribute =
            ProductAttribute::create([
                'name' =>
                    $name,

                'code' =>
                    $this->generateUniqueAttributeCode(
                        $name
                    ),
            ]);

        return response()->json([
            'message' =>
                'Đã thêm thuộc tính thành công.',

            'attribute' => [
                'id' =>
                    $attribute->id,

                'name' =>
                    $attribute->name,
            ],
        ], 201);
    }


    /**
     * Form sửa sản phẩm.
     *
     * Trang sửa chỉ cho phép thay đổi thông tin sản phẩm,
     * hình ảnh và dữ liệu vận hành của các variant:
     * giá bán, tồn kho, trạng thái.
     *
     * Không cho chỉnh sửa cấu trúc thuộc tính/variant tại đây.
     */
    public function edit(Product $product)
    {
        $categories = Category::query()
            ->orderBy('name')
            ->get();

        $product->load([
            'images' => function ($query) {
                $query
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id');
            },
            'variants.attributeValues.attribute',
        ]);

        $productCode = 'SP-' . str_pad(
            $product->id,
            4,
            '0',
            STR_PAD_LEFT
        );

        return view(
            'admin.products.edit',
            compact(
                'product',
                'categories',
                'productCode'
            )
        );
    }


    /**
     * Cập nhật sản phẩm.
     *
     * Không nhận attributes từ request và không tạo/xóa
     * hoặc thay đổi quan hệ attribute_values của variant.
     * Chỉ cập nhật:
     * - Thông tin product.
     * - Hình ảnh.
     * - Giá / tồn kho / trạng thái của variant hiện có.
     */
    public function update(
        Request $request,
        Product $product
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'category_id' => [
                'required',
                'integer',
                'exists:categories,id',
            ],

            'base_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'description' => [
                'nullable',
                'string',
                'max:10000',
            ],

            'status' => [
                'required',
                'boolean',
            ],

            /*
             * Chỉ nhận state của các variant hiện tại.
             * Không nhận attribute_id / attribute_values.
             */
            'variants' => [
                'required',
                'array',
                'max:500',
            ],

            'variants.*.id' => [
                'required',
                'integer',
                'distinct',
                'exists:product_variants,id',
            ],

            'variants.*.price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'variants.*.stock_quantity' => [
                'required',
                'integer',
                'min:0',
            ],

            'variants.*.status' => [
                'required',
                'boolean',
            ],

            /*
             * Hình ảnh mới.
             */
            'images' => [
                'nullable',
                'array',
                'max:10',
            ],

            'images.*' => [
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],

            /*
             * Ảnh cũ cần xóa.
             */
            'delete_image_ids' => [
                'nullable',
                'array',
                'max:10',
            ],

            'delete_image_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:product_images,id',
            ],
        ], [
            'category_id.required' =>
                'Vui lòng chọn danh mục.',

            'category_id.exists' =>
                'Danh mục không tồn tại.',

            'name.required' =>
                'Vui lòng nhập tên sản phẩm.',

            'name.max' =>
                'Tên sản phẩm không được vượt quá 200 ký tự.',

            'base_price.required' =>
                'Vui lòng nhập giá cơ bản.',

            'base_price.numeric' =>
                'Giá cơ bản phải là số.',

            'base_price.min' =>
                'Giá cơ bản không được âm.',

            'variants.required' =>
                'Không tìm thấy danh sách biến thể hiện tại.',

            'variants.*.id.exists' =>
                'Một biến thể không tồn tại.',

            'variants.*.price.required' =>
                'Giá bán biến thể là bắt buộc.',

            'variants.*.stock_quantity.required' =>
                'Tồn kho biến thể là bắt buộc.',

            'variants.*.status.required' =>
                'Trạng thái biến thể không hợp lệ.',
        ]);

        /*
         * Không cho request tự ý thêm/xóa variant.
         * Phải đúng toàn bộ danh sách variant hiện tại của product.
         */
        $currentVariantIds = $product->variants()
            ->pluck('id')
            ->sort()
            ->values()
            ->all();

        $submittedVariantIds = collect(
            $validated['variants'] ?? []
        )
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->sort()
            ->values()
            ->all();

        if ($currentVariantIds !== $submittedVariantIds) {
            throw ValidationException::withMessages([
                'variants' =>
                    'Không được thêm, xóa hoặc thay đổi cấu trúc biến thể tại màn hình Sửa sản phẩm.',
            ]);
        }

        /*
         * Kiểm tra ảnh xóa có thực sự thuộc product hay không.
         */
        $deleteImageIds = collect(
            $request->input(
                'delete_image_ids',
                []
            )
        )
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $existingImages = $product->images()->get();

        $productImageIds = $existingImages
            ->pluck('id');

        $invalidDeleteImageIds = $deleteImageIds
            ->diff($productImageIds);

        if ($invalidDeleteImageIds->isNotEmpty()) {
            throw ValidationException::withMessages([
                'delete_image_ids' =>
                    'Có ảnh không thuộc sản phẩm này.',
            ]);
        }

        $validDeleteImageIds = $deleteImageIds
            ->intersect($productImageIds)
            ->values();

        $newImages = $request->file(
            'images',
            []
        );

        $newImageCount = is_array($newImages)
            ? count($newImages)
            : 0;

        $remainingImageCount =
            $existingImages->count()
            - $validDeleteImageIds->count();

        if (($remainingImageCount + $newImageCount) > 10) {
            throw ValidationException::withMessages([
                'images' =>
                    'Sản phẩm chỉ được có tối đa 10 hình ảnh.',
            ]);
        }

        $uploadedPaths = [];
        $deletedImagePaths = [];

        try {

            DB::transaction(function () use (
                $validated,
                $request,
                $product,
                $validDeleteImageIds,
                $newImages,
                &$uploadedPaths,
                &$deletedImagePaths
            ) {
                /*
                 * ---------------------------------------------------------
                 * 1. Cập nhật thông tin product
                 * ---------------------------------------------------------
                 */
                $slug = $this->generateUniqueProductSlug(
                    $validated['name'],
                    $product->id
                );

                $product->update([
                    'category_id' =>
                        $validated['category_id'],

                    'name' =>
                        trim($validated['name']),

                    'slug' =>
                        $slug,

                    'description' =>
                        $validated['description'] ?? null,

                    'base_price' =>
                        $validated['base_price'],

                    'status' =>
                        (bool) $validated['status'],
                ]);


                /*
                 * ---------------------------------------------------------
                 * 2. Xóa ảnh cũ
                 * ---------------------------------------------------------
                 */
                if ($validDeleteImageIds->isNotEmpty()) {

                    $imagesToDelete = $product->images()
                        ->whereIn(
                            'id',
                            $validDeleteImageIds->all()
                        )
                        ->get();

                    foreach ($imagesToDelete as $image) {

                        $url = $image->image_url;

                        if (
                            is_string($url)
                            && str_contains($url, '/storage/')
                        ) {
                            $position = strpos(
                                $url,
                                '/storage/'
                            );

                            $deletedImagePaths[] = substr(
                                $url,
                                $position + strlen('/storage/')
                            );
                        }
                    }

                    $product->images()
                        ->whereIn(
                            'id',
                            $validDeleteImageIds->all()
                        )
                        ->delete();
                }


                /*
                 * ---------------------------------------------------------
                 * 3. Thêm ảnh mới
                 * ---------------------------------------------------------
                 */
                foreach ($newImages as $image) {

                    $path = $image->store(
                        'products',
                        'public'
                    );

                    $uploadedPaths[] = $path;

                    $product->images()->create([
                        'image_url' =>
                            asset('storage/' . $path),

                        'is_primary' => false,

                        'sort_order' =>
                            9999,
                    ]);
                }


                /*
                 * ---------------------------------------------------------
                 * 4. Chuẩn hóa ảnh đại diện + thứ tự
                 * ---------------------------------------------------------
                 */
                $remainingImages = $product->images()
                    ->orderByDesc('is_primary')
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->get();

                foreach (
                    $remainingImages as $sortOrder => $image
                ) {
                    $image->update([
                        'is_primary' =>
                            $sortOrder === 0,

                        'sort_order' =>
                            $sortOrder,
                    ]);
                }


                /*
                 * ---------------------------------------------------------
                 * 5. Cập nhật các variant hiện tại
                 * ---------------------------------------------------------
                 *
                 * Chỉ cập nhật giá / tồn kho / trạng thái.
                 * Không đụng tới SKU và attribute_values.
                 */
                $submittedVariants = collect(
                    $validated['variants']
                )
                    ->keyBy(function ($row) {
                        return (int) $row['id'];
                    });

                $existingVariants = $product->variants()
                    ->get();

                foreach ($existingVariants as $variant) {

                    $row = $submittedVariants->get(
                        (int) $variant->id
                    );

                    if (!$row) {
                        throw ValidationException::withMessages([
                            'variants' =>
                                'Danh sách biến thể không hợp lệ.',
                        ]);
                    }

                    $variant->update([
                        'price' =>
                            $row['price'],

                        'stock_quantity' =>
                            $row['stock_quantity'],

                        'status' =>
                            (bool) $row['status'],
                    ]);
                }
            });

            /*
             * Chỉ xóa file vật lý sau khi transaction thành công.
             */
            foreach ($deletedImagePaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return redirect()
                ->route('admin.products.index')
                ->with(
                    'success',
                    'Cập nhật sản phẩm "' .
                    $product->name .
                    '" thành công.'
                );

        } catch (ValidationException $e) {

            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            return back()
                ->withErrors($e->errors())
                ->withInput();

        } catch (\Throwable $e) {

            foreach ($uploadedPaths as $path) {
                Storage::disk('public')->delete($path);
            }

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Không thể cập nhật sản phẩm. Vui lòng thử lại.'
                );
        }
    }


    /**
     * Xóa sản phẩm.
     */
    public function destroy(
        Product $product
    ) {
        /*
        |--------------------------------------------------------------------------
        | Không xóa product nếu đã có variant.
        |--------------------------------------------------------------------------
        */

        if (
            $product
                ->variants()
                ->exists()
        ) {

            return back()->with(
                'error',
                'Không thể xóa sản phẩm vì sản phẩm đã có biến thể.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Xóa ảnh
        |--------------------------------------------------------------------------
        */

        $product
            ->images()
            ->get()
            ->each(function ($image) {

                /*
                 * Chỉ cố xóa nếu đây là đường dẫn
                 * thuộc storage của ứng dụng.
                 */
                $url = $image->image_url;

                if (
                    is_string($url)
                    && str_starts_with(
                        $url,
                        '/storage/'
                    )
                ) {

                    $path =
                        substr(
                            $url,
                            strlen('/storage/')
                        );

                    Storage::disk(
                        'public'
                    )->delete($path);
                }
            });


        $product->delete();


        return redirect()
            ->route(
                'admin.products.index'
            )
            ->with(
                'success',
                'Xóa sản phẩm thành công.'
            );
    }


    /**
     * Bật / tắt trạng thái sản phẩm.
     */
    public function toggleStatus(
        Product $product
    ) {
        $product->update([
            'status' =>
                !$product->status,
        ]);


        return back()->with(
            'success',
            $product->status
                ? 'Đã kích hoạt sản phẩm.'
                : 'Đã tắt sản phẩm.'
        );
    }


    /**
     * Sinh slug sản phẩm duy nhất.
     */
    private function generateUniqueProductSlug(
        string $name
    ): string {
        $baseSlug =
            Str::slug($name);


        if ($baseSlug === '') {
            $baseSlug = 'product';
        }


        $slug =
            $baseSlug;

        $counter = 1;


        while (
            Product::query()
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {

            $slug =
                $baseSlug .
                '-' .
                $counter;

            $counter++;
        }


        return $slug;
    }


    /**
     * Sinh slug danh mục duy nhất.
     */
    private function generateUniqueCategorySlug(
        string $name
    ): string {
        $baseSlug =
            Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = 'category';
        }

        $slug =
            $baseSlug;

        $counter = 1;

        while (
            Category::query()
                ->where(
                    'slug',
                    $slug
                )
                ->exists()
        ) {

            $slug =
                $baseSlug .
                '-' .
                $counter;

            $counter++;
        }

        return $slug;
    }


    /**
     * Sinh code attribute duy nhất.
     */
    private function generateUniqueAttributeCode(
        string $name
    ): string {
        $baseCode =
            Str::slug($name);


        if ($baseCode === '') {
            $baseCode = 'attribute';
        }


        $code =
            $baseCode;

        $counter = 1;


        while (
            ProductAttribute::query()
                ->where(
                    'code',
                    $code
                )
                ->exists()
        ) {

            $code =
                $baseCode .
                '-' .
                $counter;

            $counter++;
        }


        return $code;
    }


    /**
     * Tạo Cartesian Product.
     *
     * Ví dụ:
     *
     * Màu:
     *     Đen, Trắng
     *
     * Size:
     *     S, M, L
     *
     * Kết quả:
     *
     *     Đen-S
     *     Đen-M
     *     Đen-L
     *     Trắng-S
     *     Trắng-M
     *     Trắng-L
     */
    private function cartesianProduct(
        array $lists
    ): array {
        if (empty($lists)) {
            return [
                []
            ];
        }


        $result = [
            []
        ];


        foreach ($lists as $list) {

            $newResult = [];


            foreach (
                $result as $prefix
            ) {

                foreach (
                    $list as $item
                ) {

                    $newResult[] = [
                        ...$prefix,
                        $item,
                    ];
                }
            }


            $result =
                $newResult;
        }


        return $result;
    }
}