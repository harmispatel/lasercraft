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

    // Order Settings
    $order_settings = getOrderSettings();

    // Payment Settings
    $payment_settings = getPaymentSettings();

    // Client Settings
    $client_settings = getClientSettings();
    $default_currency = (isset($client_settings['default_currency'])) ? $client_settings['default_currency'] : 'USD';

    $delivery_message = (isset($client_settings['delivery_message']) && !empty($client_settings['delivery_message'])) ? $client_settings['delivery_message'] : 'Sorry your address is out of our delivery range.';

    $discount_per = session()->get('discount_per');
    $discount_type = session()->get('discount_type');

    $total_amount = 0;

    $user_details = App\Models\User::where('id',1)->where('user_type',2)->first();
    $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
    $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

    $current_check_type = session()->get('checkout_type');

    $cust_lat = session()->get('cust_lat','');
    $cust_lng = session()->get('cust_long','');
    $cust_address = session()->get('cust_address','');

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Checkout Page'))

@section('content')

    {{-- Delivery Out of Range Message Model --}}
    <div class="modal fade" id="deliveyModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="deliveyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {!! $delivery_message !!}
                </div>
            </div>
        </div>
    </div>

	<section class="view_cart_main sec_main">
		<div class="container">
			<div class="row">
                @php
                    $sub_total = Cart::getTotal();
                    $total_amount = $sub_total;
                @endphp
				<div class="col-md-12 col-lg-4">
					<div class="cart_total_info">
						<div class="cart_total_title">
							<h2>Order Summary</h2>
						</div>
						<div class="cart_total_info_inr">
							<table class="table mb-0">
								<tbody>
									<tr>
										<td>Sub Total :</td>
										<td class="text-end">{{ Currency::currency($default_currency)->format($sub_total) }}</td>
									</tr>
                                    @if($discount_per > 0)
                                        <tr>
                                            <td>Discount :</td>
                                            @if($discount_type == 'fixed')
                                                <td class="text-end">- {{ Currency::currency($default_currency)->format($discount_per) }}</td>
                                            @else
                                                <td class="text-end">- {{ $discount_per }}%</td>
                                            @endif

                                            @php
                                                if($discount_type == 'fixed')
                                                {
                                                    $discount_amount = $discount_per;
                                                }
                                                else
                                                {
                                                    $discount_amount = ($total_amount * $discount_per) / 100;
                                                }
                                                $total_amount = $total_amount - $discount_amount;
                                            @endphp
                                        </tr>
                                    @endif
                                    @if($cgst > 0 && $sgst > 0)
                                        <tr>
                                            <td>CGST ({{ $cgst }}%) : </td>
                                            <td class="text-end">+ {{ Currency::currency($default_currency)->format(($total_amount * $cgst) / 100) }}</td>
                                            @php
                                                $cgst_amount = ($total_amount * $cgst) / 100;
                                            @endphp
                                        </tr>
                                        <tr>
                                            <td>SGST ({{ $sgst }}%) : </td>
                                            <td class="text-end">+ {{ Currency::currency($default_currency)->format(($total_amount * $sgst) / 100) }}</td>
                                            @php
                                                $sgst_amount = ($total_amount * $sgst) / 100;
                                                $total_amount = $total_amount + $sgst_amount + $cgst_amount;
                                            @endphp
                                        </tr>
                                    @endif
									<tr class="bg-light">
										<th>Total Amount:</th>
										<td class="text-end"> <span class="fw-bold"> {{ Currency::currency($default_currency)->format($total_amount) }} </span></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
                    @if($current_check_type == 'delivery')
                        <div class="row">
                            <div class="col-md-12">
                                <div id="map" style="height: 300px;"></div>
                            </div>
                        </div>
                    @endif
				</div>
                <div class="col-md-12 col-lg-8">
                    <div class="checkout-form">
                        <h3 class="text-center">Please Fill Your Information to Checkout</h3>
                        <hr>
                        <form method="POST" enctype="multipart/form-data" action="{{ route('cart.checkout.post') }}">
                            @csrf
                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $total_amount }}">
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <label for="firstname" class="form-label">Firstname <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" id="firstname" class="form-control {{ ($errors->has('firstname')) ? 'is-invalid' : '' }}" value="{{ Auth::user()->firstname }}">
                                    @if($errors->has('firstname'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('firstname') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname" class="form-label">Lastname <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" id="lastname" class="form-control {{ ($errors->has('lastname')) ? 'is-invalid' : '' }}" value="{{ Auth::user()->lastname }}">
                                    @if($errors->has('lastname'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('lastname') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="text" name="email" id="email" class="form-control {{ ($errors->has('email')) ? 'is-invalid' : '' }}" value="{{ Auth::user()->email }}">
                                    @if($errors->has('email'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Phone No. <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control {{ ($errors->has('phone_number')) ? 'is-invalid' : '' }}" maxlength="10" value="{{ (Auth::user()->mobile) ? Auth::user()->mobile : old('phone_number') }}">
                                    @if($errors->has('phone_number'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('phone_number') }}
                                        </div>
                                    @endif
                                </div>
                                @if($current_check_type == 'delivery')
                                    <div class="col-md-6 mb-3">
                                        <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                        <input type="hidden" name="latitude" id="latitude" value="{{ $cust_lat }}">
                                        <input type="hidden" name="longitude" id="longitude" value="{{ $cust_lng }}">
                                        <input type="text" name="address" id="address" class="form-control {{ ($errors->has('address')) ? 'is-invalid' : '' }}" value="{{ $cust_address }}">
                                        @if($errors->has('address'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('address') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="street_number" class="form-label">Street No. <span class="text-danger">*</span></label>
                                        <input type="text" name="street_number" id="street_number" class="form-control {{ ($errors->has('street_number')) ? 'is-invalid' : '' }}" value="{{ old('street_number') }}">
                                        @if($errors->has('street_number'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('street_number') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="floor" class="form-label">Floor <span class="text-danger">*</span></label>
                                        <input type="text" name="floor" id="floor" class="form-control {{ ($errors->has('floor')) ? 'is-invalid' : '' }}" value="{{ old('floor') }}">
                                        @if($errors->has('floor'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('floor') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="door_bell" class="form-label">Door Bell <span class="text-danger">*</span></label>
                                        <input type="text" name="door_bell" id="door_bell" class="form-control {{ ($errors->has('door_bell')) ? 'is-invalid' : '' }}" value="{{ old('door_bell') }}">
                                        @if($errors->has('door_bell'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('door_bell') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label for="instructions" class="form-label">Instructions</label>
                                    <textarea name="instructions" id="instructions" rows="4" class="form-control">{{ old('instructions') }}</textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                                    <select name="payment_method" id="payment_method" class="form-select {{ ($errors->has('payment_method')) ? 'is-invalid' : '' }}">
                                        <option value="">Select Payment Method</option>
                                        @if(isset($payment_settings['cash']) && $payment_settings['cash'] == 1)
                                            <option value="cash" {{ (old('payment_method') == 'cash') ? 'selected' : '' }}>Cash</option>
                                        @endif
                                        @if(isset($payment_settings['paypal']) && $payment_settings['paypal'] == 1)
                                            <option value="paypal" {{ (old('payment_method') == 'paypal') ? 'selected' : '' }}>Paypal</option>
                                        @endif
                                    </select>
                                    @if($errors->has('payment_method'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('payment_method') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <button class="btn checkout_bt">Checkout</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
			</div>
		</div>
	</section>

@endsection

@section('page-js')

<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyBsf7LHMQFIeuA_7-bR7u7EXz5CUaD6I2A&libraries=places"></script>
<script type="text/javascript">

    // Error Messages
    @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}')
    @endif

    // Success Messages
    @if (Session::has('success'))
        toastr.success('{{ Session::get('success') }}')
    @endif

    // Map Functionality
    var lat = "{{ $cust_lat }}";
    var lng = "{{ $cust_lng }}";
    var check_type = "{{ $current_check_type }}";

    navigator.geolocation.getCurrentPosition(function (position){
        if(lat == '' || lng == ''){
            lat = position.coords.latitude;
            lng = position.coords.longitude;
        }

        if(check_type == 'delivery'){
            initMap(lat,lng);
        }
    },function errorCallback(error){
        console.log(error)
    });

    function initMap(lat,long){
        const myLatLng = { lat: parseFloat(lat), lng: parseFloat(long) };
        const map = new google.maps.Map(document.getElementById("map"), {
            zoom: 16,
            center: myLatLng,
        });

        new google.maps.Marker({
            position: myLatLng,
            map,
        });
    }


    if(check_type == 'delivery')
    {
        google.maps.event.addDomListener(window, 'load', initialize);

        function initialize()
        {
            var input = document.getElementById('address');
            var autocomplete = new google.maps.places.Autocomplete(input);

            $('#address').keydown(function (e)
            {
                if (e.keyCode == 13)
                {
                    e.preventDefault();
                    return false;
                }
            });

            autocomplete.addListener('place_changed', function ()
            {
                var place = autocomplete.getPlace();
                if(place != '')
                {
                    initMap(place.geometry['location'].lat(),place.geometry['location'].lng());
                    $('#latitude').val(place.geometry['location'].lat());
                    $('#longitude').val(place.geometry['location'].lng());

                    $.ajax({
                        type: "POST",
                        url: "{{ route('cart.set.delivery.address') }}",
                        data: {
                            "_token" : "{{ csrf_token() }}",
                            "latitude" : place.geometry['location'].lat(),
                            "longitude" : place.geometry['location'].lng(),
                            "address" : $('#address').val(),
                        },
                        dataType: "JSON",
                        success: function (response)
                        {
                            if(response.success == 1)
                            {
                                if(response.available == 0)
                                {
                                    $('#deliveyModal').modal('show');
                                }
                            }
                            else
                            {
                                console.error(response.message);
                            }
                        }
                    });

                }
            });
        }
    }

</script>

@endsection
