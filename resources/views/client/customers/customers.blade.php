@extends('client.layouts.client-layout')

@section('title', __('Customers'))

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>{{ __('Customers')}}</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Customers') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Customer Section --}}
    <section class="section dashboard">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="customersTable">
                                <thead>
                                    <tr>
                                        <th>{{ __('Sr.') }}</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Profile') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($customers) > 0)
                                        @foreach ($customers as $customer)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $customer->firstname }} {{ $customer->lastname }}</td>
                                                <td>{{ $customer->email }}</td>
                                                <td>
                                                    @if(!empty($customer->image) && file_exists('public/admin_uploads/users/'.$customer->image))
                                                        <img src="{{ asset('public/admin_uploads/users/'.$customer->image) }}" alt="Profile" style="width: 45px; height: 45px; border-radius:50%">
                                                    @else
                                                        <img src="{{ asset('public/client_images/not-found/no_image_1.jpg') }}" alt="Profile" style="width: 45px; height: 45px; border-radius:50%">
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $status = $customer->status;
                                                        $checked = ($status == 1) ? 'checked' : '';
                                                        $checkVal = ($status == 1) ? 0 : 1;
                                                    @endphp
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch" onchange="changeStatus({{ $checkVal }},{{ $customer->id }})" id="statusBtn" {{ $checked }}>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a onclick="deleteCustomer({{ $customer->id }})" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
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

        $('#customersTable').DataTable({
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

        // Function for Change Status of Admin
        function changeStatus(status, id)
        {
            $.ajax({
                type: "POST",
                url: "{{ route('customers.status') }}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'status': status,
                    'id': id
                },
                dataType: 'JSON',
                success: function(response)
                {
                    if (response.success == 1)
                    {
                        toastr.success("Status has been Changed SuccessFully");
                        location.reload();
                    }
                    else
                    {
                        toastr.error("Internal Server Error!");
                        location.reload();
                    }
                }
            });
        }


        // Function for Delete Customer
        function deleteCustomer(customerID)
        {
            swal({
                title: "Are you sure You want to Delete It ?",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelCustomer) =>
            {
                if (willDelCustomer)
                {
                    $.ajax({
                        type: "POST",
                        url: '{{ route("customers.destroy") }}',
                        data: {
                            "_token": "{{ csrf_token() }}",
                            'id': customerID,
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
                                }, 1200);
                            }
                            else
                            {
                                swal(response.message, "", "error");
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
