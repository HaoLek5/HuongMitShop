<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Snapshot sản phẩm tại thời điểm mua
            $table->string('product_name', 200);
            $table->string('sku', 100);
            $table->string('variant_name', 255);

            $table->decimal('price', 15, 2);
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};