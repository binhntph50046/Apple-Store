<?php

namespace App\Http\Controllers\admin;

use Illuminate\Support\Facades\Log;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\ProductVariant;
use App\Models\VariantCombination;
use Illuminate\Support\Facades\DB;
use App\Models\ProductSpecification;
use App\Models\VariantAttributeType;
use App\Models\VariantAttributeValue;
use App\Http\Requests\admin\StoreProductRequest;
use App\Models\Specification;
use App\Http\Requests\admin\UpdateProductRequest;
use Illuminate\Support\Facades\Auth;
use App\Events\ProductCreated;
use App\Models\User;
use App\Notifications\AdminDatabaseNotification;
use Illuminate\Support\Facades\Notification;

class ProductController
{
    public function index()
    {
        $query = Product::with([
            'category',
            'variants',
            'defaultVariant'
        ]);

        if (request('name')) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }
        if (request('category_id')) {
            $query->where('category_id', request('category_id'));
        }
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $products = $query->orderBy('created_at', 'desc')->paginate(10);
        $categories = Category::where('type', 1)->get();
        $trashedCount = Product::onlyTrashed()->count();

        return view('admin.products.index', compact('products', 'categories', 'trashedCount'));
    }

    public function create()
    {
        $categories = Category::where('type', 1)->where('status', 'active')->get();
        $attributeTypes = VariantAttributeType::all();
        return view('admin.products.create', compact('categories', 'attributeTypes'));
    }

    public function store(StoreProductRequest $request)
    {
        try {
            DB::beginTransaction();

            $productData = [
                'name' => $request->name,
                'slug' => Str::slug($request->name) . '-' . time(),
                'category_id' => $request->category_id,
                'warranty_months' => $request->warranty_months ?? 12,
                'description' => $request->description,
                'content' => $request->content,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
                'status' => 'active',
                'views' => 0,
            ];

            $product = Product::create($productData);

            // Lưu thông số kỹ thuật nếu có
            if ($request->has('specifications') && is_array($request->specifications)) {
                foreach ($request->specifications as $spec) {
                    if (!empty($spec['value'])) {
                        $product->specifications()->create([
                            'specification_id' => $spec['specification_id'],
                            'value' => $spec['value']
                        ]);
                    }
                }
            }

            // Kiểm tra thuộc tính có ít nhất một thuộc tính hợp lệ
            $validAttributes = [];
            $attributes = $request->input('attributes', []);
            if (!empty($attributes)) {
                foreach ($attributes as $index => $attribute) {
                    if (!empty($attribute['attribute_type_id']) && !empty($attribute['selected_values'])) {
                        $validAttributes[] = [
                            'attribute_type_id' => $attribute['attribute_type_id'],
                            'selected_values' => $attribute['selected_values']
                        ];
                    }
                }
            }
            if (empty($validAttributes)) {
                throw new \Exception('Cần ít nhất một thuộc tính với giá trị để tạo biến thể.');
            }

            // Tạo biến thể tự động từ thuộc tính
            $this->createVariantsFromAttributes($product, $validAttributes, $request);

            // Đảm bảo có ít nhất một biến thể mặc định
            $this->ensureDefaultVariant($product);

            // Broadcast event cho realtime
            $product->load(['category', 'variants', 'reviews']);
            event(new ProductCreated($product));

            DB::commit();
            $this->syncToAIEngine('create', $product);

            // Gửi thông báo cho tất cả client
            $clients = User::whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['admin', 'staff']);
            })->get();

            if ($clients->isNotEmpty()) {
                $data = [
                    'type' => 'new_product',
                    'title' => 'Sản phẩm mới đã ra mắt!',
                    'message' => "Khám phá ngay sản phẩm mới: {$product->name}",
                    'url' => route('product.detail', $product->slug),
                ];
                Notification::send($clients, new AdminDatabaseNotification($data));
            }
            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi tạo sản phẩm: ' . $e->getMessage());
            Log::error('Chi tiết lỗi: ' . $e->getTraceAsString());
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        try {
            DB::beginTransaction();

            // Log dữ liệu yêu cầu để debug
            Log::info('Yêu cầu cập nhật sản phẩm:', [
                'product_id' => $product->id,
                'has_variants' => $request->has('variants'),
                'variants_count' => $request->has('variants') ? count($request->variants) : 0,
                'images_to_delete' => $request->images_to_delete ?? 'none',
                'variants_to_delete' => $request->variants_to_delete ?? 'none',
                'has_image_deletions' => $request->has('has_image_deletions') ? $request->has_image_deletions : '0'
            ]);

            // Lưu giá trị cũ cho nhật ký hoạt động
            $oldValues = $product->toArray();

            // Cập nhật thông tin sản phẩm cơ bản
            $product->update([
                'name' => $request->name,
                'slug' => $product->name !== $request->name ? Str::slug($request->name) . '-' . time() : $product->slug,
                // 'category_id' => $request->category_id,
                'warranty_months' => $request->warranty_months ?? 12,
                'description' => $request->description,
                'content' => $request->content,
                'is_featured' => $request->has('is_featured') ? 1 : 0,
                'status' => $request->has('status') ? 'active' : 'inactive',
            ]);

            // Ghi nhật ký hoạt động cho cập nhật sản phẩm
            $this->logProductActivity($product, 'updated', 'Cập nhật thông tin sản phẩm', $oldValues, $product->toArray());

            // Xử lý thông số kỹ thuật - xóa cũ và tạo mới
            ProductSpecification::where('product_id', $product->id)->delete();
            if ($request->has('specifications')) {
                $specifications = $request->specifications;
                if (is_array($specifications)) {
                    foreach ($specifications as $spec) {
                        if (!empty($spec['value']) && !empty($spec['specification_id'])) {
                            ProductSpecification::create([
                                'product_id' => $product->id,
                                'specification_id' => $spec['specification_id'],
                                'value' => $spec['value'],
                            ]);
                        }
                    }
                }
            }

            // Xử lý biến thể một cách thông minh
            if ($request->has('variants')) {
                $variants = $request->variants;
                if (is_array($variants)) {
                    // Lấy ID biến thể từ yêu cầu
                    $requestedVariantIds = [];
                    $existingVariantIds = $product->variants()->whereNull('deleted_at')->pluck('id')->toArray();

                    foreach ($variants as $index => $variantData) {
                        if (!is_array($variantData)) {
                            continue;
                        }

                        // Nếu biến thể có ID, nó là biến thể hiện tại
                        if (!empty($variantData['id'])) {
                            $requestedVariantIds[] = $variantData['id'];
                            $this->updateExistingVariant($variantData, $request, $index);
                        } else {
                            // Đây là biến thể mới
                            $this->createNewVariant($product, $variantData, $request, $index);
                        }
                    }

                    // Xử lý biến thể để xóa từ yêu cầu
                    $variantsToDelete = [];
                    if ($request->has('variants_to_delete') && !empty($request->variants_to_delete)) {
                        $variantsToDeleteData = $request->variants_to_delete;
                        if (is_string($variantsToDeleteData)) {
                            $variantsToDelete = json_decode($variantsToDeleteData, true) ?: [];
                        } elseif (is_array($variantsToDeleteData)) {
                            $variantsToDelete = $variantsToDeleteData;
                        }
                    }

                    // Xóa mềm biến thể không còn trong yêu cầu
                    $variantsToDelete = array_merge($variantsToDelete, array_diff($existingVariantIds, $requestedVariantIds));

                    if (!empty($variantsToDelete)) {
                        foreach ($variantsToDelete as $variantId) {
                            $variant = ProductVariant::find($variantId);
                            if ($variant) {
                                // Nếu biến thể bị xóa là mặc định, tìm biến thể khác làm mặc định
                                if ($variant->is_default) {
                                    $newDefaultVariant = ProductVariant::where('product_id', $product->id)
                                        ->where('id', '!=', $variantId)
                                        ->whereNull('deleted_at')
                                        ->whereNotIn('id', $variantsToDelete)
                                        ->first();

                                    if ($newDefaultVariant) {
                                        $newDefaultVariant->update(['is_default' => 1]);
                                    }
                                }

                                $variant->combinations()->delete();
                                $variant->delete();
                            }
                        }
                    }
                }
            } else {
                // Nếu không có biến thể trong yêu cầu, xóa mềm tất cả biến thể hiện tại
                $existingVariantIds = $product->variants()->whereNull('deleted_at')->pluck('id')->toArray();
                if (!empty($existingVariantIds)) {
                    foreach ($existingVariantIds as $variantId) {
                        $variant = ProductVariant::find($variantId);
                        if ($variant) {
                            $variant->combinations()->delete();
                            $variant->delete();
                        }
                    }
                }
            }

            // Đảm bảo có ít nhất một biến thể mặc định
            $this->ensureDefaultVariant($product);

            DB::commit();
            $this->syncToAIEngine('update', $product);
            return redirect()->route('admin.products.index')->with('success', 'Sản phẩm đã được cập nhật thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
            Log::error('Chi tiết lỗi: ' . $e->getTraceAsString());
            return back()->withInput()->withErrors(['error' => 'Đã xảy ra lỗi: ' . $e->getMessage()]);
        }
    }

    private function updateExistingVariant($variantData, $request, $index)
    {
        $variant = ProductVariant::find($variantData['id']);
        if (!$variant) return;

        // 1. So sánh và chỉ cập nhật trường thay đổi
        $fields = ['name', 'stock', 'purchase_price', 'selling_price'];
        $needUpdate = false;
        foreach ($fields as $field) {
            if ($variant->$field != $variantData[$field]) {
                $variant->$field = $variantData[$field];
                $needUpdate = true;
            }
        }
        // Slug luôn cập nhật nếu name đổi
        if ($variant->name != $variantData['name']) {
            $variant->slug = Str::slug($variantData['name']);
            $needUpdate = true;
        }
        // is_default
        $isDefault = isset($variantData['is_default']) && $variantData['is_default'] ? 1 : 0;
        if ($variant->is_default != $isDefault) {
            $variant->is_default = $isDefault;
            $needUpdate = true;
        }
        if ($needUpdate) $variant->save();

        // 2. Chỉ cập nhật hình ảnh nếu có thay đổi
        $imagesToDelete = [];
        if ($request->has('images_to_delete') && !empty($request->images_to_delete)) {
            $imagesToDeleteData = $request->images_to_delete;
            if (is_string($imagesToDeleteData)) {
                $imagesToDelete = json_decode($imagesToDeleteData, true) ?: [];
            } elseif (is_array($imagesToDeleteData)) {
                $imagesToDelete = $imagesToDeleteData;
            }
        }
        $newImagePaths = [];
        if ($request->hasFile("variants.{$index}.images")) {
            $destinationPath = public_path('uploads/products');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0777, true);
            foreach ($request->file("variants.{$index}.images") as $image) {
                if ($image->isValid()) {
                    $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
                    $image->move($destinationPath, $filename);
                    $path = 'uploads/products/' . $filename;
                    $newImagePaths[] = $path;
                }
            }
        }
        $existingImages = is_string($variant->images) ? (json_decode($variant->images, true) ?: []) : ($variant->images ?: []);
        if (!empty($imagesToDelete) || !empty($newImagePaths) || $request->has('has_image_deletions')) {
            $updatedImages = array_filter($existingImages, function ($image) use ($imagesToDelete) {
                $normalizedImage = ltrim($image, '/');
                if (!str_starts_with($normalizedImage, 'uploads/')) $normalizedImage = 'uploads/' . $normalizedImage;
                foreach ($imagesToDelete as $deletePath) {
                    $normalizedDeletePath = ltrim($deletePath, '/');
                    if (!str_starts_with($normalizedDeletePath, 'uploads/')) $normalizedDeletePath = 'uploads/' . $normalizedDeletePath;
                    if ($normalizedImage === $normalizedDeletePath) return false;
                }
                return true;
            });
            $allImages = array_merge($updatedImages, $newImagePaths);
            $variant->images = json_encode($allImages);
            $variant->save();
        }

        // 3. Chỉ cập nhật tổ hợp nếu có thay đổi
        if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
            // Lấy tổ hợp cũ
            $oldValueIds = $variant->combinations()->pluck('attribute_value_id')->sort()->values()->toArray();
            $newValueIds = [];
            foreach ($variantData['attributes'] as $attr) {
                if (!empty($attr['selected_values'])) {
                    $vals = is_array($attr['selected_values']) ? $attr['selected_values'] : [$attr['selected_values']];
                    foreach ($vals as $v) $newValueIds[] = (int)$v;
                }
            }
            sort($newValueIds);
            if ($oldValueIds !== $newValueIds) {
                VariantCombination::where('variant_id', $variant->id)->delete();
                foreach ($newValueIds as $valueId) {
                    VariantCombination::create([
                        'variant_id' => $variant->id,
                        'attribute_value_id' => $valueId,
                    ]);
                }
            }
        }
    }

    private function createNewVariant($product, $variantData, $request, $index)
    {
        $variantName = $variantData['name'];
        $variantSlug = Str::slug($variantName);
        $sku = $this->generateUniqueSku($product->id);

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'name' => $variantName,
            'slug' => $variantSlug,
            'sku' => $sku,
            'stock' => $variantData['stock'] ?? 0,
            'purchase_price' => $variantData['purchase_price'] ?? 0,
            'selling_price' => $variantData['selling_price'] ?? 0,
            'is_default' => isset($variantData['is_default']) && $variantData['is_default'] ? 1 : 0,
        ]);

        // Xử lý tải lên hình ảnh cho biến thể mới
        if ($request->hasFile("variants.{$index}.images")) {
            $imagePaths = [];
            $destinationPath = public_path('uploads/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            foreach ($request->file("variants.{$index}.images") as $image) {
                if ($image->isValid()) {
                    $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
                    $image->move($destinationPath, $filename);
                    $path = 'uploads/products/' . $filename;
                    $imagePaths[] = $path;
                }
            }

            if (!empty($imagePaths)) {
                $variant->images = json_encode($imagePaths);
                $variant->save();
            }
        }

        // Tạo các tổ hợp cho biến thể mới
        if (isset($variantData['attributes']) && is_array($variantData['attributes'])) {
            foreach ($variantData['attributes'] as $attr) {
                if (!empty($attr['attribute_type_id']) && !empty($attr['selected_values'])) {
                    $selectedValues = $attr['selected_values'];
                    // Đảm bảo selected_values luôn là array
                    if (!is_array($selectedValues)) {
                        $selectedValues = [$selectedValues];
                    }
                    foreach ($selectedValues as $valueId) {
                        VariantCombination::create([
                            'variant_id' => $variant->id,
                            'attribute_value_id' => $valueId,
                        ]);
                    }
                }
            }
        }
    }

    public function edit(Product $product)
    {
        $categories = Category::where('type', 1)->where('status', 'active')->get();
        $categoryId = $product->category_id;

        // Lấy thông số kỹ thuật thuộc danh mục sản phẩm
        $specifications = Specification::where('status', 'active')
            ->whereJsonContains('category_ids', (string)$categoryId)
            ->get();

        // Lấy giá trị thông số kỹ thuật sản phẩm (theo specification_id)
        $productSpecs = ProductSpecification::where('product_id', $product->id)
            ->get()
            ->keyBy('specification_id');

        // Chuẩn bị mảng dữ liệu cho view
        $specificationsData = $specifications->map(function ($spec) use ($productSpecs) {
            $value = '';
            if (isset($productSpecs[$spec->id])) {
                $specValue = $productSpecs[$spec->id]->value;
                $value = is_array($specValue) ? implode(', ', $specValue) : $specValue;
            }

            return [
                'id' => $spec->id,
                'name' => is_array($spec->name) ? implode(', ', $spec->name) : $spec->name,
                'value' => $value
            ];
        });

        // Lấy thuộc tính biến thể theo danh mục
        $attributeTypes = VariantAttributeType::where('status', 'active')
            ->whereJsonContains('category_ids', (string)$categoryId)
            ->with(['attributeValues' => function ($query) {
                $query->where('status', 'active');
            }])
            ->get();

        // Tải chỉ các biến thể hoạt động (không bị xóa mềm) với tổ hợp và hình ảnh
        $product->load(['variants' => function ($query) {
            $query->whereNull('deleted_at')
                ->with(['combinations.attributeValue.attributeType']);
        }]);

        // Chuẩn bị giá trị thuộc tính cho form
        $attributeValues = [];
        if ($product->variants->isNotEmpty()) {
            $attributeMap = [];
            foreach ($product->variants as $variant) {
                foreach ($variant->combinations as $comb) {
                    $typeId = $comb->attributeValue->attribute_type_id;
                    $valueId = $comb->attribute_value_id;
                    if (!isset($attributeMap[$typeId])) {
                        $attributeMap[$typeId] = [
                            'attribute_type_id' => $typeId,
                            'selected_values' => []
                        ];
                    }
                    if (!in_array($valueId, $attributeMap[$typeId]['selected_values'])) {
                        $attributeMap[$typeId]['selected_values'][] = $valueId;
                    }
                }
            }
            ksort($attributeMap);
            $attributeValues = array_values($attributeMap);
        }

        // Chuẩn bị biến thể với thuộc tính cho Blade
        $variants = $product->variants->whereNull('deleted_at')->map(function ($variant) {
            // Chuyển tổ hợp thành thuộc tính
            $attributes = [];
            foreach ($variant->combinations as $comb) {
                $typeId = $comb->attributeValue->attribute_type_id;
                $valueId = $comb->attribute_value_id;
                $found = false;
                foreach ($attributes as &$attr) {
                    if ($attr['attribute_type_id'] == $typeId) {
                        $attr['selected_values'][] = $valueId;
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    $attributes[] = [
                        'attribute_type_id' => $typeId,
                        'selected_values' => [$valueId]
                    ];
                }
            }
            $arr = $variant->toArray();
            $arr['attributes'] = $attributes;
            return $arr;
        })->values()->toArray();

        // Ghi log dữ liệu đã chuẩn bị để debug
        Log::info('Dữ liệu chỉnh sửa sản phẩm:', [
            'product_id' => $product->id,
            'specifications' => $specificationsData->toArray(),
            'attribute_values' => $attributeValues,
            'attribute_types' => $attributeTypes->toArray(),
            'variants' => $variants
        ]);

        return view('admin.products.edit', compact(
            'product',
            'categories',
            'attributeTypes',
            'specificationsData',
            'attributeValues',
            'variants'
        ));
    }

    public function getAttributeValues($attributeTypeId)
    {
        $values = \App\Models\VariantAttributeValue::where('attribute_type_id', $attributeTypeId)
            ->where('status', 'active')
            ->get()
            ->map(function ($value) {
                // Đảm bảo trả về đúng cấu trúc mà view đang mong đợi
                $displayValue = $value->value;
                if (is_string($displayValue) && json_decode($displayValue) !== null) {
                    $displayValue = json_decode($displayValue, true);
                }
                if (is_array($displayValue)) {
                    $displayValue = implode(', ', $displayValue);
                }

                $hexValue = $value->hex;
                if (is_string($hexValue) && json_decode($hexValue) !== null) {
                    $hexValue = json_decode($hexValue, true);
                }
                if (is_array($hexValue) && !empty($hexValue)) {
                    $hexValue = implode(', ', $hexValue);
                }

                return [
                    'id' => $value->id,
                    'value' => $displayValue,
                    'hex' => $hexValue,
                    'attribute_type_id' => $value->attribute_type_id
                ];
            });

        Log::info('Giá trị thuộc tính đang được trả về:', [
            'attribute_type_id' => $attributeTypeId,
            'values' => $values->toArray()
        ]);

        return response()->json($values);
    }

    private function generateUniqueSku($productId)
    {
        do {
            $random = str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            $sku = 'SP-' . $random;
        } while (ProductVariant::where('sku', $sku)->exists());

        return $sku;
    }

    private function generateCombinations($arrays)
    {
        if (empty($arrays)) {
            return [];
        }

        // Bắt đầu với mảng đầu tiên
        $result = array_map(function ($item) {
            return [$item];
        }, $arrays[0]);

        // Lặp qua các mảng còn lại để tạo tổ hợp
        for ($i = 1; $i < count($arrays); $i++) {
            $temp = [];
            foreach ($result as $existing) {
                foreach ($arrays[$i] as $item) {
                    $temp[] = array_merge($existing, [$item]);
                }
            }
            $result = $temp;
        }

        return $result;
    }

    private function createVariantsFromAttributes($product, $attributes, $request)
    {
        try {
            Log::info('Bắt đầu tạo biến thể với dữ liệu:', [
                'product_id' => $product->id,
                'attributes' => $attributes
            ]);

            if (!is_array($attributes)) {
                throw new \Exception('Thuộc tính phải là một mảng');
            }

            $attributeValueIds = [];
            foreach ($attributes as $attribute) {
                if (!isset($attribute['attribute_type_id']) || empty($attribute['selected_values'])) {
                    continue;
                }

                // Đảm bảo selected_values luôn là một mảng
                $selectedValues = $attribute['selected_values'];
                if (!is_array($selectedValues)) {
                    if (is_string($selectedValues) && strpos($selectedValues, ',') !== false) {
                        $selectedValues = array_map('trim', explode(',', $selectedValues));
                    } else {
                        $selectedValues = [$selectedValues];
                    }
                }

                $attrValues = VariantAttributeValue::select('id', 'value', 'attribute_type_id')
                    ->whereIn('id', $selectedValues)
                    ->where('attribute_type_id', $attribute['attribute_type_id'])
                    ->get();

                if ($attrValues->isNotEmpty()) {
                    $values = [];
                    foreach ($attrValues as $value) {
                        // Giải mã giá trị JSON nếu nó là chuỗi JSON
                        $displayValue = $value->value;
                        if (is_string($displayValue)) {
                            $decoded = json_decode($displayValue, true);
                            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                                $displayValue = $decoded;
                            }
                        }

                        // Chuyển mảng thành chuỗi nếu cần
                        if (is_array($displayValue)) {
                            $displayValue = implode(', ', $displayValue);
                        }

                        $values[] = [
                            'id' => $value->id,
                            'attribute_type_id' => $value->attribute_type_id,
                            'display_value' => $displayValue
                        ];
                    }
                    if (!empty($values)) {
                        $attributeValueIds[] = $values;
                    }
                }
            }

            $combinations = $this->generateCombinations($attributeValueIds);

            Log::info('Đã tạo các tổ hợp:', ['combinations' => $combinations]);

            foreach ($combinations as $index => $combination) {
                $variantAttributeValues = [];
                $attributeIds = [];

                foreach ($combination as $attrValue) {
                    if (isset($attrValue['display_value'])) {
                        $variantAttributeValues[] = $attrValue['display_value'];
                    }
                    if (isset($attrValue['id'])) {
                        $attributeIds[] = $attrValue['id'];
                    }
                }

                $variantName = $product->name;
                if (!empty($variantAttributeValues)) {
                    $variantName .= ' - ' . implode(' - ', $variantAttributeValues);
                }

                $slug = Str::slug($variantName) . '-' . time() . '-' . $index;
                $sku = $this->generateUniqueSku($product->id);

                $imagePaths = [];
                if (isset($request->variants[$index]) && $request->hasFile("variants.{$index}.images")) {
                    $destinationPath = public_path('uploads/products');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0777, true);
                    }
                    foreach ($request->file("variants.{$index}.images") as $image) {
                        if ($image->isValid()) {
                            $filename = time() . '_' . $index . '_' . $image->getClientOriginalName();
                            $image->move($destinationPath, $filename);
                            $path = 'uploads/products/' . $filename;
                            $imagePaths[] = $path;
                        }
                    }
                }

                $variantData = $request->variants[$index] ?? [];

                $productVariant = ProductVariant::create([
                    'product_id' => $product->id,
                    'name' => $variantName,
                    'slug' => $slug,
                    'sku' => $sku,
                    'stock' => $variantData['stock'] ?? 0,
                    'purchase_price' => $variantData['purchase_price'] ?? 0,
                    'selling_price' => $variantData['selling_price'] ?? 0,
                    'images' => json_encode($imagePaths),
                    'is_default' => isset($variantData['is_default']) && $variantData['is_default'] ? 1 : ($index === 0 ? 1 : 0),
                ]);

                foreach ($attributeIds as $attributeId) {
                    VariantCombination::create([
                        'variant_id' => $productVariant->id,
                        'attribute_value_id' => $attributeId
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi trong createVariantsFromAttributes: ' . $e->getMessage());
            Log::error('Chi tiết lỗi đầy đủ:', [
                'attributes' => $attributes,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function ensureDefaultVariant($product)
    {
        $hasDefault = ProductVariant::where('product_id', $product->id)
            ->where('is_default', 1)
            ->exists();

        if (!$hasDefault) {
            $firstVariant = ProductVariant::where('product_id', $product->id)->first();
            if ($firstVariant) {
                $firstVariant->update(['is_default' => 1]);
            }
        }
    }

    function syncToAIEngine($action, $data)
    {
        $url = "http://localhost:5000/api/v1/webhook/sync-data";
        $api_key = "sk-live-f5e5cc0e6bf28becf7c88815577fe7e8ab1332111854dac0";

        $external_id = !empty($data['slug']) ? $data['slug'] : (string)$data['id'];

        $product_url = ""; 
        $payload = json_encode([
            "action" => $action,
            "external_id" => $external_id,
            "title" => $data['name'],
            "content" => strip_tags($data['description']),
            "url" => $product_url
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "x-api-key: " . $api_key
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
    }


    public function trash()
    {
        $products = Product::onlyTrashed()
            ->with([
                'category',
                'variants' => function ($query) {
                    $query->withTrashed(); // Load cả variants đã bị xóa mềm
                },
                'defaultVariant' => function ($query) {
                    $query->withTrashed(); // Load default variant đã bị xóa mềm
                }
            ])
            ->latest()
            ->paginate(10);

        return view('admin.products.trash', compact('products'));
    }

    public function restore($id)
    {
        try {
            DB::beginTransaction();

            // Tìm sản phẩm đã bị xóa mềm
            $product = Product::onlyTrashed()->findOrFail($id);

            // Lấy tất cả biến thể đã bị xóa mềm của sản phẩm này
            $variantIds = ProductVariant::onlyTrashed()
                ->where('product_id', $id)
                ->pluck('id')
                ->toArray();

            if (!empty($variantIds)) {
                // Khôi phục tất cả biến thể đã bị xóa mềm
                ProductVariant::onlyTrashed()
                    ->whereIn('id', $variantIds)
                    ->restore();

                // Khôi phục tất cả tổ hợp biến thể đã bị xóa mềm
                VariantCombination::onlyTrashed()
                    ->whereIn('variant_id', $variantIds)
                    ->restore();
            }

            // Khôi phục sản phẩm
            $product->restore();

            DB::commit();
            $this->syncToAIEngine('sync', $product);

            return redirect()->route('admin.products.trash')
                ->with('success', 'Sản phẩm đã được khôi phục thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi khôi phục sản phẩm: ' . $e->getMessage());
            Log::error('Chi tiết lỗi: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Đã xảy ra lỗi khi khôi phục sản phẩm.']);
        }
    }

    public function destroy(Product $product)
    {
        try {
            DB::beginTransaction();

            // Lấy tất cả ID biến thể trước khi xóa mềm
            $variantIds = $product->variants()->pluck('id')->toArray();

            // Xóa mềm tất cả biến thể
            if (!empty($variantIds)) {
                ProductVariant::whereIn('id', $variantIds)->delete();

                // Xóa mềm tất cả tổ hợp biến thể
                VariantCombination::whereIn('variant_id', $variantIds)->delete();
            }

            // Xóa mềm sản phẩm
            $product->delete();

            DB::commit();
            $this->syncToAIEngine('delete', $product);
            return redirect()->route('admin.products.index')
                ->with('success', 'Sản phẩm đã được chuyển vào thùng rác thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Lỗi khi xóa sản phẩm: ' . $e->getMessage());
            Log::error('Chi tiết lỗi: ' . $e->getTraceAsString());
            return back()->withErrors(['error' => 'Đã xảy ra lỗi khi xóa sản phẩm.']);
        }
    }

    public function show(Product $product)
    {
        $product->load(['category', 'variants', 'specifications.specification']);

        // F. Đánh giá và đánh giá
        $reviews = $product->reviews()
            ->with(['user', 'variant'])
            ->whereNull('deleted_at')
            ->latest()
            ->get();

        $averageRating = $reviews->avg('rating') ?? 0;
        $ratingStats = [];
        for ($i = 1; $i <= 5; $i++) {
            $ratingStats[$i] = $reviews->where('rating', $i)->count();
        }

        $topReviews = $reviews->take(3);

        // G. Biểu đồ dữ liệu
        $monthlySales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->where('order_items.product_id', $product->id)
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->whereYear('orders.created_at', now()->year)
            ->selectRaw('
                MONTH(orders.created_at) as month, 
                SUM(order_items.quantity * order_items.price) as total_sales, 
                SUM(order_items.quantity) as total_quantity,
                AVG(order_items.price) as avg_price,
                COUNT(DISTINCT order_items.product_variant_id) as variant_count
            ')
            ->groupBy('month')
            ->get()
            ->keyBy('month')
            ->toArray();

        // Thêm chi tiết doanh số theo tháng để debug
        $monthlySalesDetail = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('product_variants', 'order_items.product_variant_id', '=', 'product_variants.id')
            ->where('order_items.product_id', $product->id)
            ->whereIn('orders.status', ['completed', 'delivered'])
            ->whereYear('orders.created_at', now()->year)
            ->selectRaw('
                MONTH(orders.created_at) as month,
                product_variants.name as variant_name,
                order_items.price as unit_price,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.quantity * order_items.price) as total_sales
            ')
            ->groupBy('month', 'product_variants.name', 'order_items.price')
            ->orderBy('month')
            ->orderBy('total_sales', 'desc')
            ->get();

        $dailyViews = DB::table('product_views')
            ->where('product_id', $product->id)
            ->where('viewed_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(viewed_at) as date, COUNT(*) as views')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // H. Nhật ký hoạt động - Sử dụng ProductActivity thực tế
        $activityLogs = $product->activities()
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'type' => $activity->action,
                    'action' => $this->getActionTitle($activity->action),
                    'description' => $activity->formatted_description,
                    'timestamp' => $activity->created_at->format('d/m/Y H:i'),
                    'user' => $activity->user ? $activity->user->name : 'System'
                ];
            });

        // Nếu chưa có nhật ký hoạt động, tạo dữ liệu giả
        if ($activityLogs->isEmpty()) {
            $activityLogs = collect();

            // Thêm hoạt động tạo mới
            if ($product->created_at) {
                $activityLogs->push([
                    'type' => 'created',
                    'action' => 'Tạo sản phẩm',
                    'description' => 'Sản phẩm được tạo bởi Admin',
                    'timestamp' => $product->created_at->format('d/m/Y H:i'),
                    'user' => 'Admin'
                ]);
            }

            // Thêm hoạt động cập nhật nếu sản phẩm được cập nhật   
            if ($product->updated_at && $product->updated_at->ne($product->created_at)) {
                $activityLogs->push([
                    'type' => 'updated',
                    'action' => 'Cập nhật sản phẩm',
                    'description' => 'Thông tin sản phẩm được cập nhật',
                    'timestamp' => $product->updated_at->format('d/m/Y H:i'),
                    'user' => 'Admin'
                ]);
            }

            // Thêm hoạt động biến thể
            foreach ($product->variants as $variant) {
                if ($variant->updated_at && $variant->updated_at->ne($variant->created_at)) {
                    $activityLogs->push([
                        'type' => 'variant_updated',
                        'action' => 'Cập nhật biến thể',
                        'description' => "Biến thể '{$variant->name}' được cập nhật",
                        'timestamp' => $variant->updated_at->format('d/m/Y H:i'),
                        'user' => 'Admin'
                    ]);
                }
            }
        }

        return view('admin.products.show', compact(
            'product',
            'reviews',
            'averageRating',
            'ratingStats',
            'topReviews',
            'monthlySales',
            'monthlySalesDetail',
            'dailyViews',
            'activityLogs'
        ));
    }

    private function getActionTitle($action)
    {
        $titles = [
            'created' => 'Tạo sản phẩm',
            'updated' => 'Cập nhật sản phẩm',
            'deleted' => 'Xóa sản phẩm',
            'variant_updated' => 'Cập nhật biến thể',
            'variant_created' => 'Tạo biến thể',
            'variant_deleted' => 'Xóa biến thể',
            'price_updated' => 'Cập nhật giá',
            'stock_updated' => 'Cập nhật tồn kho',
            'status_updated' => 'Cập nhật trạng thái'
        ];

        return $titles[$action] ?? ucfirst(str_replace('_', ' ', $action));
    }

    private function logProductActivity($product, $action, $description, $oldValues = null, $newValues = null)
    {
        try {
            \App\Models\ProductActivity::create([
                'product_id' => $product->id,
                'user_id' => Auth::check() ? Auth::id() : null,
                'action' => $action,
                'description' => $description,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Exception $e) {
            Log::error('Không thể ghi log hoạt động sản phẩm: ' . $e->getMessage());
        }
    }

    public function checkDuplicateVariants(\Illuminate\Http\Request $request)
    {
        $productId = $request->input('product_id');
        $variantCombinations = $request->input('variant_combinations', []);

        $duplicates = [];

        // Lấy tất cả giá trị id liên quan để map sang tên
        $allValueIds = collect($variantCombinations)->flatten()->unique()->toArray();
        $valueMap = VariantAttributeValue::withTrashed()->whereIn('id', $allValueIds)->get()->keyBy('id');

        foreach ($variantCombinations as $index => $combination) {
            $valueIds = array_map('intval', $combination);
            sort($valueIds);

            // Chỉ kiểm tra trong phạm vi sản phẩm hiện tại
            $query = \App\Models\ProductVariant::withTrashed()
                ->where('product_id', $productId)
                ->whereHas('combinations', function ($q) use ($valueIds) {
                    $q->whereIn('attribute_value_id', $valueIds);
                }, '=', count($valueIds))
                ->whereHas('combinations', function ($q) use ($valueIds) {
                    $q->whereNotIn('attribute_value_id', $valueIds);
                }, '=', 0);

            $existingVariant = $query->first();

            if ($existingVariant) {
                // Lấy tên giá trị
                $valueNames = collect($valueIds)->map(function ($id) use ($valueMap) {
                    $v = $valueMap[$id] ?? null;
                    if (!$v) return $id;
                    if (is_array($v->value)) return implode(', ', $v->value);
                    return $v->value;
                })->toArray();
                $duplicates[] = [
                    'index' => $index,
                    'combination' => $valueNames,
                    'variant_name' => $existingVariant->name,
                    'is_soft_deleted' => $existingVariant->trashed(),
                ];
            }
        }

        return response()->json(['duplicates' => $duplicates]);
    }
}
