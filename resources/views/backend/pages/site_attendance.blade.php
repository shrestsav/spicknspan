@extends('backend.layouts.app',['title'=> 'Site Attendance'])

@push('styles')
  <style type="text/css">
      .select2{
        width: 130px;
      }
      .search_form{
        display: inline-block;
      }
      .filter_label{
        padding: 20px;
      }
      .search_by_date{
        display: inline-table;
        width: 150px; 
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
              {{-- <label class="filter_label">Filter</label> --}}
              @php 
                $search_arr = [
                  'User Name' => [
                    'class'   => 'search_by_user_id',
                    'name'    => 'search_by_user_id',
                    'value'   => 'user_id',
                    'view'    => 'name'
                  ],
                  'Building Name' => [
                    'class'   => 'search_by_building_name',
                    'name'    => 'search_by_building_id',
                    'value'   => 'building_id',
                    'view'    => 'building_name'
                  ],
                  'Building No' => [
                    'class'   => 'search_by_building_no',
                    'name'    => '',
                    'value'   => 'building_id',
                    'view'    => 'building_no'
                  ],
                  'Division / Area' => [
                    'class'   => 'search_by_room_name',
                    'name'    => 'search_by_room_id',
                    'value'   => 'room_id',
                    'view'    => 'room_name'
                  ],
                  'Room No' => [
                    'class'   => 'search_by_room_no',
                    'name'    => '',
                    'value'   => 'room_id',
                    'view'    => 'room_no'
                  ],
                ]
              @endphp

              @foreach($search_arr as $part => $arr)
                <select class="select2 {{$arr['class']}}" name="{{$arr['name']}}">
                  <option disabled selected value> {{$part}}</option>
                  @foreach($site_attendances_search->unique($arr['value']) as $site_attendance)
                    @php 
                      $val = $site_attendance->{$arr['value']};
                    @endphp
                    <option value="{{$val}}" @if(Request::input('search_by_'.$arr['value'])==$val) selected @endif>
                      {{$site_attendance->{$arr['view']} }}
                    </option>
                  @endforeach
                </select>
              @endforeach

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
        <div class="box-body table-responsive no-padding">
          @if(count($site_attendances))
            <table id="site_attendance_table" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>S.No</th>
                <th>Name</th>
                <th>Building Name</th>
                <th>Building No</th>
                <th>Division / Area</th>
                <th>Room No</th>
                <th>Description</th>
                <th>Login Time</th>
                <th>Location</th>
                <th>Date</th>
              </tr>
              </thead>
              <tbody>
                @php 
                  $count = 1;
                @endphp    
              @foreach($site_attendances as $site_attendance)
                @php 
                  $date = \Carbon\Carbon::parse($site_attendance->tz_login_date); 
                @endphp
                <tr>
                  <td>{{$count}}</td>
                  <td>{{$site_attendance->name}}</td>
                  <td>{{$site_attendance->building_name}}</td>
                  <td>{{$site_attendance->building_no}}</td>
                  <td>{{$site_attendance->room_name}}</td>
                  <td>{{$site_attendance->room_no}}</td>
                  <td>{{$site_attendance->description}}</td>
                  <td>{{$site_attendance->tz_login_time}}</td>
                  <td class="loc" data-location = "{{$site_attendance->login_location}}">
                    @if($site_attendance->login_location)
                      <a href="javascript:;">{{$site_attendance->login_location}}</a>
                    @else
                      Not Available
                    @endif
                  </td>
                  <td>{{$date->format('d M Y')}}</td>
                </tr>
                @php
                  $count++;
                @endphp
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




<div class="modal modal-info fade" id="modal-info">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Location</h4>
      </div>
      <div class="modal-body no-padding" style="height: 700px;">
        
      </div>
    </div>
  </div>
</div>

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

  $('.loc').on('click',function(){
    var loc = $(this).data('location');
    var src = "<iframe width='100%' height='100%' id='mapcanvas' src='https://maps.google.com/maps?q="+loc+"&amp;t=&amp;z=17&amp;ie=UTF8&amp;iwloc=&amp;output=embed' frameborder='0' scrolling='no' marginheight='0' marginwidth='0'><div class='zxos8_gm'></div><div style='overflow:hidden;'><div id='gmap_canvas' style='height:100%;width:100%;'></div></div><div><small>Powered by</small></div></iframe>";
    $('#modal-info .modal-body').html(src);
    $('#modal-info').modal();
  })
</script>
  
@endpush