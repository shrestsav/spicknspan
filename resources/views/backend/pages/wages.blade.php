@extends('backend.layouts.app',['title'=> 'Wages'])

@section('content')

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-6">
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
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Employee Information</h3>
        </div>
        <form role="form" action="{{route('wages.store')}}" method="POST">
          @csrf
          <div class="box-body pad">
            <div class="form-group">
              <label for="employee_id">Employee Name</label>
              <select class="form-control" name="employee_id">

                  @if ($employee->count())
                          <option selected disabled>Select Employee</option>
                      @foreach($employee as $user)
                          <option value="{{$user->id}}">{{$user->name}}</option>
                      @endForeach
                  @endif

              </select>
            </div>
            <div class="form-group">
              <label for="client_id">Client Name</label>
              <select class="form-control" name="client_id">

                  @if ($client->count())
                          <option selected disabled>Select Client</option>
                      @foreach($client as $user)
                          <option value="{{$user->id}}">{{$user->name}}</option>
                      @endForeach
                  @endif

              </select>
            </div>
            <div class="form-group">
              <label for="hourly_rate">Base Hourly Rate ($)</label>
              <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Select Hourly Rate">
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Employee Wages List</h3>
        </div>
        <!-- /.box-header -->
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
                    <tr>
                      @foreach($employee as $user1)
                          @if($wage->employee_id == $user1->id)
                              <td>{{$user1->name}}</td>
                          @endif
                      @endforeach
                      @foreach($client as $user2)
                          @if($wage->client_id == $user2->id)
                              <td>{{$user2->name}}</td>
                          @endif
                      @endforeach
                      <td>{{$wage->hourly_rate}}</td>
                      <form action="{{ url('/wages/').'/'.$wage->id}}" method="POST">
                        {{ csrf_field() }}
                        <input type="hidden" name="_method" value="POST">
                        <td><button>Delete</button></td>
                      </form>
                    </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <!-- /.box-body -->
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script type="text/javascript">
  $(function () {
    $('#employee_wages_table').DataTable()
  })
</script>
  
@endpush