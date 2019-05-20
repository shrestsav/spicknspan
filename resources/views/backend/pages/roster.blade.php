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
</style>
@endpush

@section('content')

@php

//Yeslai controller maa rakhnu parxa
  function dates_month($month, $year) {
    $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    $dates_month = array();
    for ($i = 1; $i <= $num; $i++) {
        $mktime = mktime(0, 0, 0, $month, $i, $year);
        $date = date("D-M-d", $mktime);
        $dates_month[$i] = $date;
    }
    return $dates_month;
  }
  
  $yr = date('Y');
  $mth = date('m');
  if(isset($_GET['full_date']) && $_GET['full_date']){
    $year_month = explode('-',$_GET['full_date']);
    $yr = $year_month[0];
    $mth = $year_month[1];
  }

  $all_days = dates_month($mth, $yr);

@endphp

<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if ($errors->any())
        <div class="alert alert-danger">
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
      @if (\Session::has('error'))
        <div class="alert alert-error custom_success_msg">
            {{ \Session::get('error') }}
        </div>
      @endif
      <div class="container col-sm-12">
        <div class="box box-primary">
          <div class="box-header with-border">
            <form role="form" action="{{route('roster.index')}}" method="GET">
              <div class="box-header">
                <h3 class="box-title"></h3>
                <p class="pull-right">
                    <label for="">Month-Year : </label>
                    <input name="full_date" type="text" id="full_date" class="txtTime" style="width:85px;" value="<?php if(isset($_GET['full_date'])){
                        $date_filter = $_GET['full_date'];
                    } else {
                        $date_filter = date("Y-m");
                    } echo $date_filter;?>" autocomplete="off" required>
                    <button type="submit" class="btn btn-warning"><i class="fa fa-refresh"></i></button>
                </p>
              </div>
            </form>

            <div class="col-md-12 text-center">
              <button id='b_week_1' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 1</button>
              <button id='b_week_2' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 2</button>
              <button id='b_week_3' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 3</button>
              <button id='b_week_4' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 4</button>
              <button id='b_week_5' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 5</button>
              <br>
              <br>
            </div>
            <table id="tblRoster" class="table table-hover table-bordered dataTable no-footer order-list" role="grid" aria-describedby="tblRoster_info">
              <thead class="thead-dark">
                @php
                  $month_part   = explode('-', $date_filter);
                  $month        = $month_part[1];
                  $total_days =  count($all_days);
                @endphp
                  <tr role="row">
                    <th><input type="checkbox" id="check_all"></th>
                    <th class="">EMPLOYEE</th>
                    <th class="" >CLIENT</th>
                    @foreach($all_days as $day => $date)
                      @php
                        if($day>=1 && $day<=7)
                         $week = 'week_1';
                        if($day>=8 && $day<=14)
                          $week = 'week_2'; 
                        if($day>=15 && $day<=21)
                          $week = 'week_3'; 
                        if($day>=22 && $day<=28)
                          $week = 'week_4'; 
                        if($day>=29 && $day<=31)
                          $week = 'week_5'; 
                      @endphp
                      <th class="{{$week}}">
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
                    {{\App\User::find($emp_id)->name}}
                  </td>
                  <td>
                    {{\App\User::find($client_id)->name}}
                  </td>
                  @foreach($all_days as $day => $date)
                    @php
                      $start_time = '';
                      $end_time = '';

                      if($day>=1 && $day<=7)
                        $week = 'week_1';
                      elseif($day>=8 && $day<=14)
                        $week = 'week_2';
                      elseif($day>=15 && $day<=21)
                        $week = 'week_3';
                      elseif($day>=22 && $day<=28)
                        $week = 'week_4';
                      elseif($day>=29 && $day<=32)
                        $week = 'week_5'; 

                      foreach($emp_rosters as $roster){
                        $thisDay = \Carbon\Carbon::parse($roster->date)->format('d');
                        if($thisDay==$day){
                          $start_time = $roster->start_time;
                          $end_time = $roster->end_time;
                        }
                      }
                      $status = 0;
                      foreach($leaves as $leave){
                        $from = \Carbon\Carbon::parse($leave->from);
                        $to = \Carbon\Carbon::parse($leave->to);
                        $full_day = \Carbon\Carbon::parse($yr.'-'.$mth.'-'.$day);
                        if($leave->user_id==$emp_id && $full_day->between($from, $to))
                          $status = 1;
                      }
                    @endphp
                    <td class="{{$week}}">
                      <input type="text" class="form-control timepicker txtTime time_from" value="{{$start_time}}" data-date = "{{$yr.'-'.$mth.'-'.$day}}" @if($status)disabled @endif>
                        -
                      <input type="text" class="form-control timepicker txtTime time_to" value="{{$end_time}}" data-date = "{{$yr.'-'.$mth.'-'.$day}}" @if($status)disabled @endif>
                    </td>
                  @endforeach
                </tr>
                @endforeach
              @endforeach
              </tbody>
            </table>

            <div class="container box-roster row">
              <div class="box-footer-left col-md-11">
                <button type="button" class="btn btn-danger delete_all">Delete</button>
              </div>
              <div class="box-footer-right col-md-1">
                <button id="addrow" class="btn btn-success">Add Row</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.10.0/jquery.timepicker.min.js"></script>
