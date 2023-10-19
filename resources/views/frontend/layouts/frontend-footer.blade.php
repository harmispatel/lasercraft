@php
    $current_year = \Carbon\Carbon::now()->format('Y');
    $copyright_text = isset($client_settings['homepage_intro']) ? $client_settings['homepage_intro'] : '';
    $copyright_text = str_replace('[year]',$current_year,$copyright_text);

    $custom_pages = App\Models\CustomPage::where('status', 1)->get();
@endphp

<footer class="footer">
    <div class="footer_inr">
        <div class="footer_top">
            <div class="social_media_box">
                <h3>Follow Us</h3>
                <ul>
                    @if(isset($client_settings['instagram_link']) && !empty($client_settings['instagram_link']))
                        <li><a target="_blank" href="{{ $client_settings['instagram_link'] }}"><i class="fa-brands fa-square-instagram"></i></a></li>
                    @endif

                    @if(isset($client_settings['facebook_link']) && !empty($client_settings['facebook_link']))
                        <li><a target="_blank" href="{{ $client_settings['facebook_link'] }}"><i class="fa-brands fa-square-facebook"></i></a></li>
                    @endif

                    @if(isset($client_settings['pinterest_link']) && !empty($client_settings['pinterest_link']))
                        <li><a target="_blank" href="{{ $client_settings['pinterest_link'] }}"><i class="fa-brands fa-square-pinterest"></i></a></li>
                    @endif

                    @if(isset($client_settings['twitter_link']) && !empty($client_settings['twitter_link']))
                        <li><a target="_blank" href="{{ $client_settings['twitter_link'] }}"><i class="fa-brands fa-square-twitter"></i></a></li>
                    @endif

                    @if(isset($client_settings['youtube_link']) && !empty($client_settings['youtube_link']))
                        <li><a target="_blank" href="{{ $client_settings['youtube_link'] }}"><i class="fa-brands fa-square-youtube"></i></a></li>
                    @endif

                    @if(isset($client_settings['map_url']) && !empty($client_settings['map_url']))
                        <li><a target="_blank" href="{{ $client_settings['map_url'] }}"><i class="fa-solid fa-location-dot"></i></a></li>
                    @endif

                    @if(isset($client_settings['business_telephone']) && !empty($client_settings['business_telephone']))
                        <li><a target="_blank" href="tel:{{ $client_settings['business_telephone'] }}"><i class="fa-solid fa-phone"></i></a></li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="footer_menu">
            <div class="footer_menu_inr">
                <h3>Collections</h3>
                <ul>
                    @if(count($child_categories) > 0)
                        @foreach ($child_categories as $child_key => $child_cat)
                            @if($child_key > 7)
                                @php
                                    break;
                                @endphp
                            @endif
                            <li><a href="{{ route('categories.collections',$child_cat['id']) }}">{{ $child_cat[$name_key] }}</a></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <div class="footer_menu_inr">
                <h3>Quick LInks</h3>
                <ul>
                    <li><a href="{{ route('home') }}">Home</a></li>
                    <li><a href="{{ route('contact.us') }}">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer_menu_inr">
                <h3>Customer care</h3>
                <ul>
                    @if(count($custom_pages) > 0)
                        @foreach ($custom_pages as $custom_page)
                            <li><a href="{{ route('custom.page.view',$custom_page['page_slug']) }}">{{ $custom_page['name'] }}</a></li>
                        @endforeach
                    @endif
                    {{-- <li><a href="">Shipping and Turnaround Times</a></li>
                    <li><a href="">FAQ</a></li>
                    <li><a href="">Privacy Policy</a></li>
                    <li><a href="">Refund Policy</a></li>
                    <li><a href="">Terms of Service</a></li> --}}
                </ul>
            </div>
            <div class="footer_menu_inr text-center">
                <a href="{{ route('home') }}">
                    @if(isset($client_settings['shop_view_header_logo']) && !empty($client_settings['shop_view_header_logo']) && file_exists('public/client_uploads/top_logos/'.$client_settings['shop_view_header_logo']))
                        <img src="{{ asset('public/client_uploads/top_logos/'.$client_settings['shop_view_header_logo']) }}" width="180">
                    @else
                        <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" width="150">
                    @endif
                </a>
            </div>
        </div>
    </div>
    <div class="footer_right text-center text-white">
        {!! $copyright_text !!}
    </div>
</footer>
