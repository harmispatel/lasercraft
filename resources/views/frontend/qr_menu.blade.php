<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qr Menu</title>

    <link href="{{ asset('public/admin_images/favicons/smartqrscan.ico') }}" rel="icon">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,400;0,500;0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/vendor/css/toastr.min.css') }}">
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/css/swiper.min.css" rel="stylesheet">
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" rel="stylesheet">-->
    <link rel="stylesheet" href="{{ asset('public/frontend/css/frontend.css') }}" >
</head>
<body>

    <!-- Header -->
    @include('frontend.frontend-header')

    <section class="qr_menu_banner banner_title ">
        <h2>QR Menu</h2>
    </section>

    <section class="menu_info sec_main">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 col-lg-5 offset-lg-1">
                    <div class="menu_detail">
                        <h3>The digital menu for food/beverage businesses unlocks new possibilities for modern, fast and quality service for your customers.</h3>

                        <a href="#" class="btn view_btn">View Demo</a>

                         <p>Suitable for: Cafés – Bars – Bistros – Restaurants – Beach bars- Playgrounds</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="menu_detail_img">
                        <img src="{{ asset('public/frontend/image/menu_info.JPG') }}" class="w-100" alt="">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="menu_point_info sec_main">
        <div class="title_text">
            <h2>Featured</h2>
        </div>
        <div class="container">
            <div class="menu_point_inr">
                <div class="menu_point_box">
                    <i class="fa-solid fa-scroll  menu_box_icon"></i>
                    <p>Interactive and configurable digital menu</p>
                </div>
                <div class="menu_point_box">
                    <i class="fa-solid fa-layer-group menu_box_icon"></i>
                    <p>Includes categories where customers can see all products with or without photos, prices and detailed descriptions</p>
                </div>
                <div class="menu_point_box">
                    <i class="fa-solid fa-mobile menu_box_icon"></i>
                    <p>Contactless Order Service</p>
                </div>
                <div class="menu_point_box">
                    <i class="fa-solid fa-laptop menu_box_icon"></i>
                    <p>user-friendly Website</p>
                </div>
                <div class="menu_point_box">
                    <i class="fa-sharp fa-solid fa-pager menu_box_icon"></i>
                    <p>Display room service menu, with ordering capability</p>
                </div>
            </div>
        </div>
    </section>

    <section class="menu_manage_main sec_main">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <div class="menu_detail_img position-relative">
                        <img src="{{ asset('public/frontend/image/frontend.png') }}" class="w-100" alt="">
                        <div id="light">
                            <a class="boxclose" id="boxclose" >X</a>
                            <video id="video"  width="600" controls autoplay>
                                <source src="{{ asset('public/frontend/video/frontend_desk.mp4') }}" type="video/mp4">
                            </video>
                        </div>

                        <div id="fade"></div>
                        <div class="playbutton">
                            <a  id="watch">
                                <img src="{{ asset('public/frontend/image/playic.png') }}">
                            </a>
                        </div>

                    </div>
                </div>
                <div class="col-md-6 offset-md-1">
                    <div class="menu_manage_main_title">
                        <h3>Μenu management platform.</h3>
                        <p>our menu management platform is user-friendly. Customers can easily place their orders. They can pay with UPI or cash.</p>
                        <a href="https://smartqrscan.com/smartqrscandemo" target="_blank" class="btn view_btn">View Demo</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    @include('frontend.frontend-footer')

    <script src="{{ asset('public/client/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/admin/assets/vendor/js/toastr.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-1H7NBQJCND"></script>

    <script type="text/javascript">

        // Google tag
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-1H7NBQJCND');

        // Custom JavaScript
        $(document).ready(function() {
            "use strict";

            // only for the safari browser Mac
                document.getElementById('video').pause();

                $('#watch').click(function() {
                    var lightBoxVideoopen = document.getElementById("#video");
                    window.scrollTo(0, 0);
                    document.getElementById('light').style.display = 'block';
                    document.getElementById('fade').style.display = 'block';
                    document.getElementById('video').play();
                });

                $('#boxclose').click(function(){
                    var lightBoxVideoclose = document.getElementById("#video");
                    document.getElementById('light').style.display = 'none';
                    document.getElementById('fade').style.display = 'none';
                    document.getElementById('video').pause();
                    $('#video').get(0).currentTime = 0;
                })
        });

        // Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            timeOut: 4000
        }

        // Error Messages
        @if (Session::has('error'))
            toastr.error('{{ Session::get('error') }}')
        @endif

        // Success Message
        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>
</body>
</html>
