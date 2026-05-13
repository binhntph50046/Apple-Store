<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImagesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_images')->insert([
            'id' => 1,
            'product_id' => 1,
            'image_url' => 'iphone15pro_main.jpg',
          
        ]);
    }
}
