<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use DB;
use App\Client;
use App\Attendance;
use App\Room;
use App\SiteAttendance;
use App\User;
use App\Roster;
use Entrust;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Here, Condition is , if contractors or admin goes to check in out page then they will be displayed with their added clients whereas if employees goes in checkin out page then they will be displayed with their assigned clients only

        $clients = User::whereHas('roles', function ($query) {
                                  $query->where('name', '=', 'client');
                               });

        if(Entrust::hasRole(['contractor'])){
          //Retrieve all clients added by Contractors
          $clients->where('added_by','=',Auth::id());
        }
        elseif(Entrust::hasRole(['employee'])){
          $client_ids  = json_decode(Auth::user()->client_ids);
          if($client_ids)
            $clients->whereIn('id',$client_ids);
        }
        
        $clients = $clients->get();

        $now_converted = \Carbon\Carbon::now()->timezone(Auth::user()->timezone)->format('Y-m-d');
        $atten_three_days = Attendance::where('employee_id',Auth::id())
                                        ->whereDate('check_in','>=',\Carbon\Carbon::yesterday())
                                        ->whereDate('check_in','<=',\Carbon\Carbon::tomorrow())
                                        ->orderBy('check_in', 'desc')
                                        ->get()
                                        ->toArray();
        $attendance_check = collect($atten_three_days);
        $attendance_check = array_values($attendance_check->where('local_check_in.date',$now_converted)->toArray());
        $last_check_in_out_client = '';
        // Check Users last login status
        // $last_check_in_out_client = Attendance::select('check_in', 'check_out', 'client_id')
        //                                 ->where('employee_id',Auth::id())
        //                                 ->whereDate('check_in',\Carbon\Carbon::today())
        //                                 ->orderBy('id','desc')
        //                                 ->first();
        if($attendance_check){
            $last_check_in_out_client = $attendance_check[0]['client_id'];
        }

        return view('backend.pages.check_in_out',compact('clients','last_check_in_out_client'));
    }

    public function list(Request $request)
    {
        $attendances = Attendance::select('attendances.client_id',
                                          'attendances.employee_id',
                                          'attendances.check_in',
                                          'attendances.check_out',
                                          'client.name as client_name',
                                          'employee.added_by as added_by',
                                          'employee.name as employee_name')
                                  ->join('users as employee','attendances.employee_id','=','employee.id')
                                  ->join('users as client','attendances.client_id','=','client.id')
                                  ->orderBy('attendances.check_in','desc');

        if(Entrust::hasRole('contractor')){
          $attendances->where('employee.added_by',Auth::id());
        }
        if(!Entrust::hasRole(['contractor','superAdmin'])){
          $attendances->where('attendances.employee_id','=',Auth::id());
        }

        //Pluck Username and Clientnames for search
        $users_clients = $attendances->get();
        // $users = $attendances->pluck('employee_name','employee_id')->toArray();
        // $clients = $attendances->pluck('client_name','client_id')->toArray();
        $users = $users_clients->pluck('employee_name','employee_id')->toArray();
        $clients = $users_clients->pluck('client_name','client_id')->toArray();

        // if Search
        if($request->all()){
          if($request->search_by_employee_id)
            $attendances->where('attendances.employee_id','=',$request->search_by_employee_id);
          if($request->search_by_client_id)
            $attendances->where('attendances.client_id','=',$request->search_by_client_id);
        }

        $attendances = $attendances->get()->toArray();
        $collection = collect($attendances);

        if($request->search_date_from_to){
          $search_date_from_to = explode("-", $request->search_date_from_to);
          $search_date_from = date('Y-m-d',strtotime($search_date_from_to[0]));
          $search_date_to = date('Y-m-d',strtotime($search_date_from_to[1]));

          $collection = $collection->where('local_check_in.date','>=',$search_date_from)->where('local_check_in.date','<=',$search_date_to);
        }

        $grouped_attendances = $collection->groupBy(['local_check_in.date','employee_id'])->toArray();
        
        // $attendance_lists = Attendance::select(
        //                       DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(TIMEDIFF(`check_out`,`check_in`) AS TIME)))) AS total_time"),
        //                       DB::raw("min(check_in) as check_in"),
        //                       DB::raw("max(check_out) as check_out"),
        //                       DB::raw("CAST(check_in AS date) AS date"),
        //                       'attendances.client_id',
        //                       'attendances.employee_id',
        //                       'client.name as client_name',
        //                       'employee.name as employee_name'
        //                           )
        //                     ->join('users as employee','attendances.employee_id','=','employee.id')
        //                     ->join('users as client','attendances.client_id','=','client.id')
        //                     ->groupBy('client_name','employee_name','date','client_id','employee_id')
        //                     ->orderBy('check_out','desc');
                            

        return view('backend.pages.attendance_list',compact('clients', 'users','grouped_attendances'));
    }

    public function checkin(Request $request)
    {
        $rule = ['image' => 'required',
                'client_id' => 'required',
                'latitude' => 'required',
                'longitude' => 'required'
                ];
        $msg = ['image.required' => 'Something is wrong with your camera',
                'client_id.required' => 'Please Select Client First',
                'latitude.required' => 'Location Error',
                'longitude.required' => 'Location Error'
                ];
        $this->validate($request, $rule, $msg);

        //Have to check this validation could be already validated from above validate method
        if(!$request->get('image')){
            return redirect()->back()->withErrors('No Photo');
        }
        
        $now_converted = \Carbon\Carbon::now()->timezone(Auth::user()->timezone)->format('Y-m-d');
        $atten_three_days = Attendance::where('client_id',$request->client_id)
                                        ->where('employee_id',Auth::id())
                                        ->whereDate('check_in','>=',\Carbon\Carbon::yesterday())
                                        ->whereDate('check_in','<=',\Carbon\Carbon::tomorrow())
                                        ->orderBy('check_in', 'desc')
                                        ->get()
                                        ->toArray();
        $attendance_check = collect($atten_three_days);
        $attendance_check = array_values($attendance_check->where('local_check_in.date',$now_converted)->toArray());

        //Check if already Logged In
        // $attendance_check = Attendance::where('client_id',$request->client_id)
        //                               ->where('employee_id',Auth::id())
        //                               ->whereDate('check_in',\Carbon\Carbon::today())
        //                               ->orderBy('id', 'desc')
        //                               ->first();
        if(($attendance_check && $attendance_check[0]['check_out']!=null ) || !$attendance_check){
            $now = date("Y-m-d").'_'.strtotime(date('H:i:s'));
            $location = $request->latitude.', '. $request->longitude;
            $filename = 'check_in_'.Auth::user()->id.'_'.$now.'.png';
            $carbon = now();
            $current_date_time = $carbon->toDateTimeString();
            $check_in = new Attendance;
            $check_in->client_id = $request->client_id;
            $check_in->employee_id = Auth::id();
            $check_in->check_in = $current_date_time;
            $check_in->check_in_location = $location;
            $check_in->check_in_image = $filename;
            $check_in->save();
            
            //Save User Log In Image 
            if($check_in){
                $image = Image::make($request->get('image'));
                $path = public_path('files'.DS.'employee_login'.DS.Auth::user()->id);
                if (!file_exists($path)) {
                    \File::makeDirectory($path, 0755, true);
                }
                $image->save($path.DS.$filename);
            }
        }
        else{
            return redirect()->back()->withErrors('You are Already Logged In ');
        }
        return redirect()->back()->with('message', 'Client Logged In Successfully');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'image' => 'required',
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        $location = $request->latitude.', '. $request->longitude;
        $client_id = $request->client_id;
        $employee_id = Auth::id();
        $carbon = now();
        $current_date_time = $carbon->toDateTimeString();
      
        $now_converted = \Carbon\Carbon::now()->timezone(Auth::user()->timezone)->format('Y-m-d');
        $atten_three_days = Attendance::where('client_id',$request->client_id)
                                        ->where('employee_id',Auth::id())
                                        ->whereDate('check_in','>=',\Carbon\Carbon::yesterday())
                                        ->whereDate('check_in','<=',\Carbon\Carbon::tomorrow())
                                        ->orderBy('check_in', 'desc')
                                        ->get()
                                        ->toArray();
        $attendance_check = collect($atten_three_days);
        $attendance_check = array_values($attendance_check->where('local_check_in.date',$now_converted)->toArray());
        





        // $attendance_check = Attendance::where('client_id',$request->client_id)
        //                               ->where('employee_id',$employee_id)
        //                               ->whereDate('check_in',\Carbon\Carbon::today())
        //                               ->orderBy('id', 'desc')
        //                               ->first();

        if($attendance_check && $attendance_check[0]['check_out']==null ){
            $update_id = $attendance_check[0]['id'];
            $now = strtotime(date('H:i:s'));
            $filename = 'check_out_'.Auth::user()->id.'_'.$now.'.png';
            $image = Image::make($request->get('image'));
            $path = public_path('files'.DS.'employee_login'.DS.Auth::user()->id);
            if (!file_exists($path)) {
                \File::makeDirectory($path, 0755, true);
            }
            $image->save($path.DS.$filename);                

            $check_in = Attendance::where('id',$update_id )->update(['check_out'=>$current_date_time, 'check_out_location'=>$location, 'check_out_image'=>$filename]);
        }
        elseif($attendance_check && $attendance_check[0]['check_out']!=null)
            return  redirect()->back()->withErrors('You have already logged out');
        else{
            return  redirect()->back()->withErrors('You have already logged out');
        }

        return redirect()->back()->with('message', 'Logged Out Successfully');
    }

    public function details($client_id, $employee_id, $date)
    {
        $attendance_details = Attendance::select(
                                            'attendances.id',
                                            'attendances.client_id',
                                            'attendances.employee_id',
                                            'attendances.check_in',
                                            'attendances.check_in_location',
                                            'attendances.check_in_image',
                                            'attendances.check_out',
                                            'attendances.check_out_location',
                                            'attendances.check_out_image',
                                            'employee.name as employee_name',
                                            'employee.email',
                                            'client.name as client_name')
                                ->where('attendances.client_id',$client_id)
                                ->where('attendances.employee_id',$employee_id)
                                // ->whereDate('attendances.check_in',$date)
                                ->join('users as employee','attendances.employee_id','=','employee.id')
                                ->join('users as client','attendances.client_id','=','client.id')
                                ->get()
                                ->toArray();
        $collection = collect($attendance_details);

        // array_values reindexes all keys in array
        $attendance_details = array_values($collection->where('local_check_in.date',$date)->toArray());

        return view('backend.pages.attendance_details', compact('attendance_details'));
    }
     public function ajax_in_out_stat(Request $request)
    {
        $client_id = $request->client_id;

        $now_converted = \Carbon\Carbon::now()->timezone(Auth::user()->timezone)->format('Y-m-d');
        $atten_three_days = Attendance::where('employee_id',Auth::id())
                                        ->where('client_id',$client_id)
                                        ->whereDate('check_in','>=',\Carbon\Carbon::yesterday())
                                        ->whereDate('check_in','<=',\Carbon\Carbon::tomorrow())
                                        ->orderBy('check_in', 'desc')
                                        ->get()
                                        ->toArray();
        $attendance_check = collect($atten_three_days);
        $attendance_check = array_values($attendance_check->where('local_check_in.date',$now_converted)->toArray());
        $in_out_stats = [];
        if($attendance_check)
          $in_out_stats = $attendance_check[0];




        // $in_out_stats = Attendance::select('check_in', 'check_out')
        //                                 ->where('employee_id',Auth::id())
        //                                 ->where('client_id',$client_id)
        //                                 ->whereDate('check_in',\Carbon\Carbon::today())
        //                                 ->orderBy('id','desc')
        //                                 ->first();
        return json_encode($in_out_stats);
    }

    public function site_attendance(Request $request)
    {
        $site_attendances = SiteAttendance::select('site_attendances.id as id',
                                                  'users.name',
                                                  'site_attendances.created_at',
                                                  'site_attendances.updated_at',
                                                  'users.id as user_id',
                                                  'users.added_by as added_by',
                                                  'rooms.id as room_id',
                                                  'rooms.room_no',
                                                  'rooms.name as room_name',
                                                  'rooms.description',
                                                  'buildings.building_no',
                                                  'buildings.id as building_id',
                                                  'buildings.name as building_name',
                                                  'site_attendances.login',
                                                  'site_attendances.login_location',
                                                  'site_attendances.created_at as date')
                            ->join('rooms','rooms.id','=','site_attendances.room_id')
                            ->join('buildings','buildings.id','=','rooms.building_id')
                            ->join('users','users.id','=','site_attendances.user_id');

        if(Entrust::hasRole('contractor')){
          $site_attendances->where('added_by',Auth::id());
        }
        if(!Entrust::hasRole(['contractor','superAdmin'])){
          $site_attendances->where('site_attendances.user_id',Auth::id());
        }

        $site_attendances_search = $site_attendances->get(); 
        
        // if search
        if($request->all()){
          if($request->search_by_user_id)
              $site_attendances->where('site_attendances.user_id','=',$request->search_by_user_id);

          if($request->search_by_building_id)
              $site_attendances->where('buildings.id','=',$request->search_by_building_id);
          
          if($request->search_by_room_id)
              $site_attendances->where('site_attendances.room_id','=',$request->search_by_room_id);
        }

        $site_attendances = $site_attendances->get();

        // Populate query results with converted timezone datetime
        foreach ($site_attendances as $value) {
            $value['tz_login_date']=$value->toLocalTime('login')['date'];
            $value['tz_login_time']=$value->toLocalTime('login')['time'];
          }

        //This is for search according to users timezone
        if($request->search_by_date){
          $search_date = date('Y-m-d',strtotime($request->search_by_date));
          $site_attendances = $site_attendances->where('tz_login_date',$search_date);
        }

        return view('backend.pages.site_attendance',compact('site_attendances','site_attendances_search'));
    }

    public function ajax_qr_login(Request $request)
    {
        $room_id = $request->room_id;
        //Check if Room ID Exists
        $room = Room::where('id','=', $room_id);
        if($room->exists()){
            $room_no = $room->first()->room_no;
            $time = date('H:i:s'); //yo use gareni huxna
            $carbon = now();
            $current_date_time = $carbon->toDateTimeString(); //yo use gareyni hunxa
            $request->merge(['user_id'=>Auth::user()->id]);
            $request->merge(['login'=>$current_date_time]);
            $request->merge(['login_location'=>$request->lat_long]);

            SiteAttendance::create($request->all());
            return response()->json(['success'=>'Logged In Successfully','room_no'=>$room_no]);
        }
        else{
            return response()->json(['error'=>'Room doesnot exists in our system. Make sure you are scanning the right QR Code'],401);
        }
        
    }

}
