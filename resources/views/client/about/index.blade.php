@extends('client.layouts.app')
@section('title', 'Giới thiệu - Apple Store')

@section('content')
    <!-- Shop Banner -->
    <div class="shop-banner">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h1 data-aos="fade-up">Giới thiệu</h1>
                    <!-- Breadcrumbs -->
                    <div class="breadcrumbs" data-aos="fade-up" data-aos-delay="200">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Start Team Section -->
    <div class="untree_co-section">
        <div class="container">

            <div class="row mb-5">
                <div class="col-lg-5 mx-auto text-center">
                    <h2 class="section-title">Các thành viên</h2>
                </div>
            </div>

            <div class="team-slider">
                <!-- Team Member 1 -->
                <div class="team-member">
                    <img src="{{ asset('uploads/avatar_member/banh.JPG') }}" class="img-fluid mb-3">
                    <h3><a href="#"><span>Nguyễn Ngọc</span> Bảo Anh</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

                <!-- Team Member 2 -->
                <div class="team-member">
                    <img src="https://sbcf.fr/wp-content/uploads/2018/03/sbcf-default-avatar.png" class="img-fluid mb-3">
                    <h3><a href="#"><span>Chu Văn</span> Đại</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

                <!-- Team Member 3 -->
                <div class="team-member">
                    <img src="https://sbcf.fr/wp-content/uploads/2018/03/sbcf-default-avatar.png" class="img-fluid mb-3">
                    <h3><a href="#"><span>Nguyễn Mạnh</span> Cường</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

                <!-- Team Member 4 -->
                <div class="team-member">
                    <img src="{{ asset('uploads/avatar_member/binh.jpg') }}" class="img-fluid mb-3">
                    <h3><a href="#"><span>Nguyễn Thanh</span> Bình</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

                <!-- Team Member 5 -->
                <div class="team-member">
                    <img src="https://sbcf.fr/wp-content/uploads/2018/03/sbcf-default-avatar.png" class="img-fluid mb-3">
                    <h3><a href="#"><span>Nguyễn Văn</span> Quảng</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

                <!-- Team Member 6 -->
                <div class="team-member">
                    <img src="{{ asset('uploads/avatar_member/khai.jpg') }}" class="img-fluid mb-3">
                    <h3><a href="#"><span>Nguyễn Văn</span> Khải</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

                <!-- Team Member 7 -->
                <div class="team-member">
                    <img src="{{ asset('uploads/avatar_member/z6795420761011_54509d5aa1987147982f0627793c6f11.jpg') }}"
                        class="img-fluid mb-3">
                    <h3><a href="#"><span>Kim Hồng</span> Phong</a></h3>
                    <span class="d-block position mb-3">Lập trình web</span>
                    <p>Cao đẳng FPT Polytechnic Hà Nội</p>
                </div>

            </div>

        </div>
    </div>
    <!-- End Team Section -->
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Slick JS -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

    <!-- Slick Initialization -->
    <script>
        $(document).ready(function() {
            $('.team-slider').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                dots: true,
                arrows: true,
                responsive: [{
                        breakpoint: 992,
                        settings: {
                            slidesToShow: 2
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1
                        }
                    }
                ]
            });
        });
    </script>
    <style>
        .team-member img {
            width: 300px;
            height: 300px;
            object-fit: cover;
            overflow: hidden;
        }

        .team-member h3 a {
            color: #333 !important;
            text-decoration: none;
        }
    </style>

@endsection
