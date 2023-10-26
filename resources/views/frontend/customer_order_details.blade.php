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

    $title = "Order Details - ".$business_name;

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', $title)

@section('content')

<section class="profile_main sec_main">
    <div class="container">
        <div class="row">

            @if(Auth::user() && Auth::user()->user_type == 3)
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
            @endif

            <div class="col-md-{{ (Auth::user() && Auth::user()->user_type == 3) ? '8' : '12' }} col-lg-{{ (Auth::user() && Auth::user()->user_type == 3) ? '9' : '12' }}">
                <div class="profile_info_main">
                    <div class="card">
                        <div class="card-body order-details">
                            <h3>Order Details</h3>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                                <tbody class="fw-semibold text-gray-600">
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-calendar-date"></i>&nbsp; Order Date
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ date('d-m-Y',strtotime($order_details->created_at)) }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-credit-card"></i>&nbsp; Payment Method
                                                                </div>
                                                                <div class="fw-bold text-capitalize">
                                                                    {{ $order_details->payment_method }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-truck"></i>&nbsp; Shipping Method
                                                                </div>
                                                                <div class="fw-bold text-capitalize">
                                                                    {{ ($order_details->checkout_type == 'takeaway') ? 'PickUp' : 'Ship' }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-truck"></i>&nbsp; Order Status
                                                                </div>
                                                                <div class="fw-bold text-capitalize">
                                                                    @if($order_details->order_status == 'completed')
                                                                        <span class="badge bg-success">Completed</span>
                                                                    @elseif ($order_details->order_status == 'pending')
                                                                        <span class="badge bg-warning">Pending</span>
                                                                    @elseif ($order_details->order_status == 'accepted')
                                                                        <span class="badge bg-primary">Accepted</span>
                                                                    @elseif ($order_details->order_status == 'rejected')
                                                                        <span class="badge bg-danger">Rejected</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                                <tbody class="fw-semibold text-gray-600">
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-person-circle"></i>&nbsp; Customer
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order_details->firstname }} {{ $order_details->lastname }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-envelope"></i>&nbsp; Email
                                                                </div>
                                                                <div class="fw-bold text-break">
                                                                    {{ $order_details->email }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-telephone"></i>&nbsp; Mobile No.
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order_details->phone }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-2">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                                <tbody class="fw-semibold text-gray-600">
                                                    @if($order_details->checkout_type == 'delivery')
                                                        <tr>
                                                            <td class="text-muted">
                                                                <div class="client-order-info">
                                                                    <div class="">
                                                                        <i class="bi bi-map"></i>&nbsp; Address
                                                                    </div>
                                                                    <div class="fw-bold">
                                                                        {{ $order_details->address }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">
                                                                <div class="client-order-info">
                                                                    <div class="">
                                                                        <i class="bi bi-building"></i>&nbsp; Street
                                                                    </div>
                                                                    <div class="fw-bold">
                                                                        {{ $order_details->street_number }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">
                                                                <div class="client-order-info">
                                                                    <div class="">
                                                                        <i class="bi bi-building"></i>&nbsp; City
                                                                    </div>
                                                                    <div class="fw-bold">
                                                                        {{ $order_details->city }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">
                                                                <div class="client-order-info">
                                                                    <div class="">
                                                                        <i class="bi bi-building"></i>&nbsp; State
                                                                    </div>
                                                                    <div class="fw-bold">
                                                                        {{ $order_details->state }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td class="text-muted">
                                                                <div class="client-order-info">
                                                                    <div class="">
                                                                        <i class="bi bi-building"></i>&nbsp; Postcode
                                                                    </div>
                                                                    <div class="fw-bold">
                                                                        {{ $order_details->postcode }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    @if($order_details->checkout_type == 'takeaway')
                                                        <tr>
                                                            <td class="text-muted">
                                                                <div class="client-order-info">
                                                                    <div class="w-50">
                                                                        <i class="bi bi-map"></i>&nbsp; PickUp Location
                                                                    </div>
                                                                    <div class="fw-bold ps-5">
                                                                        {{ $order_details->pickup_location }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endif

                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="w-50">
                                                                    <i class="bi bi-card-text"></i>&nbsp; Comments
                                                                </div>
                                                                <div class="fw-bold ps-5">
                                                                    {{ $order_details->instructions }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                                    <thead>
                                                        <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                            <th class="text-start" style="width:60%">Item</th>
                                                            <th class="text-center">Qty.</th>
                                                            <th class="text-end">Item Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="fw-semibold text-gray-600">
                                                        @if(isset($order_details->order_items) && count($order_details->order_items) > 0)
                                                            @foreach ($order_details->order_items as $ord_item)
                                                                @php
                                                                    $item_dt = itemDetails($ord_item['item_id']);
                                                                    $item_images = App\Models\ItemImages::where('item_id',$ord_item['item_id'])->get();
                                                                    $item_image = (count($item_images) > 0 && isset($item_images[0]->image)) ? asset('public/client_uploads/items/'.$item_images[0]->image) : asset('public/client_images/not-found/no_image_1.jpg');
                                                                    $options = (isset($ord_item['options']) && !empty($ord_item['options'])) ? unserialize($ord_item['options']) : [];
                                                                @endphp
                                                                <tr>
                                                                    <td class="text-start">
                                                                        <div class="d-flex align-items-center">
                                                                            <a class="symbol symbol-50px">
                                                                                <span class="symbol-label" style="background-image:url({{ $item_image }});"></span>
                                                                            </a>
                                                                            <div class="ms-5">
                                                                                <a href="{{ route('product.deatails',$ord_item->item_id) }}" class="fw-bold" style="color: #7e8299">
                                                                                    {{ ($ord_item->item_name) }}
                                                                                </a>
                                                                                @if(isset($ord_item->personalised_message) && !empty($ord_item->personalised_message))
                                                                                    <p class="m-0 text-muted">Personalised Message : {{ $ord_item->personalised_message }}</p>
                                                                                @endif
                                                                                @if(count($options) > 0)
                                                                                    @foreach ($options as $option)
                                                                                        @php
                                                                                            $option_price = App\Models\OptionPrice::with(['option'])->where('id',$option)->first();
                                                                                            $option_name = (isset($option_price['option'][$title_key])) ? $option_price['option'][$title_key] : '';
                                                                                            $price_name = (isset($option_price[$name_key])) ? $option_price[$name_key] : '';
                                                                                        @endphp
                                                                                        <p class="m-0"><strong> - {{ $option_name }} :</strong> {{ $price_name }}</p>
                                                                                    @endforeach
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $ord_item['item_qty'] }}
                                                                    </td>
                                                                    <td class="text-end">
                                                                        {{ $ord_item['sub_total_text'] }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                        <tr>
                                                            <td colspan="2" class="text-dark fs-5 text-end">
                                                                Sub Total
                                                            </td>
                                                            <td class="text-dark fs-5 text-end">{{ Currency::currency($default_currency)->format($order_details->order_subtotal) }}</td>
                                                        </tr>

                                                        {{-- Apply Discount --}}
                                                        @if($order_details->discount_per > 0)
                                                            <tr>
                                                                <td colspan="2" class="text-dark fs-5 text-end">
                                                                    {{ __('Discount') }}
                                                                </td>
                                                                @if($order_details->discount_type == 'fixed')
                                                                    <td class="text-dark fs-5 text-end">- {{ Currency::currency($default_currency)->format($order_details->discount_per) }}</td>
                                                                @else
                                                                    <td class="text-dark fs-5 text-end">- {{ $order_details->discount_per }}%</td>
                                                                @endif
                                                            </tr>
                                                        @endif

                                                        {{-- Apply GST --}}
                                                        @if($order_details->cgst > 0 && $order_details->sgst > 0)
                                                            <tr>
                                                                @php
                                                                    $gst_amt = $order_details->cgst + $order_details->sgst;
                                                                    $gst_amt = $order_details->gst_amount / $gst_amt;
                                                                @endphp
                                                                <td colspan="2" class="text-dark fs-5 text-end">
                                                                    {{ __('CGST.') }} ({{ $order_details->cgst }}%)</td>
                                                                <td class="text-dark fs-5 text-end">+ {{ Currency::currency($default_currency)->format($order_details->cgst * $gst_amt) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" class="text-dark fs-5 text-end">
                                                                    {{ __('SGST.') }} ({{ $order_details->sgst }}%)
                                                                </td>
                                                                <td class="text-dark fs-5 text-end">+ {{ Currency::currency($default_currency)->format($order_details->sgst * $gst_amt) }}</td>
                                                            </tr>
                                                        @endif

                                                        {{-- Apply Shipping Charge --}}
                                                        @if($order_details->checkout_type == 'delivery' && !empty($order_details->shipping_amount) && $order_details->shipping_amount > 0)
                                                            <tr>
                                                                <td colspan="2" class="text-dark fs-5 text-end">
                                                                    {{ __('Shipping Charge') }}
                                                                </td>
                                                                <td class="text-dark fs-5 text-end">+ {{ Currency::currency($default_currency)->format($order_details->shipping_amount) }}</td>
                                                            </tr>
                                                        @endif

                                                        <tr>
                                                            <td colspan="3" class="text-dark fs-5 fw-bold text-end">
                                                                {{ Currency::currency($default_currency)->format($order_details->order_total) }}
                                                            </td>
                                                        </tr>
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
