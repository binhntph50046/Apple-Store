@extends('client.layouts.app')
@section('title', 'Thanh toán - Apple Store')

@section('content')
    <style>
.checkout-wrapper {
    padding: 120px 0 60px; /* Tăng padding-top để tránh bị header che */
    background-color: #f8f9fa;
    min-height: 100vh;
}

.checkout-title {
    font-size: 2.2rem;
    font-weight: 600;
    color: #1a1a1a;
}

.checkout-header {
    text-align: center;
    margin-bottom: 40px;
}

.checkout-header h1 {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 20px;
}

/* Checkout Steps */
.checkout-steps {
            display: flex;
    align-items: center;
    justify-content: center;
    margin: 30px 0;
}

.step {
            display: flex;
    flex-direction: column;
            align-items: center;
            position: relative;
        }

.step-number {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-bottom: 8px;
}

.step.active .step-number {
    background: #3b82f6;
    color: white;
}

.step-text {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 500;
}

.step.active .step-text {
    color: #3b82f6;
}

.step-line {
    width: 100px;
    height: 2px;
    background: #e2e8f0;
    margin: 0 15px;
}

.step-line.active {
    background: #3b82f6;
}

/* Alert Styles */
.alert-container {
    margin-bottom: 30px;
}

        .custom-alert {
            display: flex;
            align-items: center;
    padding: 15px;
    border-radius: 10px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 10px;
}

.custom-alert.error {
    border-left: 4px solid #ef4444;
        }

        .custom-alert .icon {
    color: #ef4444;
            font-size: 20px;
    margin-right: 15px;
        }

        .custom-alert .content {
    flex: 1;
    color: #1f2937;
}

.custom-alert .close {
    background: none;
    border: none;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    font-size: 18px;
}

/* Checkout Sections */
.checkout-section {
    background: white;
    border-radius: 15px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    padding: 25px;
    margin-bottom: 25px;
}

.section-header {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.section-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    margin-right: 15px;
}

.section-header h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
}

/* Form Styles */
.form-floating > .form-control {
    padding: 1rem 0.75rem;
}

.form-floating > .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.form-floating > .form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.1);
}

.form-floating > .form-control.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.25);
}

.form-floating > label {
    padding: 1rem 0.75rem;
}

/* Error message styling */
.invalid-feedback {
    display: block !important;
    margin-top: 0.25rem;
}

.invalid-feedback div {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #dc3545;
    font-size: 0.875rem;
    font-weight: 500;
}

.invalid-feedback i {
    font-size: 14px;
    flex-shrink: 0;
}

/* Custom Checkbox */
.custom-checkbox-container {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    padding: 10px 0;
}

.custom-checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: relative;
    height: 24px;
    width: 24px;
    background-color: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 6px;
    margin-right: 10px;
}

.custom-checkbox-container:hover .checkmark {
    border-color: #3b82f6;
}

