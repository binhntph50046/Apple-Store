<?php
namespace App\Http\Requests\admin;

use App\Models\VariantAttributeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\ProductVariant;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product') ? $this->route('product')->id : null;
        $variantIds = $this->route('product') ? $this->route('product')->variants->pluck('id')->toArray() : [];
        $variantsToDelete = $this->input('variants_to_delete', []);
        if (is_string($variantsToDelete)) {
            $variantsToDelete = json_decode($variantsToDelete, true) ?: [];
        }

        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('products', 'name')->ignore($productId),
            ],
            'slug' => [
                'nullable', 
                'string', 
                'max:255',
                Rule::unique('products', 'slug')->ignore($productId),
            ],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'warranty_months' => ['required', 'integer', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'views' => ['nullable', 'integer', 'min:0'],
            'specifications' => ['nullable', 'array'],
            'specifications.*.specification_id' => ['nullable', 'exists:specifications,id'],
            'specifications.*.value' => ['nullable', 'string', 'max:255'],
            'attributes' => ['required', 'array'],
            'attributes.*.attribute_type_id' => ['nullable', 'exists:variant_attribute_types,id'],
            'attributes.*.selected_values' => ['nullable'],
            'variants' => ['required', 'array'],
            'variants.*.id' => ['nullable', 'exists:product_variants,id'],
            'variants.*.name' => ['required', 'string', 'max:255'],
            'variants.*.slug' => ['required', 'string', 'max:255'],
            'variants.*.stock' => ['required', 'integer', 'min:0'],
            'variants.*.purchase_price' => ['required', 'numeric', 'min:0'],
            'variants.*.selling_price' => ['required', 'numeric', 'min:0'],
            'variants.*.is_default' => ['nullable', 'boolean'],
            'variants.*.images' => ['nullable', 'array'],
            'variants.*.images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'variants_to_delete' => ['nullable'],
            'images_to_delete' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.string' => 'Tên sản phẩm phải là chuỗi.',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên sản phẩm đã tồn tại.',
            'slug.string' => 'Đường dẫn sản phẩm phải là chuỗi.',
            'slug.max' => 'Đường dẫn sản phẩm không được vượt quá 255 ký tự.',
            'slug.unique' => 'Đường dẫn sản phẩm đã tồn tại.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'content.string' => 'Nội dung phải là chuỗi.',
            'category_id.nullable' => 'Danh mục có thể để trống.',
            'category_id.exists' => 'Danh mục không hợp lệ.',
            'warranty_months.required' => 'Vui lòng nhập thời gian bảo hành.',
            'warranty_months.integer' => 'Thời gian bảo hành phải là số nguyên.',
            'warranty_months.min' => 'Thời gian bảo hành phải lớn hơn hoặc bằng 0.',
            'is_featured.boolean' => 'Trạng thái nổi bật phải là đúng hoặc sai.',
            'status.required' => 'Trạng thái sản phẩm là bắt buộc.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'views.nullable' => 'Lượt xem có thể để trống.',
            'views.integer' => 'Lượt xem phải là số nguyên.',
            'views.min' => 'Lượt xem không thể là số âm.',
            'specifications.nullable' => 'Thông số kỹ thuật có thể để trống.',
            'specifications.array' => 'Thông số kỹ thuật phải là một mảng.',
            'specifications.*.specification_id.nullable' => 'ID thông số kỹ thuật có thể để trống.',
            'specifications.*.specification_id.exists' => 'Thông số kỹ thuật không hợp lệ.',
            'specifications.*.value.nullable' => 'Giá trị thông số kỹ thuật có thể để trống.',
            'specifications.*.value.string' => 'Giá trị thông số kỹ thuật phải là chuỗi.',
            'specifications.*.value.max' => 'Giá trị thông số kỹ thuật không được vượt quá 255 ký tự.',
            'attributes.required' => 'Cần ít nhất một thuộc tính để tạo biến thể.',
            'attributes.array' => 'Thuộc tính phải là một mảng.',
            'attributes.min' => 'Cần ít nhất một thuộc tính để tạo biến thể.',
            'attributes.*.attribute_type_id.nullable' => 'ID loại thuộc tính có thể để trống.',
            'attributes.*.attribute_type_id.exists' => 'Loại thuộc tính không hợp lệ.',
            'attributes.*.selected_values.nullable' => 'Giá trị được chọn có thể để trống.',
            'variants.required' => 'Cần ít nhất một biến thể.',
            'variants.array' => 'Biến thể phải là một mảng.',
            'variants.min' => 'Cần ít nhất một biến thể.',
            'variants.*.id.nullable' => 'ID biến thể có thể để trống.',
            'variants.*.id.exists' => 'Biến thể không tồn tại trong hệ thống.',
            'variants.*.name.required' => 'Vui lòng nhập tên biến thể.',
            'variants.*.name.string' => 'Tên biến thể phải là chuỗi.',
            'variants.*.name.max' => 'Tên biến thể không được vượt quá 255 ký tự.',
            'variants.*.name.unique' => 'Tên biến thể đã tồn tại.',
            'variants.*.slug.required' => 'Đường dẫn biến thể là bắt buộc.',
            'variants.*.slug.string' => 'Đường dẫn biến thể phải là chuỗi.',
            'variants.*.slug.max' => 'Đường dẫn biến thể không được vượt quá 255 ký tự.',
            'variants.*.slug.unique' => 'Đường dẫn biến thể đã tồn tại.',
            'variants.*.stock.required' => 'Vui lòng nhập số lượng tồn kho.',
            'variants.*.stock.integer' => 'Số lượng tồn kho phải là số nguyên.',
            'variants.*.stock.min' => 'Số lượng tồn kho phải lớn hơn hoặc bằng 0.',
            'variants.*.purchase_price.required' => 'Vui lòng nhập giá nhập.',
            'variants.*.purchase_price.numeric' => 'Giá nhập phải là số.',
            'variants.*.purchase_price.min' => 'Giá nhập phải lớn hơn hoặc bằng 0.',
            'variants.*.selling_price.required' => 'Vui lòng nhập giá bán.',
            'variants.*.selling_price.numeric' => 'Giá bán phải là số.',
            'variants.*.selling_price.min' => 'Giá bán phải lớn hơn hoặc bằng 0.',
            'variants.*.is_default.boolean' => 'Trạng thái mặc định phải là đúng hoặc sai.',
            'variants.*.images.nullable' => 'Hình ảnh biến thể có thể để trống.',
            'variants.*.images.array' => 'Hình ảnh biến thể phải là một mảng.',
            'variants.*.images.*.image' => 'Tệp phải là hình ảnh.',
            'variants.*.images.*.mimes' => 'Hình ảnh phải có định dạng jpeg, png, jpg, gif hoặc webp.',
            'variants.*.images.*.max' => 'Kích thước hình ảnh không được vượt quá 2MB.',
            'variants_to_delete.nullable' => 'Danh sách biến thể cần xóa có thể để trống.',
            'variants_to_delete.array' => 'Danh sách biến thể cần xóa không hợp lệ.',
            'variants_to_delete.*.exists' => 'Một số biến thể cần xóa không tồn tại.',
            'images_to_delete.nullable' => 'Danh sách hình ảnh cần xóa có thể để trống.',
            'images_to_delete.array' => 'Danh sách hình ảnh cần xóa không hợp lệ.',
            'images_to_delete.*.string' => 'Đường dẫn hình ảnh cần xóa không hợp lệ.'
        ];
    }

    protected function prepareForValidation()
    {
        // Clean and prepare variants_to_delete
        if ($this->has('variants_to_delete')) {
            $variantsToDelete = $this->input('variants_to_delete');
            if (is_string($variantsToDelete)) {
                $variantsToDelete = json_decode($variantsToDelete, true) ?: [];
            }
            $this->merge(['variants_to_delete' => $variantsToDelete]);
        }

        // Clean and prepare images_to_delete
        if ($this->has('images_to_delete')) {
            $imagesToDelete = $this->input('images_to_delete');
            if (is_string($imagesToDelete)) {
                $imagesToDelete = json_decode($imagesToDelete, true) ?: [];
            }
            $this->merge(['images_to_delete' => $imagesToDelete]);
        }

        // Clean and prepare attributes
        if ($this->has('attributes')) {
            $attributes = $this->input('attributes', []);
            foreach ($attributes as $key => $attribute) {
                if (isset($attribute['selected_values'])) {
                    $selectedValues = $attribute['selected_values'];
                    if (is_string($selectedValues)) {
                        $selectedValues = json_decode($selectedValues, true);
                        if (!is_array($selectedValues)) {
                            $selectedValues = [$selectedValues];
                        }
                    } elseif (!is_array($selectedValues)) {
                        $selectedValues = [$selectedValues];
                    }
                    $attributes[$key]['selected_values'] = $selectedValues;
                }
            }
            $this->merge(['attributes' => $attributes]);
        }
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate at least one attribute is selected
            $attributes = $this->input('attributes', []);
            $hasValidAttribute = false;
            foreach ($attributes as $attribute) {
                if (!empty($attribute['attribute_type_id']) && 
                    !empty($attribute['selected_values']) && 
                    is_array($attribute['selected_values'])) {
                    $hasValidAttribute = true;
                    break;
                }
            }
            if (!$hasValidAttribute) {
                $validator->errors()->add('attributes', 'Cần ít nhất một thuộc tính để tạo biến thể.');
            }

            // Validate default variant & duplicate slug
            $variants = $this->input('variants', []);
            $defaultCount = 0;
            $variantSlugs = [];
            $productId = $this->route('product') ? $this->route('product')->id : null;
            foreach ($variants as $index => $variant) {
                if (!empty($variant['is_default'])) {
                    $defaultCount++;
                }
                // Validate duplicate slug (tên biến thể ghép)
                $variantName = $variant['name'] ?? '';
                $slug = Str::slug($variantName);
                if (!empty($slug)) {
                    // Kiểm tra trùng trong request
                    if (in_array($slug, $variantSlugs)) {
                        $validator->errors()->add(
                            "variants.$index.name",
                            "Biến thể '{$variantName}' bị trùng với biến thể khác trong form."
                        );
                    }
                    $variantSlugs[] = $slug;

                    // Kiểm tra trùng trong DB (kể cả soft delete)
                    $exists = ProductVariant::withTrashed()
                        ->where('product_id', $productId)
                        ->where('slug', $slug)
                        ->when(!empty($variant['id']), function ($query) use ($variant) {
                            $query->where('id', '!=', $variant['id']);
                        })
                        ->exists();
                    if ($exists) {
                        $validator->errors()->add(
                            "variants.$index.name",
                            "Biến thể '{$variantName}' đã tồn tại trong hệ thống."
                        );
                    }
                }
            }
            if ($defaultCount !== 1) {
                $validator->errors()->add('variants', 'Phải có đúng một biến thể mặc định.');
            }

            // Validate selling price > purchase price
            foreach ($variants as $key => $variant) {
                if (isset($variant['purchase_price'], $variant['selling_price']) &&
                    $variant['selling_price'] < $variant['purchase_price']) {
                    $validator->errors()->add(
                        "variants.{$key}.selling_price",
                        'Giá bán phải lớn hơn hoặc bằng giá nhập.'
                    );
                }
            }
        });
    }
}
