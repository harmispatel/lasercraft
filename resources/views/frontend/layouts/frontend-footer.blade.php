<footer class="footer">
    <div class="footer_inr">
        <div class="footer_top">
            {{-- <div class="newsletter_box">
                <h3>Subscribe to Our Newsletter</h3>
                <div class="newsletter_box_inr">
                    <input type="email" placeholder="Enter your email" class="form-control"/>
                    <i class="fa-solid fa-envelope icon"></i>
                </div>
            </div> --}}
            <div class="social_media_box">
                <h3>Follow Us</h3>
                <ul>
                    <li><a href="#"><i class="fa-brands fa-square-instagram"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-square-facebook"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-square-pinterest"></i></a></li>
                    <li><a href="#"><i class="fa-brands fa-square-youtube"></i></a></li>
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
                    <li><a href="">About Us</a></li>
                    <li><a href="">Contact Us</a></li>
                </ul>
            </div>
            <div class="footer_menu_inr">
                <h3>Customer care</h3>
                <ul>
                    <li><a href="">Shipping and Turnaround Times</a></li>
                    <li><a href="">FAQ</a></li>
                    <li><a href="">Privacy Policy</a></li>
                    <li><a href="">Refund Policy</a></li>
                    <li><a href="">Terms of Service</a></li>
                </ul>
            </div>
            <div class="footer_menu_inr text-center">
                <img src="{{ asset('public/frontend/image/logo.jpeg')}}" height="200">
            </div>
        </div>
    </div>
    <div class="footer_right">
        <p>© 2023 - Mahantam  </p>
    </div>
</footer>
