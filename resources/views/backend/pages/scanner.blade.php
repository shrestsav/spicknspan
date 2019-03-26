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
  <div class="row">
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
          swal({
            title: "Room "+code.data+" Connected",
            text: "Do you want to Log in ?",
            icon: "success",
            buttons: ["Cancel", "LOG ME IN"],
          })
          .then((login) => {
          if (login) {
            swal("LOGGED INTO ROOM "+code.data+" SUCCESSFULLY !", {
              type: "success",
              timer: 1200
            })
            .then((value) => {

                  $.ajax({
                          type:'post',
                          url:'{{ route("ajax.qrLogin") }}',
                          dataType: 'json',
                          data:{
                              room_id:code.data                 
                          },
                          success:function(data) {
                              console.log(data);
                              window.location.href = "{{route('site.attendance')}}";
                          },
                          error: function(response){
                          
                          }
                      });
               
            });
           
          }
          else{
            window.location.href = "/scanner";
          }
        });
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





















