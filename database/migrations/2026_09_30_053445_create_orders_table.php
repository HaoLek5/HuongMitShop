<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_code', 50)->unique();

            // Người đặt hàng
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Địa chỉ được chọn lúc checkout
            $table->foreignId('address_id')
                ->nullable()
                ->constrained('addresses')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // Snapshot người nhận
            $table->string('receiver_name', 100);
            $table->string('receiver_phone', 20);
            $table->text('shipping_address');

            // Tiền đơn hàng
            $table->decimal('subtotal', 15, 2);
            $table->decimal('discount_amount', 15, 2);
            $table->decimal('shipping_fee', 15, 2);
            $table->decimal('total_amount', 15, 2);

            // Phương thức thanh toán
            $table->enum('payment_method', [
                'cod',
                'bank_transfer',
            ]);

            // Trạng thái thanh toán
            $table->enum('payment_status', [
                'unpaid',
                'deposit_pending',
                'deposit_paid',
                'paid',
                'refunded',
            ])->default('unpaid');

            // Quy tắc cọc
            $table->boolean('deposit_required')->default(false);
            $table->decimal('deposit_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('remaining_amount', 15, 2)->default(0);

            // Trạng thái đơn hàng
            $table->enum('order_status', [
                'pending',
                'confirmed',
                'processing',
                'shipping',
                'delivered',
                'cancelled',
            ])->default('pending');

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};