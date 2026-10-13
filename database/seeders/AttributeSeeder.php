<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $color = Attribute::create([
            'name' => 'Màu sắc',
            'code' => 'color',
        ]);

        $color->values()->createMany([
            [
                'value' => 'Đen',
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'value' => 'Trắng',
                'sort_order' => 2,
                'status' => true,
            ],
            [
                'value' => 'Đỏ',
                'sort_order' => 3,
                'status' => true,
            ],
            [
                'value' => 'Xanh',
                'sort_order' => 4,
                'status' => true,
            ],
        ]);

        $size = Attribute::create([
            'name' => 'Kích thước',
            'code' => 'size',
        ]);

        $size->values()->createMany([
            [
                'value' => 'S',
                'sort_order' => 1,
                'status' => true,
            ],
            [
                'value' => 'M',
                'sort_order' => 2,
                'status' => true,
            ],
            [
                'value' => 'L',
                'sort_order' => 3,
                'status' => true,
            ],
            [
                'value' => 'XL',
                'sort_order' => 4,
                'status' => true,
            ],
        ]);
    }
}