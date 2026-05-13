<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlashSaleItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('flash_sale_items')->insert([
            [
                'flash_sale_id' => 1,
                'product_variant_id' => 91,
                'count' => 20,
                'discount' => 10, // 10%
                'discount_type' => 'percent',
                'buy_limit' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'flash_sale_id' => 1,
                'product_variant_id' => 92,
                'count' => 15,
                'discount' => 300000, // 300.000 VND
                'discount_type' => 'fixed',
                'buy_limit' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
