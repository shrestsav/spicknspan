<div class="row">
    @php 

        $to_display = [
            'name',
            'email',
            'user_type',
            'timezone',
            'address',
            'gender',
            'contact',
            'hourly_rate',
            'annual_salary',
            'description',
            'date_of_birth',
            'employment_start_date',
          ];
                    
        $documents = json_decode($user_details->documents, true);
    
    @endphp
    
    @foreach($to_display as $value)
        <div class="col-md-4">
            <div class="form-group">
              <label for="user_name">{{str_replace('_',' ',ucfirst($value))}}</label>
              <input type="text" class="form-control" id="user_name" value="{{$user_details->$value}} 
              @if($user_details->currency && $user_details->$value && ($value=='hourly_rate' || $value=='annual_salary')) 
              {{config('setting.currencies')[$user_details->currency]}} 
              @endif" readonly>
            </div>
        </div>
    @endforeach

    @if($documents)
        <div class="col-md-12">
            <div class="form-group">
                <label for="user_name">Documents</label>
            </div>
        </div>
        @foreach($documents as $document)
            <div class="col-md-4 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-yellow"><i class="fa fa-files-o"></i></span>
                <div class="info-box-content">
                    <span class="info-box-number">Document</span>
                    <a href="{{ asset('files/users/'.$user_details->id.'/'.$document)}}" target="__blank"><span class="info-box-text">{{$document}}</span></a>
                </div>
              </div>
            </div>
        @endforeach
    
    @else
        <div class="col-md-12 text-center">
            <div class="form-group">
                <label for="user_name" style="color: red;">No Documents Attached</label>
            </div>
        </div>
    @endif
</div>


