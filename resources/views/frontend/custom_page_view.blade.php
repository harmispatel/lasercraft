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

    $styles = (isset($contact_page['styles'])) ? unserialize($contact_page['styles']) : [];

    $title = $custom_page['name']." - ".$business_name;

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', $title)

@section('content')

	<section class="contact_us_main sec_main">
	      <div class="container">
	        <div class="sec_title">
	          <h2><span>{{ $custom_page['name'] }}</span></h2>
	        </div>
	      </div>
          <div class="container mt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="check-in-page">
                        <div class="row justify-content-center">
                            <div class="col-md-12 mb-3">
                                {!! $custom_page['content'] !!}
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

    // Success Messages
    @if (Session::has('success'))
        toastr.success('{{ Session::get('success') }}')
    @endif

    // Error Messages
    @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}')
    @endif

</script>

@endsection
