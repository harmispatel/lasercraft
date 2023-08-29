@extends('client.layouts.client-layout')

@section('title', __('Schedule Settings'))

@section('content')

    {{-- Page Title --}}
    <div class="pagetitle">
        <h1>{{ __('Schedule Settings')}}</h1>
        <div class="row">
            <div class="col-md-8">
                <nav>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('client.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Schedule Settings') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    {{-- Options Section --}}
    <section class="section dashboard">
        <div class="row">

            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="scheduleSettingForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <h3>Shop Open/Close Time</h3>
                                </div>
                                @csrf
                                <div class="col-md-6 text-end">
                                    <label class="switch me-2">
                                        <input type="checkbox" value="1" name="scheduler_active" id="scheduler_active" class="ord-setting" {{ (isset($shop_schedule['status']) && $shop_schedule['status'] == 1) ? 'checked' : '' }}>
                                        <span class="slider round">
                                            <i class="fa-solid fa-circle-check check_icon"></i>
                                            <i class="fa-sharp fa-solid fa-circle-xmark uncheck_icon"></i>
                                        </span>
                                    </label>
                                    <label for="scheduler_active" class="form-label">Activate</label>
                                </div>
                                @php
                                    $schedule_arr = (isset($shop_schedule['value']) && !empty($shop_schedule['value'])) ? json_decode($shop_schedule['value'],true) : [];
                                @endphp
                                <div class="col-md-12 mt-3">
                                    @if(count($schedule_arr) > 0)
                                        <div class="row mt-3">
                                            <div class="col-md-12 sc_inner">
                                                <div class="sc_array_section" id="sc_array_section">
                                                    @foreach($schedule_arr as $key => $sched)
                                                        @php
                                                            $active_day = ($sched['enabled'] == 1) ? 'checked' : '';
                                                            $time_arr = $sched['timesSchedules'];
                                                        @endphp

                                                        <div class="p-2" id="{{ $key }}_sec">
                                                            <div class="text-center">
                                                                <input type="checkbox" class="me-2" name="" id="{{ $key }}" {{ $active_day }}> <label for="{{ $key }}">{{ $sched['name'] }}</label>
                                                            </div>
                                                            <div class="sch-sec">
                                                                @if(count($time_arr) > 0)
                                                                    @foreach($time_arr as $tkey => $sc_time)
                                                                        @php
                                                                            $time_key = $tkey + 1;
                                                                            $sc_start_time = $sc_time['startTime'];
                                                                            $sc_end_time = $sc_time['endTime'];
                                                                        @endphp
                                                                        <div class="sch_{{ $time_key }}">
                                                                            @if($time_key > 1)
                                                                                <div class="sch-minus">
                                                                                    <i class="bi bi-dash-circle" onclick="$('#{{ $key }}_sec .sch_{{ $time_key }}').remove()"></i>
                                                                                </div>
                                                                            @endif
                                                                            <input type="time" class="form-control mt-2" name="startTime" id="startTime" value="{{ $sc_start_time }}">
                                                                            <input type="time" class="form-control mt-2" name="endTime" id="endTime" value="{{ $sc_end_time }}">
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                            <div class="sch-plus">
                                                                <i class="bi bi-plus-circle" onclick="addNewSchedule('{{ $key }}_sec')"></i>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row">
                                            <div class="col-md-12 sc_inner">
                                                <div class="sc_array_section" id="sc_array_section">
                                                    <div class="p-2" id="sunday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="sunday"> <label for="sunday">Sun</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('sunday_sec')"></i>
                                                        </div>
                                                    </div>
                                                    <div class="p-2" id="monday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="monday"> <label for="monday">Mon</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('monday_sec')"></i>
                                                        </div>
                                                    </div>
                                                    <div class="p-2" id="tuesday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="tuesday"> <label for="tuesday">Tue</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('tuesday_sec')"></i>
                                                        </div>
                                                    </div>
                                                    <div class="p-2" id="wednesday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="wednesday"> <label for="wednesday">Wed</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('wednesday_sec')"></i>
                                                        </div>
                                                    </div>
                                                    <div class="p-2" id="thursday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="thursday"> <label for="thursday">Thu</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('thursday_sec')"></i>
                                                        </div>
                                                    </div>
                                                    <div class="p-2" id="friday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="friday"> <label for="friday">Fri</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('friday_sec')"></i>
                                                        </div>
                                                    </div>
                                                    <div class="p-2" id="saturday_sec">
                                                        <div class="text-center">
                                                            <input type="checkbox" class="me-2" name="" id="saturday"> <label for="saturday">Sat</label>
                                                        </div>
                                                        <div class="sch-sec">
                                                            <div class="sch_1">
                                                                <input type="time" class="form-control mt-2" name="startTime" id="startTime">
                                                                <input type="time" class="form-control mt-2" name="endTime" id="endTime">
                                                            </div>
                                                        </div>
                                                        <div class="sch-plus">
                                                            <i class="bi bi-plus-circle" onclick="addNewSchedule('saturday_sec')"></i>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="row">
                                <div class="col-md-12">
                                    <button id="update-btn" class="btn btn-success"><i class="bi bi-save"></i> Update</button>
                                </div>
                            </div>
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

        // Function for Add Schedule Section
        function addNewSchedule(divID)
        {
            // sch-sec
            var html = '';
            var counter;
            counter = $('#'+divID+' .sch-sec').children('div').length + 1;

            html += '<div class="sch_'+counter+'">';
                html += '<div class="sch-minus">';
                    html += '<i class="bi bi-dash-circle" onclick="$(this).parent().parent().remove()"></i>';
                html += '</div>';
                html += '<input type="time" name="startTime" id="startTime" class="form-control mt-2">';
                html += '<input type="time" name="endTime" id="endTime" class="form-control mt-2">';
            html += '</div>';

            $('#'+divID+" .sch-sec").append(html);
        }

        @if (Session::has('success'))
            toastr.success('{{ Session::get('success') }}')
        @endif


        // Update Schedule Settings
        $('#update-btn').on("click",function(e)
        {
            e.preventDefault();

            var main_arr = {};
            var days_arr = ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'];

            $.each(days_arr, function (indexInArray, day)
            {
                var dayName = $('#'+day+'_sec label').html();
                var checkedVal = $('#'+day+'_sec #'+day).is(":checked");
                var scheduleLength = $('#'+day+'_sec .sch-sec').children('div').length;
                var sch_all_childs = $('#'+day+'_sec .sch-sec').children('div');

                var time_arr = [];
                var inner_arr_1 = {};

                inner_arr_1['name'] = dayName;
                inner_arr_1['enabled'] = checkedVal;
                inner_arr_1['dayInWeek'] = indexInArray;

                for(var i=0;i<scheduleLength;i++)
                {
                    var inner_arr_2 = {};
                    var sch_child = sch_all_childs[i];
                    var className = sch_child.getAttribute('class');

                    inner_arr_2['startTime'] = $('#'+day+'_sec .sch-sec .'+className+' #startTime').val();
                    inner_arr_2['endTime'] = $('#'+day+'_sec .sch-sec .'+className+' #endTime').val();
                    time_arr.push(inner_arr_2);
                }

                inner_arr_1['timesSchedules'] = time_arr;
                main_arr[day] = inner_arr_1;
            });

            const myFormData = new FormData(document.getElementById('scheduleSettingForm'));
            myFormData.append('schedule_array', JSON.stringify(main_arr));

            $.ajax({
                type: "POST",
                url: "{{ route('update.shop.schedule') }}",
                data: myFormData,
                dataType: "JSON",
                contentType: false,
                cache: false,
                processData: false,
                success: function (response)
                {
                    if(response.success == 1)
                    {
                        toastr.success(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                    else
                    {
                        toastr.error(response.message);
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }
                }
            });

        });

    </script>
@endsection
