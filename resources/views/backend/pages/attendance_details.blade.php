@extends('backend.layouts.app',['title'=>'Attendance'])

@section('content')

    <section class="content location_history" style="padding-top: 50px;">
      
      <div class="col-xs-12">
        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Location History</h3>
              <div class="box-body table-responsive no-padding new_padding">
                <div class="row">
                  <div class="col-md-6">
                    <h2>Check-In Details</h2>
                    <div id="mapid"></div>
                    <?php echo '<strong>Date & Time : </strong>' . $att_details['check_in'];?><br>
                    <?php list($latitude1, $longitude1) = explode(",", $att_details['check_in_location']);
                    ?>
                  </div>
                  <div class="col-md-6">
                    <h2>Check-Out Details</h2>
                    <div id="mapid2"></div>
                    <?php echo '<strong>Date & Time : </strong>' . $att_details['check_out'];?><br>
                    <?php list($latitude2, $longitude2) = explode(",", $att_details['check_out_location']);
                    ?>
                  </div>
                </div>
              </div>
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
      marker.bindPopup("<b>Staff was Here</b>").openPopup();
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
      marker.bindPopup("<b>Staff was Here</b>").openPopup();
      map2.scrollWheelZoom.disable();

      L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanJ1cGZxZ3UwNnhsNGFsNTAzcWtsanpsIn0.tnq36qhYA87WJb2nR7_KIw', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox.streets',
      accessToken: 'pk.eyJ1IjoiYW1pdC1tYWhhcnhhbiIsImEiOiJjanRwcjQwbWQwNnljM3lsbDlkcmFlNWVwIn0.BZXR3VF6Xdn7E1OLQaYRiw'
      }).addTo(map2);


  </script>
@endpush