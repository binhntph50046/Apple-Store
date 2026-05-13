@extends('admin.layouts.app')
@section('title', 'Tạo sản phẩm mới')

<style>
    .custom-shadow {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        background-color: #fff;
        padding: 16px;
    }

    .variant-row {
        border: 1px solid #ddd;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .preview-image {
        max-width: 150px;
        max-height: 150px;
        margin-top: 10px;
    }

    .error-message {
        color: red;
        font-size: 0.9em;
        display: none;
    }
</style>

@section('content')
    <!-- Add Select2 CSS at the top of the file -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <div class="pc-container">
        <div class="pc-content">
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Tạo Sản Phẩm</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                                <li class="breadcrumb-item" aria-current="page">Tạo mới</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card custom-shadow">
                        <div class="card-header">
                            <h5>Tạo Sản Phẩm Mới</h5>
                        </div>
                        <div class="card-body">
                            <!-- Thay thế section hiển thị lỗi trong view -->
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <h6><strong>Có lỗi xảy ra:</strong></h6>
                                    <ul class="mb-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>

                                    <!-- Thông báo đặc biệt cho vấn đề ảnh -->
                                    @if ($errors->has('variants.*.images.*'))
                                        <div class="mt-2 p-2 bg-warning text-dark rounded">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <strong>Lưu ý về ảnh:</strong> Do có lỗi validation, bạn cần chọn lại ảnh cho
                                            các biến thể.
                                            Chúng tôi đã lưu thông tin về các file đã chọn để bạn tham khảo.
                                        </div>
                                    @endif

                                    <!-- Hướng dẫn khắc phục -->
                                    <div class="mt-2 p-2 bg-info text-white rounded">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Hướng dẫn:</strong>
                                        <ul class="mt-1 mb-0">
                                            <li>Kiểm tra và sửa các trường bị lỗi ở trên</li>
                                            <li>Đảm bảo đã chọn ít nhất một thuộc tính với giá trị hợp lệ</li>
                                            <li>Không chọn trùng loại thuộc tính</li>
                                            <li>Chọn lại ảnh cho các biến thể nếu cần</li>
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Thêm script để highlight các trường có lỗi -->
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Highlight các trường có lỗi
                                    const errorFields = document.querySelectorAll('.is-invalid');
                                    errorFields.forEach(field => {
                                        field.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });
                                        field.focus();
                                        return; // Chỉ focus vào trường đầu tiên
                                    });

                                    // Thêm tooltip cho các trường bắt buộc
                                    const requiredFields = document.querySelectorAll('input[required], select[required]');
                                    requiredFields.forEach(field => {
                                        if (!field.title) {
                                            field.title = 'Trường này là bắt buộc';
                                        }
                                    });
                                });
                            </script>

                            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                                id="productForm">
                                @csrf

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Tên sản phẩm <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                                id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Danh mục <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select @error('category_id') is-invalid @enderror"
                                                id="category_id" name="category_id" required>
                                                <option value="">Chọn danh mục</option>
                                                @foreach ($categories as $category)
                                                    <option value="{{ $category->id }}"
                                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="warranty_months" class="form-label">Tháng bảo hành <span
                                                    class="text-danger">*</span></label>
                                            <input type="number"
                                                class="form-control @error('warranty_months') is-invalid @enderror"
                                                id="warranty_months" name="warranty_months"
                                                value="{{ old('warranty_months', 12) }}" min="0" required>
                                            @error('warranty_months')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Mô tả ngắn</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                                rows="3">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="content" class="form-label">Mô tả chi tiết</label>
                                            <textarea class="snettech-editor form-control @error('content') is-invalid @enderror" id="content" name="content">{{ old('content') }}</textarea>
                                            @error('content')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Sản phẩm nổi bật</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="is_featured"
                                                    name="is_featured" value="1"
                                                    {{ old('is_featured', 0) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">Đặt làm nổi bật</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Technical Specifications -->
                                <div class="mb-3" id="specifications-section" style="display: none;">
                                    <label class="form-label">Thông số kỹ thuật</label>
                                    <div id="categorySpecifications">
                                        <p class="text-muted">Vui lòng chọn danh mục để hiển thị thông số kỹ thuật.</p>
                                    </div>
                                </div>

                                <!-- Variant Attributes -->
                                <div class="mb-3" id="variant-attributes-section" style="display: none;">
                                    <label class="form-label">Thuộc tính biến thể <span
                                            class="text-danger">*</span></label>
                                    <div id="variant-attributes">
                                        <div id="attributes-wrapper">
                                            <div class="row mb-2 attribute-row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Thuộc tính 1</label>
                                                        <select class="form-select attribute-type"
                                                            name="attributes[0][attribute_type_id]" id="attribute_type_0">
                                                            <option value="">-- Chọn thuộc tính --</option>
                                                            @foreach ($attributeTypes as $type)
                                                                <option value="{{ $type->id }}"
                                                                    {{ old('attributes.0.attribute_type_id') == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <div class="error-message" id="error-type-0">Vui lòng chọn loại
                                                            thuộc tính.</div> --}}
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Giá trị</label>
                                                        <select class="form-select attribute-values"
                                                            name="attributes[0][selected_values][]" id="values_0"
                                                            multiple>
                                                            <option value="">-- Chọn giá trị --</option>
                                                        </select>
                                                        <div class="error-message" id="error-values-0">Vui lòng chọn ít
                                                            nhất một giá trị.</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">Thuộc tính 2</label>
                                                        <select class="form-select attribute-type"
                                                            name="attributes[1][attribute_type_id]" id="attribute_type_1">
                                                            <option value="">-- Chọn thuộc tính --</option>
                                                            @foreach ($attributeTypes as $type)
                                                                <option value="{{ $type->id }}"
                                                                    {{ old('attributes.1.attribute_type_id') == $type->id ? 'selected' : '' }}>
                                                                    {{ $type->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        {{-- <div class="error-message" id="error-type-1">Vui lòng chọn loại
                                                            thuộc tính.</div> --}}
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Giá trị</label>
                                                        <select class="form-select attribute-values"
                                                            name="attributes[1][selected_values][]" id="values_1"
                                                            multiple>
                                                            <option value="">-- Chọn giá trị --</option>
                                                        </select>
                                                        <div class="error-message" id="error-values-1">Vui lòng chọn ít
                                                            nhất một giá trị.</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="error-message" id="error-duplicate" style="display: none;">Không được
                                        phép chọn trùng loại thuộc tính.</div>
                                    <div class="error-message" id="error-min-attributes" style="display: none;">Vui lòng
                                        chọn ít nhất một thuộc tính với giá trị hợp lệ.</div>
                                </div>

                                <!-- Generated Variants -->
                                <div class="mb-3">
                                    <label class="form-label">Biến thể sản phẩm</label> <br>
                                    <button type="button" class="btn btn-primary mb-3" id="generate-variants">Tạo biến
                                        thể</button>
                                    <div id="variantsContainer">
                                        @if (old('variants'))
                                            @foreach (old('variants') as $index => $variant)
                                                <div class="variant-row" data-index="{{ $index }}">
                                                    <h6>Biến thể {{ $index + 1 }}: {{ $variant['name'] ?? '' }}</h6>
                                                    <input type="hidden" name="variants[{{ $index }}][name]"
                                                        value="{{ $variant['name'] ?? '' }}">
                                                    <input type="hidden" name="variants[{{ $index }}][slug]"
                                                        value="{{ $variant['slug'] ?? '' }}">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Tồn kho</label>
                                                                <input type="number" class="form-control"
                                                                    name="variants[{{ $index }}][stock]"
                                                                    min="0" value="{{ $variant['stock'] ?? '' }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Giá nhập</label>
                                                                <input type="number" class="form-control"
                                                                    name="variants[{{ $index }}][purchase_price]"
                                                                    min="0" step="0.01"
                                                                    value="{{ $variant['purchase_price'] ?? '' }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <div class="mb-3">
                                                                <label class="form-label">Giá bán</label>
                                                                <input type="number" class="form-control"
                                                                    name="variants[{{ $index }}][selling_price]"
                                                                    min="0" step="0.01"
                                                                    value="{{ $variant['selling_price'] ?? '' }}"
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Hình ảnh</label>
                                                                <input type="file" class="form-control variant-images"
                                                                    name="variants[{{ $index }}][images][]"
                                                                    multiple accept="image/*">
                                                                <div id="preview-{{ $index }}"
                                                                    class="preview-images-container mt-2 d-flex flex-wrap gap-2">
                                                                    @if (isset($variant['images']) && is_array($variant['images']))
                                                                        @foreach ($variant['images'] as $image)
                                                                            <img src="{{ asset('storage/' . $image) }}"
                                                                                class="preview-image">
                                                                        @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-3">
                                                                <label class="form-label">Biến thể mặc định</label>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input default-variant-toggle"
                                                                        type="checkbox"
                                                                        name="variants[{{ $index }}][is_default]"
                                                                        id="is_default_{{ $index }}"
                                                                        value="1"
                                                                        {{ !empty($variant['is_default']) ? 'checked' : '' }}>
                                                                    <label class="form-check-label"
                                                                        for="is_default_{{ $index }}">Đặt làm mặc
                                                                        định</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @if (isset($variant['attributes']) && is_array($variant['attributes']))
                                                        @foreach ($variant['attributes'] as $attrIdx => $attr)
                                                            {{-- Thêm dòng này để luôn có attribute_type_id --}}
                                                            <input type="hidden"
                                                                name="variants[{{ $index }}][attributes][{{ $attrIdx }}][attribute_type_id]"
                                                                value="{{ $attr['attribute_type_id'] ?? '' }}">
                                                            @if (isset($attr['selected_values']) && is_array($attr['selected_values']))
                                                                @foreach ($attr['selected_values'] as $sv)
                                                                    <input type="hidden"
                                                                        name="variants[{{ $index }}][attributes][{{ $attrIdx }}][selected_values][]"
                                                                        value="{{ $sv }}">
                                                                @endforeach
                                                            @else
                                                                <input type="hidden"
                                                                    name="variants[{{ $index }}][attributes][{{ $attrIdx }}][selected_values][]"
                                                                    value="{{ $attr['selected_values'] ?? '' }}">
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Tạo sản phẩm</button>
                                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Hủy</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Truyền dữ liệu old specifications từ Blade sang JavaScript
        const oldSpecifications = @json(old('specifications', []));

        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            if (categorySelect.value) {
                loadCategoryData(categorySelect.value);
            }

            // Khởi tạo Select2 cho tất cả select box thuộc tính
            $('.attribute-values').select2({
                width: '100%',
                placeholder: 'Chọn giá trị',
                allowClear: true,
                multiple: true,
                templateResult: formatAttributeValue,
                templateSelection: formatAttributeValue
            });

            function formatAttributeValue(data) {
                if (!data.id) return data.text;

                const option = data.element;
                const hex = option ? option.getAttribute('data-hex') : null;

                if (hex) {
                    return $(
                        `<span>${data.text} <span style="display:inline-block;width:15px;height:15px;background-color:${hex};margin-left:5px;vertical-align:middle;border:1px solid #ddd;"></span></span>`
                    );
                }

                return data.text;
            }

            // Xử lý khi thay đổi loại thuộc tính
            $('.attribute-type').on('change', function() {
                const index = $(this).attr('id').replace('attribute_type_', '');
                const valueSelect = $(`#values_${index}`);
                const attributeTypeId = $(this).val();

                if (attributeTypeId) {
                    // Load giá trị thuộc tính
                    fetch(`/admin/attributes/${attributeTypeId}/values`)
                        .then(response => response.json())
                        .then(data => {
                            valueSelect.empty().trigger('change');

                            data.forEach(item => {
                                const option = new Option(item.value, item.id, false, false);
                                if (item.hex) {
                                    option.setAttribute('data-hex', item.hex);
                                }
                                valueSelect.append(option);
                            });

                            // Nếu có dữ liệu cũ, khôi phục lại
                            const oldAttributes = @json(old('attributes', []));
                            if (oldAttributes[index] && oldAttributes[index].selected_values) {
                                const oldValues = oldAttributes[index].selected_values.map(v =>
                                    parseInt(v)).filter(v => !isNaN(v));
                                $(valueSelect).val(oldValues).trigger('change');
                            }
                        });
                } else {
                    valueSelect.empty().trigger('change');
                }
            });

            // Khôi phục giá trị đã chọn sau khi validate
            const oldAttributes = @json(old('attributes', []));
            if (oldAttributes.length > 0) {
                oldAttributes.forEach((attr, idx) => {
                    const typeSelect = document.getElementById(`attribute_type_${idx}`);
                    const valueSelect = document.getElementById(`values_${idx}`);

                    if (typeSelect && valueSelect) {
                        // Set attribute type
                        if (attr.attribute_type_id) {
                            typeSelect.value = attr.attribute_type_id;
                            typeSelect.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }

            validateAttributeType(0);
            validateAttributeType(1);
            validateAttributeValues(0);
            validateAttributeValues(1);
        });

        function loadCategoryData(categoryId) {
            // Show/hide sections based on category selection
            const variantAttributesSection = document.getElementById('variant-attributes-section');
            const specificationsSection = document.getElementById('specifications-section');

            variantAttributesSection.style.display = categoryId ? 'block' : 'none';
            specificationsSection.style.display = categoryId ? 'block' : 'none';

            // Load specifications
            if (categoryId) {
                fetch(`/admin/categories/${categoryId}/specifications`)
                    .then(response => response.json())
                    .then(data => {
                        const container = document.getElementById('categorySpecifications');
                        container.innerHTML = '';
                        if (data.length > 0) {
                            const wrapper = document.createElement('div');
                            wrapper.id = 'specifications-wrapper';
                            let row = null;
                            data.forEach((spec, idx) => {
                                if (idx % 2 === 0) {
                                    row = document.createElement('div');
                                    row.className = 'row mb-2 spec-row';
                                    if (idx / 2 >= 3) row.style.display = 'none';
                                    wrapper.appendChild(row);
                                }
                                const col = document.createElement('div');
                                col.className = 'col-md-6';
                                // Lấy giá trị từ oldSpecifications nếu có
                                const oldValue = oldSpecifications[idx] ? oldSpecifications[idx].value : '';
                                col.innerHTML = `
                                    <div class="mb-3">
                                        <label class="form-label">${spec.name}</label>
                                        <input type="hidden" name="specifications[${idx}][specification_id]" value="${spec.id}">
                                        <input type="text" class="form-control" name="specifications[${idx}][value]" value="${oldValue || ''}" placeholder="Nhập thông số (không bắt buộc)">
                                    </div>
                                `;
                                row.appendChild(col);
                            });
                            container.appendChild(wrapper);

                            if (data.length > 6) {
                                const seeMoreBtn = document.createElement('button');
                                seeMoreBtn.type = 'button';
                                seeMoreBtn.className = 'btn btn-link p-0';
                                seeMoreBtn.textContent = 'Xem thêm';
                                seeMoreBtn.onclick = function() {
                                    document.querySelectorAll('.spec-row').forEach((row, idx) => {
                                        if (idx >= 3) row.style.display = '';
                                    });
                                    this.style.display = 'none';
                                };
                                container.appendChild(seeMoreBtn);
                            }
                        } else {
                            container.innerHTML = '<p class="text-muted">Không tìm thấy thông số cho danh mục này.</p>';
                        }
                    });

                // Load attribute types
                fetch(`/admin/categories/${categoryId}/attributes`)
                    .then(response => response.json())
                    .then(data => {
                        const selects = [
                            document.getElementById('attribute_type_0'),
                            document.getElementById('attribute_type_1')
                        ];
                        const valueSelects = [
                            document.getElementById('values_0'),
                            document.getElementById('values_1')
                        ];

                        selects.forEach((select, idx) => {
                            select.disabled = false;
                            valueSelects[idx].disabled = false;

                            const selectedValue = select.value;
                            select.options.length = 1; // Giữ lại option đầu tiên
                            data.forEach(attr => {
                                const option = document.createElement('option');
                                option.value = attr.id;
                                option.textContent = attr.name;
                                select.appendChild(option);
                            });
                            if (selectedValue) {
                                select.value = selectedValue;
                                select.dispatchEvent(new Event('change'));
                            }
                        });
                    });
            }
        }

        // Thêm event listener cho category select
        document.getElementById('category_id').addEventListener('change', function() {
            const categoryId = this.value;
            const variantAttributesSection = document.getElementById('variant-attributes-section');

            if (categoryId) {
                loadCategoryData(categoryId);
                variantAttributesSection.style.display = 'block';
            } else {
                // Reset specifications
                document.getElementById('categorySpecifications').innerHTML = '';
                // Reset and hide attribute types
                variantAttributesSection.style.display = 'none';
                const selects = [
                    document.getElementById('attribute_type_0'),
                    document.getElementById('attribute_type_1')
                ];
                const valueSelects = [
                    document.getElementById('values_0'),
                    document.getElementById('values_1')
                ];
                selects.forEach((select, idx) => {
                    select.options.length = 1;
                    select.disabled = true;
                    valueSelects[idx].disabled = true;
                });
            }
        });

        function validateAttributeType(index) {
            const select = document.getElementById(`attribute_type_${index}`);
            const error = document.getElementById(`error-type-${index}`);
            if (select) {
                const value = select.value;
                if (value) {
                    error.style.display = 'none';
                } else {
                    error.style.display = 'block';
                }
            }
        }

        function validateAttributeValues(index) {
            const input = document.getElementById(`values_${index}`);
            const error = document.getElementById(`error-values-${index}`);
            if (input) {
                input.addEventListener('input', function() {
                    const values = this.value.split(',').map(v => v.trim()).filter(v => v.length > 0);
                    if (values.length > 0) {
                        error.style.display = 'none';
                    } else {
                        error.style.display = 'block';
                    }
                });
            }
        }

        function validateHexValues(index) {
            const input = document.getElementById(`hex_${index}`);
            const error = document.getElementById(`error-hex-${index}`);
            if (input) {
                input.addEventListener('input', function() {
                    const hexValues = this.value.split(',').map(h => h.trim()).filter(h => h.length > 0);
                    error.style.display = 'none';
                });
            }
        }

        function validateAllAttributes() {
            const selects = document.querySelectorAll('.attribute-type');
            const valueSelects = document.querySelectorAll('.attribute-values');
            let valid = false;
            let hasError = false;

            // Reset error messages
            document.querySelectorAll('.error-message').forEach(error => {
                error.style.display = 'none';
            });

            // Validate first attribute (required)
            const firstSelect = selects[0];
            const firstValueSelect = valueSelects[0];
            const errorType0 = document.getElementById('error-type-0');
            const errorValue0 = document.getElementById('error-values-0');

            // Validate second attribute (optional)
            const secondSelect = selects[1];
            const secondValueSelect = valueSelects[1];
            const errorType1 = document.getElementById('error-type-1');
            const errorValue1 = document.getElementById('error-values-1');

            // Check if at least one attribute is selected and valid
            if (firstSelect.value && $(firstValueSelect).val() && $(firstValueSelect).val().length > 0) {
                valid = true;
            } else if (secondSelect && secondSelect.value && $(secondValueSelect).val() && $(secondValueSelect).val()
                .length > 0) {
                valid = true;
            }

            // If first attribute is selected but no values chosen
            if (firstSelect.value && (!$(firstValueSelect).val() || $(firstValueSelect).val().length === 0)) {
                errorValue0.style.display = 'block';
                hasError = true;
            }

            // If second attribute is selected but no values chosen
            if (secondSelect && secondSelect.value && (!$(secondValueSelect).val() || $(secondValueSelect).val().length ===
                    0)) {
                errorValue1.style.display = 'block';
                hasError = true;
            }

            // Check for duplicate attribute types
            if (firstSelect.value && secondSelect && secondSelect.value && firstSelect.value === secondSelect.value) {
                errorType1.style.display = 'block';
                errorType1.textContent = 'Không được chọn trùng loại thuộc tính.';
                hasError = true;
            }

            const errorMinAttributes = document.getElementById('error-min-attributes');
            if (!valid) {
                errorMinAttributes.style.display = 'block';
                errorMinAttributes.textContent = 'Vui lòng chọn ít nhất một thuộc tính với giá trị hợp lệ.';
            }

            return valid && !hasError;
        }

        document.getElementById('generate-variants').addEventListener('click', function() {
            const nameInput = document.getElementById('name');
            if (!nameInput.value.trim()) {
                alert('Vui lòng nhập tên sản phẩm.');
                return;
            }

            // Kiểm tra trùng lặp thuộc tính
            const attributeType0 = document.getElementById('attribute_type_0');
            const attributeType1 = document.getElementById('attribute_type_1');

            if (attributeType0.value && attributeType1.value && attributeType0.value === attributeType1.value) {
                alert('Không được chọn trùng loại thuộc tính!');
                return;
            }

            if (!validateAllAttributes()) {
                alert('Vui lòng chọn ít nhất một thuộc tính với giá trị hợp lệ.');
                return;
            }

            // Thu thập dữ liệu thuộc tính
            const attributeData = [];
            const selects = document.querySelectorAll('.attribute-type');
            const valueSelects = document.querySelectorAll('.attribute-values');

            selects.forEach((select, idx) => {
                if (select && select.value) {
                    const valueSelect = valueSelects[idx];
                    if (valueSelect && $(valueSelect).val() && $(valueSelect).val().length > 0) {
                        const selectedValues = Array.from(valueSelect.selectedOptions).map(opt => ({
                            id: parseInt(opt.value),
                            value: opt.text,
                            attribute_type_id: parseInt(select.value),
                            hex: opt.getAttribute('data-hex')
                        }));

                        if (selectedValues.length > 0) {
                            attributeData.push({
                                attribute_type_id: parseInt(select.value),
                                selected_values: selectedValues
                            });
                        }
                    }
                }
            });

            if (attributeData.length === 0) {
                alert('Vui lòng chọn ít nhất một thuộc tính với giá trị.');
                return;
            }

            // Tạo HTML cho các biến thể
            const variantsContainer = document.getElementById('variantsContainer');
            variantsContainer.innerHTML = '';

            // Tạo tổ hợp từ các giá trị đã chọn
            const combinations = generateCombinations(attributeData);

            // Render từng biến thể
            combinations.forEach((combination, index) => {
                const variantValues = combination.map(item => item.value).filter(Boolean);
                const variantName = nameInput.value + (variantValues.length > 0 ? ' - ' + variantValues
                    .join(' - ') : '');
                const variantSlug = variantName
                    .toLowerCase()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '');

                const oldVariant = @json(old('variants', []));
                const oldData = oldVariant[index] || {};

                // Tạo input hidden cho mỗi giá trị thuộc tính
                const attributeInputs = combination.map((attrValue, attrIdx) => {
                    if (!attrValue || !attrValue.id) return '';
                    return `
                        <input type="hidden" name="variants[${index}][attributes][${attrIdx}][attribute_type_id]" value="${attrValue.attribute_type_id}">
                        <input type="hidden" name="variants[${index}][attributes][${attrIdx}][selected_values][]" value="${attrValue.id}">
                    `;
                }).join('');

                const variantHtml = `
                    <div class="variant-row" data-index="${index}">
                        <h6>Biến thể ${index + 1}: ${variantName}</h6>
                        <input type="hidden" name="variants[${index}][name]" value="${variantName}">
                        <input type="hidden" name="variants[${index}][slug]" value="${variantSlug}">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Tồn kho</label>
                                    <input type="number" class="form-control" name="variants[${index}][stock]" min="0" value="${oldData.stock || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Giá nhập</label>
                                    <input type="number" class="form-control" name="variants[${index}][purchase_price]" min="0" step="0.01" value="${oldData.purchase_price || ''}" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label class="form-label">Giá bán</label>
                                    <input type="number" class="form-control" name="variants[${index}][selling_price]" min="0" step="0.01" value="${oldData.selling_price || ''}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Hình ảnh</label>
                                    <input type="file" class="form-control variant-images" name="variants[${index}][images][]" multiple accept="image/*">
                                    <div id="preview-${index}" class="preview-images-container mt-2 d-flex flex-wrap gap-2"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Biến thể mặc định</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input default-variant-toggle" type="checkbox" name="variants[${index}][is_default]" id="is_default_${index}" value="1" ${index === 0 || oldData.is_default ? 'checked' : ''}>
                                        <label class="form-check-label" for="is_default_${index}">Đặt làm mặc định</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        ${attributeInputs}
                    </div>
                `;
                variantsContainer.insertAdjacentHTML('beforeend', variantHtml);
            });

            // Khởi tạo các event listeners cho các input mới
            initializeVariantListeners();
        });

        function generateCombinations(arrays) {
            if (!arrays || arrays.length === 0) return [];

            const result = arrays.reduce((acc, curr) => {
                if (acc.length === 0) {
                    return curr.selected_values.map(value => [value]);
                }

                const combinations = [];
                acc.forEach(combination => {
                    curr.selected_values.forEach(value => {
                        combinations.push([...combination, value]);
                    });
                });
                return combinations;
            }, []);

            return result;
        }

        function initializeVariantListeners() {
            // Khởi tạo preview ảnh cho các input file mới
            document.querySelectorAll('.variant-images').forEach((input, index) => {
                input.addEventListener('change', function() {
                    previewVariantImages(index, this);
                });
            });

            // Khởi tạo xử lý cho các checkbox default
            document.querySelectorAll('.default-variant-toggle').forEach((checkbox, index) => {
                checkbox.addEventListener('change', function() {
                    if (this.checked) {
                        document.querySelectorAll('.default-variant-toggle').forEach((cb, idx) => {
                            if (idx !== index) cb.checked = false;
                        });
                    }
                });
            });
        }

        function previewVariantImages(index, input) {
            const previewContainer = document.getElementById(`preview-${index}`);
            previewContainer.innerHTML = '';

            if (input.files) {
                Array.from(input.files).forEach((file, i) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative d-inline-block';
                        wrapper.style.width = '100px';
                        wrapper.style.height = '100px';
                        wrapper.style.margin = '5px';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '4px';
                        img.style.border = '1px solid #ddd';

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-danger btn-sm position-absolute';
                        removeBtn.innerHTML = '×';
                        removeBtn.style.top = '-5px';
                        removeBtn.style.right = '-5px';
                        removeBtn.style.padding = '0 6px';
                        removeBtn.style.fontSize = '12px';
                        removeBtn.style.lineHeight = '1';
                        removeBtn.style.borderRadius = '50%';
                        removeBtn.onclick = function() {
                            // Tạo FileList mới không chứa file bị xóa
                            const dt = new DataTransfer();
                            Array.from(input.files).forEach((f, idx) => {
                                if (idx !== i) {
                                    dt.items.add(f);
                                }
                            });
                            input.files = dt.files;
                            wrapper.remove();

                            // Cập nhật lại preview
                            previewVariantImages(index, input);
                        };

                        wrapper.appendChild(img);
                        wrapper.appendChild(removeBtn);
                        previewContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            }
        }

        const style = document.createElement('style');
        style.textContent = `
            .preview-images-container {
                min-height: 50px;
            }
            .preview-image {
                border: 1px solid #ddd;
                transition: all 0.3s ease;
            }
            .preview-image:hover {
                transform: scale(1.05);
            }
            .btn-danger {
                opacity: 0.8;
            }
            .btn-danger:hover {
                opacity: 1;
            }
        `;
        document.head.appendChild(style);

        // Lưu trữ ảnh tạm thời trong localStorage
        let tempImageStorage = {};

        function saveImagesToTemp() {
            const variantRows = document.querySelectorAll('.variant-row');
            variantRows.forEach((row, index) => {
                const fileInput = row.querySelector('.variant-images');
                if (fileInput && fileInput.files.length > 0) {
                    const files = Array.from(fileInput.files);
                    tempImageStorage[index] = files.map(file => ({
                        name: file.name,
                        size: file.size,
                        type: file.type,
                        lastModified: file.lastModified
                    }));
                }
            });
            localStorage.setItem('tempProductImages', JSON.stringify(tempImageStorage));
        }

        function restoreImagesFromTemp() {
            const stored = localStorage.getItem('tempProductImages');
            if (stored) {
                try {
                    tempImageStorage = JSON.parse(stored);
                    // Khôi phục preview images (chỉ hiển thị tên file vì không thể khôi phục file object)
                    Object.keys(tempImageStorage).forEach(index => {
                        const previewContainer = document.getElementById(`preview-${index}`);
                        if (previewContainer) {
                            previewContainer.innerHTML = '';
                            tempImageStorage[index].forEach(fileInfo => {
                                const fileDiv = document.createElement('div');
                                fileDiv.className = 'alert alert-info';
                                fileDiv.innerHTML =
                                    `<small>File đã chọn trước đó: ${fileInfo.name} (${(fileInfo.size/1024/1024).toFixed(2)}MB)</small>`;
                                previewContainer.appendChild(fileDiv);
                            });
                        }
                    });
                } catch (e) {
                    console.error('Không thể khôi phục ảnh tạm thời:', e);
                }
            }
        }

        // Cập nhật hàm previewVariantImages
        function previewVariantImages(index, input) {
            const previewContainer = document.getElementById(`preview-${index}`);
            previewContainer.innerHTML = '';

            if (input.files) {
                Array.from(input.files).forEach((file, i) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const wrapper = document.createElement('div');
                        wrapper.className = 'position-relative d-inline-block';
                        wrapper.style.width = '100px';
                        wrapper.style.height = '100px';
                        wrapper.style.margin = '5px';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'preview-image';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        img.style.borderRadius = '4px';
                        img.style.border = '1px solid #ddd';

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'btn btn-danger btn-sm position-absolute';
                        removeBtn.innerHTML = '×';
                        removeBtn.style.top = '-5px';
                        removeBtn.style.right = '-5px';
                        removeBtn.style.padding = '0 6px';
                        removeBtn.style.fontSize = '12px';
                        removeBtn.style.lineHeight = '1';
                        removeBtn.style.borderRadius = '50%';
                        removeBtn.onclick = function() {
                            // Tạo FileList mới không chứa file bị xóa
                            const dt = new DataTransfer();
                            Array.from(input.files).forEach((f, idx) => {
                                if (idx !== i) {
                                    dt.items.add(f);
                                }
                            });
                            input.files = dt.files;
                            wrapper.remove();

                            // Cập nhật lại preview
                            previewVariantImages(index, input);
                        };

                        wrapper.appendChild(img);
                        wrapper.appendChild(removeBtn);
                        previewContainer.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });

                // Lưu thông tin file vào temp storage
                saveImagesToTemp();
            }
        }

        // Khôi phục ảnh khi trang load (nếu có lỗi validation)
        document.addEventListener('DOMContentLoaded', function() {
            // Các code khác...

            // Khôi phục ảnh tạm thời
            restoreImagesFromTemp();

            // Xóa temp storage khi form submit thành công
            const form = document.getElementById('productForm');
            if (form) {
                form.addEventListener('submit', function() {
                    // Lưu ảnh trước khi submit
                    saveImagesToTemp();
                });
            }
        });

        // Xóa temp storage khi rời khỏi trang (success case)
        window.addEventListener('beforeunload', function() {
            // Chỉ xóa nếu không có lỗi validation
            const hasErrors = document.querySelector('.alert-danger');
            if (!hasErrors) {
                localStorage.removeItem('tempProductImages');
            }
        });
    </script>
@endsection

@push('scripts')
    <!-- Add Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo Select2 cho tất cả select box thuộc tính
            $('.attribute-values').select2({
                width: '100%',
                placeholder: 'Chọn giá trị',
                allowClear: true,
                multiple: true,
                templateResult: formatAttributeValue,
                templateSelection: formatAttributeValue
            });

            function formatAttributeValue(data) {
                if (!data.id) return data.text;

                const option = data.element;
                const hex = option ? option.getAttribute('data-hex') : null;

                if (hex) {
                    return $(
                        `<span>${data.text} <span style="display:inline-block;width:15px;height:15px;background-color:${hex};margin-left:5px;vertical-align:middle;border:1px solid #ddd;"></span></span>`
                    );
                }

                return data.text;
            }

            // Xử lý khi thay đổi loại thuộc tính
            $('.attribute-type').on('change', function() {
                const index = $(this).attr('id').replace('attribute_type_', '');
                const valueSelect = $(`#values_${index}`);
                const attributeTypeId = $(this).val();

                if (attributeTypeId) {
                    // Load giá trị thuộc tính
                    fetch(`/admin/attributes/${attributeTypeId}/values`)
                        .then(response => response.json())
                        .then(data => {
                            valueSelect.empty().trigger('change');

                            data.forEach(item => {
                                const option = new Option(item.value, item.id, false, false);
                                if (item.hex) {
                                    option.setAttribute('data-hex', item.hex);
                                }
                                valueSelect.append(option);
                            });

                            // Nếu có dữ liệu cũ, khôi phục lại
                            const oldAttributes = @json(old('attributes', []));
                            if (oldAttributes[index] && oldAttributes[index].selected_values) {
                                const oldValues = oldAttributes[index].selected_values.map(v =>
                                    parseInt(v)).filter(v => !isNaN(v));
                                $(valueSelect).val(oldValues).trigger('change');
                            }
                        });
                } else {
                    valueSelect.empty().trigger('change');
                }
            });

            // Khôi phục giá trị đã chọn sau khi validate
            const oldAttributes = @json(old('attributes', []));
            if (oldAttributes.length > 0) {
                oldAttributes.forEach((attr, idx) => {
                    const typeSelect = document.getElementById(`attribute_type_${idx}`);
                    const valueSelect = document.getElementById(`values_${idx}`);

                    if (typeSelect && valueSelect) {
                        // Set attribute type
                        if (attr.attribute_type_id) {
                            typeSelect.value = attr.attribute_type_id;
                            typeSelect.dispatchEvent(new Event('change'));
                        }
                    }
                });
            }

            // Validate attributes
            function validateAllAttributes() {
                const selects = document.querySelectorAll('.attribute-type');
                const valueSelects = document.querySelectorAll('.attribute-values');
                let valid = false;
                let hasError = false;

                // Reset error messages
                document.querySelectorAll('.error-message').forEach(error => {
                    error.style.display = 'none';
                });

                // Validate first attribute (required)
                const firstSelect = selects[0];
                const firstValueSelect = valueSelects[0];
                const errorType0 = document.getElementById('error-type-0');
                const errorValue0 = document.getElementById('error-values-0');

                // Validate second attribute (optional)
                const secondSelect = selects[1];
                const secondValueSelect = valueSelects[1];
                const errorType1 = document.getElementById('error-type-1');
                const errorValue1 = document.getElementById('error-values-1');

                // Check if at least one attribute is selected and valid
                if (firstSelect.value && $(firstValueSelect).val() && $(firstValueSelect).val().length > 0) {
                    valid = true;
                } else if (secondSelect && secondSelect.value && $(secondValueSelect).val() && $(secondValueSelect)
                    .val().length > 0) {
                    valid = true;
                }

                // If first attribute is selected but no values chosen
                if (firstSelect.value && (!$(firstValueSelect).val() || $(firstValueSelect).val().length === 0)) {
                    errorValue0.style.display = 'block';
                    hasError = true;
                }

                // If second attribute is selected but no values chosen
                if (secondSelect && secondSelect.value && (!$(secondValueSelect).val() || $(secondValueSelect).val()
                        .length === 0)) {
                    errorValue1.style.display = 'block';
                    hasError = true;
                }

                // Check for duplicate attribute types
                if (firstSelect.value && secondSelect && secondSelect.value && firstSelect.value === secondSelect
                    .value) {
                    errorType1.style.display = 'block';
                    errorType1.textContent = 'Không được chọn trùng loại thuộc tính.';
                    hasError = true;
                }

                const errorMinAttributes = document.getElementById('error-min-attributes');
                if (!valid) {
                    errorMinAttributes.style.display = 'block';
                    errorMinAttributes.textContent = 'Vui lòng chọn ít nhất một thuộc tính với giá trị hợp lệ.';
                }

                return valid && !hasError;
            }
        });
    </script>
@endpush
