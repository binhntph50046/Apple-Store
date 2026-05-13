<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartItemsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('cart_items')->insert([
            'id' => 1,
            'cart_id' => 1,
            'product_id' => 1,
            'variant_id' => 1,
            'quantity' => 1,
        ]);
    }
}
