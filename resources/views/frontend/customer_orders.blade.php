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

    $title = "My Orders - ".$business_name;

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
                            <h3>Orders</h3>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive">
                                        <table class="table table-striped" id="ordersTable">
                                            <thead>
                                                <tr>
                                                    <th>Sr.</th>
                                                    <th>Customer</th>
                                                    <th>Order Status</th>
                                                    <th>Email</th>
                                                    <th>Total Amount</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(count($orders) > 0)
                                                    @foreach ($orders as $order)
                                                        <tr>
                                                            <td>{{ $loop->iteration }}</td>
                                                            <td>{{ $order->firstname }} {{ $order->lastname }}</td>
                                                            <td>
                                                                @if($order->order_status == 'completed')
                                                                    <span class="badge bg-success">Completed</span>
                                                                @elseif ($order->order_status == 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @elseif ($order->order_status == 'accepted')
                                                                    <span class="badge bg-primary">Accepted</span>
                                                                @elseif ($order->order_status == 'rejected')
                                                                    <span class="badge bg-danger">Rejected</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $order->email }}</td>
                                                            <td>{{ $order->order_total_text }}</td>
                                                            <td>
                                                                <a href="{{ route('customer.orders.details',encrypt($order->id)) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
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

    $('#ordersTable').DataTable({
        // 'sorting' : false,
    });

</script>

@endsection
