@php

if(Route::current()->getName() == 'user_company.index'){
  $title = 'Company';
  $user_type = 'company';
}
elseif(Route::current()->getName() == 'user_employee.index'){
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

@endphp

@extends('backend.layouts.app',['title'=> $title])

@push('styles')
<style type="text/css">
  label.checkbox.mark_default {
    padding-left: 20px;
  }
</style>

@endpush

@section('content')


<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-primary collapsed-box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Add {{$title}}</h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body pad">
          <form role="form" action="{{route('user.store')}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
            @csrf
            <input type="hidden" class="form-control" name="utilisateur" value="{{encrypt($user_type)}}">
            <div class="col-md-6">
              <div class="form-group col-md-12">
                <div class="col-md-4 col-md-offset-4">
                  <div class="circle">
                    <img class="profile-pic" src="">
                  </div>
                  <div class="p-image">
                    <i class="fa fa-camera upload-button"></i>
                    <input class="file-upload" type="file" name="photo" id="photo" accept="image/*"/>
                  </div>
                  {{-- <label for="photo">Photo</label> --}}
                </div>
              </div>
              <div class="form-group">
                <label for="name">{{($user_type == 'client')?'Client Name *':'Full Name *'}}</label>
                <input type="text" name="name" class="form-control" id="name" placeholder="Enter Name" required>
                <div class="help-block with-errors"></div>
              </div>
              <div class="col-md-6 no-padding">
                <div class="form-group">
                  <label for="gender">Gender</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="gender" id="male" value="male" checked="">
                      Male
                    </label>
                    <label>
                      <input type="radio" name="gender" id="female" value="female">
                      Female
                    </label>
                  </div>
                </div>
              </div>
              <div class="col-md-6 no-padding">
                <div class="form-group">
                  <label for="date_of_birth">Date of Birth</label>
                  <input type="date" name="date_of_birth" class="form-control" id="date_of_birth" placeholder="Enter Date of Birth">
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="col-md-6 no-padding" style="padding-right: 10px !important;">
                <div class="form-group">
                  <label for="contact">{{($user_type == 'client')?'Client Phone Number *':'Phone Number *'}}</label>
                  <input type="text" name="contact" class="form-control" id="contact" placeholder="Enter Phone Number" required>
                </div>
              </div>
              <div class="col-md-6 no-padding">
                <div class="form-group">
                  <label for="email">{{($user_type == 'client')?'Client E-mail address *':'E-mail address *'}}</label>
                  <input type="email" name="email" class="form-control" id="email" placeholder="Enter email" required>
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="form-group">
                <label for="address">Address</label>
                <input type="text" name="address" class="form-control" id="address" placeholder="Enter Address">
                <div class="help-block with-errors"></div>
              </div>
              
              <div class="form-group">
                <label for="documents">Documents</label><br>
                <input type="file" name="documents[]" class="jfilestyle" multiple>
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12 no-padding">
                <div class="form-group col-md-3">
                  <label for="timezone">Timezone</label><br>
                  <select class="select2" id="timezone" name="timezone" required>
                    {{-- Timezones will be filled by moment js --}}
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
                <div class="form-group col-md-3">
                  <label for="currency">Currency</label><br>
                  <select class="select2" id="currency" name="currency" required>
                    <option selected disabled value>Select Currency</option>
                    @foreach(config('setting.currencies') as $id => $curr)
                    <option value="{{$id}}">{{$curr}}</option>
                    @endforeach
                  </select>
                  <div class="help-block with-errors"></div>
                </div>
              </div>
              <div class="form-group">
                <label for="hourly_rate">Base Hourly Rate</label>
                <input type="number" name="hourly_rate" class="form-control" id="hourly_rate" placeholder="Enter Hourly Rate">
              </div>
              <div class="form-group">
                <label for="annual_salary">Annual Salary</label>
                <input type="number" name="annual_salary" class="form-control" id="annual_salary" placeholder="Enter Annual Salary">
              </div>
              <div class="form-group">
                <label for="description">Description</label>
                <textarea name="description" class="form-control" id="description" placeholder="Enter Description"></textarea><div class="help-block with-errors"></div>
              </div> 
              <div class="form-group">
                <label for="employment_start_date">{{($user_type == 'client')?'Contract Start Date *':'Start Date *'}}</label>
                <input type="date" name="employment_start_date" class="form-control" id="employment_start_date" placeholder="Enter Start Date" required>
                <div class="help-block with-errors"></div>
              </div>
              {{-- @if($user_type=='client')
                <div class="form-group">
                  <label class="checkbox mark_default">
                    <input type="checkbox" name="mark_default" value="1">Mark as default client ?
                  </label>
                </div>
              @endif --}}
              <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" name="password" data-minlength="6" class="form-control" id="password" placeholder="Enter Password" required>
                <div class="help-block with-errors"></div>
              </div>
              <div class="form-group">
                <label for="password">Confirm Password *</label>
                <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" placeholder="Confirm Password" data-match="#password" data-match-error="Passwords don't match"  required>
                <div class="help-block with-errors"></div>
              </div>
              @if(Route::current()->getName() == 'user_employee.index')
                <div class="form-group">
                  <label>Choose Clients</label>
                  <select class="form-control select2" name="client_ids[]" multiple="multiple" data-placeholder="Select Clients" style="width: 100%;" required>
                    @foreach($clients as $client)
                      <option value="{{$client->id}}">{{$client->name}}</option>
                    @endforeach
                  </select>
                </div>
              @endif
            </div>
            <div class="col-md-12">
              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </div>
          </form>
        </div>
        
      </div>
    </div>
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
          <form action="{{ route('import_from_excel') }}" method="POST" enctype="multipart/form-data" data-toggle="validator">
            @csrf
            <input type="hidden" name="user_type" class="form-control" value="{{$user_type}}">
            <div class="col-md-12" style="text-align: center;">
              <div class="form-group">
                <label for="file"><a href="javascript:;"  data-toggle="modal" data-target="#modal-info"> Please Read this before using this feature</a></label><br><br>
                <input type="file" name="file" class="form-control jfilestyle" required>
                <div class="help-block with-errors"></div>
                <button class="btn btn-success" type="submit">Import User Data</button>
              </div>
            </div>  
          </form>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      @permission('import_export_excel')
        <div class="pull-right">
          <form role="form" action="{{route('export.excel')}}" method="POST">
            @csrf
            <input type="hidden" name="type" value="{{$user_type}}">
            <button type="submit" class="btn btn-success">Export to Excel</button>
          </form>
        </div>
      @endpermission
    </div>
    <div class="col-md-12">
      <div class="box"> 
        <div class="box-body table-responsive">
          <table id="users_table" class="table table-bordered table-hover table-striped">
            <thead>
            <tr>
              <th>S.No</th>
              <th>Name</th>
              <th>Email</th>
              <th>Contact No</th>
              <th>Hourly Rate</th>
              <th>Start Date</th>
              <th>Documents</th>
              {{-- <th>Action</th> --}}
            </tr>
            </thead>
            <tbody>
            @php
              $c = 1;
            @endphp
            @foreach($users as $user)
             <div class="dropdown-contextmenu" id="contextmenu_{{$user->id}}">
              <a style="position: relative;"><i class="fa fa-user"></i> {{$user->name}}</a>
              <hr style="margin-top: 0px; margin-bottom: 0px;">
              <a href="#" class="btn btn-link view_user_details" data-user-id="{{$user->id}}" title="View All Details">
                <i class="fa fa-eye" aria-hidden="true"></i>View Details
              </a>
              <a href="{{route('user.edit',$user->id)}}" class="btn btn-link" title="Edit Details">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>Edit Details
              </a>
              @if(Route::current()->getName() == 'user_employee.index')
              <a href="javascript:;" class="btn btn-link delete_user" data-user_id='{{$user->id}}' title="Delete Employee">
                <i class="fa fa-trash" aria-hidden="true"></i>Delete
              </a>
              @endif
            </div>
              <tr class="contextmenurow" dataid="{{$user->id}}">
                <td>{{$c}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->contact}}</td>
                <td>{{$user->hourly_rate}} @if($user->currency){{config('setting.currencies')[$user->currency]}}@endif</td>
                <td>{{$user->employment_start_date}}</td>
                @php
                  $c++;
                  $docs = json_decode($user->documents, true);
                  $count = 'No Docs';
                  if($docs)
                    $count = count($docs);
                @endphp
                <td>{{$count}}</td>
{{--                 <td>
                  <a href="#" class="view_user_details" data-user-id="{{$user->id}}">
                    <span class="action_icons"><i class="fa fa-eye" aria-hidden="true"></i></span>
                  </a>
                  <a href="{{route('user.edit',$user->id)}}">
                    <span class="action_icons"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
                  </a>
                  @if(Route::current()->getName() == 'user_employee.index')
                  <a href="javascript:;" class="delete_user" data-user_id='{{$user->id}}'>
                    <span class="action_icons"><i class="fa fa-trash" aria-hidden="true"></i></span>
                  </a>
                  @endif
                </td> --}}
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
            'modalId' => 'userDetailsModal',
            'modalFile' => '__modal_body',
            'modalTitle' => __('User Details'),
            'modalSize' => 'large_modal_dialog',
        ])

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
            <li>Donot upload employees lists from clients page or clients from employees page and likewise</li>
            <li>After you have filled X number of rows, save and upload it.</li>
          </ol>
        </div>
      </div>
      <div class="modal-footer" style="text-align: center;">
        <a href="{{ asset('files/import_from_excel_format.xlsx') }}"><button type="button" class="btn btn-outline">Download Excel Format</button></a>
      </div>
    </div>
  </div>
