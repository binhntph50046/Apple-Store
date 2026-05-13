@extends('client.layouts.app')

@section('title', 'Kết quả tìm kiếm: ' . $query)

@section('content')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <div class="container py-4" style="margin-top:140px">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4 section-title" data-aos="fade-up">Kết quả tìm kiếm cho: <span class="text-primary">"{{ $query }}"</span></h2>
                
        @if ($products->count() > 0)
            <div class="row">
                @foreach ($products as $product)
                            <div class="col-md-3 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <a class="product-item" href="{{ route('product.detail', $product->slug) }}"
                                    onclick="incrementView('{{ $product->id }}')" data-product-id="{{ $product->id }}">
                                    <div class="product-thumbnail text-center">
                                        @php
                                            $images = getImagesArray($product->images);
                                            if (empty($images) && $product->variants->isNotEmpty()) {
                                                $variant = $product->variants->first();
                                                $images = getImagesArray($variant->images);
                                            }
                                            $mainImage = $images[0] ?? 'uploads/default/default.jpg';
                                            if (!empty($mainImage) && !str_starts_with($mainImage, 'uploads/')) {
                                                $mainImage = 'uploads/products/' . $mainImage;
                                            }
                                        @endphp

                                        <img src="{{ asset($mainImage) }}" class="img-fluid mx-auto"
                                            alt="{{ $product->name }}" style="max-height: 200px; object-fit: contain;">
                                    </div>
                                    <h3 class="product-title text-center">{{ $product->name }}</h3>
                                    <div class="product-price-and-rating text-center">
                                        @if ($product->variants->isNotEmpty())
                                            @php
                                                $variant = $product->variants->first();
                                            @endphp
                                            @if ($variant->discount_price)
                                                <strong
                                                    class="product-price text-decoration-line-through text-muted">{{ number_format($variant->selling_price) }}đ</strong>
                                                <strong
                                                    class="product-price text-danger ms-2">{{ number_format($variant->discount_price) }}đ</strong>
                                            @else
                                                <strong
                                                    class="product-price">{{ number_format($variant->selling_price) }}đ</strong>
                                            @endif
                                        @endif
                                        <div class="product-rating d-flex justify-content-center align-items-center">
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <i class="fas fa-star"></i>
                                            <span>({{ number_format($product->views) }} views)</span>
                                        </div>
                                    </div>
                                    <div class="product-icons">
                                        <span class="icon-compare"
                                            onclick="event.preventDefault(); addToCompare('{{ $product->id }}', '{{ $product->name }}', '{{ $product->category_id }}')"
                                            title="Thêm vào so sánh">
                                            <i class="fa-solid fa-code-compare"></i>
                                        </span>
                                        @auth
                                            <form action="{{ route('wishlist.toggle', $product) }}" method="POST"
                                                style="display: none;" id="wishlist-form-{{ $product->id }}">
                                                @csrf
                                                <input type="hidden" name="product_name" value="{{ $product->name }}">
                                            </form>
                                            <span
                                                class="icon-heart icon-add-to-wishlist {{ in_array($product->id, $wishlistProductIds ?? []) ? 'in-wishlist' : '' }}"
                                                onclick="event.preventDefault(); toggleWishlist('{{ $product->id }}', '{{ route('wishlist.toggle', $product) }}', this)"
                                                title="{{ in_array($product->id, $wishlistProductIds ?? []) ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        @else
                                            <span class="icon-heart icon-add-to-wishlist"
                                                onclick="event.preventDefault(); showLoginPrompt()"
                                                title="Đăng nhập để thêm vào yêu thích">
                                                <i class="fas fa-heart"></i>
                                            </span>
                                        @endauth
                                        <span class="icon-quick-view"
                                            onclick="event.preventDefault(); showQuickView({{ $product->id }})"><i
                                                class="fas fa-eye"></i></span>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->appends(['q' => $query])->links() }}
            </div>
        @else
                    <div class="alert alert-warning mt-4" data-aos="fade-up">
                        <h5>Không tìm thấy sản phẩm phù hợp</h5>
                        <p class="mb-0">Vui lòng thử lại với từ khóa khác hoặc <a href="{{ route('shop') }}">xem tất cả sản phẩm</a></p>
                    </div>
        @endif

        @if ($blogs->count() > 0)
                    <div class="mt-5" data-aos="fade-up">
                        <h4 class="section-title">Bài viết liên quan</h4>
            <div class="row">
                @foreach ($blogs as $blog)
                                <div class="col-md-6 mb-3" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                    <div class="border rounded p-3 h-100 shadow-sm">
                                        <a href="{{ route('blog.show', $blog->slug) }}" class="fw-bold text-decoration-none">{{ $blog->title }}</a>
                                        <p class="mb-0 text-muted mt-2">{{ Str::limit(strip_tags($blog->content), 120) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
                        <div class="d-flex justify-content-center mt-3">
            {{ $blogs->appends(['q' => $query])->links() }}
                        </div>
                    </div>
        @endif

        @if ($products->count() == 0 && $blogs->count() == 0)
                    <div class="alert alert-warning mt-4" data-aos="fade-up">
                        <h5>Không tìm thấy kết quả phù hợp</h5>
                        <p class="mb-0">Vui lòng thử lại với từ khóa khác hoặc <a href="{{ route('shop') }}">xem tất cả sản phẩm</a></p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Nút FAB và Panel Chat -->
    <div x-data="{ open: false, selected: [] }" x-init="if (window.innerWidth > 768) open = false" @resize.window="if (window.innerWidth > 768) open = false">
        <!-- Form ẩn trong panel chat -->
        <form id="compareForm" action="{{ route('compare.index') }}" method="GET" x-show="open" x-ref="compareForm"
            @click.away="open = false"
            class="fixed bottom-20 right-4 w-72 bg-white border border-gray-200 rounded-lg shadow-lg p-4 z-50 transform transition-transform duration-300"
            :class="{ 'translate-y-full': !open, 'translate-y-0': open }">
            <div class="mb-2">
                <label class="block text-sm font-medium text-gray-700">Chọn từ 2-4 sản phẩm:</label>
                <select multiple name="products[]" x-model="selected"
                    @change="if (selected.length >= 2 && selected.length <= 4) $refs.compareForm.submit()"
                    class="w-full border p-2 rounded mt-1" required>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            @if (count($errors) > 0)
                <p class="text-red-500 text-xs mt-1">Vui lòng chọn từ 2 đến 4 sản phẩm.</p>
        @endif
            <p class="text-xs text-gray-500 mt-1">Chọn từ 2-4 sản phẩm để so sánh tự động.</p>
        </form>
    </div>

    <!-- JavaScript for Wishlist and Quick View -->
    <script>
        function showCustomAlert(message, type = 'success') {
            const alertId = 'custom-alert-' + Date.now();
            const icon = type === 'success' ? 'fa-check-circle' : 'fa-times-circle';
            const alertDiv = document.createElement('div');
            alertDiv.className = `custom-alert ${type}`;
            alertDiv.id = alertId;
            alertDiv.innerHTML = `
                <div class="icon"><i class="fas ${icon}"></i></div>
                <div class="content">
                    <strong>${type.toUpperCase()}</strong>
                    <p>${message}</p>
                </div>
                <div class="close" onclick="this.parentElement.style.display='none';">×</div>
            `;
            document.body.appendChild(alertDiv);
            setTimeout(() => {
                alertDiv.style.display = 'none';
            }, 3000);
        }

        async function toggleWishlist(productId, url, iconElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                showCustomAlert('Lỗi hệ thống, vui lòng thử lại!', 'error');
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
                    showCustomAlert(data.message, data.type);
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
                    showCustomAlert(data.message || 'Đã xảy ra lỗi, vui lòng thử lại!', 'error');
                }
            } catch (error) {
                showCustomAlert('Đã xảy ra lỗi: ' + (error.message || 'Unknown error'), 'error');
            }
        }

        function showLoginPrompt() {
            showCustomAlert('Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích.', 'error');
        }

        function incrementView(productId) {
            fetch(`/increment-view/${productId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                }
            });
        }

        function showQuickView(productId) {
            // Implement quick view functionality
            showCustomAlert('Tính năng xem nhanh đang được phát triển!', 'info');
        }
    </script>

    <!-- CSRF Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Quick View Modal -->
    <div class="modal fade" id="quickViewModal" tabindex="-1" aria-labelledby="quickViewModalLabel"
        aria-hidden="true">
        <!-- Nội dung modal giữ nguyên như trước -->
    </div>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            duration: 800,
            once: false,
        });
    </script>

    <style>
        /* Product styling */
        .product-item {
            display: block;
            text-decoration: none;
            color: inherit;
            border-radius: 8px;
            transition: all 0.3s ease;
            width: 95%;
            border: 1px solid #e9ecef;
            padding: 15px;
            background: white;
        }

        .product-item:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
            text-decoration: none;
            color: inherit;
        }

        .product-thumbnail {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 200px;
            margin-bottom: 15px;
        }

        .product-thumbnail img {
            max-width: 100%;
            height: auto;
            transition: transform 0.3s ease;
        }

        .product-item:hover .product-thumbnail img {
            transform: scale(1.001);
            transition: transform 0.2s ease;
        }

        .product-title {
            font-size: 1.1rem;
            margin: 10px 0;
            color: #333;
        }

        .product-price-and-rating {
            margin: 10px 0;
        }

        .product-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 10px;
        }

        .product-icons span {
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .product-icons span:hover {
            background: #e9ecef;
            color: #007bff;
        }

        .icon-heart {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .icon-heart i {
            color: #000000;
            transition: all 0.3s ease;
        }

        .icon-heart.in-wishlist i {
            color: #ff4d4d;
        }

        .icon-heart:hover i {
            transform: scale(1.1);
        }

        .icon-heart:not(.in-wishlist):hover i {
            color: #ffffff;
        }

        .icon-compare {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .icon-compare i {
            color: #000000;
            transition: all 0.3s ease;
        }

        .icon-compare:hover {
            background-color: #007bff;
        }

        .icon-compare:hover i {
            transform: scale(1.1);
            color: #ffffff;
        }

        /* Section title styling */
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: #333;
        }

        /* Custom alert styling */
        .custom-alert {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            max-width: 400px;
            animation: slideInRight 0.3s ease;
        }

        .custom-alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .custom-alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .custom-alert.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .custom-alert .icon {
            margin-right: 15px;
            font-size: 20px;
        }

        .custom-alert .content {
            flex: 1;
        }

        .custom-alert .content strong {
            display: block;
            margin-bottom: 5px;
        }

        .custom-alert .close {
            margin-left: 15px;
            cursor: pointer;
            font-size: 20px;
            font-weight: bold;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @media (max-width: 768px) {
            .col-md-3 {
                width: 100%;
                max-width: 300px;
                margin: 0 auto;
            }
            
            .section-title {
                font-size: 1.5rem;
            }
        }
    </style>

    @php
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
    @endphp

    <script>
        let compareSelected = [];
        let compareNames = [];
        let compareCategory = null;

        function addToCompare(productId, productName, categoryId) {
            const isAlreadySelected = compareSelected.includes(productId);

            if (isAlreadySelected) {
                // Remove product
                const index = compareSelected.indexOf(productId);
                compareSelected.splice(index, 1);
                compareNames.splice(index, 1);
                showCustomAlert('Đã bỏ chọn ' + productName + ' khỏi so sánh', 'info');

                // If the list becomes empty, reset the category
                if (compareSelected.length === 0) {
                    compareCategory = null;
                }
            } else {
                // Add product
                if (compareSelected.length >= 4) {
                    showCustomAlert('Chỉ được chọn tối đa 4 sản phẩm để so sánh!', 'error');
                    return;
                }

                // Check category
                if (compareSelected.length > 0 && compareCategory != categoryId) {
                    showCustomAlert('Vui lòng chỉ chọn các sản phẩm trong cùng một danh mục!', 'error');
                    return;
                }

                compareSelected.push(productId);
                compareNames.push(productName);

                // Set category if it's the first product being added
                if (compareSelected.length === 1) {
                    compareCategory = categoryId;
                }
                showCustomAlert('Đã thêm ' + productName + ' vào so sánh', 'success');
            }

            // Cập nhật hiển thị nút so sánh
            updateCompareButton();
        }

        function updateCompareButton() {
            const button = document.getElementById('compareButton');
            const count = document.getElementById('compareCount');
            const text = document.getElementById('compareButtonText');

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
                // Tạo form ẩn và submit
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
                showCustomAlert('Vui lòng chọn từ 2 đến 4 sản phẩm để so sánh!', 'error');
            }
        }
    </script>

    <!-- Nút So sánh nổi -->
    <div id="compareButton"
        style="display:none; position:fixed; bottom:80px; right:30px; z-index:9999; background:#007bff; color:white; padding:15px 25px; border-radius:25px; box-shadow:0 4px 12px rgba(0,123,255,0.3); cursor:pointer; transition:all 0.3s ease;"
        onclick="goToCompare()">
        <i class="fa-solid fa-code-compare me-2"></i>
        <span id="compareButtonText">So sánh ngay</span>
        <span id="compareCount" class="badge bg-light text-dark ms-2">0</span>
    </div>
@endsection
