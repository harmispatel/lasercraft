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

    $item_price = (isset($item_details->itemPrices) && count($item_details->itemPrices) > 0) ? $item_details->itemPrices[0]->price : 0.00;
@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Product Details'))

@section('content')

    <section class="item_detail sec_main">
        <div class="">
            <div class="row">
                <div class="col-md-6">
                    <div class="deatil_page_slider">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="swiper-container gallery-thumbs">
                                    <div class="swiper-wrapper">
                                        @if(count($item_details->itemImages) > 0)
                                            @foreach ($item_details->itemImages as $item_image)
                                                <div class="swiper-slide">
                                                    @if(!empty($item_image['image']) && file_exists('public/client_uploads/items/'.$item_image['image']))
                                                        <img src="{{ asset('public/client_uploads/items/'.$item_image['image']) }}" class="w-100" />
                                                    @else
                                                        <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" class="w-100" />
                                                    @endif
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="swiper-slide">
                                                <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" class="w-100" />
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-10">
                                <div class="deatil_page_slider_gallery">
                                    <div class="swiper-container gallery-top">
                                        <div class="swiper-wrapper">
                                            @if(count($item_details->itemImages) > 0)
                                                @foreach ($item_details->itemImages as $item_image)
                                                    <div class="swiper-slide">
                                                        @if(!empty($item_image['image']) && file_exists('public/client_uploads/items/'.$item_image['image']))
                                                            <img src="{{ asset('public/client_uploads/items/'.$item_image['image']) }}" class="w-100" />
                                                        @else
                                                            <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" class="w-100" />
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="swiper-slide">
                                                    <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" class="w-100" />
                                                </div>
                                            @endif
                                        </div>
                                        <div class="swiper-button-next swiper-button-white"></div>
                                        <div class="swiper-button-prev swiper-button-white"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="item_detail_info">
                        <div class="item_name">
                            <h3>{{ $item_details[$name_key] }}</h3>
                            <label>{{ Currency::currency($default_currency)->format($item_price); }}</label>
                        </div>
                        <div class="add_design_text mb-3">
                            <div class="from-group">
                                <label class="mb-2">Personalised Message</label>
                                <textarea class="form-control" placeholder="Add your Text" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="add_cart_group">
                            <div class="quantity">
                                <button class="btn"><i class="fa-solid fa-minus"></i></button>
                                <input class="form-control" type="text" min={1} value="1" />
                                <button class="btn"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <button class="btn add_cart_btn"><i class="fa-solid fa-cart-shopping me-2"></i>Add to
                                Cart</button>
                        </div>

                        <div class="des_text">
                            <h4>Description</h4>
                            <div>{!! $item_details[$description_key] !!}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="item_review sec_main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="add_review">
                        <button class="btn add_review_btn">Write a review</button>
                    </div>
                </div>
                <div class="col-lg-8 col-md-10">
                    <div class="review_info">
                        <form>
                            <div class="from-group">
                                <label>Name : </label>
                                <input type="text" name="name" class="form-control" placeholder="Enter Your Name">
                            </div>
                            <div class="from-group">
                                <label>E-mail : </label>
                                <input type="email" name="email" class="form-control" placeholder="Enter Your Email">
                            </div>
                            <div class="from-group">
                                <label>Review</label>
                                <textarea class="form-control" placeholder="Your Message" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="sec_main releted_product">
        <div class="sec_title">
            <h2><span>Related Products.</span></h2>
        </div>
        @if(count($related_items) > 0)
            <div class="product_items">
                @foreach ($related_items as $rel_item)
                    @php
                        $item_image = (isset($rel_item->itemImages) && count($rel_item->itemImages) > 0) ? $rel_item->itemImages[0]->image : '';
                        $item_price = (isset($rel_item->itemPrices) && count($rel_item->itemPrices) > 0) ? $rel_item->itemPrices[0]->price : 0.00;
                    @endphp
                    <a href="{{ route('product.deatails',$rel_item['id']) }}">
                        <div class="product_box">
                            <div class="product_image">
                                @if(!empty($item_image) && file_exists('public/client_uploads/items/'.$item_image))
                                    <img src="{{asset('public/client_uploads/items/'.$item_image)}}" class="w-100">
                                @else
                                    <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" class="w-100">
                                @endif
                            </div>
                            <div class="product_info">
                                <h3>{{ $rel_item[$name_key] }}</h3>
                                <p>{{ Currency::currency($default_currency)->format($item_price); }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="row">
                <div class="col-md-12 text-center">
                    <h4>Related Products Not Found!</h4>
                </div>
            </div>
        @endif
    </section>

@endsection
