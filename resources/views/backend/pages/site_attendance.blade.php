@extends('backend.layouts.app',['title'=> 'Site Attendance'])

@push('styles')
  <style type="text/css">
    .date_picker{
        border-radius: 0 !important;
        box-shadow: none !important;
        border-color: #d2d6de;
        height: 34px;
        padding: 6px 12px;
        font-size: 14px;
        line-height: 1.42857143;
        color: #555;
        background-color: #fff;
        background-image: none;
        border: 1px solid #ccc;
        border-radius: 4px;
        -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
        -webkit-transition: border-color ease-in-out .15s,-webkit-box-shadow ease-in-out .15s;
        -o-transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
        transition: border-color ease-in-out .15s,box-shadow ease-in-out .15s;
      }
      .search_form{
        margin-left: 100px;
        display: inline-block;
      }
  </style>
@endpush
@section('content')

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
  
          <h3 class="box-title">Logged in Users</h3>
          <form autocomplete="off" class="search_form pull-right" role="form" action="{{route('site.attendance.search')}}" method="POST" enctype="multipart/form-data">
            @csrf
            <select class="select2 search_by_user_id" id="search_by_user_id" name="search_by_user_id">
              <option disabled selected value>Select User</option>
              @foreach($site_attendances_search->unique('user_id') as $site_attendance)
               <option value="{{$site_attendance->user_id}}">{{$site_attendance->name}}</option>
              @endforeach
            </select>
            <select class="select2 search_by_room_id" id="search_by_room_id" name="search_by_room_id">
              <option disabled selected value>Select Room</option>
              @foreach($site_attendances_search->unique('room_id') as $site_attendance)
                <option value="{{$site_attendance->room_id}}">{{$site_attendance->room_no}}</option>
              @endforeach
            </select>

            {{-- <select class="select2 search_by_building_id" id="timezone" name="search_by_building_id">
              <option disabled selected value>Select Building</option>
              @foreach($site_attendances->unique('building_no') as $site_attendance)
                <option value="{{$site_attendance->building_id}}">{{$site_attendance->building_no}}</option>
              @endforeach
            </select> --}}
            {{-- <i class="fa fa-calendar"></i> --}}

            <input type="text" class="date_picker" name="search_by_date" id="datepicker" placeholder="Select Date">
            <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
          </form>
        </div>


        <div class="box-body">
          @if(count($site_attendances))
            <table id="site_attendance_table" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Name</th>
                <th>Room No</th>
                <th>Building No</th>
                <th>Address</th>
                <th>Login Time</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>     
              @foreach($site_attendances as $site_attendance)
                @php 
                  $date = \Carbon\Carbon::parse($site_attendance->date)->timezone(Session::get('timezone')); 
                  $login_time = \Carbon\Carbon::parse($site_attendance->login)->timezone(Session::get('timezone')); 

                @endphp
                <tr>
                  <td>{{$site_attendance->name}}</td>
                  <td>{{$site_attendance->room_no}}</td>
                  <td>{{$site_attendance->building_no}}</td>
                  <td>{{$site_attendance->address}}</td>
                  <td>{{$login_time->format('g:i A')}}</td>
                  <td>{{$date->format('d M Y')}}</td>
                </tr>
              @endforeach
              </tbody>
            </table>
             {{-- {{ $site_attendances->links() }}  --}}
          @else
            <div class="col-md-12" style="text-align: center;">
              No results found
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
  $(function () {
      //Date picker
    $('#datepicker').datepicker({
      autoclose: true
    })


    // $('#site_attendance_table').DataTable({
    //   "pageLength": 8
    // });
  })
</script>
  
@endpush