@extends('backend.layouts.app',['title'=>'Attendance'])

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/excel-plugins/tableexport.css') }}">
<style type="text/css">
    .form-group.date_filter {
        display: inline-flex;
    }
    .row .col-sm-3 {
        padding-top: 8px;
    }
    label.label_date {
        padding-right: 10px;
        padding-top: 6px;
    }
    label.label_emp, .label_cli {
        padding-right: 6px;
    }
    .search_form{
        display: inline-block;
    }
    .filter_label{
      padding: 20px;
    }
    .search_by_date{
      display: inline-table;
      width: 240px; 
      top: 14px;
    }
</style>
@endpush

@section('content')

    <!-- Main content -->
    <section class="content attendance_history" style="padding-top: 50px;">
      <div class="row">
        <div class="col-md-12">
          @if(Request::all())
            <a href="{{url('/attendance')}}"><button class="btn btn-primary">Show All</button></a>
          @endif
        </div>
        <div class="col-xs-12">
          <div class="box">

            <!-- attendance filter part -->
            <div class="box-header">
              <div class="search_form">
                <form autocomplete="off" role="form" action="{{route('attendance.search')}}" method="POST" enctype="multipart/form-data">
                  @csrf
                  <label class="filter_label">Filter</label>
                    <select class="select2" name="search_by_employee_id" id="sel_emp">
                      <option disabled selected value>Employee Name</option>
                      @foreach($employees as $emp)
                        <option value="{{$emp->id}}" @if(Request::input('search_by_employee_id')==$emp->id) selected @endif>{{$emp->name}}</option>
                      @endforeach
                    </select>
                    <select class="select2" name="search_by_client_id" id="sel_cli">
                      <option disabled selected value>Client Name</option>
                      @foreach($clients as $client)
                        <option value="{{$client->id}}" @if(Request::input('search_by_client_id')==$client->id) selected @endif>{{$client->name}}</option>
                      @endforeach
                    </select>
                    <div class="input-group search_by_date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      @php
                        $today = date('m/d/Y');
                        $pastOneMonth = date("m/d/Y", strtotime( date( "m/d/Y", strtotime( date("m/d/Y") ) ) . "-1 month" ) );
                      @endphp
                      <input type="text" class="form-control pull-right" id="search_date_from_to" name="search_date_from_to" @if(Request::input('search_date_from_to')) value="{{Request::input('search_date_from_to')}}" @else value="{{$pastOneMonth.' - '.$today}}" @endif>
                    </div>
                    
                    {{-- {{Request::input('search_date_from_to')}} --}}
                    &nbsp; &nbsp; &nbsp;
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
              </div>
              {{-- <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default export_to_excel" onclick="exportTableToExcel('employee_attendance', 'members-data')"><i class="fa fa-file-excel-o"></i></button>
                  </div>
                </div>
              </div> --}}
            </div>

{{--             <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-striped" id="employee_attendance">
                <tr>
                  <th>Date</th>
                  <th>Employee Name</th>
                  <th>Client Name</th>
                  <th>Total Time</th>
                  <th>Timing</th>
                  <th>View Details</th>
                </tr>
                @if(count($attendance_lists))
                  @foreach($attendance_lists as $attendance_list)
                    @php  
                      $check_in = \Carbon\Carbon::parse($attendance_list->check_in)->timezone(Session::get('timezone')); 
                      $check_out = \Carbon\Carbon::parse($attendance_list->check_out)->timezone(Session::get('timezone')); 
                        // $hours = $check_out->diffInHours($check_in);
                    @endphp
                    <tr>
                      <td>{{$attendance_list->date}}</td>                   
                      <td>{{$attendance_list->employee_name}}</td>
                      <td>{{$attendance_list->client_name}}</td>
                      <td>{{$attendance_list->total_time}}</td>
                      <td>{{$check_in->format('g:i A')}} - 
                        @if($attendance_list->check_out!=null || $attendance_list->check_out!='')
                          {{$check_out->format('g:i A')}}
                        @else 
                          NOT LOGGED OUT 
                        @endif
                      </td>
                      <td>
                        <a class="view_att_details btn btn-success" href="{{ url('attendance/details').'/'.$attendance_list->client_id.'/'.$attendance_list->employee_id.'/'.$attendance_list->date}}">Details
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="6" align="center"><strong>No results available.</strong></td>
                  </tr>
                @endif
              </table>
            </div> --}}
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-striped" id="employee_attendance">
                <thead>
                  <tr>
                    <th>Date</th>
                    <th>Employee Name</th>
                    <th>Client Name</th>
                    <th>Total Time</th>
                    <th>Timing</th>
                    <th>View Details</th>
                  </tr>
                </thead>
                <tbody>
                @foreach($grouped_attendances as $date => $grouped_attendance)
                  @foreach($grouped_attendance as $id => $details)  
                    <tr>
                      <td>{{$date}}</td>                   
                      <td>{{$details[0]['employee_name']}}</td>
                      <td>{{$details[0]['client_name']}}</td>
                      <td>
                        @php 
                          $count = count($details);
                          $totalSec = 0;
                          foreach($details as $detail){
                            if($detail['check_in'] && $detail['check_out']){
                              $startTime = \Carbon\Carbon::parse($detail['check_in']);
                              $endTime = \Carbon\Carbon::parse($detail['check_out']);
                              $totalDuration = $endTime->diffInSeconds($startTime);
                              $totalSec += $totalDuration;
                            }
                          }
                          $startTime = $details[0]['local_check_in']['time'];
                          if($details[($count-1)]['check_out'])
                            $endTime = $details[($count-1)]['local_check_in']['time'];
                          else
                            $endTime = 'Not Logged Out';
                        @endphp
                        {{gmdate('H:i:s', $totalSec)}}
                      </td>
                      <td>{{$startTime}} - {{$endTime}}</td>
                      <td>
                        <a class="view_att_details btn btn-success" href="{{ url('attendance/details').'/'.$details[0]['client_id'].'/'.$details[0]['employee_id'].'/'.$date}}">
                          Details
                        </a>
                      </td>
                    </tr>
                  @endforeach
                @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

@endsection

@push('scripts')
  {{-- <script src="{{ asset('backend/js/export-table-excel.js') }}"></script> --}}
  <script src="{{ asset('backend/excel-plugins/xlsx.core.min.js') }}"></script>
  <script src="{{ asset('backend/excel-plugins/Blob.js') }}"></script>
  <script src="{{ asset('backend/excel-plugins/FileSaver.js') }}"></script>
  <script src="{{ asset('backend/excel-plugins/Export2Excel.js') }}"></script>


  <script src="{{ asset('backend/excel-plugins/jquery.tableexport.v2.js') }}"></script>
  <script type="text/javascript">
  $(function () {
    //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    });
    $('#search_date_from_to').daterangepicker();
  });

$("table").tableExport({
        formats: ["xlsx"],
        ignoreCols: Number[3],  
    });


  </script>
@endpush