<script type="text/javascript">

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
console.log(getMonths('Mar',2011))

  $('body').on('change','.time_from',function(e){
    e.preventDefault();
    var row_type = $(this).parent().parent().data('row-type');
    var type = 'start_time';
    var time_from = $(this).val();
    var date = $(this).data('date');
    var roster_id = $(this).parent().parent().data('roster-id');
    if(row_type=='new_row'){
      var client_id = $(this).parent().siblings('.client_td').children('.client_name').val();
      var employee_id = $(this).parent().siblings('.employee_td').children('.employee_name').val();
      newTimetable(type,client_id,employee_id,time_from,date);
    }
    else if(row_type=='old_row'){
      updateTimetable(type,roster_id,time_from,date);
    }
    
  });

  $('body').on('change','.time_to',function(e){
    e.preventDefault();
    var row_type = $(this).parent().parent().data('row-type');
    var type = 'end_time';
    var time_to = $(this).val();
    var date = $(this).data('date');
    var roster_id = $(this).parent().parent().data('roster-id');
    if(row_type=='new_row'){
      var client_id = $(this).parent().siblings('.client_td').children('.client_name').val();
      var employee_id = $(this).parent().siblings('.employee_td').children('.employee_name').val();
      newTimetable(type,client_id,employee_id,time_to,date);
    }
    else if(row_type=='old_row'){
      updateTimetable(type,roster_id,time_to,date);
    }
  });

  function updateTimetable(type,roster_id,time,date){
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
      },
      error: function(response){
      
      }
    });
  }

  function newTimetable(type,client_id,employee_id,time,date){
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
      },
      error: function(response){
      
      }
    });
  }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-confirmation/1.0.5/bootstrap-confirmation.min.js"></script>
<script type="text/javascript">
  $(function () {

    $('.timepicker').timepicker({ 'timeFormat': 'H:i' });
    $('#full_date').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'yyyy-mm'
    });

    var counter = 1;
    $('body').on('click','#addrow',function(e){
        e.preventDefault();
        var newRow = $("<tr style='text-align: center;' role='row' data-row-type='new_row'>");
        var cols = "";
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger" value="X"></td>';
        cols += '<td class="employee_td"><select class="employee_name" required><option value="" selected disabled>Select Employee</option>@foreach($employees as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select></td>';
        cols += '<td class="client_td"><select class="client_name" required><option value selected disabled>Select Client</option>@foreach($clients as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select></td>';
        cols += '@for($i=1; $i<=$total_days; $i++) @php if($i>=1 && $i<=7)$week = "week_1";elseif($i>=8 && $i<=14)$week = "week_2";elseif($i>=15 && $i<=21)$week = "week_3";elseif($i>=22 && $i<=28)$week = "week_4";elseif($i>=29 && $i<=31)$week = "week_5"; @endphp <td class="{{$week}}"><input type="text" class="form-control timepicker txtTime time_from"  data-date = "{{$yr.'-'.$mth.'-'.$i}}">-<input type="text" class="form-control timepicker txtTime time_to"  data-date = "{{$yr.'-'.$mth.'-'.$i}}"></td>@endfor';
        newRow.append(cols);
        $("tbody.roster-list").append(newRow);
        $('.timepicker').timepicker({ 'timeFormat': 'H:i' });

        counter++;
        $('#addrow').hide();
        $('.week_1').hide();
        $('.week_2').hide();
        $('.week_3').hide();
        $('.week_4').hide();
        $('.week_5').hide();
        $('.week_'+curr_week).show();
    });

    $("tbody.roster-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1;
        $('#addrow').show();
    });
    
    $('.select2').select2();

    //delete roster rows
    $('#check_all').on('click', function(e) {
     if($(this).is(':checked',true))  
     {
        $(".sub_chk").prop('checked', true);  
     } else {  
        $(".sub_chk").prop('checked',false);  
     }  
    });

    $('.delete_all').on('click', function(e) {
        var sel_Rows = []; 
        $(".sub_chk:checked").each(function() {  
            sel_Rows.push($(this).parent().parent().data('roster-id'));
        });  
        if(sel_Rows.length <= 0)  
        {  
          alert("Please select row to delete.");
          e.preventDefault();
        }  
        else {
            var check = confirm("Are you sure you want to delete this row?");  
            if(check == true){  
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
        }  
    });

    var arr = [1,2,3,4,5];
    var today = moment().format('D');
    var curr_week;
    // Select Current Week
    arr.forEach(function(a){
      if((today/7)>(a-1) && (today/7)<=a){
        curr_week = a;
        $('.week_'+a).show();
        $('#b_week_'+a).addClass('btn-success').css('color','white');
        arr.forEach(function(b){
          if(a!=b){
            $('.week_'+b).hide();
          }
        });
      }
    });

    // Week Switch
    arr.forEach(function(a){
      $('#b_week_'+a).on('click', function(e) {
        $('.week_'+a).show();
        $(this).addClass('btn-success').css('color','white');
        arr.forEach(function(b){
          if(a!=b){
            $('#b_week_'+b).removeClass('btn-success').css('color','black');
            $('.week_'+b).hide();
          }
        })
      })
    })

  });
</script>
  
@endpush