.custom-checkbox-container input:checked ~ .checkmark {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.custom-checkbox-container input:checked ~ .checkmark:after {
            display: block;
}

.custom-checkbox-container .checkmark:after {
    left: 8px;
    top: 4px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.label-text {
    font-weight: 500;
    color: #1f2937;
}

/* Payment Methods */
.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.payment-method-option {
    cursor: pointer;
            margin: 0;
}

.payment-method-option input[type="radio"] {
    display: none;
}

.payment-method-content {
    display: flex;
    align-items: center;
    padding: 15px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    transition: all 0.3s ease;
}

.payment-method-option input[type="radio"]:checked + .payment-method-content {
    border-color: #3b82f6;
    background: #f0f9ff;
}

.payment-method-content img {
    width: 40px;
    height: 40px;
    object-fit: contain;
    margin-right: 15px;
}

.payment-method-info {
    display: flex;
    flex-direction: column;
}

.payment-method-name {
    font-weight: 600;
    color: #1f2937;
}

.payment-method-desc {
    font-size: 0.9rem;
    color: #6b7280;
}

/* Order Summary */
.order-summary-sticky {
    /* Xóa position: sticky và top */
    position: relative;
}

/* Thêm style cho order summary để đảm bảo layout vẫn đẹp */
.order-summary {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
    height: 100%;
}

.order-items {
    margin-bottom: 20px;
}

.order-item {
    display: flex;
    padding: 15px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    margin-bottom: 15px;
    background: #fff;
}

.order-item:last-child {
    margin-bottom: 0;
}

.item-image {
    width: 100px; /* Tăng kích thước ảnh */
    height: 100px;
    border-radius: 10px;
    overflow: hidden;
    margin-right: 15px;
    border: 1px solid #e2e8f0;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: contain; /* Đổi thành contain để hiển thị đầy đủ ảnh */
}

.item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.item-info h4 {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 8px 0;
    line-height: 1.4;
}

.item-variants {
    margin-bottom: 5px;
}

.variant-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #f3f4f6;
    border-radius: 20px;
    font-size: 0.85rem;
    color: #4b5563;
    margin-right: 5px;
    margin-bottom: 5px;
}

.item-price {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.price {
    font-weight: 600;
    color: #1f2937;
    font-size: 1.1rem;
}

.quantity {
    color: #6b7280;
    font-size: 0.9rem;
    background: #f3f4f6;
    padding: 4px 12px;
    border-radius: 20px;
}

/* Voucher Section */
.voucher-section {
    padding: 20px 0;
    border-top: 1px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
}

.voucher-input {
    display: flex;
    gap: 10px;
}

.voucher-input .form-control {
    border-radius: 8px;
}

.voucher-input select.form-control {
    padding: 0.75rem;
    font-size: 0.9rem;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.voucher-input select.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
}

.voucher-input select.form-control option {
    padding: 8px;
    font-size: 0.9rem;
}

.btn-apply {
    padding: 0 20px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-apply:hover {
    background: #2563eb;
}

.voucher-notice {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-top: 10px;
    color: #ef4444;
    font-size: 0.9rem;
}

.voucher-message {
    margin-top: 10px;
    font-size: 0.9rem;
}

/* Order Total */
.order-total {
    padding: 20px 0;
}

.total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    color: #4b5563;
}

.total-row.grand-total {
    font-size: 1.2rem;
    font-weight: 700;
    color: #1f2937;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 2px solid #e5e7eb;
}

.discount {
    color: #10b981;
}

/* Place Order Button */
.btn-place-order {
    width: 100%;
    padding: 15px;
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.btn-place-order:hover {
    background: #2563eb;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 991px) {
    .order-summary-sticky {
        margin-top: 2rem;
    }
    
    .step-line {
        width: 60px;
    }
}

@media (max-width: 768px) {
    .checkout-wrapper {
        padding: 20px 0;
    }
    
    .checkout-section {
        padding: 20px;
    }
    
    .step-text {
        font-size: 0.8rem;
    }
    
    .step-line {
        width: 40px;
    }
}

/* Cập nhật style cho checkbox */
.ship-to-different-toggle {
    margin: 2rem 0;
}

.custom-checkbox-container {
    display: flex;
    align-items: center;
    cursor: pointer;
    user-select: none;
    color: #2d3748;
    font-weight: 500;
}

.custom-checkbox-container input {
    position: absolute;
                opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: relative;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    margin-right: 8px;
    transition: all 0.2s ease;
}

.custom-checkbox-container:hover .checkmark {
    border-color: #3b82f6;
}

.custom-checkbox-container input:checked ~ .checkmark {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

.custom-checkbox-container input:checked ~ .checkmark:after {
    display: block;
}

/* Style cho textarea ghi chú */
textarea.form-control {
    min-height: 80px;
    resize: vertical;
}

.form-floating > textarea.form-control {
    padding-top: 1.625rem;
}

/* Voucher Modal Styles */
.voucher-list {
    max-height: 300px;
    overflow-y: auto;
}

.voucher-item {
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
}

.voucher-item:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.voucher-item.selected {
    border-color: #10b981;
    background: #f0fdf4;
}

.voucher-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.voucher-code {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
}

.voucher-type .badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}

.voucher-details {
    font-size: 0.85rem;
    color: #6b7280;
}

.voucher-condition,
.voucher-savings {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 3px;
}

.voucher-savings {
    color: #10b981;
    font-weight: 500;
}

/* Modal custom styles */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* Style cho readonly fields */
.readonly-field {
    background-color: #f8f9fa !important;
    cursor: not-allowed !important;
    opacity: 0.8;
}

.readonly-field:focus {
    box-shadow: none !important;
    border-color: #e2e8f0 !important;
}

.modal-header {
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem 1.25rem;
}

.modal-body {
    padding: 1rem 1.25rem;
}

.modal-footer {
    border-top: 1px solid #e5e7eb;
    padding: 0.75rem 1.25rem;
}
    </style>

<div class="checkout-wrapper">
    <div class="container">
        <div class="text-center" style="margin:40px;">
            <h1 class="checkout-title">Thanh toán đơn hàng</h1>
        </div>

                        @if($errors->any())
            <div class="alert-container mb-5">
                            @foreach ($errors->all() as $error)
                                <div class="custom-alert error" id="error-alert">
                        <div class="icon"><i class="fas fa-exclamation-circle"></i></div>
                        <div class="content">{{ $error }}</div>
                        <button class="close" onclick="this.parentElement.style.display='none';">
                            <i class="fas fa-times"></i>
                        </button>
                                </div>
                            @endforeach
            </div>
                        @endif

        <div class="checkout-content">
            <div class="row g-5">
                <div class="col-lg-8">
                    <form id="checkoutForm" action="{{ isset($variant) ? route('checkout.store') : route('cart.checkout.store') }}" method="POST">
                            @csrf
                            @if(isset($variant))
                                <input type="hidden" name="variant_id" value="{{ $variant->id }}">
                                <input type="hidden" name="quantity" value="{{ $quantity ?? 1 }}">
                            @endif
                            @foreach(request('selected_items', []) as $itemId)
                                <input type="hidden" name="selected_items[]" value="{{ $itemId }}">
                            @endforeach
                            <input type="hidden" name="voucher_code" id="voucher_code_input">
                            <input type="hidden" name="voucher_id" id="voucher_id_input">
                            <input type="hidden" name="discount_amount" id="discount_amount_input">

                        <!-- Thông tin người đặt -->
                        <div class="checkout-section mb-4">
                            <div class="section-header mb-4">
                                <div class="section-icon">
                                    <i class="fas fa-user-circle"></i>
                                </div>
                                <h2>Thông tin người đặt</h2>
                            </div>
                            <div class="section-content">
                                <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="c_fname" name="c_fname"
                                        value="{{ old('c_fname', Auth::check() && Auth::user() ? Auth::user()->name : '') }}"
                                        placeholder="Nhập họ và tên">
                                    <label for="c_fname">Họ và tên <span class="text-danger">*</span></label>
                                </div>
                                <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="c_address" name="c_address"
                                        value="{{ old('c_address', Auth::check() && Auth::user() ? Auth::user()->address : '') }}"
                                        placeholder="Nhập địa chỉ">
                                    <label for="c_address">Địa chỉ <span class="text-danger">*</span></label>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="c_email_address" name="c_email_address"
                                                value="{{ old('c_email_address', Auth::check() && Auth::user() ? Auth::user()->email : '') }}"
                                                placeholder="Nhập email">
                                            <label for="c_email_address">Email <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="c_phone" name="c_phone"
                                                value="{{ old('c_phone', Auth::check() && Auth::user() ? Auth::user()->phone : '') }}"
                                                placeholder="Nhập số điện thoại">
                                            <label for="c_phone">Số điện thoại <span class="text-danger">*</span></label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Giao hàng đến địa chỉ khác -->
                                <div class="ship-to-different-toggle mb-3">
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" id="ship_to_different" name="ship_to_different" value="1">
                                        <span class="checkmark"></span>
                                        <span class="label-text">Giao hàng đến địa chỉ khác</span>
                                    </label>
                                </div>

                                <!-- Thông tin người nhận -->
                                <div class="recipient-info mb-4" style="display: none;">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="shipping_name" name="shipping_name"
                                            placeholder="Nhập họ và tên người nhận">
                                        <label for="shipping_name">Họ và tên người nhận <span class="text-danger recipient-required" style="display: none;">*</span></label>
                                    </div>
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="shipping_address" name="shipping_address"
                                            placeholder="Nhập địa chỉ giao hàng">
                                        <label for="shipping_address">Địa chỉ giao hàng <span class="text-danger recipient-required" style="display: none;">*</span></label>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="email" class="form-control" id="shipping_email" name="shipping_email"
                                                    placeholder="Nhập email người nhận">
                                                <label for="shipping_email">Email người nhận</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control" id="shipping_phone" name="shipping_phone"
                                                    placeholder="Nhập số điện thoại người nhận">
                                                <label for="shipping_phone">Số điện thoại người nhận <span class="text-danger recipient-required" style="display: none;">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ghi chú -->
                                <div class="form-floating mb-4">
                                    <textarea class="form-control" id="c_order_notes" name="c_order_notes" 
                                        style="height: 100px" placeholder="Nhập ghi chú"></textarea>
                                    <label for="c_order_notes">Ghi chú đơn hàng</label>
                                </div>
                            </div>
                        </div>

                        <!-- Phương thức thanh toán -->
                        <div class="checkout-section mb-4">
                            <div class="section-header mb-4">
                                <div class="section-icon">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <h2>Phương thức thanh toán</h2>
                            </div>
                            <div class="section-content">
                                    <div class="payment-methods">
                                    <label class="payment-method-option">
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <span class="payment-method-content">
                                            <img src="{{ asset('/images/logo/cod.png') }}" alt="COD">
                                            <div class="payment-method-info">
                                                <span class="payment-method-name">Thanh toán khi nhận hàng (COD)</span>
                                                <span class="payment-method-desc">Thanh toán bằng tiền mặt khi nhận hàng</span>
                                            </div>
                                        </span>
                                        </label>
                                    <label class="payment-method-option">
                                        <input type="radio" name="payment_method" value="vnpay">
                                        <span class="payment-method-content">
                                            <img src="{{ asset('/images/logo-primary.svg') }}" alt="VNPay">
                                            <div class="payment-method-info">
                                                <span class="payment-method-name">Thanh toán qua VNPay</span>
                                                <span class="payment-method-desc">Thanh toán an toàn với VNPay</span>
                                            </div>
                                        </span>
                                        </label>
                                    </div>
                            </div>
                            </div>
                        </form>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary-sticky">
                        <!-- Thông tin đơn hàng -->
                        <div class="checkout-section">
                            <div class="section-header mb-4">
                                <div class="section-icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <h2>Đơn hàng của bạn</h2>
                            </div>
                            <div class="section-content">
                                <div class="order-items">
                                    @if(isset($variant))
                                        <div class="order-item">
                                            <div class="item-image">
                                                <img src="{{ asset($variant->product->default_variant_image ?? 'images/no-image.png') }}" 
                                                    alt="{{ $variant->product->name }}">
                        </div>
                                            <div class="item-info">
                                                <h4>{{ $variant->product->name }}</h4>
                                                <div class="item-variants">
                                                                @foreach($attributes as $type => $value)
                                                        <span class="variant-badge">{{ $type }}: {{ $value }}</span>
                                                                @endforeach
                                                            </div>
                                                <div class="item-price">
                                                    <span class="price">{{ number_format($variant->selling_price, 0, ',', '.') }} VNĐ</span>
                                                    <span class="quantity">x {{ $quantity }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                        @else
                                            @foreach($cartItems as $item)
                                            <div class="order-item">
                                                <div class="item-image">
                                                    @if($item->variant && $item->variant->image)
                                                        <img src="{{ asset($item->variant->image) }}" 
                                                            alt="{{ $item->product->name }}">
                                                    @elseif($item->product && $item->product->default_variant_image)
                                                        <img src="{{ asset($item->product->default_variant_image) }}" 
                                                            alt="{{ $item->product->name }}">
                                                    @else
                                                        <img src="{{ asset('images/no-image.png') }}" 
                                                            alt="{{ $item->product->name }}">
                                                    @endif
                                                </div>
                                                <div class="item-info">
                                                    <h4>{{ $item->product->name }}</h4>
                                                                @if($item->variant)
                                                        <div class="item-variants">
                                                                        @foreach($item->variant->combinations as $comb)
                                                                <span class="variant-badge">
                                                                                {{ $comb->attributeValue->attributeType->name }}: 
                                                                                {{ is_array($comb->attributeValue->value) ? $comb->attributeValue->value[0] : $comb->attributeValue->value }}
                                                                            </span>
                                                                        @endforeach
                                                                    </div>
                                                                @endif
                                                    <div class="item-price">
                                                        <span class="price">
                                                            {{ number_format($item->variant ? $item->variant->selling_price : $item->product->selling_price, 0, ',', '.') }} VNĐ
                                                        </span>
                                                        <span class="quantity">x {{ $item->quantity }}</span>
                                                    </div>
                                                            </div>
                                                        </div>
                                            @endforeach
                                    @endif
                                </div>

                                <!-- Mã giảm giá -->
                                <div class="voucher-section my-4">
                                    <div class="voucher-input">
                                        <button type="button" 
                                            class="btn btn-outline-primary w-100" 
                                            id="voucher-select-btn"
                                            {{ isset($hasDiscountedProducts) && $hasDiscountedProducts ? 'disabled' : '' }}>
                                            <i class="fas fa-tag me-2"></i>
                                            Chọn mã giảm giá
                                        </button>
                                    </div>
                                    @if(isset($hasDiscountedProducts) && $hasDiscountedProducts)
                                        <div class="voucher-notice">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Không thể áp dụng mã giảm giá cho sản phẩm đang khuyến mãi</span>
                                        </div>
                                    @endif
                                    <div id="voucher-message" class="voucher-message"></div>
                                </div>

                                <!-- Tổng tiền -->
                                <div class="order-total mt-4">
                                    <div class="total-row">
                                        <span>Tạm tính</span>
                                        <span>{{ number_format(isset($variant) ? $variant->selling_price * $quantity : $subtotal, 0, ',', '.') }} VNĐ</span>
                                    </div>
                                    <div class="total-row" id="voucher-discount-row" style="display: none;">
                                        <span>Giảm giá</span>
                                        <span class="discount" style="position: absolute; right: 25px;">-<span id="voucher-discount-amount">0</span> VNĐ</span>
                                    </div>
                                    <div class="total-row">
                                        <span>Phí vận chuyển</span>
                                        <span>30.000 VNĐ</span>
                                    </div>
                                    <div class="total-row grand-total">
                                        <span>Tổng cộng</span>
                                        <span id="final-total">{{ number_format((isset($variant) ? $variant->selling_price * $quantity : $subtotal) + 30000, 0, ',', '.') }} VNĐ</span>
                                    </div>
                                </div>

                                <button type="button" class="btn-place-order mt-4" id="placeOrderBtn">
                                    <i class="fas fa-lock"></i>
                                    <span>Đặt hàng</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Voucher Modal -->
<div class="modal fade" id="voucherModal" tabindex="-1" aria-labelledby="voucherModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="voucherModalLabel">
                    <i class="fas fa-tag me-2"></i>
                    Chọn mã giảm giá
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="voucher-list">
                            @if(isset($availableVouchers) && $availableVouchers->count() > 0)
                                @foreach($availableVouchers as $voucher)
                                    <div class="voucher-item" 
                                         data-voucher-code="{{ $voucher->code }}"
                                         data-voucher-id="{{ $voucher->id }}"
                                         data-discount-amount="{{ $voucher->discount_amount }}"
                                         data-type="{{ $voucher->type }}"
                                         data-value="{{ $voucher->value }}">
                                        <div class="voucher-header">
                                            <div class="voucher-code">{{ $voucher->code }}</div>
                                            <div class="voucher-type">
                                                @if($voucher->type === 'percentage')
                                                    <span class="badge bg-success">Giảm {{ $voucher->value }}%</span>
                                                @elseif($voucher->type === 'fixed')
                                                    <span class="badge bg-primary">Giảm {{ number_format($voucher->value, 0, ',', '.') }} VNĐ</span>
                                                @elseif($voucher->type === 'free_shipping')
                                                    <span class="badge bg-info">Miễn phí vận chuyển</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="voucher-details">
                                            @if($voucher->min_order_amount)
                                                <div class="voucher-condition">
                                                    <i class="fas fa-info-circle text-muted"></i>
                                                    Tối thiểu {{ number_format($voucher->min_order_amount, 0, ',', '.') }} VNĐ
                                                </div>
                                            @endif
                                            @if($voucher->discount_amount > 0)
                                                <div class="voucher-savings">
                                                    <i class="fas fa-gift text-success"></i>
                                                    Tiết kiệm {{ number_format($voucher->discount_amount, 0, ',', '.') }} VNĐ
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-tag text-muted" style="font-size: 3rem;"></i>
                                    <p class="text-muted mt-3">Không có mã giảm giá khả dụng</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkoutForm = document.getElementById('checkoutForm');
        const placeOrderBtn = document.getElementById('placeOrderBtn');
        
        // Xử lý khi click nút đặt hàng
        placeOrderBtn.addEventListener('click', function() {
            // Kiểm tra validation trước
            if (!validateForm()) {
                return;
            }
            
            // Xử lý đặt hàng trực tiếp
            processOrder();
        });
        
        // Real-time validation
        setupRealTimeValidation();
        
        function setupRealTimeValidation() {
            // Các trường cần validate real-time
            const fieldsToValidate = [
                'c_fname', 'c_address', 'c_email_address', 'c_phone',
                'shipping_name', 'shipping_address', 'shipping_phone'
            ];
            
            fieldsToValidate.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    field.addEventListener('blur', function() {
                        validateField(this);
                    });
                    
                    field.addEventListener('input', function() {
                        // Clear error khi user bắt đầu nhập
                        if (this.classList.contains('is-invalid')) {
                            clearFieldError(this);
                        }
                    });
                }
            });
        }
        
        function validateField(element) {
            const fieldId = element.id;
            const value = element.value.trim();
            
            // Clear error trước
            clearFieldError(element);
            
            // Validate theo từng loại field
            switch(fieldId) {
                case 'c_fname':
                case 'shipping_name':
                    if (!value) {
                        showFieldError(element, 'Họ và tên là bắt buộc');
                    } else if (value.includes('  ')) {
                        showFieldError(element, 'Họ và tên không được chứa khoảng trắng liên tiếp');
                    }
                    break;
                    
                case 'c_address':
                case 'shipping_address':
                    if (!value) {
                        showFieldError(element, 'Địa chỉ là bắt buộc');
                    } else if (value.includes('  ')) {
                        showFieldError(element, 'Địa chỉ không được chứa khoảng trắng liên tiếp');
                    }
                    break;
                    
                case 'c_email_address':
                case 'shipping_email':
                    if (value) {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(value)) {
                            showFieldError(element, 'Email không hợp lệ');
                        }
                    }
                    break;
                    
                case 'c_phone':
                case 'shipping_phone':
                    if (value) {
                        const phoneRegex = /^[0-9]{10,11}$/;
                        if (!phoneRegex.test(value)) {
                            showFieldError(element, 'Số điện thoại phải có 10-11 chữ số');
                        }
                    }
                    break;
            }
        }
        

        
        function validateForm() {
            let isValid = true;
            
            // Xóa tất cả thông báo lỗi cũ
            clearAllErrors();
            
            // Kiểm tra các trường bắt buộc
            const requiredFields = [
                { id: 'c_fname', name: 'Họ và tên' },
                { id: 'c_address', name: 'Địa chỉ' },
                { id: 'c_email_address', name: 'Email' },
                { id: 'c_phone', name: 'Số điện thoại' }
            ];
            
            requiredFields.forEach(field => {
                const element = document.getElementById(field.id);
                if (!element.value.trim()) {
                    showFieldError(element, `${field.name} là bắt buộc`);
                    isValid = false;
                } else if (element.value.includes('  ')) {
                    showFieldError(element, `${field.name} không được chứa khoảng trắng liên tiếp`);
                    isValid = false;
                } else {
                    clearFieldError(element);
                }
            });
            
            // Kiểm tra email
            const emailField = document.getElementById('c_email_address');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailField.value.trim() && !emailRegex.test(emailField.value.trim())) {
                showFieldError(emailField, 'Email không hợp lệ');
                isValid = false;
            }
            
            // Kiểm tra số điện thoại
            const phoneField = document.getElementById('c_phone');
            const phoneRegex = /^[0-9]{10,11}$/;
            if (phoneField.value.trim() && !phoneRegex.test(phoneField.value.trim())) {
                showFieldError(phoneField, 'Số điện thoại phải có 10-11 chữ số');
                isValid = false;
            }
            
            // Kiểm tra thông tin người nhận nếu có chọn
            const shipToDifferent = document.getElementById('ship_to_different').checked;
            if (shipToDifferent) {
                const recipientFields = [
                    { id: 'shipping_name', name: 'Họ và tên người nhận' },
                    { id: 'shipping_address', name: 'Địa chỉ giao hàng' },
                    { id: 'shipping_phone', name: 'Số điện thoại người nhận' }
                ];
                
                recipientFields.forEach(field => {
                    const element = document.getElementById(field.id);
                    if (!element.value.trim()) {
                        showFieldError(element, `${field.name} là bắt buộc`);
                        isValid = false;
                    } else if (element.value.includes('  ')) {
                        showFieldError(element, `${field.name} không được chứa khoảng trắng liên tiếp`);
                        isValid = false;
                    } else {
                        clearFieldError(element);
                    }
                });
                
                // Kiểm tra số điện thoại người nhận
                const shippingPhoneField = document.getElementById('shipping_phone');
                if (shippingPhoneField.value.trim() && !phoneRegex.test(shippingPhoneField.value.trim())) {
                    showFieldError(shippingPhoneField, 'Số điện thoại người nhận phải có 10-11 chữ số');
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        function showFieldError(element, message) {
            element.classList.add('is-invalid');
            element.style.borderColor = '#dc3545';
            
            // Tìm hoặc tạo error div
            let errorDiv = element.parentNode.querySelector('.invalid-feedback');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback d-block';
                element.parentNode.appendChild(errorDiv);
            }
            
            // Tạo icon và message
            errorDiv.innerHTML = `
                <div style="display: flex; align-items: center; gap: 8px; color: #dc3545; font-size: 0.875rem; margin-top: 4px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 14px;"></i>
                    <span>${message}</span>
                </div>
            `;
        }
        
        function clearFieldError(element) {
            element.classList.remove('is-invalid');
            element.style.borderColor = '';
            const errorDiv = element.parentNode.querySelector('.invalid-feedback');
            if (errorDiv) {
                errorDiv.remove();
            }
        }
        
        function clearAllErrors() {
            // Xóa tất cả thông báo lỗi
            document.querySelectorAll('.invalid-feedback').forEach(error => {
                error.remove();
            });
            
            // Xóa trạng thái lỗi của tất cả input
            document.querySelectorAll('.is-invalid').forEach(input => {
                input.classList.remove('is-invalid');
                input.style.borderColor = '';
            });
        }
        

        
        function processOrder() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked')?.value;
            
            // Thêm input ẩn để truyền thông tin voucher và giá đã giảm
            const finalTotalElement = document.getElementById('final-total');
            const finalTotalText = finalTotalElement.innerText;
            const finalTotalValue = parseInt(finalTotalText.replace(/[^\d]/g, '')); // Lấy số tiền cuối cùng
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'final_total';
            hiddenInput.value = finalTotalValue;
            checkoutForm.appendChild(hiddenInput);

            if (paymentMethod === 'vnpay') {
                checkoutForm.setAttribute('action', "{{ isset($variant) ? route('vnpay.payment') : route('cart.checkout.vnpay') }}");
            } else {
                checkoutForm.setAttribute('action', "{{ isset($variant) ? route('checkout.store') : route('cart.checkout.store') }}");
            }
            
            // Submit form
            checkoutForm.submit();
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const voucherSelectBtn = document.getElementById('voucher-select-btn');
        const voucherModal = document.getElementById('voucherModal');
        const voucherMsg = document.getElementById('voucher-message');
        const voucherDiscountRow = document.getElementById('voucher-discount-row');
        const voucherDiscountAmount = document.getElementById('voucher-discount-amount');
        const finalTotal = document.getElementById('final-total');
        const voucherCodeInput = document.getElementById('voucher_code_input');
        const voucherIdInput = document.getElementById('voucher_id_input');
        const discountAmountInput = document.getElementById('discount_amount_input');

        // Lấy tạm tính (subtotal) từ view
        let subtotal = {{ (isset($variant) ? $variant->selling_price * ($quantity ?? 1) : (isset($subtotal) ? $subtotal : 0)) }};
        const shippingFee = 30000; // Phí vận chuyển cố định

        // Mở modal khi click button
        voucherSelectBtn.addEventListener('click', function() {
            const modal = new bootstrap.Modal(voucherModal);
            modal.show();
        });

        // Xử lý khi click vào voucher item
        document.querySelectorAll('.voucher-item').forEach(item => {
            item.addEventListener('click', function() {
                const code = this.dataset.voucherCode;
                const voucherId = this.dataset.voucherId;
                const discountAmount = parseInt(this.dataset.discountAmount) || 0;

                // Kiểm tra xem đã có voucher nào được áp dụng chưa
                if (voucherCodeInput.value && voucherCodeInput.value !== code) {
                    voucherMsg.style.display = 'block';
                                            voucherMsg.className = 'alert alert-warning mt-2';
                        voucherMsg.innerText = 'Chỉ có thể áp dụng 1 mã giảm giá cho mỗi đơn hàng!';
                        voucherDiscountRow.style.display = 'none';
                        finalTotal.innerText = (subtotal + shippingFee).toLocaleString('vi-VN') + ' VNĐ';
                    voucherCodeInput.value = '';
                    voucherIdInput.value = '';
                    discountAmountInput.value = '';
                    return;
                }

                // Gọi API kiểm tra voucher
                fetch('{{ route('voucher.check') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify({
                        voucher_code: code,
                        subtotal: subtotal + shippingFee, // Bao gồm phí vận chuyển
                        email: document.getElementById('c_email_address').value
                    })
                })
                .then(res => res.json())
                .then(data => {
                    voucherMsg.style.display = 'block';
                    if (data.success) {
                        voucherMsg.className = 'alert alert-success mt-2';
                        voucherMsg.innerText = data.message;
                        voucherDiscountRow.style.display = 'table-row';
                        voucherDiscountAmount.innerText = data.voucher.discount_amount.toLocaleString('vi-VN');
                        
                        // Tính tổng cuối cùng bao gồm phí vận chuyển
                        const finalAmount = (subtotal + shippingFee) - data.voucher.discount_amount;
                        finalTotal.innerText = finalAmount.toLocaleString('vi-VN') + ' VNĐ';
                        
                        // Cập nhật các input hidden
                        voucherCodeInput.value = data.voucher.code;
                        voucherIdInput.value = data.voucher.id;
                        discountAmountInput.value = data.voucher.discount_amount;

                        // Cập nhật text button
                        voucherSelectBtn.innerHTML = `<i class="fas fa-check me-2"></i>${data.voucher.code}`;
                        voucherSelectBtn.className = 'btn btn-success w-100';

                        // Đóng modal
                        const modal = bootstrap.Modal.getInstance(voucherModal);
                        modal.hide();
                    } else {
                        voucherMsg.className = 'alert alert-danger mt-2';
                        voucherMsg.innerText = data.message;
                        voucherDiscountRow.style.display = 'none';
                        finalTotal.innerText = (subtotal + shippingFee).toLocaleString('vi-VN') + ' VNĐ';
                        
                        // Reset các input hidden
                        voucherCodeInput.value = '';
                        voucherIdInput.value = '';
                        discountAmountInput.value = '';
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    voucherMsg.style.display = 'block';
                    voucherMsg.className = 'alert alert-danger mt-2';
                    voucherMsg.innerText = 'Có lỗi xảy ra, vui lòng thử lại!';
                    
                    // Reset các input hidden trong trường hợp lỗi
                    voucherCodeInput.value = '';
                    voucherIdInput.value = '';
                    discountAmountInput.value = '';
                });
            });
        });

        // Reset voucher khi modal đóng
        voucherModal.addEventListener('hidden.bs.modal', function() {
            // Reset button nếu chưa có voucher được chọn
            if (!voucherCodeInput.value) {
                voucherSelectBtn.innerHTML = `<i class="fas fa-tag me-2"></i>Chọn mã giảm giá`;
                voucherSelectBtn.className = 'btn btn-outline-primary w-100';
            }
        });
    });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    function hideAlert(alertId) {
        const alert = document.getElementById(alertId);
        if (alert) {
            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }
    }

    // Tự động ẩn tất cả các thông báo lỗi
    document.querySelectorAll('.custom-alert').forEach((alert, index) => {
        alert.id = `error-alert-${index}`;
        hideAlert(`error-alert-${index}`);
    });
});
</script>
@endsection

