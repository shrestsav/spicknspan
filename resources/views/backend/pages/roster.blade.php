@extends('backend.layouts.app',['title'=> 'Roster'])

@push('styles')
  <!-- Jquery Time Picker -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.css" rel="stylesheet">
<style type="text/css">
  .timepicker{
    width: 100%;
    text-align: center;
  }
  .week_selector{
    margin:0px 8px;
    padding: 10px 20px;
  }
  td:hover{
    border:1px solid #9a9a9a !important;
  }
  th{
    text-align: center;
  }
  .ibtnDel{
    padding: 0px 2px;
    background: red;
    color: white;
  }
  .ibtnDel:hover{
    cursor: pointer;
  }
  td.week_1,td.week_2,td.week_3,td.week_4,td.week_5{
    padding: 10px 20px !important;
  }
  .week_1 input,.week_2 input,.week_3 input,.week_4 input,.week_5 input{
    border: 0px !important;
    margin: 4px;
  }
  .editable_roster .week_1 input:hover,.editable_roster .week_2 input:hover,.editable_roster .week_3 input:hover,.editable_roster .week_4 input:hover,.editable_roster .week_5 input:hover{
    border: 0.5px dashed  #00a65a !important;
    cursor: pointer;
  }
  input:hover{
    cursor: pointer;
  }
  .search_by_date{
    display: inline-table;
    width: 150px; 
    top: 14px;
  }
  .pagination{
    margin: 0px;
  }
  .form-control[readonly],.readonly_roster{
    background-color: #f7f7f7ad;
    opacity: 1;
  }
</style>
@endpush

@section('content')

@php
  $total_days =  count($all_days);
  $week = [1,2,3,4,5];
  $search_arr = [
    'Employee Name' => [
      'data'    => 'employees',
      'name'    => 'search_by_employee_id'
    ],
    'Client Name' => [
      'data'    => 'clients',
      'name'    => 'search_by_client_id'
    ],
  ];

  $today = strtotime(Date('Y-m-d'));
  $todayWeek = weekOfMonth($today);

  function weekOfMonth($date) {
    //Get the first day of the month.
    $firstOfMonth = strtotime(date("Y-m-01", $date));
    //Apply above formula.
    return intval(date("W", $date)) - intval(date("W", $firstOfMonth)) + 1;
  } 
@endphp

