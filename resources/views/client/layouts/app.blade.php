<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Untree.co">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <link rel="shortcut icon" href="/images/logo/iphone.png">

    <meta name="description" content="">
    <meta name="keywords" content="bootstrap, ecommerce, iphone">

    <title>@yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <!-- Tiny Slider -->
    <link href="{{ asset('css/tiny-slider.css') }}" rel="stylesheet">
    <!-- Custom Style -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <!-- Slick Slider CSS -->
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css" rel="stylesheet">
    <!-- Shop Card Styles (dùng chung hiệu ứng hover product-card) -->
    <link href="{{ asset('css/shop-custom.css') }}" rel="stylesheet">

    <!-- CSS riêng từ view -->
    @yield('styles')
</head>


<body>
    @include('client.partials.header')
    <!-- @include('client.partials.notification') -->
    <!-- @stack('scripts') -->
    @yield('content')
    @include('client.partials.chatbot')
    @include('client.partials.footer')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle + Popper -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Tiny Slider -->
    <script src="{{ asset('js/tiny-slider.js') }}"></script>
    <!-- Slick Slider JS -->
    <script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <!-- Load Pusher first -->
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <!-- Then load your custom scripts -->
    <script src="{{ asset('js/custom.js') }}"></script>

    <!-- Finally load Vite build -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        console.log('Layout loaded, checking Echo...');
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                console.log('Echo is available in layout');
            } else {
                console.error('Echo is not available in layout');
            }

            // Debug SweetAlert2
            console.log('Checking SweetAlert2...');
            if (typeof Swal !== 'undefined') {
                console.log('SweetAlert2 is loaded successfully');
            } else {
                console.error('SweetAlert2 is not loaded');
                return;
            }

            // Custom styling cho SweetAlert2 - sửa position để không bị header che
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal-toast-custom'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            // Thêm CSS để đảm bảo toast hiển thị trên header
            const style = document.createElement('style');
            style.textContent = `
                .swal-toast-custom {
                    z-index: 10000 !important;
                    margin-top: 5px !important;
                }
                .swal2-container {
                    z-index: 10000 !important;
                }
            `;
            document.head.appendChild(style);

            // Handle session flash messages
            @if (session('success'))
                console.log('Session success found:', '{{ session('success') }}');
                Toast.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: '{{ session('success') }}'
                });
            @endif

            @if (session('error'))
                console.log('Session error found:', '{{ session('error') }}');
                Toast.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: '{{ session('error') }}'
                });
            @endif

            @if (session('warning'))
                console.log('Session warning found:', '{{ session('warning') }}');
                Toast.fire({
                    icon: 'warning',
                    title: 'Cảnh báo',
                    text: '{{ session('warning') }}'
                });
            @endif

            @if (session('info'))
                console.log('Session info found:', '{{ session('info') }}');
                Toast.fire({
                    icon: 'info',
                    title: 'Thông tin',
                    text: '{{ session('info') }}'
                });
            @endif
        });
    </script>

    @yield('scripts')

    <script>
        const pageUrl = window.location.href;

        // Gửi khi vào trang
        fetch("/track/start", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    url: pageUrl
                })
            })
            .then(res => res.json())
            .then(data => {
                // Lưu id bản ghi lại để dùng khi thoát
                localStorage.setItem("page_view_id", data.id);
            });

        // Gửi khi rời trang
        window.addEventListener("pagehide", function() {
            const id = localStorage.getItem("page_view_id");
            if (!id) return;

            fetch("/track/stop", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    id: id
                })
            });

            localStorage.removeItem("page_view_id");
        });
    </script>

    <!-- AI Semantic Search Widget -->
    <script 
      src="https://a20-app-162.vercel.app/widget.js" 
      data-widget-key="sk-live-76a71f7d2d4007bbb4159216cbc10d970f6a56857b838ca4"
      data-api-url="https://a20-app-162-production.up.railway.app"
      defer
      crossorigin
    ></script>
</body>

</html>
