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

    $parent_categories = \App\Models\Category::where('parent_id',NULL)->get();
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
                                <li class="nav-item dropdown" id="myDropdown">
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

                        <li class="nav-item">
                            <a class="nav-link" href="#">Shop</a>
                        </li>
                    </ul>
                </div>
                <a class="navbar-brand m-0" href="{{ route('home') }}">
                    <img src="{{asset('public/frontend/image/logo.jpeg')}}" height="60" />
                </a>
                <div class="header_right">
                    <div class="search_box position-relative">
                        <input class="form-control" placeholder="search..." />
                        <i class="fa-solid fa-search src_icon"></i>
                    </div>
                    <ul>
                        <li><a class="icon" href="#"><i class="fa-solid fa-user"></i></a></li>
                        <li><a class="icon" href="#"><i class="fa-solid fa-bag-shopping"></i></a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </div>
</header>
