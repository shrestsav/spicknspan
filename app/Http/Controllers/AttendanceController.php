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

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_type = 'client';
        $clients = User::all()->where('user_type','=',$user_type);

        return view('backend.pages.check_in_out',compact('clients'));
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
        $employee_id = Auth::id();

        if(Auth::check() && Auth::user()->user_type == "admin")
            $attendance_lists = Attendance::all();
        else
            $attendance_lists = Attendance::all()->where('employee_id',$employee_id);

        
        $user_lists = User::all();
        return view('backend.pages.attendance_list',compact('attendance_lists', 'user_lists'));
    }
    public function checkin(Request $request)
    {
        if(isset($_POST['latitude']))
            { $latitude = $_POST['latitude']; }
        else { $latitude = 0; }

        if(isset($_POST['longitude']))
            { $longitude = $_POST['longitude']; }
        else { $longitude = 0; }

        $location = $latitude.', '. $longitude;

        $client_id = $request->client;
        $employee_id = Auth::id();

        //Check if already Logged In
        $attendance_check = Attendance::where('client_id',$client_id)->whereDate('created_at',\Carbon\Carbon::today());
        if(!$attendance_check->exists()){
            $carbon = now();
            $current_date_time = $carbon->toDateTimeString();
            $check_in = new Attendance;
            $check_in->client_id = $client_id;
            $check_in->employee_id = $employee_id;
            $check_in->check_in = $current_date_time;
            $check_in->check_in_location = $location;
            $check_in->save();
        }
        else{
            return  redirect()->back()->withErrors('Client Already Logged In for Today');
        }
        return redirect()->back()->with('message', 'Client Logged In Successfully');
    }
    public function checkout(Request $request)
    {
       if(isset($_POST['latitude']))
            { $latitude = $_POST['latitude']; }
        else { $latitude = 0; }

        if(isset($_POST['longitude']))
            { $longitude = $_POST['longitude']; }
        else { $longitude = 0; }

        $location = $latitude.', '. $longitude;

        $client_id = $request->client;
        $employee_id = Auth::id();
        $carbon = now();
        $current_date_time = $carbon->toDateTimeString();
        $check_in = Attendance::where('client_id',$client_id)->where('employee_id',$employee_id)->whereDate('created_at',\Carbon\Carbon::today())->update(['full_date'=>$current_date_time, 'check_out'=>$current_date_time, 'check_out_location'=>$location]);

        //check total hours difference in the roster table
        $current_date_time = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $current_date_time)->format('Y-m-d');
        $roster_lists     = DB::table('rosters')->where('client_id', '=', $client_id)->where('employee_id', '=', $employee_id)->where('full_date', '=', $current_date_time)->get();
        $roster_lists = json_decode($roster_lists, true);
        $diff_hour1 = $roster_lists[0]['total_hours'];

        //check total hours difference in the attendance table
        $attendance_lists = DB::table('attendances')->where('attendances.client_id', '=', $client_id)->where('attendances.employee_id', '=', $employee_id)->where('attendances.full_date', '=', $current_date_time)->get();
        $attendance_lists = json_decode($attendance_lists, true);
        $check_in  = $attendance_lists[0]['check_in'];
        $check_out = $attendance_lists[0]['check_out'];
        $diff_hour2 = round(abs(strtotime($check_in) - strtotime($check_out)) / 3600);

        if($diff_hour1 == $diff_hour2){
            $status = 1;
        }else{
            $status = 2;
        }

        $check_in = Attendance::where('client_id',$client_id)->where('employee_id',$employee_id)->whereDate('created_at',\Carbon\Carbon::today())->update(['status'=>$status]);

        return redirect()->back()->with('message', 'Client Logged Out Successfully');
    }

    public function details(Request $request, $id)
    {
        $att_details = Attendance::findOrFail($id);
        $att_details = json_decode($att_details, true);
        return view('backend.pages.attendance_details', compact('att_details'));
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
