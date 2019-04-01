@extends('backend.layouts.app',['title'=>'Attendance'])

@section('content')

    <!-- Main content -->
    <section class="content attendance_history" style="padding-top: 50px;">
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header">
              <h3 class="box-title">Attendance</h3>
               <div class="box-tools">
                <div class="input-group input-group-sm" style="width: 150px;">
                  <div class="input-group-btn">
                    <button type="submit" class="btn btn-default" onclick="exportTableToExcel('employee_attendance', 'members-data')"><i class="fa fa-file-excel-o"></i></button>
                  </div>
                </div>
              </div>
            </div>
            <div class="box-body table-responsive no-padding">
              <table class="table table-hover" id="employee_attendance">
                <tr>
                  <th>Date</th>
                  <th>Employee Name</th>
                  <th>Client Name</th>
                  <th>Total Hours</th>
                  <th>Timing</th>
                  <th>View Details</th>
                </tr>
                @php $dataArr = []; @endphp
                @foreach($attendance_lists as $attendance_list)
                  @php  
                      $check_in = \Carbon\Carbon::parse($attendance_list->check_in);
                      $check_out = \Carbon\Carbon::parse($attendance_list->check_out);
                      $hours = $check_out->diffInHours($check_in);
                  @endphp
                  <tr>
                    <td>{{$check_in->format('d-m-Y')}}</td>                   
                    <td>{{$attendance_list->employee_name}}</td>
                    <td>{{$attendance_list->client_name}}</td>
                    <td>{{$hours}} Hours</td>
                    <td>{{$check_in->format('H:i')}} - 
                        @if($attendance_list->check_out!=null || $attendance_list->check_out!='')
                          {{$check_out->format('H:i')}}@else Logged In @endif</td>
                    <td>
                      <button class="view_att_details">
                        <a href="{{ url('attendance/details/').'/'.$attendance_list->id}}">Details</a>
                      </button>
                    </td>
                  </tr>
                @endforeach
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