@extends('backend.layouts.app',['title'=>'Check In / Out'])

@push('styles')
<style type="text/css">
  .check_in_btn_container,.check_out_btn_container{
    margin-top: 20px;
  }
  .check_in_btn, .check_out_btn{
    margin: 10px;
    font-weight: 600;
    font-size: 25px;
    padding: 15px 10px;
    border-radius: 60px;
    height: 100px;
    width: 100px;
    color: white;
  }
  .check_in_btn{
    border: 3px solid #338000;
    background-color: #63b929;
  }
  .check_in_btn:hover{
    background-color: #509821;
    color: white;
  }
  .check_out_btn{
    border: 3px solid #940000;
    background-color: #e20000;
  }
  .check_out_btn:hover{
    background-color: #ff0000;
    color: white;
  }
  .check_in_out_body{
    min-height: 300px;
    text-align: center;
  }
  .check_in_out_client{
    padding: 10px;
    width: 300px;
  }


  video,canvas{
    margin-left: -80px;
    margin-top: 20px;
    position: absolute;
  }
  .last_check_in_out{
     padding: 15px;
  }
  .text{
    padding: 20px 0px 30px 0px;
    font-size: 1.8rem;
  }
  @media(max-width: 991px) {
    video, canvas {
      margin-left: -150px;
      width: 300px;
      height: 220px;
    }
    .check_in_out_video_opp{
      margin-top: 250px;
    }
    .check_in_out_client{
      width: 200px;
    }
  }
  @media(max-width: 420px) {
    video, canvas {
      margin-left: -150px;
      width: 300px;
      height: 220px;
    }
    .check_in_out_video_opp{
      margin-top: 250px;
    }
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
              <div class="box-body check_in_out_body">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="image" id="user_image" required>
                <div class="col-md-4 check_in_out_video">  
                  <div id="container"  name='cont' class="container-fluid no-padding ">
                    <video autoplay="true" id="videoElement" name='vid' width="320" height="240"></video>
                    <canvas id="canvas" width="320" height="240"></canvas>
                    <div id="captured_photo"></div>
                  </div>
                </div>
                <div class="col-md-8 check_in_out_video_opp">  
                  <div class="col-md-12">
                    <div class="text">
                      <strong>Please Select Client</strong>
                    </div>
                  </div>
                  <div class="col-md-12">
                    <select class="select2 check_in_out_client" name="client_id" required>
                        <option value="">--</option>
                      @foreach($clients as $client)
                        <option value="{{$client->id}}">{{$client->name}}</option>
                      @endforeach
                    </select>
                  </div>
                  <div class="col-md-12 check_in_btn_container" style="display: none;">
                    <button  type="button" class="btn check_in_btn" id="check_in_btn" type="submit" onclick="checkin();">
                      IN 
                    </button>
                  </div>
                  <div class="col-md-12 check_out_btn_container" style="display: none;">
                    <button  type="button" class="btn check_out_btn" id="check_out_btn" onclick="checkout();">
                      OUT
                    </button>
                  </div>
                  <div class="col-md-12">
                    <div class="last_check_in_out"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <canvas id="CANVAS" name="CANVAS" height="240" width="320">Your browser does not support Canvas.</canvas>
        </div>
      </form>
    </section>

@endsection

@push('scripts')
  <script src="{{ asset('backend/js/tracking-min.js') }}"></script>
  <script src="{{ asset('backend/js/face-min.js') }}"></script>
  <script type="text/javascript">
    $(function () {
      var last_check_in_client = '{{$last_check_in_out_client}}';
      $(".check_in_out_client").val(last_check_in_client).change();
    });
    var height = 240;
    var width = 320;
    var deviceIsMobile = false; 

    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent) 
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) {
       deviceIsMobile = true;
    }

    if(deviceIsMobile){
       var height = 320;
       var width = 240;
       $('#CANVAS').attr('height',height);
       $('#CANVAS').attr('width',width);
       $('.check_in_out_client').removeClass('select2');
       // $(".select2").select2("destroy");
      //  $(".select2").select2({
      //    minimumResultsForSearch: Infinity
      // });
    }
  </script>

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
        ctx.drawImage(video, 0, 0, width, height);
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

        //Currently not in use
        // function download(){
        //   var download = document.getElementById("download");
        //   var image = document.getElementById("CANVAS").toDataURL();
        //   $('#user_image').val(image);
          
          // download.setAttribute("href", image);
        // }
    
  </script>

  <script>

      $('.check_in_out_client').on('change',function(e){
        $('.last_check_in_out').html('');
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
              $('.check_out_btn_container').hide(); 
              $('.check_in_btn_container').show(); 
              $('.last_check_in_out').html('Please Log In');
            }
            else{
              var check_in_stat = data.check_in;
              var check_out_stat = data.check_out;
              if(check_out_stat==null){
                $('.check_in_btn_container').hide(); 
                $('.check_out_btn_container').show(); 
                let localTime = moment.utc(check_in_stat).tz('{{Session::get('timezone')}}').format('YYYY-MM-DD HH:mm');
                $('.last_check_in_out').html('Last Login : '+localTime);
              }
              else if(check_out_stat!=null){
                let localTime = moment.utc(check_out_stat).tz('{{Session::get('timezone')}}').format('YYYY-MM-DD HH:mm');
                $('.check_out_btn_container').hide(); 
                $('.check_in_btn_container').show(); 
                $('.last_check_in_out').html('Last Logout : '+localTime);
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
