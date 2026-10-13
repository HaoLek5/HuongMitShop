<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        $customer = User::where('phone', '0900000003')->firstOrFail();

        Address::create([
            'user_id' => $customer->id,
            'receiver_name' => 'Khách hàng',
            'receiver_phone' => '0900000003',
            'province' => 'Hà Nội',
            'district' => 'Cầu Giấy',
            'ward' => 'Dịch Vọng',
            'detail_address' => 'Số 10 đường Dịch Vọng',
            'is_default' => true,
        ]);

        Address::create([
            'user_id' => $customer->id,
            'receiver_name' => 'Nguyễn Văn B',
            'receiver_phone' => '0911111111',
            'province' => 'Hà Nội',
            'district' => 'Nam Từ Liêm',
            'ward' => 'Mỹ Đình 1',
            'detail_address' => 'Số 20 đường Trần Bình',
            'is_default' => false,
        ]);
    }
}