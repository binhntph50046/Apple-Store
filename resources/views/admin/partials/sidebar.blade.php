<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="{{ route('admin.dashboard') }}" class="b-brand text-primary">
                <!-- ========   Change your logo from here   ============ -->
                <img src="{{ asset('/images/logo/Apple_Store.png') }}" class="img-fluid logo-lg" alt="logo">
            </a>
        </div>
        <div class="navbar-content">
            <ul class="pc-navbar">
                <li class="pc-item">
                    <a href="{{ route('admin.dashboard') }}" class="pc-link">
                        <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
                        <span class="pc-mtext">Thống kê</span>
                    </a>
                </li>

                <!-- ADMIN SECTION -->
                <li class="pc-item pc-caption">
                    <label>Quản trị hệ thống</label>
                    <i class="ti ti-settings"></i>
                </li>

                <!-- Quản lý sản phẩm -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-package"></i></span>
                        <span class="pc-mtext">Quản lý sản phẩm</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.products.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-box"></i></span>
                                <span class="pc-mtext">Sản phẩm</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.attributes.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-list"></i></span>
                                <span class="pc-mtext">Thuộc tính</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.specifications.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-settings"></i></span>
                                <span class="pc-mtext">Thông số kỹ thuật</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.variants.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-box-multiple"></i></span>
                                <span class="pc-mtext">Biến thể sản phẩm</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý danh mục bài viết + sản phẩm -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-list"></i></span>
                        <span class="pc-mtext">Quản lý danh mục</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.categories.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-layout-grid"></i></span>
                                <span class="pc-mtext">Danh mục</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý đơn hàng -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-shopping-cart"></i></span>
                        <span class="pc-mtext">Quản lý đơn hàng</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.orders.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-shopping-cart"></i></span>
                                <span class="pc-mtext">Đơn hàng</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.invoices.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-file-invoice"></i></span>
                                <span class="pc-mtext">Hóa đơn</span>
                            </a>
                        </li>

                        <li class="pc-item">
                            <a href="{{ route('admin.order-returns.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-file-invoice"></i></span>
                                <span class="pc-mtext">Yêu cầu hoàn hàng</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý giảm giá -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-gift"></i></span>
                        <span class="pc-mtext">Quản lý ưu đãi</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.vouchers.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-ticket"></i></span>
                                <span class="pc-mtext">Mã giảm giá</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.flash-sales.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-bolt"></i></span>
                                <span class="pc-mtext">Khuyến mãi</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý nội dung -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-news"></i></span>
                        <span class="pc-mtext">Quản lý nội dung</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.notify.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-bell"></i></span>
                                <span class="pc-mtext">Thông báo</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.livechat.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-message"></i></span>
                                <span class="pc-mtext">Tin nhắn</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.blogs.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-news"></i></span>
                                <span class="pc-mtext">Bài viết</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.banners.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-photo"></i></span>
                                <span class="pc-mtext">Banner</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.faqs.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-help"></i></span>
                                <span class="pc-mtext">FAQ</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.reviews.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-star"></i></span>
                                <span class="pc-mtext">Đánh giá sản phẩm</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý người dùng -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-users"></i></span>
                        <span class="pc-mtext">Quản lý người dùng</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.users.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-user"></i></span>
                                <span class="pc-mtext">Người dùng</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.contacts.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-mail"></i></span>
                                <span class="pc-mtext">Liên hệ</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.subscribers.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-mail-opened"></i></span>
                                <span class="pc-mtext">Email đăng ký</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.activities.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-clock"></i></span>
                                <span class="pc-mtext">Hoạt động</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- CLIENT SECTION -->
                <li class="pc-item pc-caption">
                    <label>Quản lý giao diện</label>
                    <i class="ti ti-devices"></i>
                </li>

                <!-- Giao diện -->
                {{-- <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-palette"></i></span>
                        <span class="pc-mtext">Giao diện</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="elements/bc_typography.html" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-text-size"></i></span>
                                <span class="pc-mtext">Typography</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="elements/bc_color.html" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-palette"></i></span>
                                <span class="pc-mtext">Màu sắc</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="elements/icon-tabler.html" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-icons"></i></span>
                                <span class="pc-mtext">Icons</span>
                            </a>
                        </li>
                    </ul>
                </li> --}}

                <!-- Quản lý SEO -->
                <li class="pc-item pc-hasmenu" data-pc-parent="multiple">
                    <a class="pc-link">
                        <span class="pc-micon"><i class="ti ti-search"></i></span>
                        <span class="pc-mtext">Quản lý SEO</span>
                        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
                    </a>
                    <ul class="pc-submenu">
                        <li class="pc-item">
                            <a href="{{ route('admin.sitemap.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-sitemap"></i></span>
                                <span class="pc-mtext">Sitemap</span>
                            </a>
                        </li>
                        <li class="pc-item">
                            <a href="{{ route('admin.robots.index') }}" class="pc-link">
                                <span class="pc-micon"><i class="ti ti-file-text"></i></span>
                                <span class="pc-mtext">Robots.txt</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
            {{-- <div class="card text-center">
                <div class="card-body">
                    <img src="assets/images/img-navbar-card.png" alt="images" class="img-fluid mb-2">
                    <h5>Upgrade To Pro</h5>
                    <p>To get more features and components</p>
                    <a href="https://codedthemes.com/item/berry-bootstrap-5-admin-template/" target="_blank"
                        class="btn btn-success">Buy Now</a>
                </div>
            </div> --}}
        </div>
    </div>
</nav>
