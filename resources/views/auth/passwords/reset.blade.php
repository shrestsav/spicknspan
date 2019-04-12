@extends('layouts.log',['title'=> 'Change Password'])
@section('content')

<div class="login-box">
  <div class="login-logo">
    <a href="/"><img src="{{ asset('backend/img/company-logo.png') }}"></a>
  </div>

  <div class="login-box-body">
    <p class="login-box-msg">RESET PASSWORD</p>
    <form method="POST" action="{{ url('password/reset') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="form-group has-feedback">
            <input id="email" type="email" placeholder="{{ __('Enter Your Email') }}" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required autofocus>
            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input id="password" placeholder="{{ __('Password') }}" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="form-group has-feedback">
            <input id="password-confirm" type="password" placeholder="{{ __('Confirm Password') }}" class="form-control" name="password_confirmation" required>
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
        </div>
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
              <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Reset Password') }}</button>
            </div>
        </div>
    </form>
  </div>
</div>

@endsection
