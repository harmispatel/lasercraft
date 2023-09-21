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

    $parent_categories = \App\Models\Category::where('parent_id',NULL)->where('category_type','product_category')->where('published',1)->get();

    $dynamic_pages = \App\Models\Category::where('category_type','page')->where('published',1)->get();

    $client_settings = getClientSettings();
@endphp


<header class="header">
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-body-tertiary">
            <div class="header_inner">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="{{ route('home') }}">Home</a>
                        </li>

                        @if(count($parent_categories) > 0)
                            @foreach ($parent_categories as $parent_cat)
                                <li class="nav-item dropdown">
                                    @php
                                        $menu_count = 1;
                                    @endphp
                                    <a class="nav-link dropdown-toggle" href="{{ route('categories.collections',$parent_cat['id']) }}" data-bs-toggle="dropdown">{{ $parent_cat[$name_key] }}</a>

                                    @if(count($parent_cat->subcategories) > 0)
                                        @include('frontend.child_categories_menu',['subcategories' => $parent_cat->subcategories,'parent_key'=>$menu_count])
                                    @endif
                                </li>
                            @endforeach
                        @endif

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="" data-bs-toggle="dropdown">Custom PRINTS</a>
                            <ul class="dropdown-menu">
                                @if(count($dynamic_pages) > 0)
                                    @foreach ($dynamic_pages as $dpage)
                                        <li><a class="dropdown-item" href="{{ route('prints.page',encrypt($dpage['id'])) }}">{{ $dpage[$name_key] }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </li>

                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact.us') }}">Contact US</a>
                        </li> --}}

                        {{-- <li class="nav-item">
                            <a class="nav-link" href="#">Shop</a>
                        </li> --}}
                    </ul>
                </div>
                <a class="navbar-brand m-0" href="{{ route('home') }}">
                    @if(isset($client_settings['shop_view_header_logo']) && !empty($client_settings['shop_view_header_logo']) && file_exists('public/client_uploads/top_logos/'.$client_settings['shop_view_header_logo']))
                        <img src="{{ asset('public/client_uploads/top_logos/'.$client_settings['shop_view_header_logo']) }}" height="80">
                    @else
                        <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" height="80">
                    @endif
                </a>
                <div class="header_right">
                    <ul>
                        <li><a class="icon" data-bs-toggle="modal" data-bs-target="#globalSearchModal"><i class="fa-solid fa-search"></i></a></li>

                        @if(Auth::user() && Auth::user()->user_type == 3)
                            <li>
                                <div class="login_user">
                                    <div class="user_name">
                                        @if(!empty(Auth::user()->image) && file_exists('public/admin_uploads/users/'.Auth::user()->image))
                                            <img src="{{ asset('public/admin_uploads/users/'.Auth::user()->image) }}" width="40px" height="40px" style="border-radius: 50%">
                                        @else
                                            <img src="{{ asset('public/admin_images/demo_images/profiles/profile1.jpg') }}" width="40px" height="40px" style="border-radius: 50%">
                                        @endif
                                        <h3>{{ Auth::user()->firstname }} {{ Auth::user()->lastname }}</h3>
                                    </div>
                                    <div class="user_dropdown">
                                        <ul>
                                            <li><a href="#">Dashboard</a></li>
                                            <li><a href="{{ route('logout') }}">Logout</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        @else
                            <li>
                                <a class="icon" href="{{ route('login') }}"><i class="fa-solid fa-user"></i></a>
                            </li>
                        @endif
                        <li class="position-relative"><a class="icon" href="{{ route('cart.list') }}"><i class="fa-solid fa-bag-shopping"></i></a> <span class="cart-qty">{{ Cart::getTotalQuantity()}}</span></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
