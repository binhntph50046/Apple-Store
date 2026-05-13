@extends('client.layouts.app')
@section('title', 'Yêu cầu hoàn hàng')
@section('content')
<style>
.return-container {
    max-width: 800px;
    margin: 100px auto;
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    padding: 2.5rem 2rem 2rem 2rem;
}
.return-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    text-align: center;
}
.form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}
.form-check-label {
    font-weight: 500;
    color: #2c3e50;
}
.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}
.form-control, .form-control:focus {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    box-shadow: none;
    font-size: 1rem;
    transition: border-color 0.2s;
}
.form-control:focus {
    border-color: #667eea;
}
.btn-danger {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: #fff;
    font-weight: 600;
    border-radius: 10px;
    padding: 0.7rem 2rem;
    font-size: 1rem;
    transition: background 0.2s;
}
.btn-danger:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: #fff;
}
.btn-secondary {
    border-radius: 10px;
    padding: 0.7rem 2rem;
    font-size: 1rem;
    font-weight: 600;
}
.upload-info {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 0.25rem;
}
.alert {
    border-radius: 10px;
    border: none;
}
.alert-danger {
    background-color: #fee2e2;
    color: #991b1b;
}
@media (max-width: 600px) {
    .return-container {
        padding: 1.2rem 0.5rem;
    }
}
.form-check-input {
    position: relative !important;
    z-index: 10 !important;
}
input[type="checkbox"] {
    pointer-events: auto !important;
    opacity: 1 !important;
    z-index: 1000 !important;
    position: relative !important;
}
</style>
<div class="return-container">
    <div class="return-title">Yêu cầu hoàn hàng cho đơn #{{ $order->id }}</div>
    @php
        if (!function_exists('viMessage')) {
            function viMessage($message) {
                $map = [
                    'The reason field is required.' => 'Vui lòng nhập lý do hoàn hàng.',
                    'The image field is required.' => 'Vui lòng chọn hình ảnh minh chứng.',
                    'The proof video field is required.' => 'Vui lòng chọn video minh chứng.',
                    'The bank info field is required.' => 'Vui lòng nhập thông tin tài khoản ngân hàng.',
                ];
                if (!isset($map[$message]) && preg_match('/^The (.+) field is required\.$/i', $message, $m)) {
                    $attr = strtolower($m[1]);
                    $attrMap = [
                        'reason' => 'lý do hoàn hàng',
                        'image' => 'hình ảnh minh chứng',
                        'proof video' => 'video minh chứng',
                        'bank info' => 'thông tin tài khoản ngân hàng',
                        'items' => 'sản phẩm',
                        'quantity' => 'số lượng',
                    ];
                    $label = $attrMap[$attr] ?? $attr;
                    return 'Vui lòng nhập ' . $label . '.';
                }
                return $map[$message] ?? $message;
            }
        }
    @endphp
    {{-- Hiển thị lỗi ngay dưới từng trường, không dùng alert tổng --}}
    <form id="returnForm" action="{{ route('order.returns.store', $order->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-4">
            <label class="form-label">Chọn sản phẩm muốn hoàn:</label>
            @php
                // Helper function to safely handle both JSON strings and arrays
                if (!function_exists('getImagesArray')) {
                    function getImagesArray($images) {
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
            @foreach($order->items as $item)
                @php
                    $images = $item->variant && $item->variant->images ? getImagesArray($item->variant->images) : [];
                    $imgSrc = isset($images[0]) ? asset($images[0]) : (isset($item->product->image) ? asset($item->product->image) : asset('uploads/default/default.jpg'));
                @endphp
                <div class="form-check mb-2 d-flex align-items-center" style="gap: 10px;">
                    <div class="d-flex align-items-center flex-grow-1" style="cursor: pointer;">
                        <input class="form-check-input" type="checkbox" name="items[{{ $item->id }}][selected]" value="1" id="item{{ $item->id }}">
                        <label class="form-check-label d-flex align-items-center ms-2" for="item{{ $item->id }}" style="cursor: pointer;">
                            <img src="{{ $imgSrc }}" alt="Ảnh sản phẩm" style="width:48px; height:48px; object-fit:cover; border-radius:8px; border:1.5px solid #eee; margin-right:10px;">
                            <span>{{ $item->product->name }} <span class="text-muted">(SL: {{ $item->quantity }})</span></span>
                        </label>
                    </div>
                    <input type="number" name="items[{{ $item->id }}][quantity]" min="1" max="{{ $item->quantity }}" value="1" class="form-control @error('items.'.$item->id.'.quantity') is-invalid @enderror" style="width:80px;" placeholder="Số lượng hoàn">
                </div>
                @error('items.'.$item->id.'.quantity')
                    <div class="invalid-feedback d-block"><div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>{{ viMessage($message) }}</span></div></div>
                @enderror
            @endforeach
            @if ($errors->has('items'))
                <div class="invalid-feedback d-block"><div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>{{ viMessage($errors->first('items')) }}</span></div></div>
            @endif
        </div>
        <div class="mb-4">
            <label for="reason" class="form-label">Lý do hoàn hàng <span class="text-danger">*</span></label>
            <textarea name="reason" id="reason" class="form-control @error('reason') is-invalid @enderror" rows="3">{{ old('reason') }}</textarea>
            <div class="upload-info">Vui lòng mô tả chi tiết lý do hoàn hàng</div>
            @error('reason')
                <div class="invalid-feedback d-block"><div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>{{ viMessage($message) }}</span></div></div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="image" class="form-label">Hình ảnh minh chứng <span class="text-danger">*</span></label>
            <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
            <div class="upload-info">Chấp nhận các định dạng: JPG, PNG, JPEG. Kích thước tối đa: 2MB</div>
            @error('image')
                <div class="invalid-feedback d-block"><div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>{{ viMessage($message) }}</span></div></div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="proof_video" class="form-label">Video minh chứng <span class="text-danger">*</span></label>
            <input type="file" name="proof_video" id="proof_video" class="form-control @error('proof_video') is-invalid @enderror" accept="video/mp4,video/mov,video/avi">
            <div class="upload-info">Chấp nhận các định dạng: MP4, MOV, AVI. Kích thước tối đa: 20MB</div>
            @error('proof_video')
                <div class="invalid-feedback d-block"><div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>{{ viMessage($message) }}</span></div></div>
            @enderror
        </div>
        <div class="mb-4">
            <label for="bank_info" class="form-label">Thông tin tài khoản ngân hàng <span class="text-danger">*</span></label>
            <textarea name="bank_info" id="bank_info" class="form-control @error('bank_info') is-invalid @enderror" rows="2" placeholder="VD: Ngân hàng Vietcombank - STK: 1234567890 - Chủ TK: Nguyễn Văn A">{{ old('bank_info') }}</textarea>
            <div class="upload-info">Vui lòng nhập đầy đủ thông tin: Tên ngân hàng - Số tài khoản - Tên chủ tài khoản</div>
            @error('bank_info')
                <div class="invalid-feedback d-block"><div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>{{ viMessage($message) }}</span></div></div>
            @enderror
        </div>
        <div class="d-flex justify-content-center gap-3 mt-5">
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-undo me-2"></i>Gửi yêu cầu hoàn hàng
            </button>
            <a href="{{ route('order.index') }}" class="btn btn-secondary">Quay lại</a>
        </div>
    </form>
</div>
@endsection 

@push('scripts')
<style>
.invalid-feedback { display:block !important; margin-top:0.25rem; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('returnForm');
    const reason = document.getElementById('reason');
    const bankInfo = document.getElementById('bank_info');
    const imageInput = document.getElementById('image');
    const videoInput = document.getElementById('proof_video');

    function showFieldError(element, message) {
        element.classList.add('is-invalid');
        let err = element.parentNode.querySelector('.invalid-feedback');
        if (!err) {
            err = document.createElement('div');
            err.className = 'invalid-feedback d-block';
            element.parentNode.appendChild(err);
        }
        err.innerHTML = '<div style="display:flex;align-items:center;gap:8px;color:#dc3545;font-size:0.875rem;"><i class="fas fa-exclamation-circle" style="font-size:14px;"></i><span>'+message+'</span></div>';
    }

    function clearFieldError(element) {
        element.classList.remove('is-invalid');
        const err = element.parentNode.querySelector('.invalid-feedback');
        if (err) err.remove();
    }

    function validateText(element, label) {
        const value = element.value.trim();
        clearFieldError(element);
        if (!value) { showFieldError(element, label + ' là bắt buộc'); return false; }
        if (value.includes('  ')) { showFieldError(element, label + ' không được chứa khoảng trắng liên tiếp'); return false; }
        return true;
    }

    function validateFile(element, label) {
        clearFieldError(element);
        if (!element.files || element.files.length === 0) {
            showFieldError(element, 'Vui lòng chọn ' + label.toLowerCase());
            return false;
        }
        return true;
    }

    function validateItems() {
        let valid = true;
        const checkboxes = document.querySelectorAll('input[type="checkbox"][name^="items[" ]');
        let any = false;
        checkboxes.forEach(cb => { if (cb.checked) any = true; });
        const listContainer = checkboxes.length ? checkboxes[0].closest('.mb-4') : null;
        let topErr = document.querySelector('.items-error');
        if (!any) {
            if (!topErr && listContainer) {
                topErr = document.createElement('div');
                topErr.className = 'text-danger items-error';
                topErr.style.cssText = 'font-size:0.875rem;margin-top:6px;';
                topErr.textContent = 'Vui lòng chọn ít nhất 1 sản phẩm cần hoàn.';
                listContainer.appendChild(topErr);
            }
            valid = false;
        } else if (topErr) { topErr.remove(); }

        document.querySelectorAll('input[name^="items["][name$="[quantity]"]')
            .forEach(qty => {
                const row = qty.closest('.form-check');
                const cb = row.querySelector('input[type="checkbox"]');
                const min = parseInt(qty.min || '1', 10);
                const max = parseInt(qty.max || '9999', 10);
                if (cb && cb.checked) {
                    const val = parseInt(qty.value || '0', 10);
                    clearFieldError(qty);
                    if (isNaN(val) || val < min || val > max) {
                        showFieldError(qty, `Số lượng phải từ ${min} đến ${max}`);
                        valid = false;
                    }
                } else {
                    clearFieldError(qty);
                }
            });
        return valid;
    }

    [reason, bankInfo].forEach(el => {
        if (!el) return;
        el.addEventListener('blur', () => validateText(el, el === reason ? 'Lý do hoàn hàng' : 'Thông tin tài khoản ngân hàng'));
        el.addEventListener('input', () => clearFieldError(el));
    });
    [imageInput, videoInput].forEach(el => {
        if (!el) return;
        el.addEventListener('change', () => clearFieldError(el));
    });
    document.querySelectorAll('input[name^="items["][name$="[quantity]"]')
        .forEach(q => q.addEventListener('input', () => clearFieldError(q)));
    document.querySelectorAll('input[type="checkbox"][name^="items[" ]')
        .forEach(cb => cb.addEventListener('change', () => validateItems()));

    form.addEventListener('submit', function(e) {
        let ok = true;
        if (!validateItems()) ok = false;
        if (!validateText(reason, 'Lý do hoàn hàng')) ok = false;
        if (!validateFile(imageInput, 'Hình ảnh minh chứng')) ok = false;
        if (!validateFile(videoInput, 'Video minh chứng')) ok = false;
        if (!validateText(bankInfo, 'Thông tin tài khoản ngân hàng')) ok = false;
        if (!ok) e.preventDefault();
    });
});
</script>
@endpush