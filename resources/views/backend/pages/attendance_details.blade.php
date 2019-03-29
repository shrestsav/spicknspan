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
  </style>
@endpush
@section('content')

<?php 
      list($latitude1, $longitude1) = explode(",", $att_details['check_in_location']);
      $employee_id = $att_details['employee_id'];
      $check_in_image = $att_details['check_in_image'];
?>

    <section class="content location_history" style="padding-top: 50px;">
      <div class="row">
        <div class="col-xs-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Check In Details</h3>
            </div>
            <div class="box-body table-responsive no-padding new_padding">
              <div class="row">
                <div class="row user_photo_container">
                  <div class="col-md-4 user_photo">
                    <img class="check_in_user_photo" height="150" src="{{asset('storage/employee_login/'.$employee_id.'/'.$check_in_image)}}">
                  </div>
                  <div class="col-md-6 user_check_in_time">
                    @php  
                      $check_in = \Carbon\Carbon::parse($att_details->check_in); 

                    @endphp
                    <strong>Date : </strong>{{ $check_in->format('M d'.', '.'Y') }}<br>
                    <strong>Time : </strong>{{ $check_in->format('H : i') }}<br>
                  </div>
                </div>
                <div class="row user_map_container">
                  <div id="mapid"></div>     
                </div>                   
              </div>
            </div>
          </div>
        </div>

      @if($att_details['check_out']!='' || $att_details['check_out']!=null)
      <?php list($latitude2, $longitude2) = explode(",", $att_details['check_out_location']);
        $check_out_image = $att_details['check_out_image'];
      ?>
        <div class="col-xs-6">
          <div class="box">
            <div class="box-header with-border">
              <h3 class="box-title">Check Out Details</h3>
            </div>
            <div class="box-body table-responsive no-padding new_padding">
              <div class="row">
                <div class="row user_photo_container">
                  <div class="col-md-4 user_photo">
                    <img class="check_in_user_photo" height="150" src="{{asset('storage/employee_login/'.$employee_id.'/'.$check_out_image)}}">
                  </div>
                  <div class="col-md-6 user_check_out_time">
                    @php  
                      $check_out = \Carbon\Carbon::parse($att_details->check_out); 
                    @endphp
                    <strong>Date : </strong>{{ $check_out->format('M d'.', '.'Y') }}<br>
                    <strong>Time : </strong>{{ $check_out->format('H : i') }}<br>
                  </div>
                </div>
                <div class="row user_map_container">
                  <div id="mapid2"></div>     
                </div>   
              </div>
            </div>
          </div>
        </div>
      @else
        <?php $latitude2 = ''; $longitude2 = ''; ?>
      @endif
      </div>
    </div>

      </div>

    </section>

@endsection
@push('scripts')

  <script src="https://unpkg.com/leaflet@1.3.4/dist/leaflet.js"></script>
  <script type="text/javascript">

      //map in
      var geoCoords = '[' + <?php echo $latitude1;?> + ', ' + <?php echo $longitude1;?> + ']';
      var map = L.map('mapid', {
      center: JSON.parse(geoCoords),
      zoom: 14
      });
      var marker = L.marker(JSON.parse(geoCoords)).addTo(map);
      marker.bindPopup("<b>{{$att_details->name}} was here</b>").openPopup();
      map.scrollWheelZoom.disable();

      L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanJ1cGZxZ3UwNnhsNGFsNTAzcWtsanpsIn0.tnq36qhYA87WJb2nR7_KIw', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox.streets',
      accessToken: 'pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanRwcjQwbWQwNnljM3lsbDlkcmFlNWVwIn0.BZXR3VF6Xdn7E1OLQaYRiw'
      }).addTo(map);

      //map out
      var geoCoords2 = '[' + <?php echo $latitude2;?> + ', ' + <?php echo $longitude2;?> + ']';
      var map2 = L.map('mapid2', {
      center: JSON.parse(geoCoords2),
      zoom: 14
      });
      var marker = L.marker(JSON.parse(geoCoords2)).addTo(map2);
      marker.bindPopup("<b>{{$att_details->name}} was here</b>").openPopup();
      map2.scrollWheelZoom.disable();

      L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanJ1cGZxZ3UwNnhsNGFsNTAzcWtsanpsIn0.tnq36qhYA87WJb2nR7_KIw', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox.streets',
      accessToken: 'pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanRwcjQwbWQwNnljM3lsbDlkcmFlNWVwIn0.BZXR3VF6Xdn7E1OLQaYRiw'
      }).addTo(map2);


  </script>
@endpush