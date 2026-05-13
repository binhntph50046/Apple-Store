<p>Xin chào {{ $invoice->order->shipping_name ?? 'Quý khách' }},</p>
<p>Hóa đơn cho đơn hàng #{{ $invoice->order_id }} đã được đính kèm trong email này dưới dạng file PDF.</p>
<p>Cảm ơn bạn đã mua hàng tại cửa hàng của chúng tôi!</p> 