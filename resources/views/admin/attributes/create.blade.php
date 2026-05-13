@extends('admin.layouts.app')
@section('title', 'Tạo loại thuộc tính')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }
    #attributeValuesForm {
        display: none;
    }
    .value-row {
        background-color: #f8f9fa;
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }
    .remove-value {
        color: #dc3545;
        cursor: pointer;
    }
    .color-section {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .color-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .color-toggle .form-check-input {
        width: 20px;
        height: 20px;
        cursor: pointer;
        margin-top: 0;
    }
    .color-toggle .form-check-label {
        cursor: pointer;
        user-select: none;
    }
    .color-picker-container {
        display: none;
        position: relative;
    }
    .color-preview {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        border: 2px solid #dee2e6;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .hex-color-input {
        position: absolute;
        width: 40px;
        height: 40px;
        padding: 0;
        margin: 0;
        top: 0;
        left: 0;
        cursor: pointer;
        border: none;
        opacity: 0;
    }
    .hex-color-input::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    .hex-color-input::-webkit-color-swatch {
        border: none;
    }
    .hex-value {
        margin-left: 10px;
        font-family: monospace;
        font-size: 14px;
        color: #6c757d;
        min-width: 70px;
    }
    .color-section-label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
    }
    .value-input {
        border-radius: 6px;
        border: 1px solid #ced4da;
        padding: 8px 12px;
        transition: border-color 0.15s ease-in-out;
    }
    .value-input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
</style>

@section('content')
<div class="pc-container">
    <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h5 class="m-b-10">Tạo loại thuộc tính</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}">Loại thuộc tính</a></li>
                            <li class="breadcrumb-item" aria-current="page">Tạo mới</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-12">
                <!-- Attribute Type Form -->
                <div class="card custom-shadow" id="attributeTypeForm">
                    <div class="card-header">
                        <h5>Tạo loại thuộc tính mới</h5>
                    </div>
                    <div class="card-body">
                        <form id="createAttributeTypeForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="category_ids" class="form-label">Danh mục</label>
                                        <select class="form-select select2" id="category_ids" name="category_ids[]" multiple required>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category_ids')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                            <option value="active" selected>Hoạt động</option>
                                            <option value="inactive">Không hoạt động</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Tạo loại thuộc tính</button>
                                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Quay lại</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Attribute Values Form -->
                <div class="card custom-shadow mt-4" id="attributeValuesForm">
                    <div class="card-header">
                        <h5>Thêm giá trị thuộc tính</h5>
                    </div>
                    <div class="card-body">
                        <form id="createAttributeValuesForm">
                            @csrf
                            <input type="hidden" id="attribute_type_id" name="attribute_type_id">
                            
                            <div id="valuesContainer">
                                <!-- Giá trị sẽ được thêm vào đây động -->
                            </div>

                            <div class="mb-3">
                                <button type="button" class="btn btn-info" id="addValue">Thêm giá trị khác</button>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Lưu giá trị</button>
                                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Bỏ qua & Hoàn thành</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#category_ids').select2({
            width: '100%',
            placeholder: "Chọn danh mục"
        });

        // Xử lý gửi form loại thuộc tính
        $('#createAttributeTypeForm').on('submit', function(e) {
            e.preventDefault();
            // Đặt lại lỗi trước đó
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: "{{ route('admin.attributes.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    if (response.success) {
                        $('#attribute_type_id').val(response.attributeType.id);
                        $('#attributeTypeForm').slideUp();
                        $('#attributeValuesForm').slideDown();
                        addValueRow();
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(function(key) {
                            const input = $(`[name="${key}"]`);
                            input.addClass('is-invalid');
                            input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                        });
                    } else {
                        alert('Đã xảy ra lỗi. Vui lòng thử lại.');
                    }
                }
            });
        });

        // Xử lý nút thêm giá trị
        $('#addValue').click(function() {
            addValueRow();
        });

        // Xử lý gửi form giá trị
        $('#createAttributeValuesForm').on('submit', function(e) {
            e.preventDefault();
            // Đặt lại lỗi trước đó
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
            $('.alert').remove();

            let values = [];
            $('.value-row').each(function() {
                const hasColor = $(this).find('.has-hex-color').is(':checked');
                values.push({
                    value: $(this).find('.value-input').val(),
                    hex_color: hasColor ? $(this).find('.hex-color-input').val() : null
                });
            });

            $.ajax({
                url: "{{ route('admin.attributes.store-values') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    attribute_type_id: $('#attribute_type_id').val(),
                    values: values
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = "{{ route('admin.attributes.index') }}";
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        
                        // Xử lý lỗi chung
                        if (errors.general) {
                            $('#valuesContainer').before(`
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    ${errors.general[0]}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            `);
                        }

                        // Xử lý lỗi trường cụ thể
                        Object.keys(errors).forEach(function(key) {
                            if (key.startsWith('values.')) {
                                const matches = key.match(/values\.(\d+)\.(value|hex_color)/);
                                if (matches) {
                                    const index = matches[1];
                                    const field = matches[2];
                                    const row = $('.value-row').eq(index);
                                    const input = field === 'value' ? 
                                        row.find('.value-input') : 
                                        row.find('.hex-color-input');
                                    
                                    input.addClass('is-invalid');
                                    input.after(`<div class="invalid-feedback">${errors[key][0]}</div>`);
                                }
                            }
                        });
                    } else {
                        $('#valuesContainer').before(`
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                Đã xảy ra lỗi. Vui lòng thử lại.
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        `);
                    }
                }
            });
        });

        // Xử lý thay đổi checkbox có màu hex
        $(document).on('change', '.has-hex-color', function() {
            const colorPickerContainer = $(this).closest('.color-section').find('.color-picker-container');
            if ($(this).is(':checked')) {
                colorPickerContainer.fadeIn(200);
                // Cập nhật màu preview ngay khi hiển thị
                const colorInput = colorPickerContainer.find('.hex-color-input');
                const colorPreview = colorPickerContainer.find('.color-preview');
                colorPreview.css('background-color', colorInput.val());
            } else {
                colorPickerContainer.fadeOut(200);
            }
        });

        // Cập nhật xem trước màu khi màu thay đổi
        $(document).on('input change', '.hex-color-input', function() {
            const hexValue = $(this).val();
            $(this).closest('.color-preview').css('background-color', hexValue);
        });

        // Hàm thêm hàng giá trị
        function addValueRow() {
            const row = `
                <div class="value-row">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Giá trị</label>
                                <input type="text" class="form-control value-input" placeholder="Nhập giá trị thuộc tính">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="color-section-label">Lựa chọn màu sắc</label>
                                <div class="color-section">
                                    <div class="color-toggle">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input has-hex-color" id="hasColor_${Date.now()}">
                                            <label class="form-check-label" for="hasColor_${Date.now()}">Có màu sắc</label>
                                        </div>
                                    </div>
                                    <div class="color-picker-container">
                                        <div class="color-preview" style="background-color: #000000;">
                                            <input type="color" class="hex-color-input" value="#000000">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-danger remove-value">
                                    <i class="fas fa-trash-alt"></i> Xóa
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('#valuesContainer').append(row);
        }

        // Xử lý nút xóa giá trị
        $(document).on('click', '.remove-value', function() {
            if ($('.value-row').length > 1) {
                $(this).closest('.value-row').remove();
            } else {
                alert('Cần ít nhất một giá trị.');
            }
        });
    });
</script>
@endpush