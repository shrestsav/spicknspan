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
                <input type="hidden" name="image" id="user_image" required>
                <div class="row">
                  
                  <div id="container"  name='cont' class="container-fluid no-padding ">
                     <video autoplay="true" id="videoElement" name='vid'>
                      
                     </video>
                     <div id="captured_photo"></div>
                  </div>

                  <a id="download" download="snap.jpg">
                    <button onclick="myFunction(); download();" align="center" style="margin: 20px 250px auto " class="btn btn-primary dropdown-toggle" type="button" >capture</button>
                  </a>
                </div>

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

                
                {{-- <button type="submit"></button> --}}
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->
    <canvas id="CANVAS" name="CANVAS" width="400" height="400">Your browser does not support Canvas.</canvas>
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
    
  </script>

  <script>

    function download(){
      var download = document.getElementById("download");
      var image = document.getElementById("CANVAS").toDataURL();
      $('#user_image').val(image);
      
      // download.setAttribute("href", image);
    }

    var video = document.querySelector("#videoElement");

    if (navigator.mediaDevices.getUserMedia) 
    {       
       navigator.mediaDevices.getUserMedia({video: true})
        .then(function(stream) {
          video.srcObject = stream;
         })
        .catch(function(err0r) {
          console.log("Something went wrong!");
        });
      
      var i=0;
      function myFunction() {
        var x =  document.getElementById("CANVAS") ;
        var ctx = x.getContext("2d");
        ctx.fillStyle = "#FF0000";
        ctx.drawImage(video, 0, 0, 400, 350);
        if (i <10)
        {
          document.getElementById("captured_photo").appendChild(x);
          $('#videoElement').remove();
          i=i+1;
        }
      }
    }
</script>
@endpush
