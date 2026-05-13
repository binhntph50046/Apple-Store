<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderReturn extends Model
{
    // Các trường có thể gán giá trị hàng loạt
    protected $fillable = [
        'order_id', 
        'user_id', 
        'status', 
        'reason', 
        'image', 
        'proof_video', // Video chứng minh hoàn hàng từ khách
        'refund_proof_image', // Hình ảnh chứng minh đã hoàn tiền từ admin
        'refund_note', // Ghi chú hoàn tiền từ admin
        'refund_amount', // Số tiền hoàn lại
        'refund_method', // Phương thức hoàn tiền
        'bank_info', // Thông tin tài khoản ngân hàng
        'admin_id', 
        'processed_at'
    ];

    // Kiểu dữ liệu cho các trường
    protected $casts = [
        'processed_at' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    /**
     * Đơn hàng liên quan đến yêu cầu hoàn hàng
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Người gửi yêu cầu hoàn hàng (khách hàng)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin xử lý yêu cầu hoàn hàng
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function items()
    {
        return $this->hasMany(OrderReturnItem::class);
    }

    /**
     * Lấy trạng thái hoàn hàng dạng text
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            'pending' => 'Chờ xử lý',
            'approved' => 'Đã duyệt',
            'completed' => 'Đã hoàn tiền',
            'rejected' => 'Đã từ chối',
        ];
        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Lấy phương thức hoàn tiền dạng text
     */
    public function getRefundMethodTextAttribute()
    {
        $methods = [
            'bank' => 'Chuyển khoản ngân hàng',
            'cash' => 'Tiền mặt',
            'e_wallet' => 'Ví điện tử',
        ];
        return $methods[$this->refund_method] ?? $this->refund_method;
    }
} 