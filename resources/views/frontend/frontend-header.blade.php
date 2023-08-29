<!-- Header -->
<header class="header bg-white">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="{{ URL::to('/') }}"><img src="{{ asset('public/admin_images/logos/smart_qr_logo.gif') }}" height="80px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
              <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link {{ (Route::currentRouteName() == 'home') ? 'active' : '' }}" aria-current="page" href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (Route::currentRouteName() == 'qr.guide') ? 'active' : '' }}" aria-current="page" href="{{ route('qr.guide') }}">QR Guide</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (Route::currentRouteName() == 'qr.menu') ? 'active' : '' }}" aria-current="page" href="{{ route('qr.menu') }}">QR Menu</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ (Route::currentRouteName() == 'contact.us') ? 'active' : '' }}" aria-current="page" href="{{ route('contact.us') }}">Contact</a>
                </li>
               <li class="m-1">
                    <a href="{{ route('login') }}" class="btn text-white" style="background-color:#2498bd; margin-left:10px;"><i class="fa fa-user"></i> <strong>Login</strong></a>
               </li>
               <li class="m-1">
                    <a href="{{ route('signup.trial') }}" class="btn text-white" style="background-color:#2498bd; margin-left:10px;"><i class="fa fa-user"></i> <strong>Get Started Now</strong></a>
               </li>
              </ul>
            </div>
        </nav>
    </div>
</header>
