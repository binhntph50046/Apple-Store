<?php

namespace App\Http\Controllers\client;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\AttributeValue;
use App\Models\AttributeType;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ShopController
{
    public function index(Request $request)
    {
        // Get categories for the top menu, ordered by the 'order' column from admin
        $topCategories = Category::where('type', 1)->where('status', 1)->orderBy('order', 'asc')->get();

        // Get all products with pagination (9 products per page)
        $query = Product::with(['variants' => function ($query) {
            $query->with(['combinations.attributeValue.attributeType']);
        }, 'reviews', 'category'])
            ->where('status', 1);

        // Apply filters
        $query = $this->applyFilters($query, $request);

        // Get products with pagination
        $products = $query->orderBy('created_at', 'desc')->paginate(9);

        // Get filter data for the view
        $filterData = $this->getFilterDataForView();

        // Flash Sale
        $flashSaleItems = $this->getActiveFlashSaleItems();
        $flashSaleTimeRange = $this->getFlashSaleTimeRange();

        // Get applied filters for display
        $appliedFilters = $this->getAppliedFilters($request);

        return view('client.shop.index', compact(
            'products',
            'flashSaleItems',
            'flashSaleTimeRange',
            'topCategories',
            'filterData',
            'appliedFilters'
        ));
    }

    protected function applyFilters($query, Request $request)
    {
        // Apply category filter by slug(s)
        if ($request->filled('category_slug') && is_array($request->category_slug)) {
            $categoryIds = Category::whereIn('slug', $request->category_slug)->pluck('id');
            if ($categoryIds->isNotEmpty()) {
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Apply price filters
        if ($request->filled('min_price')) {
            $min = (float) $request->min_price;
            $query->whereHas('variants', function ($q) use ($min) {
                $q->where('selling_price', '>=', $min);
            });
        }

        if ($request->filled('max_price')) {
            $max = (float) $request->max_price;
            $query->whereHas('variants', function ($q) use ($max) {
                $q->where('selling_price', '<=', $max);
            });
        }

        // Apply dynamic attribute filters via combinations -> attributeValue
        if ($request->has('filters')) {
            foreach ($request->filters as $attributeTypeId => $values) {
                if (!empty($values)) {
                    $vals = array_values(array_filter((array) $values));
                    if (count($vals) === 0) continue;

                    $query->whereHas('variants.combinations', function ($comboQ) use ($attributeTypeId, $vals) {
                        $comboQ->whereHas('attributeValue', function ($attrQ) use ($attributeTypeId, $vals) {
                            $attrQ->where('attribute_type_id', $attributeTypeId)
                                ->where(function ($sub) use ($vals) {
                                    foreach ($vals as $val) {
                                        $sub->orWhereJsonContains('value', $val);
                                    }
                                });
                        });
                    });
                }
            }
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_low':
                    $query->whereHas('variants', function ($q) {
                        $q->orderBy('selling_price', 'asc');
                    });
                    break;
                case 'price_high':
                    $query->whereHas('variants', function ($q) {
                        $q->orderBy('selling_price', 'desc');
                    });
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    protected function getAppliedFilters(Request $request)
    {
        return [
            'category_slug' => (array) $request->get('category_slug', []),
            'min_price' => $request->get('min_price'),
            'max_price' => $request->get('max_price'),
            'filters' => (array) $request->get('filters', []),
            'sort' => $request->get('sort', 'newest'),
        ];
    }

    public function showCategory(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)
            ->where('type', 1)
            ->where('status', 1)
            ->firstOrFail();

        // Base query: only products within this category
        $query = Product::with(['variants' => function ($query) {
            $query->with(['combinations.attributeValue.attributeType']);
        }, 'reviews'])
            ->where('status', 1)
            ->where('category_id', $category->id);

        // Apply price, attribute, and sorting filters (no category filter here)
        $query = $this->applyFiltersForCategory($query, $request);

        // Paginate results
        $products = $query->paginate(9);

        // Build filter data limited to this category
        $filterData = $this->getFilterDataForCategory($category);

        // Get applied filters for keeping UI state
        $appliedFilters = $this->getAppliedFilters($request);

        return view('client.shop.category', compact('category', 'products', 'filterData', 'appliedFilters'));
    }

    /**
     * Apply price, dynamic attribute, and sorting filters to the category query
     */
    protected function applyFiltersForCategory($query, Request $request)
    {
        // Apply price filters
        if ($request->filled('min_price')) {
            $min = (float) $request->min_price;
            $query->whereHas('variants', function ($q) use ($min) {
                $q->where('selling_price', '>=', $min);
            });
        }

        if ($request->filled('max_price')) {
            $max = (float) $request->max_price;
            $query->whereHas('variants', function ($q) use ($max) {
                $q->where('selling_price', '<=', $max);
            });
        }

        // Apply dynamic attribute filters via combinations -> attributeValue
        if ($request->has('filters')) {
            foreach ($request->filters as $attributeTypeId => $values) {
                if (!empty($values)) {
                    $vals = array_values(array_filter((array) $values));
                    if (count($vals) === 0) continue;

                    $query->whereHas('variants.combinations', function ($comboQ) use ($attributeTypeId, $vals) {
                        $comboQ->whereHas('attributeValue', function ($attrQ) use ($attributeTypeId, $vals) {
                            $attrQ->where('attribute_type_id', $attributeTypeId)
                                ->where(function ($sub) use ($vals) {
                                    foreach ($vals as $val) {
                                        $sub->orWhereJsonContains('value', $val);
                                    }
                                });
                        });
                    });
                }
            }
        }

        // Apply sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'price_low':
                    $query->whereHas('variants', function ($q) {
                        $q->orderBy('selling_price', 'asc');
                    });
                    break;
                case 'price_high':
                    $query->whereHas('variants', function ($q) {
                        $q->orderBy('selling_price', 'desc');
                    });
                    break;
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;
                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'popular':
                    $query->orderBy('views', 'desc');
                    break;
                case 'rating':
                    $query->orderBy('rating', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query;
    }

    /**
     * Build filter data (dynamic attributes and price range) limited to a category
     */
    protected function getFilterDataForCategory(Category $category)
    {
        try {
            // Discover dynamic attribute values actually used by variants in this category
            $attributeRows = DB::table('variant_attribute_values as vav')
                ->join('variant_attribute_types as vat', 'vav.attribute_type_id', '=', 'vat.id')
                ->join('variant_combinations as vc', 'vc.attribute_value_id', '=', 'vav.id')
                ->join('product_variants as pv', 'pv.id', '=', 'vc.variant_id')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->where('p.category_id', $category->id)
                ->where('p.status', 1)
                ->where('vat.status', 'active')
                ->whereNull('vav.deleted_at')
                ->whereNull('vat.deleted_at')
                ->select('vat.id as type_id', 'vat.name as type_name', 'vat.type as type_key', 'vav.value', 'vav.hex')
                ->distinct()
                ->get();

            $grouped = [];
            foreach ($attributeRows as $row) {
                $valueArray = @json_decode($row->value, true) ?: [];
                $hexArray = @json_decode($row->hex, true) ?: [];
                $valueName = $valueArray[0] ?? null;
                if (!$valueName) continue;

                $filterType = $this->determineFilterType($row->type_name, $valueName, $hexArray);

                if (!isset($grouped[$row->type_id])) {
                    $grouped[$row->type_id] = [
                        'name' => $row->type_name,
                        'type' => $filterType,
                        'values' => [],
                        'hexValues' => [],
                    ];
                }

                // Add value ensuring uniqueness
                if (!in_array($valueName, $grouped[$row->type_id]['values'], true)) {
                    if ($filterType === 'color') {
                        // Only keep when we have a valid hex
                        $hex = $hexArray[0] ?? null;
                        if ($hex) {
                            $grouped[$row->type_id]['values'][] = $valueName;
                            $grouped[$row->type_id]['hexValues'][$valueName] = $hex;
                        }
                    } else {
                        $grouped[$row->type_id]['values'][] = $valueName;
                    }
                }
            }

            // Finalize dynamic filters with counts
            $dynamicFilters = [];
            foreach ($grouped as $typeId => $data) {
                if (count($data['values']) > 0) {
                    $dynamicFilters[$typeId] = [
                        'name' => $data['name'],
                        'type' => $data['type'],
                        'values' => collect($data['values']),
                        'hexValues' => $data['hexValues'],
                        'count' => count($data['values']),
                    ];
                }
            }

            // Price range limited to this category
            $priceRange = DB::table('product_variants as pv')
                ->join('products as p', 'p.id', '=', 'pv.product_id')
                ->where('p.category_id', $category->id)
                ->where('p.status', 1)
                ->selectRaw('MIN(pv.selling_price) as min_price, MAX(pv.selling_price) as max_price')
                ->first();

            return [
                'dynamicFilters' => $dynamicFilters,
                'priceRange' => $priceRange,
            ];
        } catch (\Exception $e) {
            return [
                'dynamicFilters' => [],
                'priceRange' => (object) ['min_price' => 0, 'max_price' => 10000000],
            ];
        }
    }

    protected function getActiveFlashSaleItems()
    {
        $now = Carbon::now();

        $flashSaleItems = DB::table('flash_sale_items')
            ->join('flash_sales', 'flash_sale_items.flash_sale_id', '=', 'flash_sales.id')
            ->join('product_variants', 'flash_sale_items.product_variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('flash_sales.status', 1)
            ->where('flash_sales.start_time', '<=', $now)
            ->where('flash_sales.end_time', '>=', $now)
            ->select(
                'flash_sale_items.*',
                'product_variants.name as variant_name',
                'product_variants.selling_price',
                'product_variants.slug as variant_slug',
                'product_variants.images as variant_images',
                'products.name as product_name',
                'products.slug as product_slug'
            )
            ->get();

        $result = [];
        foreach ($flashSaleItems as $item) {
            $firstImage = null;

            // Xử lý variant_images
            if (!empty($item->variant_images)) {
                try {
                    // Nếu là array, sử dụng trực tiếp
                    if (is_array($item->variant_images)) {
                        $images = $item->variant_images;
                    }
                    // Nếu là string, thử decode
                    else if (is_string($item->variant_images)) {
                        $images = json_decode($item->variant_images, true);
                        // Nếu decode ra string, thử decode lần nữa
                        if (is_string($images)) {
                            $images = json_decode($images, true);
                        }
                    }

                    // Lấy ảnh đầu tiên nếu có
                    if (is_array($images) && !empty($images[0])) {
                        $firstImage = $images[0];
                    }
                } catch (\Exception $e) {
                    // Log lỗi nếu cần
                    Log::error('Error processing variant images: ' . $e->getMessage());
                }
            }

            $result[] = (object) [
                'variant_name' => $item->variant_name,
                'selling_price' => $item->selling_price,
                'discount' => $item->discount,
                'discount_type' => $item->discount_type,
                'product_slug' => $item->product_slug,
                'first_image' => $firstImage,
                'count' => $item->count ?? 0,
            ];
        }

        return collect($result);
    }

    protected function getFlashSaleTimeRange()
    {
        $now = Carbon::now();

        $flashSale = DB::table('flash_sales')
            ->where('status', 1)
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->select('name', 'start_time', 'end_time') // Thêm 'name'
            ->first();

        if ($flashSale) {
            return [
                'name' => $flashSale->name, // Thêm dòng này
                'start_time' => Carbon::parse($flashSale->start_time),
                'end_time' => Carbon::parse($flashSale->end_time)
            ];
        }

        return null;
    }

    protected function getFilterDataForView()
    {
        try {
            // Get all active categories with product count
            $categories = Category::where('status', 'active')
                ->withCount(['products' => function ($query) {
                    $query->where('status', 'active');
                }])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->get();

            // DYNAMIC FILTER DISCOVERY - Tự động phát hiện tất cả thuộc tính
            $allAttributeTypes = DB::table('variant_attribute_types as vat')
                ->where('vat.status', 'active')
                ->whereNull('vat.deleted_at')
                ->select('vat.id', 'vat.name', 'vat.type')
                ->get();

            $dynamicFilters = [];

            foreach ($allAttributeTypes as $attrType) {
                // Lấy tất cả giá trị của thuộc tính này
                $attributeValues = DB::table('variant_attribute_values as vav')
                    ->where('vav.attribute_type_id', $attrType->id)
                    ->where('vav.status', 'active')
                    ->whereNull('vav.deleted_at')
                    ->select('vav.value', 'vav.hex')
                    ->distinct()
                    ->get();

                $values = collect();
                $hexValues = [];

                foreach ($attributeValues as $item) {
                    try {
                        $value = json_decode($item->value, true);
                        $hex = json_decode($item->hex, true);

                        if (is_array($value) && !empty($value[0])) {
                            $valueName = $value[0];

                            // Phân loại thuộc tính dựa trên tên và dữ liệu
                            $filterType = $this->determineFilterType($attrType->name, $valueName, $hex);

                            if ($filterType === 'color') {
                                // Chỉ thêm màu sắc khi có hex thực sự
                                if (is_array($hex) && !empty($hex[0]) && $hex[0] !== '') {
                                    $values->push($valueName);
                                    $hexValues[$valueName] = $hex[0];
                                }
                            } elseif ($filterType === 'storage') {
                                // Thêm dung lượng
                                $values->push($valueName);
                            } elseif ($filterType === 'size') {
                                // Thêm kích thước
                                $values->push($valueName);
                            } elseif ($filterType === 'text') {
                                // Thêm thuộc tính text khác
                                $values->push($valueName);
                            }
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }

                // Chỉ thêm vào filter khi có giá trị
                if ($values->count() > 0) {
                    $dynamicFilters[$attrType->id] = [
                        'name' => $attrType->name,
                        'type' => $this->determineFilterType($attrType->name, '', []),
                        'values' => $values,
                        'hexValues' => $hexValues,
                        'count' => $values->count()
                    ];
                }
            }

            // Get price range
            $priceRange = DB::table('product_variants')
                ->where('status', 'active')
                ->selectRaw('MIN(selling_price) as min_price, MAX(selling_price) as max_price')
                ->first();

            return [
                'categories' => $categories,
                'dynamicFilters' => $dynamicFilters,
                'priceRange' => $priceRange,
            ];
        } catch (\Exception $e) {
            // Return default data if there's an error
            return [
                'categories' => collect(),
                'dynamicFilters' => [],
                'priceRange' => (object) ['min_price' => 0, 'max_price' => 10000000],
            ];
        }
    }

    /**
     * Tự động xác định loại filter dựa trên tên thuộc tính và giá trị
     */
    protected function determineFilterType($attributeName, $value, $hex)
    {
        $attributeName = strtolower($attributeName);
        $value = strtolower($value);

        // Kiểm tra theo tên thuộc tính
        if (strpos($attributeName, 'màu') !== false || strpos($attributeName, 'color') !== false) {
            return 'color';
        }

        if (strpos($attributeName, 'dung lượng') !== false || strpos($attributeName, 'storage') !== false || strpos($attributeName, 'capacity') !== false) {
            return 'storage';
        }

        if (strpos($attributeName, 'kích thước') !== false || strpos($attributeName, 'size') !== false) {
            return 'size';
        }

        // Kiểm tra theo giá trị nếu không xác định được từ tên
        if (preg_match('/^\d+(gb|tb|mb)$/i', $value)) {
            return 'storage';
        }

        if (preg_match('/^\d+(cm|inch|mm)$/i', $value)) {
            return 'size';
        }

        // Kiểm tra theo hex color
        if (is_array($hex) && !empty($hex[0]) && preg_match('/^#[0-9a-f]{6}$/i', $hex[0])) {
            return 'color';
        }

        // Mặc định là text
        return 'text';
    }

    public function getFilterData()
    {
        try {
            // Get all active categories with product count
            $categories = Category::where('status', 'active')
                ->withCount(['products' => function ($query) {
                    $query->where('status', 'active');
                }])
                ->having('products_count', '>', 0)
                ->orderBy('name')
                ->get();

            // Get all unique colors from variant_attribute_values
            $colors = DB::table('variant_attribute_values as vav')
                ->join('variant_attribute_types as vat', 'vav.attribute_type_id', '=', 'vat.id')
                ->where('vat.name', 'like', '%màu%')
                ->where('vav.status', 'active')
                ->where('vat.status', 'active')
                ->whereNull('vav.deleted_at')
                ->whereNull('vat.deleted_at')
                ->select('vav.value', 'vav.hex')
                ->distinct()
                ->get()
                ->map(function ($item) {
                    $value = json_decode($item->value, true);
                    $hex = json_decode($item->hex, true);
                    return [
                        'name' => $value[0] ?? '',
                        'hex' => $hex[0] ?? '#000000'
                    ];
                })
                ->filter(function ($item) {
                    return !empty($item['name']);
                })
                ->values();

            // Get all unique storage options from variant_attribute_values
            $storageOptions = DB::table('variant_attribute_values as vav')
                ->join('variant_attribute_types as vat', 'vav.attribute_type_id', '=', 'vat.id')
                ->where('vat.name', 'like', '%dung lượng%')
                ->where('vav.status', 'active')
                ->where('vat.status', 'active')
                ->whereNull('vav.deleted_at')
                ->whereNull('vat.deleted_at')
                ->select('vav.value')
                ->distinct()
                ->get()
                ->map(function ($item) {
                    $value = json_decode($item->value, true);
                    return $value[0] ?? '';
                })
                ->filter()
                ->values();

            // Get price range
            $priceRange = DB::table('product_variants')
                ->where('status', 'active')
                ->selectRaw('MIN(selling_price) as min_price, MAX(selling_price) as max_price')
                ->first();

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $categories,
                    'colors' => $colors,
                    'storage_options' => $storageOptions,
                    'price_range' => [
                        'min' => (float) ($priceRange->min_price ?? 0),
                        'max' => (float) ($priceRange->max_price ?? 10000000)
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tải dữ liệu filter: ' . $e->getMessage()
            ], 500);
        }
    }
}
