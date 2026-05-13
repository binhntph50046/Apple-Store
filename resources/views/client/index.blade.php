@extends('client.layouts.app')

@section('title', 'Trang chủ - Apple Store')


@section('banner')
    <!-- Start Hero Section -->
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach ($banners as $index => $banner)
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $index }}"
                    class="{{ $index == 0 ? 'active' : '' }}" aria-current="{{ $index == 0 ? 'true' : 'false' }}"
                    aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach ($banners as $index => $banner)
                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}"
                    style="background-image: url('{{ asset($banner->image) }}');">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-9">
                                <div class="intro-excerpt">
                                    <h1>{{ $banner->title }}</h1>
                                    <p class="mb-4">{{ $banner->description }}</p>
                                    <p class="d-flex align-items-center gap-2">
                                        <a href="{{ route('shop') }}"
                                            class="btn btn-secondary text-nowrap me-2 d-inline-block">Mua ngay</a>
                                        <a href="{{ route('blog') }}" class="btn btn-white-outline d-inline-block">Khám
                                            phá</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
@endsection

@section('content')
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Start We Help Section -->
    <div class="we-help-section" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-7 mb-5 mb-lg-0" data-aos="fade-right">
                    <div class="imgs-grid">
                        <div class="grid grid-1"><img
                                src="https://product.hstatic.net/1000259254/product/apple_watch_series_6_gps-3_f473dbbd7a8a4eebb713eb4737a4bb3c_grande.jpg"
                                alt="Untree.co"></div>
                        <div class="grid grid-2"><img
                                src="https://store.storeimages.cdn-apple.com/1/as-images.apple.com/is/mac-card-100-customize-202503_FMT_WHH?wid=660&hei=492&fmt=png-alpha&.v=WGVJR1JzeVlHQndDQ0hPeUcxZEhIVVI0Q213c2R4MVpIbnhYSW0yemZZSjhCbGlKVjM2L2p6MTB1VFhaSTNQZTFpQTgzYnRLU3FIbktmUFFaOW9RVVI3OWM1K2xYY1BvNUdGM3NrY09OUVVBUlVVbndFOUhuNmlVSkZDVGZlRGc"
                                alt="Untree.co"></div>
                        <div class="grid grid-3"><img
                                src="https://www.apple.com/newsroom/images/2023/09/apple-debuts-iphone-15-and-iphone-15-plus/article/Apple-iPhone-15-lineup-hero-geo-230912_inline.jpg.large.jpg"
                                alt="Untree.co"></div>
                    </div>
                </div>
                <div class="col-lg-5 ps-lg-5" data-aos="fade-left" data-aos-delay="100">
                    <h2 class="section-title mb-4">Chúng Tôi Giúp Bạn Sở Hữu Thiết Bị Apple Hiện Đại</h2>
                    <p>Chúng tôi không chỉ bán sản phẩm – chúng tôi mang đến giải pháp công nghệ toàn diện cho cuộc sống
                        hiện đại. Từ iPhone đến MacBook, tất cả đều được chọn lọc kỹ lưỡng để mang đến trải nghiệm đỉnh cao.
                    </p>

                    <ul class="list-unstyled custom-list my-4">
                        <li>Sản phẩm Apple chính hãng, bảo hành toàn quốc</li>
                        <li>Đa dạng mẫu mã từ iPhone, iPad, MacBook,...</li>
                        <li>Giao hàng nhanh chóng, hỗ trợ tận nơi</li>
                        <li>Hỗ trợ kỹ thuật & tư vấn tận tình 24/7</li>
                    </ul>
                    <p><a href="{{ route('shop') }}" class="btn">Khám Phá Ngay</a></p>
                </div>
            </div>
        </div>
    </div>
    <!-- End We Help Section -->

    <!-- Start Most Popular Products Section -->
    <div class="most-popular-section" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-8">
                    <div class="product-slider">
                        @foreach ($mostViewedProducts as $product)
                            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="product-card">
                                    <div class="product-media">
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
                                        <a href="{{ route('product.detail', $product->slug) }}" class="d-block w-100 h-100 text-center"
                                            onclick="incrementView('{{ $product->id }}')" data-product-id="{{ $product->id }}">
                                            <img src="{{ asset($mainImage) }}" class="product-img" alt="{{ $product->name }}">
                                        </a>
                                        <div class="product-tools">
                                            <span class="tool-btn" title="So sánh"
                                                onclick="event.preventDefault(); addToCompare('{{ $product->id }}', '{{ $product->name }}', '{{ $product->category_id }}')">
                                                <i class="fa-solid fa-code-compare"></i>
                                            </span>
                                            @auth
                                                <span class="tool-btn icon-heart icon-add-to-wishlist {{ in_array($product->id, $wishlistProductIds) ? 'in-wishlist' : '' }}"
                                                    onclick="event.preventDefault(); toggleWishlist('{{ $product->id }}', '{{ route('wishlist.toggle', $product) }}', this)"
                                                    title="{{ in_array($product->id, $wishlistProductIds) ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                                                    <i class="fas fa-heart"></i>
                                                </span>
                                            @else
                                                <span class="tool-btn icon-heart icon-add-to-wishlist"
                                                    onclick="event.preventDefault(); showLoginPrompt()"
                                                    title="Đăng nhập để thêm vào yêu thích">
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
                                        <a href="{{ route('product.detail', $product->slug) }}" class="text-decoration-none">
                                            <h3 class="product-title mb-1">{{ $product->name }}</h3>
                                        </a>
                                        <div class="product-price-row">
                                        @if ($product->variants->isNotEmpty())
                                                @php $variant = $product->variants->first(); @endphp
                                            @if ($variant->discount_price)
                                                    <strong class="product-price">{{ number_format($variant->discount_price) }}đ</strong>
                                                    <span class="old-price"><del>{{ number_format($variant->selling_price) }}đ</del></span>
                                            @else
                                                    <strong class="product-price">{{ number_format($variant->selling_price) }}đ</strong>
                                            @endif
                                        @endif
                                        </div>
                                        @php
                                            $ratedVariants = $product->variants->filter(function ($variant) {
                                                return isset($variant->reviews_count)
                                                    ? $variant->reviews_count > 0
                                                    : $variant->reviews && $variant->reviews->count() > 0;
                                            });
                                            $avgRating = null;
                                            if ($ratedVariants->count() > 0) {
                                                $avgRating = $ratedVariants->avg(function ($variant) {
                                                    return isset($variant->avg_rating)
                                                        ? $variant->avg_rating
                                                        : ($variant->reviews
                                                            ? $variant->reviews->avg('rating')
                                                            : 0);
                                                });
                                            }
                                            $avgRating = $avgRating ? round($avgRating, 1) : 5;
                                        @endphp
                                        <div class="product-rating-row">
                                            <div class="stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= floor($avgRating))
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif ($i - $avgRating < 1)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                            <span class="sold-count">({{ number_format($product->sold_count ?? 0) }} đã bán)</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="popular-content" data-aos="fade-left" data-aos-delay="400">
                        <h2 class="section-title">Sản Phẩm Thịnh Hành</h2>
                        <p class="mb-4">Khám phá những thiết bị Apple được ưa chuộng nhất hiện nay – nơi hội tụ đỉnh cao
                            công nghệ và thiết kế tinh tế. Đáp ứng mọi nhu cầu từ giải trí đến công việc.</p>

                        <div class="popular-features">
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Công Nghệ Đột Phá</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Thiết Kế Cao Cấp</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Đồng Bộ Hoàn Hảo</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Bảo Hành Toàn Cầu</span>
                            </div>
                        </div>

                        <p><a href="{{ route('shop') }}" class="btn">Xem Tất Cả Sản Phẩm</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Most Popular Products Section -->

    <!-- Start Product Section -->
    <div class="product-section" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-md-12 col-lg-3 mb-5 mb-lg-0" data-aos="fade-right" data-aos-delay="100">
                    <h2 class="mb-4 section-title">Thiết Kế Hướng Tới Tương Lai</h2>
                    <p class="mb-4">Các sản phẩm Apple của chúng tôi được chế tác tinh xảo, kết hợp giữa chất liệu cao
                        cấp và công nghệ tiên tiến. Mỗi chi tiết đều được chăm chút để mang đến trải nghiệm vượt xa một
                        thiết bị – là tuyên ngôn phong cách sống số hiện đại.</p>
                    <p><a href="{{ route('shop') }}" class="btn">Khám Phá Ngay</a></p>
                </div>

                <div class="col-lg-9">
                    <div class="product-slider">
                        @foreach ($latestProducts as $product)
                            <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                                <div class="product-card">
                                    <div class="product-media">
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
                                        <a class="d-block w-100 h-100 text-center" href="{{ route('product.detail', $product->slug) }}"
                                            onclick="incrementView('{{ $product->id }}')" data-product-id="{{ $product->id }}">
                                            <img src="{{ asset($mainImage) }}" class="product-img" alt="{{ $product->name }}">
                                        </a>
                                        <div class="product-tools">
                                            <span class="tool-btn" title="So sánh"
                                                onclick="event.preventDefault(); addToCompare('{{ $product->id }}', '{{ $product->name }}', '{{ $product->category_id }}')">
                                                <i class="fa-solid fa-code-compare"></i>
                                            </span>
                                            @auth
                                                <span class="tool-btn icon-heart icon-add-to-wishlist {{ in_array($product->id, $wishlistProductIds) ? 'in-wishlist' : '' }}"
                                                    onclick="event.preventDefault(); toggleWishlist('{{ $product->id }}', '{{ route('wishlist.toggle', $product) }}', this)"
                                                    title="{{ in_array($product->id, $wishlistProductIds) ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' }}">
                                                    <i class="fas fa-heart"></i>
                                                </span>
                                            @else
                                                <span class="tool-btn icon-heart icon-add-to-wishlist"
                                                    onclick="event.preventDefault(); showLoginPrompt()"
                                                    title="Đăng nhập để thêm vào yêu thích">
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
                                        <a href="{{ route('product.detail', $product->slug) }}" class="text-decoration-none">
                                            <h3 class="product-title mb-1" style="height: 33px;line-height: 21px">{{ $product->name }}</h3>
                                        </a>
                                        <div class="product-price-row">
                                        @if ($product->variants->isNotEmpty())
                                                @php $variant = $product->variants->first(); @endphp
                                            @if ($variant->discount_price)
                                                    <strong class="product-price">{{ number_format($variant->discount_price) }}đ</strong>
                                                    <span class="old-price"><del>{{ number_format($variant->selling_price) }}đ</del></span>
                                            @else
                                                    <strong class="product-price">{{ number_format($variant->selling_price) }}đ</strong>
                                            @endif
                                        @endif
                                        </div>
                                        @php
                                            $ratedVariants = $product->variants->filter(function ($variant) {
                                                return isset($variant->reviews_count)
                                                    ? $variant->reviews_count > 0
                                                    : $variant->reviews && $variant->reviews->count() > 0;
                                            });
                                            $avgRating = null;
                                            if ($ratedVariants->count() > 0) {
                                                $avgRating = $ratedVariants->avg(function ($variant) {
                                                    return isset($variant->avg_rating)
                                                        ? $variant->avg_rating
                                                        : ($variant->reviews
                                                            ? $variant->reviews->avg('rating')
                                                            : 0);
                                                });
                                            }
                                            $avgRating = $avgRating ? round($avgRating, 1) : 5;
                                        @endphp
                                        <div class="product-rating-row">
                                            <div class="stars">
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= floor($avgRating))
                                                    <i class="fas fa-star text-warning"></i>
                                                @elseif ($i - $avgRating < 1)
                                                    <i class="fas fa-star-half-alt text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-warning"></i>
                                                @endif
                                            @endfor
                                        </div>
                                            <span class="sold-count">({{ number_format($product->sold_count ?? 0) }} đã bán)</span>
                                    </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Product Section -->

    <!-- Start Why Choose Us Section -->
    <div class="why-choose-section" data-aos="fade-up">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-lg-6">
                    <h2 class="section-title">Vì Sao Bạn Chọn Chúng Tôi</h2>
                    <p>Chúng tôi cam kết mang đến trải nghiệm mua sắm Apple chính hãng dễ dàng, nhanh chóng và đáng tin cậy.
                        Dịch vụ tận tâm, sản phẩm chất lượng, giá cả hợp lý – tất cả đều vì bạn.</p>

                    <div class="row my-5">
                        <div class="col-6 col-md-6" data-aos="fade-up" data-aos-delay="100">
                            <div class="feature">
                                <div class="icon">
                                    <img src="images/truck.svg" alt="Giao hàng nhanh" class="img-fluid">
                                </div>
                                <h3>Giao Hàng Nhanh & Miễn Phí</h3>
                                <p>Miễn phí giao hàng toàn quốc với tốc độ nhanh chóng, đảm bảo sản phẩm Apple đến tay bạn
                                    an toàn và đúng hẹn.</p>
                            </div>
                        </div>

                        <div class="col-6 col-md-6" data-aos="fade-up" data-aos-delay="200">
                            <div class="feature">
                                <div class="icon">
                                    <img src="images/bag.svg" alt="Mua sắm dễ dàng" class="img-fluid">
                                </div>
                                <h3>Mua Sắm Dễ Dàng</h3>
                                <p>Website thân thiện, hỗ trợ tìm kiếm nhanh chóng các sản phẩm Apple chính hãng như iPhone,
                                    MacBook, AirPods và nhiều hơn nữa.</p>
                            </div>
                        </div>

                        <div class="col-6 col-md-6" data-aos="fade-up" data-aos-delay="300">
                            <div class="feature">
                                <div class="icon">
                                    <img src="images/support.svg" alt="Hỗ trợ 24/7" class="img-fluid">
                                </div>
                                <h3>Hỗ Trợ Khách Hàng 24/7</h3>
                                <p>Đội ngũ tư vấn luôn sẵn sàng hỗ trợ bạn mọi lúc mọi nơi, từ chọn sản phẩm đến bảo hành và
                                    kỹ thuật.</p>
                            </div>
                        </div>

                        <div class="col-6 col-md-6" data-aos="fade-up" data-aos-delay="400">
                            <div class="feature">
                                <div class="icon">
                                    <img src="images/return.svg" alt="Đổi trả dễ dàng" class="img-fluid">
                                </div>
                                <h3>Đổi Trả Nhanh Chóng</h3>
                                <p>Hỗ trợ đổi trả trong 7 ngày nếu sản phẩm gặp lỗi từ nhà sản xuất hoặc không đúng mô tả.
                                    Mua sắm không lo rủi ro.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="img-wrap" data-aos="zoom-in">
                        <img src="https://img.freepik.com/free-photo/business-man-holding-clipboard-with-why-choose-us-question_23-2148932313.jpg?semt=ais_hybrid&w=740"
                            alt="Image" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Why Choose Us Section -->

    <!-- Start Testimonial Slider -->
    @if ($topRatedVariants->count() > 0)
        <section class="top-rated-products py-5" data-aos="fade-up">
            <div class="container">
                <div class="text-center mb-4">
                    <h2 class="section-title">Các Đánh Giá Của Sản Phẩm</h2>
                </div>
                <div class="position-relative">
                    <div class="top-rated-slider py-2">
                        @php
                            if (!function_exists('getImagesArray')) {
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
                            }
                        @endphp

                        @foreach ($topRatedVariants as $variant)
                            @php
                                $images = getImagesArray($variant->images);
                                $imageUrl = !empty($images[0]) ? $images[0] : 'uploads/default/default.jpg';
                            @endphp

                            <div class="product-card py-1 px-4">
                                <div class="border p-4 h-100 text-center bg-white"
                                    style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.21);">
                                    <img src="{{ asset($imageUrl) }}" alt="{{ $variant->product->name }}"
                                        class="img-fluid mb-3"
                                        style="height: 220px; object-fit: contain; display: inline-block; margin: 0 auto;">
                                    <small class="text-muted d-block mb-2"> {{ $variant->name }}</small>
                                    @if ($variant->reviews_count > 0)
                                        <div class="text-warning fw-bold mb-2">
                                            {{ number_format($variant->avg_rating, 1) }} ★
                                            <small class="text-secondary">({{ $variant->reviews_count }} đánh giá)</small>
                                        </div>
                                    @else
                                        <small class="text-muted">Chưa có đánh giá</small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    <!-- End Testimonial Slider -->

    <!-- Start Blog Section -->
    @if ($latestBlogs->count() > 0)
        <div class="blog-section" data-aos="fade-up">
            <div class="container">
                <div class="row mb-5">
                    <div class="col-md-6" data-aos="fade-right">
                        <h2 class="section-title">Bài viết gần đây</h2>
                    </div>
                    <div class="col-md-6 text-start text-md-end" data-aos="fade-left">
                        <a href="{{ route('blog') }}" class="more">Xem tất cả</a>
                    </div>
                </div>

                <div class="row">
                    @foreach ($latestBlogs as $blog)
                        <div class="col-12 col-sm-6 col-md-4 mb-4 mb-md-0" data-aos="fade-up"
                            data-aos-delay="{{ $loop->iteration * 100 }}">
                            <div class="post-entry">
                                <a href="{{ route('blog.show', $blog->slug) }}" class="post-thumbnail">
                                    <img src="{{ asset($blog->image ?? 'uploads/default/default.jpg') }}"
                                        alt="{{ $blog->title }}" class="img-fluid">
                                </a>
                                <div class="post-content-entry">
                                    <h3 style="height: 33px;line-height: 21px"><a
                                            href="{{ route('blog.show', $blog->slug) }}">{{ Str::limit($blog->title, 90) }}</a>
                                    </h3>
                                    <div class="meta">
                                        <span>{{ $blog->created_at->diffForHumans() }}</span>
                                        {{-- <span>on <a href="#">{{ $blog->created_at->format('M d, Y') }}</a></span> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    <!-- End Blog Section -->

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

        function showLoginPrompt() {
            showToast('Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích.', 'error');
        }

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
                showToast('Đã bỏ chọn ' + productName + ' khỏi so sánh', 'info');

                // If the list becomes empty, reset the category
                if (compareSelected.length === 0) {
                    compareCategory = null;
                }
            } else {
                // Add product
                if (compareSelected.length >= 4) {
                    showToast('Chỉ được chọn tối đa 4 sản phẩm để so sánh!', 'error');
                    return;
                }

                // Check category
                if (compareSelected.length > 0 && compareCategory != categoryId) {
                    showToast('Vui lòng chỉ chọn các sản phẩm trong cùng một danh mục!', 'error');
                    return;
                }

                compareSelected.push(productId);
                compareNames.push(productName);

                // Set category if it's the first product being added
                if (compareSelected.length === 1) {
                    compareCategory = categoryId;
                }
                showToast('Đã thêm ' + productName + ' vào so sánh', 'success');
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
                showToast('Vui lòng chọn từ 2 đến 4 sản phẩm để so sánh!', 'error');
            }
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

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('.product-slider, .latest-products-slider, .top-rated-slider').slick({
                dots: true,
                infinite: true,
                speed: 300,
                slidesToShow: 3,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                arrows: true,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });
        });

        // Hàm hiển thị toast message
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

        // Hàm xử lý thêm/xóa yêu thích
        async function toggleWishlist(productId, url, iconElement) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Hiển thị thông báo
                    showToast(data.message, data.type || 'success');

                    // Cập nhật trạng thái icon
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
                } else {
                    showToast(data.message || 'Có lỗi xảy ra', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra khi xử lý yêu cầu', 'error');
            }
        }

        function showLoginPrompt() {
            showToast('Vui lòng đăng nhập để thêm sản phẩm vào danh sách yêu thích', 'warning');
        }
    </script>
