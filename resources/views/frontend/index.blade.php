@extends('frontend.layouts.frontend-layout')

@section('title', __('Clients'))

@section('content')

@php
    // Language Settings
    $language_settings = clientLanguageSettings();
    $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

    // Language Details
    $language_detail = App\Models\Languages::where('id',$primary_lang_id)->first();
    $lang_code = isset($language_detail->code) ? $language_detail->code : '';

    $description_key = $lang_code."_description";
    $image_key = $lang_code."_image";
@endphp

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
        <!-- <div class="swiper-button-next"><i class="fas fa-angle-right csb"></i></div>
        <div class="swiper-button-prev"><i class="fas fa-angle-left csb"></i></div> -->
    </div>
</section>

<section class="sec_main our_collection">
    <div class="sec_title">
        <h2><span>Our Collection</span></h2>
    </div>
    <div class="collection_inr">
        <div class="collction_box position-relative">
            <div class="collection_img">
                <img class="w-100" src="{{ asset('public/frontend/image/collection1.png')}}">
            </div>
            <div class="collection_name">
                <h3>Night Light</h3>
            </div>
        </div>
        <div class="collction_box position-relative">
            <div class="collection_img">
                <img class="w-100" src="{{asset('public/frontend/image/collection1.png')}}">
            </div>
            <div class="collection_name">
                <h3>Night Light</h3>
            </div>
        </div>
        <div class="collction_box position-relative">
            <div class="collection_img">
                <img class="w-100" src="{{asset('public/frontend/image/collection1.png')}}">
            </div>
            <div class="collection_name">
                <h3>Night Light</h3>
            </div>
        </div>
        <div class="collction_box position-relative">
            <div class="collection_img">
                <img class="w-100" src="{{asset('public/frontend/image/collection1.png')}}">
            </div>
            <div class="collection_name">
                <h3>Night Light</h3>
            </div>
        </div>
        <div class="collction_box position-relative">
            <div class="collection_img">
                <img class="w-100" src="{{asset('public/frontend/image/collection1.png')}}">
            </div>
            <div class="collection_name">
                <h3>Night Light</h3>
            </div>
        </div>
        <div class="collction_box position-relative">
            <div class="collection_img">
                <img class="w-100" src="{{asset('public/frontend/image/collection1.png')}}">
            </div>
            <div class="collection_name">
                <h3>Night Light</h3>
            </div>
        </div>
    </div>
</section>

<section class="offer_sec">
    <div class="offer_banner position-relative">
        <img src="{{asset('public/frontend//image/offer.png')}}" class="w-100" />
        <button class="btn btn_explore">Explore More</button>
    </div>
</section>

<section class="sec_main product_sec">
    <div class="sec_title">
        <h2><span>Father's Day</span></h2>
    </div>
    <div class="product_items">
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/mug_dad.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Personalised Stainless Steel Mugs 350ml</h3>
                <p>$34.55</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/mug_dad.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Personalised Stainless Steel Mugs 350ml</h3>
                <p>$34.55</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/mug_dad.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Personalised Stainless Steel Mugs 350ml</h3>
                <p>$34.55</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/mug_dad.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Personalised Stainless Steel Mugs 350ml</h3>
                <p>$34.55</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/mug_dad.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Personalised Stainless Steel Mugs 350ml</h3>
                <p>$34.55</p>
            </div>
        </div>
    </div>
    <div class="view_bt text-center py-3">
        <button class="btn btn_explore">- View all</button>
    </div>
</section>

<section class="sec_main product_sec">
    <div class="sec_title">
        <h2><span>Night lights</span></h2>
    </div>
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="novelty-tab" data-bs-toggle="tab" data-bs-target="#novelty"
                type="button" role="tab" aria-controls="novelty" aria-selected="true">NOVELTY</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="printed-tab" data-bs-toggle="tab" data-bs-target="#printed" type="button"
                role="tab" aria-controls="printed" aria-selected="false">PRINTED</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="novelty" role="tabpanel" aria-labelledby="novelty-tab">
            <div class="product_items">
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{asset('public/frontend/image/night_light.png')}}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Personalised Unicorn Wings Led Night Light</h3>
                        <p>$45.95</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{asset('public/frontend/image/night_light.png')}}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Personalised Unicorn Wings Led Night Light</h3>
                        <p>$45.95</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{asset('public/frontend/image/night_light.png')}}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Personalised Unicorn Wings Led Night Light</h3>
                        <p>$45.95</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{asset('public/frontend/image/night_light.png')}}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Personalised Unicorn Wings Led Night Light</h3>
                        <p>$45.95</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{asset('public/frontend/image/night_light.png')}}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Personalised Unicorn Wings Led Night Light</h3>
                        <p>$45.95</p>
                    </div>
                </div>
            </div>
            <div class="view_bt text-center py-3">
                <button class="btn btn_explore">- View all</button>
            </div>
        </div>
        <div class="tab-pane fade" id="printed" role="tabpanel" aria-labelledby="printed-tab">
            <div class="product_items">
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{ asset('public/frontend/image/printed.png') }}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Elephant Night Light</h3>
                        <p>$68.12</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{ asset('public/frontend/image/printed.png') }}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Elephant Night Light</h3>
                        <p>$68.12</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{ asset('public/frontend/image/printed.png') }}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Elephant Night Light</h3>
                        <p>$68.12</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{ asset('public/frontend/image/printed.png') }}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Elephant Night Light</h3>
                        <p>$68.12</p>
                    </div>
                </div>
                <div class="product_box">
                    <div class="product_image">
                        <img src="{{ asset('public/frontend/image/printed.png') }}" class="w-100">
                        <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
                    </div>
                    <div class="product_info">
                        <h3>Elephant Night Light</h3>
                        <p>$68.12</p>
                    </div>
                </div>
            </div>
            <div class="view_bt text-center py-3">
                <button class="btn btn_explore">- View all</button>
            </div>
        </div>
    </div>

</section>

<section class="sec_main product_sec">
    <div class="sec_title">
        <h2><span>Religious</span></h2>
    </div>
    <div class="product_items">
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/rakhi.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Evil Eye Rakhi</h3>
                <p>$14.35</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/rakhi.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Evil Eye Rakhi</h3>
                <p>$14.35</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/rakhi.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Evil Eye Rakhi</h3>
                <p>$14.35</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/rakhi.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Evil Eye Rakhi</h3>
                <p>$14.35</p>
            </div>
        </div>
        <div class="product_box">
            <div class="product_image">
                <img src="{{asset('public/frontend/image/rakhi.png')}}" class="w-100">
                <button class="btn cart_bt"><i class="fa-solid fa-bag-shopping"></i></button>
            </div>
            <div class="product_info">
                <h3>Evil Eye Rakhi</h3>
                <p>$14.35</p>
            </div>
        </div>
    </div>
    <div class="view_bt text-center py-3">
        <button class="btn btn_explore">- View all</button>
    </div>
</section>

<section class="sec_main img_gallery">
    <div class="container">
        <div class="gallery_inr">
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
            <div class="gallery_img">
                <a href="#">
                    <img src="{{asset('public/frontend/image/gallery.jpg')}}" class="w-100">
                </a>
            </div>
        </div>
    </div>
</section>

@endsection