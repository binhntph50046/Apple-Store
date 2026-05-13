<!DOCTYPE html>
<html>
<head>
    <title>Hóa đơn đơn hàng #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        table { width: 100%; line-height: inherit; text-align: left; }
        table th, table td { padding: 8px; border-bottom: 1px solid #eee; }
        .total { font-weight: bold; color: #d00; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
<div class="invoice-box">
    <h2>HÓA ĐƠN ĐƠN HÀNG #{{ $order->id }}</h2>
    <p>Ngày đặt: {{ $order->created_at->format('d/m/Y H:i') }}</p>
    <p>Khách hàng: {{ $order->shipping_name }} | SĐT: {{ $order->shipping_phone }}</p>
    <p>Địa chỉ: {{ $order->shipping_address }}</p>
    <hr>
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
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? '' }}</td>
                    <td>{{ $item->variant->name ?? '' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->price) }} VNĐ</td>
                    <td>{{ number_format($item->total) }} VNĐ</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4" class="text-right">Tổng cộng:</td>
                <td class="total">{{ number_format($order->total_price) }} VNĐ</td>
            </tr>
        </tfoot>
    </table>
    <hr>
    <p>Cảm ơn quý khách đã mua hàng!</p>
    <script>
        window.onload = function() { window.print(); }
    </script>
</div>
</body>
</html> 