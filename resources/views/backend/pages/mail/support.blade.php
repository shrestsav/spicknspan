@extends('layouts.log',['title'=> 'Change Password'])
@section('content')

<div class="login-logo">
  <a href="/"><img src="{{ asset('backend/img/company-logo.png') }}"></a>
</div>
<section class="content">
  <div class="row">
    <div class="col-md-6 col-md-offset-3">
      @if ($errors->any())
          <div class="alert alert-danger">
              @foreach ($errors->all() as $error)
                  {{ $error }}<br>
              @endforeach
          </div>
      @endif
      @if (\Session::has('message'))
        <div class="alert alert-success custom_success_msg">
            {{ \Session::get('message') }}
        </div>
      @endif
      <div class="box box-primary pad">
        <div class="box-header with-border text-center">
          <h1 class="box-title">SUPPORT DESK</h1>
        </div>
        <form role="form" action="{{route('support')}}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="box-body">
            <div class="form-group">
              <input class="form-control" type="text" name="name" placeholder="Name" required>
            </div>
            <div class="form-group">
              <input class="form-control" type="email" name="email" placeholder="Email :  Enter Your Email Address" required>
            </div>
            <div class="form-group">
              <input class="form-control" type="text" name="contact" placeholder="Phone :  Enter Your Contact Address">
            </div>
            <div class="form-group">
              <input class="form-control" type="text" name="subject" placeholder="Subject :  Enter Subject of Troubleshoot" required>
            </div>
            <div class="form-group">
                  <textarea id="compose-textarea" name="message" class="form-control" style="height: 300px" required>
                    
                  </textarea>
            </div>
          </div>
          <div class="box-footer">
            <div class="pull-right">
              <button type="submit" class="btn btn-primary"><i class="fa fa-envelope-o"></i> &nbsp;Send</button>
            </div>
            <a href="{{url('/')}}"><button type="button" class="btn btn-default"><i class="fa fa-arrow-left"></i> GO BACK</button></a>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection