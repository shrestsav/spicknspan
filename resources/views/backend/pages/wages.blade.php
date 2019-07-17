@extends('backend.layouts.app',['title'=> 'Wages'])

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary collapsed-box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Set Employee Wage</h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body pad">
          <form role="form" action="{{route('wages.store')}}" method="POST">
            @csrf
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label>Employee Name</label>
                  <select class="form-control select2" name="employee_id" style="width: 100%;">
                    <option selected disabled>Select Employee</option>
                    @foreach($employees as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label>Client Name</label>
                  <select class="form-control select2" name="client_id" style="width: 100%;">
                    <option selected disabled>Select Client</option>
                    @foreach($clients as $user)
                        <option value="{{$user->id}}">{{$user->name}}</option>
                    @endforeach
                  </select>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="hourly_rate">Base Hourly Rate ($)</label>
                  <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Select Hourly Rate">
                </div>
              </div>
              <div class="col-md-12">
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    @permission('import_export_excel')
    <div class="col-lg-12">
      <div class="box box-default collapsed-box box-solid">
        <div class="box-header">
          <h3 class="box-title">IMPORT FROM EXCEL
            <small>Format: xlsx</small>
          </h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body">
          <form action="{{ route('import_from_excel','wages') }}" method="POST" enctype="multipart/form-data" data-toggle="validator">
            @csrf
            <div class="col-md-12" style="text-align: center;">
              <div class="form-group">
                <label for="file"><a href="javascript:;"  data-toggle="modal" data-target="#modal-info"> Please Read this before using this feature</a></label><br><br>
                <input type="file" name="file" class="form-control jfilestyle" required>
                <div class="help-block with-errors"></div>
                <button class="btn btn-success" type="submit">Import Wages Records</button>
              </div>
            </div>  
          </form>
        </div>
      </div>
    </div>
    @endpermission
    <div class="col-md-12">
      @if(Request::all())
        <a href="{{url('/wages')}}"><button class="btn btn-primary">Show All</button></a>
      @endif
      @permission('import_export_excel')
        <div class="pull-right">
          <form role="form" action="{{route('export.excel')}}" method="POST">
            @csrf
            <input type="hidden" name="type" value="wages">
            <button type="submit" class="btn btn-success">Export to Excel</button>
          </form>
        </div>
      @endpermission
    </div>
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <div class="search_form">
            <form autocomplete="off" role="form" action="{{route('wages.search')}}" method="POST" enctype="multipart/form-data">
              @csrf
              @php 
                $search_arr = [
                  'Employee Name' => [
                    'data'    => 'employees',
                    'name'    => 'search_by_user_id'
                  ],
                  'Client Name' => [
                    'data'    => 'clients',
                    'name'    => 'search_by_client_id'
                  ],
                ]
              @endphp

              @foreach($search_arr as $part => $arr)
                <select class="select2 {{$arr['name']}}" name="{{$arr['name']}}">
                  <option disabled selected value> {{$part}}</option>
                  @foreach(${$arr['data']} as $data)
                    <option value="{{$data->id}}" @if(Request::input($arr['name'])==$data->id) selected @endif>
                      {{$data->name }}
                    </option>
                  @endforeach
                </select>
              @endforeach

              &nbsp; &nbsp; &nbsp;
              <button type="submit" class="btn btn-primary">Search</button>
            </form>
          </div>
        </div>
        <div class="box-body table-responsive no-padding">
          <table id="employee_wages_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>S.No.</th>
              <th>Employee</th>
              <th>Client</th>
              <th>Hourly Rate ($)</th>
            </tr>
            </thead>
            <tbody>
            @php 
              $count = 1;
            @endphp
            @foreach($wages as $wage)
              <div class="dropdown-contextmenu" id="contextmenu_{{$wage->id}}" data-wage_id='{{encrypt($wage->id)}}'>
                <a style="position: relative;"><i class="fa fa-user"></i>{{$wage->employee_name}}</a>
                <hr style="margin-top: 0px; margin-bottom: 0px;">
                <a href="javascript:;" class="btn btn-link edit_wage" title="Edit Wage">
                  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit Wage
                </a>
                <a href="javascript:;" class="btn btn-link delete_wage" title="Delete Wage">
                  <i class="fa fa-trash" aria-hidden="true"></i>Delete Wage
                </a>
              </div>
              <tr data-wage_id='{{encrypt($wage->id)}}' class="contextmenurow" dataid="{{$wage->id}}">
                <td>{{$count++}}</td>
                <td>{{$wage->employee_name}}</td>
                <td>{{$wage->client_name}}</td>
                <td>{{$wage->hourly_rate}}$ / hr</td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="modal modal-info fade" id="modal-info">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Read Very Carefully</h4>
      </div>
      <div class="modal-body">
        <h3></h3>
        <div>
          <ol>
            <li>First Download the excel file provided here for the correct format to upload</li>
            <li>Now open the file and replace the dummy data with your actual data</li>
            <li>Please donot edit or remove any header columns</li>
            <li>The yellow marked column should not be left empty</li>
            <li>Donot leave any empty row after heading row or in between any of the rows</li>
            <li>Donot Try to upload records which already exists</li>
            <li>After you have filled X number of rows, save and upload it.</li>
          </ol>
        </div>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <a href="{{ asset('files/import_from_excel_format(wages).xlsx') }}"><button type="button" class="btn btn-outline">Download Excel Format</button></a>
      </div>
    </div>
  </div>
</div>
</section>

@include('backend.modals.modal', [
            'modalId' => 'editWageModal',
            'modalFile' => '__modal_body',
            'modalTitle' => __('Edit Wage'),
            'modalSize' => 'small_modal_dialog',
        ])

@endsection



@push('scripts')
<script type="text/javascript">

  $('.delete_wage').on('click',function(){
    var wage_id = $(this).parent().data('wage_id');
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this data!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
      if (willDelete) {
        window.location.href = SITE_URL + "deleteWages/"+wage_id;
      } 
    });
  });

  $('.edit_wage').on('click',function(e){
      e.preventDefault();
      var wage_id = $(this).parent().data('wage_id');
      $.ajax({
          type: 'POST',
          url: SITE_URL + 'editWages',
          data: {
              'wage_id': wage_id
          },
          dataType: 'json'
      }).done(function (response) {
          console.log(response);
          detailModel = $('#editWageModal');
          detailModel.find('.modal-content .modal-title').html(response.title);
          detailModel.find('.modal-body').html(response.html);
          detailModel.modal('show');
      });
  });
</script>
  
@endpush