@extends('backend.layouts.app',['title'=> 'Roster Variation'])

@push('styles')
<style type="text/css">
  #employee_attendance_status_filter{
    display: none;
  }
</style>
@endpush
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
    </div>
    <div class="col-md-12">
      <div class="box box-info">
        <div class="box-header">
          <h3 class="box-title">Variations Approval</h3>
        </div>

        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Client Name</th>
              <th>Employee Name</th>
              <th>Rostered Period</th>
              <th>Attended Period</th>
              <th>Variation</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            @foreach($roster_variations as $r_variation)
              @if($r_variation->status==null)
                @php
                  $attended_period = 'Not Checked in';
                  if($r_variation->attended_period){
                    $attended_period = gmdate('H:i', $r_variation->attended_period);
                  }
                @endphp

                <tr>
                  <td>{{$r_variation->full_date}}</td>
                  <td>{{$r_variation->client_name}}</td>
                  <td>{{$r_variation->employee_name}}</td>
                  <td>{{gmdate('H:i', $r_variation->roster_period)}}</td>
                  <td>{{$attended_period}}</td>
                  <td>
                    @if($r_variation->variation < 0)
                    {{gmdate('H:i', abs($r_variation->variation))}} plus attended
                    @elseif($r_variation->variation > 0)
                    {{gmdate('H:i', $r_variation->variation)}} left
                    @endif
                  </td>
                  <td>
                    @if($r_variation->variation)
                      <form action="{{ url('/roster-variation/accept/').'/'.$r_variation->id.'/'.$r_variation->full_date}}" method="POST" style="display: inline-block;">
                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-success">Approve</button>
                      </form>
                      <form action="{{ url('/roster-variation/decline/').'/'.$r_variation->id.'/'.$r_variation->full_date}}" method="POST" style="display: inline-block;">
                        {{ csrf_field() }}
                        <button type="submit"class="btn btn-warning">Decline</button>
                      </form>
                    @endif
                  
                  </td>
                </tr>
              @endif
            @endforeach
         
            
          </tbody>
        </table>
      </div>
      <div class="box box-info">
        <div class="box-header">
          <h3 class="box-title">Employee Attendance Status</h3>
        </div>

        <table class="table" id="employee_attendance_status">
          <thead>
            <tr>
              <th>Date</th>
              <th>Client Name</th>
              <th>Employee Name</th>
              <th>Rostered Period</th>
              <th>Attended Period</th>
              <th>Variation</th>
              <th>Status</th>
            </tr>
            <tr class="search">
                <td>Date</td>
                <td>Client Name</td>
                <td>Employee Name</td>
                <td>Rostered Period</td>
                <td>Attended Period</td>
                <td>Variation</td>
                <td>Status</td>
            </tr>
          </thead>
          <tbody>
            @foreach($roster_variations as $r_variation)
              @if($r_variation->status!=null)
                @php
                  $attended_period = 'Not Checked in';
                  if($r_variation->attended_period){
                    $attended_period = gmdate('H:i', $r_variation->attended_period);
                  }
                @endphp

                <tr>
                  <td>{{$r_variation->full_date}}</td>
                  <td>{{$r_variation->client_name}}</td>
                  <td>{{$r_variation->employee_name}}</td>
                  <td>{{gmdate('H:i', $r_variation->roster_period)}}</td>
                  <td>{{$attended_period}}</td>
                  <td>
                    @if($r_variation->variation < 0)
                    {{gmdate('H:i', abs($r_variation->variation))}} plus attended
                    @elseif($r_variation->variation > 0)
                    {{gmdate('H:i', $r_variation->variation)}} left
                    @endif
                  </td>
                  <td>
                    @if($r_variation->status==1)
                      Approved
                    @else
                      Declined
                    @endif
                  </td>
                </tr>
              @endif
            @endforeach
         
            
          </tbody>
          
        </table>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')

<script type="text/javascript">
  $(function () {

    $('#full_date').datepicker({
        autoclose: true,
        minViewMode: 1,
        format: 'yyyy-mm'
    });

  })
</script>
<script type="text/javascript">
  $(document).ready(function() {
    // Setup - add a text input to each footer cell
    $('#employee_attendance_status .search td').each( function () {
        var title = $(this).text();
        var id = '';
        if(title=='Date')
          id = 'datepicker';
        $(this).html( '<input type="text" placeholder="'+title+'" id="'+id+'" style="width:100%;" />' );
    } );

   //Date picker
    $('#datepicker').datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true
    });

    // DataTable
    var table = $('#employee_attendance_status').DataTable({
      "ordering": false,
       "paging": false,
    });
 
    // Apply the search
    table.columns().every( function () {
        var that = this;
 
        $( 'input', this.header() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
                that
                    .search( this.value )
                    .draw();
            }
        } );
    } );
} );
</script>
  
@endpush