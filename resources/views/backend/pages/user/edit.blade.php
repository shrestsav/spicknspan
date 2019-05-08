{{-- @php
if(Route::current()->getName() == 'user_employee.index'){
  $title = 'Employees';
  $user_type = 'employee';
}
elseif(Route::current()->getName() == 'user_contractor.index'){
  $title = 'Contractors';
  $user_type = 'contractor';
}
elseif(Route::current()->getName() == 'user_client.index'){
  $title = 'Clients';
  $user_type = 'client';
}

@endphp --}}

@extends('backend.layouts.app',['title'=> 'Edit User'])

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
      <div class="box box-primary">
        <div class="box-header with-border">
          <h3 class="box-title">Employee Information</h3>
        </div>
        <form role="form" action="{{route('user.update',$user->id)}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
          @csrf
          <div class="box-body pad">
            {{-- Hidden Fields --}}
            <input type="hidden" class="form-control" name="user_type" value="{{$user->user_type}}">
            <div class="col-md-6">
              <div class="form-group col-md-12">
                <div class="col-md-4 col-md-offset-4">
                  <div class="circle">
                    <img class="profile-pic" src="{{ asset('files/users/'.$user->id.'/dp_user_'.$user->id.'.png') }}">
                  </div>
                  <div class="p-image">
                    <i class="fa fa-camera upload-button"></i>
                    <input class="file-upload" type="file" name="photo" id="photo" accept="image/*"/>
                  </div>
                  {{-- <label for="photo">Photo</label> --}}
                </div>
              </div>
              <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" value="{{$user->name}}" required>
              </div>
              <div class="form-group">
                <label for="gender">Gender</label>
                <div class="radio">
                  <label>
                    <input type="radio" name="gender" id="male" value="male" @if($user->gender=='male') checked @endif>
                    Male
                  </label>
                  <label>
                    <input type="radio" name="gender" id="female" value="female" @if($user->gender=='female') checked @endif>
                    Female
                  </label>
                </div>
              </div>
              <div class="form-group">
                <label for="hourly_rate">Base Hourly Rate</label>
                <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Enter Hourly Rate" value="{{$user->hourly_rate}}">
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address" value="{{$user->address}}">
              </div>
              <div class="form-group">
                <label for="contact">Phone Number</label>
                <input type="text" name="contact" class="form-control" id="contact" placeholder="Enter Phone Number" value="{{$user->contact}}" required>
              </div>
              <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" value="{{$user->email}}" required>
              </div>
              <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" placeholder="Enter Date of Birth" value="{{$user->date_of_birth}}">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="annual_salary">Annual Salary</label>
                <input type="number" name="annual_salary" class="form-control" id="annual_salary" placeholder="Enter Annual Salary" value="{{$user->annual_salary}}">
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description">{{$user->description}}</textarea>
              </div>
              <div class="form-group">
                <label for="employment_start_date">Employment Start Date</label>
                <input type="date" name="employment_start_date" class="form-control" id="employment_start_date" placeholder="Enter Enployment Start Date" value="{{$user->employment_start_date}}" required>
              </div>
              <div class="form-group">
                <label for="timezone">Timezone</label><br>
                <select class="select2" id="timezone" name="timezone" required>
                  {{-- Timezones will be filled by moment js --}}
                </select>
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <input type="hidden" name="left_user_doc_array" id="left_user_doc_array" value="">
                <input type="hidden" name="del_user_doc_array" id="del_user_doc_array" value="">
                <label for="documents">Documents</label><br>
                @php 
                  $documents = json_decode($user->documents, true);
                @endphp
                
                @if($documents)
                  @foreach($documents as $key => $document)
                    <div class="input-group document_container" style="width: 350px;">
                      <a href="{{ asset('files/users/'.$user->id.'/'.$document)}}" target="__blank">
                        <input type="text" class="form-control pull-right" value="{{$document}}" readonly>
                      </a>
                      <div class="input-group-addon">
                        <i class="fa fa-window-close delete_document" aria-hidden="true" data-document-pointer="{{$key}}" data-user-id="{{$user->id}}"></i>
                      </div>
                    </div>
                  @endforeach 
                @endif 
                <br><label for="adding">Add Documents</label><br>
                <input type="file" name="documents[]" class="jfilestyle" multiple> 
                <div class="help-block with-errors"></div>
              </div> 
              @if($user->hasRole('employee'))
              <input type="hidden" name="deleted_client_ids" id="deleted_client_ids">
              <input type="hidden" name="added_client_ids" id="added_client_ids">
                <div class="form-group"> 
                  @php 
                    $client_ids = json_decode($user->client_ids);
                  @endphp
                  <label>Choose Clients</label>
                  <select class="form-control select2 client_ids" multiple="multiple" data-placeholder="Select Clients" style="width: 100%;" required>
                    @foreach($clients as $client)
                      <option value="{{$client->id}}" @if(in_array($client->id, $client_ids)) selected @endif>{{$client->name}}</option>
                    @endforeach
                  </select>
                </div>
              @endif
              
            </div>
          </div>
          <div class="box-footer">
            <button type="submit" class="btn btn-primary">Update</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
  <script type="text/javascript">
    //To load all the timezones provided by moment-timezone-data
    var timezone = moment.tz.names();
    for (var i = 0; i < timezone.length; i++) {
      $('#timezone').append('<option value="' + timezone[i] + '">' + timezone[i] + '</option>');
    }
    //Guesses the current timezone automatically 
    var timezone = '{{$user->timezone}}';
    $("#timezone").val(timezone).change();


    var user_doc_json = '@php echo $user->documents @endphp';
    var left_user_doc_array = JSON.parse(user_doc_json);
    var del_user_doc_array = [];
    $('#left_user_doc_array').val(JSON.stringify(left_user_doc_array));
    $('#del_user_doc_array').val(JSON.stringify(del_user_doc_array));


  
    $('.delete_document').on('click',function(e){
      e.preventDefault();
      var my = $(this);
      var document_id = my.data('document-pointer');
      var user_id = my.data('user-id');
      del_user_doc_array.push(left_user_doc_array[document_id]);
      left_user_doc_array.splice(document_id,1);
      $('#left_user_doc_array').val(JSON.stringify(left_user_doc_array));
      $('#del_user_doc_array').val(JSON.stringify(del_user_doc_array));

      my.parent().parent().fadeOut('slow');
    });

    var client_ids = $('.client_ids').val();
    $('.client_ids').on('change',function(e){
      e.preventDefault();
      var sel_client_ids = $(this).val();
      var diff = arr_diff(client_ids,sel_client_ids);
      var added = [];
      var deleted = [];
      diff.forEach(function(entry) {
        if(sel_client_ids.includes(entry))
          added.push(entry);
        else
          deleted.push(entry);
      });
      $('#added_client_ids').val(JSON.stringify(added));
      $('#deleted_client_ids').val(JSON.stringify(deleted));
    });


  function arr_diff (a1, a2) {
    var a = [], diff = [];
    for (var i = 0; i < a1.length; i++) {
        a[a1[i]] = true;
    }
    for (var i = 0; i < a2.length; i++) {
        if (a[a2[i]]) {
            delete a[a2[i]];
        } else {
            a[a2[i]] = true;
        }
    }
    for (var k in a) {
        diff.push(k);
    }
    return diff;
  }





  </script>
@endpush