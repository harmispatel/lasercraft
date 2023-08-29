<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link href="{{ asset('public/admin_images/favicons/smartqrscan.ico') }}" rel="icon">
    @include('client.layouts.client-css')
</head>

@php
    $order_settings = getOrderSettings();
    $play_sound = (isset($order_settings['play_sound']) && !empty($order_settings['play_sound'])) ? $order_settings['play_sound'] : 0;
    $notification_sound = (isset($order_settings['notification_sound']) && !empty($order_settings['notification_sound'])) ? $order_settings['notification_sound'] : 'buzzer-01.mp3';
@endphp

<body>

    <input type="hidden" name="play_sound" id="play_sound" value="{{ $play_sound }}">
    <input type="hidden" name="notification_sound" id="notification_sound" value="{{ asset('public/admin/assets/audios/'.$notification_sound) }}">


    {{-- Navbar --}}
    @include('client.layouts.client-navbar')

    {{-- Sidebar --}}
    @include('client.layouts.client-sidebar')

    {{-- Main Content --}}
    <main id="main" class="main">
        @yield('content')
    </main>
    <!-- End #main -->

    {{-- Footer --}}
    @include('client.layouts.client-footer')

    {{-- Uplink --}}
    <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    {{-- Client JS --}}
    @include('client.layouts.client-js')

    @yield('page-js')

</body>

</html>
