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
</style>
@endpush

@section('content')

@php
  $total_days =  count($all_days);
  // echo '<pre>'; print_r($all_days); echo '</pre>';
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

        <div class="box box-primary">
          <div class="box-header with-border">
            <form role="form" action="{{route('roster.index')}}" method="POST">
              @csrf
              <div class="box-header">
                <h3 class="box-title"></h3>
                <div class="col-md-10 text-center">
                  <button id='b_week_1' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 1</button>
                  <button id='b_week_2' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 2</button>
                  <button id='b_week_3' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 3</button>
                  <button id='b_week_4' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 4</button>
                  <button id='b_week_5' class="btn btn-default week_selector" onclick="event.preventDefault();">Week 5</button>
                </div>
                <div class="col-md-2 pull-right">
                    <label for="">MONTH : </label>
                    <input name="year_month" type="text" id="year_month" class="txtTime" style="width:85px;" value="{{$year.'-'.$month}}" autocomplete="off" required>
                    <button type="submit" class="btn btn-warning"><i class="fa fa-refresh"></i></button>
                </div>
              </div>

            </form>

            
          </div>
          <div class="box-body {{-- table-responsive  --}}no-padding">
            <table id="tblRoster" class="table table-hover table-bordered dataTable no-footer order-list" role="grid" aria-describedby="tblRoster_info">
              <thead class="thead-dark">
                <tr role="row">
                  <th><input type="checkbox" id="check_all"></th>
                  <th class="" style="min-width: 120px;">EMPLOYEE</th>
                  <th class="" style="min-width: 120px;">CLIENT</th>
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
                      <input type="text" class="form-control timepicker txtTime time_from" value=" @if($status){{config('setting.leave_types')[$leave_type]}} @else{{$start_time}}@endif" data-date = "{{$year.'-'.$month.'-'.$day}}" @if($status)disabled @endif>
                        -
                      <input type="text" class="form-control timepicker txtTime time_to" value="@if($status){{config('setting.leave_types')[$leave_type]}} @else{{$end_time}}@endif" data-date = "{{$year.'-'.$month.'-'.$day}}" @if($status)disabled @endif>
                    </td>
                  @endforeach
                </tr>
                @endforeach
              @endforeach
              </tbody>
            </table>
          </div>
          <div class="box-footer clearfix">
              <button type="button" class="btn btn-danger delete_all">Delete</button>
              <button id="addrow" class="btn btn-success pull-right">Add Row</button>
            </div>
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
    var full_date = moment().format('YYYY-MM-DD');
    var year_month = moment().format('YYYY-MM');
    var sel_year_month = '{{$year."-".$month}}';
    var today = moment().format('D');
    var total_days = Number('{{$total_days}}');
    var leave_types = JSON.parse('{!! json_encode(config("setting.leave_types")) !!}');
    var curr_week;

    // Select Current Week
    if(year_month==sel_year_month){
      arr.forEach(function(a){
        if((today/7)>(a-1) && (today/7)<=a){
          curr_week = a;
          $('.week_'+a).show();
          $('.'+full_date).addClass('bg-danger');
          $('#b_week_'+a).addClass('btn-success').css('color','white');
          arr.forEach(function(b){
            if(a!=b){
              $('.week_'+b).hide();
            }
          });
        }
      });
    }
    else{
        $('.week_1').show();
        $('#b_week_1').addClass('btn-success').css('color','white');
        $('.week_2').hide();
        $('.week_3').hide();
        $('.week_4').hide();
        $('.week_5').hide();
      }
      
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
        });
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

        for(i=1;i<=total_days;i++){
          if(i>=1 && i<=7)
            week = "week_1";
          else if(i>=8 && i<=14)
            week = "week_2";
          else if(i>=15 && i<=21)
            week = "week_3";
          else if(i>=22 && i<=28)
            week = "week_4";
          else if(i>=29 && i<=31)
            week = "week_5";

          var this_date = sel_year_month + "-" + i;
          cols += '<td class="'+week+'">';
          cols += '<input type="text" class="form-control timepicker txtTime time_from"  data-date = "'+this_date+'">-';
          cols += '<input type="text" class="form-control timepicker txtTime time_to"  data-date = "'+this_date+'">';
          cols += '</td>';
        }
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
          
        },
        error: function(response){
          $.each(response.responseJSON, function(index, val){
            console.log(index+":"+val);
            showNotify('danger',val); 
          });
          my.val('');
        }
      });
    })

    $("tbody.roster-list").on("click", ".ibtnDel", function (event) {
        $(this).closest("tr").remove();       
        counter -= 1;
        $('#addrow').show();
    });
    
    $('.select2').select2();

    //delete roster rows
    $('#check_all').on('click', function(e){
      e.preventDefault();
      if($(this).is(':checked',true))  
        $(".sub_chk").prop('checked', true);  
      else  
        $(".sub_chk").prop('checked',false); 
    });

    $('.delete_all').on('click', function(e) {
      e.preventDefault();
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

  });
</script>
  
@endpush