<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductVariantsTableSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            // iPhone 15 Pro Max variants
            [
                'product_id' => 1,
                'sku' => 'IP15PM-256-BLK',
                'name' => 'iPhone 15 Pro Max 256GB Black Titanium',
                'slug' => 'iphone-15-pro-max-256gb-black-titanium',
                'color_id' => 1,
                'capacity_id' => 1,
                'price' => 33990000,
                'discount_price' => 32990000,
                'stock' => 30,
                
            ]
        ];

        foreach ($variants as $variant) {
            DB::table('product_variants')->insert($variant);
        }
    }
}
