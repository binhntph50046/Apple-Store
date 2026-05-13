@extends('client.layouts.app')
@section('title', 'Đánh giá đơn hàng')

@section('content')

    {{-- Import Tailwind CSS CDN and FontAwesome for icons --}}
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="container mx-auto max-w-5xl px-4 py-16 bg-gray-50 min-h-screen">
        <div style="margin-top: 80px" class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fa-solid fa-star text-yellow-400 mr-2"></i>
                Đánh giá đơn hàng #{{ $order->id }}
            </h2>
            <p class="text-gray-600 text-sm">Vui lòng chia sẻ cảm nhận của bạn về các sản phẩm để giúp chúng tôi cải thiện
                chất lượng dịch vụ!</p>
        </div>

        @php
            // Hàm lấy mảng ảnh từ biến thể (giữ nguyên từ mã gốc)
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

        @foreach ($items as $item)
            @php
                $variant = $item->variant;
                if (!$variant) {
                    continue;
                }
                $review = $reviews->get($variant->id);

                // Lấy ảnh đầu tiên của biến thể
                $variantImages = $variant && $variant->images ? getImagesArray($variant->images) : [];
                $firstImage = isset($variantImages[0])
                    ? (str_starts_with($variantImages[0], 'http')
                        ? $variantImages[0]
                        : asset($variantImages[0]))
                    : asset('uploads/default/default.jpg');
            @endphp

            <div class="bg-white rounded-2xl shadow-md mb-6 p-6 hover:shadow-lg transition-shadow duration-300">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between mb-4">
                    <div class="flex items-start space-x-4">
                        <img src="{{ $firstImage }}" alt="{{ $item->product->name ?? '' }}"
                            class="w-16 h-16 object-cover rounded-md border border-gray-200">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $item->product->name ?? '' }}</h3>
                            <p class="text-sm text-gray-500">Biến thể: {{ $variant->name ?? '' }}</p>
                        </div>
                    </div>
                    @if ($review)
                        <span
                            class="inline-flex items-center bg-green-100 text-green-700 text-xs font-medium px-3 py-1 rounded-full mt-2 md:mt-0">
                            <i class="fa-solid fa-check mr-1"></i> Đã đánh giá
                        </span>
                    @endif
                </div>

                @if ($review)
                    <div class="space-y-3 bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $review->rating)
                                    <i class="fa-solid fa-star text-yellow-400 w-5 h-5"></i>
                                @else
                                    <i class="fa-regular fa-star text-gray-300 w-5 h-5"></i>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm text-gray-600">({{ $review->rating }} sao)</span>
                        </div>
                        <p class="text-gray-700 text-sm">Nội Dung: {{ $review->review }}</p>

                        @if (!empty($review->images) && is_array($review->images))
                            <label for="">Hình ảnh đánh giá: </label>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($review->images as $img)
                                    <a href="{{ asset('storage/' . $img) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $img) }}" alt="Review image"
                                            class="w-20 h-20 object-cover rounded-md border border-gray-200 hover:opacity-80 transition-opacity">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    <form action="{{ route('order.review.store', [$order->id, $variant->id]) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-5 mt-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Chọn số sao <span
                                    class="text-red-500">*</span></label>
                            <div class="flex items-center space-x-1" id="star-rating-{{ $variant->id }}">
                                <input type="hidden" name="rating" id="rating-input-{{ $variant->id }}" value="0"
                                    required>
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-regular fa-star text-gray-400 w-6 h-6 cursor-pointer hover:text-yellow-400 transition-colors"
                                        data-value="{{ $i }}" data-variant="{{ $variant->id }}"></i>
                                @endfor
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung đánh giá</label>
                            <textarea name="review" rows="4" maxlength="1000" placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..."
                                class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Hình ảnh (tối đa 5 ảnh)</label>
                            <div
                                class="relative border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:border-blue-500 transition">
                                <input type="file" name="images[]" multiple accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                <p class="text-sm text-gray-500">Kéo thả hoặc nhấn để tải ảnh lên</p>
                                <p class="text-xs text-gray-400 mt-1">Định dạng: JPG, PNG (tối đa 5MB/ảnh)</p>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition">
                                <i class="fa-solid fa-paper-plane mr-2"></i> Gửi đánh giá
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        @endforeach
    </div>

    <script>
        // Handle star rating clicks
        document.querySelectorAll('[id^="star-rating-"]').forEach(starContainer => {
            const variantId = starContainer.id.replace('star-rating-', '');
            const stars = starContainer.querySelectorAll('.fa-star');
            const ratingInput = document.getElementById(`rating-input-${variantId}`);

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.getAttribute('data-value');
                    ratingInput.value = rating;

                    stars.forEach((s, index) => {
                        s.classList.toggle('fa-solid', index < rating);
                        s.classList.toggle('fa-regular', index >= rating);
                        s.classList.toggle('text-yellow-400', index < rating);
                        s.classList.toggle('text-gray-400', index >= rating);
                    });
                });

                // Hover effect
                star.addEventListener('mouseover', function() {
                    const rating = this.getAttribute('data-value');
                    stars.forEach((s, index) => {
                        if (index < rating) {
                            s.classList.add('text-yellow-400');
                            s.classList.remove('text-gray-400');
                        }
                    });
                });

                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value;
                    stars.forEach((s, index) => {
                        s.classList.toggle('text-yellow-400', index < currentRating);
                        s.classList.toggle('text-gray-400', index >= currentRating);
                    });
                });
            });
        });
    </script>

@endsection
