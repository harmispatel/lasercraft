<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Smart QR SCAN</title>
    <link href="{{ asset('public/admin_images/favicons/smartqrscan.ico') }}" rel="icon">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,400;0,500;0,700;1,500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/admin/assets/vendor/css/toastr.min.css') }}">
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" rel="stylesheet">-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/css/swiper.min.css" rel="stylesheet">
    <!--<link href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" rel="stylesheet">-->
    <link rel="stylesheet" href="{{ asset('public/frontend/css/frontend.css') }}" >
</head>
<body>
    <!-- Header -->
    @include('frontend.frontend-header')

    <!-- contact banner -->
    <section class="contact_banner">
        <h2>Register Shop for Trial</h2>
    </section>

    <div class="bg_login" style="background: #cdd6e9;">
        <div class="container">
            <section class="section register align-items-center justify-content-center py-4">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 col-md-6 d-flex flex-column align-items-center justify-content-center">

                            <div class="card mb-3">
                                <div class="card-body">
                                    <div class="pt-4 pb-2">
                                        <h5 class="card-title text-center pb-0 fs-4">Register Your Shop</h5>
                                        <p class="text-center small">Please Enter Valid Details for Register Your Shop</p>
                                    </div>

                                    <form class="row g-3" method="POST" action="{{ route('frontend.register.shop') }}" enctype="multipart/form-data">
                                        @csrf
                                        <div class="col-6">
                                            <label for="firstname" class="form-label">FirstName <span class="text-danger">*</span></label>
                                            <input type="text" name="firstname" class="form-control {{ ($errors->has('firstname')) ? 'is-invalid' : '' }}" id="firstname" value="{{ old('firstname') }}">
                                            @if($errors->has('firstname'))
                                                <div class="invalid-feedback">{{ $errors->first('firstname') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="lastname" class="form-label">LastName</label>
                                            <input type="text" name="lastname" class="form-control" id="lastname" value="{{ old('lastname') }}">
                                        </div>
                                        <div class="col-6">
                                            <label for="shop_name" class="form-label">Shop Name <span class="text-danger">*</span></label>
                                            <input type="text" name="shop_name" class="form-control {{ ($errors->has('shop_name')) ? 'is-invalid' : '' }}" id="shop_name" value="{{ old('shop_name') }}">
                                            @if($errors->has('shop_name'))
                                                <div class="invalid-feedback">{{ $errors->first('shop_name') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="text" name="email" class="form-control {{ ($errors->has('email')) ? 'is-invalid' : '' }}" id="email" value="{{ old('email') }}">
                                            @if($errors->has('email'))
                                                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="mobile_number" class="form-label">Mobile No. <span class="text-danger">*</span></label>
                                            <input type="text" name="mobile_number" class="form-control {{ ($errors->has('mobile_number')) ? 'is-invalid' : '' }}" id="mobile_number" value="{{ old('mobile_number') }}" maxlength="10">
                                            @if($errors->has('mobile_number'))
                                                <div class="invalid-feedback">{{ $errors->first('mobile_number') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="city" class="form-label">City <span class="text-danger">*</span></label>
                                            <input type="text" name="city" class="form-control {{ ($errors->has('city')) ? 'is-invalid' : '' }}" id="city" value="{{ old('city') }}">
                                            @if($errors->has('city'))
                                                <div class="invalid-feedback">{{ $errors->first('city') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                            <select name="country" id="country" class="form-select {{ ($errors->has('country')) ? 'is-invalid' : '' }}">
                                                <option value="">Choose Your Country</option>
                                                @if(count($countries) > 0)
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}" {{ (old('country') == $country->id) ? 'selected' : '' }}>{{ $country->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if($errors->has('country'))
                                                <div class="invalid-feedback">{{ $errors->first('country') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="subscription" class="form-label">Subscription <span class="text-danger">*</span></label>
                                            <select name="subscription" id="subscription" class="form-select {{ ($errors->has('subscription')) ? 'is-invalid' : '' }}">
                                                @if(count($subscriptions) > 0)
                                                    @foreach ($subscriptions as $subscription)
                                                        <option value="{{ $subscription->id }}">{{ $subscription->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @if($errors->has('subscription'))
                                                <div class="invalid-feedback">{{ $errors->first('subscription') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="pincode" class="form-label">Pincode <span class="text-danger">*</span></label>
                                            <input type="text" name="pincode" class="form-control {{ ($errors->has('pincode')) ? 'is-invalid' : '' }}" id="pincode" value="{{ old('pincode') }}">
                                            @if($errors->has('pincode'))
                                                <div class="invalid-feedback">{{ $errors->first('pincode') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="catalogue" class="form-label">Upload Catalogue / Menu</label>
                                            <input type="file" name="catalogue" id="catalogue" class="form-control {{ ($errors->has('catalogue')) ? 'is-invalid' : '' }}">
                                            @if($errors->has('catalogue'))
                                                <div class="invalid-feedback">{{ $errors->first('catalogue') }}</div>
                                            @endif
                                            <code>Note : Supported DOC. (PDF, CSV)</code>
                                        </div>
                                        <div class="col-6">
                                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                            <input type="password" name="password" class="form-control {{ ($errors->has('password')) ? 'is-invalid' : '' }}" id="password">
                                            @if($errors->has('password'))
                                                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-6">
                                            <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                            <input type="password" name="confirm_password" class="form-control {{ ($errors->has('confirm_password')) ? 'is-invalid' : '' }}" id="confirm_password">
                                            @if($errors->has('confirm_password'))
                                                <div class="invalid-feedback">{{ $errors->first('confirm_password') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <label for="shop_url" class="form-label">Shop URL <span class="text-danger">*</span></label>
                                            <input type="text" name="shop_url" class="form-control {{ ($errors->has('shop_url')) ? 'is-invalid' : '' }}" id="shop_url" value="{{ old('shop_url') }}">
                                            @if($errors->has('shop_url'))
                                                <div class="invalid-feedback">{{ $errors->first('shop_url') }}</div>
                                            @endif
                                            <code>Note : Enter Shop Url in Only String & Allowed hyphen symbol. (Ex-: demo-restaurant, smartqr-shop, demo-rest-shop)</code>
                                        </div>
                                        <div class="col-12">
                                            <label for="address" class="form-label">Shop Address <span class="text-danger">*</span></label>
                                            <textarea name="address" id="address" rows="3" class="form-control {{ ($errors->has('address')) ? 'is-invalid' : '' }}">{{ old('address') }}</textarea>
                                            @if($errors->has('address'))
                                                <div class="invalid-feedback">{{ $errors->first('address') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-12">
                                            <label>Complete Below Captcha Task</label>
                                            <h2><code>{{ $number1 }} {{ $operator }} {{ $number2 }} = ?</code></h2>
                                            <input type="number" name="captcha_response" id="captcha_response" class="form-control {{ ($errors->has('captcha_response')) ? 'is-invalid' : '' }}">
                                            @if($errors->has('captcha_response'))
                                                <div class="invalid-feedback">{{ $errors->first('captcha_response') }}</div>
                                            @endif
                                        </div>
                                        <div class="col-12 text-center">
                                            <button class="btn btn-primary w-25" type="submit">Register</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </section>

        </div>
    </div>

    {{-- Footer --}}
    @include('frontend.frontend-footer')

    <script src="{{ asset('public/client/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('public/admin/assets/vendor/js/toastr.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Swiper/3.4.1/js/swiper.min.js"></script>

    <script type="text/javascript">

        // Toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            timeOut: 4000
        }

        // Error Messages
        @if (Session::has('error'))
            toastr.error('{{ Session::get('error') }}')
        @endif

        // Success Message
        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

    </script>
</body>
</html>
