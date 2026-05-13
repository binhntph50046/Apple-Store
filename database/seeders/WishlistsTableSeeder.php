<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WishlistsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('wishlists')->insert([
            'id' => 1,
            'user_id' => 1,
            'product_id' => 1,
            'variant_id' => 1,
            'created_at' => now(),
        ]);
    }
}
