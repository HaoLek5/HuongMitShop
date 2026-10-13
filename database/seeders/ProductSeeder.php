<?php

namespace Database\Seeders;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $aoCategory = Category::where('slug', 'ao')->firstOrFail();

        $color = Attribute::where('code', 'color')->firstOrFail();
        $size = Attribute::where('code', 'size')->firstOrFail();

        $red = $color->values()->where('value', 'Đỏ')->firstOrFail();
        $black = $color->values()->where('value', 'Đen')->firstOrFail();

        $sizeM = $size->values()->where('value', 'M')->firstOrFail();
        $sizeL = $size->values()->where('value', 'L')->firstOrFail();

        $product = Product::create([
            'category_id' => $aoCategory->id,
            'name' => 'Áo thun Basic',
            'slug' => 'ao-thun-basic',
            'description' => 'Áo thun Basic dành cho trẻ em.',
            'base_price' => 120000,
            'status' => true,
        ]);

        $product->images()->createMany([
            [
                'image_url' => 'products/ao-thun-basic-1.jpg',
                'is_primary' => true,
                'sort_order' => 1,
            ],
            [
                'image_url' => 'products/ao-thun-basic-2.jpg',
                'is_primary' => false,
                'sort_order' => 2,
            ],
        ]);

        $variant1 = $product->variants()->create([
            'sku' => 'ATB-RED-M',
            'price' => 120000,
            'cost_price' => 70000,
            'stock_quantity' => 20,
            'status' => true,
        ]);

        $variant1->attributeValues()->attach([
            $red->id,
            $sizeM->id,
        ]);

        $variant2 = $product->variants()->create([
            'sku' => 'ATB-RED-L',
            'price' => 125000,
            'cost_price' => 72000,
            'stock_quantity' => 15,
            'status' => true,
        ]);

        $variant2->attributeValues()->attach([
            $red->id,
            $sizeL->id,
        ]);

        $variant3 = $product->variants()->create([
            'sku' => 'ATB-BLK-M',
            'price' => 120000,
            'cost_price' => 70000,
            'stock_quantity' => 10,
            'status' => true,
        ]);

        $variant3->attributeValues()->attach([
            $black->id,
            $sizeM->id,
        ]);

        $variant4 = $product->variants()->create([
            'sku' => 'ATB-BLK-L',
            'price' => 125000,
            'cost_price' => 72000,
            'stock_quantity' => 8,
            'status' => true,
        ]);

        $variant4->attributeValues()->attach([
            $black->id,
            $sizeL->id,
        ]);
    }
}