@extends('backend.layouts.app',['title'=> 'Site Attendance'])

@push('styles')
  <style type="text/css">
      .search_form{
        display: inline-block;
      }
      .filter_label{
        padding: 20px 20px 10px 20px;
      }
      .search_by_date{
        display: inline-table;
        width: 170px; 
        top: 14px;
      }
  </style>
@endpush
@section('content')

<section class="content">
  <div class="row">
    <div class="col-md-12">

      @if(Request::all())
        <a href="{{url('/siteAttendance')}}"><button class="btn btn-primary">Show All</button></a>
      @endif

      @permission('import_export_excel')
        <div class="pull-right">
          <a href="{{ route('export_to_excel',Route::current()->getName()) }}" class="export_to_excel">
            <button class="btn btn-success">Export to Excel</button>
          </a>
        </div>
      @endpermission

    </div>
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          {{-- <h3 class="box-title">Logged in Users</h3> --}}
          
          {{-- Search Form --}}
          <div class="search_form">
            <form autocomplete="off" role="form" action="{{route('site.attendance.search')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <label class="filter_label">Filter</label>
              <select class="select2 search_by_user_id" name="search_by_user_id">
                <option disabled selected value>User Name</option>
                @foreach($site_attendances_search->unique('user_id') as $site_attendance)
                 <option value="{{$site_attendance->user_id}}" @if(Request::input('search_by_user_id')==$site_attendance->user_id) selected @endif>{{$site_attendance->name}}</option>
                @endforeach
              </select>
              <select class="select2 search_by_building_name" name="search_by_building_id">
                <option disabled selected value>Building Name</option>
                @foreach($site_attendances_search->unique('building_id') as $site_attendance)
                 <option value="{{$site_attendance->building_id}}" @if(Request::input('search_by_building_id')==$site_attendance->building_id) selected @endif>{{$site_attendance->building_name}}</option>
                @endforeach
              </select>
              <select class="select2 search_by_building_no">
                <option disabled selected value> Building No</option>
                @foreach($site_attendances_search->unique('building_id') as $site_attendance)
                  <option value="{{$site_attendance->building_id}}" @if(Request::input('search_by_building_id')==$site_attendance->building_id) selected @endif>{{$site_attendance->building_no}}</option>
                @endforeach
              </select>
              <select class="select2 search_by_room_name" name="search_by_room_id">
                <option disabled selected value>Division / Area</option>
                @foreach($site_attendances_search->unique('room_id') as $site_attendance)
                  <option value="{{$site_attendance->room_id}}" @if(Request::input('search_by_room_id')==$site_attendance->room_id) selected @endif>{{$site_attendance->room_name}}</option>
                @endforeach
              </select>
              <select class="select2 search_by_room_no">
                <option disabled selected value>Room No</option>
                @foreach($site_attendances_search->unique('room_id') as $site_attendance)
                  <option value="{{$site_attendance->room_id}}"  @if(Request::input('search_by_room_id')==$site_attendance->room_id) selected @endif>{{$site_attendance->room_no}}</option>
                @endforeach
              </select>
              <div class="input-group date search_by_date">
                <div class="input-group-addon">
                  <i class="fa fa-calendar"></i>
                </div>
                <input type="text" class="form-control pull-right" name="search_by_date" id="datepicker" placeholder="Select Date" @if(Request::input('search_by_date')) value="{{Request::input('search_by_date')}}" @endif>
              </div>
              &nbsp; &nbsp; &nbsp;
              <button type="submit" class="btn btn-primary">Search</button>
            </form>
          </div>
        </div>
        <div class="box-body table-responsive">
          @if(count($site_attendances))
            <table id="site_attendance_table" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>Name</th>
                <th>Building Name</th>
                <th>Building No</th>
                <th>Division / Area</th>
                <th>Room No</th>
                <th>Description</th>
                <th>Login Time</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>    
              @foreach($site_attendances as $site_attendance)
                @php 
                  $date = \Carbon\Carbon::parse($site_attendance->tz_login_date); 
                @endphp
                <tr>
                  <td>{{$site_attendance->name}}</td>
                  <td>{{$site_attendance->building_name}}</td>
                  <td>{{$site_attendance->building_no}}</td>
                  <td>{{$site_attendance->room_name}}</td>
                  <td>{{$site_attendance->room_no}}</td>
                  <td>{{$site_attendance->description}}</td>
                  <td>{{$site_attendance->tz_login_time}}</td>
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
    });


    // Search Switch Algorithms
    search_by_classes = {
                          search_by_building_name:'search_by_building_no', 
                          search_by_building_no:'search_by_building_name', 
                          search_by_room_name:'search_by_room_no',
                          search_by_room_no:'search_by_room_name',
                        };
    
    Object.keys(search_by_classes).forEach(function(key){
        $('.'+key).on('change', function(e) {
          e.preventDefault();
          var oldval = $('.'+search_by_classes[key]).val();
          var newval = this.value;
          if(oldval!=newval)
              $('.'+search_by_classes[key]).val(this.value).trigger('change');
        });
    });
    


    // $('#site_attendance_table').DataTable({
    //   "pageLength": 8,
    //   "searching": false
    // });
  })
</script>
  
@endpush