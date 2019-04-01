@php
if(Route::current()->getName() == 'user_employee.index'){
  $title = 'Employees';
  $user_type = 'employee';
}
elseif(Route::current()->getName() == 'user_contractor.index'){
  $title = 'Contractors';
  $user_type = 'contractor';
}
elseif(Route::current()->getName() == 'user_client.index'){
  $title = 'Clients';
  $user_type = 'client';
}

@endphp

@extends('backend.layouts.app',['title'=> $title])

@section('content')

<!-- Main content -->
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
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Employee Information</h3>
        </div>
        <form role="form" action="{{route('user.store')}}" method="POST">
          @csrf
          <div class="box-body pad">
            {{-- Hidden Fields --}}
            <input type="hidden" class="form-control" name="user_type" value="{{$user_type}}">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" required>
              </div>
              <div class="form-group">
                <label for="gender">Gender</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="gender" id="male" value="male" checked="">
                    Male
                  </label>
                  <label>
                    <input type="radio" name="gender" id="female" value="female">
                    Female
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label for="hourly_rate">Base Hourly Rate</label>
                <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Enter Hourly Rate">
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address" required>
              </div>
              <div class="form-group">
                <label for="contact">Phone Number</label>
                <input type="text" name="contact" class="form-control" id="contact" placeholder="Enter Phone Number">
              </div>
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
              </div>
              <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" placeholder="Enter Date of Birth" required>
              </div>
            </div>
            <div class="col-md-6">
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
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description" required></textarea>
              </div>
              <div class="form-group">
                <label for="employment_start_date">Employment Start Date</label>
                <input type="date" name="employment_start_date" class="form-control" id="employment_start_date" placeholder="Enter Enployment Start Date" required>
              </div>
              <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="Enter Password" required>
              </div>
              <div class="form-group">
                <label for="password">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Enter Password" required>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-12">
      <div class="box"> 
        <a href="{{ route('export_to_excel',Route::current()->getName()) }}"><button class="btn btn-success">Download Excel xls</button></a>
       {{--  <div class="box-header">
          <h3 class="box-title">Data Table With Full Features</h3>
        </div> --}}
        <!-- /.box-header -->
        <div class="box-body">
          <table id="users_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Contact No</th>
              <th>Hourly Rate</th>
              <th>Start Date</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
              <tr>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->contact}}</td>
                <td>{{$user->hourly_rate}}</td>
                <td>{{$user->employment_start_date}}</td>
                <td>
                  <a href="{{route('user.edit',$user->id)}}">
                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                  </a>
                  <a href="javascript:;" id="delete_user" data-user_id = '{{$user->id}}'><i class="fa fa-trash" aria-hidden="true"></i>
                  </a>
                </td>
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
    $('#users_table').DataTable()
  });
  $('#delete_user').on('click',function(){
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this data!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  })
  .then((willDelete) => {
    if (willDelete) {
      var user_id = $(this).data('user_id');
      alert(user_id);
      window.location.href = "{{url('delete_user/')}}/"+user_id;
    } 
  });
  });

</script>
  
@endpush