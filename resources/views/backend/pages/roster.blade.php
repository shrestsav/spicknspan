@extends('backend.layouts.app',['title'=> 'Roster'])

@section('content')

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

            {{ csrf_field() }}

            <input name="full_date_add" type="hidden" id="full_dates" class="txtTime" style="width:85px;" value="<?= $date_filter;?>" autocomplete="off" required>

            <table id="tblRoster" class="table table-hover dataTable no-footer order-list table-striped" role="grid" aria-describedby="tblRoster_info">
              <thead>
                <?php
                  $month_part   = explode('-', $date_filter);
                  $month        = $month_part[1];

                      if(($month == '01') || ($month == '03') || ($month == '05') || ($month == '07') || ($month == '08') || ($month == '10') || ($month == '12')){
                          $m_days = 31;
                      }
                      elseif(($month == '04') || ($month == '06') || ($month == '09') || ($month == '11')){
                          $m_days = 30;
                      }
                      elseif($month == '02'){
                          $m_days = 28;
                      }
                ?>
                <button id='b_week_1' class="btn btn-primary" onclick="event.preventDefault();">Week 1</button>
                <button id='b_week_2' class="btn btn-primary" onclick="event.preventDefault();">Week 2</button>
                <button id='b_week_3' class="btn btn-primary" onclick="event.preventDefault();">Week 3</button>
                <button id='b_week_4' class="btn btn-primary" onclick="event.preventDefault();">Week 4</button>
                <button id='b_week_5' class="btn btn-primary" onclick="event.preventDefault();">Week 5</button>
                <br>
                <br>
                  <tr role="row">
                      <th width="50px"><input type="checkbox" id="master"></th>
                      <!-- <th>Action</th> -->
                      <th width="100px" class="sorting_disabled" rowspan="1" colspan="1">Employee</th>
                      <th width="100px" class="sorting_disabled" rowspan="1" colspan="1">Client</th>

                      <?php for($i=1; $i<=$m_days; $i++){ ?>
                          <th class="sorting_disabled <?php if($i>=1 && $i<=7){ echo 'week_1';} if($i>=8 && $i<=14){ echo 'week_2';} if($i>=15 && $i<=21){ echo 'week_3';} if($i>=22 && $i<=28){ echo 'week_4';} if($i>=29 && $i<=31){ echo 'week_5';} ?>" rowspan="1" colspan="1" style="width: 20px;" ><?php echo $i; ?></th>
                      <?php } ?>

                  </tr>
              </thead>

              <tbody class="roster-list">

              <?php 
              if(empty($arr_rosters)){ $empty_val = 'true'; }
              if(!empty($arr_rosters)){

              $k = count($arr_rosters); $x = 0; ?>

              @for ($j=0; $j<$k; $j++)

              <?php
                  $id   = $arr_rosters[$j]['id'];
                  // echo 'asd'.$id;
                  // die();
                  $employee_id   = $arr_rosters[$j]['employee_id'];
                  $client_id     = $arr_rosters[$j]['client_id'];
              ?>

              <tr style="text-align: center;" role="row" class="odd" id="tr_{{$id}}">
                <input type="hidden" name="counter" value="0">
                  <td>
                      <input type="checkbox" class="sub_chk" data-id="{{$id}}">
                  </td>
                  <td>
                      <select name="employee_id[]" id="emp_name" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                          
                          @if ($employee->count())
                                  <option selected disabled>Select Employee</option>
                              @foreach($employee as $user)
                                  <option value="{{ $user->id }}" {{$employee_id == $user->id  ? 'selected' : ''}}>{{ $user->name}}</option>
                              @endForeach
                          @endif
                          
                      </select>
                  </td>

                  <td>
                      <select name="client_id[]" id="client_name" class="form-control select2 select2-hidden-accessible" style="width: 100%;" tabindex="-1" aria-hidden="true">
                        
                        @if ($client->count())
                                <option selected disabled>Select Client</option>
                            @foreach($client as $user)
                                <option value="{{ $user->id }}" {{$client_id == $user->id  ? 'selected' : ''}}>{{ $user->name}}</option>
                            @endForeach
                        @endif
                        
                      </select>
                  </td>

                <?php $working_time = DB::table('rosters')
                                          ->where('employee_id','=', $employee_id)
                                          ->where('client_id','=', $client_id)
                                          ->where('full_date', '=', $date_filter)
                                          ->get();
                      $working_time = json_decode($working_time, true);
                      $r_id = $working_time[0]['id'];
                      // echo $working_time[$j]['id'];
                      // echo '<br>';
                      // die();
                      $time_table = DB::table('roster_timetables')
                                          ->where('roster_id','=', $working_time[0]['id'])
                                          ->get()
                                          ->toArray();

                      // print_r($time_table);
                      // die();
                      $full_date  =  $time_table[0]->full_date;
                ?>
                  <input type="hidden" name="old_roster_id[]" value="<?php echo $working_time[0]['id'];?>">

                  @for ($i = 0; $i<$m_days; $i++)
                      <td class="<?php if($i>=0 && $i<=6){ echo 'week_1';} if($i>=7 && $i<=13){ echo 'week_2';} if($i>=14 && $i<=20){ echo 'week_3';} if($i>=21 && $i<=27){ echo 'week_4';} if($i>=28 && $i<=31){ echo 'week_5';} ?>">
                          <input name="start_time_<?php echo $j;?>[<?php echo $i;?>]" type="text" id="start_time" class="timepicker txtTime" style="width:40px;" value="<?php echo $time_table[$i]->start_time;?>">
                            <br>to<br>
                          <input name="end_time_<?php echo $j;?>[<?php echo $i;?>]" type="text" id="end_time" class="timepicker txtTime" style="width:40px;" value="<?php echo $time_table[$i]->end_time;?>">
                      </td>
                  @endfor

                </tr>
              @endfor
            <?php } ?>
            </tbody>
          </table>

          <div class="container box-roster row">
            <div class="box-footer-left col-md-11">
              <button type="submit" class="btn btn-primary">Update</button>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-confirmation/1.0.5/bootstrap-confirmation.min.js"></script>
