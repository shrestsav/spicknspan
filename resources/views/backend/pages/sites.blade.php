@extends('backend.layouts.app',['title'=> 'Sites'])

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-6">
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
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Add Building</h3>
        </div>
        <form role="form" action="{{route('site.store')}}" method="POST">
          @csrf
          <div class="box-body pad">
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
            <div class="form-group">
              <label for="b_description">Description</label>
              <textarea name="description" class="form-control" id="b_description" placeholder="Enter Additional Information" required></textarea>
            </div>
            <div class="form-group">
              <label for="b_image">Image</label>
              <input type="file" name="image" class="form-control" id="b_image">
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Buildings</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
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
        <!-- /.box-body -->
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Add Rooms</h3>
        </div>
        <form role="form" action="{{route('room.store')}}" method="POST">
          @csrf

          <div class="box-body pad">
            <div class="form-group">
              <select class="select2" name="building_id">
                @foreach($buildings as $building)
                  <option value="{{$building->id}}">{{$building->name}}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label for="r_name">Name</label>
              <input type="text" name="name" class="form-control" id="r_name" placeholder="Enter Name">
            </div>

            <div class="form-group">
              <label for="room_no">Room No</label>
              <input type="text" name="room_no" class="form-control" id="room_no" placeholder="Enter Room Number" required>
            </div>
            <div class="form-group">
              <label for="r_description">Description</label>
              <textarea name="description" class="form-control" id="r_description" placeholder="Enter Additional Information" required></textarea>
            </div>
            <div class="form-group">
              <label for="r_image">Image</label>
              <input type="file" name="image" class="form-control" id="r_image">
            </div>
            <div class="form-group">
              <label for="r_image">Select Questionare</label><br>
              <select class="select2" name="question_id">
                @foreach($questionTemplate as $qT)
                  <option value="{{$qT->id}}">{{$qT->template_title}}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Rooms</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="room_list_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>S.No</th>
              <th>Name</th>
              <th>Room No</th>
              <th>Building No</th>
              <th>Question Template</th>
              <th>QR</th>
            </tr>
            </thead>
            <tbody>
              <?php $count=1; ?>
            @foreach($rooms as $room)
              <tr>
                <td>{{$count}}</td>
                <td>{{$room->name}}</td>
                <td>{{$room->room_no}}</td>
                <td>{{$room->building_no}}</td>
                @foreach($questionTemplate as $qT)
                <?php if($qT->id == $room->question_id){ ?>
                <td>{{$qT->template_title}}</td>
                <?php } ?>
                @endforeach
                <td><a href="{{route('generate.qr',$room->id)}}" target="_blank">Show QR</a></td>
              </tr>
              <?php $count++; ?>
            @endforeach
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
  $(function () {
    $('#building_list_table').DataTable({
      "pageLength": 8
    });
    $('#room_list_table').DataTable({
      "pageLength": 8
    });
  })
</script>
  
@endpush