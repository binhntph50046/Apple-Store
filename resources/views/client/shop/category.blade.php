@extends('client.layouts.app')
@section('title', $category->name . ' - Apple Store')

@section('content')
    <link href="{{ asset('css/shop-custom.css') }}" rel="stylesheet">

    <div class="shop-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>{{ $category->name }}</h1>
                    <div class="breadcrumbs">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('shop') }}">Cửa hàng</a></li>
                                <li class="breadcrumb-item active">{{ $category->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="untree_co-section product-section">
        <div class="container">
            <div class="filter-section mb-5">
                <div class="row">
                    <div class="col-lg-3 col-md-4">
                        <!-- Filter Actions -->
                        <div class="filter-actions mb-3 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-clear-filter" style="flex: 1; margin-right: 10px;"
                                onclick="clearFilters()">
                                <i class="fas fa-times"></i> Xóa
                            </button>
                            <button type="button" class="btn btn-primary" style="flex: 1;" onclick="applyFilters()">
                                <i class="fas fa-filter"></i> Lọc
                            </button>
                        </div>
                        <!-- Price Filter -->
                        <div class="filter-group mb-3">
                            <h6 class="filter-title">Khoảng giá</h6>
                            <div class="price-range">
                                <div class="row">
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="min_price" placeholder="Từ"
                                            value="{{ $appliedFilters['min_price'] ?? '' }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="number" class="form-control" name="max_price" placeholder="Đến"
                                            value="{{ $appliedFilters['max_price'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dynamic Filters -->
                        @foreach ($filterData['dynamicFilters'] as $filterId => $filter)
                            <div class="filter-group mb-3">
                                <h6 class="filter-title d-flex align-items-center" style="cursor:pointer"
                                    data-bs-toggle="collapse" data-bs-target="#filterCollapse{{ $filterId }}"
                                    aria-expanded="false" aria-controls="filterCollapse{{ $filterId }}">
                                    {{ $filter['name'] }} ({{ $filter['count'] }})
                                    <span class="ms-auto filter-arrow" style="transition:0.3s;"><i
                                            class="fas fa-chevron-down"></i></span>
                                </h6>
                                <div class="collapse" id="filterCollapse{{ $filterId }}">
                                    @if ($filter['type'] === 'color')
                                        <div class="color-options">
                                            @foreach ($filter['values'] as $value)
                                                <div class="color-option-wrapper">
                                                    <input type="checkbox" class="color-checkbox"
                                                        id="filter_{{ $filterId }}_{{ $loop->index }}"
                                                        value="{{ $value }}" name="filters[{{ $filterId }}][]"
                                                        {{ in_array($value, $appliedFilters['filters'][$filterId] ?? []) ? 'checked' : '' }}>
                                                    <label for="filter_{{ $filterId }}_{{ $loop->index }}"
                                                        class="color-label"
                                                        style="background-color: {{ $filter['hexValues'][$value] ?? '#CCCCCC' }}"
                                                        title="{{ $value }}">
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif($filter['type'] === 'storage')
                                        <div class="filter-options">
                                            @foreach ($filter['values'] as $value)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="filters[{{ $filterId }}][]"
                                                        id="filter_{{ $filterId }}_{{ $loop->index }}"
                                                        value="{{ $value }}"
                                                        {{ in_array($value, $appliedFilters['filters'][$filterId] ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="filter_{{ $filterId }}_{{ $loop->index }}">
                                                        {{ $value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="filter-options">
                                            @foreach ($filter['values'] as $value)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="filters[{{ $filterId }}][]"
                                                        id="filter_{{ $filterId }}_{{ $loop->index }}"
                                                        value="{{ $value }}"
                                                        {{ in_array($value, $appliedFilters['filters'][$filterId] ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="filter_{{ $filterId }}_{{ $loop->index }}">
                                                        {{ $value }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="col-lg-9 col-md-8">
                        <div class="product-controls mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">{{ $category->name }} ({{ $products->total() }})</h5>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="d-flex align-items-center justify-content-md-end">
                                        <label class="me-2">Sắp xếp:</label>
                                        <select class="form-select form-select-sm" id="sortSelect" name="sort"
                                            style="width: auto;">
                                            <option value="newest"
                                                {{ ($appliedFilters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' }}>
                                                Mới nhất</option>
                                            <option value="price_low"
                                                {{ ($appliedFilters['sort'] ?? '') == 'price_low' ? 'selected' : '' }}>Giá
                                                tăng dần</option>
                                            <option value="price_high"
                                                {{ ($appliedFilters['sort'] ?? '') == 'price_high' ? 'selected' : '' }}>Giá
                                                giảm dần</option>
                                            <option value="name_asc"
                                                {{ ($appliedFilters['sort'] ?? '') == 'name_asc' ? 'selected' : '' }}>Tên
                                                A-Z</option>
                                            <option value="name_desc"
                                                {{ ($appliedFilters['sort'] ?? '') == 'name_desc' ? 'selected' : '' }}>Tên
                                                Z-A</option>
                                            <option value="popular"
                                                {{ ($appliedFilters['sort'] ?? '') == 'popular' ? 'selected' : '' }}>Phổ
                                                biến</option>
                                            <option value="rating"
                                                {{ ($appliedFilters['sort'] ?? '') == 'rating' ? 'selected' : '' }}>Đánh
                                                giá cao</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($products->count() > 0)
                            <div class="row product-grid">
                                @foreach ($products as $product)
                                    <div class="col-lg-4 col-md-6 mb-4">
                                        <div class="product-card">
                                            <div class="product-media">
                                                <a href="{{ route('product.detail', $product->slug) }}"
                                                    class="d-block w-100 h-100 text-center">
                                                    @php
                                                        $defaultImage = asset('uploads/default/default.jpg');
                                                        $variantImage = null;
                                                        $defaultVariant = $product->variants->first();

                                                        if ($defaultVariant && $defaultVariant->images) {
                                                            $images = $defaultVariant->images;
                                                            if (is_array($images) && !empty($images[0])) {
                                                                $variantImage = asset($images[0]);
                                                            } elseif (is_string($images)) {
                                                                $decoded = json_decode($images, true);
                                                                if (is_array($decoded) && !empty($decoded[0])) {
                                                                    $variantImage = asset($decoded[0]);
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <img src="{{ $variantImage ?? $defaultImage }}" class="product-img"
                                                        alt="{{ $product->name }}">
                                                </a>
                                            </div>

                                            <div class="product-tools">
                                                <span class="tool-btn" title="So sánh"
                                                    onclick="event.preventDefault(); addToCompare('{{ $product->id }}', '{{ $product->name }}', '{{ $product->category_id }}')">
                                                    <i class="fa-solid fa-code-compare"></i>
                                                </span>
                                                @auth
                                                    <span
                                                        class="tool-btn icon-heart icon-add-to-wishlist {{ in_array($product->id, $wishlistProductIds ?? []) ? 'in-wishlist' : '' }}"
                                                        title="{{ in_array($product->id, $wishlistProductIds ?? []) ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' }}"
                                                        onclick="event.preventDefault(); toggleWishlist('{{ $product->id }}', '{{ route('wishlist.toggle', $product) }}', this)">
                                                        <i class="fas fa-heart"></i>
                                                    </span>
                                                @else
                                                    <span class="tool-btn icon-heart icon-add-to-wishlist"
                                                        title="Đăng nhập để thêm vào yêu thích"
                                                        onclick="event.preventDefault(); showLoginPrompt()">
                                                        <i class="fas fa-heart"></i>
                                                    </span>
                                                @endauth
                                                <span class="tool-btn" title="Xem nhanh"
                                                    onclick="event.preventDefault(); showQuickView({{ $product->id }}, '{{ $product->slug }}')">
                                                    <i class="fas fa-eye"></i>
                                                </span>
                                            </div>

                                            <div class="product-body">
                                                <a href="{{ route('product.detail', $product->slug) }}"
                                                    class="text-decoration-none">
                                                    <h3 class="product-title mb-1">{{ $product->name }}</h3>
                                                </a>

                                                <div class="product-price-row">
                                                    @if ($product->variants->isNotEmpty())
                                                        @php $variant = $product->variants->first(); @endphp
                                                        @if ($variant->discount_price)
                                                            <strong
                                                                class="product-price">{{ number_format($variant->discount_price) }}đ</strong>
                                                            <span
                                                                class="old-price"><del>{{ number_format($variant->selling_price) }}đ</del></span>
                                                        @else
                                                            <strong
                                                                class="product-price">{{ number_format($variant->selling_price) }}đ</strong>
                                                        @endif
                                                    @endif
                                                </div>

                                                <div class="product-rating-row">
                                                    @php
                                                        $rating = $product->reviews->avg('rating') ?? 0;
                                                        $fullStars = floor($rating);
                                                        $halfStar = $rating - $fullStars >= 0.5;
                                                    @endphp
                                                    <div class="stars">
                                                        @if ($rating == 0)
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="fas fa-star"></i>
                                                            @endfor
                                                        @else
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                @if ($i <= $fullStars)
                                                                    <i class="fas fa-star"></i>
                                                                @elseif($i == $fullStars + 1 && $halfStar)
                                                                    <i class="fas fa-star-half-alt"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        @endif
                                                    </div>
                                                    <span
                                                        class="sold-count">({{ number_format($product->sold_count ?? 0) }}
                                                        đã bán)</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="pagination-wrapper text-center">
                                {{ $products->appends(request()->query())->links() }}
                            </div>
                        @else
                            <div class="no-products text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Không tìm thấy sản phẩm nào</h4>
                                <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                                <button class="btn btn-primary" onclick="clearFilters()">Xóa bộ lọc</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // No need to call setupFilters() since we're not binding change events anymore
        });

        function applyFilters() {
            const formData = new FormData();

            // Dynamic filters
            const filterInputs = document.querySelectorAll('input[name^="filters["]');
            filterInputs.forEach(input => {
                if (input.checked) {
                    formData.append(input.name, input.value);
                }
            });

            // Price
            const minPrice = $('input[name="min_price"]').val();
            const maxPrice = $('input[name="max_price"]').val();
            if (minPrice) formData.append('min_price', minPrice);
            if (maxPrice) formData.append('max_price', maxPrice);

            // Sort
            const sort = $('select[name="sort"]').val();
            if (sort) formData.append('sort', sort);

            // Redirect to category with params
            const params = new URLSearchParams(formData);
            window.location.href = '{{ route('shop.category', ['slug' => $category->slug]) }}' + '?' + params.toString();
        }

        function clearFilters() {
            window.location.href = '{{ route('shop.category', ['slug' => $category->slug]) }}';
        }

        // Toast helper
        function showToast(message, type = 'success') {
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

            Toast.fire({
                icon: type,
                title: message
            });
        }

        function showLoginPrompt() {
            showToast('Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích.', 'error');
        }

        // Wishlist toggle (same as index)
        async function toggleWishlist(productId, url, iconElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                showToast('Lỗi hệ thống, vui lòng thử lại!', 'error');
                return;
            }
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });
                const data = await response.json();
                if (data.status) {
                    showToast(data.message, data.type);
                    if (data.type === 'success') {
                        const allHeartIcons = document.querySelectorAll(
                            `.icon-heart[onclick*="toggleWishlist('${productId}'"]`);
                        allHeartIcons.forEach(icon => {
                            if (data.in_wishlist) {
                                icon.classList.add('in-wishlist');
                                icon.title = 'Xóa khỏi yêu thích';
                            } else {
                                icon.classList.remove('in-wishlist');
                                icon.title = 'Thêm vào yêu thích';
                            }
                        });
                    }
                } else {
                    showToast(data.message || 'Đã xảy ra lỗi, vui lòng thử lại!', 'error');
                }
            } catch (error) {
                showToast('Đã xảy ra lỗi: ' + (error.message || 'Unknown error'), 'error');
            }
        }

        // Compare feature
        let compareSelected = [];
        let compareNames = [];
        let compareCategory = null;

        function addToCompare(productId, productName, categoryId) {
            const isAlreadySelected = compareSelected.includes(productId);

            if (isAlreadySelected) {
                const index = compareSelected.indexOf(productId);
                compareSelected.splice(index, 1);
                compareNames.splice(index, 1);
                showToast('Đã bỏ chọn ' + productName + ' khỏi so sánh', 'info');

                if (compareSelected.length === 0) {
                    compareCategory = null;
                }
            } else {
                if (compareSelected.length >= 4) {
                    showToast('Chỉ được chọn tối đa 4 sản phẩm để so sánh!', 'error');
                    return;
                }

                if (compareSelected.length > 0 && compareCategory != categoryId) {
                    showToast('Vui lòng chỉ chọn các sản phẩm trong cùng một danh mục!', 'error');
                    return;
                }

                compareSelected.push(productId);
                compareNames.push(productName);

                if (compareSelected.length === 1) {
                    compareCategory = categoryId;
                }
                showToast('Đã thêm ' + productName + ' vào so sánh', 'success');
            }

            updateCompareButton();
        }

        function updateCompareButton() {
            const button = document.getElementById('compareButton');
            const count = document.getElementById('compareCount');
            const text = document.getElementById('compareButtonText');

            if (!button || !count || !text) return;

            if (compareSelected.length > 0) {
                button.style.display = 'block';
                count.textContent = compareSelected.length;
                if (compareSelected.length >= 2) {
                    text.textContent = 'So sánh ngay';
                    button.style.background = '#28a745';
                } else {
                    text.textContent = 'Chọn thêm sản phẩm';
                    button.style.background = '#007bff';
                }
            } else {
                button.style.display = 'none';
            }
        }

        function goToCompare() {
            if (compareSelected.length >= 2 && compareSelected.length <= 4) {
                const form = document.createElement('form');
                form.method = 'GET';
                form.action = '{{ route('compare.index') }}';

                const productsInput = document.createElement('input');
                productsInput.type = 'hidden';
                productsInput.name = 'products';
                productsInput.value = compareSelected.join(',');
                form.appendChild(productsInput);

                document.body.appendChild(form);
                form.submit();
            } else {
                showToast('Vui lòng chọn từ 2 đến 4 sản phẩm để so sánh!', 'error');
            }
        }
    </script>

    <!-- Nút So sánh nổi (đồng bộ với shop/index) -->
    <div id="compareButton"
        style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:9999; background:#007bff; color:white; padding:15px 25px; border-radius:25px; box-shadow:0 4px 12px rgba(0,123,255,0.3); cursor:pointer; transition:all 0.3s ease;"
        onclick="goToCompare()">
        <i class="fa-solid fa-code-compare me-2"></i>
        <span id="compareButtonText">So sánh ngay</span>
        <span id="compareCount" class="badge bg-light text-dark ms-2">0</span>
    </div>

@endsection
