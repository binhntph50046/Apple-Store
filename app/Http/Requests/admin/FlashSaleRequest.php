<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FlashSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Hoặc kiểm tra quyền nếu cần
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:flash_sales,name',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'required|exists:product_variants,id',
            'items.*.discount' => 'required|numeric|min:1',
            'items.*.discount_type' => 'required|in:percent,fixed',
            'items.*.count' => 'required|integer|min:1',
            'items.*.buy_limit' => 'required|integer|min:1',
        ];
            if ($this->isMethod('post')) {
        // Store: không cần nhận status từ form (set cứng = 0 trong controller)
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Update: chỉ cho phép status là 0 hoặc 2
            $rules['status'] = 'required|in:0,2';
        }
            return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên chương trình khuyến mãi là bắt buộc.',
            'name.string' => 'Tên phải là chuỗi ký tự.',
            'name.max' => 'Tên không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên chương trình đã tồn tại.',

            'start_time.required' => 'Vui lòng chọn thời gian bắt đầu.',
            'start_time.date' => 'Thời gian bắt đầu không hợp lệ.',

            'end_time.required' => 'Vui lòng chọn thời gian kết thúc.',
            'end_time.date' => 'Thời gian kết thúc không hợp lệ.',
            'end_time.after' => 'Thời gian kết thúc phải sau thời gian bắt đầu.',

            'items.required' => 'Vui lòng chọn ít nhất một sản phẩm.',
            'items.array' => 'Danh sách sản phẩm không hợp lệ.',
            'items.min' => 'Phải có ít nhất một sản phẩm.',

            'items.*.product_id.required' => 'Sản phẩm là bắt buộc.',
            'items.*.product_id.exists' => 'Sản phẩm không tồn tại.',

            'items.*.product_variant_id.required' => 'Biến thể là bắt buộc.',
            'items.*.product_variant_id.exists' => 'Biến thể sản phẩm không tồn tại.',

            'items.*.discount.required' => 'Giá khuyến mãi là bắt buộc.',
            'items.*.discount.numeric' => 'Giá khuyến mãi phải là số.',
            'items.*.discount.min' => 'Giá khuyến mãi không được nhỏ hơn 0.',

            'items.*.discount_type.required' => 'Loại giảm giá là bắt buộc.',
            'items.*.discount_type.in' => 'Loại giảm giá không hợp lệ.',

            'items.*.count.required' => 'Số lượng là bắt buộc.',
            'items.*.count.integer' => 'Số lượng phải là số nguyên.',
            'items.*.count.min' => 'Số lượng tối thiểu là 1.',

            'items.*.buy_limit.required' => 'Giới hạn mua là bắt buộc.',
            'items.*.buy_limit.integer' => 'Giới hạn mua phải là số nguyên.',
            'items.*.buy_limit.min' => 'Giới hạn mua tối thiểu là 1.',

            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }
}
