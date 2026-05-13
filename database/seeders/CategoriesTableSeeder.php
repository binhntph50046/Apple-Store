<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'iPhone',
                'slug' => 'iphone',
                'parent_id' => null,
                'order' => 1,
                'status' => 'active',
                'type' => 1,
            ],
            [
                'name' => 'Mac',
                'slug' => 'mac',
                'parent_id' => null,
                'order' => 2,
                'status' => 'active',
                'type' => 1,
            ],
            [
                'name' => 'iPad',
                'slug' => 'ipad',
                'parent_id' => null,
                'order' => 3,
                'status' => 'active',
                'type' => 1,
            ],
            [
                'name' => 'Apple Watch',
                'slug' => 'apple-watch',
                'parent_id' => null,
                'order' => 4,
                'status' => 'active',
                'type' => 1,
            ],
            [
                'name' => 'AirPods',
                'slug' => 'airpods',
                'parent_id' => null,
                'order' => 5,
                'status' => 'active',
                'type' => 1,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert($category);
        }
    }
}
