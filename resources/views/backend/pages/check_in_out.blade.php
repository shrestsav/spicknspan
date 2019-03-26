@extends('backend.layouts.app',['title'=>'Check In / Out'])

@section('content')
 
    <!-- Main content -->
    <section class="content" style="padding-top: 50px;">
      <!-- /.row -->
      @if ($errors->any())
          <div class="alert alert-danger custom_error_msg">   
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
        
      
      <form method='POST' action='' class='check_in_form'>
        @csrf
        <div class="row">
          <div class="col-md-12">
            <div class="box box-success">
              <div class="box-body check_in_body">
                <select class="select2" name="client">
                  @foreach($clients as $client)
                    <option value="{{$client->id}}">{{$client->name}}</option>
                  @endforeach
                </select>
                <div class="check_in_btn_container">
                  <a class="btn check_in_btn" type="submit" onclick="checkin();">
                    IN
                  </a>
                </div>
                <div class="check_in_btn_container">
                  <a class="btn check_in_btn" onclick="checkout();">
                    OUT
                  </a>
                </div>
                <button type="submit"></button>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->
          </div>
        </div>
      </form>
    </section>
    <!-- /.content -->

@endsection
@push('scripts')
  <script type="text/javascript">
      function checkin(){
        var action = "{{route('attendance.checkin')}}";
        $('form').attr('action',action);
        $('form').submit();
      }
      function checkout(){
        var action = "{{route('attendance.checkout')}}";
        $('form').attr('action',action);
        $('form').submit();
      }
  </script>
@endpush
