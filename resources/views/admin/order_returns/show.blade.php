@extends('admin.layouts.app')
@section('title', 'Yêu cầu hoàn hàng')
@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 d-flex justify-content-center">
                <div class="card"
                    style="background: #fff; margin-top: 15px; padding: 32px 24px;  border: none; border-radius: 0; width: 100%; max-width: 700px; margin-left: auto; margin-right: auto;">
                    <div class="card-header text-dark bg-white"
                        style="background: #fff; border: none; padding-bottom: 0; border-radius: 0;">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-undo-alt fa-lg me-2"></i>
                            <h4 class="mb-0">Yêu cầu hoàn hàng #{{ $return->id }}</h4>
                            <span
                                class="badge ms-auto {{ $return->status == 'Chờ xử lí' ? 'bg-warning text-dark' : ($return->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ ucfirst($return->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-0 pt-3">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="mb-2">
                                    <span class="fw-bold"><i class="fas fa-receipt me-1"></i>Đơn hàng:</span>
                                    #{{ $return->order->id }}
                                </div>
                                <div class="mb-2">
                                    <span class="fw-bold"><i class="fas fa-user me-1"></i>Khách hàng:</span>
                                    {{ $return->user->name }} ({{ $return->user->email }})
                                </div>
                                <div class="mb-2">
                                    <span class="fw-bold"><i class="fas fa-calendar-alt me-1"></i>Ngày gửi:</span>
                                    {{ $return->created_at->format('d/m/Y H:i') }}
                                </div>
                                <div class="mb-2">
                                    <span class="fw-bold"><i class="fas fa-user-shield me-1"></i>Admin xử lý:</span>
                                    {{ $return->admin ? $return->admin->name : '-' }}
                                </div>
                                <div class="mb-2">
                                    <span class="fw-bold"><i class="fas fa-calendar-check me-1"></i>Ngày xử lý:</span>
                                    {{ $return->processed_at ? $return->processed_at->format('d/m/Y H:i') : '-' }}
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div class="mb-2 fw-bold"><i class="fas fa-image me-1"></i>Ảnh hoàn hàng:</div>
                                @if ($return->image)
                                    <img src="{{ asset($return->image) }}" alt="Ảnh hoàn hàng"
                                        style="max-width: 250px; max-height: 250px; object-fit: cover;">
                                @else
                                    <span class="text-muted">Không có</span>
                                @endif
                            </div>
                        </div>
                        <hr>

                        <div class="mb-3">
                            <span class="fw-bold"><i class="fas fa-video me-1"></i>Video hoàn hàng:</span>
                            <div class="d-flex justify-content-center my-2">
                                @if ($return->proof_video)
                                    <video src="{{ asset($return->proof_video) }}" controls
                                        style="max-width: 350px; max-height: 250px; object-fit: cover; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.08);">
                                    </video>
                                @else
                                    <span class="text-muted align-self-center">Không có</span>
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <span class="fw-bold"><i class="fas fa-comment-dots me-1"></i>Lý do hoàn hàng:</span>
                            <div class="alert alert-secondary mt-2 mb-0" style="white-space: pre-line;">
                                {{ $return->reason }}</div>
                        </div>
                        <div class="mb-3">
                            <span class="fw-bold"><i class="fas fa-university me-1"></i>Thông tin tài khoản ngân
                                hàng:</span>
                            <div class="alert alert-info mt-2 mb-0" style="white-space: pre-line;">{{ $return->bank_info }}
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.order-returns.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Quay lại
                            </a>
                            @if ($return->status == 'pending')
                                <form action="{{ route('admin.order-returns.reject', $return->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i>Từ
                                        chối</button>
                                </form>
                                <form action="{{ route('admin.order-returns.approve', $return->id) }}" method="POST"
                                    class="d-inline ms-2" enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal fade" id="approveModal" tabindex="-1"
                                        aria-labelledby="approveModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="approveModalLabel">Duyệt hoàn đơn</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="refund_proof_image" class="form-label">Hình ảnh chứng từ
                                                            hoàn tiền <span class="text-danger">*</span></label>
                                                        <input type="file" class="form-control" id="refund_proof_image"
                                                            name="refund_proof_image" accept="image/*" required>
                                                        <div class="form-text">Vui lòng tải lên hình ảnh chứng từ chuyển
                                                            khoản/hoàn tiền</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="refund_note" class="form-label">Ghi chú hoàn
                                                            tiền</label>
                                                        <textarea class="form-control" id="refund_note" name="refund_note" rows="3"
                                                            placeholder="Nhập ghi chú về việc hoàn tiền (nếu có)"></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Tùy chọn cộng lại kho:</label>
                                                        <div class="mb-2">
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-primary me-2"
                                                                onclick="selectAllRestock()">
                                                                <i class="fas fa-check-square"></i> Chọn tất cả
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-outline-secondary"
                                                                onclick="deselectAllRestock()">
                                                                <i class="fas fa-square"></i> Bỏ chọn tất cả
                                                            </button>
                                                        </div>
                                                        <div class="border rounded p-3">
                                                            @foreach ($return->items as $item)
                                                                <div class="form-check mb-2">
                                                                    <input class="form-check-input restock-checkbox"
                                                                        type="checkbox"
                                                                        name="restock[{{ $item->id }}]"
                                                                        value="1" id="restock_{{ $item->id }}">
                                                                    <label class="form-check-label"
                                                                        for="restock_{{ $item->id }}">
                                                                        <strong>{{ $item->orderItem->product->name }}</strong>
                                                                        - SL: {{ $item->quantity }}
                                                                        @if ($item->orderItem->variant)
                                                                            <br><small class="text-muted">Variant:
                                                                                {{ $item->orderItem->variant->name }}</small>
                                                                        @endif
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle text-info"></i>
                                                            Chọn các sản phẩm cần cộng lại vào kho. Nếu không chọn, sản phẩm
                                                            sẽ không được cộng lại kho.
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-success"><i
                                                            class="fas fa-check me-1"></i>Xác nhận duyệt</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                        data-bs-target="#approveModal">
                                        <i class="fas fa-check me-1"></i>Duyệt
                                    </button>
                                </form>
                            @endif
                        </div>

                        @if ($return->status == 'approved' || $return->status == 'refunded')
                            <div class="mt-4">
                                <h5 class="mb-3"><i class="fas fa-receipt me-1"></i>Thông tin hoàn tiền</h5>
                                <div class="card bg-light">
                                    <div class="card-body">
                                        @if ($return->refund_proof_image)
                                            <div class="mb-3">
                                                <label class="fw-bold mb-2">Chứng từ hoàn tiền:</label>
                                                <img src="{{ asset($return->refund_proof_image) }}"
                                                    alt="Chứng từ hoàn tiền" class="img-fluid rounded"
                                                    style="max-height: 300px;">
                                            </div>
                                        @endif
                                        @if ($return->refund_note)
                                            <div>
                                                <label class="fw-bold mb-2">Ghi chú hoàn tiền:</label>
                                                <p class="mb-0">{{ $return->refund_note }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function selectAllRestock() {
            document.querySelectorAll('.restock-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAllRestock() {
            document.querySelectorAll('.restock-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>
@endsection
