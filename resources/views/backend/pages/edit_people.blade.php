{{-- @php
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

@endphp --}}

@extends('backend.layouts.app',['title'=> 'Edit User'])

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
        <form role="form" action="{{route('user.update',$user->id)}}" method="POST" data-toggle="validator">
          @csrf
          <div class="box-body pad">
            {{-- Hidden Fields --}}
            <input type="hidden" class="form-control" name="user_type" value="{{$user->user_type}}">
            <div class="col-md-6">
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{$user->name}}" required>
              </div>
              <div class="form-group">
                <label for="gender">Gender</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="gender" id="male" value="male" @if($user->gender=='male') checked @endif>
                    Male
                  </label>
                  <label>
                    <input type="radio" name="gender" id="female" value="female" @if($user->gender=='female') checked @endif>
                    Female
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label for="hourly_rate">Base Hourly Rate</label>
                <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Enter Hourly Rate" value="{{$user->hourly_rate}}">
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address" value="{{$user->address}}">
              </div>
              <div class="form-group">
                <label for="contact">Phone Number</label>
                <input type="text" name="contact" class="form-control" id="contact" placeholder="Enter Phone Number" value="{{$user->contact}}" required>
              </div>
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="{{$user->email}}" required>
              </div>
              <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" placeholder="Enter Date of Birth" value="{{$user->date_of_birth}}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="photo">Photo</label>
                <input type="file" name="photo" class="form-control" id="photo" value="{{$user->photo}}">
              </div>
              <div class="form-group">
                <label for="annual_salary">Annual Salary</label>
                <input type="number" name="annual_salary" class="form-control" id="annual_salary" placeholder="Enter Annual Salary" value="{{$user->annual_salary}}">
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description">{{$user->description}}</textarea>
              </div>
              <div class="form-group">
                <label for="employment_start_date">Employment Start Date</label>
                <input type="date" name="employment_start_date" class="form-control" id="employment_start_date" placeholder="Enter Enployment Start Date" value="{{$user->employment_start_date}}" required>
              </div>
              <div class="form-group">
                <label for="timezone">Timezone</label><br>
                <select class="select2" id="timezone" name="timezone" required>
                  {{-- Timezones will be filled by moment js --}}
                </select>
                <div class="help-block with-errors"></div>
              </div>
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
  <script type="text/javascript">
    //To load all the timezones provided by moment-timezone-data
    var timezone = moment.tz.names();
    for (var i = 0; i < timezone.length; i++) {
      $('#timezone').append('<option value="' + timezone[i] + '">' + timezone[i] + '</option>');
    }
    //Guesses the current timezone automatically 
    var timezone = '{{$user->timezone}}';
    $("#timezone").val(timezone).change();
  </script>
@endpush