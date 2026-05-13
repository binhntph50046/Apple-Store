<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy order_id đầu tiên từ bảng orders
        $orderId = DB::table('orders')->first()->id;

        DB::table('order_items')->insert([
            [
                'order_id' => $orderId,
                'product_id' => 4,
                'product_variant_id' => 2,
                'quantity' => 1,
                'price' => 32990000,
                'total' => 32990000,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
