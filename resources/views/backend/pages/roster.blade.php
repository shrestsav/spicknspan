@extends('backend.layouts.app',['title'=> 'Roster'])

@push('styles')
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
  echo"<pre>"; print_r($all_days); echo"</pre>";   



@endphp



<!-- Main content -->
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
              <h3 class="box-title">Roster List</h3>
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

          <form role="form" action="{{route('roster.store')}}" method="POST">
            @csrf
            <input name="full_date_add" type="hidden" id="full_dates" class="txtTime" style="width:85px;" value="{{$date_filter}}" autocomplete="off" required>

            <table id="tblRoster" class="table table-hover table-bordered dataTable no-footer order-list" role="grid" aria-describedby="tblRoster_info">
              <thead class="thead-dark">
                @php
                  $month_part   = explode('-', $date_filter);
                  $month        = $month_part[1];
                  $total_days =  count($all_days);
                @endphp
                <div class="col-md-12 text-center">
                  <button id='b_week_1' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 1</button>
                  <button id='b_week_2' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 2</button>
                  <button id='b_week_3' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 3</button>
                  <button id='b_week_4' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 4</button>
                  <button id='b_week_5' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 5</button>

                <br>
                <br>
                </div>
                  <tr role="row">
                    <th><input type="checkbox" id="master"></th>
                    <th class="">Employee</th>
                    <th class="" >Client</th>
                    @foreach($all_days as $date => $day)
                      @php
                        if($date>=1 && $date<=7)
                         $week = 'week_1';
                        if($date>=8 && $date<=14)
                          $week = 'week_2'; 
                        if($date>=15 && $date<=21)
                          $week = 'week_3'; 
                        if($date>=22 && $date<=28)
                          $week = 'week_4'; 
                        if($date>=29 && $date<=31)
                          $week = 'week_5'; 
                      @endphp
                      <th class=" {{$week}}">
                        {{$day}}
                      </th>
                    @endforeach

                  </tr>
              </thead>

              <tbody class="roster-list">
              @foreach($rosters as $client_id => $roster_by_clients)
                @foreach($roster_by_clients as $emp_id => $emp_rosters)
                <tr style="text-align: center;" role="row" class="" id="" data-roster-id="{{$emp_rosters[0]->id}}" data-row-type="old_row">
                  <td>
                      <input type="checkbox" class="sub_chk" data-id="">
                  </td>
                  <td>
                    {{\App\User::find($emp_id)->name}}
                  </td>
                  <td>
                    {{\App\User::find($client_id)->name}}
                  </td>
                  @for ($i = 1; $i<=$total_days; $i++)
                    @php
                      if($i>=1 && $i<=7)
                        $week = 'week_1';
                      elseif($i>=8 && $i<=14)
                        $week = 'week_2';
                      elseif($i>=15 && $i<=21)
                        $week = 'week_3';
                      elseif($i>=22 && $i<=28)
                        $week = 'week_4';
                      elseif($i>=29 && $i<=32)
                        $week = 'week_5'; 

                      $start_time = '';
                      $end_time = '';
                      foreach($emp_rosters as $roster){
                        $day = \Carbon\Carbon::parse($roster->date)->format('d');
                        if($day==$i){
                          $start_time = $roster->start_time;
                          $end_time = $roster->end_time;
                        }
                      }
                    @endphp
                    <td class="{{$week}}">
                      <input type="text" class="timepicker txtTime time_from" value="{{$start_time}}" data-date = "{{$yr.'-'.$mth.'-'.$i}}">
                        <br>-<br>
                      <input type="text" class="timepicker txtTime time_to" value="{{$end_time}}" data-date = "{{$yr.'-'.$mth.'-'.$i}}">
                    </td>
                  @endfor
                </tr>
                @endforeach
              @endforeach
              </tbody>
          </table>

          <div class="container box-roster row">
            <div class="box-footer-left col-md-11">
              {{-- <button type="submit" class="btn btn-primary">Update</button> --}}
              <button class="btn btn-danger delete_all" data-url="{{ url('rosterDeleteAll') }}">Delete All Selected</button>
              <!-- <button type="" class="btn btn-danger delete_all">Delete</button> -->
            </div>
            <div class="box-footer-right col-md-1">
              <button id="addrow" class="btn btn-success">Add Row</button>
            </div>
          </div>

        </form>
        </div></div></div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
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
  
  // $('body').on('change','.employee_name',function(e){
  //    e.preventDefault();
  //    $('.time_from').prop('readonly','true');
  // })

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
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger" value="X"><input type="button" class="ibtnSave btn btn-md btn-success" value="Y"></td>';
        cols += '<td class="employee_td"><select class="form-control employee_name" required><option value="" selected disabled>Select Employee</option>@foreach($employees as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select></td>';
        cols += '<td class="client_td"><select class="form-control client_name" required><option value selected disabled>Select Client</option>@foreach($clients as $user)<option value="{{$user->id}}">{{$user->name}}</option>@endforeach</select></td>';
        cols += '@for($i=1; $i<=$total_days; $i++) @php if($i>=1 && $i<=7)$week = "week_1";elseif($i>=8 && $i<=14)$week = "week_2";elseif($i>=15 && $i<=21)$week = "week_3";elseif($i>=22 && $i<=28)$week = "week_4";elseif($i>=29 && $i<=31)$week = "week_5"; @endphp <td class="{{$week}}"><input type="text" class="timepicker txtTime time_from"  data-date = "{{$yr.'-'.$mth.'-'.$i}}"><br>-<br><input type="text" class="timepicker txtTime time_to"  data-date = "{{$yr.'-'.$mth.'-'.$i}}"></td>@endfor';
        newRow.append(cols);
        $("tbody.roster-list").append(newRow);
        $('.timepicker').timepicker({ 'timeFormat': 'H:i' });

        counter++;
        $('#addrow').hide();
        $('.week_2').hide();
        $('.week_3').hide();
        $('.week_4').hide();
        $('.week_5').hide();
    });

    $("tbody.roster-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1;
        $('#addrow').show();
    });
    
    $('.select2').select2();

    //delete roster rows
    $('#master').on('click', function(e) {
     if($(this).is(':checked',true))  
     {
        $(".sub_chk").prop('checked', true);  
     } else {  
        $(".sub_chk").prop('checked',false);  
     }  
    });

    $('.delete_all').on('click', function(e) {

        var allVals = [];  
        $(".sub_chk:checked").each(function() {  
            allVals.push($(this).attr('data-id'));
        });  

        if(allVals.length <= 0)  
        {  
            alert("Please select row.");
            e.preventDefault();
        }  
        else {
            var check = confirm("Are you sure you want to delete this row?");  
            if(check == true){  
              var join_selected_values = allVals.join(",");

                $.ajax({
                    url: $(this).data('url'),
                    type: 'DELETE',
                    data: 'ids='+join_selected_values,
                    success: function (data) {
                        if (data['success']) {
                            $(".sub_chk:checked").each(function() {  
                                $(this).parents("tr").remove();
                            });
                            alert(data['success']);
                        } else if (data['error']) {
                            alert(data['error']);
                        } else {
                            alert('Whoops Something went wrong!!');
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });

              $.each(allVals, function( index, value ) {
                  $('table tr').filter("[data-row-id='" + value + "']").remove();
              });
            }  
        }  
    });

    $('[data-toggle=confirmation]').confirmation({
        rootSelector: '[data-toggle=confirmation]',
        onConfirm: function (event, element) {
            element.trigger('confirm');
        }
    });

    $(document).on('confirm', function (e) {
        var ele = e.target;
        e.preventDefault();

        $.ajax({
            url: ele.href,
            type: 'DELETE',
            success: function (data) {
                if (data['success']) {
                    $("#" + data['tr']).slideUp("slow");
                    alert(data['success']);
                } else if (data['error']) {
                    alert(data['error']);
                } else {
                    alert('Whoops Something went wrong!!');
                }
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
        return false;
    });

      $('.week_1').show();
      $('.week_2').hide();
      $('.week_3').hide();
      $('.week_4').hide();
      $('.week_5').hide();


    $('#b_week_1').on('click', function(e) {
      $('.week_1').show();
      $(this).addClass('btn-success').css('color','white');
      $(this).addClass('btn-success').css('color','white');
      $(this).addClass('btn-success').css('color','white');
      $(this).addClass('btn-success').css('color','white');
      $(this).addClass('btn-success').css('color','white');
      $('.week_2').hide();
      $('.week_3').hide();
      $('.week_4').hide();
      $('.week_5').hide();
    })
    $('#b_week_2').on('click', function(e) {
      $('.week_1').hide();
      $('.week_2').show();
      $('.week_3').hide();
      $('.week_4').hide();
      $('.week_5').hide();
    })
    $('#b_week_3').on('click', function(e) {
      $('.week_1').hide();
      $('.week_2').hide();
      $('.week_3').show();
      $('.week_4').hide();
      $('.week_5').hide();
    })
    $('#b_week_4').on('click', function(e) {
      $('.week_1').hide();
      $('.week_2').hide();
      $('.week_3').hide();
      $('.week_4').show();
      $('.week_5').hide();
    })
    $('#b_week_5').on('click', function(e) {
      $('.week_1').hide();
      $('.week_2').hide();
      $('.week_3').hide();
      $('.week_4').hide();
      $('.week_5').show();
    })
  });
</script>
  
@endpush