<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    {{-- css --}}
    @include('frontend.layouts.frontend-css')
</head>

<body>

    {{-- Header --}}
    @include('frontend.layouts.frontend-header')

    {{-- Main content --}}
    <main id="main" class="main">
        @yield('content')
    </main>


    {{-- Footer --}}
    @include('frontend.layouts.frontend-footer')

    {{-- js --}}
    @include('frontend.layouts.frontend-js')

    @yield('page-js')


</body>

</html>
