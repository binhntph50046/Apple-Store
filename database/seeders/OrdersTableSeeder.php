<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrdersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy user_id đầu tiên từ bảng users
        $userId = DB::table('users')->first()->id;

        DB::table('orders')->insert([
            // [
            //     'user_id' => $userId,
            //     'subtotal' => 81980000,
            //     'discount' => 0,
            //     'shipping_fee' => 30000,
            //     'total_price' => 82010000,
            //     'shipping_address' => 'Hồ Chí Minh, Việt Nam',
            //     'shipping_name' => 'User',
            //     'shipping_phone' => '0987654321',
            //     'shipping_email' => 'admin@gmail.com',
            //     'payment_method' => 'bank_transfer',
            //     'payment_status' => 'paid',
            //     'shipping_method_id' => 1,
            //     'status' => 'pending',
            //     'is_paid' => true,
            //     'notes' => 'Đơn hàng mẫu',
            //     'created_at' => now(),
            //     'updated_at' => now()
            // ],
            // Thêm đơn hàng mẫu thứ 2
            [
                'user_id' => $userId,
                'subtotal' => 10000000,
                'discount' => 0,
                'shipping_fee' => 50000,    
                'total_price' => 10005000,
                'shipping_address' => 'Hà Nội, Việt Nam',
                'shipping_name' => 'User',
                'shipping_phone' => '0987654321',
                'shipping_email' => 'admin@gmail.com',
                'payment_method' => 'bank_transfer',
                'payment_status' => 'paid',
                'shipping_method_id' => 1,
                'status' => 'pending',
                'is_paid' => true,
                'notes' => 'Đơn hàng mẫu',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
