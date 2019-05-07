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

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        //Here, Condition is ,, if contractors or admin goes to check in out page then they will be displayed with their added clients whereas if employees goes in checkin out page then they will be displayed with their assigned clients only

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

        // Check Users last login status
        $last_check_in_out_client = Attendance::select('check_in', 'check_out', 'client_id')
                                        ->where('employee_id',Auth::id())
                                        ->whereDate('check_in',\Carbon\Carbon::today())
                                        ->orderBy('id','desc')
                                        ->first();
        if($last_check_in_out_client){
            $last_check_in_out_client = $last_check_in_out_client->client_id;
        }

        return view('backend.pages.check_in_out',compact('clients','last_check_in_out_client'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function list(Request $request)
    {
        $addedBy  = Auth::user()->added_by;

        $employee_id = Auth::id();

        $attendance_lists = Attendance::select(
                              DB::raw("SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(TIMEDIFF(`check_out`,`check_in`) AS TIME)))) AS total_time"),
                              DB::raw("min(check_in) as check_in"),
                              DB::raw("max(check_out) as check_out"),
                              DB::raw("CAST(check_in AS date) AS date"),
                              'attendances.client_id',
                              'attendances.employee_id',
                              'client.name as client_name',
                              'employee.name as employee_name'
                                  )
                            ->join('users as employee','attendances.employee_id','=','employee.id')
                            ->join('users as client','attendances.client_id','=','client.id')
                            ->groupBy('client_name','employee_name','date','client_id','employee_id')
                            ->orderBy('check_out','desc');
                            
        if(!Entrust::can('view_all_data'))
          $attendance_lists->where('attendances.employee_id','=',$employee_id);
        
            
        $employees = User::whereHas('roles', function ($query) {
                                $query->where('name', '=', 'employee');
                             });
        $clients = User::whereHas('roles', function ($query) {
                                $query->where('name', '=', 'client');
                             });

        if(!Entrust::can('view_all_data')){
            $employees->where('added_by','=',$addedBy);
            $clients->where('added_by','=',$addedBy);
        }
        
        if($request->all()){
          if($request->search_by_employee_id)
            $attendance_lists->where('attendances.employee_id','=',$request->search_by_employee_id);
          if($request->search_by_client_id)
            $attendance_lists->where('attendances.client_id','=',$request->search_by_client_id);
        }

        $attendance_lists = $attendance_lists->get();
        $employees = $employees->get();
        $clients = $clients->get();

        if($request->search_date_from_to){
          $search_date_from_to = explode("-", $request->search_date_from_to);
      
          $search_date_from = date('Y-m-d',strtotime($search_date_from_to[0]));
          $search_date_to = date('Y-m-d',strtotime($search_date_from_to[1]));
          
          // return $search_date_from.','.$search_date_to;
          $attendance_lists = $attendance_lists->where('date','>=',$search_date_from)->where('date','<=',$search_date_to);
        }
        // return $attendance_lists;
        return view('backend.pages.attendance_list',compact('attendance_lists', 'clients', 'employees'));
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

        //Check if already Logged In
        $attendance_check = Attendance::where('client_id',$request->client_id)
                                      ->where('employee_id',Auth::id())
                                      ->whereDate('check_in',\Carbon\Carbon::today())
                                      ->orderBy('id', 'desc')
                                      ->first();
        
        if(($attendance_check && $attendance_check->check_out!=null ) || !$attendance_check){
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
                    \File::makeDirectory($path, 755, true);
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
        
        $attendance_check = Attendance::where('client_id',$request->client_id)
                                      ->where('employee_id',$employee_id)
                                      ->whereDate('check_in',\Carbon\Carbon::today())
                                      ->orderBy('id', 'desc')
                                      ->first();

        if($attendance_check && $attendance_check->check_out==null ){
            $update_id = $attendance_check->id;
            $now = strtotime(date('H:i:s'));
            $filename = 'check_out_'.Auth::user()->id.'_'.$now.'.png';
            $image = Image::make($request->get('image'));
            $path = public_path('files'.DS.'employee_login'.DS.Auth::user()->id);
            if (!file_exists($path)) {
                \File::makeDirectory($path, 755, true);
            }
            $image->save($path.DS.$filename);                

            $check_in = Attendance::where('id',$update_id )->update(['check_out'=>$current_date_time, 'check_out_location'=>$location, 'check_out_image'=>$filename]);
        }
        elseif($attendance_check && $attendance_check->check_out!=null)
            return  redirect()->back()->withErrors('You have already logged out');
        else{
            return  redirect()->back()->withErrors('You have already logged out');
        }
        //check total hours difference in the roster table
        // $current_date_time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $current_date_time)->format('Y-m-d');
        // $roster_lists     = DB::table('rosters')->where('client_id', '=', $client_id)->where('employee_id', '=', $employee_id)->where('full_date', '=', $current_date_time)->get();
        // $roster_lists = json_decode($roster_lists, true);
        // $diff_hour1 = $roster_lists[0]['total_hours'];

        //check total hours difference in the attendance table
        // $attendance_lists = DB::table('attendances')->where('attendances.client_id', '=', $client_id)->where('attendances.employee_id', '=', $employee_id)->where('attendances.full_date', '=', $current_date_time)->get();
        // $attendance_lists = json_decode($attendance_lists, true);
        // $check_in  = $attendance_lists[0]['check_in'];
        // $check_out = $attendance_lists[0]['check_out'];
        // $diff_hour2 = round(abs(strtotime($check_in) - strtotime($check_out)) / 3600);

        // if($diff_hour1 == $diff_hour2){
        //     $status = 1;
        // }else{
        //     $status = 2;
        // }

        // $check_in = Attendance::where('client_id',$client_id)->where('employee_id',$employee_id)->whereDate('created_at',\Carbon\Carbon::today())->update(['status'=>$status]);

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
                                ->whereDate('attendances.check_in',$date)
                                ->join('users as employee','attendances.employee_id','=','employee.id')
                                ->join('users as client','attendances.client_id','=','client.id')
                                ->get();

        return view('backend.pages.attendance_details', compact('attendance_details'));
    }
     public function ajax_in_out_stat(Request $request)
    {
        $client_id = $request->client_id;
        $in_out_stats = Attendance::select('check_in', 'check_out')
                                        ->where('employee_id',Auth::id())
                                        ->where('client_id',$client_id)
                                        ->whereDate('check_in',\Carbon\Carbon::today())
                                        ->orderBy('id','desc')
                                        ->first();
        return json_encode($in_out_stats);
    }

    public function site_attendance(Request $request)
    {
        $site_attendances = SiteAttendance::select('site_attendances.id as id',
                                                  'users.name',
                                                  'site_attendances.created_at',
                                                  'site_attendances.updated_at',
                                                  'users.id as user_id',
                                                  'rooms.id as room_id',
                                                  'rooms.room_no',
                                                  'rooms.name as room_name',
                                                  'rooms.description',
                                                  'buildings.building_no',
                                                  'buildings.id as building_id',
                                                  'buildings.name as building_name',
                                                  'site_attendances.login',
                                                  'site_attendances.created_at as date',)
                            ->join('rooms','rooms.id','=','site_attendances.room_id')
                            ->join('buildings','buildings.id','=','rooms.building_id')
                            ->join('users','users.id','=','site_attendances.user_id');
        
        if(!Entrust::can('view_all_data')){
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
          
          // if($request->search_by_date){
          //     $search_date = date('Y-m-d',strtotime($request->search_by_date));
          //     $site_attendances->whereDate('site_attendances.created_at','=',$search_date);       
          // }
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
        // return $site_attendances;
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

            SiteAttendance::create($request->all());
            return response()->json(['success'=>'Logged In Successfully','room_no'=>$room_no]);
        }
        else{
            return response()->json(['error'=>'Room doesnot exists in our system. Make sure you are scanning the right QR Code'],401);
        }
        
    }

}
