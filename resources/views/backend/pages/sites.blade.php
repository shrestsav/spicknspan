@extends('backend.layouts.app',['title'=> 'Sites'])

@push('styles')
<style type="text/css">
  select{
    width: 200px;
  }
</style>
@endpush
@section('content')

<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if ($errors->any())
          <div class="alert alert-danger">
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
      <div class="box box-primary collapsed-box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Add Building</h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body pad">
          <form role="form" action="{{route('site.store')}}" method="POST">
            @csrf
            <div class="col-md-6">
              <div class="form-group">
                <label for="b_name">Name</label>
                <input type="text" name="name" class="form-control" id="b_name" placeholder="Enter Name">
              </div>
              <div class="form-group">
                <label for="building_no">Building No</label>
                <input type="text" name="building_no" class="form-control" id="building_no" placeholder="Enter Building Number" required>
              </div>
              <div class="form-group">
                <label for="b_address">Address</label>
                <input type="text" name="address" class="form-control" id="b_address" placeholder="Enter Address" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="b_image">Image</label><br>
                <input type="file" name="image" class="form-control jfilestyle" id="b_image">
              </div>
              <div class="form-group">
                <label for="b_description">Description</label>
                <textarea name="description" rows="4" class="form-control" id="b_description" placeholder="Enter Additional Information" required></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Buildings</h3>
        </div>
        <div class="box-body table-responsive no-padding">
          <table id="building_list_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Name</th>
              <th>Building No</th>
              <th>Address</th>
              <th>Description</th>
            </tr>
            </thead>
            <tbody>
            @foreach($buildings as $building)
              <tr>
                <td>{{$building->name}}</td>
                <td>{{$building->building_no}}</td>
                <td>{{$building->address}}</td>
                <td>{{$building->description}}</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="box-footer clearfix">
          <div class="col-md-4 col-md-offset-4 text-center">
            {{ $buildings->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row" id="area_division">
    <div class="col-md-12">
      <div class="box box-primary collapsed-box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Create Division/Area</h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body pad">
          <form role="form" action="{{route('room.store')}}" method="POST">
            @csrf
            <div class="col-md-6">
              <div class="col-md-6 no-padding">
                <div class="form-group">
                  <label for="">Select Building</label><br>
                  <select class="select2" name="building_id">
                    @foreach($buildings as $building)
                      <option value="{{$building->id}}">{{$building->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-6 no-padding">
                @if(Auth::user()->hasRole(['superAdmin','contractor']) && Auth::user()->inspection == '1')
                  <div class="form-group">
                    <label for="r_image">Select Questionare</label><br>
                    <select class="select2" name="question_id">
                      @foreach($questionTemplate as $qt)
                        <option value="{{$qt->id}}">{{$qt->template_title}}</option>
                      @endforeach
                    </select>
                  </div>
                @endif
              </div>
              <div class="form-group">
                <label for="r_name">Name</label>
                <input type="text" name="name" class="form-control" id="r_name" placeholder="Enter Name">
              </div>
              <div class="form-group">
                <label for="room_no">Room No</label>
                <input type="text" name="room_no" class="form-control" id="room_no" placeholder="Enter Room Number" required>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="r_image">Image</label><br>
                <input type="file" name="image" class="form-control jfilestyle" id="r_image">
              </div>
              <div class="form-group">
                <label for="r_description">Description</label>
                <textarea name="description" rows="4" class="form-control" id="r_description" placeholder="Enter Additional Information" required></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Division / Area</h3>
        </div>
        <div class="box-body table-responsive no-padding">
          <table id="room_list_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>
                <div class="print_qr" style="display: none;">
                  <i class="fa fa-print" aria-hidden="true"></i>
                </div>
              </th>
              <th>S.No</th>
              <th>Name</th>
              <th>Room No</th>
              <th>Description</th>
              <th>Building No</th>
              @if(Auth::user()->hasRole(['superAdmin','contractor']) && Auth::user()->inspection == '1')
                  <th>Question Template</th>
              @endif
              <th>QR</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
              @php $count = 1; @endphp
            @foreach($rooms as $room)
              <tr>
                <td><input type="checkbox" class="room_check" data-room-id="{{$room->id}}"></td>
                <td>{{$count}}</td>
                <td>{{$room->name}}</td>
                <td>{{$room->room_no}}</td>
                <td>{{$room->description}}</td>
                <td>{{$room->building_no}}</td>
                <td>
                  @if($room->template_title)
                    {{$room->template_title}}
                  @else
                    --
                  @endif
                </td>

                <td><a href="{{route('generate.qr',json_encode([$room->id]))}}" target="_blank">Show QR</a></td>
                <form action="{{ url('/site/delete_room/').'/'.$room->id}}" method="POST">
                  {{ csrf_field() }}
                  <input type="hidden" name="_method" value="POST">
                  <td><button><i class="fa fa-trash-o"></i></button></td>
                </form>
              </tr>
              @php $count++; @endphp
            @endforeach
            </tbody>
          </table>
        </div>
        <div class="box-footer clearfix">
          <div class="col-md-4 col-md-offset-4 text-center">
            {{ $rooms->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
  
    //DELETE CHECKBOX COUNT STARTS
    (function($) {
      
      "use_strict";

      // This array will store the values of the "checked" room-id checkboxes
      var cboxArray = [];
            
      // Check if the room-id has already been added to the array and if not - add it
      function itemExistsChecker(cboxValue) {          
        var len = cboxArray.length;
        if (len > 0) {
          for (var i = 0; i < len; i++) {
            if (cboxArray[i] == cboxValue) {
              return true;
            }
          }
        }       
        cboxArray.push(cboxValue);
      } 
            
      $('input[type="checkbox"]').each(function() {  
        var cboxValue = $(this).data('room-id');
            
        // On checkbox change add/remove the room-id from the array based on the choice
        $(this).change(function() {

          //Display Print button only if any one of checkbox is checked
           if ($('.room_check').is(':checked'))
                $('.print_qr').show();
            else
                $('.print_qr').hide();

          if ($(this).is(':checked')) {
            itemExistsChecker(cboxValue);
          } else {

            // Delete room-id from the array if its checkbox is unchecked
            var cboxValueIndex = cboxArray.indexOf(cboxValue);
            if (cboxValueIndex >= 0) {
              cboxArray.splice( cboxValueIndex, 1 );
            }
          }
          // console.log(cboxArray);
        });
      });

      $('.print_qr').on('click', function () {
        var serialize = JSON.stringify(cboxArray);
        window.location = SITE_URL+"generate_qr/"+serialize;
      });
})(jQuery);


  //DELETE CHECKBOX COUNT ENDS
</script>
@endpush