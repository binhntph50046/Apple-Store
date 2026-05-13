<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@.com',
                'password' => Hash::make('88888888'),
                'phone' => '0123456789',
                'address' => 'Hà Nội, Việt Nam',
                'avatar' => null,
                'dob' => '1990-01-01',
                'gender' => 'other',
                'is_verified' => true,
                'last_login' => now(),
                'role_id' => $adminRole->id,
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }
}
