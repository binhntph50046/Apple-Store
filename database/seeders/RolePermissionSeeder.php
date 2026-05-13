<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Tạo quyền cho các module
        $permissions = [
            // Categories
            'view categories', 'create categories', 'edit categories', 'delete categories',
            // Banners
            'view banners', 'create banners', 'edit banners', 'delete banners',
            // Products
            'view products', 'create products', 'edit products', 'delete products',
            // Orders
            'view orders', 'create orders', 'edit orders', 'delete orders',
            // Users
            'view users', 'create users', 'edit users', 'delete users',
            // Blogs
            'view blogs', 'create blogs', 'edit blogs', 'delete blogs',
            // Attributes
            'view attributes', 'create attributes', 'edit attributes', 'delete attributes',
            // Dashboard
            'view dashboard',
            // Categories specifications and attributes
            'view category specifications', 
            'view category attributes', 
            // Product Specifications
            'view specifications', 'trash specifications', 'restore specifications', 'delete specifications',
            // Vouchers
            'view vouchers', 'create vouchers', 'edit vouchers', 'delete vouchers',
            // Roles
            'addrole',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Tạo vai trò
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $staffRole = Role::firstOrCreate(['name' => 'staff']);
        $userRole = Role::firstOrCreate(['name' => 'user']);

        // Gán quyền cho admin (toàn quyền)
        $adminRole->givePermissionTo($permissions);

        // Gán quyền cho staff (theo yêu cầu quản lý)
        $staffRole->givePermissionTo([
            'view categories', 'view banners', 'view products', 'create products', 'edit products', 'delete products',
            'view orders', 'edit orders', 'delete orders',
            'view users', 'create users', 'edit users',
            'view blogs', 'create blogs', 'edit blogs', 'delete blogs',
            'view attributes', 'view dashboard',
            'view vouchers', 'create vouchers', 'edit vouchers'
        ]);

        // Tạo người dùng mẫu và gán vai trò
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('123123123'),
                'phone' => '0123456789',
                'address' => 'Hanoi',
                'status' => 'active'
            ]
        );
        $admin->assignRole('admin');

        $staff = User::firstOrCreate(
            ['email' => 'staffp@example.com'],
            [
                'name' => 'Staff User',
                'password' => bcrypt('123123123'),
                'phone' => '0987654321',
                'address' => 'Hanoi',
                'status' => 'active'
            ]
        );
        $staff->assignRole('staff');

        $user = User::firstOrCreate(
            ['email' => 'userp@example.com'],
            [
                'name' => 'Normal User',
                'password' => bcrypt('123123123'),
                'phone' => '1234567890',
                'address' => 'Hanoi',
                'status' => 'active'
            ]
        );
        $user->assignRole('user');
    }
}