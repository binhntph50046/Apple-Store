@extends('admin.layouts.app')
@section('title', 'Tạo chương trình khuyến mãi')

@section('content')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">Tạo khuyến mãi</h5>
                            </div>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.flash-sales.index') }}">Khuyến mãi</a>
                                </li>
                                <li class="breadcrumb-item" aria-current="page">Tạo</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Tạo khuyến mãi mới</h5>
                        </div>
                        @if ($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif

                        <div class="card-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form action="{{ route('admin.flash-sales.store') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Tên khuyến mãi</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" placeholder="Nhập tên khuyến mãi"
                                            value="{{ old('name') }}">
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="start_time" class="form-label">Thời gian bắt đầu</label>
                                        <input type="datetime-local"
                                            class="form-control @error('start_time') is-invalid @enderror" id="start_time"
                                            name="start_time" value="{{ old('start_time') }}">
                                        @error('start_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="end_time" class="form-label">Thời gian kết thúc</label>
                                        <input type="datetime-local"
                                            class="form-control @error('end_time') is-invalid @enderror" id="end_time"
                                            name="end_time" value="{{ old('end_time') }}">
                                        @error('end_time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="status" class="form-select" disabled>
                                            <option value="0">Không kích hoạt</option>
                                        </select>
                                    </div>
                                </div>

                                <hr>

                                @php
                                    $oldItems = old('items', [
                                        [
                                            'product_id' => '',
                                            'product_variant_id' => '',
                                            'discount' => '',
                                            'discount_type' => 'percent',
                                            'count' => 1,
                                            'buy_limit' => 1,
                                        ],
                                    ]);
                                @endphp

                                <div id="flash-sale-items-container">
                                    <h6>Biến thể khuyến mãi</h6>
                                    @foreach (array_values($oldItems) as $index => $item)
                                        <div class="flash-sale-item row g-2 mb-2" data-index="{{ $index }}">
                                            <div class="col-md-3">
                                                <label class="form-label">Sản phẩm</label>
                                                <select name="items[{{ $index }}][product_id]"
                                                    class="form-select product-select @error("items.$index.product_id") is-invalid @enderror"
                                                    {{-- required --}}>
                                                    <option value="">-- Chọn sản phẩm --</option>
                                                    @foreach ($products as $product)
                                                        <option value="{{ $product->id }}"
                                                            {{ $item['product_id'] == $product->id ? 'selected' : '' }}>
                                                            {{ $product->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error("items.$index.product_id")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">Biến thể</label>
                                                <select name="items[{{ $index }}][product_variant_id]"
                                                    class="form-select variant-select @error("items.$index.product_variant_id") is-invalid @enderror"
                                                    data-selected="{{ $item['product_variant_id'] }}">
                                                    <option value="">-- Chọn biến thể --</option>
                                                </select>
                                                @error("items.$index.product_variant_id")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label">Giảm giá</label>
                                                <input type="number" step="1" min="1"
                                                    name="items[{{ $index }}][discount]"
                                                    class="form-control @error("items.$index.discount") is-invalid @enderror"
                                                    placeholder="Số" value="{{ $item['discount'] }}">
                                                @error("items.$index.discount")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Loại</label>
                                                <select name="items[{{ $index }}][discount_type]"
                                                    class="form-select @error("items.$index.discount_type") is-invalid @enderror">
                                                    <option value="percent"
                                                        {{ $item['discount_type'] == 'percent' ? 'selected' : '' }}>%
                                                    </option>
                                                    <option value="fixed"
                                                        {{ $item['discount_type'] == 'fixed' ? 'selected' : '' }}>₫
                                                    </option>
                                                </select>
                                                @error("items.$index.discount_type")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Số lượng</label>
                                                <input type="number" name="items[{{ $index }}][count]"
                                                    class="form-control @error("items.$index.count") is-invalid @enderror"
                                                    min="1" value="{{ $item['count'] }}" placeholder="Qty">
                                                @error("items.$index.count")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label">Giới hạn</label>
                                                <input type="number" name="items[{{ $index }}][buy_limit]"
                                                    class="form-control @error("items.$index.buy_limit") is-invalid @enderror"
                                                    min="1" value="{{ $item['buy_limit'] }}" placeholder="Limit">
                                                @error("items.$index.buy_limit")
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-1 d-flex align-items-center pt-4">
                                                <button type="button"
                                                    class="btn btn-danger btn-sm remove-item h-95">X</button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-primary" id="add-item">+ Thêm biến
                                        thể</button>
                                </div>

                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Tạo khuyến mãi</button>
                                    <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-secondary">Hủy</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
    <script>
        const productVariantsData = @json($products->keyBy('id')->map->variants);
    </script>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        let itemIndex = {{ count($oldItems) }};

        $(document).ready(function() {
            $('.product-select').select2({
                placeholder: "-- Chọn biến thể --",
                allowClear: true,
                width: '100%'
            });
        });

        // Load variant khi chọn product
        function populateVariants(productSelect) {
            const productId = $(productSelect).val();
            const variantSelect = $(productSelect).closest('.flash-sale-item').find('.variant-select');
            const selectedVariantId = variantSelect.data('selected'); // nếu có

            variantSelect.html('<option value="">-- Chọn biến thể --</option>');

            if (productVariantsData[productId]) {
                productVariantsData[productId].forEach(v => {
                    const isSelected = selectedVariantId == v.id ? 'selected' : '';
                    variantSelect.append(`<option value="${v.id}" ${isSelected}>${v.name} (${v.sku})</option>`);
                });
            }

            // Sau khi load xong thì bỏ data-selected
            variantSelect.removeAttr('data-selected');
        }

        $(document).on('change', '.product-select', function() {
            populateVariants(this);
        });


        // Thêm dòng mới
        $('#add-item').click(function() {
            const newItem = `
                <div class="flash-sale-item row align-items-top g-2 mb-3">
                    <div class="col-md-3">
                        <label class="form-label">Sản phẩm</label>
                        <select name="items[${itemIndex}][product_id]" class="form-select product-select">
                            <option value="">-- Chọn sản phẩm --</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Biến thể</label>
                        <select name="items[${itemIndex}][product_variant_id]" class="form-select variant-select" data-selected="">
                            <option value="">-- Chọn biến thể --</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Giảm giá</label>
                        <input type="number" step="1" min="0" name="items[${itemIndex}][discount]" class="form-control" placeholder="Số">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Loại</label>
                        <select name="items[${itemIndex}][discount_type]" class="form-select">
                            <option value="percent">%</option>
                            <option value="fixed">₫</option>
                        </select>
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Số lượng</label>
                        <input type="number" name="items[${itemIndex}][count]" class="form-control" value="1" placeholder="Qty" min="1">
                    </div>

                    <div class="col-md-1">
                        <label class="form-label">Giới hạn</label>
                        <input type="number" name="items[${itemIndex}][buy_limit]" class="form-control" value="1" placeholder="Limit" min="1">
                    </div>

                    <div class="col-md-1 d-flex align-items-center pt-4">
                        <button type="button" class="btn btn-danger btn-sm remove-item h-95">X</button>
                    </div>
                </div>
                `;


            $('#flash-sale-items-container').append(newItem);

            $('#flash-sale-items-container').find('.product-select').last().select2({
                placeholder: "-- Choose Product --",
                allowClear: true,
                width: '100%'
            });

            itemIndex++;
        });

        // Xoá dòng
        $(document).on('click', '.remove-item', function() {
            $(this).closest('.flash-sale-item').remove();
        });

        // Khôi phục lại danh sách biến thể nếu có old()
        $('#flash-sale-items-container .flash-sale-item').each(function() {
            const productSelect = $(this).find('.product-select');
            if (productSelect.val()) {
                populateVariants(productSelect);
            }
        });
    </script>
@endpush
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Ép tất cả input, select, select2 có cùng chiều cao */
        .flash-sale-item .form-control,
        .flash-sale-item .form-select,
        .flash-sale-item .select2-container .select2-selection--single {
            height: 38px !important;
            padding: 6px 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        /* Fix alignment cho select2 */
        .select2-container--default .select2-selection--single {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        /* Đảm bảo select2 rộng 100% */
        .select2-container {
            width: 100% !important;
        }

        .invalid-feedback {
            display: block;
            height: 10px;
            /* hoặc 1.5em */
            font-size: 13px;
        }

        .invalid-feedback:empty::after {
            content: " ";
            visibility: hidden;
            display: block;
        }
    </style>
@endpush
