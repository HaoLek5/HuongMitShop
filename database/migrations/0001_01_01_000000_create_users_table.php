<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name', 100);

            // Số điện thoại dùng để đăng nhập
            $table->string('phone', 20)->unique();

            // Email để mở rộng sau này, hiện chưa dùng đăng nhập
            $table->string('email', 255)->nullable()->unique();

            // Mật khẩu đã hash
            $table->string('password', 255);

            // PIN code, cho phép NULL khi chưa thiết lập
            $table->string('pin_code', 255)->nullable();

            // Vai trò tài khoản
            $table->enum('role', [
                'admin',
                'staff',
                'customer',
            ])->default('customer');

            // 1 = hoạt động, 0 = khóa
            $table->boolean('status')->default(true);

            // Lần đăng nhập gần nhất
            $table->timestamp('last_login_at')->nullable();

            // Remember Me
            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};