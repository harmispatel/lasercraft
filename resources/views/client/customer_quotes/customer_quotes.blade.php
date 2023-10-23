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

                        // Intialized Text Editor
                        CKEDITOR.ClassicEditor.create(document.getElementById("message"),
                        {
                            toolbar: {
                                items: [
                                    'heading', '|',
                                    'bold', 'italic', 'strikethrough', 'underline', 'code', 'subscript', 'superscript', 'removeFormat', '|',
                                    'bulletedList', 'numberedList', 'todoList', '|',
                                    'outdent', 'indent', '|',
                                    'undo', 'redo',
                                    '-',
                                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'highlight', '|',
                                    'alignment', '|',
                                    'link', 'insertImage', 'blockQuote', 'insertTable', 'mediaEmbed', 'codeBlock', 'htmlEmbed', '|',
                                    'specialCharacters', 'horizontalLine', 'pageBreak', '|',
                                    'sourceEditing'
                                ],
                                shouldNotGroupWhenFull: true
                            },
                            list: {
                                properties: {
                                    styles: true,
                                    startIndex: true,
                                    reversed: true
                                }
                            },
                            'height':500,
                            fontSize: {
                                options: [ 10, 12, 14, 'default', 18, 20, 22 ],
                                supportAllValues: true
                            },
                            htmlSupport: {
                                allow: [
                                    {
                                        name: /.*/,
                                        attributes: true,
                                        classes: true,
                                        styles: true
                                    }
                                ]
                            },
                            htmlEmbed: {
                                showPreviews: true
                            },
                            link: {
                                decorators: {
                                    addTargetToExternalLinks: true,
                                    defaultProtocol: 'https://',
                                    toggleDownloadable: {
                                        mode: 'manual',
                                        label: 'Downloadable',
                                        attributes: {
                                            download: 'file'
                                        }
                                    }
                                }
                            },
                            mention: {
                                feeds: [
                                    {
                                        marker: '@',
                                        feed: [
                                            '@apple', '@bears', '@brownie', '@cake', '@cake', '@candy', '@canes', '@chocolate', '@cookie', '@cotton', '@cream',
                                            '@cupcake', '@danish', '@donut', '@dragée', '@fruitcake', '@gingerbread', '@gummi', '@ice', '@jelly-o',
                                            '@liquorice', '@macaroon', '@marzipan', '@oat', '@pie', '@plum', '@pudding', '@sesame', '@snaps', '@soufflé',
                                            '@sugar', '@sweet', '@topping', '@wafer'
                                        ],
                                        minimumCharacters: 1
                                    }
                                ]
                            },
                            removePlugins: [
                                'CKBox',
                                'CKFinder',
                                'EasyImage',
                                'RealTimeCollaborativeComments',
                                'RealTimeCollaborativeTrackChanges',
                                'RealTimeCollaborativeRevisionHistory',
                                'PresenceList',
                                'Comments',
                                'TrackChanges',
                                'TrackChangesData',
                                'RevisionHistory',
                                'Pagination',
                                'WProofreader',
                                'MathType'
                            ]
                        });

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
                        $('#quoteDetailsModal #quoteReplyForm .item_child').remove();
                        $('#quoteDetailsModal .invoices_div').html('');
                        $('#quoteDetailsModal .invoices_div').append(response.data);
                        $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').html('');
                        $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').append(response.reset_form);
                        toastr.success(response.message);
                    }else{
                        toastr.error(response.message);
                        $('#quoteDetailsModal .modal-body').html('');
                        $('#quoteDetailsModal').modal('hide');
                        $('#quoteDetailsModal #quoteReplyForm').trigger("reset");
                        $('#quoteDetailsModal #quoteReplyForm .item_child').remove();
                        $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').html('');
                        $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').append(response.reset_form);
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

        // Function for Add New Item & Price
        function AddItemPrice(){
            var count = $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').children('.item_price_div').length;
            var html = "";
            count ++;

            html += '<div class="row item_child item_price_div item_price_div_'+count+' mb-3">';
                html += '<div class="col-md-5">';
                    html += '<input type="text" name="price[item][]" class="form-control" placeholder="Enter Item Name">';
                html += '</div>';
                html += '<div class="col-md-2">';
                    html += '<input type="number" name="price[qty][]" class="form-control" value="1">';
                html += '</div>';
                html += '<div class="col-md-2">';
                    html += '<input type="number" name="price[price][]" class="form-control" value="0">';
                html += '</div>';
                html += '<div class="col-md-2">';
                    html += '<input type="number" name="price[discount][]" class="form-control" value="0">';
                html += '</div>';
                html += '<div class="col-md-1">';
                    html += '<a onclick="$(\'.item_price_div_'+count+'\').remove()" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>';
                html += '</div>';
            html += '</div>';

            $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').append(html);
        }

        // Function for Edit Quote Reply
        function editQuoteReply(quoteReplyID){
            $.ajax({
                type: "POST",
                url: "{{ route('customer.quote.reply.edit') }}",
                data: {
                    "_token" : "{{ csrf_token() }}",
                    "quote_reply_id" : quoteReplyID,
                },
                dataType: "JSON",
                success: function (response) {
                    if(response.success == 1){
                        $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').html('');
                        $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').append(response.data);
                    }else{
                        toastr.error(response.message);
                    }
                }
            });
        }

        // Reset Customer Quote Reply Form
        function resetQuoteReplyForm(){
            var reset_form = '';
            reset_form += '<div class="row mb-3">';
                reset_form += '<div class="col-md-5">';
                    reset_form += '<strong>Item Name</strong>';
                reset_form += '</div>';
                reset_form += '<div class="col-md-2">';
                    reset_form += '<strong>Qty.</strong>';
                reset_form += '</div>';
                reset_form += '<div class="col-md-2">';
                    reset_form += '<strong>Price</strong>';
                reset_form += '</div>';
                reset_form += '<div class="col-md-2">';
                    reset_form += '<strong>Discount</strong>';
                reset_form += '</div>';
            reset_form += '</div>';
            reset_form += '<div class="row item_price_div item_price_div_1 mb-3">';
                reset_form += '<div class="col-md-5">';
                    reset_form += '<input type="text" name="price[item][]" class="form-control" placeholder="Enter Item Name">';
                reset_form += '</div>';
                reset_form += '<div class="col-md-2">';
                    reset_form += '<input type="number" name="price[qty][]" class="form-control" value="1">';
                reset_form += '</div>';
                reset_form += '<div class="col-md-2">';
                    reset_form += '<input type="number" name="price[price][]" class="form-control" value="0">';
                reset_form += '</div>';
                reset_form += '<div class="col-md-2">';
                    reset_form += '<input type="number" name="price[discount][]" class="form-control" value="0">';
                reset_form += '</div>';
                reset_form += '<div class="col-md-1">';
                    reset_form += '<button class="btn btn-sm btn-danger" disabled><i class="bi bi-trash"></i></button>';
                reset_form += '</div>';
            reset_form += '</div>';

            $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').html('');
            $('#quoteDetailsModal #quoteReplyForm .main_item_price_div').append(reset_form);
        }

    </script>
@endsection
