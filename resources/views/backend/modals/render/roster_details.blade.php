
<div class="col-md-12">
  <div class="col-md-3">
      <label>Employee : </label>
      <span>{{$roster_details->employee->name}}</span>
  </div>
  <div class="col-md-3">
      <label>Client : </label>
      <span>{{$roster_details->client->name}}</span>
  </div>  
  <div class="col-md-3">
      <label>Month : </label>
      <span>{{\Carbon\Carbon::parse($roster_details->full_date)->format('F Y')}}</span>
  </div>
  @permission('import_export_excel')
    <div class="pull-right">  
      <form role="form" action="{{route('export.excel')}}" method="POST">
        @csrf
        <input type="hidden" name="type" value="roster_details">
        <input type="hidden" name="roster_id" value="{{$roster_details->id}}">
        <button type="submit" class="btn btn-success">Export to Excel</button>
      </form>
    </div>
  @endpermission
</div>
<div class="row">
  <div class="col-md-12">
    <div class="box-body no-padding">
      <table class="table" id="roster_details_table">
        <thead>
          <tr>
            <th style="width: 10px">S.No.</th>
            <th>Date</th>
            <th>Timing</th>
            <th>Total Time</th>
          </tr>
        </thead>
        <tbody>
          @php $count = 1; @endphp
          @foreach($roster_details->timetable as $timetable)
            @php 
              $formattedDuration = '';
              $formattedStartTime = 'Not Set';
              $formattedEndTime = 'Not Set';
              if($timetable->start_time){
                $startTime = \Carbon\Carbon::parse($timetable->start_time);
                $formattedStartTime = $startTime->format('g:i A');
              }
              if($timetable->end_time){
                $endTime = \Carbon\Carbon::parse($timetable->end_time);
                $formattedEndTime = $endTime->format('g:i A');
              }
              if($timetable->start_time && $timetable->end_time){
                $totalDuration = $endTime->diffInSeconds($startTime);
                $formattedDuration = gmdate('H:i:s', $totalDuration);
              }

              // if($timetable->start_time && $timetable->end_time){
              //   $startTime = \Carbon\Carbon::parse($timetable->start_time);
              //   $endTime = \Carbon\Carbon::parse($timetable->end_time);
              //   $totalDuration = $endTime->diffInSeconds($startTime);
              //   $formattedDuration = gmdate('H:i:s', $totalDuration);
              //   $formattedStartTime = $startTime->format('g:i A');
              //   $formattedEndTime = $endTime->format('g:i A');
              // }
              // elseif($timetable->start_time && !$timetable->end_time){
              //   $formattedStartTime = \Carbon\Carbon::parse($timetable->start_time)->format('g:i A');
              //   $endTime = '';
              // }
              // elseif(!$timetable->start_time && $timetable->end_time){
              //   $startTime = '';
              //   $formattedEndTime = \Carbon\Carbon::parse($timetable->end_time)->format('g:i A');
              // }
            @endphp
            <tr>
              <td style="text-align: center;">{{$count++}}</td>
              <td style="text-align: center;">{{$timetable->date}}</td>

              <td style="text-align: center;">
                {{$formattedStartTime}} - {{$formattedEndTime}}
              </td style="text-align: center;">
              <td style="text-align: center;"><span class="badge bg-red">{{$formattedDuration}}</span></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>