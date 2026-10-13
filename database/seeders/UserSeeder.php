<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Quản trị viên',
            'phone' => '0900000001',
            'email' => null,
            'password' => '12345678',
            'pin_code' => '111111',
            'role' => 'admin',
            'status' => true,
        ]);

        User::create([
            'name' => 'Nhân viên cửa hàng',
            'phone' => '0900000002',
            'email' => null,
            'password' => '12345678',
            'pin_code' => '222222',
            'role' => 'staff',
            'status' => true,
        ]);

        User::create([
            'name' => 'Khách hàng',
            'phone' => '0900000003',
            'email' => null,
            'password' => '12345678',
            'pin_code' => null,
            'role' => 'customer',
            'status' => true,
        ]);
    }
}