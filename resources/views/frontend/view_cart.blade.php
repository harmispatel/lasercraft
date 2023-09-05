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

    $client_settings = getClientSettings();
@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('View Cart'))

@section('content')

	<section class="view_cart_main sec_main">
		<div class="container">
			<div class="row">
				<div class="col-md-12 col-lg-8">
					<div class="cart_main">
						<div class="cart_info">
							<div class="cart_product_info">	
								<div class="product_img">
									<img src="https://i.pinimg.com/474x/fd/55/b7/fd55b7347bcbe0f021ed2ad9eba66742--wood-lights-wooden-lamp.jpg">
								</div>
								<div class="product_name">
									<h2>Product Name</h2>
									<div class="product_review">
										<i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star-half-stroke text-warning"></i>
									</div>
									<p><strong>Color :</strong> Blue</p>
								</div>
							</div>
							<div class="cart_product_price_info">
								<div class="product_price">
									<h3>Price</h3>
									<p><span>$560</span> $450</p>
								</div>
								<div class="quantity">
	                                <button class="btn"><i class="fa-solid fa-minus"></i></button>
	                                <input class="form-control" type="text" min={1} value="2" />
	                                <button class="btn"><i class="fa-solid fa-plus"></i></button>
	                            </div>
	                            <div class="product_total">
									<h3>Price</h3>
									<p>$900</p>
								</div>
							</div>
							<button class="delet_bt">
								<i class="fa-solid fa-trash"></i>
							</button>
						</div>
						<div class="cart_info">
							<div class="cart_product_info">	
								<div class="product_img">
									<img src="https://i.pinimg.com/474x/fd/55/b7/fd55b7347bcbe0f021ed2ad9eba66742--wood-lights-wooden-lamp.jpg">
								</div>
								<div class="product_name">
									<h2>Product Name</h2>
									<div class="product_review">
										<i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star text-warning"></i>
	                                    <i class="fa-solid fa-star-half-stroke text-warning"></i>
									</div>
									<p><strong>Color :</strong> Blue</p>
								</div>
							</div>
							<div class="cart_product_price_info">
								<div class="product_price">
									<h3>Price</h3>
									<p><span>$560</span> $450</p>
								</div>
								<div class="quantity">
	                                <button class="btn"><i class="fa-solid fa-minus"></i></button>
	                                <input class="form-control" type="text" min={1} value="2" />
	                                <button class="btn"><i class="fa-solid fa-plus"></i></button>
	                            </div>
	                            <div class="product_total">
									<h3>Price</h3>
									<p>$900</p>
								</div>
							</div>
							<button class="delet_bt">
								<i class="fa-solid fa-trash"></i>
							</button>
						</div>
					</div>
				</div>
				<div class="col-md-12 col-lg-4">
					<div class="cart_total_info">
						<div class="cart_total_title">
							<h2>Order Summary</h2>
							<p>#MN0124</p>
						</div>
						<div class="cart_total_info_inr">
							<table class="table mb-0">
								<tbody>
									<tr>
										<td>Sub Total :</td>
										<td class="text-end">$ 1800</td>
									</tr>
									<tr>
										<td>Discount :</td>
										<td class="text-end">- $ 120</td>
									</tr>
									<tr>
										<td>Shipping Charge :</td>
										<td class="text-end">$ 25</td>
									</tr>
									<tr>
										<td>Estimated Tax :</td>
										<td class="text-end">$ 18.20</td>
									</tr>
									<tr class="bg-light">
										<th>Total :</th>
										<td class="text-end"> <span class="fw-bold"> $ 1,733.2 </span></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

@endsection
