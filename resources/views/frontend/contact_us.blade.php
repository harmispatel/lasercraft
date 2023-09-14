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

    $description_key = $lang_code."_description";
    $image_key = $lang_code."_image";
    $name_key = $lang_code."_name";

    $styles = (isset($contact_page['styles'])) ? unserialize($contact_page['styles']) : [];

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Contact Us'))

@section('content')

	<section class="contact_us_main sec_main">
	      <div class="container">
	        <div class="sec_title">
	          <h2><span>Contact Info</span></h2>
	        </div>
	      </div>
          <div class="container mt-2">
            <div class="row">
                <div class="col-md-12">
                    <div class="check-in-page">
                        <div class="row justify-content-center">
                            <div class="col-md-8 mb-3">
                                <div class="check-in-form" style="background-color: {{ isset($styles['background_color']) ? $styles['background_color'] : '' }}">
                                    <form action="{{ route('submit.contact.us') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <label for="firstname" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">First Name <span class="text-danger">*</span></label>
                                                <input type="text" name="firstname" id="firstname" placeholder="Enter Your First Name" class="form-control {{ ($errors->has('firstname')) ? 'is-invalid' : '' }}" value="{{ old('firstname') }}">
                                                @if($errors->has('firstname'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('firstname') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="lastname" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">Last Name <span class="text-danger">*</span></label>
                                                <input type="text" name="lastname" id="lastname" placeholder="Enter Your Last Name" class="form-control {{ ($errors->has('lastname')) ? 'is-invalid' : '' }}" value="{{ old('lastname') }}">
                                                @if($errors->has('lastname'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('lastname') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="email" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">Email <span class="text-danger">*</span></label>
                                                <input type="text" name="email" id="email" placeholder="Enter Your Email" class="form-control {{ ($errors->has('email')) ? 'is-invalid' : '' }}" value="{{ old('email') }}">
                                                @if($errors->has('email'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('email') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="phone" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">Phone No. <span class="text-danger">*</span></label>
                                                <input type="number" name="phone" id="phone" placeholder="Enter Your Phone No." class="form-control {{ ($errors->has('phone')) ? 'is-invalid' : '' }}" value="{{ old('phone') }}">
                                                @if($errors->has('phone'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('phone') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="company_name" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">Company Name <span class="text-danger">*</span></label>
                                                <input type="text" name="company_name" id="company_name" placeholder="Enter Your Company Name." class="form-control {{ ($errors->has('company_name')) ? 'is-invalid' : '' }}" value="{{ old('company_name') }}">
                                                @if($errors->has('company_name'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('company_name') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-2">
                                                <label for="document" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">Document</label>
                                                <input type="file" name="document" id="document" class="form-control {{ ($errors->has('document')) ? 'is-invalid' : '' }}">
                                                @if($errors->has('document'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('document') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12 mb-2">
                                                <label for="message" class="form-label" style="color: {{ isset($styles['font_color']) ? $styles['font_color'] : '' }}">Message <span class="text-danger">*</span></label>
                                                <textarea name="message" id="message" rows="5" class="w-100 form-control {{ ($errors->has('message')) ? 'is-invalid' : '' }}" placeholder="Write Your Message here...">{{ old('message') }}</textarea>
                                                @if($errors->has('message'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('message') }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12 text-center mt-2">
                                                <button class="btn btn-sm" style="background-color: {{ isset($styles['button_color']) ? $styles['button_color'] : '#198754' }}; color: {{ isset($styles['button_text_color']) ? $styles['button_text_color'] : '#fff' }}">SUBMIT</button>
                                            </div>
                                        </div>
                                    </form>
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
