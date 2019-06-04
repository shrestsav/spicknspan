@php

if(Route::current()->getName() == 'leaveRequest.index' || Route::current()->getName() == 'leaveRequest.search'){
  $page = 1;
  $title = 'Leave Applications';
}
if(Route::current()->getName() == 'archivedleaveRequest.index' || Route::current()->getName() == 'archivedleaveRequest.search'){
  $page = 0;
  $title = 'Archived Leave Applications';
}

@endphp



@extends('backend.layouts.app',['title'=>$title])

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/excel-plugins/tableexport.css') }}">
<style type="text/css">
  label.checkbox.mark_default {
    padding-left: 20px;
  }
  .export_to_excel{
    position: absolute;
    top: 7px;
    right: 227px;
    z-index: 1000;
  }

</style>

@endpush

@section('content')


<section class="content">
  <div class="row">

    @if($page)
      <div class="col-md-12">
        <div class="box box-primary collapsed-box box-solid">
          <div class="box-header with-border">
            <h3 class="box-title">Create Leave Application</h3>
            <div class="pull-right box-tools">
              <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
                <i class="fa fa-plus"></i></button>
              <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
                <i class="fa fa-times"></i></button>
            </div>
          </div>
          <div class="box-body padding">
            <form role="form" action="{{route('leaveRequest.store')}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
              @csrf
              <div class="col-md-6 col-md-offset-3">
                <div class="form-group">
                  <label>Select the type of leave you are requesting</label>
                  <select name="leave_type" class="form-control select2" required style="width: 100%">
                    <option value disabled selected>Select Leave Type</option>
                    @foreach(config('setting.leave_types') as $id => $leave_typ)
                      <option value="{{$id}}">{{$leave_typ}}</option>
                    @endforeach
                  </select>
                </div>

                <div class="form-group">
                  <label for="">Select Your Leave Period</label>
                  <div class="input-group">
                    <div class="input-group-addon">
                      <i class="fa fa-calendar"></i>
                    </div>
                    @php
                      $today = date('m/d/Y');
                      $postOneMonth = date("m/d/Y", strtotime( date( "m/d/Y", strtotime( date("m/d/Y") ) ) . "+1 month" ) );
                    @endphp
                    <input type="text" class="form-control pull-right" id="from_to" name="from_to" value="{{$today.' - '.$postOneMonth}}" required>
                  </div>
                </div>
                <div class="form-group">
                  <label for="">Describe your reasons for leave</label>
                  <textarea class="form-control" rows="4" name="description" placeholder="Describe why you need this leave" maxlength="" required></textarea>
                </div>
              </div>
              <div class="col-md-6 col-md-offset-3">
                <div class="box-footer">
                  <button type="Submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
          
        </div>
      </div>
    @endif
    @if($leave_requests)
    <div class="col-xs-12">
      @if(Request::all())
        <a href="@if($page){{url('/leaveApplication')}} @else {{url('/archivedLeaveApplication')}} @endif"><button class="btn btn-primary">Go Back</button></a>
      @endif
      <div class="box">
        <div class="box-header">
          {{-- Search Form --}}
          <div class="search_form">
            <form autocomplete="off" role="form" action="@if($page){{route('leaveRequest.search')}} @else {{route('archivedleaveRequest.search')}} @endif" method="POST" enctype="multipart/form-data">
              @csrf
              @php 
                $search_arr = [
                  'Employee Name' => [
                    'class'   => 'search_by_employee_id',
                    'name'    => 'search_by_employee_id',
                    'value'   => 'employee_id',
                    'view'    => 'name'
                  ],
                ]
              @endphp

              @foreach($search_arr as $part => $arr)
                <select class="select2 {{$arr['class']}}" name="{{$arr['name']}}">
                  <option disabled selected value> {{$part}}</option>
                  @foreach($leave_requests->unique($arr['value']) as $lr)
                    @php 
                      $val = $lr->{$arr['value']};
                    @endphp
                    <option value="{{$val}}" @if(Request::input('search_by_'.$arr['value'])==$val) selected @endif>
                      {{$lr->{$arr['view']} }}
                    </option>
                  @endforeach
                </select>
              @endforeach
               
              <select class="select2" name="search_by_lt">
                <option disabled selected value>Leave Type</option>
                @foreach(config('setting.leave_types') as $id => $lt)
                  <option value="{{$id}}" @if(Request::input('search_by_lt')==$id) selected @endif>{{$lt}}</option>
                @endforeach
              </select>
              <select class="select2" name="search_by_status">
                <option disabled selected value>Status</option>
                @foreach(config('setting.status') as $id => $status)
                  <option value="{{$id}}" @if(Request::input('search_by_status')==$id) selected @endif>{{$status}}</option>
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
              @if($page)
              <a href="{{ route('archivedleaveRequest.index') }}" target="_blank">
                <button type="button" class="btn btn-primary">Show Archives</button>
              </a>
              @endif
            </form>
            
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table class="table table-bordered table-hover datatable" id="leave_applications">
            <thead>
              <tr>
                <th style="width: 70px;">
                  <input type="checkbox" id="check_all" class="icheck">
                  @if($page)
                    <span style="margin-left: 10px; display: none;" class="archive_selected"><i class="fa fa-archive" aria-hidden="true"></i></span>
                  @else
                    <span style="margin-left: 10px; display: none;" class="undo_archive_selected"><i class="fa fa-send-o" aria-hidden="true"></i></span>
                  @endif
                  </th>
                <th>S.No</th>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Days</th>
                <th>Status</th>
                <th>Submitted</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @php $count = 0; @endphp
              @foreach($leave_requests as $lr)
                @php 
                  $from = \Carbon\Carbon::parse($lr->from);
                  $to = \Carbon\Carbon::parse($lr->to);
                  $days = $from->diffInDays($to);
                  $count++;
                @endphp
              <tr data-lr-id="{{$lr->id}}">
                <td><input type="checkbox" class="check_me icheck"></td>
                <td>{{$count}}</td>
                <td>{{$lr->name}}</td>
                <td>{{config('setting.leave_types')[$lr->leave_type]}}</td>
                <td>{{$from->format('dS F Y')}} - {{$to->format('dS F Y')}}</td>
                <td>{{$days}} Days</td>
                <td>
                  @if($page)
                    @if($lr->status==0)
                      Pending
                    @elseif($lr->status==1)
                      Approved
                    @else
                      Denied
                    @endif
                  @else
                    ARCHIVED
                  @endif
                </td>
                <td>{{\Carbon\Carbon::parse($lr->created_at)->diffForHumans()}}</td>
                <td>
                  @if($page)
                  <a  href="javascript:;"  data-toggle="modal" data-target="#leaveRequest_{{$lr->id}}">
                    <span class="action_icons"><i class="fa fa-eye" aria-hidden="true"></i></span>
                  </a>
                  @endif
                </td>
              </tr>

              <div class="modal modal-default fade" id="leaveRequest_{{$lr->id}}">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title">LEAVE REQUEST</h4>
                    </div>
                    <div class="modal-body">
                      <ul style="list-style: none;">
                        <li><b>Type: </b> {{config('setting.leave_types')[$lr->leave_type]}}</li>
                        <li><b>Period: </b> {{$lr->from}} - {{$lr->to}}</li>
                        <li><b>Submitted: </b> {{\Carbon\Carbon::parse($lr->created_at)->diffForHumans()}}</li>
                        <li><b>Description: </b> <br>{{$lr->description}}</li>
                      </ul>
                    </div>
                    <div class="modal-footer">
                      <div class="pull-left">
                        <form role="form" action="{{route('leave_request.status')}}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="type" value="deny">
                          <input type="hidden" name="id" value="{{$lr->id}}">
                          <button type="submit" class="btn btn-danger">Deny</button>
                        </form>
                      </div>
                      <div class="pull-right">
                        <form role="form" action="{{route('leave_request.status')}}" method="POST" enctype="multipart/form-data">
                          @csrf
                          <input type="hidden" name="type" value="approve">
                          <input type="hidden" name="id" value="{{$lr->id}}">
                          <button type="submit" class="btn btn-success">Approve</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              @endforeach
            </tbody>
          </table>
        </div>
        <div class="box-footer clearfix">
          <div class="col-md-4 col-md-offset-4 text-center">
            {{ $leave_requests->links() }}
          </div>
        </div>
      </div>
    </div>
    @endif



  </div>
