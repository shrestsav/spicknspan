@extends('backend.layouts.app',['title'=>'Check In / Out'])

@section('content')
 
    <!-- Main content -->
    <section class="content" style="padding-top: 50px;">
      <!-- /.row -->
      @if ($errors->any())
          <div class="alert alert-danger custom_error_msg">   
              @foreach ($errors->all() as $error)
                 {{ $error }}
              @endforeach
          </div>
      @endif

      @if (\Session::has('message'))
          <div class="alert alert-success custom_success_msg">
              {{ \Session::get('message') }}
          </div>
      @endif
        
      
      <form method='POST' action='' class='check_in_form'>
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="box box-success">
              <div class="box-body check_in_body">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <select class="select2" name="client" required>
                  @foreach($clients as $client)
                    <option value="{{$client->id}}">{{$client->name}}</option>
                  @endforeach
                </select>
                <div class="check_in_btn_container">
                  <a class="btn check_in_btn" type="submit" onclick="checkin();">
                    IN
                  </a>
                </div>
                <div class="check_in_btn_container">
                  <a class="btn check_in_btn" onclick="checkout();">
                    OUT
                  </a>
                </div>

                <video id="video" width="640" height="480" autoplay></video>
                <button id="snap" onclick="event.preventDefault();">Snap Photo</button>
                <canvas id="canvas" width="640" height="480"></canvas>

                <button type="submit"></button>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->

@endsection
@push('scripts')
  <script type="text/javascript">

        function checkin(){
            if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(showPosition);
            } else { 
              x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }
        function showPosition(position) {
          var latitude = position.coords.latitude;
          var longitude = position.coords.longitude;
          var action = "{{route('attendance.checkin')}}";
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);
            $('form').attr('action',action);
            $('form').submit();
        }

        function checkout(){
            if (navigator.geolocation) {
              navigator.geolocation.getCurrentPosition(showPosition1);
            } else { 
              x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }
        function showPosition1(position) {
          var latitude = position.coords.latitude;
          var longitude = position.coords.longitude;
          var action = "{{route('attendance.checkout')}}";
            $('#latitude').val(latitude);
            $('#longitude').val(longitude);
            $('form').attr('action',action);
            $('form').submit();
        }

        $('.select2').select2();
      
        // Grab elements, create settings, etc.
        var video = document.getElementById('video');
        
        // Get access to the camera!
        if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            // Not adding `{ audio: true }` since we only want video now
            navigator.mediaDevices.getUserMedia({ video: true }).then(function(stream) {
                //video.src = window.URL.createObjectURL(stream);
                video.srcObject = stream;
                video.play();
            });
        }
        
        // Elements for taking the snapshot
        var canvas = document.getElementById('canvas');
        var context = canvas.getContext('2d');
        var video = document.getElementById('video');
        
        // Trigger photo take
        document.getElementById("snap").addEventListener("click", function() {
          context.drawImage(video, 0, 0, 640, 480);
        });

    </script>
@endpush
