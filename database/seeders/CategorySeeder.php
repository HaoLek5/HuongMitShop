<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name' => 'Áo',
            'slug' => 'ao',
            'description' => 'Các loại áo',
            'status' => true,
        ]);

        Category::create([
            'name' => 'Quần',
            'slug' => 'quan',
            'description' => 'Các loại quần',
            'status' => true,
        ]);

        Category::create([
            'name' => 'Đầm',
            'slug' => 'dam',
            'description' => 'Các loại đầm',
            'status' => true,
        ]);

        Category::create([
            'name' => 'Phụ kiện',
            'slug' => 'phu-kien',
            'description' => 'Phụ kiện thời trang',
            'status' => true,
        ]);
    }
}