<div class="container">
    {{-- <h4 class="mb-4">Sản phẩm gợi ý</h4> --}}
    <hr>
    <br style="margin: 200px 0px 200px 0px;">
    <div class="container">
        <h4 class="mb-4">Sản phẩm gợi ý</h4>

        @php
            $groups = [
                'by_price' => 'Gợi ý theo mức giá',
                'by_view' => 'Dựa trên sản phẩm đã xem',
                'by_search' => 'Dựa trên tìm kiếm gần đây',
                'trending' => 'Sản phẩm đang hot trong tuần',
            ];
        @endphp

        @foreach (['Gợi ý cho bạn' => $suggestions['unique']] as $key => $products)
            @if ($products->isNotEmpty())
                <div class="mb-5">
                    <h5 class="mb-3">{{ $groups[$key] ?? ucfirst($key) }}</h5>

                    <div class="row row-cols-2 row-cols-sm-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6 g-3">
                        @foreach ($products as $product)
                            <div class="col" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="product-card">
                                    <div class="product-media">
                                        <a href="{{ route('product.detail', $product->slug) }}"
                                            class="d-block w-100 h-100 text-center"
                                            onclick="incrementView('{{ $product->id }}')"
                                            data-product-id="{{ $product->id }}">
                                            @php
                                                $defaultImage = asset('uploads/default/default.jpg');
                                                $variantImage = null;
                                                $defaultVariant = $product->variants->first();

                                                if ($defaultVariant && $defaultVariant->images) {
                                                    $images = json_decode($defaultVariant->images, true);
                                                    if (!empty($images[0])) {
                                                        $variantImage = asset($images[0]);
                                                    }
                                                }

                                                if (!$variantImage) {
                                                    $otherVariant = $product->variants->skip(1)->first();
                                                    if ($otherVariant && $otherVariant->images) {
                                                        $images = json_decode($otherVariant->images, true);
                                                        if (!empty($images[0])) {
                                                            $variantImage = asset($images[0]);
                                                        }
                                                    }
                                                }

                                                $variant = $defaultVariant;
                                            @endphp
                                            <img src="{{ $variantImage ?? $defaultImage }}" class="product-img"
                                                alt="{{ $product->name }}">
                                        </a>

                                        <div class="product-tools">
                                            <span class="tool-btn" title="Thêm vào so sánh"
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
                                                onclick="event.preventDefault(); showQuickView({{ $product->id }})">
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
                                            @if ($variant)
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

                                        <div
                                            class="product-rating-row d-flex align-items-center justify-content-center">
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
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>
    <style>
        .product-slider {
            overflow-x: auto;
            white-space: nowrap;
            scroll-snap-type: x mandatory;
        }

        .product-slider::-webkit-scrollbar {
            display: none;
        }
    </style>

    <script>
        // Fallback Toast
        if (typeof window.showToast !== 'function') {
            window.showToast = function(message, type = 'success') {
                if (typeof Swal !== 'undefined') {
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
                } else {
                    alert(message);
                }
            }
        }

        // Fallback Wishlist
        if (typeof window.toggleWishlist !== 'function') {
            window.toggleWishlist = async function(productId, url) {
                try {
                    const csrfEl = document.querySelector('meta[name="csrf-token"]');
                    const csrf = csrfEl ? csrfEl.getAttribute('content') : null;
                    if (!csrf) return showToast('Thiếu CSRF token', 'error');

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            product_id: productId
                        })
                    });
                    const data = await res.json();
                    if (res.ok && data) {
                        showToast(data.message || 'Đã cập nhật yêu thích', data.type || 'success');
                        const icons = document.querySelectorAll(
                            `.icon-heart[onclick*="toggleWishlist('${productId}'"]`);
                        icons.forEach(icon => {
                            if (data.in_wishlist) {
                                icon.classList.add('in-wishlist');
                                icon.title = 'Xóa khỏi yêu thích';
                            } else {
                                icon.classList.remove('in-wishlist');
                                icon.title = 'Thêm vào yêu thích';
                            }
                        });
                    } else {
                        showToast(data?.message || 'Có lỗi xảy ra', 'error');
                    }
                } catch (e) {
                    showToast('Có lỗi xảy ra khi xử lý yêu cầu', 'error');
                }
            }
        }

        // Fallback Compare
        if (typeof window.compareSelected === 'undefined') window.compareSelected = [];
        if (typeof window.compareNames === 'undefined') window.compareNames = [];
        if (typeof window.compareCategory === 'undefined') window.compareCategory = null;

        if (typeof window.addToCompare !== 'function') {
            window.addToCompare = function(productId, productName, categoryId) {
                const isAlready = compareSelected.includes(productId);
                if (isAlready) {
                    const idx = compareSelected.indexOf(productId);
                    compareSelected.splice(idx, 1);
                    compareNames.splice(idx, 1);
                    showToast('Đã bỏ chọn ' + productName + ' khỏi so sánh', 'info');
                    if (compareSelected.length === 0) compareCategory = null;
                } else {
                    if (compareSelected.length >= 4) return showToast('Chỉ được chọn tối đa 4 sản phẩm!', 'error');
                    if (compareSelected.length > 0 && compareCategory != categoryId) {
                        return showToast('Vui lòng chỉ chọn sản phẩm cùng danh mục!', 'error');
                    }
                    compareSelected.push(productId);
                    compareNames.push(productName);
                    if (compareSelected.length === 1) compareCategory = categoryId;
                    showToast('Đã thêm ' + productName + ' vào so sánh', 'success');
                }
                if (typeof updateCompareButton === 'function') updateCompareButton();
            }
        }

        // Fallback Quick View
        if (typeof window.showQuickView !== 'function') {
            window.showQuickView = function(productId) {
                // Điều hướng đến link trong thẻ a gần nhất bên trong card
                try {
                    const tool = event && event.currentTarget ? event.currentTarget : null;
                    const card = tool ? tool.closest('.product-card') : null;
                    const link = card ? card.querySelector('a[href]') : null;
                    if (link) window.location.href = link.getAttribute('href');
                } catch (e) {
                    // Bỏ qua
                }
            }
        }

        if (typeof window.showLoginPrompt !== 'function') {
            window.showLoginPrompt = function() {
                showToast('Vui lòng đăng nhập để thêm vào yêu thích', 'warning');
            }
        }

        // Inject Compare Button + fallback handlers
        if (typeof window.updateCompareButton !== 'function') {
            window.updateCompareButton = function() {
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
        }

        if (typeof window.goToCompare !== 'function') {
            window.goToCompare = function() {
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
        }

        (function ensureCompareButton() {
            if (!document.getElementById('compareButton')) {
                const div = document.createElement('div');
                div.id = 'compareButton';
                div.setAttribute('onclick', 'goToCompare()');
                div.style.cssText =
                    'display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:9999; background:#007bff; color:white; padding:15px 25px; border-radius:25px; box-shadow:0 4px 12px rgba(0,123,255,0.3); cursor:pointer; transition:all 0.3s ease;';
                div.innerHTML =
                    '<i class="fa-solid fa-code-compare me-2"></i> <span id="compareButtonText">So sánh ngay</span> <span id="compareCount" class="badge bg-light text-dark ms-2">0</span>';
                document.body.appendChild(div);
            }
        })();
    </script>

    <style>
        .product-card .product-title {
            text-align: center !important;
        }
    </style>
