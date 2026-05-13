<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hóa đơn #{{ $invoice->invoice_code }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; }
        h2, h4 { margin-bottom: 10px; }
        .status { 
            padding: 5px 10px;
            border-radius: 4px;
            font-weight: bold;
        }
        .status-pending { background-color: #ffeeba; color: #856404; }
        .status-processing { background-color: #b8daff; color: #004085; }
        .status-completed { background-color: #c3e6cb; color: #155724; }
        .status-cancelled { background-color: #f5c6cb; color: #721c24; }
        .customer-info { margin-bottom: 20px; }
        .total-section { margin-top: 20px; }
        .total-row { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Hóa đơn #{{ $invoice->invoice_code }}</h2>
    
    <div class="customer-info">
        <h4>Thông tin khách hàng</h4>
        <p>Tên: {{ $invoice->order->shipping_name }}</p>
        <p>Email: {{ $invoice->order->shipping_email }}</p>
        <p>Số điện thoại: {{ $invoice->order->shipping_phone }}</p>
        <p>Địa chỉ: {{ $invoice->order->shipping_address }}</p>
    </div>

    <div class="order-info">
        <p>Ngày xuất: {{ $invoice->issued_at ? $invoice->issued_at->format('d/m/Y H:i') : '' }}</p>
        <p>Mã đơn hàng: #{{ $invoice->order->order_code }}</p>
        <p>Trạng thái đơn hàng: 
            <span class="status status-{{ strtolower($invoice->order->status) }}">
                @switch($invoice->order->status)
                    @case('pending')
                        Chờ xử lý
                        @break
                    @case('confirmed')
                        Đã xác nhận
                        @break
                    @case('preparing')
                        Đang chuẩn bị hàng
                        @break
                    @case('shipping')
                        Đang giao hàng
                        @break
                    @case('completed')
                        Hoàn thành
                        @break
                    @case('cancelled')
                        Đã hủy
                        @break
                    @default
                        {{ $invoice->order->status }}
                @endswitch
            </span>
        </p>
        <p>Phương thức thanh toán: 
            @switch($invoice->order->payment_method)
                @case('cod')
                    Thanh toán khi nhận hàng (COD)
                    @break
                @case('vnpay')
                    Thanh toán qua VNPay
                    @break
                @default
                    {{ $invoice->order->payment_method }}
            @endswitch
        </p>
    </div>

    <hr>
    <h4>Chi tiết sản phẩm</h4>
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Biến thể</th>
                <th>Số lượng</th>
                <th>Đơn giá</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->order->items as $item)
            <tr>
                <td>{{ $item->product->name ?? '' }}</td>
                <td>{{ $item->variant->name ?? '' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->price) }} VNĐ</td>
                <td>{{ number_format($item->total) }} VNĐ</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-section">
        <table>
            <tr>
                <td>Tạm tính:</td>
                <td>{{ number_format($invoice->order->subtotal) }} VNĐ</td>
            </tr>
            <tr>
                <td>Phí vận chuyển:</td>
                <td>{{ number_format($invoice->order->shipping_fee) }} VNĐ</td>
            </tr>
            <tr>
                <td>Giảm giá:</td>
                <td>{{ number_format($invoice->order->discount) }} VNĐ</td>
            </tr>
            <tr class="total-row">
                <td>Tổng cộng:</td>
                <td>{{ number_format($invoice->total) }} VNĐ</td>
            </tr>
        </table>
    </div>

    @if($invoice->order->notes)
    <div class="notes">
        <h4>Ghi chú đơn hàng:</h4>
        <p>{{ $invoice->order->notes }}</p>
    </div>
    @endif
</body>
</html> 