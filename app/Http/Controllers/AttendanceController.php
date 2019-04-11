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
        // return $request->session()->all();
        $s_user_id   = session('user_id');
        $s_added_by  = session('added_by');
        // echo $s_added_by;
        // die();
        $clients = User::all()->where('user_type','=','client')->where('added_by','=',$s_added_by);

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
    public function list()
    {
        $s_user_id   = session('user_id');
        $s_added_by  = session('added_by');

        if(isset($_GET['employee_id'])){
            $filtEmpId = $_GET['employee_id'];
        } else {
            $filtEmpId = '';
        }

        if(isset($_GET['client_id'])){
            $filtCliId = $_GET['client_id'];
        } else {
            $filtCliId = '';
        }

        if(isset($_GET['filt_date'])){
            $filtDate = $_GET['filt_date'];
        } else {
            $filtDate = '';
        }

        $employee_id = Auth::id();

        // If User is Admin
        if(Auth::check() && Auth::user()->user_type == "admin"){

            $attendance_lists = \DB::select('SELECT
                                            SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(TIMEDIFF(`check_out`,`check_in`) AS TIME)))) AS total_time,
                                            min(a.check_in) as check_in,
                                            max(a.check_out) as check_out,
                                            CAST(a.check_in AS date) AS date,
                                            a.client_id,
                                            a.employee_id,
                                            (select name from users where id=a.client_id) as client_name,  
                                            (select name from users where id=a.`employee_id`) as employee_name 
                                            FROM `attendances` a
                                            GROUP BY client_name,employee_name,date,client_id,employee_id
                                            ORDER BY check_out DESC
                                            ');
            // print_r($attendance_lists);
            // die();
        }
        else
            $attendance_lists = \DB::select('SELECT
                                            SEC_TO_TIME(SUM(TIME_TO_SEC(CAST(TIMEDIFF(`check_out`,`check_in`) AS TIME)))) AS total_time,
                                            min(a.check_in) as check_in,
                                            max(a.check_out) as check_out,
                                            CAST(a.check_in AS date) AS date,
                                            a.client_id,
                                            a.employee_id,
                                            (select name from users where id=a.client_id) as client_name,  
                                            (select name from users where id=a.`employee_id`) as employee_name 
                                            FROM `attendances` a where a.employee_id='.$employee_id.'
                                            GROUP BY client_name,employee_name,date,client_id,employee_id
                                            ORDER BY check_out DESC
                                             ');

        $employees = User::all()->where('user_type','=','employee')->where('added_by','=',$s_added_by);
        $clients = User::all()->where('user_type','=','client')->where('added_by','=',$s_added_by);

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
                                            'users.name',
                                            'users.email')
                                ->where('attendances.client_id',$client_id)
                                ->where('attendances.employee_id',$employee_id)
                                ->whereDate('attendances.check_in',$date)
                                ->join('users','attendances.employee_id','=','users.id')
                                ->get();
        $client_name = User::find($client_id)->name;
        return view('backend.pages.attendance_details', compact('attendance_details','client_name'));
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

    public function site_attendance()
    {
       $site_attendances = SiteAttendance::select('users.name','rooms.room_no','rooms.name as room_name','buildings.building_no','buildings.address','site_attendances.login')
                            ->join('rooms','rooms.id','=','site_attendances.room_id')
                            ->join('buildings','buildings.id','=','rooms.building_id')
                            ->join('users','users.id','=','site_attendances.user_id')
                            ->get();
       return view('backend.pages.site_attendance',compact('site_attendances'));
    }
    public function ajax_qr_login(Request $request)
    {
        $room_id = $request->room_id;

        //Check if Room ID Exists
        $room = Room::where('id','=', $room_id)->exists();
        if($room){
            $time = date('H:i:s'); //yo use gareni huxna
            $carbon = now();
            $current_date_time = $carbon->toDateTimeString('H:i:s'); //yo use gareyni hunxa
            $request->merge(['user_id'=>Auth::user()->id]);
            $request->merge(['login'=>$current_date_time]);

            SiteAttendance::create($request->all());
            return json_encode($request->all());
        }
        else{
            return json_encode('room doesnot exists');
        }
        return json_encode($request->room_id);
        $request->all();
    }

}
