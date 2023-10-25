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
    $business_name = (isset($client_settings['business_name'])) ? $client_settings['business_name'] : 'Mahantam Laser Crafts';

    // Current Route Name
    $routeName = Route::currentRouteName();

    $title = "Edit Profile - ".$business_name;

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', $title)

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
                                <a href="{{ route('customer.orders') }}" class="{{ (($routeName == 'customer.orders') || ($routeName == 'customer.orders.details')) ? 'active' : '' }}">
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
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('customer.profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="user_id" id="user_id" value="{{ $user_details['id'] }}">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="firstname" class="form-label">Firstname <span class="text-danger">*</span></label>
                                        <input type="text" name="firstname" id="firstname" value="{{ $user_details['firstname'] }}" class="form-control {{ ($errors->has('firstname')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('firstname'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('firstname') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="lastname" class="form-label">Lastname <span class="text-danger">*</span></label>
                                        <input type="text" name="lastname" id="lastname" value="{{ $user_details['lastname'] }}" class="form-control {{ ($errors->has('lastname')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('lastname'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('lastname') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                        <input type="text" name="email" id="email" value="{{ $user_details['email'] }}" class="form-control {{ ($errors->has('email')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('email'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('email') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone No. <span class="text-danger">*</span></label>
                                        <input type="text" maxlength="10" name="phone" id="phone" value="{{ $user_details['mobile'] }}" class="form-control {{ ($errors->has('phone')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('phone'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('phone') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" name="password" id="password" class="form-control {{ ($errors->has('password')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('password') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="password" name="confirm_password" id="confirm_password" class="form-control {{ ($errors->has('confirm_password')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('confirm_password'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('confirm_password') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="profile_picture" class="form-label">Profile Picture</label>
                                        <input type="file" name="profile_picture" id="profile_picture" class="form-control {{ ($errors->has('profile_picture')) ? 'is-invalid' : '' }}">
                                        @if($errors->has('profile_picture'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('profile_picture') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        @if(!empty($user_details->image) && file_exists('public/admin_uploads/users/'.$user_details->image))
                                            <img src="{{ asset('public/admin_uploads/users/'.$user_details->image) }}" width="80px" height="80px" style="border-radius: 50%">
                                        @else
                                            <img src="{{ asset('public/admin_images/demo_images/profiles/profile1.jpg') }}" width="80px" height="80px" style="border-radius: 50%">
                                        @endif
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <button class="btn btn-success">Update</button>
                                    </div>
                                </div>
                            </form>
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
