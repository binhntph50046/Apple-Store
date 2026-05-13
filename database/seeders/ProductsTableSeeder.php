<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductsTableSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'iPhone 15 Pro Max',
                'slug' => 'iphone-15-pro-max',
                'description' => 'iPhone 15 Pro Max - Điện thoại thông minh mạnh mẽ nhất của Apple',
                'content' => 'iPhone 15 Pro Max với chip A17 Pro, camera 48MP, màn hình Super Retina XDR 6.7 inch, và thiết kế titan cao cấp.',
                'price' => 33990000,
                'discount_price' => 32990000,
                'category_id' => 1, // iPhone
                'model' => 'iPhone 15 Pro Max',
                'series' => 'iPhone',
                'stock' => 100,
                'warranty_months' => 12,
                'is_featured' => true,
                'status' => 'active',
                'image' => 'iphone-15-pro-max.jpg',
                'specifications' => json_encode([
                    'screen' => '6.7 inch Super Retina XDR OLED',
                    'processor' => 'A17 Pro chip',
                    'ram' => '8GB',
                    'storage' => '256GB',
                    'camera' => '48MP + 12MP + 12MP',
                    'battery' => '4422 mAh',
                    'os' => 'iOS 17'
                ]),
                'features' => json_encode([
                    'Dynamic Island',
                    'USB-C',
                    'Action button',
                    'Emergency SOS via satellite',
                    'MagSafe'
                ])
            ],
            [
                'name' => 'MacBook Pro M3 Pro',
                'slug' => 'macbook-pro-m3-pro',
                'description' => 'MacBook Pro với chip M3 Pro - Hiệu năng đột phá cho công việc chuyên nghiệp',
                'content' => 'MacBook Pro với chip M3 Pro, màn hình Liquid Retina XDR 14.2 inch, và thời lượng pin lên đến 22 giờ.',
                'price' => 49990000,
                'discount_price' => 48990000,
                'category_id' => 2, // Mac
                'model' => 'MacBook Pro M3 Pro',
                'series' => 'MacBook Pro',
                'stock' => 50,
                'warranty_months' => 12,
                'is_featured' => true,
                'status' => 'active',
                'image' => 'macbook-pro-m3-pro.jpg',
                'specifications' => json_encode([
                    'screen' => '14.2 inch Liquid Retina XDR',
                    'processor' => 'M3 Pro chip',
                    'ram' => '18GB',
                    'storage' => '512GB SSD',
                    'gpu' => '18-core GPU',
                    'battery' => 'Up to 22 hours',
                    'os' => 'macOS Sonoma'
                ]),
                'features' => json_encode([
                    'ProMotion technology',
                    'True Tone',
                    'Touch Bar',
                    'Magic Keyboard',
                    'Thunderbolt 4 ports'
                ])
            ]
        ];

        foreach ($products as $product) {
            DB::table('products')->insert($product);
        }
    }
}
