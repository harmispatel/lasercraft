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

    $shipping_charge = (isset($order_settings['shipping_charge']) && !empty($order_settings['shipping_charge'])) ? $order_settings['shipping_charge'] : 0;

    // Payment Settings
    $payment_settings = getPaymentSettings();

    // Client Settings
    $client_settings = getClientSettings();
    $default_currency = (isset($client_settings['default_currency'])) ? $client_settings['default_currency'] : 'USD';
    $business_name = (isset($client_settings['business_name'])) ? $client_settings['business_name'] : 'Mahantam Laser Crafts';

    $delivery_message = (isset($client_settings['delivery_message']) && !empty($client_settings['delivery_message'])) ? $client_settings['delivery_message'] : 'Sorry your address is out of our delivery range.';
    $pickup_address = (isset($client_settings['pickup_address']) && !empty($client_settings['pickup_address'])) ? $client_settings['pickup_address'] : '';

    $discount_per = session()->get('discount_per');
    $discount_type = session()->get('discount_type');

    $total_amount = 0;

    $user_details = App\Models\User::where('id',1)->where('user_type',1)->first();
    $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
    $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

    $current_check_type = session()->get('checkout_type');

    $cust_lat = session()->get('cust_lat','');
    $cust_lng = session()->get('cust_long','');
    $cust_address = session()->get('cust_address','');

    $paypal_config = getPayPalConfig();
    $paypal_mode = (isset($paypal_config['settings']['mode'])) ? $paypal_config['settings']['mode'] : '';
    $client_id = (isset($paypal_config['client_id'])) ? $paypal_config['client_id'] : '';
    $secret_id = (isset($paypal_config['secret'])) ? $paypal_config['secret'] : '';

    $title = "Checkout - ".$business_name;

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', $title)

