<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserAddressesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_addresses')->insert([
            'id' => 1,
            'user_id' => 1,
            'fullname' => 'Admin User',
            'phone' => '0123456789',
            'address_line' => '123 Apple St',
            'city' => 'Cupertino',
            'district' => 'Santa Clara',
            'ward' => null,
            'postal_code' => '95014',
            'is_default' => true,
            'created_at' => now(),
        ]);
    }
}
