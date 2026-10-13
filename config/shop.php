<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deposit
    |--------------------------------------------------------------------------
    |
    | Các giá trị này là cấu hình của shop.
    | Có thể thay đổi sau mà không ảnh hưởng dữ liệu đơn cũ.
    |
    */

    'deposit_threshold' => env(
        'SHOP_DEPOSIT_THRESHOLD',
        1000000
    ),

    'deposit_percent' => env(
        'SHOP_DEPOSIT_PERCENT',
        30
    ),

];