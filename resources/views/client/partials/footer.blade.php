 <style>
     .shadow-effect {
         filter: drop-shadow(-19px 21px 15px rgba(0, 0, 0, 0.4));
         /* Các thông số trên lần lượt là: offsetX, offsetY, blur-radius, màu */
     }

     .footer-info-bar {
         background: #111827;
     }

     .untree_co-section a{
        color: #ffffff !important;
     }
 </style>

 <div class="footer-info-bar py-4">
     <div class="container">
         <div class="row text-center">
             <div class="col-12 col-md-3 mb-3 mb-md-0">
                 <img alt="Frame 2085663203 (2).png" loading="lazy" width="298" height="60" decoding="async"
                     data-nimg="1" class="mb:hidden" style="color: transparent;"
                     srcset="https://cdn2.fptshop.com.vn/unsafe/360x0/filters:quality(100)/small/Frame_2085663203_2_3941f3c68d.png 1x, https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663203_2_3941f3c68d.png 2x"
                     src="https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663203_2_3941f3c68d.png">
             </div>
             <div class="col-12 col-md-3 mb-3 mb-md-0">
                 <img alt="Frame 2085663204 (2).png" loading="lazy" width="298" height="60" decoding="async"
                     data-nimg="1" class="mb:hidden" style="color: transparent;"
                     srcset="https://cdn2.fptshop.com.vn/unsafe/360x0/filters:quality(100)/small/Frame_2085663204_2_7da5637bdc.png 1x, https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663204_2_7da5637bdc.png 2x"
                     src="https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663204_2_7da5637bdc.png">
             </div>
             <div class="col-12 col-md-3 mb-3 mb-md-0">
                 <img alt="Frame 2085663205 (3).png" loading="lazy" width="298" height="60" decoding="async"
                     data-nimg="1" class="mb:hidden" style="color: transparent;"
                     srcset="https://cdn2.fptshop.com.vn/unsafe/360x0/filters:quality(100)/small/Frame_2085663205_3_a74cb8453a.png 1x, https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663205_3_a74cb8453a.png 2x"
                     src="https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663205_3_a74cb8453a.png">
             </div>
             <div class="col-12 col-md-3">
                 <img alt="Frame 2085663206 (2).png" loading="lazy" width="298" height="60" decoding="async"
                     data-nimg="1" class="mb:hidden" style="color: transparent;"
                     srcset="https://cdn2.fptshop.com.vn/unsafe/360x0/filters:quality(100)/small/Frame_2085663206_2_c845c5a7ee.png 1x, https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663206_2_c845c5a7ee.png 2x"
                     src="https://cdn2.fptshop.com.vn/unsafe/640x0/filters:quality(100)/small/Frame_2085663206_2_c845c5a7ee.png">
             </div>
         </div>
     </div>
 </div>

 <!-- Start Footer Section -->
 <footer class="footer-section">
     <div class="container relative">

         <div class="sofa-img">
             <img src="{{ asset('images/iphone-shadow4.png') }}" alt="Image" style="width: 100%;margin-top: 146px;"
                 class="img-fluid shadow-effect">
         </div>

         <div class="row">
             <div class="col-lg-8">
                 <div class="subscription-form">
                     <h3 class="d-flex align-items-center"><span class="me-1"><img
                                 src="{{ asset('images/envelope-outline.svg') }}" alt="Image"
                                 class="img-fluid"></span><span>Đăng ký nhận thông tin mới nhất</span></h3>

                     <form action="{{ route('subscribe.store') }}" method="POST" class="row g-3">
                         @csrf
                         <div class="col-4">
                             <input type="text" class="form-control" name="name" placeholder="Họ và tên của bạn">
                         </div>
                         <div class="col-4">
                             <input type="email" class="form-control" name="email" placeholder="Email của bạn">
                         </div>
                         <div class="col-auto">
                             <button class="btn btn-primary">
                                 <span class="fa fa-paper-plane"></span>
                             </button>
                         </div>
                     </form>

                 </div>
             </div>
         </div>

         <div class="row g-5 mb-5">
             <div class="col-lg-4">
                 <div class="mb-4 footer-logo-wrap"><a href="#" class="footer-logo">Apple Store<span>.</span></a>
                 </div>
                 <p class="mb-4" style="color: #ffffff">Chuyên cung cấp các sản phẩm Apple chính hãng với chất lượng
                     cao cấp. Cam kết mang
                     đến trải nghiệm tốt nhất cho khách hàng với dịch vụ tận tâm và chuyên nghiệp.</p>

                 <div class="custom-social d-flex align-items-center">
                     <img src="https://cdn2.fptshop.com.vn/svg/facebook_icon_a64b579fe2.svg?w=32&q=100" alt="Facebook">
                     <img src="https://cdn2.fptshop.com.vn/svg/zalo_icon_8cbef61812.svg?w=32&q=100" alt="Zalo">
                     <img src="https://cdn2.fptshop.com.vn/svg/youtube_icon_b492d61ba5.svg?w=32&q=100" alt="Youtube">
                     <img src="https://cdn2.fptshop.com.vn/svg/tiktok_icon_faabbeeb61.svg?w=32&q=100" alt="Tiktok">
                 </div>
             </div>

             <div class="col-lg-8">
                 <div class="row">
                     <!-- Cột 1 -->
                     <div class="col-12 col-sm-6 col-md-4 mb-3">
                         <ul class="list-unstyled">
                             <li><a href="{{ route('about') }}">Về chúng tôi</a></li>
                             <li><a href="{{ route('shop') }}">Sản phẩm</a></li>
                             <li><a href="{{ route('blog') }}">Bài viết</a></li>
                             <li><a href="{{ route('contact') }}">Liên hệ</a></li>
                         </ul>
                     </div>

                     <!-- Cột 2 -->
                     <div class="col-12 col-sm-6 col-md-4 mb-3">
                         <ul class="list-unstyled">
                             <li><a href="/shop/iphone">iPhone</a></li>
                             <li><a href="/shop/ipad">iPad</a></li>
                             <li><a href="/shop/mac">MacBook</a></li>
                             <li><a href="/shop/watch">Apple Watch</a></li>
                         </ul>
                     </div>

                     <!-- Cột 3 -->
                     <div class="col-12 col-sm-6 col-md-4 mb-3">
                         <ul class="list-unstyled">
                             <li><a href="/shop/tai-nghe-loa">AirPods</a></li>
                             <li><a href="/shop/phu-kien">Phụ kiện</a></li>
                         </ul>
                     </div>
                 </div>
             </div>


         </div>

         <div class="border-top copyright">
             <div class="row pt-4">
                 <div class="col-lg-6">
                     <p class="mb-2 text-center text-lg-start text-white">© 2025 Apple Store. Thiết kế bởi
                         <a href="https://www.facebook.com/nguyenbaoanhhh"><strong style="color: #ffffff">Apple Store</strong></a>
                     </p>
                 </div>
                 {{-- <div class="col-lg-6">
                     <p class="mb-2 text-center text-lg-end">
                         <a href="#">Điều khoản sử dụng</a> |
                         <a href="#">Chính sách bảo mật</a>
                     </p>
                 </div> --}}
             </div>
         </div>
     </div>
 </footer>
 <!-- End Footer Section -->
