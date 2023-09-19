<!-- bootstrap css -->
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/bootstrap.min.css')}}">

<!-- custom css -->
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/custom.css')}}">


<!-- swipper css -->
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/swiper-bundle.min.css')}}">

{{-- Toastr --}}
<link rel="stylesheet" href="{{ asset('public/admin/assets/vendor/css/toastr.min.css') }}">


<!-- font awesome -->
{{-- <link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/font-awsome.min.css')}}"> --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css" />

<link href="{{ asset('public/admin/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">



<!-- font-family -->
<link rel="stylesheet" type="text/css" href="{{asset('public/frontend/css/font-family.css')}}">

<style>
    /* ============ desktop view ============ */
    @media all and (min-width: 992px) {
        .header .dropdown-menu li {
            position: relative;
        }

        .header .nav-item .submenu {
            display: none;
            position: absolute;
            left: 100%;
            top: -7px;
        }

        .header .nav-item .submenu-left {
            right: 100%;
            left: auto;
        }

        .header .dropdown-menu>li:hover {
            background-color: #f1f1f1
        }

        .header .dropdown-menu>li:hover>.submenu {
            display: block;
        }
    }

    /* ============ desktop view .end// ============ */

    /* ============ small devices ============ */
    @media (max-width: 991px) {
        .header .dropdown-menu .dropdown-menu {
            margin-left: 0.7rem;
            margin-right: 0.7rem;
            margin-bottom: .5rem;
        }
    }


    .check-in-form {
        padding: 20px 30px;
        border-radius: 10px;
        box-shadow: 6px 6px 6px #cbced1, -6px -6px 6px #fff;
    }

    .check-in-page .form-control {
        padding: 10px 18px;
        box-shadow: inset 5px 5px 5px #cbced1, inset -5px -5px 5px #fff;
        background-color: #ffffffc7;
    }

    .check-in-page button {
        padding: 10px 22px;
        font-size: 15px;
        box-shadow: 0px 2px 5px rgb(0 0 0 / 30%);
        font-weight: 700;
    }

    .check-in-page textarea {
        height: calc(100% - 24px);
    }

</style>