<section class="content">
  <div class="row">
    <div class="col-lg-12">
      <div class="box box-default collapsed-box box-solid">
        <div class="box-header">
          <h3 class="box-title">IMPORT FROM EXCEL
            <small>Format: xlsx</small>
          </h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <form action="{{ route('import_from_excel','rosters') }}" method="POST" enctype="multipart/form-data" data-toggle="validator">
            @csrf
            {{-- <input type="hidden" name="user_type" class="form-control" value="{{$user_type}}"> --}}
            <div class="col-md-12" style="text-align: center;">
              <div class="form-group">
                <label for="file"><a href="javascript:;"  data-toggle="modal" data-target="#modal-info"> Please Read this before using this feature</a></label><br><br>
                <input type="file" name="file" class="form-control jfilestyle" required>
                <div class="help-block with-errors"></div>
                <button class="btn btn-success" type="submit">Import Roster</button>
              </div>
            </div>  
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      @permission('import_export_excel')
        <div class="pull-right">
          <form role="form" action="{{route('export.excel')}}" method="POST">
            @csrf
            <input type="hidden" name="type" value="roster">
            <input type="hidden" name="year_month" value="{{$year.'-'.$month}}">
            <input type="hidden" name="employee_id" value="{{Request::input('search_by_employee_id')}}">
            <input type="hidden" name="client_id" value="{{Request::input('search_by_client_id')}}">
            <button type="submit" class="btn btn-success">Export to Excel</button>
          </form>
        </div>
      @endpermission

      @if(Request::all())
        <a href="{{url('/roster')}}"><button class="btn btn-primary">Go Back</button></a>
      @endif
    </div>
    <div class="col-md-12">
      <div class="box box-primary">
        <div class="box-header with-border">
          {{-- Filter Form --}}
          <div class="col-md-5">
            <div class="search_form">
              <form autocomplete="off" role="form" action="{{route('roster.index')}}" method="POST" enctype="multipart/form-data" style="margin-top: -14px">
                @csrf
                @foreach($search_arr as $part => $arr)
                  <select class="select2 {{$arr['name']}}" name="{{$arr['name']}}">
                    <option disabled selected value> {{$part}}</option>
                    @foreach(${$arr['data']} as $data)
                      <option value="{{$data->id}}" @if(Request::input($arr['name'])==$data->id) selected @endif>
                        {{$data->name }}
                      </option>
                    @endforeach
                  </select>
                @endforeach
                <div class="input-group date search_by_date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  <input name="year_month" type="text" id="year_month" class="form-control txtTime" style="width:85px;" value="{{$year.'-'.$month}}" autocomplete="off" required>
                </div> 
                <button type="submit" class="btn btn-primary">SEARCH</button>
              </form>
            </div>
          </div>
          <div class="col-md-3">
            @role('superAdmin','contractor')
              <button type="button" class="btn btn-danger edit_rosters">EDIT</button>
              <button id="addrow" class="btn btn-success" style="display: none;">Add Row</button>
              <button type="button" class="btn btn-danger delete_all" disabled style="display: none;">Delete</button>
            @endrole
          </div>
          <div class="col-md-4">
            <div class="box-tools">
              <div class="pull-right">
                <select class="select2 week_selector">
                  <option disabled selected value>Select Week</option>
                  @foreach($week as $a)
                    <option value="{{$a}}">Week {{$a}}</option>
                  @endforeach
                </select>
              </div>
              @role('superAdmin','contractor')
                <div class="input-group input-group-sm copy_to_month pull-right" style="width: 150px; display: none;">
                  <input type="text" id="copy_to_month" class="form-control pull-right" style="width:100px;" placeholder="Copy Roster" autocomplete="off">
                  <div class="input-group-btn">
                    <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </div>
                </div>
              @endrole
            </div>
          </div>


        </div>
        <div class="box-body {{-- table-responsive  --}}no-padding">
          <table id="rosterTable" class="table {{-- table-hover --}} table-bordered dataTable">
            <thead class="thead-dark">
              <tr role="row">
                <th><input type="checkbox" id="check_all"></th>
                <th class="employee_head" style="min-width: 120px;">EMPLOYEE</th>
                <th class="client_head" style="min-width: 120px;">CLIENT</th>
                @foreach($all_days as $day => $date)
                  @php
                    $d = strtotime($year.'-'.$month.'-'.$day);
                    $week = 'week_'.weekOfMonth($d);
                  @endphp
                  <th class="{{$week}} {{$year.'-'.$month.'-'.$day}}">
                    {{$date}}
                  </th>
                @endforeach

              </tr>
            </thead>

            <tbody class="roster-list readonly_roster">
            @foreach($rosters as $client_id => $roster_by_clients)
              @foreach($roster_by_clients as $emp_id => $emp_rosters)
              
              @php 
                $r_id = $emp_rosters[0]->id;
                $emp_name = $emp_rosters[0]->employee->name;
                $emp_mail = $emp_rosters[0]->employee->email;
              @endphp
              <div class="dropdown-contextmenu" id="contextmenu_{{$r_id}}">
                <a style="position: relative;"><i class="fa fa-user"></i> {{$emp_name}}</a>
                <hr style="margin-top: 0px; margin-bottom: 0px;">
                <a href="javascript:;" class="btn btn-link email_employee" data-employee-mail="{{$emp_mail}}" data-employee-name="{{$emp_name}}" title="Notify Employee of Roster Update">
                  <i class="fa fa-envelope" aria-hidden="true"></i>Notify Employee
                </a>
                <a href="javascript:;" class="btn btn-link roster_details" data-roster_id="{{$r_id}}" title="View Roster Summary">
                  <i class="fa  fa-info-circle" aria-hidden="true"></i>Roster Summary
                </a>
                <a href="javascript:;" class="btn btn-link roster_clone_row" data-roster_id="{{$r_id}}" title="Create New Clone">
                  <i class="fa fa-clone" aria-hidden="true"></i>Create Clone
                </a>
                {{--<a href="{{route('user.edit',$user->id)}}" class="btn btn-link" title="Edit Details">
                  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit Details
                </a> --}}
              </div>
              <tr class="contextmenurow" dataid="{{$r_id}}" style="text-align: center;" role="row" class="" id="" data-roster-id="{{$r_id}}" data-row-type="old_row">
                <td>
                  <input type="checkbox" class="sub_chk">
                </td>
                <td>
                  {{$emp_name}}
                </td>
                <td>
                  {{$emp_rosters[0]->client->name}}
                </td>
                @foreach($all_days as $day => $date)
                  @php
                    $start_time = '';
                    $end_time = '';
                    $d = strtotime($year.'-'.$month.'-'.$day);
                    $week = 'week_'.weekOfMonth($d);

                    foreach($emp_rosters as $roster){
                      $thisDay = \Carbon\Carbon::parse($roster->date)->format('d');
                      if($thisDay==$day){
                        $start_time = $roster->start_time;
                        $end_time = $roster->end_time;
                      }
                    }

                    $status = 0;
                    $leave_type = 0;
                    $full_day = \Carbon\Carbon::parse($year.'-'.$month.'-'.$day);
                    foreach($leaves as $leave){
                      $from = \Carbon\Carbon::parse($leave->from);
                      $to = \Carbon\Carbon::parse($leave->to);
                      if($leave->user_id==$emp_id && $full_day->between($from, $to)){
                        $status = 1;
                        $leave_type = $leave->leave_type;
                      }
                    }
                  @endphp
                  <td class="{{$week}}">
                    <input type="text" class="form-control timepicker txtTime time_from" value=" @if($status){{config('setting.leave_types')[$leave_type]}} @else{{$start_time}}@endif" data-date = "{{$year.'-'.$month.'-'.$day}}" @if($status)disabled @endif readonly>
                       
                    <input type="text" class="form-control timepicker txtTime time_to" value="@if($status){{config('setting.leave_types')[$leave_type]}} @else{{$end_time}}@endif" data-date = "{{$year.'-'.$month.'-'.$day}}" @if($status)disabled @endif readonly>
                  </td>
                @endforeach
              </tr>
              @endforeach
            @endforeach
            </tbody>
          </table>
        </div>
        @role('superAdmin','contractor')
        <div class="box-footer clearfix">
          <div class="col-md-4">
            
          </div>
          <div class="col-md-4 text-center">
            {{ $customPaginate->links() }}
          </div>
          <div class="col-md-4">
            
          </div>
        </div>
        @endrole
      </div>
    </div>
  </div>
