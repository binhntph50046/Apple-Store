<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAttributeTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;  // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('variant_attribute_types')->ignore($this->attributeType)],
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên loại thuộc tính là bắt buộc.',
            'name.string' => 'Tên loại thuộc tính phải là chuỗi.',
            'name.max' => 'Tên loại thuộc tính không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên loại thuộc tính này đã tồn tại.',
            'category_ids.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'category_ids.*.exists' => 'Một hoặc nhiều danh mục được chọn không hợp lệ.',
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái phải là hoạt động hoặc không hoạt động.',
        ];
    }
}
