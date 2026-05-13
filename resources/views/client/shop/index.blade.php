@extends('client.layouts.app')
@section('title', 'Cửa hàng - Apple Store')

@section('content')
    <link href="{{ asset('css/shop-custom.css') }}" rel="stylesheet">
    <style>
        .flash-title {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            /* Số dòng tối đa */
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 48px;
            /* Đảm bảo chiều cao cố định cho 2 dòng (tùy font-size) */
            line-height: 1.2;
        }
    </style>

    <div class="shop-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1>Cửa hàng</h1>
                    <div class="breadcrumbs">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item active">Cửa hàng</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="untree_co-section product-section">
        <div class="container">
            <!-- Flash Sale Section -->
            @if ($flashSaleItems->count() && $flashSaleTimeRange)
                <style>
                    .flash-sale-header-custom {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        background: #f5f7fa;
                        border-radius: 18px;
                        padding: 32px 16px 24px 16px;
                        margin-bottom: 24px;
                        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
                        position: relative;
                    }

                    .flash-sale-title {
                        font-size: 2.2rem;
                        font-weight: bold;
                        color: #ff9800;
                        margin-bottom: 8px;
                        letter-spacing: 1px;
                    }

                    .flash-sale-info-row {
                        display: flex;
                        justify-content: center;
                        align-items: flex-end;
                        gap: 48px;
                        width: 100%;
                        margin-top: 8px;
                        flex-wrap: wrap;
                    }

                    .flash-sale-info-block {
                        min-width: 180px;
                        text-align: center;
                    }

                    .flash-sale-label {
                        font-size: 1.1rem;
                        color: #888;
                        font-weight: 500;
                        margin-bottom: 2px;
                    }

                    .flash-sale-time {
                        font-size: 1.25rem;
                        font-weight: bold;
                        color: #222;
                    }

                    .flash-sale-img {
                        max-height: 54px;
                        margin-bottom: 8px;
                    }

                    .flash-title {
                        display: -webkit-box;
                        -webkit-line-clamp: 2;
                        /* Số dòng tối đa */
                        -webkit-box-orient: vertical;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        min-height: 2.4em;
                        /* Đảm bảo chiều cao cho 2 dòng, tùy font-size */
                        line-height: 1.2;
                        max-height: 2.4em;
                        /* 2 dòng * line-height */
                        font-size: 16px;
                    }

                    @media (max-width: 768px) {
                        .flash-sale-info-row {
                            gap: 18px;
                        }

                        .flash-sale-header-custom {
                            padding: 18px 4px 12px 4px;
                        }

                        .flash-sale-title {
                            font-size: 1.3rem;
                        }
                    }
                </style>
                <div class="row mb-5 flash-sale-box">
                    <div class="col-12">
                        <div class="flash-sale-header-custom">
                            <div class="flash-sale-title">
                                {{ $flashSaleTimeRange['name'] ?? 'FLASH SALE' }}
                            </div>
                            <div class="flash-sale-info-row">
                                <div class="flash-sale-info-block">
                                    <img src="https://cdnv2.tgdd.vn/webmwg/2024/tz/images/icon-fs.png" alt="Flash Sale"
                                        class="flash-sale-img">
                                </div>
                                <div class="flash-sale-info-block">
                                    <div class="flash-sale-label">KẾT THÚC TRONG</div>
                                    <div class="flash-sale-time" id="countdown">00:00:00</div>
                                </div>
                                <div class="flash-sale-info-block">
                                    <div class="flash-sale-label">Thời gian</div>
                                    <div class="flash-sale-time" style="font-size:1.1rem;">
                                        {{ $flashSaleTimeRange['start_time']->format('d/m H:i') }} -
                                        {{ $flashSaleTimeRange['end_time']->format('d/m H:i') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="product-slider flash-sale-slider">
                                @foreach ($flashSaleItems as $item)
                                    <div class="product-item product-carousel">
                                        <div class="flash-card">
                                            <div class="flash-media position-relative">
                                                <span class="flash-discount">
                                                    @if ($item->discount_type == 'percent')
                                                        -{{ $item->discount }}%
                                                    @else
                                                        -{{ number_format(($item->discount / $item->selling_price) * 100, 1) }}%
                                                    @endif
                                                </span>
                                                <a href="{{ route('product.detail', ['slug' => $item->product_slug]) }}"
                                                    class="d-block w-100 h-100">
                                                    <img src="{{ asset($item->first_image) }}" class="img-fluid"
                                                        alt="{{ $item->variant_name }}">
                                                </a>
                                            </div>
                                            <div class="flash-body">
                                                <div class="flash-title">{{ $item->variant_name }}</div>
                                                @php
                                                    $original = $item->selling_price;
                                                    $discount =
                                                        $item->discount_type === 'percent'
                                                            ? $original * (1 - $item->discount / 100)
                                                            : $original - $item->discount;
                                                @endphp
                                                <div class="flash-price-row">
                                                    <strong class="flash-price">{{ number_format($discount, 0) }}đ</strong>
                                                    <span
                                                        class="flash-old"><del>{{ number_format($original, 0) }}đ</del></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    // Countdown with timestamp (ms) to avoid timezone parsing issues
                    const endTimeMs = Number("{{ $flashSaleTimeRange['end_time']->timestamp * 1000 }}");
                    const countdownElement = document.getElementById('countdown');
                    const tick = () => {
                        const distance = endTimeMs - Date.now();
                        if (distance <= 0) {
                            countdownElement.textContent = 'Đã kết thúc';
                            return;
                        }
                        const days = Math.floor(distance / 86400000);
                        const hours = Math.floor((distance % 86400000) / 3600000);
                        const minutes = Math.floor((distance % 3600000) / 60000);
                        const seconds = Math.floor((distance % 60000) / 1000);
                        countdownElement.textContent =
                            (days > 0 ? days + ' ngày ' : '') +
                            String(hours).padStart(2, '0') + ':' +
                            String(minutes).padStart(2, '0') + ':' +
                            String(seconds).padStart(2, '0');
                        requestAnimationFrameId = requestAnimationFrame(() => setTimeout(tick, 250));
                    };
                    let requestAnimationFrameId = requestAnimationFrame(tick);
                </script>
            @endif

            <!-- Category Menu -->
            <div class="category-menu-container mb-5">
                <ul class="p-0 choose-cate d-flex w-100 flex-nowrap justify-content-between">
                    @foreach ($topCategories as $category)
                        <li class="box_category" style="margin: 0; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.21);">
                            <a href="{{ route('shop.category', ['slug' => $category->slug]) }}">
                                <div class="img-catesp">
                                    <img src="{{ asset($category->image) }}" alt="{{ $category->name }}"
                                        style="width: 100px; height: 100px; object-fit: contain;">
                                </div>
                                <span>{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            <!-- Filter and Search Section -->
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
                                                        value="{{ $value }}"
                                                        name="filters[{{ $filterId }}][]"
                                                        {{ in_array($value, $appliedFilters['filters'][$filterId] ?? []) ? 'checked' : '' }}>
                                                    <label for="filter_{{ $filterId }}_{{ $loop->index }}"
                                                        class="color-label"
                                                        style="background-color: {{ $filter['hexValues'][$value] ?? '#CCCCCC' }}"
                                                        title="{{ $value }}"
                                                        onclick="toggleColorFilter(this, '{{ $value }}', '{{ $filterId }}')">
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
                        <!-- Sort and View Options -->
                        <div class="product-controls mb-4">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="mb-0">Tất cả sản phẩm ({{ $products->total() }})</h5>
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

                        <!-- Products Grid -->
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
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star" style="color: #f5a524;"></i>
                                                        @endfor
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

                            <!-- Pagination -->
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

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize flash sale slider
            $('.flash-sale-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 4,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                dots: false,
                responsive: [{
                        breakpoint: 1200,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

            // Setup filter functionality
            setupFilters();

            // Automatically expand collapse sections with selected filters
            @foreach ($filterData['dynamicFilters'] as $filterId => $filter)
                @if (!empty($appliedFilters['filters'][$filterId]))
                    $('#filterCollapse{{ $filterId }}').collapse('show');
                    $('#filterCollapse{{ $filterId }}').prev('.filter-title').find('.filter-arrow i')
                        .removeClass('fa-chevron-down').addClass('fa-chevron-up');
                @endif
            @endforeach

            // Update chevron icon on collapse toggle
            $('.filter-title').on('click', function() {
                const $icon = $(this).find('.filter-arrow i');
                const isExpanded = $(this).attr('aria-expanded') === 'true';
                if (isExpanded) {
                    $icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                } else {
                    $icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                }
            });

            // Initialize realtime product updates
            initializeRealtimeProducts();

            // Update chevron icons when collapse state changes
            $('.filter-group .collapse').on('show.bs.collapse', function() {
                $(this).prev('.filter-title').find('.filter-arrow i')
                    .removeClass('fa-chevron-down').addClass('fa-chevron-up');
            });

            $('.filter-group .collapse').on('hide.bs.collapse', function() {
                $(this).prev('.filter-title').find('.filter-arrow i')
                    .removeClass('fa-chevron-up').addClass('fa-chevron-down');
            });
        });

        function setupFilters() {
            // Handle filter changes (category slug + dynamic filters)
            $('input[name="category_slug[]"], input[name^="filters["]').on('change', function() {
                applyFilters();
            });

            // Handle sort change
            $('#sortSelect').on('change', function() {
                applyFilters();
            });

            // Handle price range inputs
            $('input[name="min_price"], input[name="max_price"]').on('blur', function() {
                applyFilters();
            });
        }

        function setupFilters() {
            // Handle sort change - auto apply
            $('#sortSelect').on('change', function() {
                applyFilters();
            });

            // Initialize color filter states
            $('.color-checkbox:checked').each(function() {
                const label = $(this).next('.color-label');
                label.addClass('selected');
            });

            // Track filter changes
            trackFilterChanges();
        }

        function trackFilterChanges() {
            // Store initial state
            const initialFilters = getCurrentFilterState();

            // Monitor changes
            $('input[name^="filters["], input[name="min_price"], input[name="max_price"]').on('change', function() {
                const currentFilters = getCurrentFilterState();
                const hasChanges = JSON.stringify(initialFilters) !== JSON.stringify(currentFilters);

                const filterBtn = $('.filter-actions .btn-primary');
                if (hasChanges) {
                    filterBtn.addClass('has-changes').text('Lọc (' + getChangedFilterCount() + ')');
                } else {
                    filterBtn.removeClass('has-changes').text('Lọc');
                }
            });
        }

        function getCurrentFilterState() {
            const state = {};

            // Get checkbox filters
            $('input[name^="filters["]').each(function() {
                const name = $(this).attr('name');
                if (!state[name]) state[name] = [];
                if ($(this).is(':checked')) {
                    state[name].push($(this).val());
                }
            });

            // Get price filters
            const minPrice = $('input[name="min_price"]').val();
            const maxPrice = $('input[name="max_price"]').val();
            if (minPrice) state.min_price = minPrice;
            if (maxPrice) state.max_price = maxPrice;

            return state;
        }

        function getChangedFilterCount() {
            let count = 0;

            // Count checked checkboxes
            $('input[name^="filters["]:checked').each(function() {
                count++;
            });

            // Count price filters
            if ($('input[name="min_price"]').val()) count++;
            if ($('input[name="max_price"]').val()) count++;

            return count;
        }

        // Apply filters
        function applyFilters() {
            const formData = new FormData();

            // Get all dynamic filters
            const filterInputs = document.querySelectorAll('input[name^="filters["]');
            filterInputs.forEach(input => {
                if (input.checked) {
                    formData.append(input.name, input.value);
                }
            });

            // Get price filters
            const minPrice = $('input[name="min_price"]').val();
            const maxPrice = $('input[name="max_price"]').val();
            if (minPrice) formData.append('min_price', minPrice);
            if (maxPrice) formData.append('max_price', maxPrice);

            // Get sort
            const sort = $('select[name="sort"]').val();
            if (sort) formData.append('sort', sort);

            // Redirect with filters
            const params = new URLSearchParams(formData);
            window.location.href = '{{ route('shop') }}?' + params.toString();
        }

        function clearFilters() {
            // Reset all filters
            $('input[name^="filters["]').prop('checked', false);
            $('input[name="min_price"], input[name="max_price"]').val('');
            $('.color-label').removeClass('selected');

            // Reset filter button
            $('.filter-actions .btn-primary').removeClass('has-changes').text('Lọc');

            // Redirect to clean URL
            window.location.href = '{{ route('shop') }}';
        }

        // Toggle color filter
        function toggleColorFilter(labelElement, value, filterId) {
            const checkbox = labelElement.previousElementSibling;
            const isChecked = checkbox.checked;

            if (isChecked) {
                checkbox.checked = false;
                labelElement.classList.remove('selected');
            } else {
                checkbox.checked = true;
                labelElement.classList.add('selected');
            }

            // Trigger change event to update filter button
            $(checkbox).trigger('change');
        }

        // Compare feature (ported from homepage)
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

        // Wishlist toggle (synced with homepage)
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

        // Realtime product updates
        function initializeRealtimeProducts() {
            // Initialize Pusher
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                encrypted: true
            });

            // Subscribe to public products channel
            const channel = pusher.subscribe('public.products');

            // Listen for new product events
            channel.bind('product.created', function(data) {
                console.log('New product received:', data);

                // Show notification
                showNewProductNotification(data);

                // Add product to grid if on first page
                addProductToGrid(data);
            });
        }

        function showNewProductNotification(product) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'new-product-notification';
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 15px 20px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                z-index: 10000;
                max-width: 300px;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                cursor: pointer;
            `;

            notification.innerHTML = `
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="${product.image}" alt="${product.name}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    <div>
                        <div style="font-weight: bold; margin-bottom: 5px;">Sản phẩm mới!</div>
                        <div style="font-size: 14px;">${product.name}</div>
                        <div style="font-size: 12px; opacity: 0.8;">${product.price}đ</div>
                    </div>
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
            }, 100);

            // Auto remove after 5 seconds
            setTimeout(() => {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 5000);

            // Click to go to product
            notification.addEventListener('click', () => {
                window.location.href = product.url;
            });
        }

        function addProductToGrid(product) {
            // Only add to grid if we're on the first page and no filters are applied
            const urlParams = new URLSearchParams(window.location.search);
            const hasFilters = urlParams.has('category_slug') ||
                urlParams.has('min_price') ||
                urlParams.has('max_price') ||
                urlParams.has('filters') ||
                urlParams.has('page') && urlParams.get('page') !== '1';

            if (hasFilters) return;

            const productGrid = document.querySelector('.product-grid');
            if (!productGrid) return;

            // Create new product card
            const productCard = document.createElement('div');
            productCard.className = 'col-lg-4 col-md-6 mb-4';
            productCard.style.animation = 'slideInFromTop 0.5s ease';

            // Generate rating stars - if no rating, show 5 empty stars
            let ratingStars = '';
            if (product.rating && product.rating > 0) {
                ratingStars = generateStars(product.rating);
            } else {
                // Use solid gray stars if outline set isn't loaded
                for (let i = 1; i <= 5; i++) {
                    ratingStars += '<i class="fas fa-star" style="color: #d1d5db;"></i>';
                }
            }

            productCard.innerHTML = `
                <div class="product-card new-product">
                    <div class="product-media">
                        <a href="${product.url}" class="d-block w-100 h-100 text-center">
                            <img src="${product.image}" class="product-img" alt="${product.name}">
                        </a>
                        <div class="product-tools">
                            <span class="tool-btn" title="So sánh" onclick="event.preventDefault(); addToCompare('${product.id}', '${product.name}', '${product.category_id || ''}')">
                                <i class="fa-solid fa-code-compare"></i>
                            </span>
                            <span class="tool-btn icon-heart icon-add-to-wishlist" title="Đăng nhập để thêm vào yêu thích" onclick="event.preventDefault(); showLoginPrompt()">
                                <i class="fas fa-heart"></i>
                            </span>
                            <span class="tool-btn" title="Xem nhanh" onclick="event.preventDefault(); showQuickView(${product.id}, '${product.slug}')">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>
                    <div class="product-body">
                        <a href="${product.url}" class="text-decoration-none">
                            <h3 class="product-title mb-1">${product.name}</h3>
                        </a>
                        <div class="product-price-row">
                            ${product.discount_price ? 
                                `<strong class="product-price">${product.discount_price}đ</strong>
                                                             <span class="old-price"><del>${product.price}đ</del></span>` :
                                `<strong class="product-price">${product.price}đ</strong>`
                            }
                        </div>
                        <div class="product-rating-row">
                            <div class="stars">
                                ${ratingStars}
                            </div>
                            <span class="sold-count">(${product.sold_count || 0} đã bán)</span>
                        </div>
                    </div>
                </div>
            `;

            // Add to beginning of grid
            productGrid.insertBefore(productCard, productGrid.firstChild);

            // Update product count
            const countElement = document.querySelector('.product-controls h5');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent.match(/\d+/)[0]);
                countElement.textContent = `Tất cả sản phẩm (${currentCount + 1})`;
            }

            // Remove highlight after 5 seconds
            setTimeout(() => {
                const productCardElement = productCard.querySelector('.product-card');
                if (productCardElement) {
                    productCardElement.classList.remove('new-product');
                }
            }, 5000);
        }

        function generateStars(rating) {
            const full = Math.floor(rating);
            const half = (rating - full) >= 0.5;
            let html = '';
            for (let i = 1; i <= 5; i++) {
                if (i <= full) {
                    html += '<i class="fas fa-star" style="color: #f5a524;"></i>';
                } else if (half && i === full + 1) {
                    html += '<i class="fas fa-star-half-alt" style="color: #f5a524;"></i>';
                } else {
                    // Solid gray star for empty to avoid missing outline set
                    html += '<i class="fas fa-star" style="color: #d1d5db;"></i>';
                }
            }
            return html;
        }
    </script>

    <!-- Nút So sánh nổi (đồng bộ giao diện với trang chủ) -->
    <div id="compareButton"
        style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:9999; background:#007bff; color:white; padding:15px 25px; border-radius:25px; box-shadow:0 4px 12px rgba(0,123,255,0.3); cursor:pointer; transition:all 0.3s ease;"
        onclick="goToCompare()">
        <i class="fa-solid fa-code-compare me-2"></i>
        <span id="compareButtonText">So sánh ngay</span>
        <span id="compareCount" class="badge bg-light text-dark ms-2">0</span>
    </div>

@endsection