</section>

@endsection
@push('scripts')
<script src="{{ asset('backend/excel-plugins/xlsx.core.min.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/Blob.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/FileSaver.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/Export2Excel.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/jquery.tableexport.v2.js') }}"></script>
<script src="{{ asset('backend/js/character-counter.js') }}"></script>
<script type="text/javascript">
    $("table").tableExport({
      formats: ["xlsx"],
    });
    $('#from_to, #search_date_from_to').daterangepicker();
</script>
<script type="text/javascript">

  $('.archive_selected').on('click', function(e) {
    e.preventDefault();
    var sel_Rows = []; 
    $(".check_me:checked").each(function() {  
        sel_Rows.push($(this).parent().parent().parent().data('lr-id'));
    });  
    console.log(sel_Rows);
    if(sel_Rows.length <= 0)  
    {  
      swal({
        title: "ERROR !!",
        text: "Please select row to delete.",
        icon: "warning",
        dangerMode: true,
      });
      e.preventDefault();
    }  
    else {
      swal({
        title: "Are you sure?",
        text: "Records will be moved to archive!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          $.ajax({
            type:'delete',
            url: SITE_URL+'archiveLeaveApplication',
            dataType: 'json',
            data:{                
              sel_Rows: sel_Rows,                
            },
            success:function(data) {
              console.log(data);
              sel_Rows.forEach(function(a){
                $('*[data-lr-id='+a+']').remove();
              })
              showNotify('success',data);
            },
            error: function(response){
            
            }
          });
        } 
      }); 
    }  
  });

  $('.undo_archive_selected').on('click', function(e) {
    e.preventDefault();
    var sel_Rows = []; 
    $(".check_me:checked").each(function() {  
        sel_Rows.push($(this).parent().parent().parent().data('lr-id'));
    });  
    console.log(sel_Rows);
    if(sel_Rows.length <= 0)  
    {  
      swal({
        title: "ERROR !!",
        text: "Please select row to unarchive.",
        icon: "warning",
        dangerMode: true,
      });
      e.preventDefault();
    }  
    else {
      swal({
        title: "Are you sure?",
        text: "Records will be restored!",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then((willDelete) => {
        if (willDelete) {
          $.ajax({
            type:'post',
            url: SITE_URL+'undoArchiveLeaveApplication',
            dataType: 'json',
            data:{                
              sel_Rows: sel_Rows,                
            },
            success:function(data) {
              console.log(data);
              sel_Rows.forEach(function(a){
                $('*[data-lr-id='+a+']').remove();
              })
              showNotify('success',data);
            },
            error: function(response){
            
            }
          });
        } 
      }); 
    }  
  });


  var checkAll = $('#check_all');
  var checkboxes = $('.check_me');

  //Red color scheme for iCheck
  $('.icheck').iCheck({
    checkboxClass: 'icheckbox_flat-green'
  });

  checkAll.on('ifChecked ifUnchecked', function(event) {        
      if (event.type == 'ifChecked') {
          checkboxes.iCheck('check');
      } else {
          checkboxes.iCheck('uncheck');
      }
  });
  checkboxes.on('ifChanged', function(event){
      if(checkboxes.filter(':checked').length == checkboxes.length) {
        checkAll.prop('checked', 'checked');
      } else {
        checkAll.prop( "checked", false );
      }
      checkAll.iCheck('update');

      if(checkboxes.filter(':checked').length >= 1) {
        $('.archive_selected').show();
        $('.undo_archive_selected').show();
      }
      else{
        $('.archive_selected').hide();
        $('.undo_archive_selected').hide();
      }
      
  });

</script>
@endpush