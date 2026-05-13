<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAttributeTypeRequest;
use App\Http\Requests\Admin\UpdateAttributeTypeRequest;
use App\Models\VariantAttributeType;
use App\Models\Category;
use App\Models\VariantAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VariantAttributeTypeController
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 1)->get();
        $query = VariantAttributeType::query();

        if ($request->filled('category_id')) {
            $query->whereJsonContains('category_ids', $request->category_id);
        }

        $attributeTypes = $query->paginate(10);
        return view('admin.attributes.index', compact('attributeTypes', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('type', 1)->get();
        return view('admin.attributes.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'unique:variant_attribute_types,name,NULL,id,deleted_at,NULL'
            ],
            'status' => 'required|in:active,inactive',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id'
        ], [
            'name.required' => 'Tên loại thuộc tính là bắt buộc.',
            'name.max' => 'Tên loại thuộc tính không được vượt quá 255 ký tự.',
            'name.unique' => 'Tên loại thuộc tính này đã tồn tại.',
            'category_ids.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'category_ids.*.exists' => 'Một hoặc nhiều danh mục được chọn không hợp lệ.'
        ]);

        $attributeType = VariantAttributeType::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Loại thuộc tính đã được tạo thành công.',
                'attributeType' => $attributeType
            ]);
        }

        return redirect()->route('admin.attributes.index')->with('success', 'Loại thuộc tính đã được tạo thành công.');
    }

    public function storeValues(Request $request)
    {
        $validated = $request->validate([
            'attribute_type_id' => 'required|exists:variant_attribute_types,id',
            'values' => 'required|array|min:1',
            'values.*.value' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($request) {
                    // Lấy tất cả giá trị từ yêu cầu hiện tại
                    $currentValues = collect($request->values);

                    // Lấy màu hex của giá trị hiện tại
                    $index = explode('.', $attribute)[1];
                    $currentHexColor = $request->values[$index]['hex_color'] ?? null;

                    // Kiểm tra trùng lặp trong yêu cầu hiện tại
                    $duplicateCount = $currentValues->filter(function ($item) use ($value, $currentHexColor) {
                        return strtolower($item['value']) === strtolower($value)
                            && ($item['hex_color'] ?? null) === $currentHexColor;
                    })->count();

                    if ($duplicateCount > 1) {
                        $fail('Sự kết hợp giá trị và màu sắc này đã được sử dụng nhiều lần trong yêu cầu của bạn.');
                        return;
                    }

                    // Kiểm tra trùng lặp trong cơ sở dữ liệu
                    $exists = VariantAttributeValue::where('attribute_type_id', $request->attribute_type_id)
                        ->where(function ($query) use ($value, $currentHexColor) {
                            $query->whereJsonContains('value', $value)
                                ->where(function ($q) use ($currentHexColor) {
                                    if ($currentHexColor) {
                                        $q->whereJsonContains('hex', $currentHexColor);
                                    } else {
                                        $q->whereNull('hex')
                                            ->orWhere('hex', '[]')
                                            ->orWhere('hex', '[""]');
                                    }
                                });
                        })
                        ->exists();

                    if ($exists) {
                        $fail('Sự kết hợp giá trị và màu sắc này đã tồn tại cho loại thuộc tính này.');
                    }
                }
            ],
            'values.*.hex_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i'
        ], [
            'values.required' => 'Cần ít nhất một giá trị.',
            'values.min' => 'Cần ít nhất một giá trị.',
            'values.*.value.required' => 'Giá trị thuộc tính là bắt buộc.',
            'values.*.value.max' => 'Giá trị thuộc tính không được vượt quá 255 ký tự.',
            'values.*.hex_color.regex' => 'Màu sắc phải là mã hex hợp lệ (ví dụ: #FF0000).'
        ]);

        try {
            foreach ($validated['values'] as $value) {
                VariantAttributeValue::create([
                    'attribute_type_id' => $validated['attribute_type_id'],
                    'value' => array_map('trim', explode(',', $value['value'])),
                    'hex' => !empty($value['hex_color']) ? array_map('trim', explode(',', $value['hex_color'])) : [],
                    'status' => 'active'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Giá trị thuộc tính đã được thêm thành công.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lưu giá trị thuộc tính.'
            ]);
        }
    }

    public function edit(VariantAttributeType $attributeType)
    {
        $categories = Category::where('type', 1)->get();
        $attributeValues = $attributeType->attributeValues()->get();
        return view('admin.attributes.edit', compact('attributeType', 'categories', 'attributeValues'));
    }

    public function update(UpdateAttributeTypeRequest $request, VariantAttributeType $attributeType)
    {
        try {
            DB::beginTransaction();

            // Cập nhật chi tiết loại thuộc tính
            $data = $request->only(['name', 'status', 'category_ids']);
            $attributeType->update($data);

            // Lấy ID giá trị hiện có cần giữ lại
            $existingValueIds = $request->input('existing_values', []);

            // Lấy ID của các giá trị đang được cập nhật
            $updatingValueIds = collect($request->input('values', []))
                ->pluck('id')
                ->filter()
                ->toArray();

            // Xóa các giá trị không có trong existing_values hoặc đang được cập nhật
            VariantAttributeValue::where('attribute_type_id', $attributeType->id)
                ->whereNotIn('id', array_merge($existingValueIds, $updatingValueIds))
                ->delete();

            // Xử lý cập nhật giá trị thuộc tính
            if ($request->has('values')) {
                $validated = $request->validate([
                    'values' => 'required|array|min:1',
                    'values.*.id' => 'nullable|exists:variant_attribute_values,id',
                    'values.*.value' => [
                        'required',
                        'string',
                        'max:255',
                        function ($attribute, $value, $fail) use ($request, $attributeType) {
                            $index = explode('.', $attribute)[1];
                            $currentHexColor = $request->values[$index]['hex_color'] ?? null;
                            $valueId = $request->values[$index]['id'] ?? null;

                            // Bỏ qua validation nếu hex_color bị vô hiệu hóa
                            if (
                                !isset($request->values[$index]['hex_color']) ||
                                $request->values[$index]['hex_color'] === ''
                            ) {
                                $currentHexColor = null;
                            }

                            // Kiểm tra trùng lặp trong các giá trị đã gửi
                            $duplicateCount = collect($request->values)->filter(function ($item) use ($value, $currentHexColor) {
                                $itemHexColor = isset($item['hex_color']) && $item['hex_color'] !== '' ? $item['hex_color'] : null;
                                return strtolower($item['value']) === strtolower($value)
                                    && $itemHexColor === $currentHexColor;
                            })->count();

                            if ($duplicateCount > 1) {
                                $fail('Sự kết hợp giá trị và màu sắc này đã được sử dụng nhiều lần trong yêu cầu của bạn.');
                                return;
                            }

                            // Kiểm tra trùng lặp trong cơ sở dữ liệu, loại trừ giá trị hiện tại
                            $exists = VariantAttributeValue::where('attribute_type_id', $attributeType->id)
                                ->where('id', '!=', $valueId)
                                ->where(function ($query) use ($value, $currentHexColor) {
                                    $query->whereJsonContains('value', $value)
                                        ->where(function ($q) use ($currentHexColor) {
                                            if ($currentHexColor) {
                                                $q->whereJsonContains('hex', $currentHexColor);
                                            } else {
                                                $q->whereNull('hex')
                                                    ->orWhere('hex', '[]')
                                                    ->orWhere('hex', '[""]');
                                            }
                                        });
                                })
                                ->exists();

                            if ($exists) {
                                $fail('Sự kết hợp giá trị và màu sắc này đã tồn tại cho loại thuộc tính này.');
                            }
                        }
                    ],
                    'values.*.hex_color' => 'nullable|string|regex:/^#[0-9A-F]{6}$/i'
                ]);

                // Xử lý từng giá trị
                foreach ($validated['values'] as $valueData) {
                    $hexColor = isset($valueData['hex_color']) && $valueData['hex_color'] !== ''
                        ? array_map('trim', explode(',', $valueData['hex_color']))
                        : [];

                    if (isset($valueData['id'])) {
                        // Cập nhật giá trị hiện có
                        $attributeValue = VariantAttributeValue::find($valueData['id']);
                        if ($attributeValue) {
                            $attributeValue->update([
                                'value' => array_map('trim', explode(',', $valueData['value'])),
                                'hex' => $hexColor,
                            ]);
                        }
                    } else {
                        // Tạo giá trị mới
                        VariantAttributeValue::create([
                            'attribute_type_id' => $attributeType->id,
                            'value' => array_map('trim', explode(',', $valueData['value'])),
                            'hex' => $hexColor,
                            'status' => 'active'
                        ]);
                    }
                }
            }

            DB::commit();
            return redirect()->route('admin.attributes.index')->with('success', 'Loại thuộc tính và giá trị đã được cập nhật thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi khi cập nhật loại thuộc tính: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(VariantAttributeType $attributeType)
    {
        $attributeType->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Loại thuộc tính đã được xóa thành công.');
    }

    public function trash()
    {
        $categories = Category::where('type', 1)->get();
        $attributeTypes = VariantAttributeType::onlyTrashed()->paginate(10);
        return view('admin.attributes.trash', compact('attributeTypes', 'categories'));
    }

    public function restore($id)
    {
        $attributeType = VariantAttributeType::onlyTrashed()->findOrFail($id);
        $attributeType->restore();
        return redirect()->route('admin.attributes.trash')->with('success', 'Loại thuộc tính đã được khôi phục thành công.');
    }
}
