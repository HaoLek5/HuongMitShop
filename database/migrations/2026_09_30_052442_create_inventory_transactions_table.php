<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            $table->enum('type', [
                'import',
                'sale',
                'return',
                'adjustment',
            ]);

            // Có dấu: nhập/trả hàng là số dương,
            // bán có thể là số âm, điều chỉnh +/-.
            $table->integer('quantity');

            $table->enum('reference_type', [
                'purchase_receipt',
                'order',
                'manual',
            ])->nullable();

            $table->unsignedBigInteger('reference_id')->nullable();

            $table->text('note')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_transactions');
    }
};