@php
    $shop_settings = getClientSettings();

    // Shop Currency
    $currency = (isset($shop_settings['default_currency']) && !empty($shop_settings['default_currency'])) ? $shop_settings['default_currency'] : 'USD';
@endphp

@extends('client.layouts.client-layout')

@section('title', __('Customer Quotes'))

@section('content')

    {{-- Quote Details Modal --}}
    <div class="modal fade" id="quoteDetailsModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="quoteDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quoteDetailsModalLabel">Customer Quote Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                </div>
            </div>
        </div>
    </div>

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>{{ __('Customer Quotes')}}</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Customer Quotes') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Customer Quotes Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="quotesTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Time') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($customer_quotes as $customer_quote)
                                        <tr>
                                            <td>{{ $customer_quote->firstname; }} {{ $customer_quote->lastname; }}</td>
                                            <td>{{ $customer_quote->email; }}</td>
                                            <td>{{ $customer_quote->created_at->diffForHumans() }}</td>
                                            <td>
                                                <a onclick="QuoteDetails({{ $customer_quote->id }})" class="btn btn-sm btn-primary"><i class="bi bi-eye"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

@endsection


{{-- Custom Script --}}
@section('page-js')
    <script type="text/javascript">

        $('#quotesTable').DataTable({
            "ordering": false
        });

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": 4000
        }

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif

        @if (Session::has('error'))
            toastr.error('{{ Session::get('error') }}')
        @endif


        // Function for get Quote Details
        function QuoteDetails(quoteID)
        {
            $('#quoteDetailsModal .modal-body').html('');

            $.ajax({
                type: "POST",
                url: "{{ route('customer.quote.details') }}",
                data: {
                    "_token" : "{{ csrf_token() }}",
                    "quote_id" : quoteID,
                },
                dataType: "JSON",
                success: function (response) {
                    if(response.success == 1){
                        $('#quoteDetailsModal .modal-body').append(response.data);
                        $('#quoteDetailsModal').modal('show');
                    }else{
                        toastr.error(response.message);
                    }
                }
            });
        }

        // Reset Modal when Close Quote Details Modal
        $('#quoteDetailsModal .btn-close').on('click',function(){
            $('#quoteDetailsModal .modal-body').html('');
        });

        // Function for Send Quote Reply
        function quoteReply(){
            var myFormData = new FormData(document.getElementById('quoteReplyForm'));

            // Clear all Toastr Messages
            toastr.clear();

            $.ajax({
                type: "POST",
                url: "{{ route('customer.quote.reply') }}",
                data: myFormData,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                beforeSend: function(){
                    $('#btn-quote-reply').hide();
                    $('#load-btn-quote-reply').show();
                },
                success: function (response){
                    if(response.success == 1){
                        $('#btn-quote-reply').show();
                        $('#load-btn-quote-reply').hide();
                        $('#quoteDetailsModal #quoteReplyForm').trigger("reset");
                        toastr.success(response.message);
                    }else{
                        toastr.error(response.message);
                        $('#quoteDetailsModal .modal-body').html('');
                        $('#quoteDetailsModal').modal('hide');
                    }
                },
                error: function(response){
                    if(response.responseJSON?.errors){
                        $('#btn-quote-reply').show();
                        $('#load-btn-quote-reply').hide();

                        $.each(response.responseJSON.errors, function (i, error){
                            toastr.error(error);
                        });
                    }
                }
            });
        };


    </script>
@endsection