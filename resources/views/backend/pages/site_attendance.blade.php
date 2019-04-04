@extends('backend.layouts.app',['title'=> 'Site Attendance'])

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Logged in Users</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="site_attendance_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Name</th>
              <th>Room No</th>
              <th>Building No</th>
              <th>Address</th>
              <th>Login Time</th>
            </tr>
            </thead>
            <tbody>
            @foreach($site_attendances as $site_attendance)
              <tr>
                <td>{{$site_attendance->name}}</td>
                <td>{{$site_attendance->room_no}}</td>
                <td>{{$site_attendance->building_no}}</td>
                <td>{{$site_attendance->address}}</td>
                <td>{{$site_attendance->login}}</td>
              </tr>
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
    $('#site_attendance_table').DataTable({
      "pageLength": 8
    });
  })
</script>
  
@endpush