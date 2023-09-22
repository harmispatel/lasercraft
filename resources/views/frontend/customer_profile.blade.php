@php

    // Language Settings
    $language_settings = clientLanguageSettings();
    $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

    // Language Details
    $language_detail = App\Models\Languages::where('id',$primary_lang_id)->first();
    $lang_code = isset($language_detail->code) ? $language_detail->code : '';

    // Order Settings
    $order_settings = getOrderSettings();

    $description_key = $lang_code."_description";
    $image_key = $lang_code."_image";
    $name_key = $lang_code."_name";
    $title_key = $lang_code."_title";

    $client_settings = getClientSettings();
    $default_currency = (isset($client_settings['default_currency'])) ? $client_settings['default_currency'] : 'USD';

    // Current Route Name
    $routeName = Route::currentRouteName();

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('View Cart'))

@section('content')

<section class="profile_main sec_main">
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-lg-3">
                <div class="profile_sidebar">
                    <div class="profile_info_box">
                        @if(!empty(Auth::user()->image) && file_exists('public/admin_uploads/users/'.Auth::user()->image))
                            <img src="{{ asset('public/admin_uploads/users/'.Auth::user()->image) }}" width="65px" height="65px" style="border-radius: 50%">
                        @else
                            <img src="{{ asset('public/admin_images/demo_images/profiles/profile1.jpg') }}" width="65px" height="65px" style="border-radius: 50%">
                        @endif
                        <h3>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h3>
                    </div>
                    <div class="sidebar_menu">
                        <ul>
                            <li>
                                <a href="{{ route('customer.profile') }}" class="{{ (($routeName == 'customer.profile') || ($routeName == 'customer.profile.edit')) ? 'active' : '' }}">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Profile</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    <span>Orders</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" class="">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <span>Logout</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-lg-9">
                <div class="profile_info_main">
                    <div class="card position-relative">
                        <a href="{{ route('customer.profile.edit',encrypt(Auth::user()->id)) }}" class="btn btn-sm btn-primary edit-profile-btn"><i class="bi bi-pencil"></i></a>
                        <div class="card-body pt-3">
                            <h5 class="card-title">Profile Details</h5>
                            <div class="row mb-2">
                                <div class="col-lg-3 col-md-4 label"><b>First Name</b></div>
                                <div class="col-lg-9 col-md-8">{{ Auth::user()->firstname }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3 col-md-4 label"><b>Last Name</b></div>
                                <div class="col-lg-9 col-md-8">{{ Auth::user()->lastname }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3 col-md-4 label"><b>Email</b></div>
                                <div class="col-lg-9 col-md-8">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3 col-md-4 label"><b>Phone No.</b></div>
                                <div class="col-lg-9 col-md-8">{{ (Auth::user()->mobile) ? Auth::user()->mobile : '-' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-lg-3 col-md-4 label"><b>Joined At</b></div>
                                <div class="col-lg-9 col-md-8">{{ date('d-m-Y h:i:s',strtotime(Auth::user()->created_at)) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('page-js')

<script type="text/javascript">

    // Error Messages
    @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}')
    @endif

    // Success Messages
    @if (Session::has('success'))
        toastr.success('{{ Session::get('success') }}')
    @endif

</script>

@endsection
