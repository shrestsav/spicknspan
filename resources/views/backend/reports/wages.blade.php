@extends('backend.layouts.app',['title'=>'Wages Report'])

@section('content')

  <section class="content" style="padding-top: 50px;">
    <div class="row">
      <div class="col-lg-12">
        <div class="box box-default{{--  collapsed-box --}} box-solid">
         {{--  <div class="box-header">
            <h3 class="box-title">IMPORT FROM EXCEL
              <small>Format: xlsx</small>
            </h3>
            <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                <i class="fa fa-plus"></i></button>
              <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                <i class="fa fa-times"></i></button>
            </div>
          </div> --}}
          <div class="box-body">
            <form autocomplete="off" role="form" action="{{route('wagesReport.filter')}}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="col-md-12" style="text-align: center;">              
                <select class="select2" name="search_by_employee_id" id="sel_emp">
                  <option disabled selected value>Employee Name</option>
                  @foreach($employees as $emp)
                    <option value="{{$emp->id}}" {{-- @if(Request::input('search_by_employee_id')==$user_id) selected @endif --}}>{{$emp->name}}</option>
                  @endforeach
                </select>
                <select class="select2" name="search_by_client_id" id="sel_cli">
                  <option disabled selected value>Client Name</option>
                  @foreach($clients as $client)
                    <option value="{{$client->id}}" {{-- @if(Request::input('search_by_client_id')==$client_id) selected @endif --}}>{{$client->name}}</option>
                  @endforeach
                </select>
                <div class="input-group search_by_date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  @php
                    $today = date('m/d/Y');
                    $pastOneMonth = date("m/d/Y", strtotime( date( "m/d/Y", strtotime( date("m/d/Y") ) ) . "-1 month" ) );
                  @endphp
                  <input type="text" class="form-control pull-right" id="search_date_from_to" name="search_date_from_to" @if(Request::input('search_date_from_to')) value="{{Request::input('search_date_from_to')}}" @else value="{{$pastOneMonth.' - '.$today}}" @endif>
                </div>
                &nbsp; &nbsp; &nbsp;
                <button type="submit" class="btn btn-primary">Search</button>
              </div>  
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div id="app">
  {{-- {{ message }} --}}
</div>
@endsection

@push('scripts')
<!-- production version, optimized for size and speed -->
{{-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> --}}
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

<script type="text/javascript">
var app = new Vue({
  el: '#app',
  data: {
    message: 'Hello Vue!'
  }
})
</script>
@endpush

