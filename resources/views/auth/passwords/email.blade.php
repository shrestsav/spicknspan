@extends('layouts.log',['title'=> 'Change Password'])
@section('content')

<div class="login-box">
  <div class="login-logo">
    <a href="/"><img src="{{ asset('backend/img/company-logo.png') }}"></a>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">RESET PASSWORD</p>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group has-feedback">
            <input id="email" type="email" placeholder="{{ __('Enter Your Email') }}"  class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required>
            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-8 col-xs-offset-2">
              <button type="submit" class="btn btn-primary btn-block btn-flat"> {{ __('Send Password Reset Link') }}</button>
            </div>
        </div>
    </form>
  </div>
</div>


@endsection
