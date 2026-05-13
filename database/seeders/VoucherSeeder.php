<?php

namespace Database\Seeders;

use App\Models\Voucher;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Xóa dữ liệu cũ (tuỳ chọn)
        // Voucher::truncate();

        $vouchers = [
            [
                'code' => 'WELCOME10',
                'type' => 'percentage',
                'value' => 10,
                'expires_at' => Carbon::now()->addMonth(),
                'is_active' => true,
                'purpose' => 'product_discount', // giảm giá sản phẩm
                'description' => 'Giảm 10% cho đơn hàng đầu tiên.',
                'usage_limit' => 100, // Số lượt sử dụng tối đa
                'used_count' => 0, // Đã sử dụng bao nhiêu lần
                'per_user_limit' => 1, // Số lần tối đa mỗi user được sử dụng
                'expires_at' => Carbon::now()->addDays(30),
            ],
            [
                'code' => 'FREESHIP',
                'type' => 'fixed',
                'value' => 50000,
                'expires_at' => Carbon::now()->addDays(10),
                'is_active' => true,
                'purpose' => 'free_shipping', // miễn phí vận chuyển
                'description' => 'Miễn phí vận chuyển cho đơn hàng trên 200,000 VNĐ.',
                'usage_limit' => 100, // Số lượt sử dụng tối đa
                'used_count' => 0, // Đã sử dụng bao nhiêu lần
                'per_user_limit' => 1, // Số lần tối đa mỗi user được sử dụng
                'expires_at' => Carbon::now()->addDays(30),

            ],
            [
                'code' => 'SUMMER20',
                'type' => 'percentage',
                'value' => 20,
                'expires_at' => Carbon::now()->addMonths(3),
                'is_active' => false,
                'purpose' => 'product_discount',
                'description' => 'Giảm giá 20% mùa hè.',
                'usage_limit' => 100, // Số lượt sử dụng tối đa
                'used_count' => 0, // Đã sử dụng bao nhiêu lần
                'per_user_limit' => 1, // Số lần tối đa mỗi user được sử dụng
                'expires_at' => Carbon::now()->addDays(30),

            ],
            [
                'code' => 'NOEXPIRE',
                'type' => 'fixed',
                'value' => 100000,
                'expires_at' => null,
                'is_active' => true,
                'purpose' => 'product_discount',
                'description' => 'Voucher không giới hạn thời gian.',
                'usage_limit' => 100, // Số lượt sử dụng tối đa
                'used_count' => 0, // Đã sử dụng bao nhiêu lần
                'per_user_limit' => 1, // Số lần tối đa mỗi user được sử dụng
                'expires_at' => Carbon::now()->addDays(30),

            ],
        ];

        foreach ($vouchers as $data) {
            Voucher::create($data);
        }
    }
}
