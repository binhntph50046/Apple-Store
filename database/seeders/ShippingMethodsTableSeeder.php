<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShippingMethodsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('shipping_methods')->insert([
            [
                'name' => 'Standard Shipping',
                'description' => 'Standard shipping method',
                'price' => 30000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Express Shipping',
                'description' => 'Express shipping method',
                'price' => 50000,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
