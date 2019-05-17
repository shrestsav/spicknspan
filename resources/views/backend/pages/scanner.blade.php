@extends('backend.layouts.app',['title'=> 'QR Scanner'])

@push('style')
 <link href="https://fonts.googleapis.com/css?family=Ropa+Sans" rel="stylesheet">
 <style>
    body {
      font-family: 'Ropa Sans', sans-serif;
      color: #333;
      max-width: 640px;
      margin: 0 auto;
      position: relative;
    }

    #githubLink {
      position: absolute;
      right: 0;
      top: 12px;
      color: #2D99FF;
    }

    h1 {
      margin: 10px 0;
      font-size: 40px;
    }

    #loadingMessage {
      text-align: center;
      padding: 40px;
      background-color: #eee;
    }

    #canvas {
      width: 100%;
    }

    #output {
      margin-top: 20px;
      background: #eee;
      padding: 10px;
      padding-bottom: 0;
    }

    #output div {
      padding-bottom: 10px;
      word-wrap: break-word;
    }

    #noQRFound {
      text-align: center;
    }
  </style>
  
@endpush


@section('content')

<!-- Main content -->
<section class="content">
  {{-- To display Errors from Javascript --}}
  <div class="alert alert-danger access_error_msg" style="display: none;">   
    @foreach ($errors->all() as $error)
       {{ $error }}
    @endforeach
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="alert alert-danger" style="display: none;"></div>
      <div class="alert alert-success" style="display: none;"></div>
    </div>
    <input type="hidden" name="lat_long" id="lat_long">
    <div class="col-md-6">
      <div id="loadingMessage">🎥 Unable to access video stream (please make sure you have a webcam or camera enabled)</div>
      <canvas id="canvas" hidden></canvas>
      <div id="output" hidden>
        <div id="outputMessage">No QR code detected.</div>
        <div hidden><b>Data:</b> <span id="outputData"></span></div>
      </div>
    </div>  
  </div>
</section>

@endsection

@push('scripts')

  <script src="{{ asset('backend/js/jsQR.js') }}"></script>
  
  <script>

      if(navigator.geolocation) {
        // Proceed only if User allows Location Access
        navigator.geolocation.getCurrentPosition(function(qr_scanner){
          var latitude = qr_scanner.coords.latitude;
          var longitude = qr_scanner.coords.longitude;
          
          $('#lat_long').val(latitude+','+longitude);

        }, function(){
          $('.access_error_msg').html('Please Enable Location Access First');
          $('.access_error_msg').slideDown("slow");
        });
      } else { 
        x.innerHTML = "Geolocation is not supported by this browser. You cannot Login";
      }









    var video = document.createElement("video");
    var canvasElement = document.getElementById("canvas");
    var canvas = canvasElement.getContext("2d");
    var loadingMessage = document.getElementById("loadingMessage");
    var outputContainer = document.getElementById("output");
    var outputMessage = document.getElementById("outputMessage");
    var outputData = document.getElementById("outputData");

    function drawLine(begin, end, color) {
      canvas.beginPath();
      canvas.moveTo(begin.x, begin.y);
      canvas.lineTo(end.x, end.y);
      canvas.lineWidth = 4;
      canvas.strokeStyle = color;
      canvas.stroke();
    }

    // Use facingMode: environment to attemt to get the front camera on phones
    navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function(stream) {
      video.srcObject = stream;
      video.setAttribute("playsinline", true); // required to tell iOS safari we don't want fullscreen
      video.play();
      requestAnimationFrame(tick);
    });

    function tick() {
      loadingMessage.innerText = "⌛ Loading Scanner ..."
      if (video.readyState === video.HAVE_ENOUGH_DATA) {
        loadingMessage.hidden = true;
        canvasElement.hidden = false;
        outputContainer.hidden = false;

        canvasElement.height = video.videoHeight;
        canvasElement.width = video.videoWidth;
        canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
        var imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
        var code = jsQR(imageData.data, imageData.width, imageData.height, {
          inversionAttempts: "dontInvert",
        });
        if (code) {
          drawLine(code.location.topLeftCorner, code.location.topRightCorner, "#56c318");
          drawLine(code.location.topRightCorner, code.location.bottomRightCorner, "#56c318");
          drawLine(code.location.bottomRightCorner, code.location.bottomLeftCorner, "#56c318");
          drawLine(code.location.bottomLeftCorner, code.location.topLeftCorner, "#56c318");
          outputMessage.hidden = true;
          outputData.parentElement.hidden = false;
          outputData.innerText = code.data;

          var lat_long = $('#lat_long').val();
          // swal({
          //   title: "Room "+code.data+" Connected",
          //   text: "Do you want to Log in ?",
          //   icon: "success",
          //   buttons: ["Cancel", "LOG ME IN"],
          // })
          // .then((login) => {
          // if (login) {

            
              $.ajax({
                  type:'post',
                  url:'{{ route("ajax.qrLogin") }}',
                  dataType: 'json',
                  data:{
                      room_id:code.data,
                      lat_long:lat_long
                  },
                  success:function(data) {
                      console.log(data);
                      swal({
                        title: "Logged into Room No "+data.room_no+" Successfully",
                        text: "",
                        icon: "success",
                        type: "success",
                        timer: 2500
                      })
                      .then((value) => {
                        window.location.href = "{{route('site.attendance')}}";
                      });
                  },
                  error: function(response){
                    $.each(response.responseJSON, function(index, val){
                      swal({
                        title: "Room doesnot exists in our system",
                        text: "Make sure you are scanning the right QR Code",
                        icon: "warning",
                      })
                      .then((value) => {
                        window.location.href = "/scanner";
                      });
                    });
                  }
              });
            
           
          // }
          // else{
          //   window.location.href = "/scanner";
          // }
        // });
          return;
        } else {
          outputMessage.hidden = false;
          outputData.parentElement.hidden = true;
        }
      }
      requestAnimationFrame(tick);
    }

  </script>
  
@endpush





