</div>








@endsection
@push('scripts')
<script type="text/javascript">
    //Show User Detail Modal
    
    $('.view_user_details').on('click',function (e) {
        e.preventDefault();
        var user_id = $(this).data('user-id');
        $.ajax({
            type: 'POST',
            url: SITE_URL + 'ajax_user_details',
            data: {
                'user_id': user_id
            },
            dataType: 'json'
        }).done(function (response) {
            console.log(response);
            detailModel = $('#userDetailsModal');
            detailModel.find('.modal-content .modal-title').html(response.title);
            detailModel.find('.modal-body').html(response.html);
            detailModel.modal('show');
        });
    });
</script>

<script type="text/javascript">
  $(function () {
    $('#users_table').DataTable()
  });
  $('.delete_user').on('click',function(){
    var user_id = $(this).data('user_id');
    swal({
    title: "Are you sure?",
    text: "Once deleted, you will not be able to recover this data!",
    icon: "warning",
    buttons: true,
    dangerMode: true,
  }).then((willDelete) => {
      if (willDelete) {
        window.location.href = "{{url('delete_user/')}}/"+user_id;
      } 
    });
  });

//To load all the timezones provided by moment-timezone-data
  var timezone = moment.tz.names();
  for (var i = 0; i < timezone.length; i++) {
    $('#timezone').append('<option value="' + timezone[i] + '">' + timezone[i] + '</option>');
  }
 //Guesses the current timezone automatically 
  var guess_current_timezone = moment.tz.guess();
  $("#timezone").val(guess_current_timezone).change();

</script>

@endpush