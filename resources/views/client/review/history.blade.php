{{-- @extends('client.layouts.app')
@section('title', 'Lịch sử đánh giá đơn hàng')

@section('content')
<div style="margin-top: 150px" class="container py-8 px-4 mx-auto max-w-7xl">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Lịch sử đánh giá đơn hàng #{{ $order->id }}</h2>
    @forelse($reviews as $review)
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200 mb-6 p-4">
            <div class="mb-2">
                <strong>{{ $review->product->name ?? '' }}</strong> - Biến thể: {{ $review->variant->name ?? '' }}
            </div>
            <div class="mb-2">
                <span>Đánh giá: {{ $review->rating }} sao</span>
            </div>
            <div class="mb-2">
                <span>Nội dung: {{ $review->review }}</span>
            </div>
            @if(!empty($review->images) && is_array($review->images))
                <div class="mb-2">
                    @foreach($review->images as $img)
                        <img src="{{ asset('storage/' . $img) }}" style="width:80px;">
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <div class="alert alert-info">Bạn chưa có đánh giá nào cho đơn hàng này.</div>
    @endforelse
</div>
@endsection --}}