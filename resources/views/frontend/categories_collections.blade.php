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
@endphp

@extends('frontend.layouts.frontend-layout')

@section('title', __('Collections'))

@section('content')

    <section class="sec_main item_page">
        <div class="item_page_title_main">
            <div class="item_page_title">
                <div class="item_page_page">
                    <h2>{{ $cat_details[$name_key] }}</h2>
                </div>
                <div class="breadcrumb_main">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $cat_details[$name_key] }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <div class="item_page_desc">
                <p>{!! $cat_details[$description_key] !!}</p>
            </div>
        </div>
        <div class="product_main">
            <div class="row">
                {{-- <div class="col-md-3">
                    <div class="product_list_sidebar">
                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <strong>CATEGORIES</strong>
                                        <i class="fa-solid fa-minus"></i>
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show"
                                    aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <ul>
                                            @if(count($child_categories) > 0)
                                                @foreach ($child_categories as $category)
                                                    <li> <a class="{{ ($category['id'] == $cat_details['id']) ? 'active' : '' }}" href="{{ route('categories.collections',$category['id']) }}">{{ $category[$name_key] }}</a></li>
                                                @endforeach
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <div class="col-md-12">
                    <div class="product_list">
                        {{-- <div class="row justify-content-end mb-4">
                            <div class="col-md-3">
                                <div class="product_sort">
                                    <h5><span>Sort by:</span>Best Selling</h5>
                                    <div class="sortby">
                                        <ul>
                                            <li><span>Featured</span></li>
                                            <li class="active"><span>Best selling</span></li>
                                            <li><span>Alphabetically, A-Z</span></li>
                                            <li><span>Alphabetically, Z-A</span></li>
                                            <li><span>Price, low to high</span></li>
                                            <li><span>Price, high to low</span></li>
                                            <li><span>Date, old to new</span></li>
                                            <li><span>Date, new to old</span></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        @if(count($sub_categories) > 0)
                            <div class="collection_inr">
                                @foreach ($sub_categories as $sub_cat)
                                    @php
                                        $cat_img = (isset($sub_cat->categoryImages) && count($sub_cat->categoryImages) > 0) ? $sub_cat->categoryImages[0]->image : '';
                                    @endphp
                                    <div class="collction_box position-relative">
                                        <a href="{{ route('categories.collections',$sub_cat['id']) }}">
                                            <div class="collection_img">
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
                            </div>
                        @else
                            @if(count($items) > 0)
                                <div class="product_items">
                                    @foreach ($items as $item)
                                        @php
                                            $item_image = (isset($item->itemImages) && count($item->itemImages) > 0) ? $item->itemImages[0]->image : '';
                                            $item_price = (isset($item->itemPrices) && count($item->itemPrices) > 0) ? $item->itemPrices[0]->price : 0.00;
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
                            @else
                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <h3>Collections Not Found!</h3>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