</section>

@include('backend.modals.modal', [
            'modalId' => 'notifyEmailModal',
            'modalFile' => '__modal_body',
            'modalTitle' => __('Notify Email'),
            'modalSize' => 'tiny_modal_dialog',
        ])

@include('backend.modals.modal', [
            'modalId' => 'rosterDetailsModal',
            'modalFile' => '__modal_body',
            'modalTitle' => __('Roster Summary'),
            'modalSize' => 'large_modal_dialog',
        ])

<div class="modal modal-info fade" id="modal-info">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Read Very Carefully</h4>
      </div>
      <div class="modal-body">
        <h3></h3>
        <div>
          <ol>
            <li>First Download the excel file provided here for the correct format to upload</li>
            <li>Now open the file and replace the dummy data with your actual data</li>
            <li>Please donot edit or remove any header columns</li>
            <li>The yellow marked column should not be left empty</li>
            <li>Donot leave any empty row after heading row or in between any of the rows</li>
            <li>If you need to keep some day in roster as empty like in holidays, then just leave it empty (Very Important)</li>
            <li>Heading Numbers 1 2 3 4 ... all represents particular day of the mentioned month.</li>
            <li>Client and Employee email should exists in the system</li>
            <li>After you have filled X number of rows, save and upload it.</li>
          </ol>
        </div>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <a href="{{ asset('files/import_from_excel_format(rosters).xlsx') }}"><button type="button" class="btn btn-outline">Download Excel Format</button></a>
      </div>
    </div>
  </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
