@php
    $primary_lang_details = clientLanguageSettings();

    $language = getLangDetails(isset($primary_lang_details['primary_language']) ? $primary_lang_details['primary_language'] : '');
    $language_code = isset($language['code']) ? $language['code'] : '';
    $name_key = $language_code."_name";
    $title_key = $language_code."_title";

    $shop_settings = getClientSettings();

    // Order Settings
    $order_setting = getOrderSettings();

    $discount_type = (isset($order->discount_type) && !empty($order->discount_type)) ? $order->discount_type : 'percentage';

    // Shop Currency
    $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'EUR';
@endphp

@extends('client.layouts.client-layout')

@section('title', __('Order Details'))

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>{{ __('Order Details')}}</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('client.orders') }}">{{ __('Orders') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Order Details') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Order Details Section --}}
    <section class="section dashboard">
        <div class="row">

            <div class="col-md-12 mb-3" id="print-data" style="display: none;"></div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row justify-content-center">
                            <div class="col-md-12 mb-2 text-center">
                                <h3>{{ __('Order') }} : #{{ $order->id }}</h3>
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
                                                                <i class="bi bi-calendar-date"></i>&nbsp;{{ __('Order Date') }}
                                                            </div>
                                                            <div class="fw-bold">
                                                                {{ date('d-m-Y h:i:s',strtotime($order->created_at)) }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="client-order-info">
                                                            <div class="">
                                                                <i class="bi bi-credit-card"></i>&nbsp;{{ __('Payment Method') }}
                                                            </div>
                                                            <div class="fw-bold text-capitalize">
                                                                {{ $order->payment_method }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="client-order-info">
                                                            <div class="">
                                                                <i class="bi bi-truck"></i>&nbsp;{{ __('Shipping Method') }}
                                                            </div>
                                                            <div class="fw-bold text-capitalize">
                                                                {{ ($order->checkout_type == 'takeaway') ? 'PickUp' : 'Ship' }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="client-order-info">
                                                            <div class="">
                                                                <i class="bi bi-truck"></i>&nbsp;{{ __('Order Status') }}
                                                            </div>
                                                            <div class="fw-bold text-capitalize">
                                                                {{ $order->order_status }}
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
                                                                <i class="bi bi-person-circle"></i>&nbsp;{{ __('Customer') }}
                                                            </div>
                                                            <div class="fw-bold">
                                                                {{ $order->firstname }} {{ $order->lastname }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="client-order-info">
                                                            <div class="">
                                                                <i class="bi bi-envelope"></i>&nbsp;{{ __('Email') }}
                                                            </div>
                                                            <div class="fw-bold text-break">
                                                                {{ $order->email }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="client-order-info">
                                                            <div class="">
                                                                <i class="bi bi-telephone"></i>&nbsp;{{ __('Mobile No.') }}
                                                            </div>
                                                            <div class="fw-bold">
                                                                {{ $order->phone }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="text-muted">
                                                        <div class="client-order-info">
                                                            <div class="">
                                                                <i class="bi bi-card-text"></i>&nbsp;{{ __('Comments') }}
                                                            </div>
                                                            <div class="fw-bold ps-5">
                                                                {{ $order->instructions }}
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @if($order->checkout_type == 'delivery')
                                <div class="col-md-12 mt-2 mb-2">
                                    <div class="card mb-0">
                                        <div class="card-body">
                                            <table class="table align-middle table-row-bordered mb-0 fs-6 gy-5 min-w-300px">
                                                <tbody class="fw-semibold text-gray-600">
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-map"></i>&nbsp;{{ __('Address') }}
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order->address }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-map"></i>&nbsp;{{ __('Street') }}
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order->street_number }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-map"></i>&nbsp;{{ __('City') }}
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order->city }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-map"></i>&nbsp;{{ __('State') }}
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order->state }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-muted">
                                                            <div class="client-order-info">
                                                                <div class="">
                                                                    <i class="bi bi-map"></i>&nbsp;{{ __('Postcode') }}
                                                                </div>
                                                                <div class="fw-bold">
                                                                    {{ $order->postcode }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if($order->checkout_type == 'takeaway')
                                <div class="col-md-12 mt-2 mb-2">
                                    <strong>PickUp Location : </strong> {{ $order->pickup_location }}
                                </div>
                            @endif

                            @if($order->order_status == 'rejected')
                                <div class="col-md-12 mt-2 mb-2">
                                    <strong>Order Rejection Reason : </strong> {{ $order->reject_reason }}
                                </div>
                            @endif
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table align-middle table-row-dashed fs-6 gy-5 mb-0">
                                        <thead>
                                            <tr class="text-start text-gray-400 fw-bold fs-7 text-uppercase gs-0">
                                                <th class="text-start" style="width:60%">{{ __('Item') }}</th>
                                                <th class="text-center">{{ __('Qty.') }}</th>
                                                <th class="text-end">{{ __('Item Total') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody class="fw-semibold text-gray-600">
                                            @if(isset($order->order_items) && count($order->order_items) > 0)
                                                @foreach ($order->order_items as $ord_item)
                                                    @php
                                                        $item_dt = itemDetails($ord_item['item_id']);
                                                        $item_image = (isset($item_dt['image']) && !empty($item_dt['image']) && file_exists('public/client_uploads/shops/'.$shop_slug.'/items/'.$item_dt['image'])) ? asset('public/client_uploads/shops/'.$shop_slug.'/items/'.$item_dt['image']) : asset('public/client_images/not-found/no_image_1.jpg');
                                                        $options = (isset($ord_item['options']) && !empty($ord_item['options'])) ? unserialize($ord_item['options']) : [];
                                                    @endphp
                                                    <tr>
                                                        <td class="text-start">
                                                            <div class="d-flex align-items-center">
                                                                <a class="symbol symbol-50px">
                                                                    <span class="symbol-label" style="background-image:url({{ $item_image }});"></span>
                                                                </a>
                                                                <div class="ms-5">
                                                                    <a class="fw-bold" style="color: #7e8299">
                                                                        {{ ($ord_item->item_name) }}
                                                                    </a>
                                                                    <p class="m-0 text-muted">Personalised Message : {{ $ord_item['personalised_message'] }}</p>
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
                                                    {{ __('Sub Total') }}
                                                </td>
                                                <td class="text-dark fs-5 text-end">{{ Currency::currency($currency)->format($order->order_subtotal) }}</td>
                                            </tr>

                                            @if($order->discount_per > 0)
                                                <tr>
                                                    <td colspan="2" class="text-dark fs-5 text-end">
                                                        {{ __('Discount') }}
                                                    </td>
                                                    @if($order->discount_type == 'fixed')
                                                        <td class="text-dark fs-5 text-end">- {{ Currency::currency($currency)->format($order->discount_per) }}</td>
                                                    @else
                                                        <td class="text-dark fs-5 text-end">- {{ $order->discount_per }}%</td>
                                                    @endif
                                                </tr>
                                                {{-- <tr>
                                                    <td colspan="3" class="text-dark fs-5 fw-bold text-end">
                                                        {{ Currency::currency($currency)->format($order->discount_value) }}
                                                    </td>
                                                </tr> --}}
                                            @endif

                                            @if($order->cgst > 0 && $order->sgst > 0)
                                                <tr>
                                                    @php
                                                        $gst_amt = $order->cgst + $order->sgst;
                                                        $gst_amt = $order->gst_amount / $gst_amt;
                                                    @endphp
                                                    <td colspan="2" class="text-dark fs-5 text-end">
                                                        {{ __('CGST.') }} ({{ $order->cgst }}%)</td>
                                                    <td class="text-dark fs-5 text-end">+ {{ Currency::currency($currency)->format($order->cgst * $gst_amt) }}</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="text-dark fs-5 text-end">
                                                        {{ __('SGST.') }} ({{ $order->sgst }}%)
                                                    </td>
                                                    <td class="text-dark fs-5 text-end">+ {{ Currency::currency($currency)->format($order->sgst * $gst_amt) }}</td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td colspan="3" class="text-dark fs-5 fw-bold text-end">
                                                    {{ Currency::currency($currency)->format($order->order_total) }}
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
    </section>

@endsection


{{-- Custom Script --}}
@section('page-js')

    <script type="text/javascript">

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": 4000
        }

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

        @if (Session::has('error'))
            toastr.error('{{ Session::get('error') }}')
        @endif

    </script>
@endsection
