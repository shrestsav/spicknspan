@extends('backend.layouts.app',['title'=>'Mail'])

@section('content')

    <!-- Main content -->
    <section class="content">

      <div class="row">
        
        <div class="col-md-3">

          @if ( Route::current()->getName() == 'mail.compose') 

            <a href="{{route('mail.index')}}" class="btn btn-primary btn-block margin-bottom">Back to Inbox</a>

          @elseif ( Route::current()->getName() == 'mail.index' || Route::current()->getName() == 'mail.view') 

            <a href="{{route('mail.compose')}}" class="btn btn-primary btn-block margin-bottom">Compose</a>

          @endif

          @include('backend.layouts.includes.mail.__mail_sidebar')

        </div>

        <div class="col-md-9">

          @if ( Route::current()->getName() == 'mail.index' )  

            @include('backend.layouts.includes.mail.__inbox')

          @elseif ( Route::current()->getName() == 'mail.compose')  

            @include('backend.layouts.includes.mail.__compose_mail')

          @elseif ( Route::current()->getName() == 'mail.view')  

            @include('backend.layouts.includes.mail.__view_mail')

          @endif

        </div>

      </div>

    </section>

@endsection