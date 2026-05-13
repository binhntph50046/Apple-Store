<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Tạo 10 bản ghi banner
        Banner::create([
            'title' => 'Banner 1',
            'image' => 'banner1.jpg',
            'status' => 'active',
            'order' => 1,
        ]);

        Banner::create([
            'title' => 'Banner 2',
            'image' => 'banner2.jpg',
            'status' => 'inactive',
            'order' => 2,
        ]);

        Banner::create([
            'title' => 'Banner 3',
            'image' => 'banner3.jpg',
            'status' => 'active',
            'order' => 3,
        ]);

        // Bạn có thể tiếp tục tạo thêm các banner khác ở đây
        // Tạo 7 banner còn lại
        for ($i = 4; $i <= 10; $i++) {
            Banner::create([
                'title' => 'Banner ' . $i,
                'image' => 'banner' . $i . '.jpg',
                'status' => $i % 2 == 0 ? 'inactive' : 'active',
                'order' => $i,
            ]);
        }
    }
}
