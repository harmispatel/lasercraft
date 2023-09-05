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

    {{-- Search Model --}}
    <div class="modal fade" id="globalSearchModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="globalSearchModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-body" style="background: #fffbf9">
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-md-6 text-center">
                                <h4>What are You Looking for ?</h3>
                                <button type="button" class="btn-close cls-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="col-md-8 mt-2">
                                <div class="input-group rounded">
                                    <input type="search" class="form-control rounded" placeholder="Search" aria-label="Search" aria-describedby="search-addon" name="search" id="search" />
                                  </div>
                            </div>
                        </div>
                        <div class="row mt-4" id="item_preview_div"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