@section('content')

    <div class="payment-loader" style="display: none;">
        <img src="{{ asset('public/client_images/loader/loader1.gif') }}" alt="">
    </div>

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

                                    {{-- Apply Discount --}}
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

                                    {{-- Apply GST --}}
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

                                    {{-- Apply Shipping Charge --}}
                                    @if($current_check_type == 'delivery' && $shipping_charge > 0)
                                        @php
                                            $total_amount = $total_amount + $shipping_charge;
                                        @endphp
                                        <tr>
                                            <td>Shipping Charge : </td>
                                            <td class="text-end">+ {{ Currency::currency($default_currency)->format($shipping_charge) }}</td>
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
                        <form method="POST" id="checkoutForm" enctype="multipart/form-data" action="javascript:void(0)">
                            @csrf
                            <input type="hidden" name="total_amount" id="total_amount" value="{{ $total_amount }}">
                            <div class="row mt-4">
                                <div class="col-md-6 mb-3">
                                    <label for="firstname" class="form-label">Firstname <span class="text-danger">*</span></label>
                                    <input type="text" name="firstname" id="firstname" class="form-control {{ ($errors->has('firstname')) ? 'is-invalid' : '' }}" value="{{ (Auth::user() && Auth::user()->user_type == 3) ? Auth::user()->firstname : '' }}">
                                    @if($errors->has('firstname'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('firstname') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="lastname" class="form-label">Lastname <span class="text-danger">*</span></label>
                                    <input type="text" name="lastname" id="lastname" class="form-control {{ ($errors->has('lastname')) ? 'is-invalid' : '' }}" value="{{ (Auth::user() && Auth::user()->user_type == 3) ? Auth::user()->lastname : '' }}">
                                    @if($errors->has('lastname'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('lastname') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="text" name="email" id="email" class="form-control {{ ($errors->has('email')) ? 'is-invalid' : '' }}" value="{{ (Auth::user() && Auth::user()->user_type == 3) ? Auth::user()->email : '' }}">
                                    @if($errors->has('email'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Mobile No. <span class="text-danger">*</span></label>
                                    <input type="text" name="phone_number" id="phone_number" class="form-control {{ ($errors->has('phone_number')) ? 'is-invalid' : '' }}" maxlength="10" value="{{ (Auth::user() && Auth::user()->user_type == 3) ? Auth::user()->mobile : old('phone_number') }}">
                                    @if($errors->has('phone_number'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('phone_number') }}
                                        </div>
                                    @endif
                                </div>
                                @if($current_check_type == 'takeaway')
                                    <div class="col-md-6 mb-3">
                                        <label for="pickup_address" class="form-label">PickUp Location</label>
                                        <textarea name="pickup_location" id="pickup_location" rows="4" class="form-control {{ ($errors->has('pickup_location')) ? 'is-invalid' : '' }}" readonly>{{ $pickup_address }}</textarea>
                                        @if($errors->has('pickup_location'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('pickup_location') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if($current_check_type == 'delivery')
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
                                        <label for="address" class="form-label">Street Address <span class="text-danger">*</span></label>
                                        <input type="hidden" name="latitude" id="latitude" value="{{ $cust_lat }}">
                                        <input type="hidden" name="longitude" id="longitude" value="{{ $cust_lng }}">
                                        <input type="hidden" name="address_verified" id="address_verified" value="">
                                        <input type="text" name="address" id="address" class="form-control {{ ($errors->has('address')) ? 'is-invalid' : '' }}" value="{{ $cust_address }}">
                                        @if($errors->has('address'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('address') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                        <input type="text" name="city" id="city" class="form-control {{ ($errors->has('city')) ? 'is-invalid' : '' }}" value="{{ old('city') }}">
                                        @if($errors->has('city'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('city') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                        <input type="text" name="state" id="state" class="form-control {{ ($errors->has('state')) ? 'is-invalid' : '' }}" value="{{ old('state') }}">
                                        @if($errors->has('state'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('state') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="postcode" class="form-label">Postcode <span class="text-danger">*</span></label>
                                        <input type="text" name="postcode" id="postcode" class="form-control {{ ($errors->has('postcode')) ? 'is-invalid' : '' }}" value="{{ old('postcode') }}">
                                        @if($errors->has('postcode'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('postcode') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label for="instructions" class="form-label">Instructions</label>
                                    <textarea name="instructions" id="instructions" rows="4" class="form-control">{{ old('instructions') }}</textarea>
                                </div>
                                {{-- <div class="col-md-6 mb-3">
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
                                </div> --}}
                                <div class="col-md-6 mb-3">
                                    <div id="paypal-button-container" style="max-width:1000px;"></div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-12">
                                    <a class="btn checkout_bt">Checkout</a>
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
<script src="https://www.paypal.com/sdk/js?client-id={{ $client_id }}&currency={{ $default_currency }}"></script>
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
    var cart = @json(\Cart::getContent());
    var total_amt = @json(\Cart::getTotal());
    var items_arr = [];
    var discount_amount = 0;
    var currency = @json($default_currency);
    var discount_per = @json(session()->get('discount_per'));
    var discount_type = @json(session()->get('discount_type'));
    var cgst = parseFloat(@json($cgst));
    var sgst = parseFloat(@json($sgst));
    var shipping_charge = parseFloat(@json($shipping_charge));

    if(check_type == 'delivery'){
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
    }

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
            var streetInput = document.getElementById('street_number');
            var cityInput = document.getElementById('city');
            var stateInput = document.getElementById('state');
            var zipInput = document.getElementById('postcode');

            var autocompleteOptions = {
                componentRestrictions: { country: 'AU' } // Restrict the search to Australia (AU country code)
            };

            var autocomplete = new google.maps.places.Autocomplete(input, autocompleteOptions);

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
                var addressComponents = place.address_components;
                var formattedAddress = place.formatted_address;

                // Reset inputs
                // streetInput.value = '';
                cityInput.value = '';
                stateInput.value = '';
                zipInput.value = '';

                 // Fill inputs with data from Google Maps API
                for (var i = 0; i < addressComponents.length; i++) {
                    var component = addressComponents[i];

                    if (component.types.includes('street_number')) {
                        // streetInput.value = component.long_name;
                    }else if (component.types.includes('route')) {
                        // streetInput.value = component.long_name;
                    } else if (component.types.includes('locality')) {
                        cityInput.value = component.long_name;
                    } else if (component.types.includes('administrative_area_level_1')) {
                        stateInput.value = component.long_name;
                    } else if (component.types.includes('postal_code')) {
                        zipInput.value = component.long_name;
                    }
                }

                input.value = formattedAddress.split(',')[0];

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
                                $('#address_verified').val(response.available);
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

    $('.checkout_bt').on('click',function(){
        var fname = $('#firstname');
        var lname = $('#lastname');
        var email = $('#email');
        var mobile_no = $('#phone_number');
        var pickup_location = $('#pickup_location');
        var street_number = $('#street_number');
        var address = $('#address');
        var city = $('#city');
        var state = $('#state');
        var postcode = $('#postcode');
        var regex = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        var validate = 1;

        $('#paypal-button-container').hide();
        toastr.clear();

        if(!fname.val()){
            fname.addClass('is-invalid');
            toastr.error("Firstname Field is Required!");
            validate = 0;
        }else{
            fname.removeClass('is-invalid');
        }

        if(!lname.val()){
            lname.addClass('is-invalid');
            toastr.error("Lastname Field is Required!");
            validate = 0;
        }else{
            lname.removeClass('is-invalid');
        }

        if(!email.val()){
            email.addClass('is-invalid');
            toastr.error("Email Field is Required!");
            validate = 0;
        }
        else{
            if (regex.test(email.val()) == false) {
                email.addClass('is-invalid');
                toastr.error("Please Enter Valid Email ID!");
                validate = 0;
            }else {
                email.removeClass('is-invalid');
            }
        }

        if(!mobile_no.val()){
            mobile_no.addClass('is-invalid');
            toastr.error("Mobile Number Field is Required!");
            validate = 0;
        }else{
            if($.isNumeric(mobile_no.val()) == false){
                mobile_no.addClass('is-invalid');
                toastr.error("Mobile Number Must be Numeric Value!");
                validate = 0;
            }else if(mobile_no.val().length < 10){
                mobile_no.addClass('is-invalid');
                toastr.error("Mobile Number Must have 10 Digits!");
                validate = 0;
            } else{
                mobile_no.removeClass('is-invalid');
            }
        }

        if(check_type == 'delivery'){

            if(!street_number.val()){
                street_number.addClass('is-invalid');
                toastr.error("Street Number Field is Required!");
                validate = 0;
            }else{
                street_number.removeClass('is-invalid');
            }

            if(!address.val()){
                address.addClass('is-invalid');
                toastr.error("Street Address Field is Required!");
                validate = 0;
            }else{
                if($('#address_verified').val() == '' || $('#address_verified').val() == 0){
                    address.addClass('is-invalid');
                    toastr.error("Your Adress is Out of Our Delivery Range!");
                    validate = 0;
                }else{
                    address.removeClass('is-invalid');
                }
            }

            if(!city.val()){
                city.addClass('is-invalid');
                toastr.error("City Field is Required!");
                validate = 0;
            }else{
                city.removeClass('is-invalid');
            }

            if(!state.val()){
                state.addClass('is-invalid');
                toastr.error("State Field is Required!");
                validate = 0;
            }else{
                state.removeClass('is-invalid');
            }

            if(!postcode.val()){
                postcode.addClass('is-invalid');
                toastr.error("Postcode Field is Required!");
                validate = 0;
            }else{
                postcode.removeClass('is-invalid');
            }
        }else{
            if(!pickup_location.val()){
                pickup_location.addClass('is-invalid');
                toastr.error("PickUp Location Field is Required!");
                validate = 0;
            }else{
                pickup_location.removeClass('is-invalid');
            }
        }

        if(validate == 1){
            $('#paypal-button-container').show();
        }
    });


    // Make Cart Response Array
    $.each(cart, function (indexInArray, cart_item) {
        let inner_item = {};
        let ut_amount = {};

        // Item Name
        inner_item.name = cart_item.name;

        // Item Description
        inner_item.description =  cart_item.name;

        // Unit Amount
        ut_amount.currency_code = currency;
        ut_amount.value = cart_item.price;
        inner_item.unit_amount = ut_amount;

        // Item Quantity
        inner_item.quantity = cart_item.quantity;

        items_arr.push(inner_item);
    });

    // Add GST
    if(cgst > 0 && sgst > 0)
    {
        var gst_per = cgst + sgst;

        if(items_arr.length > 0){
            $.each(items_arr, function (key, item) {
                var new_price = item.unit_amount.value + (item.unit_amount.value * gst_per) / 100;
                items_arr[key].unit_amount.value = new_price.toFixed(2);
            });
        }

        total_amt += (total_amt * gst_per) / 100;
    }

    total_amt = total_amt.toFixed(2);

    // Add Discount
    if(discount_per > 0)
    {
        if(discount_type == 'fixed')
        {
            discount_amount = discount_per;
        }
        else
        {
            discount_amount = total_amt * discount_per / 100;
            discount_amount = discount_amount.toFixed(2);
        }
    }
    var discountedTotal = total_amt - discount_amount;

    if(check_type == 'delivery' && shipping_charge > 0){
        discountedTotal += shipping_charge;
    }else{
        shipping_charge = 0;
    }

    paypal.Buttons({
        createOrder: function(data, actions) {

            return actions.order.create({
                purchase_units: [{
                    amount: {
                        currency_code : currency,
                        value: discountedTotal.toFixed(2), // Set the payment amount dynamically
                        breakdown: {
                            "item_total":{
                                currency_code : currency,
                                value: total_amt,
                            },
                            "discount": {
                                currency_code: currency,
                                value: discount_amount // Include the discount amount in the breakdown
                            },
                            "shipping": {
                                currency_code: currency,
                                value: shipping_charge // Include the shipping charge in the breakdown
                            }
                        }
                    },
                    items: items_arr,
                }]
            });
        },
        onApprove: function(data, actions) {

            // return actions.order.capture().then(function(details) {
            // });

            if(actions.order.capture()){

                var myFormData = new FormData(document.getElementById('checkoutForm'));

                $.ajax({
                    type: "POST",
                    url: "{{ route('paypal.payment.process') }}",
                    data: myFormData,
                    dataType: "JSON",
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function(){
                        $('.payment-loader').show();
                    },
                    success: function (response) {
                        if(response.success == 1){
                            window.location.href = response.success_url;
                        }else{
                            toastr.error(response.message);
                        }
                    }
                });

                // Call your backend to save the transaction
                // return fetch('/paypal/execute-payment', {
                //     method: 'POST',
                //     headers: {
                //         'content-type': 'application/json'
                //     },
                //     body: JSON.stringify({
                //         orderID: data.orderID,
                //         payerID: data.payerID
                //     })
                // });
            }
        },
        onError: function(err) {
            toastr.error("Payment Faild!");
            console.log('PayPal Error : ', err);
        }
    }).render('#paypal-button-container');

    $('#paypal-button-container').hide();

</script>

@endsection
