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
                                <li class="nav-item" id="{{ strtolower($parent_cat[$name_key]) }}_menu">
                                    <a class="nav-link" href="#">{{ $parent_cat[$name_key] }}</a>
                                </li>
                            @endforeach
                        @endif

                        {{-- <li class="nav-item" id="collection_menu">
                            <a class="nav-link" href="#">Collection</a>
                            <div class="collection_menu">
                                <ul>
                                    <li class="sub_list">
                                        <a href="#">Personalised Night lights</a>
                                        <div class="collection_sub_menu">
                                            <ul>
                                                <li>
                                                    <a href="" title="Novelty lights">Novelty lights</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Printed lights">Printed lights</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Monogram">Monogram</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="sub_list">
                                        <a href="#">Personalised Cake Topper</a>
                                        <div class="collection_sub_menu">
                                            <ul>
                                                <li>
                                                    <a href="" title="Birthday">Birthday</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Wedding/Engagement">Wedding/Engagement</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Printed toppers">Printed toppers</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Baby shower">Baby shower</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Cupcake toppers">Cupcake toppers</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="sub_list">
                                        <a href="#">Keyrings / Bagtags</a>

                                    </li>
                                    <li class="sub_list">
                                        <a href="#">Drinkware- Personalise</a>
                                        <div class="collection_sub_menu">
                                            <ul>
                                                <li>
                                                    <a href="" title="Personalised Steel Water bottles">Personalised Steel Water bottles</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Mugs">Mugs</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Tumblers">Tumblers</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="sub_list">
                                        <a href="#">Babies and Children</a>
                                        <div class="collection_sub_menu">
                                            <ul>
                                                <li>
                                                    <a href="" title="Baby announcement Plaque">Baby announcement Plaque</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Personalised Name/ Wall Plaque">Personalised Name/ Wall Plaque</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Baby shower">Baby shower</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Milestones">Milestones</a>
                                                </li>
                                                <li>
                                                    <a href="" title="Back to school">Back to school</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="sub_list"><a href="#">Wedding</a></li>
                                </ul>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="#">Occasion</a>
                        </li> --}}
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
