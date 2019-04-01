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
          <div class="container col-sm-12">
          <div class="box box-primary">
          <div class="box-header with-border">
             <form role="form" action="{{route('roster.store')}}" method="POST">

              <div class="box-header">
                <h3 class="box-title">Roster List</h3>
                <p class="pull-right">
                    <label for="">Month-Year</label>
                    <input name="full_date" type="text" id="full_date" class="txtTime" style="width:85px;" value="<?php //echo $roster['full_date'];?>" autocomplete="off" required>
                    <a id="contentSection_btnRefresh" class="btn btn-warning" href='javascript:WebForm_DoPostBackWithOptions(new WebForm_PostBackOptions("ctl00$contentSection$btnRefresh", "", true, "validation", "", false, true))' style="margin-top: -7px !important;"><i class="fa fa-refresh"></i></a>
                </p>
              </div>

              {{ csrf_field() }}

              <table id="tblRoster" class="table table-hover dataTable no-footer order-list table-striped" role="grid" aria-describedby="tblRoster_info">
                <thead>
                    <tr role="row">
                        <th style="width: 24px; padding-right: 0px !important;" class="sorting_disabled" rowspan="1" colspan="1"><span title="Check All"><input id="contentSection_chkCheckAll" type="checkbox" name="ctl00$contentSection$chkCheckAll" onclick="javascript:setTimeout('__doPostBack(\'ctl00$contentSection$chkCheckAll\',\'\')', 0)"></span></th>
                        <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Employee</th>
                        <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Client</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">1</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">2</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">3</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">4</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">5</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">6</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">7</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">8</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">9</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">10</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">11</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">12</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">13</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">14</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">15</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">16</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">17</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">18</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">19</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">20</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">21</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">22</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">23</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">24</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">25</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">26</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">27</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">28</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">29</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">30</th>
                        <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20px;">31</th>
                      </tr>
                </thead>

                <tbody class="roster-list">

                <?php $k = count($rosters); $x = 0; ?>

                @for ($j=0; $j<$k; $j++)

                <?php
                    $employee_id   = $rosters[$j]['employee_id'];
                    $client_id     = $rosters[$j]['client_id'];
                ?>

                <tr style="text-align: center;" role="row" class="odd">
                  <input type="hidden" name="counter" value="0">
                    <td>
                        <span class="chkRow"><input id="check_row_id" type="checkbox" name="roster_check"></span>
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
                                            ->where('employee_id','=',$employee_id)
                                            ->where('client_id','=',$client_id)
                                            ->get();
                        $working_time = json_decode($working_time, true);
                    
                        // print_r($working_time);
                        // die();
                        $time_table = DB::table('rosters_timetable')
                                            ->where('rosters_id','=', $working_time)
                                            ->get();
                        $time_table = json_decode($time_table, true);

                        $full_date  =  $time_table[$j]['full_date'];
                        $month_part = explode('-', $full_date);
                        $month = $month_part[1];

                        if(($month == '01') || ($month == '03') || ($month == '05') || ($month == '07') || ($month == '08') || ($month == '10') || ($month == '12')){
                            $days = 31;
                        } 
                        elseif(($month == '04') || ($month == '06') || ($month == '09') || ($month == '11')){
                            $days = 30;
                        } 
                        else{
                            $days = 28;
                        }
                  ?>
                    <input type="hidden" name="old_rosters_id[]" value="<?php echo $working_time[0]['id'];?>">

                    @for ($i = 0; $i < $days; $i++)
                        <td>
                            <input name="start_time_<?php echo $i;?>" type="text" id="start_time" class="timepicker txtTime" style="width:40px;" value="<?php echo $time_table[$i]['start_time'];?>">
                              <br>to<br>
                            <input name="end_time_<?php echo $i;?>" type="text" id="end_time" class="timepicker txtTime" style="width:40px;" value="<?php echo $time_table[$i]['end_time'];?>">
                        </td>
                    @endfor

                  </tr>
                @endfor
              </tbody>
            </table>

            <div class="container box-roster row">
              <div class="box-footer-left">
                <button type="submit" class="btn btn-primary">Update</button>
                <button type="" class="btn btn-danger">Delete</button>
              </div>
              <div class="box-footer-right">
                <button id="addrow" class="btn btn-primary">Add Row</button>
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
    $(function () {
      $('#tblRoster').DataTable( {
          "scrollX": true
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
          var newRow = $("<tr style='text-align: center;' role='row' class='odd'>");
          var cols = "";
          // cols += '<td><span class="chkRow"><input type="checkbox" name="roster_check"></span></td>';
          cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger" value="X"></td>';
          cols += '<td><select name="employee_id[]" class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true"><?php if ($employee->count()){?><option selected disabled>Select Employee</option><?php foreach($employee as $user){?><option value="<?php echo $user->id;?>"><?php echo $user->name;?></option><?php } } ?></select></td>';
          cols += '<td><select name="client_id[]" id="client_name" class="form-control select2" style="width: 100%;" tabindex="-1" aria-hidden="true"><?php if ($client->count()){?><option selected disabled>Select Client</option><?php foreach($client as $user){?><option value="<?php echo $user->id;?>"><?php echo $user->name;?></option><?php } } ?></select></td>';
          cols += '<?php for ($i = 1; $i <= 31; $i++){?><td><input name="start_time_<?php echo $i;?>" type="text" id="start_time" class="timepicker txtTime" style="width:40px;"><br>to<br><input name="end_time_<?php echo $i;?>" type="text" id="end_time" class="timepicker txtTime" style="width:40px;"></td><?php } ?>';
          newRow.append(cols);
          $("tbody.roster-list").append(newRow);
          $('.timepicker').timepicker({ 'timeFormat': 'H:i' });

          counter++;
      });

      $("tbody.roster-list").on("click", ".ibtnDel", function (event) {
          $(this).closest("tr").remove();       
          counter -= 1
      });
      
      $('.select2').select2();

    })
  </script>
    
  @endpush