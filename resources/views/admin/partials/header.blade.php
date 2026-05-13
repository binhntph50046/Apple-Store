<style>
    #notif-badge {
        right: -15px;
        font-size: 10px;
        padding: 3px;
        width: 20px;
    }

    .pc-header .pc-head-link {
        margin: 0 4px;
        position: relative;
        font-weight: 500;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        border-radius: 4px;
        color: var(--pc-header-color);
        overflow: hidden;
    }
</style>
<header class="pc-header">
    <div class="header-wrapper"> <!-- [Mobile Media Block] start -->
        <div class="me-auto pc-mob-drp">
            <ul class="list-unstyled">
                <!-- ======= Menu collapse Icon ===== -->
                <li class="pc-h-item pc-sidebar-collapse">
                    <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>
                <li class="pc-h-item pc-sidebar-popup">
                    <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
                        <i class="ti ti-menu-2"></i>
                    </a>
                </li>

            </ul>
        </div>
        <!-- [Mobile Media Block end] -->
        <div class="ms-auto">
            <ul class="list-unstyled">
                <li class="dropdown pc-h-item">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="position-relative d-inline-block">
                            <i class="ti ti-bell" style="font-size: 23px;"></i>
                            <span id="notif-badge"
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ Auth::user()->unreadNotifications->count() == 0 ? 'd-none' : '' }}">
                                {{ Auth::user()->unreadNotifications->count() }}
                            </span>
                        </span>
                    </a>

                    <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header d-flex align-items-center justify-content-between">
                            <h5 class="m-0">Thống báo</h5>
                            <div class="text-end px-3 pb-2">
                                <button class="btn btn-sm btn-link" id="mark-all-read">Đánh dấu tất cả đã đọc</button>
                            </div>
                        </div>


                        <div class="dropdown-divider"></div>

                        <div class="dropdown-header px-0 text-wrap header-notification-scroll position-relative"
                            style="max-height: calc(100vh - 215px)">
                            <div class="list-group list-group-flush w-100" id="notif-list">
                                {{-- Load từ DB --}}
                                @foreach(Auth::user()->notifications->take(5) as $notification)
                                    @php
                                        $isUnread = is_null($notification->read_at);
                                    @endphp
                                    <a href="{{ $notification->data['url'] ?? '#' }}"
                                        class="list-group-item list-group-item-action notif-item"
                                        data-id="{{ $notification->id }}">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                @php
                                                    $isUnread = is_null($notification->read_at);
                                                    $type = $notification->data['type'] ?? 'default';

                                                    $typeColor = match ($type) {
                                                        'user_created' => 'text-success',
                                                        'order_created' => 'text-info',
                                                        'return_created' => 'text-warning',
                                                        'contact_submitted' => 'text-warning',
                                                        'message_created' => 'text-info',
                                                        'error' => 'text-danger',
                                                        default => 'text-muted',
                                                    };
                                                @endphp
                                                <b
                                                    class="{{ $typeColor }}">{{ $notification->data['title'] ?? 'Thông báo' }}</b><br>
                                                <small>{{ $notification->data['user_name']}} {{ $notification->data['message'] }}</small>
                                                <small
                                                    class="text-muted d-block mt-1">{{ $notification->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div>
                                                @if($isUnread)
                                                    <span class="badge bg-success">Mới</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach

                            </div>
                        </div>

                        <div class="dropdown-divider"></div>
                        <div class="text-center py-2">
                            <a href="{{ route('admin.notify.index') }}" class="link-primary">Xem tất cả</a>
                        </div>
                    </div>
                </li>

                <li class="dropdown pc-h-item header-user-profile">
                    <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
                        @if (Auth::user()->avatar)
                            <img src="{{ asset(Auth::user()->avatar) }}" alt="user-image" class="user-avtar">
                        @else
                            <img src="/assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar">
                        @endif
                        <span>{{ Auth::user()->name }}</span>
                    </a>
                    <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex mb-1">
                                <div class="flex-shrink-0">
                                    @if (Auth::user()->avatar)
                                        <img src="{{ asset(Auth::user()->avatar) }}" alt="user-image" class="user-avtar">
                                    @else
                                        <img src="/assets/images/user/avatar-2.jpg" alt="user-image" class="user-avtar">
                                    @endif
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                                    <span>{{ Auth::user()->email }}</span>
                                </div>
                            </div>
                        </div>
                        <hr style="border: 1px solid #a8a8a8; margin: 0 0 7px 0;">
                        {{-- <ul class="nav drp-tabs nav-fill nav-tabs" id="mydrpTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="drp-t1" data-bs-toggle="tab"
                                    data-bs-target="#drp-tab-1" type="button" role="tab" aria-controls="drp-tab-1"
                                    aria-selected="true"><i class="ti ti-user"></i>
                                    Hồ sơ</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="drp-t2" data-bs-toggle="tab" data-bs-target="#drp-tab-2"
                                    type="button" role="tab" aria-controls="drp-tab-2" aria-selected="false"><i
                                        class="ti ti-settings"></i>
                                    Setting</button>
                            </li>
                        </ul> --}}
                        <div class="tab-content" id="mysrpTabContent">
                            <div class="tab-pane fade show active" id="drp-tab-1" role="tabpanel"
                                aria-labelledby="drp-t1" tabindex="0">
                                <a href="{{ route('admin.profile.index') }}" class="dropdown-item">
                                    <i class="ti ti-user"></i>
                                    <span>Xem thông tin</span>
                                </a>
                                <a href="{{ route('admin.profile.password') }}" class="dropdown-item">
                                    <i class="ti ti-lock"></i>
                                    <span>Đổi mật khẩu</span>
                                </a>
                                <a href="{{ route('home') }}" class="dropdown-item">
                                    <i class="ti ti-home"></i>
                                    <span>Quay lại trang chủ</span>
                                </a>
                                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item w-100 text-start bg-transparent border-0">
                                        <i class="ti ti-power"></i>
                                        <span>Đăng xuất</span>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
@include('admin.partials.notify')
