@extends('layouts.log',['title'=> 'Change Password'])
@section('content')

<div class="login-box">
  <div class="login-logo">
    <a href="/"><img src="{{ asset('backend/img/company-logo.png') }}"></a>
  </div>

  @if (\Session::has('error'))
    <div class="alert alert-danger">
        {{ \Session::get('error') }}
    </div>
  @endif
  @if(\Session::has('TokenMismatchException'))
      <div class="alert alert-block alert-danger">
          {{ \Session::get('TokenMismatchException') }}
      </div>
  @endif
  <div class="login-box-body">
    <p class="login-box-msg">LOGIN</p>
    <form method="POST" action="{{ route('login') }}">
        @csrf
      <div class="form-group has-feedback">
        <input type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('E-Mail Address') }}" name="email" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback">
        <input type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" name="password" required>
        @if ($errors->has('password'))
            <span class="invalid-feedback" role="alert">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}> &nbsp;&nbsp;{{ __('Remember Me') }}
            </label>
          </div>
        </div>
        <div class="col-xs-4">
          <button type="submit" class="btn btn-primary btn-block btn-flat">{{ __('Login') }}</button>
        </div>
      </div>
    </form>

    @if (Route::has('password.request'))
        <a class="btn btn-link" href="{{ route('password.request') }}">
            {{ __('Forgot Your Password?') }}
        </a>
    @endif
    <a class="btn btn-link" href="{{ route('support') }}">
        {{ __('Click Here for Support') }}
    </a>
    <br>
    <br>
    <a class="btn btn-block btn-social btn-google" href="{{ route('login.provider', 'google') }}">
      <i class="fa fa-google"></i> Sign in with Google
    </a>
    <a class="btn btn-block btn-social btn-facebook" href="{{ route('login.provider', 'facebook') }}">
      <i class="fa fa-facebook"></i> Sign in with Facebook
    </a>
  </div>
</div>

@endsection