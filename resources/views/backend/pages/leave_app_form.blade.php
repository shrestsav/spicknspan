@extends('backend.layouts.app',['title'=>'Leave Application'])

@push('styles')
<link rel="stylesheet" href="{{ asset('backend/excel-plugins/tableexport.css') }}">
<style type="text/css">
  label.checkbox.mark_default {
    padding-left: 20px;
  }
  .export_to_excel{
    position: absolute;
    top: 7px;
    right: 227px;
    z-index: 1000;
  }
</style>

@endpush

@section('content')


<section class="content">
  <div class="row">
    <div class="col-md-12">
      @if ($errors->any())
          <div class="alert alert-danger">
              @foreach ($errors->all() as $error)
                  {{ $error }}<br>
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
      <div class="box box-primary collapsed-box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Create Leave Application</h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body padding">
          <form role="form" action="{{route('leaveRequest.store')}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
            @csrf
            <div class="col-md-6 col-md-offset-3">
              <div class="form-group">
                <label>Select the type of leave you are requesting</label>
                <select name="leave_type" class="form-control select2" required>
                  <option value disabled selected>Select Leave Type</option>
                  @foreach(config('setting.leave_types') as $id => $leave_typ)
                    <option value="{{$id}}">{{$leave_typ}}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group">
                <label for="">Select Your Leave Period</label>
                <div class="input-group search_by_date">
                  <div class="input-group-addon">
                    <i class="fa fa-calendar"></i>
                  </div>
                  @php
                    $today = date('m/d/Y');
                    $postOneMonth = date("m/d/Y", strtotime( date( "m/d/Y", strtotime( date("m/d/Y") ) ) . "+1 month" ) );
                  @endphp
                  <input type="text" class="form-control pull-right" id="from_to" name="from_to" value="{{$today.' - '.$postOneMonth}}" required>
                </div>
              </div>
              <div class="form-group">
                <label for="">Describe your reasons for leave</label>
                <textarea class="form-control" rows="4" name="description" placeholder="Describe why you need this leave" maxlength="" required></textarea>
              </div>
            </div>
            <div class="col-md-6 col-md-offset-3">
              <div class="box-footer">
                <button type="Submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
        
      </div>
    </div>

    @if($leave_requests)
    <div class="col-xs-12">
      <div class="box">
        <div class="box-body table-responsive no-padding">
          <table class="table table-bordered table-striped table-hover datatable" id="leave_applications">
            <thead>
              <tr>
                <th>Submitted On</th>
                <th>Employee Name</th>
                <th>Leave Type</th>
                <th>Duration</th>
                <th>Days</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($leave_requests as $lr)
                @php 
                  $from = \Carbon\Carbon::parse($lr->from);
                  $to = \Carbon\Carbon::parse($lr->to);
                  $submitted = \Carbon\Carbon::parse($lr->created_at)->format('dS F Y');
                  $days = $from->diffInDays($to);
                @endphp
              <tr>
                <td>{{$submitted}}</td>
                <td>{{$lr->name}}</td>
                <td>{{config('setting.leave_types')[$lr->leave_type]}}</td>
                <td>{{$from->format('dS F Y')}} - {{$to->format('dS F Y')}}</td>
                <td>{{$days}} Days</td>
                <td>
                  @if($lr->status==0)
                    Pending
                  @elseif($lr->status==1)
                    Approved
                  @else
                    Denied
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif



  </div>
</section>

@include('backend.modals.modal', [
            'modalId' => 'incidentReportsModal',
            'modalFile' => '__modal_body',
            'modalTitle' => __('Incident Report Details'),
            'modalSize' => 'large_modal_dialog',
        ])

@endsection
@push('scripts')
<script src="{{ asset('backend/excel-plugins/xlsx.core.min.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/Blob.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/FileSaver.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/Export2Excel.js') }}"></script>
<script src="{{ asset('backend/excel-plugins/jquery.tableexport.v2.js') }}"></script>
<script src="{{ asset('backend/js/character-counter.js') }}"></script>
<script type="text/javascript">
    //Show Incident Detail Modal
    $('.datatable').DataTable({
      "searching": false,
      "paging": false,
      "bInfo": false,
      "dom": '<"top">rt<"bottom"flip><"clear">'
    });

    $("table").tableExport({
      formats: ["xlsx"],
    });

    $('.view_incident_details').on('click',function (e) {
        e.preventDefault();
        var incident_id = $(this).data('incident-id');
        // alert(incident_id);
        $.ajax({
            type: 'POST',
            url: SITE_URL + 'ajax_incident_report_details',
            data: {
                'incident_id': incident_id
            },
            dataType: 'json'
        }).done(function (response) {
            console.log(response);
            detailModel = $('#incidentReportsModal');
            detailModel.find('.modal-content .modal-title').html(response.title);
            detailModel.find('.modal-body').html(response.html);
            detailModel.modal('show');
        });
    });
</script>
<script type="text/javascript">
    $('#from_to').daterangepicker();
  </script>

@endpush