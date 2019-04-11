@php $userID = Auth::id(); @endphp

@extends('backend.layouts.app',['title'=> 'Edit Password'])

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
        <form role="form" action="{{route('password.update', $userID)}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
          @csrf
          <div class="box-body pad">
            {{-- Hidden Fields --}}
            {{-- <input type="hidden" class="form-control" name="user_type" value="{{$user->user_type}}"> --}}

            <div class="col-md-6">

            {{-- <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{$user->name}}" required>
              </div> --}}
              
              <div class="form-group">
                <label for="password">Old Password *</label>
                <input type="password" name="old_password" class="form-control" id="old_password" placeholder="Enter Old Password" required>
                <div class="help-block with-errors"></div>
              </div>

              <div class="form-group">
                <label for="password">New Password *</label>
                <input type="password" name="password" data-minlength="6" class="form-control" id="password" placeholder="Enter New Password" required>
                <div class="help-block with-errors"></div>
              </div>

              <div class="form-group">
                <label for="password">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm New Password" data-match="#password" data-match-error="Passwords don't match"  required>
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
