@php

    // Language Settings
    $language_settings = clientLanguageSettings();
    $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

    // Language Details
    $language_detail = App\Models\Languages::where('id',$primary_lang_id)->first();
    $lang_code = isset($language_detail->code) ? $language_detail->code : '';

    $description_key = $lang_code."_description";
    $image_key = $lang_code."_image";
    $name_key = $lang_code."_name";
    $title_key = $lang_code."_title";

    $client_settings = getClientSettings();
    $default_currency = (isset($client_settings['default_currency'])) ? $client_settings['default_currency'] : 'USD';

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Success'))

@section('content')

<input type="hidden" name="order_id" id="order_id" value="{{ isset($order_details['id']) ? $order_details['id'] : '' }}">
<input type="hidden" name="order_status" id="order_status" value="{{ isset($order_details['order_status']) ? $order_details['order_status'] : '' }}">
<input type="hidden" name="estimated_time" id="estimated_time" value="{{ isset($order_details['estimated_time']) ? $order_details['estimated_time'] : '' }}">
<input type="hidden" name="reject_reason" id="reject_reason" value="{{ isset($order_details['reject_reason']) ? $order_details['reject_reason'] : '' }}">

<section class="mt-5 mb-5">
    <div class="container px-3 my-5 clearfix">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 text-center mb-3">
                        <h3>Thank You for Your Order <i class="bi bi-hand-thumbs-up"></i></h3>
                    </div>
                    <div class="col-md-12 text-center mb-3 pending-div">
                        <img src="{{ asset('public/client_images/loader/loader1.gif') }}" width="80">
                    </div>
                    <div class="col-md-12 text-center mb-3 pending-div">
                        <p>{{ __('A store Employee checks your order.') }}</p>
                    </div>
                    <div class="col-md-12 text-center mb-3 success-div" style="display: none;">
                        <i class="bi bi-check-circle text-success fs-2"></i>
                        <p>Your order has been accepted and will be at your location in 30 minutes.</p>
                        <a class="btn btn-primary" href="{{ route('home') }}">Back to Menu</a>
                    </div>
                    <div class="col-md-12 text-center mb-3 reject-div" style="display: none;">
                        <i class="bi bi-x-circle text-danger fs-2"></i>
                        <p>{{ __('Sorry, your order has been declined. Reason:') }} {{ isset($order_details['reject_reason']) ? $order_details['reject_reason'] : '' }}</p>
                        <a class="btn btn-primary" href="{{ route('home') }}">Back to Menu</a>
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

    $(document).ready(function ()
    {
        var order_status = $('#order_status').val();
        var order_id = $('#order_id').val();
        var estimated_time = $('#estimated_time').val();
        var reject_reason = $('#reject_reason').val();

        if(order_status == 'accepted')
        {
            $('.pending-div').hide();
            $('.success-div p').html('Your order has been accepted and will be at your location in '+estimated_time+' minutes.');
            $('.success-div').show();
        }
        else if(order_status == 'rejected')
        {
            $('.pending-div').hide();
            $('.reject-div p').html(@json(__('Sorry, your order has been declined. Reason:'))+" "+reject_reason);
            $('.reject-div').show();
        }
        else
        {
            checkStaus();
        }
    });

    // Check Status
    function checkStaus()
    {
        var order_status = $('#order_status').val();
        var order_id = $('#order_id').val();
        var estimated_time = $('#estimated_time').val();

        setInterval(() =>
        {
            // Check Status
            $.ajax({
                type: "POST",
                url: "{{ route('check.order.status.user') }}",
                data: {
                    "_token" : "{{ csrf_token() }}",
                    "order_id" : order_id,
                },
                dataType: "JSON",
                success: function (response) {
                    if(response.success == 1)
                    {
                        if(response.status == 'accepted')
                        {
                            location.reload();
                        }
                        else if(response.status == 'rejected')
                        {
                            location.reload();
                        }
                    }
                }
            });
        }, 5000);
    }

</script>

@endsection
