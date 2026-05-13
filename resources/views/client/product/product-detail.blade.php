@extends('client.layouts.app')
@section('title', 'Chi tiết sản phẩm')
@section('content')

    <!-- Start Product Detail Section -->
    <div class="untree_co-section product-section" style="margin-top: 70px;padding-bottom: 0px">

        <div class="container">
            <!-- Bỏ phần thông báo cũ -->
            <div class="row">
                <!-- Product Images -->
                <div class="col-lg-6 mb-5">
                    <div class="product-gallery">
                        <div class="main-image mb-4 position-relative">
                            <button id="prevImageBtn" class="image-nav-btn" style="display:none;" onclick="showPrevImage()"><i
                                    class="fas fa-chevron-left"></i></button>
                            @php
                                // Helper function to safely handle both JSON strings and arrays
                                function getImagesArray($images)
                                {
                                    if (is_array($images)) {
                                        return $images;
                                    }
                                    if (is_string($images)) {
                                        $decoded = json_decode($images, true);
                                        return is_array($decoded) ? $decoded : [];
                                    }
                                    return [];
                                }

                                $defaultVariant = $product->defaultVariant;
                                $images = $defaultVariant ? getImagesArray($defaultVariant->images) : [];
                                $mainImage = $images[0] ?? 'uploads/default/default.jpg';
                                // Gom tất cả ảnh của mọi biến thể
                                $allImages = [];
                                foreach ($product->variants as $variant) {
                                    $imgs = getImagesArray($variant->images);
                                    foreach ($imgs as $img) {
                                        if (!in_array($img, $allImages)) {
                                            $allImages[] = $img;
                                        }
                                    }
                                }
                                $defaultPrice = $defaultVariant
                                    ? $defaultVariant->selling_price
                                    : $product->variants->min('selling_price');
                            @endphp
                            <img src="{{ asset($mainImage) }}" class="img-fluid" alt="{{ $product->name }}"
                                id="mainProductImage">
                            <button id="nextImageBtn" class="image-nav-btn" style="display:none;"
                                onclick="showNextImage()"><i class="fas fa-chevron-right"></i></button>
                        </div>
                        <div class="thumbnail-slider position-relative">
                            <button id="thumbPrevBtn" class="image-nav-btn"
                                style="left:0;top:50%;transform:translateY(-50%);" onclick="scrollThumbnails(-1)"><i
                                    class="fas fa-chevron-left"></i></button>
                            <div class="row flex-nowrap overflow-auto" id="thumbnailsRow"
                                style="scroll-behavior:smooth; margin:0 48px;"></div>
                            <button id="thumbNextBtn" class="image-nav-btn"
                                style="right:0;top:50%;transform:translateY(-50%);" onclick="scrollThumbnails(1)"><i
                                    class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="col-lg-6">
                    <div class="product-info">
                        <h1 class="product-title">{{ $product->name }}</h1>

                        <!-- Price Section -->
                        <div class="product-price mb-4">
                            <span class="current-price" id="productPrice">{{ number_format($defaultPrice, 0, ',', '.') }}
                                VNĐ</span>
                        </div>

                        <!-- Product Info Row 1: Stock & Sold -->
                        <div class="product-info-row mb-3">
                            <div class="info-item">
                                <i class="fas fa-boxes me-2"></i>
                                <span class="info-label">Tồn kho:</span>
                                <span class="info-value" id="productStock">
                                    @if ($defaultVariant)
                                        @if ($defaultVariant->stock > 0)
                                            <span class="text-success">{{ $defaultVariant->stock }} sản phẩm</span>
                                        @else
                                            <span class="text-danger">Hết hàng</span>
                                        @endif
                                    @else
                                        <span class="text-warning">Đang cập nhật</span>
                                    @endif
                                </span>
                            </div>
                            <div class="info-item sold-info" title="Số lượng sản phẩm đã được bán thành công">
                                <i class="fas fa-shopping-cart me-2"></i>
                                <span class="info-label">Đã bán:</span>
                                <span class="info-value">
                                    <span class="text-primary" id="soldQuantity">{{ number_format($totalSold) }} sản
                                        phẩm</span>
                                </span>
                            </div>
                        </div>

                        <!-- Product Info Row 2: Category & Warranty -->
                        <div class="product-info-row mb-3">
                            <div class="info-item">
                                <i class="fas fa-tag me-2"></i>
                                <span class="info-label">Danh mục:</span>
                                <span class="info-value">{{ $product->category ? $product->category->name : 'N/A' }}</span>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-shield-alt me-2"></i>
                                <span class="info-label">Bảo hành:</span>
                                <span class="info-value">{{ $product->warranty_months ?? 'N/A' }} tháng</span>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Attribute Variants (Color, Storage, ...) -->
                    @php
                        // Gom nhóm các thuộc tính theo loại (Color, Storage, ...)
                        $attributeGroups = [];
                        $seenValues = []; // Mảng để theo dõi các giá trị đã xuất hiện

                        foreach ($product->variants as $variant) {
                            // Bỏ qua các biến thể đã bị xóa mềm
                            if ($variant->deleted_at !== null) {
                                continue;
                            }
                            foreach ($variant->combinations as $combination) {
                                $typeName = $combination->attributeValue->attributeType->name ?? null;
                                if ($typeName) {
                                    $values = is_array($combination->attributeValue->value)
                                        ? $combination->attributeValue->value
                                        : json_decode($combination->attributeValue->value, true);
                                    $hexes = is_array($combination->attributeValue->hex)
                                        ? $combination->attributeValue->hex
                                        : json_decode($combination->attributeValue->hex, true);

                                    // Tạo key duy nhất cho mỗi giá trị
                                    $valueKey = $typeName . '_' . ($values[0] ?? '');

                                    // Chỉ thêm vào nếu chưa có giá trị này
                                    if (!isset($seenValues[$valueKey])) {
                                        $attributeGroups[$typeName][] = [
                                            'values' => $values,
                                            'hexes' => $hexes,
                                            'variant_id' => $variant->id,
                                            'is_default' => $variant->is_default,
                                        ];
                                        $seenValues[$valueKey] = true;
                                    }
                                }
                            }
                        }
                    @endphp
                    @foreach ($attributeGroups as $typeName => $group)
                        @php
                            $hasHex = false;
                            foreach ($group as $item) {
                                if (!empty($item['hexes'][0])) {
                                    $hasHex = true;
                                    break;
                                }
                            }
                            $defaultItem = collect($group)->first(fn($item) => $item['is_default']) ?? $group[0];
                            $defaultValue = $defaultItem['values'][0] ?? '';
                        @endphp
                        <div class="variant-group mb-4">
                            <label class="form-label">
                                {{ ucfirst($typeName) }}
                                @if ($hasHex)
                                    : <span id="selected-{{ $typeName }}-value"
                                        class="ms-2 badge bg-light text-dark border">{{ $defaultValue }}</span>
                                @endif
                            </label>
                            <div class="variant-options mt-2 d-flex gap-3">
                                @if ($hasHex)
                                    @foreach ($group as $item)
                                        @php
                                            $value = $item['values'][0] ?? '';
                                            $hex = $item['hexes'][0] ?? null;
                                        @endphp
                                        <div class="color-option {{ $item['is_default'] ? 'active' : '' }}"
                                            title="{{ $value }}" data-color="{{ $value }}"
                                            data-variant-id="{{ $item['variant_id'] }}"
                                            data-attr-type="{{ $typeName }}"
                                            onclick="selectVariant({{ $item['variant_id'] }}, '{{ addslashes($value) }}', '{{ $typeName }}', this)"
                                            style="background-color: {{ $hex ? $hex : '#f8f9fa' }}; border-radius: 50%; width: 40px; height: 40px; border: 2px solid #ddd; box-shadow: 0 2px 8px rgba(0,0,0,0.15); display: inline-block; cursor: pointer;">
                                        </div>
                                    @endforeach
                                @else
                                    @php
                                        $uniqueValues = [];
                                        $uniqueItems = [];
                                        foreach ($group as $item) {
                                            $value = $item['values'][0] ?? '';
                                            if (!in_array($value, $uniqueValues)) {
                                                $uniqueValues[] = $value;
                                                $uniqueItems[] = $item;
                                            }
                                        }
                                    @endphp
                                    @foreach ($uniqueItems as $item)
                                        @php
                                            $value = $item['values'][0] ?? '';
                                            // Kiểm tra active đúng với thuộc tính của biến thể mặc định
                                            $isActive = false;
                                            if ($defaultVariant) {
                                                foreach ($defaultVariant->combinations as $comb) {
                                                    if (
                                                        ($comb->attributeValue->attributeType->name ?? '') ===
                                                            $typeName &&
                                                        ($comb->attributeValue->value[0] ?? '') === $value
                                                    ) {
                                                        $isActive = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        <button type="button" class="storage-btn {{ $isActive ? 'active' : '' }}"
                                            data-variant-id="{{ $item['variant_id'] }}"
                                            data-attr-type="{{ $typeName }}"
                                            onclick="selectVariant({{ $item['variant_id'] }}, '{{ addslashes($value) }}', '{{ $typeName }}', this)">
                                            {{ $value }}
                                        </button>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Quantity Selector -->
                    <div class="quantity-selector mb-4">
                        <label class="form-label">Số lượng:</label>
                        <div class="quantity-control">
                            <button class="quantity-btn minus">-</button>
                            <input type="number" id="quantity" class="form-control" value="1" min="1"
                                readonly>
                            <button class="quantity-btn plus">+</button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="product-actions mb-4">
                        <button class="btn btn-primary" id="buyNowBtn">
                            <i class="fas fa-bolt me-2"></i>Mua ngay
                        </button>
                        <form action="{{ route('cart.add') }}" method="POST" style="display: inline;" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="variant_id" id="selectedVariantId" value="">
                            <input type="hidden" name="quantity" id="cartQuantity" value="1">
                            <button type="submit" class="btn btn-outline-primary" id="addToCartBtn">
                                <i class="fas fa-cart-plus me-2"></i> Thêm vào giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tabs Section (bên dưới chi tiết sản phẩm) -->
        <hr>
        <div class="container mt-5">
            <div class="d-flex justify-content-center mb-4" style="gap: 18px;">
                <button class="tab-btn-custom active" id="tab-desc-btn" onclick="showTab('desc')">Mô tả</button>
                <button class="tab-btn-custom" id="tab-spec-btn" onclick="showTab('spec')">Thông số kỹ thuật</button>
            </div>
            <div id="tab-desc" class="tab-content" style="display: block;">
                <div class="card">
                    <div class="card-body position-relative">
                        <div id="product-content" class="collapsed-content position-relative">
                            {!! $product->content !!}
                            <div class="content-overlay" id="content-overlay"></div>
                        </div>
                        <div class="d-flex justify-content-center mt-3">
                            <button id="toggle-content-btn" class="btn tab-btn-custom active btn-sm">Xem thêm<i
                                    class="fas fa-chevron-down ms-2"></i></button>
                        </div>
                    </div>
                </div>
            </div>


            <div id="tab-spec" class="tab-content" style="display: none;">
                <div class="card">
                    <div class="card-body">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th colspan="2" class="bg-light fw-bold">Cấu hình & Bộ nhớ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($product->specifications && $product->specifications->isNotEmpty())
                                    @foreach ($product->specifications as $spec)
                                        @if ($spec->specification)
                                            <tr>
                                                <td class="text-secondary" style="width: 220px;">
                                                    {{ $spec->specification->name }}</td>
                                                <td>{{ $spec->value }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="2">Chưa có thông số kỹ thuật.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @include('client.product.suggestions', ['suggestions' => $suggestions]);
    </div>
    </div>
    <!-- End Product Detail Section -->

    <style>
        .collapsed-content {
            max-height: 380px;
            overflow: hidden;
            position: relative;
            transition: max-height 0.5s ease;
        }

        .expanded-content {
            max-height: none;
        }

        /* Overlay gradient phía dưới */
        .content-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 160px;
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0), #fff);
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .card-body img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        #toggle-content-btn {
            display: inline-block;
            margin: 0 auto;
            position: relative;
            z-index: 2;
            /* Đảm bảo nút nằm trên overlay */
        }

        .color-variants {
            margin-bottom: 2rem;
        }

        .color-options {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .color-variant-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .color-name {
            font-size: 0.9rem;
            color: #666;
        }

        .color-option {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid #ddd;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .color-option:not([data-hex]) {
            border-radius: 4px;
            background-color: #f8f9fa;
            position: relative;
        }

        .color-option:not([data-hex])::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
        }

        .color-option.active {
            border-color: #007bff !important;
            box-shadow: 0 0 0 2px #007bff33;
            position: relative;
        }

        .color-option:hover {
            transform: scale(1.08);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
            z-index: 2;
        }

        .storage-btn {
            padding: 8px 16px;
            margin-right: 10px;
            border: 1px solid #ddd;
            background: #fff;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .storage-btn.active {
            background: #fff !important;
            color: #000 !important;
            border: 2px solid #007bff !important;
            box-shadow: 0 0 0 2px #007bff33;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            max-width: 150px;
        }

        .quantity-btn {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background: #fff;
            cursor: pointer;
        }

        .quantity-control input {
            text-align: center;
            border-left: none;
            border-right: none;
            border-radius: 0;
        }

        .spec-item {
            background: #f8f9fa;
            padding: 10px 14px;
            border-radius: 6px;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .thumbnail {
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            border-color: #000;
        }

        .color-option::after {
            display: none !important;
        }

        .tab-content {
            min-height: 200px;
        }

        .tab-btn-custom {
            padding: 9px 26px;
            font-size: 16px;
            font-weight: 500;
            color: #3b5d50;
            background-color: #f0f0f5;
            border: 1px solid #d0d0e0;
            border-radius: 999px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .tab-btn-custom:hover {
            background-color: #e2e6f3;
            color: #0d1b5c;
            border-color: #b0b6d1;
        }

        .tab-btn-custom.active {
            background-color: #3b5d50;
            color: #fff;
            border-color: #3b5d50;
            box-shadow: 0 4px 10px rgba(26, 35, 126, 0.2);
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .table th.bg-light {
            background: #f8f9fa !important;
        }

        .image-nav-btn {
            width: 48px;
            height: 48px;
            background: rgba(0, 0, 0, 0.18);
            border: none;
            border-radius: 50%;
            color: #fff;
            font-size: 2rem;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 3;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        }

        .image-nav-btn:hover {
            background: rgba(0, 0, 0, 0.35);
        }

        #prevImageBtn {
            left: 12px;
        }

        #nextImageBtn {
            right: 12px;
        }

        .thumbnail-slider {
            position: relative;
        }

        #thumbPrevBtn,
        #thumbNextBtn {
            top: 50%;
            transform: translateY(-50%);
            position: absolute;
            z-index: 2;
            background: rgba(0, 0, 0, 0.18);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }

        #thumbPrevBtn {
            left: 0;
        }

        #thumbNextBtn {
            right: 0;
        }

        #thumbPrevBtn:hover,
        #thumbNextBtn:hover {
            background: rgba(0, 0, 0, 0.35);
        }

        #thumbnailsRow {
            margin: 0 48px;
            overflow-x: auto;
            flex-wrap: nowrap !important;
            white-space: nowrap;
            scroll-behavior: smooth;
        }

        .product-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Hiển thị tối đa 2 dòng */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            word-break: break-word;
            min-height: 2.6em;
            /* Đảm bảo luôn chiếm 2 dòng chiều cao */
            line-height: 1.3em;
            max-height: 2.6em;
            text-align: left !important;

        }

        #thumbnailsRow .col-3 {
            flex: 0 0 auto;
            width: 80px;
            max-width: 80px;
            padding: 0 4px;
        }

        #thumbnailsRow img.thumbnail {
            border-radius: 8px;
            border: 2px solid transparent;
        }

        #thumbnailsRow img.thumbnail.active {
            border: 2px solid #007bff;
        }
    </style>

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Khai báo biến toàn cục
        let selectedValues = {};
        let selectedVariants = {};
        let variantData = {};
        let attributeToVariant = {};
        let requiredTypes = [];
        let sortedTypes = [];
        let currentImageIndex = 0;
        let currentImages = @json($images);
        let totalSold = {{ $totalSold }};
        let variantSoldData = @json($variantSoldData);

        document.addEventListener('DOMContentLoaded', function() {
            // Custom styling cho SweetAlert2
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Handle session flash messages
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: '{{ session('success') }}'
                });
            @endif

            @if (session('error'))
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: '{{ session('error') }}'
                });
            @endif

            // Quantity controls
            const quantityInput = document.getElementById('quantity');
            const minusBtn = document.querySelector('.quantity-btn.minus');
            const plusBtn = document.querySelector('.quantity-btn.plus');
            const addToCartBtn = document.getElementById('addToCartBtn');
            const buyNowBtn = document.getElementById('buyNowBtn');

            minusBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                    document.getElementById('cartQuantity').value = quantityInput.value;
                }
            });

            plusBtn.addEventListener('click', () => {
                const currentValue = parseInt(quantityInput.value);
                quantityInput.value = currentValue + 1;
                document.getElementById('cartQuantity').value = quantityInput.value;
            });
            // Khởi tạo dữ liệu biến thể
            @foreach ($product->variants as $variant)
                @if ($variant->deleted_at === null)
                    variantData[{{ $variant->id }}] = {
                        images: {!! json_encode(getImagesArray($variant->images)) !!},
                        price: {{ $variant->selling_price }},
                        stock: {{ $variant->stock }}
                    };
                @endif
            @endforeach



            // Khởi tạo mapping attribute -> variant
            @foreach ($product->variants as $variant)
                @if ($variant->deleted_at === null)
                    @php
                        $attrMap = [];
                        foreach ($variant->combinations as $comb) {
                            $typeName = $comb->attributeValue->attributeType->name ?? '';
                            $value = is_array($comb->attributeValue->value) ? $comb->attributeValue->value[0] ?? '' : json_decode($comb->attributeValue->value, true)[0] ?? '';
                            if ($typeName !== '') {
                                $attrMap[$typeName] = $value;
                            }
                        }
                        if (!empty($attrMap)) {
                            ksort($attrMap, SORT_NATURAL | SORT_FLAG_CASE);
                        }
                        $key = implode('|', $attrMap);
                    @endphp
                    attributeToVariant["{{ $key }}"] = {{ $variant->id }};
                @endif
            @endforeach

            // Lấy danh sách các loại thuộc tính bắt buộc
            document.querySelectorAll('.variant-group').forEach(function(group) {
                const label = group.querySelector('label.form-label');
                if (label) {
                    let typeName = label.textContent.split(':')[0].trim();
                    requiredTypes.push(typeName);
                }
            });
            // Sắp xếp tên thuộc tính theo alpha để đồng bộ với key ánh xạ phía trên
            sortedTypes = [...requiredTypes].sort((a, b) => a.localeCompare(b, undefined, {
                sensitivity: 'base'
            }));

            // Khởi tạo giá trị mặc định
            @if ($defaultVariant)
                @foreach ($defaultVariant->combinations as $comb)
                    @php
                        $typeName = $comb->attributeValue->attributeType->name ?? '';
                        $value = is_array($comb->attributeValue->value) ? $comb->attributeValue->value[0] : json_decode($comb->attributeValue->value, true)[0] ?? '';
                    @endphp
                    selectedValues["{{ $typeName }}"] = "{{ addslashes($value) }}";
                    selectedVariants["{{ $typeName }}"] = {{ $defaultVariant->id }};
                @endforeach
                document.getElementById('selectedVariantId').value = {{ $defaultVariant->id }};
                // Cập nhật tồn kho ban đầu theo biến thể mặc định
                updateStockDisplay({{ $defaultVariant->id }});
            @endif

            // Khởi tạo thumbnails
            updateThumbnails(@json($allImages));

            // Cập nhật hiển thị số lượng đã bán ban đầu
            updateSoldDisplay();

            // Add event listeners for buttons
            addToCartBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const variantId = getSelectedVariantId();
                if (!variantId) {
                    return false;
                }

                // Kiểm tra số lượng tồn kho
                const quantity = parseInt(quantityInput.value) || 1;
                if (variantData[variantId].stock < 1) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: '⚠️ Hết số lượng tồn kho!'
                    });
                    return false;
                }

                if (variantData[variantId].stock < quantity) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: '⚠️ Số lượng trong kho không đủ!'
                    });
                    return false;
                }

                document.getElementById('selectedVariantId').value = variantId;
                document.getElementById('addToCartForm').submit();
            });

            buyNowBtn.addEventListener('click', function(e) {
                e.preventDefault();
                const variantId = getSelectedVariantId();
                if (!variantId) {
                    return false;
                }

                // Kiểm tra số lượng tồn kho
                const quantity = parseInt(quantityInput.value) || 1;
                if (variantData[variantId].stock < 1) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: '⚠️ Hết số lượng tồn kho!'
                    });
                    return false;
                }

                if (variantData[variantId].stock < quantity) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: '⚠️ Số lượng trong kho không đủ!'
                    });
                    return false;
                }

                // Kiểm tra xem user đã đăng nhập chưa
                // @guest
                //     Toast.fire({
                //         icon: 'info',
                //         title: 'Thông báo',
                //         text: 'Vui lòng đăng nhập để tiếp tục mua hàng!'
                //     }).then((result) => {
                //         if (result.isConfirmed) {
                //             window.location.href = '{{ route('login') }}';
                //         }
                //     });
                //     return false;
                // @endguest

                const mainImage = document.getElementById('mainProductImage').src;
                window.location.href = '/checkout?variant_id=' + variantId + '&quantity=' + quantity +
                    '&image=' + encodeURIComponent(mainImage);
            });
        });

        function selectVariant(variantId, value, typeName, el) {
            // Nếu là click vào màu sắc thì chọn toàn bộ thuộc tính của biến thể đó
            if (el.classList.contains('color-option')) {
                selectAllAttributesOfVariant(variantId);
                return;
            }

            // Remove active class from all elements of this type
            document.querySelectorAll('[data-attr-type="' + typeName + '"]').forEach(opt => opt.classList.remove('active'));

            // Add active to current
            el.classList.add('active');

            // Update label if exists
            const labelSpan = document.getElementById('selected-' + typeName + '-value');
            if (labelSpan) labelSpan.textContent = value;

            // Save selection
            const matchedType = requiredTypes.find(t => t.toLowerCase() === typeName.toLowerCase()) || typeName;
            selectedValues[matchedType] = value;
            selectedVariants[matchedType] = variantId;

            // Find matching variant
            let key = sortedTypes.map(type => selectedValues[type] || '').join('|');
            let matchedVariantId = attributeToVariant[key];

            // Update images and price if valid variant found
            if (matchedVariantId && variantData[matchedVariantId]) {
                // Cập nhật giá
                document.getElementById('productPrice').textContent =
                    new Intl.NumberFormat('vi-VN').format(variantData[matchedVariantId].price) + ' VNĐ';

                // Cập nhật ảnh chính và thumbnails
                if (variantData[matchedVariantId].images && variantData[matchedVariantId].images.length > 0) {
                    updateThumbnails(variantData[matchedVariantId].images);
                    document.getElementById('mainProductImage').src = '{{ asset('') }}' + variantData[matchedVariantId]
                        .images[0];
                }

                // Cập nhật variant_id cho form
                document.getElementById('selectedVariantId').value = matchedVariantId;

                // Cập nhật hiển thị số lượng đã bán (giữ nguyên vì là tổng của sản phẩm)
                updateSoldDisplay();
                // Cập nhật tồn kho theo biến thể
                updateStockDisplay(matchedVariantId);
            }
        }

        function updateSoldDisplay() {
            const soldQuantityElement = document.getElementById('soldQuantity');
            if (soldQuantityElement) {
                const currentVariantId = document.getElementById('selectedVariantId').value;
                let soldCount = totalSold; // Mặc định hiển thị tổng số đã bán

                // Nếu có dữ liệu đã bán theo variant và variant hiện tại có dữ liệu
                if (currentVariantId && variantSoldData[currentVariantId]) {
                    soldCount = variantSoldData[currentVariantId];
                }

                soldQuantityElement.textContent = new Intl.NumberFormat('vi-VN').format(soldCount) + ' sản phẩm';
            }
        }

        function updateStockDisplay(variantId) {
            const stockEl = document.getElementById('productStock');
            if (!stockEl || !variantId || !variantData[variantId]) return;
            const stock = variantData[variantId].stock;
            if (stock > 0) {
                stockEl.innerHTML = '<span class="text-success">' + new Intl.NumberFormat('vi-VN').format(stock) +
                    ' sản phẩm</span>';
            } else {
                stockEl.innerHTML = '<span class="text-danger">Hết hàng</span>';
            }
        }

        function selectAllAttributesOfVariant(variantId) {
            const variants = @json($product->variants);
            const selectedVariant = variants.find(v => v.id === variantId);

            if (selectedVariant) {
                // Reset all active states
                document.querySelectorAll('.color-option, .storage-btn').forEach(el => el.classList.remove('active'));

                selectedVariant.combinations.forEach(comb => {
                    const typeName = comb.attribute_value.attribute_type.name.trim();
                    const matchedType = requiredTypes.find(t => t.toLowerCase() === typeName.toLowerCase()) ||
                        typeName;

                    let value = comb.attribute_value.value;
                    if (typeof value === 'string') {
                        try {
                            value = JSON.parse(value);
                        } catch {
                            value = [value];
                        }
                    }
                    value = Array.isArray(value) ? value[0] : value;

                    selectedValues[matchedType] = value;
                    selectedVariants[matchedType] = variantId;

                    // Update UI
                    document.querySelectorAll('[data-attr-type="' + typeName + '"]').forEach(el => {
                        let elValue = el.getAttribute('data-color') || el.textContent.trim();
                        if (elValue == value) {
                            el.classList.add('active');
                        }
                    });

                    const labelSpan = document.getElementById('selected-' + typeName + '-value');
                    if (labelSpan) labelSpan.textContent = value;
                });

                // Update images and price
                if (variantData[variantId]) {
                    // Cập nhật giá
                    document.getElementById('productPrice').textContent =
                        new Intl.NumberFormat('vi-VN').format(variantData[variantId].price) + ' VNĐ';

                    // Cập nhật ảnh chính và thumbnails
                    if (variantData[variantId].images && variantData[variantId].images.length > 0) {
                        updateThumbnails(variantData[variantId].images);
                        document.getElementById('mainProductImage').src = '{{ asset('') }}' + variantData[variantId]
                            .images[0];
                    }

                    // Cập nhật variant_id cho form
                    document.getElementById('selectedVariantId').value = variantId;

                    // Cập nhật hiển thị số lượng đã bán
                    updateSoldDisplay();
                    // Cập nhật tồn kho theo biến thể
                    updateStockDisplay(variantId);
                }
            }
        }

        function getSelectedVariantId() {
            let missingTypes = [];
            let key = sortedTypes.map(type => {
                if (!selectedValues[type]) {
                    missingTypes.push(type);
                    return '';
                }
                return selectedValues[type];
            }).join('|');

            if (missingTypes.length > 0) {
                Toast.fire({
                    icon: 'warning',
                    title: 'Cảnh báo',
                    text: 'Vui lòng chọn các thuộc tính sau: ' + missingTypes.join(', ')
                });
                return null;
            }

            const variantId = attributeToVariant[key] || null;
            if (!variantId) {
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không tìm thấy biến thể phù hợp với lựa chọn của bạn!'
                });
            }
            return variantId;
        }

        function scrollThumbnails(direction) {
            const row = document.getElementById('thumbnailsRow');
            const scrollAmount = 120; // px mỗi lần cuộn
            row.scrollBy({
                left: direction * scrollAmount,
                behavior: 'smooth'
            });
        }

        function updateThumbnails(images) {
            const container = document.getElementById('thumbnailsRow');
            container.innerHTML = '';
            currentImages = images;
            currentImageIndex = 0;

            images.forEach((img, idx) => {
                const div = document.createElement('div');
                div.className = 'col-3';
                const imgElement = document.createElement('img');
                imgElement.src = '{{ asset('') }}' + img;
                imgElement.className = 'img-fluid thumbnail' + (idx === 0 ? ' active' : '');
                imgElement.alt = 'Thumbnail';
                imgElement.onclick = () => updateMainImageByIndex(idx);
                div.appendChild(imgElement);
                container.appendChild(div);
            });

            // Update navigation buttons
            updateImageNavButtons();
        }

        function updateMainImageByIndex(idx) {
            if (currentImages.length > 0) {
                document.getElementById('mainProductImage').src = '{{ asset('') }}' + currentImages[idx];
                currentImageIndex = idx;
                updateImageNavButtons();
                // Highlight thumbnail
                document.querySelectorAll('#thumbnailsRow img.thumbnail').forEach((el, i) => {
                    if (i === idx) el.classList.add('active');
                    else el.classList.remove('active');
                });
            }
        }

        function updateImageNavButtons() {
            document.getElementById('prevImageBtn').style.display =
                (currentImages.length > 1 && currentImageIndex > 0) ? '' : 'none';
            document.getElementById('nextImageBtn').style.display =
                (currentImages.length > 1 && currentImageIndex < currentImages.length - 1) ? '' : 'none';
        }

        function showPrevImage() {
            if (currentImages.length > 1 && currentImageIndex > 0) {
                updateMainImageByIndex(currentImageIndex - 1);
            }
        }

        function showNextImage() {
            if (currentImages.length > 1 && currentImageIndex < currentImages.length - 1) {
                updateMainImageByIndex(currentImageIndex + 1);
            }
        }

        function showTab(tab) {
            // Ẩn tất cả các tab content
            document.querySelectorAll('.tab-content').forEach(content => {
                content.style.display = 'none';
            });

            // Bỏ active khỏi tất cả các tab button
            document.querySelectorAll('.tab-btn-custom').forEach(btn => {
                btn.classList.remove('active');
            });

            // Hiển thị tab được chọn
            document.getElementById('tab-' + tab).style.display = 'block';
            // Active tab button tương ứng
            document.getElementById('tab-' + tab + '-btn').classList.add('active');
        }

        // rút gọn mô tả
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.getElementById('product-content');
            const toggleBtn = document.getElementById('toggle-content-btn');
            const overlay = document.getElementById('content-overlay');

            toggleBtn.addEventListener('click', function() {
                const isCollapsed = content.classList.contains('collapsed-content');

                content.classList.toggle('collapsed-content');
                content.classList.toggle('expanded-content');

                // Ẩn hoặc hiện overlay
                overlay.style.display = isCollapsed ? 'none' : 'block';

                // Đổi nội dung nút
                toggleBtn.innerHTML = isCollapsed ?
                    'Thu gọn <i class="fas fa-chevron-up ms-2"></i>' :
                    'Xem thêm <i class="fas fa-chevron-down ms-2"></i>';
            });
        });
    </script>

    <style>
        /* Custom styles cho Toast notifications */
        .swal2-toast {
            max-width: 300px !important;
            font-size: 0.875rem !important;
        }

        .swal2-toast .swal2-title {
            font-size: 1rem !important;
            margin: 0.5em 1em !important;
        }

        .swal2-toast .swal2-content {
            font-size: 0.875rem !important;
        }

        .swal2-toast .swal2-icon {
            width: 2em !important;
            height: 2em !important;
            margin: 0.5em !important;
        }

        .swal2-toast .swal2-icon .swal2-icon-content {
            font-size: 1.5em !important;
        }

        .swal2-toast .swal2-success-ring {
            width: 2em !important;
            height: 2em !important;
        }

        /* Styling cho layout thông tin sản phẩm gọn gàng */
        .product-info-row {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .product-info {
            padding-left: 0px;

        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 8px 12px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid #dee2e6;
            transition: all 0.3s ease;
            flex: 1;
            min-width: 200px;
        }

        .info-item:hover {
            background-color: #e9ecef;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .info-item.sold-info {
            border-left-color: #007bff;
            cursor: help;
        }

        .info-item.sold-info:hover {
            background-color: #e3f2fd;
            box-shadow: 0 2px 8px rgba(0, 123, 255, 0.15);
        }

        .info-item i {
            color: #6c757d;
            font-size: 1rem;
            width: 16px;
            text-align: center;
        }

        .info-item.sold-info i {
            color: #007bff;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            font-size: 0.9rem;
        }

        .info-value {
            font-weight: 500;
            color: #212529;
        }

        .info-value .text-primary {
            color: #007bff !important;
            font-weight: 600;
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .product-info-row {
                flex-direction: column;
                gap: 0.5rem;
            }

            .info-item {
                min-width: auto;
            }
        }
    </style>
@endsection
@endsection
