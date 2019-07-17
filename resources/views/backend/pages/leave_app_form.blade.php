@php

  if(Route::current()->getName() == 'leaveRequest.pending' || $page === 0){
    $page = 0;
    $title = 'Pending Leave Applications';
  }
  elseif(Route::current()->getName() == 'leaveRequest.approved' || $page == 1){
    $page = 1;
    $title = 'Approved Leave Applications';
  }
  elseif(Route::current()->getName() == 'leaveRequest.denied' || $page == 2){
    $page = 2;
    $title = 'Denied Leave Applications';
  }
  if(Route::current()->getName() == 'leaveRequest.archived' || $page == 3){
    $page = 3;
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
    @if($page!=3)
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
        <a href="{{ URL::previous() }}"><button class="btn btn-primary">Go Back</button></a>
      @endif
      @permission('import_export_excel')
        <div class="pull-right">
          <button type="submit" class="btn btn-success export_btn">Export to Excel</button>
        </div>
      @endpermission
    </div>

    <div class="col-md-12">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li @if($page=='0') class="active" @endif><a href="{{ route('leaveRequest.pending') }}">Pending</a></li>
          <li @if($page=='1') class="active" @endif><a href="{{ route('leaveRequest.approved') }}">Approved</a></li>
          <li @if($page=='2') class="active" @endif><a href="{{ route('leaveRequest.denied') }}">Denied</a></li>
          <li @if($page=='3') class="active" @endif><a href="{{ route('leaveRequest.archived') }}">Archives</a></li>
        </ul>
        <div class="tab-content">
          <div class="active tab-pane">
            <div class="{{-- box --}}">
              <div class="box-header">
                <div class="search_form">
                  <form autocomplete="off" role="form" action="{{route('leaveRequest.search')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="page" value="{{$page}}">
                    @php 
                      $search_arr = [
                        'Employee Name' => [
                          'class'   => 'search_by_employee_id',
                          'name'    => 'search_by_employee_id',
                          'value'   => 'employee_id',
                          'view'    => 'name'
                        ],
                      ];
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
                  </form>
                </div>
              </div>
              <div class="box-body table-responsive no-padding">
                <table class="table table-bordered table-hover datatable" id="leave_applications">
                  <thead>
                    <tr>
                      <th style="width: 70px;">
                        <input type="checkbox" id="check_all" class="icheck">
                        @if($page!=3)
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
                        @if($page!=3)
                          @if($lr->status==0)
                            Pending
                          @elseif($lr->status==1)
                            Approved
                          @elseif($lr->status==2)
                            Denied
                          @endif
                        @else
                          ARCHIVED
                        @endif
                      </td>
                      <td>{{\Carbon\Carbon::parse($lr->created_at)->diffForHumans()}}</td>
                      <td>
                        @if($page!=3)
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

    $('.export_btn').on('click',function(e){
      e.preventDefault();
      $("button.xlsx").trigger('click')
    })

    $('button.xlsx').hide();
    $('#from_to, #search_date_from_to').daterangepicker();
</script>
<script type="text/javascript">

  $('.archive_selected').on('click', function(e) {
    e.preventDefault();
    var sel_Rows = []; 
    $(".check_me:checked").each(function() {  
        sel_Rows.push($(this).parent().parent().parent().data('lr-id'));
    });  

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
            url: SITE_URL+'leaveApplications/archive',
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
            url: SITE_URL+'leaveApplications/undoArchive',
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