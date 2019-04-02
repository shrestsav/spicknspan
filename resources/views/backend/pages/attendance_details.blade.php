@extends('backend.layouts.app',['title'=>'Attendance Details'])

@push('styles')
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
    .map_area{
      height: 500px;
    }
    .modal-dialog {
      width: 823px;
    }
    table tr th, table tr td{
      text-align: center;
    }
    table{
      margin-bottom: 0px !important;
    }
  </style>
@endpush
@section('content')

    <section class="content location_history" style="padding-top: 50px;">
      <div class="row">
        <div class="col-md-8 col-md-offset-2">
          <div class="box box-widget widget-user-2">
            <!-- Add the bg color to the header using any of the bg-* classes -->
            <div class="widget-user-header bg-yellow">
              <div class="widget-user-image">
                {{-- <img class="img-circle" src="../dist/img/user7-128x128.jpg" alt="User Avatar"> --}}
              </div>
              <!-- /.widget-user-image -->
              <h3 class="widget-user-username">{{strtoupper($attendance_details[0]->name)}}</h3>
              <h5 class="widget-user-desc">{{$attendance_details[0]->check_in}}</h5>
            </div>
            <div class="box-footer no-padding">
              <table class="table table-striped user_attendance_table">
                <tr>
                  <th>CHECKED IN</th>
                  <th>CHECKED OUT</th>
                </tr>
                @foreach($attendance_details as $attendance_detail)
                  @php 
                    list($latitude1, $longitude1) = explode(",", $attendance_detail->check_in_location);
                  @endphp
                <tr>
                  <td>
                    <span>
                      <img class="img-circle" src="{{asset('storage/employee_login/'.$attendance_detail->employee_id.'/'.$attendance_detail->check_in_image)}}" alt="User Avatar">
                    </span>
                    
                    <span><strong>Date : </strong> {{$attendance_detail->check_in}}</span>
                    <span>
                      <i class="fa fa-map" data-toggle="modal" data-target="#display_check_in_map_{{$attendance_detail->id}}"></i>

                      <div class="modal fade" id="display_check_in_map_{{$attendance_detail->id}}">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-body map_area" id="check_in_map_area_{{$attendance_detail->id}}">
                              {{-- MAP GOES HERE --}}
                            </div>
                          </div>
                        </div>
                      </div>
                    </span>
                    
                  </td>
                  <td>
                    @if($attendance_detail->check_out!=null)
                    <span>
                      <img class="img-circle" src="{{asset('storage/employee_login/'.$attendance_detail->employee_id.
                      '/'.$attendance_detail->check_out_image)}}" alt="User Avatar">
                    </span>
                    
                    <span><strong>Date : </strong> {{$attendance_detail->check_out}}</span>
                    <span>
                      <i class="fa fa-map" data-toggle="modal" data-target="#display_check_out_map_{{$attendance_detail->id}}"></i>

                      <div class="modal fade" id="display_check_out_map_{{$attendance_detail->id}}">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-body map_area" id="check_out_map_area_{{$attendance_detail->id}}">
                              {{-- MAP GOES HERE --}}
                            </div>
                          </div>
                        </div>
                      </div>
                    </span>
                    @else
                      <span><strong>NOT LOGGED OUT</strong></span>
                    @endif
                    
                  </td>
                </tr>
                @endforeach    
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>





@endsection
@push('scripts')

  <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"></script>
  <script type="text/javascript">

    @foreach($attendance_details as $attendance_detail)
      //map in
      @php list($latitude1, $longitude1) = explode(",", $attendance_detail->check_in_location); @endphp

      var geoCoords = '[' + {{$latitude1}} + ', ' + {{$longitude1}} + ']';
      var map = L.map('check_in_map_area_{{$attendance_detail->id}}', {
      center: JSON.parse(geoCoords),
      zoom: 13
      });
      var marker = L.marker(JSON.parse(geoCoords)).addTo(map);
      marker.bindPopup("<b>{{$attendance_detail->name}} was here</b>").openPopup();
      map.scrollWheelZoom.disable();

      L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanJ1cGZxZ3UwNnhsNGFsNTAzcWtsanpsIn0.tnq36qhYA87WJb2nR7_KIw', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox.streets',
      accessToken: 'pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanRwcjQwbWQwNnljM3lsbDlkcmFlNWVwIn0.BZXR3VF6Xdn7E1OLQaYRiw'
      }).addTo(map);

    @if($attendance_detail->check_out_location!=null)
      @php list($latitude2, $longitude2) = explode(",", $attendance_detail->check_out_location); @endphp
      //map out
      var geoCoords2 = '[' + {{$latitude2}} + ', ' + {{$longitude2}} + ']';
      var map2 = L.map('check_out_map_area_{{$attendance_detail->id}}', {
      center: JSON.parse(geoCoords2),
      zoom: 14
      });
      var marker = L.marker(JSON.parse(geoCoords2)).addTo(map2);
      marker.bindPopup("<b>{{$attendance_detail->name}} was here</b>").openPopup();
      map2.scrollWheelZoom.disable();

      L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanJ1cGZxZ3UwNnhsNGFsNTAzcWtsanpsIn0.tnq36qhYA87WJb2nR7_KIw', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox.streets',
      accessToken: 'pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanRwcjQwbWQwNnljM3lsbDlkcmFlNWVwIn0.BZXR3VF6Xdn7E1OLQaYRiw'
      }).addTo(map2);
    @endif


      //Chalena
      $('#display_check_in_map_{{$attendance_detail->id}}').on('show.bs.modal', function(){
        setTimeout(function() {
          map.invalidateSize();
        }, 10);
      });

    @endforeach

  </script>
@endpush