@php
    // UserDetails
    if (auth()->user())
    {
        $userID = encrypt(auth()->user()->id);
        $userName = auth()->user()->firstname." ".auth()->user()->lastname;
        $userImage = auth()->user()->image;
    }
    else
    {
        $userID = '';
        $userName = '';
        $userImage = '';
    }

    // Current Route Name
    $routeName = Route::currentRouteName();

    // Route Params
    $routeParams = Route::current()->parameters();

@endphp

<aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

        {{-- Dashboard Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'client.dashboard') ? 'active-tab' : '' }}" href="{{ route('client.dashboard') }}">
                <i class="fa-solid fa-house-chimney {{ ($routeName == 'client.dashboard') ? 'icon-tab' : '' }}"></i>
                <span>{{ __('Dashboard') }}</span>
            </a>
        </li>

        {{-- Design Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ (($routeName != 'design.general-info') && ($routeName != 'design.logo') && ($routeName != 'design.cover') && ($routeName != 'banners') && ($routeName != 'design.theme') && ($routeName != 'design.mail.forms') && $routeName != 'design.theme-preview' && $routeName != 'theme.clone') ? 'collapsed' : '' }} {{ (($routeName == 'design.general-info') || ($routeName == 'design.logo') || ($routeName == 'design.cover') || ($routeName == 'banners') || ($routeName == 'design.theme') || ($routeName == 'design.mail.forms') || $routeName == 'design.theme-preview' || $routeName == 'theme.clone') ? 'active-tab' : '' }}" data-bs-target="#design-nav" data-bs-toggle="collapse" href="#" aria-expanded="{{ (($routeName == 'design.general-info') || ($routeName == 'design.logo') || ($routeName == 'design.cover') || ($routeName == 'banners') || ($routeName == 'design.theme') || ($routeName == 'design.mail.forms') || $routeName == 'design.theme-preview' || $routeName == 'theme.clone') ? 'true' : 'false' }}">
                <i class="fa-solid fa-pen-nib {{ (($routeName == 'design.general-info') || ($routeName == 'design.logo') || ($routeName == 'design.cover') || ($routeName == 'banners') || ($routeName == 'design.theme') || ($routeName == 'design.mail.forms' || $routeName == 'design.theme-preview' || $routeName == 'theme.clone')) ? 'icon-tab' : '' }}"></i><span>{{ __('Design') }}</span><i class="bi bi-chevron-down ms-auto {{ (($routeName == 'design.general-info') || ($routeName == 'design.logo') || ($routeName == 'design.cover') || ($routeName == 'banners') || ($routeName == 'design.theme') || ($routeName == 'design.mail.forms') || $routeName == 'design.theme-preview' || $routeName == 'theme.clone') ? 'icon-tab' : '' }}"></i>
            </a>
            <ul id="design-nav" class="nav-content sidebar-ul collapse {{ (($routeName == 'design.general-info') || ($routeName == 'design.logo') || ($routeName == 'design.cover') || ($routeName == 'banners') || ($routeName == 'design.theme') || ($routeName == 'design.mail.forms') || $routeName == 'design.theme-preview' || $routeName == 'theme.clone') ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('design.general-info') }}" class="{{ ($routeName == 'design.general-info') ? 'active-link' : '' }}">
                        <span>{{ __('General Info') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('design.logo') }}" class="{{ ($routeName == 'design.logo') ? 'active-link' : '' }}">
                        <span>{{ __('Logo') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('design.cover') }}" class="{{ ($routeName == 'design.cover') ? 'active-link' : '' }}">
                        <span>{{ __('Cover') }}</span>
                    </a>
                </li>

                {{-- Banner --}}
                <li>
                    <a href="{{ route('banners') }}" class="{{ ($routeName == 'banners') ? 'active-link' : '' }}">
                        <span>{{ __('Banners') }}</span>
                    </a>
                </li>

                {{-- <li>
                    <a href="{{ route('design.theme') }}" class="{{ ($routeName == 'design.theme' || $routeName == 'design.theme-preview' || $routeName == 'theme.clone') ? 'active-link' : '' }}">
                        <span>{{ __('Themes') }}</span>
                    </a>
                </li> --}}

                <li>
                    <a href="{{ route('design.mail.forms') }}" class="{{ ($routeName == 'design.mail.forms') ? 'active-link' : '' }}">
                        <span>{{ __('Mail Forms') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Menu Nav --}}
        <li class="nav-item">
            {{-- && --}}
            <a class="nav-link {{ (($routeName != 'categories') && ($routeName != 'items') && ($routeName != 'tags') && ($routeName != 'options')) ? 'collapsed' : '' }} {{ (($routeName == 'categories') || ($routeName == 'items') || ($routeName == 'tags') || ($routeName == 'options')) ? 'active-tab' : '' }}" data-bs-target="#menu-nav" data-bs-toggle="collapse" href="#" aria-expanded="{{ (($routeName == 'categories') || ($routeName == 'items') || ($routeName == 'tags') || ($routeName == 'options')) ? 'true' : 'false' }}">
                <i class="fa-solid fa-bars {{ (($routeName == 'categories') || ($routeName == 'items') || ($routeName == 'tags')) ? 'icon-tab' : '' }}"></i><span>{{ __('Catalogue') }}</span><i class="bi bi-chevron-down ms-auto {{ (($routeName == 'categories') || ($routeName == 'items') || ($routeName == 'tags') || ($routeName == 'options')) ? 'icon-tab' : '' }}"></i>
            </a>
            <ul id="menu-nav" class="nav-content sidebar-ul collapse {{ (($routeName == 'categories') || ($routeName == 'items') || ($routeName == 'tags') || ($routeName == 'options')) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('categories') }}" class="{{ (($routeName == 'categories') &&  count($routeParams) == 0) ? 'active-link' : '' }}">
                        <span>{{ __('Categories') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('items') }}" class="{{ ($routeName == 'items') ? 'active-link' : '' }}">
                        <span>{{ __('Items') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('categories','page') }}" class="{{ (($routeName == 'categories') && (isset($routeParams['cat_id']) && $routeParams['cat_id'] == 'page')) ? 'active-link' : '' }}">
                        <span>{{ __('Pages') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('categories','link') }}" class="{{ (($routeName == 'categories') && (isset($routeParams['cat_id']) && $routeParams['cat_id'] == 'link')) ? 'active-link' : '' }}">
                        <span>{{ __('Links') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('categories','gallery') }}" class="{{ (($routeName == 'categories') && (isset($routeParams['cat_id']) && $routeParams['cat_id'] == 'gallery')) ? 'active-link' : '' }}">
                        <span>{{ __('Galleries') }}</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('categories','check_in') }}" class="{{ (($routeName == 'categories') && (isset($routeParams['cat_id']) && $routeParams['cat_id'] == 'check_in')) ? 'active-link' : '' }}">
                        <span>{{ __('Contact US') }}</span>
                    </a>
                </li>


                <li>
                    <a href="{{ route('categories','pdf_page') }}" class="{{ (($routeName == 'categories') && (isset($routeParams['cat_id']) && $routeParams['cat_id'] == 'pdf_page')) ? 'active-link' : '' }}">
                        <span>{{ __('PDF') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('tags') }}" class="{{ ($routeName == 'tags') ? 'active-link' : '' }}">
                        <span>{{ __('Tags') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('options') }}" class="{{ ($routeName == 'options') ? 'active-link' : '' }}">
                        <span>{{ __('Order Attributes') }}</span>
                    </a>
                </li>
            </ul>
        </li>

        {{-- Orders Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ (($routeName != 'order.settings') && ($routeName != 'client.orders') && ($routeName != 'client.orders.history') && ($routeName != 'view.order') && ($routeName != 'payment.settings')) ? 'collapsed' : '' }} {{ (($routeName == 'order.settings') || ($routeName == 'client.orders') || ($routeName == 'view.order') || ($routeName == 'payment.settings') || ($routeName == 'client.orders.history')) ? 'active-tab' : '' }}" data-bs-target="#orders-nav" data-bs-toggle="collapse" href="#" aria-expanded="{{ (($routeName == 'order.settings') || ($routeName == 'client.orders') || ($routeName == 'view.order') || ($routeName == 'payment.settings') || ($routeName == 'client.orders.history')) ? 'true' : 'false' }}">
                <i class="bi bi-cart-check {{ (($routeName == 'order.settings') || ($routeName == 'client.orders') || ($routeName == 'view.order') || ($routeName == 'payment.settings') || ($routeName == 'client.orders.history')) ? 'icon-tab' : '' }}"></i><span>{{ __('Orders') }}</span><i class="bi bi-chevron-down ms-auto {{ (($routeName == 'order.settings') || ($routeName == 'client.orders') || ($routeName == 'payment.settings') || ($routeName == 'client.orders.history')) ? 'icon-tab' : '' }}"></i>
            </a>
            <ul id="orders-nav" class="nav-content sidebar-ul collapse {{ (($routeName == 'order.settings') || ($routeName == 'client.orders') || ($routeName == 'view.order') || ($routeName == 'payment.settings') || ($routeName == 'client.orders.history')) ? 'show' : '' }}" data-bs-parent="#sidebar-nav">
                <li>
                    <a href="{{ route('client.orders') }}" class="{{ (($routeName == 'client.orders')) ? 'active-link' : '' }}">
                        <span>{{ __('Pending Orders') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('client.orders.history') }}" class="{{ (($routeName == 'client.orders.history') || ($routeName == 'view.order')) ? 'active-link' : '' }}">
                        <span>{{ __('Orders History') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('order.settings') }}" class="{{ (($routeName == 'order.settings') &&  count($routeParams) == 0) ? 'active-link' : '' }}">
                        <span>{{ __('Order Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('payment.settings') }}" class="{{ (($routeName == 'payment.settings')) ? 'active-link' : '' }}">
                        <span>{{ __('Payment Settings') }}</span>
                    </a>
                </li>
            </ul>
        </li>


        {{-- Languages Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'languages') ? 'active-tab' : '' }}" href="{{ route('languages') }}">
                <i class="bi bi-translate {{ ($routeName == 'languages') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Languages') }}</span>
            </a>
        </li>


        {{-- Statistics Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'statistics') ? 'active-tab' : '' }}" href="{{ route('statistics') }}">
                <i class="fa-solid fa-chart-line {{ ($routeName == 'statistics') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Statistics') }}</span>
            </a>
        </li>

         {{-- Customers --}}
         <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'customers') ? 'active-tab' : '' }}" href="{{ route('customers') }}">
                <i class="fa-solid fa-user {{ ($routeName == 'customers') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Customers') }}</span>
            </a>
        </li>

        {{-- Reviews Nav --}}
        <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'items.reviews') ? 'active-tab' : '' }}" href="{{ route('items.reviews') }}">
                <i class="fa-solid fa-comments {{ ($routeName == 'items.reviews') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Item Reviews') }}</span>
            </a>
        </li>

        {{-- Customer Quotes --}}
        <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'customer.quotes') ? 'active-tab' : '' }}" href="{{ route('customer.quotes') }}">
                <i class="fa-solid fa-quote-left {{ ($routeName == 'customer.quotes') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Customer Quotes') }}</span>
            </a>
        </li>

        {{-- Schedule Nav --}}
        {{-- <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'shop.schedule') ? 'active-tab' : '' }}" href="{{ route('shop.schedule')}}">
                <i class="bi bi-clock {{ ($routeName == 'shop.schedule') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Schedule') }}</span>
            </a>
        </li> --}}

        {{-- Contact Nav --}}
        {{-- <li class="nav-item">
            <a class="nav-link {{ ($routeName == 'contact') ? 'active-tab' : '' }}" href="{{ route('contact') }}">
                <i class="fa-solid fa-address-card {{ ($routeName == 'contact') ? 'icon-tab' : '' }}"></i>
            <span>{{ __('Contact') }}</span>
            </a>
        </li> --}}

        {{-- Logout Nav --}}
        <li class="nav-item">
            <a class="nav-link" href="{{ route('logout') }}">
                <i class="bi bi-box-arrow-right"></i>
            <span>{{ __('Logout') }}</span>
            </a>
        </li>

    </ul>
</aside>
