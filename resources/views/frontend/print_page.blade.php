@php
    // Language Settings
    $language_settings = clientLanguageSettings();
    $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

    // Language Details
    $language_detail = App\Models\Languages::where('id',$primary_lang_id)->first();
    $lang_code = isset($language_detail->code) ? $language_detail->code : '';

    // Client Settings
    $client_settings = getClientSettings();
    $default_currency = (isset($client_settings['default_currency'])) ? $client_settings['default_currency'] : 'USD';
    $business_name = (isset($client_settings['business_name'])) ? $client_settings['business_name'] : 'Mahantam Laser Crafts';

    $description_key = $lang_code."_description";
    $image_key = $lang_code."_image";
    $name_key = $lang_code."_name";

    $title = $page_details[$name_key]." - ".$business_name;
@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', $title)

@section('content')

	<section class="contact_us_main sec_main">
	      <div class="container">
	        <div class="sec_title">
	          <h2><span>{{ $page_details[$name_key] }}</span></h2>
	        </div>
	      </div>
          <div class="container">
            <div class="row">
                <div class="col-md-9">
                    {!! $page_details[$description_key] !!}
                </div>
                <div class="col-md-3">
                    <a href="{{ route('contact.us') }}" class="btn btn-primary w-100">GET A QUOTE</a>
                </div>
            </div>
          </div>
    </section>

@endsection
