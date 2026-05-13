<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->insert([
            'id' => 1,
            'name' => 'manage_products',
            'description' => 'Permission to manage Apple products',
        ]);
    }
}