<script type="text/javascript">

  // Initialize
    $('.timepicker').timepicker({ 'timeFormat': 'H:i' });

    $('#year_month, #copy_to_month').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'yyyy-mm'
    });

  //Not used kaam lagna sakxa
  function getMonths(month,year){
    var ar = [];
    var days = [];
    var start = moment(year+"-"+month,"YYYY-MMM");
    for(var end = moment(start).add(1,'month');  start.isBefore(end); start.add(1,'day')){
        ar.push(start.format('D-ddd'));
        days[start.format('D')] = start.format('ddd,MMM-D');
    }
    return days;
  }
  // console.log(getMonths('Mar',2011))

  $('body').on('change','.time_from',function(e){
    e.preventDefault();
    var my = $(this);
    var row_type = my.parent().parent().data('row-type');
    var cloned = my.parent().parent().data('cloned');
    if(cloned)
      var showNotification = 0;
    else
      var showNotification = 1;
    var type = 'start_time';
    var time_from = my.val();
    var date = my.data('date');
    var roster_id = my.parent().parent().data('roster-id');
    if(row_type=='new_row'){
      var client_id = my.parent().siblings('.client_td').children('.client_name').val();
      var employee_id = my.parent().siblings('.employee_td').children('.employee_name').val();
      newTimetable(my,type,client_id,employee_id,time_from,date,showNotification);
    }
    else if(row_type=='old_row'){
      updateTimetable(my,type,roster_id,time_from,date,showNotification);
    }
    
  });

  $('body').on('change','.time_to',function(e){
    e.preventDefault();
    var my = $(this);
    var row_type = my.parent().parent().data('row-type');
    var cloned = my.parent().parent().data('cloned');
    if(cloned)
      var showNotification = 0;
    else
      var showNotification = 1;
    var type = 'end_time';
    var time_to = my.val();
    var date = my.data('date');
    var roster_id = my.parent().parent().data('roster-id');
    if(row_type=='new_row'){
      var client_id = my.parent().siblings('.client_td').children('.client_name').val();
      var employee_id = my.parent().siblings('.employee_td').children('.employee_name').val();
      newTimetable(my,type,client_id,employee_id,time_to,date,showNotification);
    }
    else if(row_type=='old_row'){
      updateTimetable(my,type,roster_id,time_to,date,showNotification);
    }
  });

  function updateTimetable(my,type,roster_id,time,date,showNotification){
    $.ajax({
      type:'post',
      url: SITE_URL+'ajax_update_roster',
      dataType: 'json',
      data:{
        type: type,                
        roster_id: roster_id,                
        time: time,                
        date: date,                
      },
      success:function(data) {
        console.log(data);
        if(showNotification)
          showNotify('success','Roster Set Successfully');
      },
      error: function(response){
        $.each(response.responseJSON, function(index, val){
          console.log(index+":"+val);
          if(showNotification)
            showNotify('danger',val); 
        });
        my.val('');
      }
    });
  }

  function newTimetable(my,type,client_id,employee_id,time,date,showNotification){
    $.ajax({
      type:'post',
      url: SITE_URL+'ajax_store_roster',
      dataType: 'json',
      data:{
        type: type,                
        client_id: client_id,                
        employee_id: employee_id,                
        time: time,                
        date: date,                
      },
      success:function(data) {
        console.log(data);
        if(showNotification)
          showNotify('success','Roster Set Successfully');
      },
      error: function(response){
        $.each(response.responseJSON, function(index, val){
          console.log(index+":"+val);
          if(showNotification)
            showNotify('danger',val); 
        });
        my.val('');
      }
    });
  }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-confirmation/1.0.5/bootstrap-confirmation.min.js"></script>
