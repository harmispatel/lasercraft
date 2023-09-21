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

    $user_details = App\Models\User::where('id',1)->where('user_type',2)->first();
    $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
    $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;

    $current_check_type = session()->get('checkout_type');

@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('View Cart'))

@section('content')

	<section class="view_cart_main sec_main">
		<div class="container">
			<div class="row">
                @if(count($cart_items) > 0)
				    <div class="col-md-12 col-lg-8">
					    <div class="cart_main">
                            @foreach ($cart_items as $cart_item)
                                @php
                                    $item_id = $cart_item['id'];
                                    $item_details = App\Models\Items::with(['itemImages','itemPrices'])->where('id',$item_id)->first();
                                    $item_image = (count($item_details->itemImages) > 0 && isset($item_details->itemImages[0]->image)) ? $item_details->itemImages[0]->image : '';
                                @endphp

                                <div class="cart_info">
                                    <div class="cart_product_info">
                                        <div class="product_img">
                                            @if(!empty($item_image) && file_exists('public/client_uploads/items/'.$item_image))
                                                <img src="{{ asset('public/client_uploads/items/'.$item_image) }}">
                                            @else
                                                <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}">
                                            @endif
                                        </div>
                                        <div class="product_name">
                                            <h2>{{ $cart_item['name'] }}</h2>
                                            @if(count($cart_item['attributes']) > 0)
                                                @foreach ($cart_item['attributes'] as $attribute)
                                                    @php
                                                        $option_price = App\Models\OptionPrice::with(['option'])->where('id',$attribute)->first();
                                                        $option_name = (isset($option_price['option'][$title_key])) ? $option_price['option'][$title_key] : '';
                                                        $price_name = (isset($option_price[$name_key])) ? $option_price[$name_key] : '';
                                                    @endphp
                                                    <p><strong>{{ $option_name }} :</strong> {{ $price_name }}</p>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                    <div class="cart_product_price_info">
                                        <div class="product_price">
                                            <h3>Price</h3>
                                            <p>{{ Currency::currency($default_currency)->format($cart_item['price']) }}</p>
                                        </div>
                                        <div class="quantity">
                                            <button type="button" class="btn btn-danger quantity-left-minus"><i class="fa-solid fa-minus"></i></button>
                                            <input class="form-control input-quantity" item-id="{{ $cart_item['id'] }}" readonly type="text" min="1" value="{{ $cart_item['quantity'] }}" />
                                            <button type="button" class="btn btn-success quantity-right-plus"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                        <div class="product_total">
                                            <h3>Sub Total</h3>
                                            <p>{{ Currency::currency($default_currency)->format($cart_item['price'] * $cart_item['quantity']) }}</p>
                                        </div>
                                    </div>
                                    <a class="delet_bt" href="{{ route('cart.remove',$item_id) }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="col-md-12 text-center">
                        <h3>Your Cart is Empty.</h3>
                    </div>
                @endif
                @php
                    $sub_total = Cart::getTotal();
                    $total_amount = $sub_total;
                @endphp
                @if($sub_total > 0)
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
                        <div class="checkout-option">
                            <div class="checkout-option-check">
                                @if (isset($order_settings['takeaway']) && $order_settings['takeaway'] == 1)
                                    <div class="opt_radio_box">
                                        <input type="radio" id="takeaway" name="checkout_type" class="d-none" value="takeaway" {{ ($current_check_type == 'takeaway') ? 'checked' : '' }} value="takeaway" />
                                        <label class="btn btn-default" for="takeaway">TakeAway</label>
                                    </div>
                                @endif
                                @if (isset($order_settings['delivery']) && $order_settings['delivery'] == 1)
                                    <div class="opt_radio_box">
                                        <input type="radio" id="delivery" name="checkout_type" class="d-none" value="delivery" {{ ($current_check_type == 'delivery') ? 'checked' : '' }} value="delivery" />
                                        <label class="btn btn-default" for="delivery">Delivery</label>
                                    </div>
                                @endif
                            </div>
                            @if(!isset($current_check_type) || empty($current_check_type))
                                <button class="btn checkout_bt w-100" id="chk-btn-ds" disabled>Checkout</button>
                            @else
                                <a class="btn checkout_bt w-100" id="chk-btn" href="{{ route('cart.checkout') }}">Checkout</a>
                            @endif
                        </div>
                    </div>
                @endif
			</div>
		</div>
	</section>

@endsection

@section('page-js')

<script type="text/javascript">

    $(document).ready(function () {

        // Qty Increment Descrement Function
        var qty_arr = $('.input-quantity');
        $.each(qty_arr, function (indexInArray, qty) {
            var qty_val = $(this).val();
            if(qty_val == 1){
                $(this).prev().attr("disabled",true);
            }
        });

        $('.quantity-right-plus').on('click',function(e){
            e.preventDefault();
            var quantity = parseInt($(this).prev().val()) + 1;
            var itemID = $(this).prev().attr('item-id');

            if(quantity > 1){
                $(this).prev().prev().attr("disabled",false);
            }

            $(this).prev().val(quantity);
            if(quantity == 20){
                $(this).attr("disabled",true);
                return false;
            }
            updateCart(quantity,itemID);
        });

        $('.quantity-left-minus').on('click',function(e){
            e.preventDefault();
            var quantity = parseInt($(this).next().val()) - 1;
            var itemID = $(this).next().attr('item-id');
            $(this).next().val(quantity);
            if(quantity <= 20){
                $(this).next().next().attr("disabled",false);
            }

            if(quantity == 1){
                $(this).attr("disabled",true);
                return false;
            }
            updateCart(quantity,itemID);
        });
    });

    // function for update quantity in session
    function updateCart(qty,itemID){
        $.ajax({
            type: "POST",
            url: "{{ route('cart.update') }}",
            data: {
                "_token" : "{{ csrf_token() }}",
                "id" : itemID,
                "quantity" : qty,
            },
            dataType: "JSON",
            success: function (response) {
                if(response.success == 1){
                    toastr.success(response.message);
                    location.reload();
                }else{
                    toastr.error(response.message);
                    location.reload();
                }
            }
        });
    }

    // Error Messages
    @if (Session::has('error'))
        toastr.error('{{ Session::get('error') }}')
    @endif

    // Success Messages
    @if (Session::has('success'))
        toastr.success('{{ Session::get('success') }}')
    @endif


    // Set Checkout type in Session
    $('input[type=radio][name=checkout_type]').on('change',function(){
        var check_type = $(this).val();

        $.ajax({
                type: "POST",
                url: "{{ route('cart.set.checkout.type') }}",
                data: {
                    '_token' : "{{ csrf_token() }}",
                    'check_type' : check_type,
                },
                dataType: "JSON",
                success: function (response)
                {
                    if(response.success == 1)
                    {
                        location.reload();
                    }
                    else
                    {
                        toastr.error(response.message);
                        return false;
                    }
                }
            });
    });

</script>

@endsection