<script>
// Script mới đơn giản
document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('ship_to_different');
    const senderFields = document.querySelectorAll('input[name^="c_"]');
    const recipientInfo = document.querySelector('.recipient-info');
    
    function handleCheckboxChange() {
        const isChecked = checkbox.checked;
        console.log('Checkbox changed to:', isChecked);
        
        // Hiển thị/ẩn thông tin người nhận
        recipientInfo.style.display = isChecked ? 'block' : 'none';
        
        // Xử lý các trường thông tin người nhận
        const recipientInputs = document.querySelectorAll('input[name^="shipping_"]');
        const requiredMarks = document.querySelectorAll('.recipient-required');
        
        recipientInputs.forEach(input => {
            if (input.id !== 'shipping_email') { // Email không bắt buộc
                input.required = isChecked;
                
                // Xóa lỗi validation cũ nếu có
                if (!isChecked) {
                    input.classList.remove('is-invalid');
                    input.style.borderColor = '';
                    const errorDiv = input.parentNode.querySelector('.invalid-feedback');
                    if (errorDiv) errorDiv.remove();
                }
            }
        });
        
        // Thêm validation cho số điện thoại người nhận
        const shippingPhoneField = document.getElementById('shipping_phone');
        if (shippingPhoneField) {
            shippingPhoneField.required = isChecked;
            
            // Xóa lỗi validation cũ nếu có
            if (!isChecked) {
                shippingPhoneField.classList.remove('is-invalid');
                shippingPhoneField.style.borderColor = '';
                const errorDiv = shippingPhoneField.parentNode.querySelector('.invalid-feedback');
                if (errorDiv) errorDiv.remove();
            }
            
            // Thêm validation real-time cho số điện thoại người nhận
            if (isChecked) {
                shippingPhoneField.addEventListener('blur', function() {
                    if (this.required && !this.value.trim()) {
                        this.classList.add('is-invalid');
                        this.style.borderColor = '#dc3545';
                        
                        let errorDiv = this.parentNode.querySelector('.invalid-feedback');
                        if (!errorDiv) {
                            errorDiv = document.createElement('div');
                            errorDiv.className = 'invalid-feedback d-block';
                            this.parentNode.appendChild(errorDiv);
                        }
                        
                        errorDiv.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 8px; color: #dc3545; font-size: 0.875rem; margin-top: 4px;">
                                <i class="fas fa-exclamation-circle" style="font-size: 14px;"></i>
                                <span>Số điện thoại người nhận là bắt buộc</span>
                            </div>
                        `;
                    } else {
                        this.classList.remove('is-invalid');
                        this.style.borderColor = '';
                        const errorDiv = this.parentNode.querySelector('.invalid-feedback');
                        if (errorDiv) errorDiv.remove();
                    }
                });
            }
        }
        
        requiredMarks.forEach(mark => {
            mark.style.display = isChecked ? 'inline' : 'none';
        });
        
        // Xử lý các trường thông tin người đặt
        senderFields.forEach(field => {
            if (isChecked) {
                // Tick: thêm readonly và ghi chú
                field.readOnly = true;
                field.style.backgroundColor = '#f8f9fa';
                field.style.cursor = 'not-allowed';
                field.classList.add('readonly-field');
                
                // Thêm ghi chú nếu chưa có
                if (!field.parentNode.querySelector('.sender-note')) {
                    const note = document.createElement('small');
                    note.className = 'text-muted d-block mt-1 sender-note';
                    note.textContent = 'Thông tin từ tài khoản - không thể chỉnh sửa khi đặt hàng hộ';
                    field.parentNode.appendChild(note);
                }
                console.log('Đã thêm readonly cho:', field.id);
            } else {
                // Bỏ tick: xóa readonly và ghi chú
                field.readOnly = false;
                field.style.backgroundColor = '';
                field.style.cursor = '';
                field.classList.remove('readonly-field');
                
                // Xóa ghi chú
                const note = field.parentNode.querySelector('.sender-note');
                if (note) note.remove();
                
                console.log('Đã xóa readonly cho:', field.id);
            }
        });
    }
    
    // Gắn sự kiện
    checkbox.addEventListener('change', handleCheckboxChange);
    
    // Khởi tạo ban đầu
    console.log('Script loaded, checkbox checked:', checkbox.checked);
    if (checkbox.checked) {
        handleCheckboxChange();
    }
});
</script>
