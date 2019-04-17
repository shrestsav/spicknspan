@extends('backend.layouts.app',['title'=>'Attendance Details'])

@push('styles')

  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css" integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA==" crossorigin=""/>
  <style type="text/css">
    .check_in_user_photo, .check_out_user_photo{
      border: 5px solid #e6e6e6;
      border-radius: 90px;
      height: 140px;
      width: 140px;
    }
    .user_photo_container{
      padding: 30px 0px 30px 10px;
    }
    .user_photo{
      text-align: center;
    }
    .user_check_in_time, .user_check_out_time{
      padding: 30px;
      font-size: 2rem;
    }

    .user_attendance_table .img-circle{
      /*border: 1px solid red;*/
      height: 50px;
      width: 50px;
    }
    .user_attendance_table span{
      margin:0px 20px;
    }
    .modal-dialog {
      width: 823px;
    }
    table tr th, table tr td{
      text-align: center;
      vertical-align: middle !important;
    }
    table{
      margin-bottom: 0px !important;
    }
    strong{
      font-size: 1.6rem;
    }
    #display_check_in_out_photo .modal-dialog{
      margin: 200px auto;
      width: 320px;
    }
    #display_check_in_out_photo .modal-body{
      padding: 0px;
    }
    .expand_map:hover, .check_in_out_image:hover{
      cursor: pointer;
    }
    .bg-green-gradient {
      background: #00a65a !important;
      background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #292929), color-stop(1, #0f9055)) !important;
    }
  </style>
@endpush
@section('content')


@php  
  $check_in_date = \Carbon\Carbon::parse($attendance_details[0]->check_in)->timezone(Session::get('timezone')); 
  $employee_id = $attendance_details[0]->employee_id; 
@endphp

  <section class="content location_history" style="padding-top: 50px;">
    <div class="row">
      <div class="col-md-10 col-md-offset-1">
        <div class="box box-widget widget-user">
          <div class="widget-user-header bg-green">
            <h3 class="widget-user-username">{{strtoupper($attendance_details[0]->employee_name)}}</h3>
            <h5 class="widget-user-desc">CLIENT : {{$attendance_details[0]->client_name}}</h5>
            <h5 class="widget-user-desc">DATE : {{$check_in_date->format('d M Y')}}</h5>
          </div>
          <div class="widget-user-image">
            @if(file_exists(public_path('files/users/'.$employee_id.'/dp_user_'.$employee_id.'.png')))
              <img class="img-circle" src="{{ asset('files/users/'.$employee_id.'/dp_user_'.$employee_id.'.png') }}" alt="User Avatar">
            @else
              <img class="img-circle" src="{{ asset('backend/img/user_default.png') }}" alt="User Avatar">
            @endif
          </div>
          <div class="box-footer">
            <div class="row">
              <table class="table table-hover user_attendance_table">
                <tr>
                  <th><strong>CHECKED IN</strong></th>
                  <th><strong>CHECKED OUT</strong></th>
                </tr>
                @foreach($attendance_details as $attendance_detail)
                  @php 
                    $check_in_time = \Carbon\Carbon::parse($attendance_detail->check_in)->timezone(Session::get('timezone')); 
                    $check_out_time = \Carbon\Carbon::parse($attendance_detail->check_out)->timezone(Session::get('timezone')); 
                    list($check_in_latitude, $check_in_longitude) = explode(",", $attendance_detail->check_in_location);
                  @endphp
                <tr>
                  <td>
                    <span>
                      <img class="img-circle check_in_out_image" src="{{asset('files/employee_login/'.$attendance_detail->employee_id.'/'.$attendance_detail->check_in_image)}}" alt="User Avatar">
                    </span>
                    
                    <span><strong>{{$check_in_time->format('g:i A')}}</strong></span>
                    <span class="expand_map">
                      <i class="fa fa-map-marker fa-2x show_map" data-latitude="{{$check_in_latitude}}" data-longitude="{{$check_in_longitude}}"></i>
                    </span>
                  </td>
                  <td>
                    @if($attendance_detail->check_out!=null)
                      @php 
                        list($check_out_latitude, $check_out_longitude) = explode(",", $attendance_detail->check_out_location);
                      @endphp
                    <span>
                      <img class="img-circle check_in_out_image" src="{{asset('files/employee_login/'.$attendance_detail->employee_id.
                      '/'.$attendance_detail->check_out_image)}}" alt="User Avatar">
                    </span>
                    
                    <span><strong>{{$check_out_time->format('g:i A')}}</strong></span>
                    <span class="expand_map">
                      <i class="fa fa-map-marker fa-2x show_map" data-latitude="{{$check_out_latitude}}" data-longitude="{{$check_out_longitude}}"></i>
                    </span>
                    @else
                      <span><strong>NOT LOGGED OUT</strong></span>
                    @endif
                    
                  </td>
                </tr>
                @endforeach    
              </table>
            </div>
            <!-- /.row -->
          </div>
        </div>
      </div>
      <div class="col-md-10 col-md-offset-1 display_map" style="display: none;">
        <div class="box box-solid bg-green-gradient">
          <div class="box-header">
            <div class="pull-right box-tools">
              <button type="button" class="btn btn-success btn-sm pull-right" data-widget="collapse" data-toggle="tooltip" title="Collapse" style="margin-right: 5px;">
                <i class="fa fa-minus"></i>
              </button>
            </div>
            <!-- /. tools -->

            <i class="fa fa-map-marker"></i>

            <h3 class="box-title">
              Tracked Location
            </h3>
          </div>
          <div class="box-body">
            <div id="world-map" style="height: 450px; width: 100%;"></div>
          </div>
        </div>
      </div>
    </div>
  </section>

<div class="modal fade" id="display_check_in_out_photo" >
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <img src="">
      </div>
    </div>
  </div>
</div>
@endsection
@push('scripts')

   <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js" integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg==" crossorigin=""></script>
  <script type="text/javascript">

      $('.check_in_out_image').on('click',function(e){
        e.preventDefault();
        var img_src = $(this).attr('src');
        console.log(img_src);
        $('#display_check_in_out_photo img').attr('src',img_src);
        $('#display_check_in_out_photo').modal('show');
      });

      $('.show_map').on('click',function(e){
        e.preventDefault();
        $('.display_map').show();

        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');
        document.getElementById('world-map').innerHTML = "<div id='map' style='width: 100%; height: 100%;'></div>";
        var geoCoords = '[' + latitude + ', ' + longitude + ']';
        var map = L.map('map', {
        center: JSON.parse(geoCoords),
        zoom: 16
        });
        var marker = L.marker(JSON.parse(geoCoords)).addTo(map);
        marker.bindPopup("<b>{{ucfirst($attendance_detail->name)}}</b>").openPopup();
        map.scrollWheelZoom.disable();

        L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanJ1cGZxZ3UwNnhsNGFsNTAzcWtsanpsIn0.tnq36qhYA87WJb2nR7_KIw', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanRwcjQwbWQwNnljM3lsbDlkcmFlNWVwIn0.BZXR3VF6Xdn7E1OLQaYRiw'
        }).addTo(map);
      });

  </script>
@endpush