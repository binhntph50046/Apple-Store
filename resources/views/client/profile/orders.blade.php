@extends('client.layouts.app')

@section('content')
<div class="container" style="margin-top: 150px; margin-bottom: 50px;">
    <div class="row">
        @include('client.profile.partials.sidebar')

        <!-- Main Content Area -->
        <div class="col-md-9">
            <div class="card p-4" style="background: #ffffff; border: none; border-radius: 8px;">
            <h4 class="mb-4">Đơn hàng của tôi</h4>
                @forelse($orders as $order)
                    <div class="col-md-6 mb-4">
                        <div class="card p-3" style="background: #ffffff; border: none; border-radius: 8px;">
                            <h6 class="mb-2">Mã đơn hàng: {{ $order->order_number }}</h6>
                            <p class="mb-0">Tổng tiền: <span class="text-danger">{{ number_format($order->total) }} đ</span></p>
                            <p class="mb-0">Trạng thái: <span class="badge bg-{{ $order->status_color }}">{{ $order->status_text }}</span></p>
                            <small class="text-muted text-end mt-2">{{ $order->created_at->format('H:i d/m/Y') }}</small>
                            <div class="mt-2">
                                <a href="{{ route('order.tracking', $order) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Chi tiết
                                </a>
                                @if($order->status === 'pending')
                                    <form action="{{ route('order.cancel', $order) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')">
                                            <i class="fas fa-times me-1"></i>Hủy đơn
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="card p-4" style="background: #ffffff; border: none; border-radius: 8px;">
                            <p class="text-muted mb-0">Bạn chưa có đơn hàng nào.</p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if($orders->hasPages())
                <div class="mt-4">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 