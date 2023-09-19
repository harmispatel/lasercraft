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

    $discount_per = session()->get('discount_per');
    $discount_type = session()->get('discount_type');

    $total_amount = 0;

    $user_details = App\Models\User::where('id',1)->where('user_type',2)->first();
    $sgst = (isset($user_details['sgst'])) ? $user_details['sgst'] : 0;
    $cgst = (isset($user_details['cgst'])) ? $user_details['cgst'] : 0;
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
                                            <button class="btn"><i class="fa-solid fa-minus"></i></button>
                                            <input class="form-control" type="text" min="1" value="{{ $cart_item['quantity'] }}" />
                                            <button class="btn"><i class="fa-solid fa-plus"></i></button>
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
                            {{-- <div class="checkout-option-check">
                                <div class="opt_radio_box">
                                    <input type="radio" id="takeaway" name="checkout_type" class="d-none" value="takeaway" />
                                    <label class="btn btn-default" for="takeaway">TakeAway</label>
                                </div>
                                <div class="opt_radio_box">
                                    <input type="radio" id="delivery" name="checkout_type" class="d-none" value="delivery" />
                                    <label class="btn btn-default" for="delivery">Delivery</label>
                                </div>
                            </div> --}}
                            <a class="btn checkout_bt w-100" href="{{ route('cart.checkout') }}">Checkout</a>
                        </div>
                    </div>
                @endif
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

</script>

@endsection
