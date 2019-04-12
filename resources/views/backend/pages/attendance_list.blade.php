@extends('backend.layouts.app',['title'=>'Attendance'])

@push('styles')
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
</style>
@endpush

@section('content')

    <!-- Main content -->
    <section class="content attendance_history" style="padding-top: 50px;">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">

            <!-- attendance filter part -->
            <div class="box-header">
              <form role="form" action="{{route('attendance.list')}}" method="GET" data-toggle="validator" enctype="multipart/form-data">
                <?php
                    if(isset($_GET['employee_id'])){
                        $filtEmpId = $_GET['employee_id'];
                    } else {
                        $filtEmpId = '';
                    }

                    if(isset($_GET['client_id'])){
                        $filtCliId = $_GET['client_id'];
                    } else {
                        $filtCliId = '';
                    }

                    if(isset($_GET['filt_date'])){
                        $filtDate = $_GET['filt_date'];
                    } else {
                        $filtDate = '';
                    }
                ?>
                <div class="row">
                  <div class="col-sm-1">
                    <div class="form-group"><h4>Filter : </h4></div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="emp_name" class="label_emp">Employee Name</label>
                      <select class="select2" name="employee_id" id="sel_emp">
                          <option value="">-- Select --</option>
                        @foreach($employees as $emp)
                          <option value="{{$emp->id}}" <?php if ($emp->id == $filtEmpId) echo "selected='selected'";?>>{{$emp->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group">
                      <label for="client_name" class="label_cli">Client Name</label>
                      <select class="select2" name="client_id" id="sel_cli">
                          <option value="">-- Select --</option>
                        @foreach($clients as $client)
                          <option value="{{$client->id}}" <?php if ($client->id == $filtCliId) echo "selected='selected'";?>>{{$client->name}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="form-group date_filter">
                      <label for="filt_dates" class="label_date">Date</label>
                      <input type="date" name="filt_date" class="form-control" id="filt_date" placeholder="Select Date" <?php echo "value=".$filtDate;?>>
                    </div>
                  </div>
                  <div class="col-sm-2">
                    <div class="box-footer">
                      <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                  </div>
              </form>
            </div><hr>
            <!-- attendance filter part -->

            <div class="box-header">
              <h3 class="box-title">Attendance List</h3>
               <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default" onclick="exportTableToExcel('employee_attendance', 'members-data')"><i class="fa fa-file-excel-o"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover table-striped" id="employee_attendance">
                <tr>
                  <th>Date</th>
                  <th>Employee Name</th>
                  <th>Client Name</th>
                  <th>Total Time</th>
                  <th>Timing</th>
                  <th>View Details</th>
                </tr>
                @php $dataArr = []; $no_result = 'true'; @endphp
                @foreach($attendance_lists as $attendance_list)
                  <?php 
                    $fc = 0; $fe = 0; $fd = 0;
                    $fc = $attendance_list->client_id;
                    $fe = $attendance_list->employee_id;
                    $fd = $attendance_list->date;
                    if(
                      (($filtCliId == '') && ($filtEmpId == '') && ($filtDate == '')) ||
                      (($filtCliId == '') && ($filtEmpId == '') && ($filtDate == $fd)) ||
                      (($filtCliId == '') && ($filtEmpId == $fe) && ($filtDate == '')) || 
                      (($filtCliId == '') && ($filtEmpId == $fe) && ($filtDate == $fd)) || 
                      (($filtCliId == $fc) && ($filtEmpId == '') && ($filtDate == '')) || 
                      (($filtCliId == $fc) && ($filtEmpId == '') && ($filtDate == $fd)) ||
                      (($filtCliId == $fc) && ($filtEmpId == $fe) && ($filtDate == '')) ||
                      (($filtCliId == $fc) && ($filtEmpId == $fe) && ($filtDate == $fd))
                    ){ $no_result = 'false';
                   ?>
                  @php  
                      $check_in = \Carbon\Carbon::parse($attendance_list->check_in)->timezone(Session::get('timezone')); 
                      $check_out = \Carbon\Carbon::parse($attendance_list->check_out)->timezone(Session::get('timezone')); 
                      // $hours = $check_out->diffInHours($check_in);
                  @endphp
                  @if($attendance_list->client_name!=''||$attendance_list->client_name!=null)
                  <tr>
                    <td>{{$attendance_list->date}}</td>                   
                    <td>{{$attendance_list->employee_name}}</td>
                    <td>{{$attendance_list->client_name}}</td>
                    <td>{{$attendance_list->total_time}}</td>
                    <td>{{$check_in->format('g:i A')}} - 
                        @if($attendance_list->check_out!=null || $attendance_list->check_out!='')
                          {{$check_out->format('g:i A')}}@else NOT LOGGED OUT @endif</td>
                    <td>
                      <a class="view_att_details btn btn-success" href="{{ url('attendance/details').'/'.$attendance_list->client_id.'/'.$attendance_list->employee_id.'/'.$attendance_list->date}}">Details
                      </a>
                    </td>
                  </tr>
                  @endif
                <?php } ?>
                @endforeach
                <?php
                if($no_result == 'true') {
                  echo '<tr><td colspan="6" align="center"><strong>No results available.</strong></td></tr>';
                } ?>
              </table>
            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->
        </div>
      </div>
    </section>
    <!-- /.content -->

@endsection

@push('scripts')
  <script src="{{ asset('backend/js/export-table-excel.js') }}"></script>
@endpush