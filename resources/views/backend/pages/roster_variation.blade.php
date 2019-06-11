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
      <div class="box box-info">
        <div class="box-header">
          <h3 class="box-title">Variations Approval</h3>
        </div>

        <table class="table">
          <thead>
            <tr>
              <th>S.No</th>
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
            @php
              $count = 1;
            @endphp
            @foreach($roster_variations as $r_variation)
              @if($r_variation->status==null)
                @php
                  $attended_period = 'Not Checked in';
                  if($r_variation->attended_period){
                    $attended_period = gmdate('H:i', $r_variation->attended_period);
                  }
                @endphp
                <tr>
                  <td>{{$count}}</td>
                  <td>{{$r_variation->date}}</td>
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
                      {{-- <form action="{{ url('/roster-variation/accept/').'/'.$r_variation->id.'/'.$r_variation->date}}" method="POST" style="display: inline-block;"> --}}
                        {{-- {{ csrf_field() }} --}}
                        <button type="button" class="btn btn-success approve_variation" data-timetable-id="{{$r_variation->timetable_id}}">Approve</button>
                      {{-- </form> --}}
                      {{-- <form action="{{ url('/roster-variation/decline/').'/'.$r_variation->id.'/'.$r_variation->date}}" method="POST" style="display: inline-block;"> --}}
                        {{-- {{ csrf_field() }} --}}
                        <button type="button"class="btn btn-warning decline_variation" data-timetable-id="{{$r_variation->timetable_id}}">Decline</button>
                      {{-- </form> --}}
                    @endif
                  
                  </td>
                </tr>
                @php
                  $count++;
                @endphp
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
              <th>Approved By</th>
              <th>Remarks</th>
            </tr>
            <tr class="search">
                <td>Date</td>
                <td>Client Name</td>
                <td>Employee Name</td>
                <td>Rostered Period</td>
                <td>Attended Period</td>
                <td>Variation</td>
                <td>Status</td>
                <td>Approved By</td>
                <td>Remarks</td>
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
                  <td>{{$r_variation->date}}</td>
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
                  <td>{{$r_variation->approved_by_name}}</td>
                  <td>{{$r_variation->remarks}}</td>
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

    $('#date').datepicker({
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

});
</script>

<script type="text/javascript">

  $('.approve_variation').on('click',function(e){
    e.preventDefault();
    var timetable_id = $(this).data('timetable-id');
    variationStatus('approveVariation',timetable_id);
  });

  $('.decline_variation').on('click',function(e){
    e.preventDefault();
    var timetable_id = $(this).data('timetable-id');
    variationStatus('declineVariation',timetable_id);
  });

  function variationStatus(route,id){
    swal("Remarks", {
      content: "input",
      button: {
        text: "OK",
        closeModal: false,
      },
    })
    .then((value) => {
      if (!value) throw null;
      $.ajax({
        type: 'POST',
        url: SITE_URL + route,
        data: {
          timetable_id: id,
          remarks: value,
        },
        dataType: 'json',
        success:function(data) {
          console.log(data);
          showNotify('success',data);
          location.reload();
        },
        error: function(response){
          $.each(response.responseJSON, function(index, val){
            console.log(index+":"+val);
            showNotify('danger',val); 
          });
        }
      });

    }).catch(err => {
      if (err) {
        swal("Oh noes!", "The AJAX request failed!", "error");
      } else {
        swal.stopLoading();
        swal.close();
      }
    });

    
  }
</script>
  
@endpush