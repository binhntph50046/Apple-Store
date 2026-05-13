<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CartsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('carts')->insert([
            'id' => 1,
            'user_id' => 1,
            'created_at' => now(),
        ]);
    }
}
