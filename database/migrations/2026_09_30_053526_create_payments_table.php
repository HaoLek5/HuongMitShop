<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->enum('payment_type', [
                'deposit',
                'final',
            ]);

            $table->enum('payment_method', [
                'cod',
                'bank_transfer',
            ]);

            $table->decimal('amount', 15, 2);

            $table->enum('status', [
                'pending',
                'paid',
                'failed',
                'refunded',
            ])->default('pending');

            $table->string('transaction_code', 100)->nullable();

            $table->string('proof_image', 500)->nullable();

            $table->timestamp('paid_at')->nullable();

            // Admin / Staff xác nhận giao dịch
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            $table->timestamp('confirmed_at')->nullable();

            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};