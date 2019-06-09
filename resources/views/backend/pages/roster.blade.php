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
    padding: 0px !important;
  }
  .week_1 input,.week_2 input,.week_3 input,.week_4 input,.week_5 input{
    border: 0px !important;
  }
  .week_1 input:hover,.week_2 input:hover,.week_3 input:hover,.week_4 input:hover,.week_5 input:hover{
    border: 0.5px solid #00a65a !important;
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
  .form-control[readonly]{
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
          <div class="box-header">
            {{-- <h3 class="box-title"></h3> --}}

            {{-- Filter Form --}}
            <div class="search_form">
              <form autocomplete="off" role="form" action="{{route('roster.index')}}" method="POST" enctype="multipart/form-data">
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
            @role('superAdmin','contractor')
            <button type="button" class="btn btn-danger edit_rosters pull-right">EDIT</button>
            @endrole
            <div class="pull-right">
              <select class="select2 week_selector">
                <option disabled selected value>Select Week</option>
                @foreach($week as $a)
                  <option value="{{$a}}">Week {{$a}}</option>
                @endforeach
              </select>
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

            <tbody class="roster-list">
            @foreach($rosters as $client_id => $roster_by_clients)
              @foreach($roster_by_clients as $emp_id => $emp_rosters)
              <tr style="text-align: center;" role="row" class="" id="" data-roster-id="{{$emp_rosters[0]->id}}" data-row-type="old_row">
                <td>
                    <input type="checkbox" class="sub_chk">
                </td>
                <td>
                  {{$emp_rosters[0]->employee->name}}
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
            <button type="button" class="btn btn-danger delete_all" disabled>Delete</button>
          </div>
          <div class="col-md-4 text-center">
            {{ $customPaginate->links() }}
          </div>
          <div class="col-md-4">
            <button id="addrow" class="btn btn-success pull-right" disabled>Add Row</button>
          </div>
        </div>
        @endrole
      </div>
    </div>
  </div>
</section>




@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
<script type="text/javascript">

  // Initialize
    $('.timepicker').timepicker({ 'timeFormat': 'H:i' });

    $('#year_month').datepicker({
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
    var type = 'start_time';
    var time_from = my.val();
    var date = my.data('date');
    var roster_id = my.parent().parent().data('roster-id');
    if(row_type=='new_row'){
      var client_id = my.parent().siblings('.client_td').children('.client_name').val();
      var employee_id = my.parent().siblings('.employee_td').children('.employee_name').val();
      newTimetable(my,type,client_id,employee_id,time_from,date);
    }
    else if(row_type=='old_row'){
      updateTimetable(my,type,roster_id,time_from,date);
    }
    
  });

  $('body').on('change','.time_to',function(e){
    e.preventDefault();
    var my = $(this);
    var row_type = my.parent().parent().data('row-type');
    var type = 'end_time';
    var time_to = my.val();
    var date = my.data('date');
    var roster_id = my.parent().parent().data('roster-id');
    if(row_type=='new_row'){
      var client_id = my.parent().siblings('.client_td').children('.client_name').val();
      var employee_id = my.parent().siblings('.employee_td').children('.employee_name').val();
      newTimetable(my,type,client_id,employee_id,time_to,date);
    }
    else if(row_type=='old_row'){
      updateTimetable(my,type,roster_id,time_to,date);
    }
  });

  function updateTimetable(my,type,roster_id,time,date){
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
        showNotify('success','Roster Set Successfully');
      },
      error: function(response){
        $.each(response.responseJSON, function(index, val){
          console.log(index+":"+val);
          showNotify('danger',val); 
        });
        my.val('');
      }
    });
  }

  function newTimetable(my,type,client_id,employee_id,time,date){
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
        showNotify('success','Roster Set Successfully');
      },
      error: function(response){
        $.each(response.responseJSON, function(index, val){
          console.log(index+":"+val);
          showNotify('danger',val); 
        });
        my.val('');
      }
    });
  }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-confirmation/1.0.5/bootstrap-confirmation.min.js"></script>
<script type="text/javascript">

  $(function () {
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
        $("tbody.roster-list").append(newRow);
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
          t.parent().parent().find('td').each (function(){
            $(this).children('.txtTime').prop('disabled',0).val('');
          });
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
      $('.delete_all, #addrow').prop('disabled',false);
      $(this).html('DONE').removeClass('btn-danger edit_rosters').addClass('btn-success done_rosters');
    });
    $('body').on('click','.done_rosters',function(e){
      $('.time_from, .time_to').prop('readonly',true);
      $('.delete_all, #addrow').prop('disabled',true);
      $(this).html('EDIT').removeClass('btn-success done_rosters').addClass('btn-danger edit_rosters');
    });
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
  });


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
  
@endpush