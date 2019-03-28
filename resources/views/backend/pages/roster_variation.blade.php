@extends('backend.layouts.app',['title'=> 'Roster Variation'])

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
        <!--<div class="col-xs-12">-->
            <div class="box box-info">
                <div class="box-header">
                    <h3 class="box-title">Roster Variation List</h3>
                    <p class="pull-right">
                        <label for="">Month-Year</label>
                        <input name="full_date" type="text" id="full_date" class="txtTime" style="width:85px;" autocomplete="off">
                        <a id="contentSection_btnRefresh" class="btn btn-warning" href='javascript:WebForm_DoPostBackWithOptions(new WebForm_PostBackOptions("ctl00$contentSection$btnRefresh", "", true, "validation", "", false, true))' style="margin-top: -7px !important;"><i class="fa fa-refresh"></i></a>
                    </p>
                </div>

                <table id="tblRosterVariation" class="table table-hover dataTable no-footer order-list table-striped" role="grid" aria-describedby="tblRoster_info">
              <thead>
                  <tr role="row">
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Date</th>
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Client Name</th>
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Employee Name</th>
                      <!-- <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Created By</th> -->
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Total Hours</th>
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Rostered Hours</th>
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Variation Hours</th>
                      <th style="width: 100px;" class="sorting_disabled" rowspan="1" colspan="1">Action</th>
                    </tr>
              </thead>

              <tbody class="roster-list">
                @foreach($variations as $variation)
                <tr style="text-align: center;" role="row" class="odd">
                    
                        <td>
                            <?php $date = $variation->created_at;
                            $date = Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');?>
                            <div class="date"><?php echo $date;?></div>
                        </td>
                        <td>
                            <div class="client_name">
                                @foreach($user_lists as $user_list)
                                    @if($user_list->id == $variation->client_id)
                                        {{$user_list->name}}
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="employee_name">
                                @foreach($user_lists as $user_list)
                                    @if($user_list->id == $variation->employee_id)
                                        {{$user_list->name}}
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <!-- <td>
                            <div class="created_by">Admin</div>
                        </td> -->
                        <td>
                            <div class="total_hours">
                              <?php $roster_val = DB::table('rosters')
                                            ->where('employee_id','=',$variation->employee_id)
                                            ->where('client_id','=',$variation->client_id)
                                            ->where('full_date','=',$date)
                                            ->get();
                                    $roster_val = json_decode($roster_val, true);

                                    $check_in  = $roster_val[0]['start_time'];
                                    $check_out = $roster_val[0]['end_time'];

                                    $tot_hours = round(abs(strtotime($check_in) - strtotime($check_out)) / 3600,2). " Hours";
                                    echo $tot_hours;
                              ?>
                            </div>
                        </td>
                        <td>
                            <div class="rostered_hours">
                              <?php $check_in  = $variation->check_in;
                                    $check_out = $variation->check_out;
                                    $rost_hours = round(abs(strtotime($check_in) - strtotime($check_out)) / 3600,2). " Hours";
                                    echo $rost_hours;
                              ?>
                            </div>
                        </td>
                        <td>
                            <div class="diff_hours">
                                <?php echo $rost_hours;?>
                            </div>
                        </td>
                        <td>
                          <?php if($variation->status == '2'){ ?>
                            <div class="action">
                                <form action="{{ url('/roster-variation/accept/').'/'.$variation->id}}" method="POST">
                                  {{ csrf_field() }}
                                  <input type="hidden" name="_method" value="POST">
                                  <button  class="btn btn-info">Approve</button>
                                </form>
                                <form action="{{ url('/roster-variation/decline/').'/'.$variation->id}}" method="POST">
                                  {{ csrf_field() }}
                                  <input type="hidden" name="_method" value="POST">
                                  <button class="btn btn-warning">Decline</button>
                                </form>
                            </div>
                          <?php } else { ?>
                            <div class="declined_message">Variation Declined</div>
                          <?php } ?>
                        </td>
                    
                </tr>
                @endforeach    
              </tbody>

            </table>

            </div>
            <!-- /.box -->

        <!--</div>-->
    </div>
  </div>
</section>

@endsection

@push('scripts')

<script type="text/javascript">
  $(function () {

    $('#tblRosterVariation').DataTable( {
        "scrollX": true
    } );

    $('#full_date').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'yyyy-mm'
    });

  })
</script>
  
@endpush