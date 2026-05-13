<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FlashSalesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('flash_sales')->insert([
            [
                'name' => 'Flash Sale Tháng 5/2025',
                'start_time' => Carbon::parse('2025-05-28 08:00:00'),
                'end_time' => Carbon::parse('2025-05-28 20:00:00'),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Flash Sale Cuối Tuần',
                'start_time' => Carbon::parse('2025-05-30 10:00:00'),
                'end_time' => Carbon::parse('2025-05-30 22:00:00'),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
