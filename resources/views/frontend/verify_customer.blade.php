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

    $discount_per = session()->get('discount_per');
    $discount_type = session()->get('discount_type');

    $total_amount = 0;

    $user_details = App\Models\User::where('id',1)->where('user_type',1)->first();
    $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
    $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

    $current_check_type = session()->get('checkout_type');

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Verify Account'))

@section('content')

<main>
    <div class="bg_login">
        <div class="container">
            <section class="section register d-flex flex-column align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="card border-0 mb-3">
                                <div class="card-body">
                                    <div class="pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Verify Your Account</h5>
                                        <p class="text-center small">Enter your verification code to verify your account</p>
                                    </div>

                                    <form class="row g-3" method="POST" action="{{ route('customer.verify.post') }}">
                                        @csrf
                                        <input type="hidden" name="user_id" id="user_id" value="{{ (isset($user_id)) ? $user_id : '' }}">
                                        <div class="col-12">
                                            <label for="verification_code" class="form-label">Verification Code</label>
                                            <input type="text" maxlength="8" name="verification_code" class="form-control {{ ($errors->has('verification_code')) ? 'is-invalid' : '' }}" id="verification_code">
                                            @if($errors->has('verification_code'))
                                                <div class="invalid-feedback">
                                                    {{ $errors->first('verification_code') }}
                                                </div>
                                            @endif
                                        </div>

                                        <div class="col-12">
                                            <button class="btn btn-primary w-100" type="submit">Verify</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </div>
    </div>
</main>

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
