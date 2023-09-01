 <!-- bootstrap css -->
 <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/bootstrap.min.css')}}">

 <!-- custom css -->
 <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/custom.css')}}">


 <!-- swipper css -->
 <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/swiper-bundle.min.css')}}">



 <!-- font awesome -->
 {{-- <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/font-awsome.min.css')}}"> --}}
 <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" />



 <!-- font-family -->
 <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/font-family.css')}}">

 <style>
    /* ============ desktop view ============ */
    @media all and (min-width: 992px) {
        .header .dropdown-menu li{ position: relative; 	}
        .header .nav-item .submenu{
            display: none;
            position: absolute;
            left:100%; top:-7px;
        }
        .header .nav-item .submenu-left{
            right:100%; left:auto;
        }
        .header .dropdown-menu > li:hover{ background-color: #f1f1f1 }
        .header .dropdown-menu > li:hover > .submenu{ display: block; }
    }
    /* ============ desktop view .end// ============ */

    /* ============ small devices ============ */
    @media (max-width: 991px) {
    .header .dropdown-menu .dropdown-menu{
        margin-left:0.7rem; margin-right:0.7rem; margin-bottom: .5rem;
    }
    }
 </style>
