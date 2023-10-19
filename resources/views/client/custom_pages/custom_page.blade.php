@php
    $shop_settings = getClientSettings();
@endphp

@extends('client.layouts.client-layout')

@section('title', __('Custom Pages'))

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>{{ __('Custom Pages')}}</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Custom Pages') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Custom Pages Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12 mb-2 text-end">
                <a href="{{ route('custom.pages.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus"></i></a>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="customPagesTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Sr.') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($custom_pages as $custom_page)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $custom_page->name; }}</td>
                                            <td>
                                                @php
                                                    $newStatus = ($custom_page->status == 1) ? 0 : 1;
                                                @endphp
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="status" role="switch" id="status" onclick="changeStatus({{ $custom_page->id }},{{ $newStatus }})" value="1" {{ ($custom_page->status == 1) ? 'checked' : '' }}>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="{{ route('custom.pages.edit',encrypt($custom_page['id'])) }}" class="btn btn-sm btn-primary"><i class="bi bi-pencil"></i></a>
                                                <a onclick="deletePage({{ $custom_page->id }})" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
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

        $('#customPagesTable').DataTable({
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

        // Function for Change Status
        function changeStatus(pageID, status)
        {
            $.ajax({
                type: "POST",
                url: '{{ route("custom.pages.status") }}',
                data: {
                    "_token": "{{ csrf_token() }}",
                    'status':status,
                    'id':pageID
                },
                dataType: 'JSON',
                success: function(response)
                {
                    if (response.success == 1)
                    {
                        toastr.success(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1300);
                    }
                    else
                    {
                        toastr.error(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1300);
                    }
                }
            });
        }


        // Function for Delete Page
        function deletePage(pageID)
        {
            swal({
                title: "Are you sure You want to Delete It ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelBanner) =>
            {
                if (willDelBanner)
                {
                    $.ajax({
                        type: "POST",
                        url: '{{ route("custom.pages.destroy") }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': pageID,
                        },
                        dataType: 'JSON',
                        success: function(response)
                        {
                            if (response.success == 1)
                            {
                                swal(response.message, {
                                    icon: "success",
                                });
                                setTimeout(() => {
                                    location.reload();
                                }, 1300);
                            }
                            else
                            {
                                toastr.error(response.message);
                            }
                        }
                    });
                }
                else
                {
                    swal("Cancelled", "", "error");
                }
            });
        }

    </script>
@endsection
