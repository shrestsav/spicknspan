<div class="col-md-12">
  <div class="pull-right">
    
      <a data-toggle="modal" data-target="#modal-info">
        <button type="button" class="btn btn-success">
          @if($incident_details->status===0)
            Approve
          @elseif($incident_details->status===1)
            Approved
          @endif
        </button>
      </a>  
    <a href="{{route('incident.print',$incident_details->id)}}" target="_blank"><button type="button" class="btn btn-primary">Print to Form</button></a>
  </div>
</div>

<div class="row">
  @php 
      $db_incident_types = json_decode($incident_details->type);
      $db_medical_treatments = json_decode($incident_details->medical_treatment);
      $db_attended_authorities = json_decode($incident_details->attended_authorities);
      $db_witness_details = json_decode($incident_details->witness_details,true);
      $db_photos = json_decode($incident_details->photos);
  @endphp 
  <div class="incident_details">
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
              <input type="checkbox" @if(in_array($name,$db_incident_types)) checked @endif onclick="return false;"/> {{$display_name}}
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
            <td><input type="text" class="form-control" value="{{$incident_details->person_involved}}" style="width: 100%" readonly></td>
            <td><input type="text" class="form-control" value="{{$incident_details->occupation}}"  style="width: 100%" readonly></td>
            <td><input type="text" class="form-control" style="width: 100%" value="{{$incident_details->employer}}" readonly></td>
            <td><input type="text" class="form-control" value="{{$incident_details->contact}}" style="width: 100%" readonly></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="col-md-12">
      @php 
        $incident_detail = [
          'Incident Location' => 'location',
          'Incident Date & Time' => 'date'
        ];
      @endphp
      <div class="col-md-12">
        <label >INCIDENT DETAILS</label>
      </div>
      @foreach($incident_detail as $id_name => $id_id)
        <div class="form-group col-md-4"> 
          <input type="text" class="form-control" id="{{$id_id}}" value="{{$incident_details->{$id_id} }}" readonly>
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
                  <input type="checkbox" id="{{$mt_id}}" @if(in_array($mt_id,$db_medical_treatments)) checked @endif onclick="return false;"/>
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
                  <input type="radio" id="cw_yes" value="1" @if($incident_details->cease_work==1)checked @endif onclick="return false;"/>
                  Yes
                </label>
              </div>
              <div class="radio">
                <label>
                  <input type="radio" id="cw_no" value="0" @if($incident_details->cease_work==0)checked @endif onclick="return false;"/>
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
                  <input type="checkbox" id="{{$aa_id}}" @if(in_array($aa_id,$db_attended_authorities)) checked @endif onclick="return false;"/>
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
        <label for="inputEmail3">INCIDENT DESCRIPTION <small></small></label>
      </div>
      <br><br>
      <div class="col-md-12">
        @foreach($incident_descriptions as $incident_desc_name => $incident_desc_data)
          <div class="form-group">
            <label>{{$incident_desc_name}}</label>
            <textarea class="form-control" rows="3" readonly>{{$incident_details->{$incident_desc_data['id']} }}</textarea>
          </div>
        @endforeach
      </div>
    </div>
    <div class="form-group col-md-12">
      <table class="table">
        <thead class="thead-dark">
          <tr>
            <th>Name of Witness</th>
            <th>Employer</th>
            <th>Contact Number</th>
          </tr>
        </thead>
        <tbody>
          @php
            $witness_details = ['name','employer','contact'];
          @endphp
          @foreach($db_witness_details as $dwd)
            <tr>
              @foreach($witness_details as $wd)
                <td>
                  <input type="text" class="form-control" value="{{$dwd[$wd]}}" readonly>
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="form-group col-md-12">
      <table class="table">
        <thead>
          <tr>
            <th>Reported By</th>
            <th>Employer</th>
            <th>Contact Number</th>
            <th>Reported Time</th>
          </tr>
        </thead>
        <tbody>
          @php
          $r_name = $incident_details->user->name;
          $r_employer = $incident_details->user->employer->name;
          $r_contact = $incident_details->user->detail->contact;
          $reported_at = \Carbon\Carbon::parse($incident_details->created_at)->format('M-d-Y H:i');
          $cols = ['r_name','r_employer','r_contact','reported_at'];
          @endphp
          <tr>
            @foreach($cols as $col)
              <td>
                <input type="text" class="form-control" value="{{ ${$col} }}" readonly>
              </td>
            @endforeach
          </tr>
        </tbody>
      </table>
    </div>
    @if($db_photos)
      <div class="form-group col-md-12">
        <div class="col-md-12">
          <label for="photos">Incident Photographs <small></small></label>
        </div>
        @foreach($db_photos as $photo)
        <a href="{{asset('files/incidents/'.$incident_details->id.'/'.$photo)}}" target="_blank">
          <img src="{{asset('files/incidents/'.$incident_details->id.'/'.$photo)}}" height="300px">
        </a>
        @endforeach
      </div>
    @endif
    {{-- <div class="col-md-12">
      <div class="box-footer">
        <a href="{{route('incident.print',$incident_details->id)}}" target="_blank"><button type="button" class="btn btn-primary">Print to Form</button></a>
      </div>
    </div> --}}
  </div>
</div>

<div class="modal modal-info fade" id="modal-info">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Office Use Only</h4>
      </div>
      <form id="update_incident_status">
        @csrf
        <input type="hidden" name="incident_id" value="{{$incident_details->id}}">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <div class="col-md-12">
                <div class="form-group ext_auth"{{--  style="display: none;" --}}>
                  <label>HSE Manager</label>
                  <input type="text" name="HSE_manager" class="form-control" value="{{$incident_details->HSE_manager}}" required>
                  <div class="help-block with-errors"></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="ext_auth">External Authorities Notified?</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="ext_auth_notify" id="ean_yes" value="1" @if($incident_details->ext_auth_notify===1) checked @endif>
                      Yes
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="ext_auth_notify" id="ean_no" value="0" @if($incident_details->ext_auth_notify===0) checked @endif>
                      No
                    </label>
                  </div>
                </div>
                <div class="form-group ext_auth"{{--  style="display: none;" --}}>
                  <label>If Yes Which?</label>
                  <input type="text" name="ext_auth" class="form-control" value="{{$incident_details->ext_auth}}" required>
                  <div class="help-block with-errors"></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-12">
                <div class="form-group">
                  <label for="investigation_required">Further Investigation Required?</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="investigation_required" id="ir_yes" value="1" @if($incident_details->investigation_required===1) checked @endif>
                      Yes
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="investigation_required" id="ir_no" value="0" @if($incident_details->investigation_required===0) checked @endif>
                      No
                    </label>
                  </div>
                </div>
                <div class="form-group investigation_type"{{--  style="display: none;" --}}>
                  <label for="investigation_required">If Yes, Select Type</label>
                  <div class="radio">
                    <label>
                      <input type="radio" name="investigation_type" id="it_minor" value="1" @if($incident_details->investigation_type===1) checked @endif>
                      Minor
                    </label>
                  </div>
                  <div class="radio">
                    <label>
                      <input type="radio" name="investigation_type" id="it_major" value="2" @if($incident_details->investigation_type===2) checked @endif>
                      Major
                    </label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <br><br>
        </div>
        <div class="modal-footer" style="text-align: center;">
          @if($incident_details->status===0)
            <button type="button" class="btn btn-outline update_incident_status">Approve</button>
          @elseif($incident_details->status===1)
            <button type="button" class="btn btn-outline" disabled>Approved</button>
          @endif
          
        </div>
      </form>
    </div>
  </div>
</div>

