<?php

namespace App\Http\Requests\admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:products,name',
            'category_id' => 'required|exists:categories,id',
            'warranty_months' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'is_featured' => 'boolean',
            
            // Validate specifications
            'specifications' => 'nullable|array',
            'specifications.*.specification_id' => 'required_with:specifications.*.value|exists:specifications,id',
            'specifications.*.value' => 'nullable|string',

            // Validate attributes - simplified
            'attributes' => 'required|array|min:1',
            'attributes.*.attribute_type_id' => 'nullable|exists:variant_attribute_types,id',
            'attributes.*.selected_values' => 'required_with:attributes.*.attribute_type_id|array',

            // Validate variants
            'variants' => 'required|array|min:1',
            'variants.*.name' => 'required|string|max:255',
            'variants.*.slug' => 'required|string|max:255',
            'variants.*.stock' => 'required|integer|min:0',
            'variants.*.purchase_price' => 'required|numeric|min:0',
            'variants.*.selling_price' => 'required|numeric|min:0',
            'variants.*.is_default' => 'boolean',
            'variants.*.images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'variants.*.attributes' => 'required|array',
            'variants.*.attributes.*.attribute_type_id' => 'required|exists:variant_attribute_types,id',
            'variants.*.attributes.*.selected_values' => 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm là bắt buộc.',
            'name.string' => 'Tên sản phẩm phải là chuỗi.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'category_id.required' => 'Vui lòng chọn danh mục.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'warranty_months.required' => 'Thời gian bảo hành là bắt buộc.',
            'warranty_months.integer' => 'Thời gian bảo hành phải là số nguyên.',
            'warranty_months.min' => 'Thời gian bảo hành phải lớn hơn hoặc bằng 0.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'content.string' => 'Nội dung phải là chuỗi.',
            'is_featured.boolean' => 'Trạng thái nổi bật phải là đúng hoặc sai.',
            'views.integer' => 'Lượt xem phải là số nguyên.',
            'views.in' => 'Lượt xem phải là 0 cho sản phẩm mới.',
            
            'specifications.array' => 'Thông số kỹ thuật phải là một mảng.',
            'specifications.*.specification_id.required_with' => 'ID thông số kỹ thuật là bắt buộc khi có giá trị.',
            'specifications.*.specification_id.exists' => 'Thông số kỹ thuật không hợp lệ.',
            'specifications.*.value.string' => 'Giá trị thông số kỹ thuật phải là chuỗi.',
            
            'attributes.required' => 'Phải có ít nhất một thuộc tính.',
            'attributes.array' => 'Thuộc tính phải là một mảng.',
            'attributes.min' => 'Phải có ít nhất một thuộc tính.',
            'attributes.*.attribute_type_id.nullable' => 'ID loại thuộc tính có thể để trống.',
            'attributes.*.attribute_type_id.exists' => 'Loại thuộc tính không hợp lệ.',
            'attributes.*.selected_values.required_with' => 'Giá trị được chọn là bắt buộc khi có loại thuộc tính.',
            'attributes.*.selected_values.array' => 'Giá trị được chọn phải là một mảng.',
            
            'variants.required' => 'Phải có ít nhất một biến thể.',
            'variants.array' => 'Biến thể phải là một mảng.',
            'variants.min' => 'Phải có ít nhất một biến thể.',
            'variants.*.name.required' => 'Tên biến thể là bắt buộc.',
            'variants.*.name.string' => 'Tên biến thể phải là chuỗi.',
            'variants.*.name.max' => 'Tên biến thể không được vượt quá 255 ký tự.',
            'variants.*.slug.required' => 'Đường dẫn biến thể là bắt buộc.',
            'variants.*.slug.string' => 'Đường dẫn biến thể phải là chuỗi.',
            'variants.*.slug.max' => 'Đường dẫn biến thể không được vượt quá 255 ký tự.',
            'variants.*.stock.required' => 'Số lượng tồn kho là bắt buộc.',
            'variants.*.stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.stock.min' => 'Số lượng tồn kho không được âm.',
            'variants.*.purchase_price.required' => 'Giá nhập là bắt buộc.',
            'variants.*.purchase_price.numeric' => 'Giá nhập phải là số.',
            'variants.*.purchase_price.min' => 'Giá nhập không được âm.',
            'variants.*.selling_price.required' => 'Giá bán là bắt buộc.',
            'variants.*.selling_price.numeric' => 'Giá bán phải là số.',
            'variants.*.selling_price.min' => 'Giá bán không được âm.',
            'variants.*.is_default.boolean' => 'Trạng thái mặc định phải là đúng hoặc sai.',
            'variants.*.images.array' => 'Hình ảnh biến thể phải là một mảng.',
            'variants.*.images.*.nullable' => 'Hình ảnh có thể để trống.',
            'variants.*.images.*.image' => 'File phải là hình ảnh.',
            'variants.*.images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif.',
            'variants.*.images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'variants.*.attributes.required' => 'Thuộc tính biến thể là bắt buộc.',
            'variants.*.attributes.array' => 'Thuộc tính biến thể phải là một mảng.',
            'variants.*.attributes.*.attribute_type_id.required' => 'ID loại thuộc tính biến thể là bắt buộc.',
            'variants.*.attributes.*.attribute_type_id.exists' => 'Loại thuộc tính biến thể không hợp lệ.',
            'variants.*.attributes.*.selected_values.required' => 'Giá trị được chọn cho biến thể là bắt buộc.',
        ];
    }

    protected function prepareForValidation()
{
    $this->merge([
        'views' => 0 // Always set views to 0 for new products
    ]);

    // Ép lại kiểu dữ liệu cho attributes nếu là mảng chỉ số (sau khi validate lỗi)
    $attributes = $this->input('attributes');
    if (is_array($attributes)) {
        foreach ($attributes as $key => $attr) {
            if (is_array($attr) && array_key_exists(0, $attr) && array_key_exists(1, $attr)) {
                $attributes[$key] = [
                    'attribute_type_id' => $attr[0] ?? null,
                    'selected_values' => $attr[1] ?? [],
                ];
            }
        }
        $this->merge(['attributes' => $attributes]);
    }

    // Ép lại kiểu dữ liệu cho variants.*.attributes nếu là mảng chỉ số
    $variants = $this->input('variants');
    if (is_array($variants)) {
        foreach ($variants as $vKey => $variant) {
            if (isset($variant['attributes']) && is_array($variant['attributes'])) {
                foreach ($variant['attributes'] as $aKey => $attr) {
                    if (is_array($attr) && array_key_exists(0, $attr) && array_key_exists(1, $attr)) {
                        $variants[$vKey]['attributes'][$aKey] = [
                            'attribute_type_id' => $attr[0] ?? null,
                            'selected_values' => $attr[1] ?? [],
                        ];
                    }
                }
            }
        }
        $this->merge(['variants' => $variants]);
    }

    // Debug: Log the incoming data
    Log::info('Product creation data:', $this->all());
}

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Kiểm tra không được chọn trùng loại thuộc tính
            $attributes = $this->input('attributes', []);
            $attributeTypeIds = collect($attributes)
                ->pluck('attribute_type_id')
                ->filter()
                ->values();
            
            // Kiểm tra có ít nhất 1 thuộc tính được chọn
            if ($attributeTypeIds->isEmpty()) {
                $validator->errors()->add('attributes', 'Phải chọn ít nhất một thuộc tính.');
                return;
            }

            // Kiểm tra không được chọn trùng loại thuộc tính
            if ($attributeTypeIds->count() !== $attributeTypeIds->unique()->count()) {
                $validator->errors()->add('attributes', 'Không được chọn trùng loại thuộc tính.');
            }

            // Kiểm tra variants có đủ thuộc tính
            $variants = $this->input('variants', []);
            foreach ($variants as $variantIndex => $variant) {
                $variantAttributes = $variant['attributes'] ?? [];
                if (empty($variantAttributes)) {
                    $validator->errors()->add("variants.{$variantIndex}.attributes", 'Biến thể phải có thuộc tính.');
                    continue;
                }

                foreach ($variantAttributes as $attrIndex => $attribute) {
                    $selectedValues = $attribute['selected_values'] ?? [];
                    
                    if (empty($selectedValues)) {
                        $validator->errors()->add("variants.{$variantIndex}.attributes.{$attrIndex}.selected_values", 'Phải chọn ít nhất một giá trị cho thuộc tính biến thể.');
                    }
                }
            }
        });
    }
}