@endsection

<style>
    /* Các style trước giữ nguyên */
    .product-slider {
        margin: 0 -15px;
    }

    .product-row {
        display: flex !important;
        flex-wrap: nowrap;
        justify-content: space-between;
        white-space: nowrap;
    }

    .product-slider .slick-slide {
        padding: 0 15px;
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

    .product-item {
        display: block;
        text-decoration: none;
        color: inherit;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 95%;
    }

    .product-item:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .product-item:hover .product-thumbnail img {
        transform: scale(1.001);
        transition: transform 0.2s ease;
    }

    .product-title {
        font-size: 1.1rem;
        margin: 10px 0;
        color: #333;
        min-height: 44px;
        max-height: 44px;
        line-height: 22px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* Số dòng tối đa */
        -webkit-box-orient: vertical;
        white-space: normal;
            text-align: center; 
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

    .product-slider .slick-prev,
    .product-slider .slick-next {
        z-index: 1;
    }

    .product-slider .slick-prev {
        left: 20px;
    }

    .product-slider .slick-next {
        right: -20px;
    }

    .product-slider .slick-prev:before,
    .product-slider .slick-next:before {
        right: 10px;
        top: -35px;
    }

    .product-slider .slick-dots {
        bottom: -30px;
    }

    .product-slider .slick-dots li button:before {
        font-size: 12px;
    }

    .latest-products-slider {
        margin: 0 -15px;
    }

    .latest-products-slider .slick-slide {
        padding: 0 15px;
    }

    .latest-products-slider .slick-prev,
    .latest-products-slider .slick-next {
        z-index: 1;
        width: 40px;
        height: 40px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .latest-products-slider .slick-prev {
        left: -20px;
    }

    .latest-products-slider .slick-next {
        right: -20px;
    }

    .latest-products-slider .slick-prev:before,
    .latest-products-slider .slick-next:before {
        font-size: 20px;
        color: #333;
    }

    .latest-products-slider .slick-dots {
        bottom: -30px;
    }

    .latest-products-slider .slick-dots li button:before {
        font-size: 12px;
    }

    @media (max-width: 768px) {
        .product-row {
            justify-content: center;
        }

        .col-md-4 {
            width: 100%;
            max-width: 300px;
        }
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

    /* Style cho FAB và Panel Chat */
    .fab {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1050;
    }

    .chat-panel {
        position: fixed;
        bottom: 80px;
        right: 20px;
        width: 300px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1040;
        padding: 15px;
        transform: translateY(100%);
        transition: transform 0.3s ease-in-out;
    }

    .chat-panel.open {
        transform: translateY(0);
    }

    /* Style cho Compare Section */
    .compare-section {
        min-height: 70vh;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #333;
    }

    .table {
        background-color: #fff;
        border-radius: 8px;
        overflow: hidden;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 1rem;
        text-align: center;
    }

    .table th {
        font-weight: 600;
    }

    .ai-advice {
        border-left: 4px solid #007bff;
    }

    .ai-advice .lead {
        font-size: 1.1rem;
        color: #555;
    }

    @media (max-width: 768px) {
        .table {
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 1.5rem;
        }


    }

    .author-pic img {
        border-radius: 8px;
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
            showToast('Đã bỏ chọn ' + productName + ' khỏi so sánh', 'info');

            // If the list becomes empty, reset the category
            if (compareSelected.length === 0) {
                compareCategory = null;
            }
        } else {
            // Add product
            if (compareSelected.length >= 4) {
                showToast('Chỉ được chọn tối đa 4 sản phẩm để so sánh!', 'error');
                return;
            }

            // Check category
            if (compareSelected.length > 0 && compareCategory != categoryId) {
                showToast('Vui lòng chỉ chọn các sản phẩm trong cùng một danh mục!', 'error');
                return;
            }

            compareSelected.push(productId);
            compareNames.push(productName);

            // Set category if it's the first product being added
            if (compareSelected.length === 1) {
                compareCategory = categoryId;
            }
            showToast('Đã thêm ' + productName + ' vào so sánh', 'success');
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
            showToast('Vui lòng chọn từ 2 đến 4 sản phẩm để so sánh!', 'error');
        }
    }

    // ... (các hàm khác giữ nguyên như trước)
</script>

<!-- Nút So sánh nổi -->
<div id="compareButton"
    style="display:none; position:fixed; bottom:20px; left:50%; transform:translateX(-50%); z-index:9999; background:#007bff; color:white; padding:15px 25px; border-radius:25px; box-shadow:0 4px 12px rgba(0,123,255,0.3); cursor:pointer; transition:all 0.3s ease;"
    onclick="goToCompare()">
    <i class="fa-solid fa-code-compare me-2"></i>
    <span id="compareButtonText">So sánh ngay</span>
    <span id="compareCount" class="badge bg-light text-dark ms-2">0</span>
</div>


@endsection