<script type="text/javascript">

    var arr = [1,2,3,4,5];
    var full_date = moment().format('YYYY-MM-D');
    var year_month = moment().format('YYYY-MM');
    var sel_year_month = '{{$year."-".$month}}';
    var today = moment().format('D');
    var total_days = Number('{{$total_days}}');
    var leave_types = JSON.parse('{!! json_encode(config("setting.leave_types")) !!}');
    var curr_week = '{{$todayWeek}}';

    // Select Current Week
    if(year_month==sel_year_month){
      $('.week_'+curr_week).show();
      $('.'+full_date).addClass('bg-danger');
      $('.week_selector').val(curr_week).trigger('change');
      arr.forEach(function(b){
        if(curr_week!=b){
          $('.week_'+b).hide();
        }
      });
    }
    else{
        $('.week_1').show();
        $('.week_selector').val(1).trigger('change');
        $('.week_2').hide();
        $('.week_3').hide();
        $('.week_4').hide();
        $('.week_5').hide();
    }
      
    // Week Switch
    $('.week_selector').on('change', function(e) {
      var a = $(this).val();
      $('.week_'+a).show();
      arr.forEach(function(b){
        if(a!=b){
          $('.week_'+b).hide();
        }
      });
    });

    var counter = 1;
    $('body').on('click','#addrow',function(e){
        e.preventDefault();
        var i, week;
        var newRow = $("<tr style='text-align: center;' role='row' data-row-type='new_row'>");
        var cols = "";
        cols += '<td><span class="ibtnDel"><i class="fa fa-times" aria-hidden="true"></i></span></td>';
        cols += '<td class="employee_td"><select class="employee_name form-control" required>';
        cols += '<option value="" selected disabled>Select Employee</option>';
        cols += '@foreach($employees as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach';
        cols += '</select></td>';
        cols += '<td class="client_td">';
        cols += '<select class="client_name form-control" required disabled>';
        cols += '<option value selected disabled>Select Client</option>';
        cols += '@foreach($clients as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select></td>';
        cols += '@foreach($all_days as $day => $date)';
        cols += '@php $d=strtotime($year."-".$month."-".$day); $week = "week_".weekOfMonth($d) @endphp';

        cols += '<td class="{{$week}}">';
        cols += '<input type="text" class="form-control timepicker txtTime time_from"  data-date = "{{$year.'-'.$month.'-'.$day}}">';
        cols += '<input type="text" class="form-control timepicker txtTime time_to"  data-date = "{{$year.'-'.$month.'-'.$day}}">';
        cols += '</td>@endforeach';
        newRow.append(cols);
        $("tbody.roster-list").prepend(newRow);
        $('.timepicker').timepicker({ 'timeFormat': 'H:i' });

        counter++;
        $('#addrow').hide();
        if(year_month==sel_year_month){
          $('.week_1').hide();
          $('.week_2').hide();
          $('.week_3').hide();
          $('.week_4').hide();
          $('.week_5').hide();
          $('.week_'+curr_week).show();
        }
        else{
          $('.week_1').show();
          $('.week_2').hide();
          $('.week_3').hide();
          $('.week_4').hide();
          $('.week_5').hide();
        }
        $('.employee_name, .client_name').select2();
    });

    $('body').on('change','.employee_name',function(e){
      e.preventDefault();
      var t = $(this);
      const cloned = t.parent().parent().data('cloned');
      var employee_id = t.val();
      $.ajax({
        type:'post',
        url: SITE_URL+'ajaxUserLeaveRecord',
        dataType: 'json',
        data:{
          employee_id: employee_id,              
          year_month: sel_year_month,              
        },
        success:function(data) {
          console.log(data);
          $('.client_name').prop('disabled',0);
          if(!cloned){
            t.parent().parent().find('td').each (function(){
              $(this).children('.txtTime').prop('disabled',0).val('');
            });
          }
          if(data.length >= 0){
            data.forEach(function(e){
              var from = new Date(e.from);
              var to = new Date(e.to);
              t.parent().parent().find('td').each (function(){
                var input_date = new Date($(this).children().data('date'));
                if(input_date >= from && input_date <= to){
                  $(this).children().prop('disabled',1).val(leave_types[e.leave_type]);
                }
              });
            })
          }
        },
        error: function(response){
        
        }
      });
    });

    $('body').on('change','.client_name',function(e){
      e.preventDefault();
      var my = $(this);
      var client_id = my.val();
      const cloned = my.parent().parent().data('cloned');
      var employee_id = my.parent().siblings('.employee_td').children('.employee_name').val();
      $.ajax({
        type:'post',
        url: SITE_URL+'ajaxCheckIfRosterExists',
        dataType: 'json',
        data:{
          employee_id: employee_id,              
          client_id: client_id,
          year_month: sel_year_month,              
        },
        success:function(data) {
          console.log(data);
          $('.ibtnDel').hide();
          if(cloned){
            showNotify('success','Roster Copy Started. Please Wait .....',true); 
            my.parent().parent().find('td').each (function(){
              $(this).children('.time_from').trigger('change');
              $(this).children('.time_to').trigger('change');
            });
          }
        },
        error: function(response){
          $.each(response.responseJSON, function(index, val){
            console.log(index+":"+val);
            showNotify('danger',val); 
          });
          my.val('').trigger('change');
        }
      });
    })

    $("tbody.roster-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1;
        $('#addrow').show();
    });

    $('.delete_all').on('click', function(e) {
      e.preventDefault();
      var sel_Rows = []; 
      $(".sub_chk:checked").each(function() {  
          sel_Rows.push($(this).parent().parent().parent().data('roster-id'));
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
          text: "Once deleted, you will not be able to recover this data!",
          icon: "warning",
          buttons: true,
          dangerMode: true,
        })
        .then((willDelete) => {
          if (willDelete) {
            $.ajax({
              type:'delete',
              url: SITE_URL+'deleteRoster',
              dataType: 'json',
              data:{                
                sel_Rows: sel_Rows,                
              },
              success:function(data) {
                location.reload();
              },
              error: function(response){
              
              }
            });
          } 
        }); 
      }  
    });

    $('body').on('click','.edit_rosters',function(e){
      $('.time_from, .time_to').prop('readonly',false);
      $('.delete_all').prop('disabled',false);
      $('#addrow').show();
      $('tbody').removeClass('readonly_roster').addClass('editable_roster');
      $(this).html('DONE').removeClass('btn-danger edit_rosters').addClass('btn-success done_rosters');
    });

    $('body').on('click','.done_rosters',function(e){
      $('.time_from, .time_to').prop('readonly',true);
      $('.delete_all').prop('disabled',true);
      $('#addrow').hide();
      $('tbody').addClass('readonly_roster').removeClass('editable_roster');;
      $(this).html('EDIT').removeClass('btn-success done_rosters').addClass('btn-danger edit_rosters');
    });

    $('body').on('click','.email_employee',function(e){
      e.preventDefault();
      var emp_mail = $(this).data('employee-mail');
      var emp_name = $(this).data('employee-name');
      var html = '<div class="row"><div class="col-md-12 notify_email_container">';
      html += '<input class="form-control notify_email" type="email" name="notify_email" value="'+emp_mail+'"></div>';
      html += '<br><br>';
      html += '<div class="col-md-12 text-center"><button type="button" class="btn btn-info roster_notify">Notify</button></div>';
      html += '</div>';

      detailModel = $('#notifyEmailModal');
      detailModel.find('.modal-content .modal-title').html(emp_name);
      detailModel.find('.modal-body').html(html);
      detailModel.modal('show');
    });

    $('body').on('click','.roster_details',function(e){
      e.preventDefault();
      var roster_id = $(this).data('roster_id');
      $.ajax({
            type: 'POST',
            url: SITE_URL + 'ajaxRosterDetails',
            data: {
                'roster_id': roster_id
            },
            dataType: 'json'
        }).done(function (response) {
            console.log(response);
            detailModel = $('#rosterDetailsModal');
            detailModel.find('.modal-content .modal-title').html(response.title);
            detailModel.find('.modal-body').html(response.html);
            detailModel.modal('show');
        });
    });

    $('body').on('click','.roster_notify',function(e){
      e.preventDefault();
      var cont = $(this).parent().siblings('.notify_email_container').children('.notify_email');
      var email = cont.val();
      var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
      if(email.match(mailformat)){
        $.ajax({
          type: 'POST',
          url: SITE_URL + 'rosterNotify',
          data: {
            'email': email,
            'year_month': sel_year_month
          },
          dataType: 'json',
          success:function(data) {
            console.log(data);
            showNotify('success',data);
          },
          error: function(response){
            $.each(response.responseJSON, function(index, val){
              console.log(index+":"+val);
              showNotify('danger',val); 
            });
          }
        });
      }
      else{
        alert("You have entered an invalid email address!");
        cont.focus();
      }
    });

    $('body').on('click','.roster_clone_row',function(e){
      e.preventDefault();
      var roster_id = $(this).data('roster_id');
      var roster_row = $('tr[dataid="'+roster_id+'"]');
      var cloned = roster_row.clone().attr('data-row-type', 'new_row').attr('data-cloned', '1');
      
      $("tbody.roster-list").prepend(cloned);
      
      var checkbox_col = '<span class="ibtnDel"><i class="fa fa-times" aria-hidden="true"></i></span>';
      var employee_col = '<select class="employee_name form-control" required>';
      employee_col += '<option value="" selected disabled>Select Employee</option>';
      employee_col += '@foreach($employees as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach';
      employee_col += '</select>';
      var client_col = '<select class="client_name form-control" required disabled>';
      client_col += '<option value selected disabled>Select Client</option>';
      client_col += '@foreach($clients as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select>';

      cloned.children('td:first-child').html(checkbox_col);
      cloned.children('td:nth-child(2)').addClass('employee_td').html(employee_col);
      cloned.children('td:nth-child(3)').addClass('client_td').html(client_col);

    });
