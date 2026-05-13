<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UsersTableSeeder::class,
            CategoriesTableSeeder::class,
            ProductsTableSeeder::class,
            ProductImagesTableSeeder::class,
            ProductReviewsTableSeeder::class,
            ProductVariantsTableSeeder::class,
            ShippingMethodsTableSeeder::class,
            OrdersTableSeeder::class,
            OrderItemsTableSeeder::class,
            BlogSeeder::class,
            BannerSeeder::class,
        ]);
    }
}
