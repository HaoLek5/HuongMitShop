<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Danh sách danh mục.
     */
    public function index()
    {
        $categories = Category::query()
            ->latest()
            ->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Form thêm danh mục.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Lưu danh mục mới.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'boolean',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'slug.max' => 'Slug không được vượt quá 150 ký tự.',
        ]);

        // Nếu không nhập slug thì tự tạo từ tên.
        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        // Kiểm tra slug sau khi chuẩn hóa.
        $request->merge([
            'normalized_slug' => $slug,
        ]);

        validator(
            ['slug' => $slug],
            [
                'slug' => [
                    'required',
                    'unique:categories,slug',
                ],
            ],
            [
                'slug.unique' => 'Slug này đã tồn tại.',
            ]
        )->validate();

        Category::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Thêm danh mục thành công.');
    }

    /**
     * Form sửa danh mục.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Cập nhật danh mục.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'status' => [
                'boolean',
            ],
        ], [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 100 ký tự.',
            'slug.max' => 'Slug không được vượt quá 150 ký tự.',
        ]);

        $slug = !empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        validator(
            ['slug' => $slug],
            [
                'slug' => [
                    'required',
                    Rule::unique('categories', 'slug')
                        ->ignore($category->id),
                ],
            ],
            [
                'slug.unique' => 'Slug này đã tồn tại.',
            ]
        )->validate();

        $category->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'description' => $validated['description'] ?? null,
            'status' => $request->boolean('status'),
        ]);

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Cập nhật danh mục thành công.');
    }

    /**
     * Bật / tắt danh mục.
     */
    public function toggleStatus(Category $category)
    {
        $category->update([
            'status' => !$category->status,
        ]);

        return back()->with(
            'success',
            $category->status
                ? 'Đã kích hoạt danh mục.'
                : 'Đã tắt danh mục.'
        );
    }

    /**
     * Xóa danh mục.
     */
    public function destroy(Category $category)
    {
        // Không cho xóa nếu đã có sản phẩm.
        if ($category->products()->exists()) {
            return back()->with(
                'error',
                'Không thể xóa danh mục vì đang có sản phẩm thuộc danh mục này.'
            );
        }

        $category->delete();

        return redirect()
            ->route('admin.categories.index')
            ->with('success', 'Xóa danh mục thành công.');
    }
}