</script>

<script type="text/javascript">

  var checkAll = $('#check_all');
  var checkboxes = $('.sub_chk');

  //Red color scheme for iCheck
  $('input[type="checkbox"], input[type="radio"].minimal-red').iCheck({
    checkboxClass: 'icheckbox_flat-green',
    radioClass   : 'iradio_flat-green'
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
        $('.copy_to_month').show();
        $('.delete_all').show();
      }
      else{
        $('.copy_to_month').hide();
        $('.delete_all').hide();
      }
  });

  $('.copy_to_month').on('change',function(e){
    e.preventDefault();
    var copy_year_month = $('#copy_to_month').val();
    var sel_Rows = []; 
    $(".sub_chk:checked").each(function() {  
        sel_Rows.push($(this).parent().parent().parent().data('roster-id'));
    });  
    console.log(sel_Rows);
    swal({
      title: "Are you sure?",
      text: "If roster already exists, nothing will be copied",
      icon: "warning",
      buttons: true,
      dangerMode: true,
    })
    .then((willCopy) => {
      if (willCopy) {
        $.ajax({
          type:'POST',
          url: SITE_URL+'copyRoster',
          dataType: 'json',
          data:{                
            sel_Rows: sel_Rows,                
            year_month: copy_year_month,                
          },
          success:function(data) {
            console.log(data);
            showNotify('success','Roster for selected users have been copied'); 
          },
          error: function(response){
          
          }
        });
      } 
    }); 
  })

    //For default checkboxes
    // $('#check_all').on('click', function(e){
    //   alert('fsdf');
    //   if($(this).is(':checked',true)){  
    //     alert('fasdf');
    //     $(".sub_chk").prop('checked', true);
    //   }  
    //   else{ 
    //     alert('no'); 
    //     $(".sub_chk").prop('checked',false);
    //   } 
    // });
</script>
  
  {{-- Copy Paste Events --}}
  <script type="text/javascript">
    var timetable = {
      'from' : '',
      'to'   : ''
    };
 
    $('body').on('click','.editable_roster td',function(e){
      $('td').css('border','unset')
      $(this).css('border','1px solid #9a9a9a');
    })
    // $(".editable_roster td").bind("copy", function(e){
    $('table').on('copy','.editable_roster td',function(e){
      var copy_from = $(this).children('.time_from').val();
      var copy_to = $(this).children('.time_to').val();
      timetable['from'] = copy_from;
      timetable['to'] = copy_to;
      showNotify('success','Copied to Clipboard');  
    });

    // $(".editable_roster td").bind("paste", function(e){
    $('table').on('paste','.editable_roster td',function(e){
      $(this).children('.time_from').val(timetable['from']).trigger("change");;
      $(this).children('.time_to').val(timetable['to']).trigger("change");;
    });


  </script>
@endpush