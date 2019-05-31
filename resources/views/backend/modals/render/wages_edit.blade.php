<form role="form" action="{{route('wages.update')}}" method="POST">
  @csrf
  <input type="hidden" name="wage_id" value="{{$wages->id}}">
  <div class="box-body pad">
    <div class="form-group">
      <label for="employee_id">Employee Name</label>
      <select class="form-control select2">
          <option selected disabled>{{$wages->employee_name}}</option>
      </select>
    </div>
    <div class="form-group">
      <label for="client_id">Client Name</label>
      <select class="form-control select2">
        <option selected disabled>{{$wages->client_name}}</option>
      </select>
    </div>
    <div class="form-group">
      <label for="hourly_rate">Base Hourly Rate ($)</label>
      <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Select Hourly Rate" value="{{$wages->hourly_rate}}">
    </div>
  </div>
  <div class="box-footer">
    <button type="submit" class="btn btn-primary">Update</button>
  </div>
</form>