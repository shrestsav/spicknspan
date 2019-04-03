@extends('backend.layouts.app',['title'=>'Check In / Out'])
@push('styles')
<style type="text/css">
    video,canvas{
    margin-left: -80px;
    margin-top: 20px;
    position: absolute;
  }
  .heading{
    padding: 20px 0px 10px 0px;
    font-size: 1.8rem;
  }
</style>
@endpush
@section('content')
 
    <!-- Main content -->
    <section class="content" style="padding-top: 50px;">

      {{-- To display Errors from Javascript --}}
      <div class="alert alert-danger access_error_msg" style="display: none;">   
          @foreach ($errors->all() as $error)
             {{ $error }}
          @endforeach
      </div>
      {{-- To display Errors from Laravel Session --}}
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
                <input type="hidden" name="image" id="user_image" required>
                <div class="col-md-4">  
                  <div id="container"  name='cont' class="container-fluid no-padding ">

                      {{-- <div class="demo-frame">
                        <div class="demo-container">
                          <video id="video" width="320" height="240" preload autoplay loop muted></video>
                          <canvas id="canvas" width="320" height="240"></canvas>
                        </div>
                      </div> --}}
                     <video autoplay="true" id="videoElement" name='vid' width="320" height="240"></video>
                     <canvas id="canvas" width="320" height="240"></canvas>
                     
                     <div id="captured_photo"></div>
                  </div>
                </div>
                <div class="col-md-8">  
                  <div class="col-md-12">
                    <div class="heading"><strong>Please Select Client</strong></div>
                  </div>
                  <div class="col-md-12">
                    <select class="select2 check_in_out_client" name="client_id" required>
                        <option value="">--</option>
                      @foreach($clients as $client)
                        <option value="{{$client->id}}">{{$client->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-12">
                    <div class="check_in_btn_container">
                      <button  type="button" class="btn check_in_btn" id="check_in_btn" type="submit" onclick="checkin();">
                        IN 
                      </button>

                    {{-- <span style="left: 200px;">TEST DATA</span> --}}
                    </div>
                  </div>
                  <div class="col-md-12">
                    <div class="check_out_btn_container">
                      <button  type="button" class="btn check_out_btn" id="check_out_btn" onclick="checkout();">
                        OUT
                      </button>
                      {{-- <span style="left: 100px">TEST DATA</span> --}}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </form>
    </section>
    <canvas id="CANVAS" name="CANVAS" height="240" width="320px">Your browser does not support Canvas.</canvas>

@endsection
@push('scripts')
  <script src="{{ asset('backend/js/tracking-min.js') }}"></script>
  <script src="{{ asset('backend/js/face-min.js') }}"></script>

    <script>
    window.onload = function() {
      var video = document.getElementById('videoElement');
      var canvas = document.getElementById('canvas');
      var context = canvas.getContext('2d');

      var tracker = new tracking.ObjectTracker('face');
      tracker.setInitialScale(4);
      tracker.setStepSize(2);
      tracker.setEdgesDensity(0.1);

      tracking.track('#videoElement', tracker, { camera: true });

      tracker.on('track', function(event) {
        context.clearRect(0, 0, canvas.width, canvas.height);

        event.data.forEach(function(rect) {
          context.strokeStyle = '#a64ceb';
          context.strokeRect(rect.x, rect.y, rect.width, rect.height);
          context.font = '11px Helvetica';
          context.fillStyle = "#fff";
          context.fillText('Face Detected', rect.x + rect.width + 5, rect.y + 11);
          // context.fillText('x: ' + rect.x + 'px', rect.x + rect.width + 5, rect.y + 11);
          // context.fillText('y: ' + rect.y + 'px', rect.x + rect.width + 5, rect.y + 22);
        });
      });

      var gui = new dat.GUI();
      gui.add(tracker, 'edgesDensity', 0.1, 0.5).step(0.01);
      gui.add(tracker, 'initialScale', 1.0, 10.0).step(0.1);
      gui.add(tracker, 'stepSize', 1, 5).step(0.1);
    };
  </script>
  <script type="text/javascript">
    var video = document.querySelector("#videoElement");

    if (navigator.mediaDevices.getUserMedia) 
    {       
      navigator.mediaDevices.getUserMedia({video: true})
        .then(function(stream) {
          video.srcObject = stream;
         })
        .catch(function(err0r) {
          $('#CANVAS').remove();
          $('.access_error_msg').html('Please Allow Camera Permission');
          $('.access_error_msg').slideDown("slow");
        });
      
      var i=0;

      function capture() {
        var x =  document.getElementById("CANVAS") ;
        var ctx = x.getContext("2d");
        ctx.fillStyle = "#FF0000";
        ctx.drawImage(video, 0, 0, 320, 240);
        if (i <10)
        {
          document.getElementById("captured_photo").appendChild(x);
          $('#videoElement').remove();
          i=i+1;
        }
      }
    }

        function checkin(){
          if(navigator.geolocation) {
            // Proceed only if User allows Location Access
            navigator.geolocation.getCurrentPosition(function(check_in_Position){
              capture();
              var latitude = check_in_Position.coords.latitude;
              var longitude = check_in_Position.coords.longitude;
              var action = "{{route('attendance.checkin')}}";
              var image = document.getElementById("CANVAS").toDataURL();
              $('#user_image').val(image);
              $('#latitude').val(latitude);
              $('#longitude').val(longitude);
              $('form').attr('action',action);
              $('form').submit();
            }, function(){
              $('.access_error_msg').html('You Cannot Login Without Giving Location Access');
              $('.access_error_msg').slideDown("slow");
            });
          } else { 
            x.innerHTML = "Geolocation is not supported by this browser. You cannot Login";
          }
        }

        function checkout(){
            if (navigator.geolocation) {

              // Proceed only if User allows Location Access
              navigator.geolocation.getCurrentPosition(function(check_out_Position){
                capture();
                var latitude = check_out_Position.coords.latitude;
                var longitude = check_out_Position.coords.longitude;
                var action = "{{route('attendance.checkout')}}";
                var image = document.getElementById("CANVAS").toDataURL();
                $('#user_image').val(image);
                $('#latitude').val(latitude);
                $('#longitude').val(longitude);
                $('form').attr('action',action);
                $('form').submit();
              }, function(){
                $('.access_error_msg').html('You Cannot Login Without Giving Location Access');
                $('.access_error_msg').slideDown("slow");
              });
            } else { 
              x.innerHTML = "Geolocation is not supported by this browser.";
            }
        }

        $('.select2').select2();

        function download(){
          var download = document.getElementById("download");
          var image = document.getElementById("CANVAS").toDataURL();
          $('#user_image').val(image);
          
          // download.setAttribute("href", image);
        }
    
  </script>

  <script>
    $(function () {
        $(".check_in_out_client").change();
    });

      $('.check_in_out_client').on('change',function(e){
        e.preventDefault();
        var client_id = $(this).val();
        $.ajax({
          type:'post',
          url:'{{ route("ajax.in_out_stat") }}',
          dataType: 'json',
          data:{
              client_id: client_id                 
          },
          success:function(data) {
            console.log(data);
            if(data==null || data==''){
              console.log('You Need to Log In');
              $('#check_out_btn').prop("disabled",true); 
              $('#check_in_btn').prop("disabled",false); 
            }
            else{
              var check_in_stat = data.check_in;
              var check_out_stat = data.check_out;
              if(check_out_stat==null){
                $('#check_in_btn').prop("disabled",true);
                $('#check_out_btn').prop("disabled",false);  
              }
              else if(check_out_stat!=null){
                $('#check_in_btn').prop("disabled",false);
                $('#check_out_btn').prop("disabled",true);  
              }
              console.log(check_in_stat);
            }
          },
          error: function(response){
          
          }
      });

      });


  </script>
@endpush
