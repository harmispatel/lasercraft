@extends('client.layouts.client-layout')

@section('title',__('New Building'))

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>{{ __('Buildings')}}</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">{{ __('Dashboard')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('buildings') }}">{{ __('Buildings')}}</a></li>
                        <li class="breadcrumb-item active">{{ __('New Building')}}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4" style="text-align: right;">
                <a href="{{ route('buildings') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- New Building add Section --}}
    <section class="section dashboard">
        <div class="row">

            {{-- Buildings Card --}}
            <div class="col-md-12">
                <div class="card">
                    <form class="form" action="{{ route('buildings.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                        <div class="card-body">
                            <div class="card-title">
                            </div>
                            <div class="container">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label for="building_name" class="form-label">Building Name</label>
                                        <input type="text" name="building_name" id="building_name" class="form-control {{ ($errors->has('building_name')) ? 'is-invalid' : '' }}" value="{{ old('building_name') }}">
                                        @if($errors->has('building_name'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('building_name') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button class="btn btn-success">{{ __('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

@endsection

{{-- Custom JS --}}
@section('page-js')
    <script type="text/javascript">

    </script>
@endsection
