@extends('client.layouts.app')
@section('title', 'Danh sách yêu thích - Apple Store')

@section('content')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Thêm Font Awesome cho các icon -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Thêm AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Thêm SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div class="apple-wishlist-container" style="padding-top: 105px;">
        <header class="apple-wishlist-header">
            <div class="container">
                <h1><i class="bi bi-heart-fill"></i> Danh sách yêu thích</h1>
            </div>
        </header>

        <main class="apple-wishlist-main">
            @if ($wishlists->isEmpty())
                <div class="empty-wishlist">
                    <i class="bi bi-heart"></i>
                    <h3>Danh sách trống</h3>
                    <p>Bạn chưa thêm sản phẩm nào vào wishlist</p>
                </div>
            @else
                <!-- Start Wishlist Section -->
                <div class="product-section" data-aos="fade-up">
                    <div class="container">
                        <div class="row">
                            <!-- Start Wishlist Items -->
                            <div class="col-lg-9">
                                <div class="product-slider">
                                    <div class="product-row">
                                        @forelse ($wishlists as $wishlist)
                                            @php
                                                $defaultImage = asset('Uploads/default/default.jpg');
                                                $variantImage = null;
                                                $defaultVariant = $wishlist->variants->first();

                                                if ($defaultVariant && $defaultVariant->images) {
                                                    $images = json_decode($defaultVariant->images, true);
                                                    if (!empty($images[0])) {
                                                        $variantImage = asset($images[0]);
                                                    }
                                                }

                                                if (!$variantImage) {
                                                    $otherVariant = $wishlist->variants->skip(1)->first();
                                                    if ($otherVariant && $otherVariant->images) {
                                                        $images = json_decode($otherVariant->images, true);
                                                        if (!empty($images[0])) {
                                                            $variantImage = asset($images[0]);
                                                        }
                                                    }
                                                }
                                            @endphp

                                            <div class="col-md-4 col-md-4-custom mb-4" data-aos="fade-up"
                                                data-aos-delay="{{ $loop->iteration * 100 }}">
                                                <div class="product-card">
                                                    <div class="product-media">
                                                        <a href="{{ route('product.detail', $wishlist->slug) }}"
                                                            class="d-block w-100 h-100 text-center"
                                                            onclick="incrementView('{{ $wishlist->id }}')">
                                                            <img src="{{ $variantImage ?? $defaultImage }}"
                                                                class="product-img" alt="{{ $wishlist->name }}">
                                                        </a>
                                                    </div>

                                                    <div class="product-tools">
                                                        {{-- <span class="tool-btn" title="Thêm vào giỏ hàng"
                                                            onclick="addToCart('{{ $wishlist->id }}', '{{ $defaultVariant->id ?? '' }}')">
                                                            <i class="fas fa-cart-plus"></i>
                                                        </span> --}}
                                                        <a href="{{ route('product.detail', $wishlist->slug) }}"
                                                            class="tool-btn" title="Xem chi tiết"
                                                            onclick="incrementView('{{ $wishlist->id }}')">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <span class="tool-btn icon-trash icon-remove-from-wishlist"
                                                            title="Xóa khỏi yêu thích"
                                                            onclick="event.preventDefault(); toggleWishlist('{{ $wishlist->id }}', '{{ route('wishlist.toggle', $wishlist) }}', this)">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </span>
                                                    </div>

                                                    <div class="product-body">
                                                        <a href="{{ route('product.detail', $wishlist->slug) }}"
                                                            class="text-decoration-none">
                                                            <h3 class="product-title mb-1">{{ $wishlist->name }}</h3>
                                                        </a>

                                                        <div class="product-price-row">
                                                            @if ($defaultVariant)
                                                                @if ($defaultVariant->discount_price)
                                                                    <strong
                                                                        class="product-price">{{ number_format($defaultVariant->discount_price) }}đ</strong>
                                                                    <span
                                                                        class="old-price"><del>{{ number_format($defaultVariant->selling_price) }}đ</del></span>
                                                                @else
                                                                    <strong
                                                                        class="product-price">{{ number_format($defaultVariant->selling_price) }}đ</strong>
                                                                @endif
                                                            @endif
                                                        </div>

                                                        <div class="product-rating-row">
                                                            @php
                                                                $rating = $wishlist->reviews->avg('rating') ?? 0;
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
                                                                class="sold-count">({{ number_format($wishlist->sold_count ?? 0) }}
                                                                đã bán)</span>
                                                        </div>
                                                    </div>

                                                    <form action="{{ route('cart.add') }}" method="POST"
                                                        style="display: none;" id="add-cart-form-{{ $wishlist->id }}">
                                                        @csrf
                                                        <input type="hidden" name="product_id"
                                                            value="{{ $wishlist->id }}">
                                                        <input type="hidden" name="variant_id"
                                                            value="{{ $defaultVariant->id ?? '' }}">
                                                        <input type="hidden" name="quantity" value="1">
                                                    </form>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="text-center">Bạn chưa có sản phẩm nào trong danh sách yêu thích.
                                                </p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <!-- End Wishlist Items -->
                        </div>
                    </div>
                </div>
            @endif
        </main>
    </div>

    <script>
        // Khởi tạo AOS
        AOS.init({
            duration: 800,
            once: false,
        });

        // Custom styling cho SweetAlert2
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        function incrementView(productId) {
            fetch(`/increment-view/${productId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Đã tăng lượt xem');
                    }
                });
        }

        function addToCart(productId, variantId) {
            if (!variantId) {
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không tìm thấy biến thể sản phẩm!'
                });
                return;
            }

            const form = document.getElementById(`add-cart-form-${productId}`);
            const formData = new FormData(form);

            fetch('{{ route('cart.add') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Toast.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message || 'Đã thêm sản phẩm vào giỏ hàng!'
                        });
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Không thể thêm sản phẩm vào giỏ hàng!'
                        });
                    }
                })
                .catch(error => {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi thêm vào giỏ hàng!'
                    });
                });
        }

        async function toggleWishlist(productId, url, iconElement) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Lỗi hệ thống, vui lòng thử lại!'
                });
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
                    Toast.fire({
                        icon: data.type,
                        title: data.type === 'success' ? 'Thành công' : 'Thông báo',
                        text: data.message
                    });
                    if (data.type === 'success' && !data.in_wishlist) {
                        const productCard = iconElement.closest('.col-md-4, .col-md-4-custom');
                        if (productCard) productCard.remove();
                        // Check if wishlist is empty
                        if (document.querySelectorAll('.product-row .col-md-4, .product-row .col-md-4-custom')
                            .length === 0) {
                            document.querySelector('.apple-wishlist-main').innerHTML = `
                                <div class="empty-wishlist">
                                    <i class="bi bi-heart"></i>
                                    <h3>Danh sách trống</h3>
                                    <p>Bạn chưa thêm sản phẩm nào vào wishlist</p>
                                </div>
                            `;
                        }
                    }
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Đã xảy ra lỗi, vui lòng thử lại!'
                    });
                }
            } catch (error) {
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Đã xảy ra lỗi: ' + (error.message || 'Unknown error')
                });
            }
        }
    </script>

    <style>
        /* Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Container chính */
        .apple-wishlist-container {
            max-width: 1200px;
            margin: 0 auto;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Header cố định */
        .apple-wishlist-header {
            margin-top: 120px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            z-index: 998;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .apple-wishlist-header h1 {
            font-size: 24px;
            font-weight: 600;
            color: #000;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .apple-wishlist-header i {
            color: #ff3b30;
        }

        /* Nội dung chính */
        .apple-wishlist-main {
            padding: 20px;
        }

        /* Trạng thái trống */
        .empty-wishlist {
            text-align: center;
            padding: 50px 20px;
            margin-top: 62px;
        }

        .empty-wishlist i {
            font-size: 60px;
            color: #86868b;
            margin-bottom: 20px;
        }

        .empty-wishlist h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .empty-wishlist p {
            color: #86868b;
            margin-bottom: 25px;
        }

        /* CSS cho product-card */
        .product-slider {
            margin: 0 -15px;
        }

        .product-row {
            display: flex;
            margin: 0;
            justify-content: flex-start;
        }

        .col-md-4-custom {
            padding-left: 10px;
            padding-right: 10px;
        }

        .product-card {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-media {
            position: relative;
            overflow: hidden;
            width: 100%;
            height: 220px;
            /* hoặc 200px, tuỳ ý bạn */
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-img {
            transform: scale(1.05);
        }

        .product-tools {
            position: absolute;
            top: 10px;
            right: 10px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .product-card:hover .product-tools {
            opacity: 1;
        }

        .tool-btn {
            padding: 8px;
            background: #f8f9fa;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tool-btn:hover {
            background: #e9ecef;
            color: #007bff;
        }

        .icon-heart.in-wishlist {
            color: #ff3b30;
        }

        .product-body {
            padding: 15px;
            text-align: center;
        }

        .product-title {
            font-size: 1.1rem;
            margin: 10px 0;
            color: #333;
        }

        .product-price-row {
            margin: 10px 0;
        }

        .product-price {
            font-weight: 600;
            color: #000;
        }

        .old-price {
            color: #86868b;
            font-size: 0.9rem;
        }

        .product-rating-row {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .stars i {
            color: #f8d64e;
        }

        .sold-count {
            color: #86868b;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .product-row {
                justify-content: center;
            }

            .col-md-4-custom {
                width: 100%;
                max-width: 300px;
                padding-left: 15px;
                padding-right: 15px;
            }

            .apple-wishlist-container {
                padding-top: 70px;
            }

            .apple-wishlist-header h1 {
                font-size: 20px;
            }
        }

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
    </style>
@endsection