<script type="text/javascript">
  $(function () {
    $('#tblRoster').DataTable( {
        "scrollX": false
    } );
    $('.timepicker').timepicker({ 'timeFormat': 'H:i' });
    $('#full_date').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'yyyy-mm'
    });

    var counter = 1;
    $("#addrow").on("click", function () {
        event.preventDefault();
        <?php $empty_val = 'false';?>
        var newRow = $("<tr style='text-align: center;' role='row' class='odd'>");
        var cols = "";
        // cols += '<td><span class="chkRow"><input type="checkbox" name="roster_check"></span></td>';
        cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger" value="X"></td>';
        cols += '<td><select name="employee_id[]" class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" required><?php if ($employee->count()){?><option value="" selected disabled>Select Employee</option><?php foreach($employee as $user){?><option value="<?php echo $user->id;?>"><?php echo $user->name;?></option><?php } } ?></select></td>';
        cols += '<td><select name="client_id[]" id="client_name" class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true" required><?php if ($client->count()){?><option value="" selected disabled>Select Client</option><?php foreach($client as $user){?><option value="<?php echo $user->id;?>"><?php echo $user->name;?></option><?php } } ?></select></td>';
        cols += '<?php for ($i = 1; $i <= $m_days; $i++){?><td class="<?php if($i>=1 && $i<=7){ echo 'week_1';} if($i>=8 && $i<=14){ echo 'week_2';} if($i>=15 && $i<=21){ echo 'week_3';} if($i>=22 && $i<=28){ echo 'week_4';} if($i>=29 && $i<=31){ echo 'week_5';} ?>"><input name="start_time_<?php echo $i;?>" type="text" id="start_time" class="timepicker txtTime" style="width:40px;"><br>to<br><input name="end_time_<?php echo $i;?>" type="text" id="end_time" class="timepicker txtTime" style="width:40px;"></td><?php } ?>';
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