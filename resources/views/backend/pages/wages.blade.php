@extends('backend.layouts.app',['title'=> 'Wages'])

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
          <h3 class="box-title">Employee Information</h3>
        </div>
        <form role="form" action="{{route('wages.store')}}" method="POST">
          @csrf
          <div class="box-body pad">
            <div class="form-group">
              <label for="photo">Photo</label>
              <input type="file" name="photo" class="form-control" id="photo" required>
            </div>
            <div class="form-group">
              <label for="annual_salary">Annual Salary</label>
              <input type="number" name="annual_salary" class="form-control" id="annual_salary" placeholder="Enter Annual Salary">
            </div>
            <div class="form-group">
              <label for="description">Description</label>
              <textarea name="description" class="form-control" id="description" placeholder="Enter Annual Salary" required></textarea>
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
          <h3 class="box-title">Employee Wages List</h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <table id="employee_wages_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Employee</th>
              <th>Client</th>
              <th>Hourly Rate</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            {{-- @foreach($users as $user)
              <tr>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->contact}}</td>
                <td>{{$user->hourly_rate}}</td>
                <td>{{$user->employment_start_date}}</td>
              </tr>
            @endforeach --}}
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
    $('#employee_wages_table').DataTable()
  })
</script>
  
@endpush