@extends('backend.layouts.app',['title'=>'Incident Report'])

@push('styles')
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
      <div class="box box-primary collapsed-box box-solid">
        <div class="box-header with-border">
          <h3 class="box-title">Create Incident Report</h3>
          <div class="pull-right box-tools">
            <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="" data-original-title="Collapse">
              <i class="fa fa-plus"></i></button>
            <button type="button" class="btn btn-info btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove">
              <i class="fa fa-times"></i></button>
          </div>
        </div>
        <div class="box-body padding">
          <form role="form" action="{{route('incident.store')}}" method="POST" data-toggle="validator" enctype="multipart/form-data">
            @csrf
            <div class="form-group col-md-12">
              @php 
                $incident_types = [
                  'Work Related Illness' => 'work_related_illness',
                  'Plant/Equipment Damage' => 'plant_equipment_damage',
                  'Environment' => 'environment',
                  'Electrocution' => 'electrocution',
                  'Near Miss' => 'near_miss',
                  'Injury' => 'injury',
                ];
              @endphp
              <div class="col-md-12">
                <label for="inputEmail3">INCIDENT TYPE</label>
              </div>
              
              @foreach($incident_types as $display_name => $name)
                <div class="col-sm-2">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="{{$name}}"> {{$display_name}}
                    </label>
                  </div>
                </div>
              @endforeach
            </div>
            <div class="form-group col-md-12">
              <table class="table">
                <thead class="thead-dark">
                  <tr>
                    <th>Name of Person Involved</th>
                    <th>Occupation</th>
                    <th>Employer</th>
                    <th>Contact Number</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="form-group">
                        <input type="text" name="person_involved" class="form-control" placeholder="Name of Person Involved" required>
                        <div class="help-block with-errors"></div>
                      </div>
                    </td>
                    <td>
                      <div class="form-group">
                        <input type="text" name="occupation" class="form-control" placeholder="Occupation" required>
                        <div class="help-block with-errors"></div>
                      </div>
                    </td>
                    <td>
                      <div class="form-group">
                        <input type="text" name="employer" class="form-control" placeholder="Employer" required>
                        <div class="help-block with-errors"></div>
                      </div>
                    </td>
                    <td>
                      <div class="form-group">
                        <input type="text" pattern="\d*" name="contact" class="form-control" maxlength="10" placeholder="Numbers only">
                        <div class="help-block with-errors"></div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="col-md-12">
              @php 
                $incident_details = [
                  'Incident Location' => 'location',
                  'Incident Date & Time' => 'date'
                ];
              @endphp
              <div class="col-md-12">
                <label >INCIDENT DETAILS</label>
              </div>
              @foreach($incident_details as $id_name => $id_id)
                <div class="form-group col-md-4"> 
                  <input @if($id_id=='date') type="datetime-local" @else type="text" @endif class="form-control" name="{{$id_id}}" id="{{$id_id}}" placeholder="{{$id_name}}">
                </div>
              @endforeach
            </div>
            <div class="col-md-12">
              <div class="form-group col-md-4 medical_treatment">
                @php 
                  $medical_treatments = [
                    'None' => 'mt_none',
                    'First Aid' => 'mt_first_aid',
                    'Doctor' => 'mt_doctor',
                    'Hospital' => 'mt_hospital',
                  ];
                @endphp
                <div class="col-md-12">
                  <label>Medical Treatment Required</label>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    @foreach($medical_treatments as $mt_name => $mt_id)
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="{{$mt_id}}" id="{{$mt_id}}" value="{{$mt_id}}" @if($mt_id=='mt_none') checked @endif>
                          {{$mt_name}}
                        </label>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>

              <div class="form-group col-md-4 cease_work">
                <div class="col-md-12">
                  <label for="inputEmail3">Cease work for remainder of shift?</label>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                      <div class="radio">
                        <label>
                          <input type="radio" name="cease_work" id="cw_yes" value="1" >
                          Yes
                        </label>
                      </div>
                      <div class="radio">
                        <label>
                          <input type="radio" name="cease_work" id="cw_no" value="0" >
                          No
                        </label>
                      </div>
                  </div>
                </div>
              </div>

              <div class="form-group col-md-4 attended_authorities">
                @php 
                  $attended_authorities = [
                    'Police' => 'aa_police',
                    'Ambulance' => 'aa_ambulance',
                    'Fire' => 'aa_fire',
                    'Workplace H & S' => 'aa_workplace_h_s',
                    'EPA' => 'aa_epa',
                    'Media' => 'aa_media',
                  ];
                @endphp
                <div class="col-md-12">
                  <label for="inputEmail3">Which Authorities Attended? </label>
                </div>

                <div class="col-md-12">
                  <div class="form-group">
                    @foreach($attended_authorities as $aa_name => $aa_id)
                      <div class="checkbox">
                        <label>
                          <input type="checkbox" name="{{$aa_id}}" id="{{$aa_id}}" value="{{$aa_id}}">
                          {{$aa_name}}
                        </label>
                      </div>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group col-md-12">
              @php 
                $incident_descriptions = [
                  'WHAT' => [
                    'placeholder' => 'Enter what was happening when the incident occurred',
                    'id' => 'desc_what'
                  ],
                  'HOW' => [
                    'placeholder' => 'Enter how it occurred',
                    'id' => 'desc_how'
                  ],
                  'WHY' => [
                    'placeholder' => 'Enter why you think it happened',
                    'id' => 'desc_why'
                  ],
                  'IMMEDIATE ACTIONS' => [
                    'placeholder' => 'Enter what immediate actions have been taken to prevent reoccurrence',
                    'id' => 'desc_immediate_actions'
                  ],
                  'RELEVANT CONTROLS' => [
                    'placeholder' => '',
                    'id' => 'desc_relevant_controls'
                  ],
                ];
              @endphp
              <div class="col-md-12">
                <label>INCIDENT DESCRIPTION <small></small></label>
              </div>
              <br><br>
              <div class="col-md-12">
                @foreach($incident_descriptions as $incident_desc_name => $incident_desc_data)
                  @php
                    $textarea_class = 'desc_texts';
                    $maxlength = '530';
                    if($incident_desc_data['id']=='desc_relevant_controls' || $incident_desc_data['id']=='desc_immediate_actions'){
                      $textarea_class = 'desc_texts_small';
                      $maxlength = '425';
                    }
                  @endphp
                  <div class="form-group">
                    <label>{{$incident_desc_name}}</label>
                    <textarea class="form-control {{$textarea_class}}" rows="3" name="{{$incident_desc_data['id']}}" placeholder="{{$incident_desc_data['placeholder']}}" maxlength="{{$maxlength}}"></textarea>
                    <div class="help-block with-errors blah"></div>
                  </div>
                @endforeach
              </div>
              <div class="form-group col-md-12">
                <table class="table">
                  <thead class="thead-dark">
                    <tr>
                      <th>S.No</th>
                      <th>Name of Witness</th>
                      <th>Employer</th>
                      <th>Contact Number</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                      $witness_details = ['name_of_witness','n_o_w_employer','n_o_w_contact'];
                    @endphp
                    @for($i=1;$i<=2;$i++)
                      <tr>
                        <td>{{$i}}</td>
                        @foreach($witness_details as $wd)
                          <td>
                            <input type="text" name="{{$wd}}_{{$i}}" class="form-control">
                          </td>
                        @endforeach
                      </tr>
                    @endfor
                  </tbody>
                </table>
              </div>
              <div class="form-group col-md-12">
                <label for="photos">Incident Proofs <small>(Photographs)</small></label><br>
                <input type="file" name="photos[]" class="jfilestyle" multiple>
              </div>
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
    @if(count($incident_reports))
    <div class="col-md-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Incident Reports</h3>
        </div> 
        <div class="box-body table-responsive">
          <table id="users_table" class="table table-bordered table-striped">
            <thead>
            <tr>
              <th>S.No</th>
              <th>Reported by</th>
              <th>Incident Location</th>
              <th>Incident Date</th>
              <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @php
              $count=1;
            @endphp
            @foreach($incident_reports as $report)
              <tr>
                <td>{{$count}}</td>
                <td>{{$report->user->name}}</td>
                <td>{{$report->location}}</td>
                <td>{{$report->date}}</td>
                <td>
                  <a href="#" class="view_incident_details" data-incident-id="{{$report->id}}">
                    <span class="action_icons"><i class="fa fa-eye" aria-hidden="true"></i></span>
                  </a>
                  <a href="{{route('incident.print',$report->id)}}" class="print_incident_report" target="_blank">
                    <span class="action_icons"><i class="fa fa-print" aria-hidden="true"></i></span>
                  </a>
                </td>
              </tr>
            @php
              $count++;
            @endphp
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

