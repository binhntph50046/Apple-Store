@extends('admin.layouts.app')
@section('title', 'Sửa loại thuộc tính')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
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
                            <h5 class="m-b-10">Sửa loại thuộc tính</h5>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.attributes.index') }}">Loại thuộc tính</a></li>
                            <li class="breadcrumb-item" aria-current="page">Sửa</li>
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
                <div class="card custom-shadow">
                    <div class="card-header">
                        <h5>Sửa loại thuộc tính</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.attributes.update', $attributeType) }}" method="POST" id="editAttributeForm">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Tên</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $attributeType->name) }}">
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
                                                <option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', $attributeType->category_ids ?? [])) ? 'selected' : '' }}>
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
                                            <option value="active" {{ old('status', $attributeType->status) == 'active' ? 'selected' : '' }}>Hoạt động</option>
                                            <option value="inactive" {{ old('status', $attributeType->status) == 'inactive' ? 'selected' : '' }}>Không hoạt động</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Phần giá trị thuộc tính -->
                            <div class="mt-4">
                                <h5 class="mb-3">Giá trị thuộc tính</h5>
                                <div id="valuesContainer">
                                    @foreach($attributeValues as $index => $value)
                                    <div class="value-row" data-value-id="{{ $value->id }}">
                                        <input type="hidden" name="values[{{ $index }}][id]" value="{{ $value->id }}">
                                        <div class="row">
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="form-label">Giá trị</label>
                                                    <input type="text" class="form-control value-input" name="values[{{ $index }}][value]" required 
                                                           value="{{ is_array($value->value) ? implode(',', $value->value) : $value->value }}"
                                                           placeholder="Nhập giá trị thuộc tính">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-3">
                                                    <label class="color-section-label">Lựa chọn màu sắc</label>
                                                    <div class="color-section">
                                                        <div class="color-toggle">
                                                            <div class="form-check">
                                                                <input type="checkbox" class="form-check-input has-hex-color" 
                                                                       id="hasColor_{{ $index }}"
                                                                       {{ !empty($value->hex) && $value->hex[0] !== '' ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="hasColor_{{ $index }}">Có màu sắc</label>
                                                            </div>
                                                        </div>
                                                        <div class="color-picker-container" style="{{ !empty($value->hex) && $value->hex[0] !== '' ? '' : 'display: none;' }}">
                                                            <div class="color-preview" style="background-color: {{ !empty($value->hex) ? $value->hex[0] : '#000000' }};">
                                                                <input type="color" class="hex-color-input" 
                                                                       name="values[{{ $index }}][hex_color]"
                                                                       value="{{ !empty($value->hex) ? $value->hex[0] : '#000000' }}">
                                                            </div>
                                                            {{-- <span class="hex-value">{{ !empty($value->hex) ? $value->hex[0] : '#000000' }}</span> --}}
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
                                    @endforeach
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-info" id="addValue">
                                        <i class="fas fa-plus"></i> Thêm giá trị khác
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="deleted_values" id="deletedValues">

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">Cập nhật loại thuộc tính</button>
                                <a href="{{ route('admin.attributes.index') }}" class="btn btn-secondary">Quay lại</a>
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

        // Thêm các giá trị hiện có vào form khi trang tải
        $('.value-row').each(function() {
            const valueId = $(this).data('value-id');
            if (valueId) {
                $(this).append(`<input type="hidden" name="existing_values[]" value="${valueId}">`);
            }
        });

        // Xử lý nút thêm giá trị
        $('#addValue').click(function() {
            const index = Date.now(); // Sử dụng timestamp làm index duy nhất
            addValueRow(index);
        });

        // Xử lý nút xóa giá trị
        let deletedValues = [];
        $(document).on('click', '.remove-value', function() {
            const valueRow = $(this).closest('.value-row');
            const valueId = valueRow.data('value-id');
            
            if (valueId) {
                deletedValues.push(valueId);
                $('#deletedValues').val(deletedValues.join(','));
            }
            
            valueRow.remove();
            
            if ($('.value-row').length === 0) {
                const index = Date.now();
                addValueRow(index);
            }
        });

        // Xử lý thay đổi checkbox có màu hex
        $(document).on('change', '.has-hex-color', function() {
            const colorPickerContainer = $(this).closest('.color-section').find('.color-picker-container');
            const colorInput = colorPickerContainer.find('.hex-color-input');
            
            if ($(this).is(':checked')) {
                colorPickerContainer.fadeIn(200);
                colorInput.prop('disabled', false);
                if (!colorInput.val()) {
                    colorInput.val('#000000'); // Đặt màu mặc định khi được chọn
                    colorPickerContainer.find('.color-preview').css('background-color', '#000000');
                }
            } else {
                colorPickerContainer.fadeOut(200);
                colorInput.prop('disabled', true);
                colorInput.val(''); // Xóa giá trị khi bỏ chọn
            }
        });

        // Cập nhật xem trước màu khi màu thay đổi
        $(document).on('input change', '.hex-color-input', function() {
            const hexValue = $(this).val();
            $(this).closest('.color-preview').css('background-color', hexValue);
        });

        // Hàm thêm hàng giá trị
        function addValueRow(index) {
            const row = `
                <div class="value-row">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="form-label">Giá trị</label>
                                <input type="text" class="form-control value-input" name="values[${index}][value]" required placeholder="Nhập giá trị thuộc tính">
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="mb-3">
                                <label class="color-section-label">Lựa chọn màu sắc</label>
                                <div class="color-section">
                                    <div class="color-toggle">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input has-hex-color" id="hasColor_${index}">
                                            <label class="form-check-label" for="hasColor_${index}">Có màu sắc</label>
                                        </div>
                                    </div>
                                    <div class="color-picker-container" style="display: none;">
                                        <div class="color-preview" style="background-color: #000000;">
                                            <input type="color" class="hex-color-input" name="values[${index}][hex_color]" value="#000000">
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

        // Gửi form
        $('#editAttributeForm').on('submit', function(e) {
            e.preventDefault();
            
            // Vô hiệu hóa input hex_color cho các checkbox chưa được chọn
            $('.has-hex-color').each(function() {
                const colorInput = $(this).closest('.value-row').find('.hex-color-input');
                if (!$(this).is(':checked')) {
                    colorInput.prop('disabled', true);
                    colorInput.val('');
                } else {
                    colorInput.prop('disabled', false);
                }
            });

            // Thêm input ẩn để theo dõi các giá trị hiện có cần giữ lại
            $('.value-row').each(function() {
                const valueId = $(this).data('value-id');
                if (valueId) {
                    $(this).append(`<input type="hidden" name="existing_values[]" value="${valueId}">`);
                }
            });

            this.submit();
        });
    });
</script>
@endpush