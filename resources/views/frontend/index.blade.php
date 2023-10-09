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

    $intro_second = (isset($client_settings['intro_icon_duration']) && !empty($client_settings['intro_icon_duration'])) ? $client_settings['intro_icon_duration'] : '';
@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Laser Craft'))

@section('content')

<input type="hidden" name="intro_second" id="intro_second" value="{{ $intro_second }}">

<section class="banner_slider">
    <div class="swiper">
        <div class="swiper-wrapper">

            @foreach($banners as $banner)
                @php
                    $banner_image = isset($banner->$image_key) ? $banner->$image_key : '';
                    $banner_description = isset($banner->$description_key) ? $banner->$description_key : '';
                @endphp

                @if(!empty($banner_image) && file_exists('public/client_uploads/banners/'.$banner_image))
                    <div class="swiper-slide">
                        <img class="img-fluid" style="background-image: url('{{ asset('public/client_uploads/banners/'.$banner_image) }}')" />
                    </div>
                @endif
            @endforeach
        </div>
        <div class="swiper-button-next"><i class="fas fa-angle-right csb"></i></div>
        <div class="swiper-button-prev"><i class="fas fa-angle-left csb"></i></div>
        <div class="swiper-pagination"></div>
    </div>
</section>

<section class="sec_main our_collection">
    <div class="sec_title">
        <h2><span>Our Collection</span></h2>
    </div>
    <div class="collection_inr">

        @if(count($parent_categories) > 0)
            @foreach($parent_categories as $parent_cat)
                @if(count($parent_cat->subcategories) > 0)
                    @foreach ($parent_cat->subcategories as $sub_cat)
                        <div class="collction_box position-relative">
                            <a href="{{ route('categories.collections',$sub_cat['id']) }}">
                                <div class="collection_img">
                                    @php
                                        $cat_img = (isset($sub_cat->categoryImages) && count($sub_cat->categoryImages) > 0) ? $sub_cat->categoryImages[0]->image : '';
                                    @endphp
                                    @if(!empty($cat_img) && file_exists('public/client_uploads/categories/'.$cat_img))
                                        <img class="w-100" src="{{ asset('public/client_uploads/categories/'.$cat_img) }}">
                                    @else
                                        <img class="w-100" src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}">
                                    @endif
                                </div>
                                <div class="collection_name">
                                    <h3>{{ isset($sub_cat[$name_key]) ? $sub_cat[$name_key] : '' }}</h3>
                                </div>
                            </a>
                        </div>
                    @endforeach
                @endif
            @endforeach
        @endif
    </div>
</section>

@php
    $main_loop_key = 1;
@endphp
@if(count($child_categories) > 0)
    @foreach ($child_categories as $category)
        @if($main_loop_key > 3)
            @php
                break;
            @endphp
        @else
            @if(count($category->items) > 0)
                @php
                    $main_loop_key++;
                @endphp
                <section class="sec_main product_sec">
                    <div class="sec_title">
                        <h2><span>{{ $category[$name_key] }}</span></h2>
                    </div>
                    <div class="product_items">
                        @foreach ($category->items as $item_key => $item)
                            @php
                                $item_image = (isset($item->itemImages) && count($item->itemImages) > 0) ? $item->itemImages[0]->image : '';
                                $item_price = (isset($item->itemPrices) && count($item->itemPrices) > 0) ? $item->itemPrices[0]->price : 0.00;

                                if($item_key > 4)
                                {
                                    break;
                                }

                            @endphp

                            <a href="{{ route('product.deatails',$item['id']) }}">
                                <div class="product_box">
                                    <div class="product_image">
                                        @if(!empty($item_image) && file_exists('public/client_uploads/items/'.$item_image))
                                            <img src="{{asset('public/client_uploads/items/'.$item_image)}}" class="w-100">
                                        @else
                                            <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" class="w-100">
                                        @endif
                                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                                    </div>
                                    <div class="product_info">
                                        <h3>{{ $item[$name_key] }}</h3>
                                        <p>{{ Currency::currency($default_currency)->format($item_price); }}</p>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="view_bt text-center py-3">
                        <a class="btn btn_explore" href="{{ route('categories.collections',$category['id']) }}">View all</a>
                    </div>
                </section>
            @endif
        @endif
    @endforeach
@endif


@if(isset($client_settings['intro_icon_status']) && !empty($client_settings['intro_icon_status']) && $client_settings['intro_icon_status'] == 1)
    @if(isset($client_settings['shop_intro_icon']) && !empty($client_settings['shop_intro_icon']) && file_exists('public/client_uploads/intro_icons/'.$client_settings['shop_intro_icon']))
        @php
            $intro_file_ext = pathinfo($client_settings['shop_intro_icon'], PATHINFO_EXTENSION);
        @endphp

        <div class="cover-img">
            @if($intro_file_ext == 'mp4' || $intro_file_ext == 'mov')
                <video src="{{ asset('public/client_uploads/intro_icons/'.$client_settings['shop_intro_icon']) }}" width="100%" autoplay muted loop>
            </video>
            @else
                <img src="{{ asset('public/client_uploads/intro_icons/'.$client_settings['shop_intro_icon']) }}" width="100%">
            @endif
        </div>
    @endif
@endif


@endsection

@section('page-js')

<script type="text/javascript">

    $(document).ready(function () {
        // Timeout for Cover
        var introSec = $('#intro_second').val() * 1000;
        setTimeout(() => {
            $('.cover-img').hide();
        }, introSec);
    });

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