<script src="{{ asset('backend/js/character-counter.js') }}"></script>
<script type="text/javascript">
    //Show Incident Detail Modal
    
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
  $('.desc_texts').characterCounter({
    max: 530,
    textArea: true,
  });
  $('.desc_texts_small').characterCounter({
    max: 425,
    textArea: true,
  });

  </script>

<script type="text/javascript">
  // $('body').on('change','input:radio[name="ext_auth_notify"]',function(e){
  //   if ($(this).is(':checked') && $(this).val() == 1) {
  //     $('.ext_auth').show();
  //   }
  //   else{
  //     $('.ext_auth').hide();
  //   }
  // });

  // $('body').on('change','input:radio[name="investigation_required"]',function(e){
  //   if ($(this).is(':checked') && $(this).val() == 1) {
  //     $('.investigation_type').show();
  //   }
  //   else{
  //     $('.investigation_type').hide();
  //   }
  // });

  $('body').on('click','.update_incident_status',function(e){
    var datastring = $("#update_incident_status").serialize();
    $.ajax({
        type: 'POST',
        url: SITE_URL + 'updateIncidentStatus',
        data: datastring,
        dataType: 'json'
    }).done(function (response) {
        console.log(response);
        showNotify('success','Updated');
    });
  })


</script>
@endpush
