<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductReviewsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_reviews')->insert([
            'id' => 1,
            'product_id' => 1,
            'user_id' => 1,
            'rating' => 5,
            'review' => 'Amazing phone with excellent performance!',
            'created_at' => now(),
        ]);
    }
}
