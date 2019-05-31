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
    <div class="col-md-12">
      @permission('import_export_excel')
        <div class="pull-right">
          <a href="{{ route('export_to_excel',Route::current()->getName()) }}" class="export_to_excel">
            <button class="btn btn-success">Export to Excel</button>
          </a>
        </div>
      @endpermission
      @if(Request::all())
        <a href="{{url('/wages')}}"><button class="btn btn-primary">Show All</button></a>
      @endif
    </div>
    
    <div class="col-md-12">

      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Employee Wages List</h3>
          {{-- Search Form --}}
          <div class="search_form pull-right">
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
        <div class="box-body">
          <table id="employee_wages_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>Employee</th>
              <th>Client</th>
              <th>Hourly Rate ($)</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($wages as $wage)
              <tr data-wage_id='{{encrypt($wage->id)}}'>
                <td>{{$wage->employee_name}}</td>
                <td>{{$wage->client_name}}</td>
                <td>{{$wage->hourly_rate}}$ / hr</td>
                <td>
                  <a href="javascript:;" class="edit_wage">
                    <span class="action_icons"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                  </a>
                  <a href="javascript:;" class="delete_wage">
                    <span class="action_icons"><i class="fa fa-trash" aria-hidden="true"></i></span>
                  </a>
                </td>

              </tr>
            @endforeach
            </tbody>
          </table>
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
  $(function () {
    $('#employee_wages_table').DataTable()
  });

  $('.delete_wage').on('click',function(){
    var wage_id = $(this).parent().parent().data('wage_id');
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
      var wage_id = $(this).parent().parent().data('wage_id');
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