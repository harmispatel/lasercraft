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
    $title_key = $lang_code."_title";

    $item_price = (isset($item_details->itemPrices) && count($item_details->itemPrices) > 0) ? $item_details->itemPrices[0]->price : 0.00;

    $title = $item_details[$name_key]." - ".$business_name;
@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', $title)

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
                                                        @if(!empty($item_image['image']) && file_exists('public/client_uploads/items/og_images/'.$item_image['image']))
                                                            <img src="{{ asset('public/client_uploads/items/og_images/'.$item_image['image']) }}" class="w-100" />
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
                    <form id="cartForm" method="POST" action="{{ route('cart.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="item_detail_info">
                            <div class="item_name">
                                <h3>{{ $item_details[$name_key] }}</h3>
                                <label>{{ Currency::currency($default_currency)->format($item_price); }}</label>
                                <input type="hidden" name="item_price" id="item_price" value="{{ $item_price }}">
                                <input type="hidden" name="item_id" id="item_id" value="{{ $item_details['id'] }}">
                            </div>
                            @php
                                // Options
                                $option_ids = (isset($item_details['options']) && !empty($item_details['options'])) ? unserialize($item_details['options']) : [];
                            @endphp
                            @if(count($option_ids) > 0)
                                @foreach($option_ids as $outer_key => $opt_id)
                                    @php
                                        $opt_dt = App\Models\Option::with(['optionPrices'])->where('id',$opt_id)->first();
                                        $enable_price = (isset($opt_dt['enabled_price'])) ? $opt_dt['enabled_price'] : '';
                                        $option_prices = (isset($opt_dt['optionPrices'])) ? $opt_dt['optionPrices'] : [];
                                    @endphp
                                    <div class="design_opation">
                                        <h4>{{ $opt_dt[$title_key] }}</h4>
                                        <div class="design_opation_inr">
                                            @if(count($option_prices) > 0)
                                                @foreach($option_prices as $key => $option_price)
                                                    @php
                                                        $opt_price = Currency::currency($default_currency)->format($option_price['price']);
                                                        $opt_price_label = (isset($option_price[$name_key])) ? $option_price[$name_key] : "";
                                                    @endphp
                                                    <div class="opt_radio_box">
                                                        <input type="radio" id="opt_{{ $outer_key }}_{{ $key }}" name="attr_option[{{ $outer_key }}]" class="d-none" value="{{ $option_price['id'] }}" />
                                                        <label class="btn btn-default" for="opt_{{ $outer_key }}_{{ $key }}">{{ $opt_price_label }}</label>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            @if(isset($item_details['pers_message']) && $item_details['pers_message'] == 1)
                                <div class="add_design_text mb-3">
                                    <div class="from-group">
                                        <label class="mb-2">Personalised Message</label>
                                        <textarea class="form-control" name="personalised_message" placeholder="Add your Text" rows="3"></textarea>
                                    </div>
                                </div>
                            @endif
                            <div class="add_cart_group">
                                <div class="quantity">
                                    <button type="button" class="btn btn-danger quantity-left-minus"><i class="fa-solid fa-minus"></i></button>
                                    <input class="form-control" name="quantity" id="quantity" type="text" value="1" readonly />
                                    <button type="button" class="btn btn-success quantity-right-plus"><i class="fa-solid fa-plus"></i></button>
                                </div>
                                <button class="btn add_cart_btn"><i class="fa-solid fa-cart-shopping me-2"></i>Add to Cart</button>
                            </div>

                            <div class="des_text">
                                <h4>Description</h4>
                                <div>{!! $item_details[$description_key] !!}</div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    @if($item_details['review'] == 1)
        <section class="item_review sec_main">
            <div class="sec_title">
                <h2><span>Customer Reviews</span></h2>
            </div>
            <div class="container">
                <div class="row justify-content-center align-items-center">
                    @if(count($item_details->ratings) > 0)
                    @php
                        $all_rat_key = [1,2,3,4,5];
                        $rating_array = $item_details->ratings->groupBy('rating')->mapWithKeys(function ($reviews, $rating){return [$rating => $reviews->count()];})->toArray();
                        $rating_array = collect($rating_array)->union(array_fill_keys(array_diff($all_rat_key, array_keys($rating_array)), 0))->all();
                        krsort($rating_array);
                        $avg_rat = round((number_format($averageRating,2)));
                    @endphp
                        <div class="col-md-4">
                            <div class="product_review">
                                <div class="review_product_inr">
                                    @php
                                        $remain_avg_star = 5 - $avg_rat;
                                    @endphp

                                    @for($i=1; $i <=$avg_rat; $i++)
                                        <i class="fa-solid fa-star text-warning"></i>
                                    @endfor

                                    @if($remain_avg_star > 0)
                                        @for ($i=1; $i<=$remain_avg_star; $i++)
                                        <i class="fa-regular fa-star text-warning"></i>
                                        @endfor
                                    @endif
                                </div>
                                <p>{{ (number_format($averageRating,2)) }} out of 5 Based on {{ count($item_details->ratings) }} reviews</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="product_review_point">
                                <ul>
                                    @if(count($rating_array) > 0)
                                    @foreach ($rating_array as $rat_key => $rat_arr)
                                        <li>
                                            <div class="review_product_inr">
                                                @php
                                                    $remain_star = 5 - $rat_key;
                                                @endphp

                                                @for ($i=1; $i<=$rat_key; $i++)
                                                    <i class="fa-solid fa-star text-warning"></i>
                                                @endfor

                                                @if($remain_star > 0)
                                                    @for ($i=1; $i<=$remain_star; $i++)
                                                    <i class="fa-regular fa-star text-warning"></i>
                                                    @endfor
                                                @endif
                                            </div>
                                            <div class="review_number">
                                                <span>({{ $rat_arr }})</span>
                                            </div>
                                        </li>
                                    @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    @else
                        <div class="col-md-4">
                            <div class="product_review">
                                <div class="review_product_inr">
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                    <i class="fa-solid fa-star text-warning"></i>
                                </div>
                                <p class="m-0">Be the first to write a review</p>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-4">
                        <div class="add_review text-center">
                            <a class="btn add_review_btn">Write a Review.</a>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-md-10 col-lg-7" id="review-info" style="display: none;margin-top: 30px;">
                        <div class="review_info">
                            <form id="itemReviewForm" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $item_details['id'] }}">
                                <div class="from-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter Your Name">
                                </div>
                                <div class="from-group">
                                    <label class="form-label">Email</label>
                                    <input type="text" name="email" class="form-control" placeholder="Enter Your email">
                                </div>
                                <div class="from-group">
                                    <label class="mb-0">Review</label>
                                    <div>
                                        <div class="rate">
                                            <input type="radio" id="star5" class="rate" name="rating" value="5" />
                                            <label for="star5" title="text">5 stars</label>
                                            <input type="radio" id="star4" class="rate" name="rating" value="4" checked />
                                            <label for="star4" title="text">4 stars</label>
                                            <input type="radio" id="star3" class="rate" name="rating" value="3"/>
                                            <label for="star3" title="text">3 stars</label>
                                            <input type="radio" id="star2" class="rate" name="rating" value="2"/>
                                            <label for="star2" title="text">2 stars</label>
                                            <input type="radio" id="star1" class="rate" name="rating" value="1"/>
                                            <label for="star1" title="text">1 star</label>
                                        </div>
                                    </div>
                                    <textarea class="form-control" rows="3" placeholder="Write Your Message" name="message"></textarea>
                                </div>
                                <div class="from-group">
                                    <a class="btn review_sub" onclick="submitItemReview()">Submit</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="sec_main releted_product">
        <div class="sec_title">
            <h2><span>Related Products</span></h2>
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

@section('page-js')

<script type="text/javascript">

    $(document).ready(function () {

        // Qty Increment Descrement Function
        var qty = parseInt($('#quantity').val());
        if(qty == 1){
            $('.quantity-left-minus').attr("disabled",true);
        }

        $('.quantity-right-plus').on('click',function(e){
            e.preventDefault();
            var quantity = parseInt($('#quantity').val()) + 1;

            if(quantity > 1){
                $('.quantity-left-minus').attr("disabled",false);
            }

            $('#quantity').val(quantity);
            if(quantity == 20){
                $(this).attr("disabled",true);
                return false;
            }
        });

        $('.quantity-left-minus').on('click',function(e){
            e.preventDefault();
            var quantity = parseInt($('#quantity').val()) - 1;
            $('#quantity').val(quantity);
            if(quantity <= 20){
                $('.quantity-right-plus').attr("disabled",false);
            }

            if(quantity == 1){
                $(this).attr("disabled",true);
                return false;
            }
        });
    });

</script>

@endsection
