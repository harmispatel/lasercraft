@php
    $shop_settings = getClientSettings($shop_details['id']);
    $shop_theme_id = isset($shop_settings['shop_active_theme']) ? $shop_settings['shop_active_theme'] : '';

    // Theme
    $theme = \App\Models\Theme::where('id',$shop_theme_id)->first();
    $theme_name = isset($theme['name']) ? $theme['name'] : '';

    // Theme Settings
    $theme_settings = themeSettings($shop_theme_id);

    $payment_settings = getPaymentSettings($shop_details['id']);
    $upi_id = (isset($payment_settings['upi_id'])) ? $payment_settings['upi_id'] : '';
    $payee_name = (isset($payment_settings['payee_name'])) ? $payment_settings['payee_name'] : '';
    $upi_qr = (isset($payment_settings['upi_qr'])) ? $payment_settings['upi_qr'] : '';

    // Shop Currency
    $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'EUR';

    // Store Schedule
    $store_schedule = checkStoreSchedule($shop_details['id']);

    $local = session('locale','en');

@endphp

<!DOCTYPE html>
<html lang="{{ $local }}" dir="{{ ($local == 'ar') ? "rtl" : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link href="{{ asset('public/admin_images/favicons/smartqrscan.ico') }}" rel="icon">
    @include('shop.shop-css')
</head>
<body class="{{ (!empty($theme_name) && $theme_name == 'Default Dark Theme') ? 'dark' : '' }} custom-scroll">

    {{-- Item Details Modal --}}
    <div class="modal fade" id="itemDetailsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="itemDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="item_dt_div">
                </div>
            </div>
        </div>
    </div>

    {{-- Store Close Modal --}}
    <div class="modal fade" id="shopScheduleModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="shopScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="text-danger">The store remains closed during this time. You cannot order until the store is opened.</h3>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" aria-label="Close">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    <div class="modal fade" id="PaymentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="PaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Pay Using UPI / QR </h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="payment_detail_modal">
                    <div class="row">
                            <div class="col-md-12 text-center">
                                <h5>{{ __('QR Code & Button For UPI Payment')}}</h5>
                                <p>Please Click on Pay Now Button for Confirm your order or Scan QR code to complete Payment.</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                @if(!empty($upi_qr) && file_exists('public/admin_uploads/upi_qr/'.$upi_qr))
                                    <img src="{{ asset('public/admin_uploads/upi_qr/'.$upi_qr) }}" width="200">
                                @endif
                                <p><strong>UPI ID : {{ $upi_id }}</strong></p>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label" for="payment_amount">Enter Amount</label>
                                <input type="number" name="payment_amount" id="payment_amount" class="form-control">
                            </div>
                            <div class="col-md-12 text-center mt-2">
                                <a pay-type="gpay" id="gpay_btn" class="btn me-2 mt-2 pay-btn" style="border: 1px solid green"><img src="{{ asset('public/admin_images/logos/gpay.png') }}" height="40"></a>
                                <a pay-type="phonepe" id="phonepe_btn" class="btn me-2 mt-2 pay-btn" style="border: 1px solid green"><img src="{{ asset('public/admin_images/logos/phonepe.png') }}" height="40"></a>
                                <a pay-type="paytm" id="paytm_btn" class="btn mt-2 pay-btn" style="border: 1px solid green"><img src="{{ asset('public/admin_images/logos/paytm.png') }}" height="40"></a>
                                {{-- <a href="upi://pay?pa={{ $upi_id }}&pn={{ $payee_name }}&am=0&cu={{ $currency }}" id="upi_btn" class="btn btn-success">Pay Now</a> --}}
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navbar --}}
    @include('shop.shop-navbar')

    {{-- Main Content --}}
    <main id="main" class="main shop-main">
        @yield('content')
    </main>

    {{-- JS --}}
    @include('shop.shop-js')

    {{-- Custom JS --}}
    @yield('page-js')

</body>
</html>
