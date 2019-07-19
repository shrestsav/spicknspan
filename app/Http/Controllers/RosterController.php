<?php

namespace App\Http\Controllers;

use App\Roster;
use App\RosterTimetable;
use App\LeaveRequest;
use DB;
use Auth;
use App\User;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\sendMail;

class RosterController extends Controller
{
    /**
     * @var User
     */
    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // I think Roster maa Eager loading garyo bhaney load kam parla
        if($request->year_month){
            $year_month = explode('-', $request->year_month);
            $year = $year_month[0];
            $month = $year_month[1];
        }
        else{
            $year = date('Y');
            $month = date('m');
        }

        $all_days = $this->dates_month($month,$year);

        // This is used for pagination of roster as direct pagination on rosters query is not practical for this case, see the code and you'll get it
        $customPaginate = Roster::where('full_date','=',$year.'-'.$month);

        if($request->search_by_employee_id)
            $customPaginate->where('employee_id','=',$request->search_by_employee_id);
        if($request->search_by_client_id)
            $customPaginate->where('client_id','=',$request->search_by_client_id);
        if(Entrust::hasRole('contractor')){
            $customPaginate->where('added_by','=',Auth::id());
        }
        if(Entrust::hasRole('employee')){
            $customPaginate->where('employee_id','=',Auth::id());
        }
        $customPaginate = $customPaginate->orderBy('created_at','desc')->simplePaginate(config('setting.rows'));
        //Now grab roster ids & fetch rosters ids to get actual data
        $rostIds = $customPaginate->pluck('id')->toArray();

        $rosters = Roster::select('rosters.id',
                                  'rosters.employee_id',
                                  'rosters.client_id',
                                  'rosters.full_date',
                                  'rosters.added_by',
                                  'rt.id as rt_id',
                                  'rt.date',
                                  'rt.start_time',
                                  'rt.end_time',
                                  'rt.status')
                        ->join('roster_timetables as rt','rt.roster_id','=','rosters.id')
                        ->whereIn('rosters.id',$rostIds)
                        ->with('client','employee')
                        ->orderBy('rosters.created_at','desc');
        
        $leaves = LeaveRequest::where('status',1)->get();

        $employees = $this->user->employeeList();
        $clients = $this->user->clientList();
        $rosters = $rosters->get();
        $rosters = $rosters->groupBy(['client_id','employee_id']);

        return view('backend.pages.roster',compact('rosters','employees', 'clients','leaves','all_days','year','month','customPaginate'));
    }

    public function sheets(Request $request)
    {
        // I think Roster maa Eager loading garyo bhaney load kam parla
        if($request->year_month){
            $year_month = explode('-', $request->year_month);
            $year = $year_month[0];
            $month = $year_month[1];
        }
        else{
            $year = date('Y');
            $month = date('m');
        }

        $all_days = $this->dates_month($month,$year);

        // This is used for pagination of roster as direct pagination on rosters query is not practical for this case, see the code and you'll get it
        $customPaginate = Roster::where('full_date','=',$year.'-'.$month);

        if($request->search_by_employee_id)
            $customPaginate->where('employee_id','=',$request->search_by_employee_id);
        if($request->search_by_client_id)
            $customPaginate->where('client_id','=',$request->search_by_client_id);
        if(Entrust::hasRole('contractor')){
            $customPaginate->where('added_by','=',Auth::id());
        }
        if(Entrust::hasRole('employee')){
            $customPaginate->where('employee_id','=',Auth::id());
        }
        $customPaginate = $customPaginate->orderBy('created_at','desc')->simplePaginate(config('setting.rows'));
        //Now grab roster ids & fetch rosters ids to get actual data
        $rostIds = $customPaginate->pluck('id')->toArray();

        $rosters = Roster::select('rosters.id',
                                  'rosters.employee_id',
                                  'rosters.client_id',
                                  'rosters.full_date',
                                  'rosters.added_by',
                                  'rt.id as rt_id',
                                  'rt.date',
                                  'rt.start_time',
                                  'rt.end_time',
                                  'rt.status')
                        ->join('roster_timetables as rt','rt.roster_id','=','rosters.id')
                        ->whereIn('rosters.id',$rostIds)
                        ->with('client','employee')
                        ->orderBy('rosters.created_at','desc');
        
        $leaves = LeaveRequest::where('status',1)->get();

        $employees = $this->user->employeeList();
        $clients = $this->user->clientList();
        $rosters = $rosters->get();
        $rosters = $rosters->groupBy(['client_id','employee_id']);

        return view('backend.pages.sheets',compact('rosters','employees', 'clients','leaves','all_days','year','month','customPaginate'));
    }

    /**
     * Returns all days of perticular month
     */
    public function dates_month($month, $year)
    {
        $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $dates_month = array();

        for ($i = 1; $i <= $num; $i++) {
            $mktime = mktime(0, 0, 0, $month, $i, $year);
            $date = date("D-M-d", $mktime);
            $dates_month[$i] = $date;
        }

        return $dates_month;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

    public function destroy(Request $request)
    {
        foreach($request->sel_Rows as $sel_Row){
            Roster::find($sel_Row)->delete();
        }
        return response()->json("Roster Deleted successfully");
    }

    public function ajax_store_roster(Request $request)
    {   
        $rule = ['type' => 'required',
                'client_id' => 'required|numeric',
                'employee_id' => 'required|numeric',
                'time' => 'required|date_format:H:i',
                ];
        $msg = ['type.required' => 'Type Missing',
                'client_id.required' => 'Please Select Client First',
                'employee_id.required' => 'Please Select Employee First',
                'time.required' => 'Please Select Time',
                'time.date_format' => 'Please select correct time from dropdown',
                ];

        $validate = Validator::make($request->all(), $rule, $msg);
        if($validate->fails()){
            return response($validate->errors(),401);
        }

        $type = $request->type;
        $client_id = $request->client_id;
        $employee_id = $request->employee_id;
        $time = $request->time;
        $full_date = Carbon::parse($request->date)->format('Y-m');
        $request->merge(['full_date' => $full_date,'added_by' => Auth::id()]);

        //Check if roster exists
        $check_roster = Roster::where('employee_id',$employee_id)->where('client_id',$client_id)->where('full_date',$full_date);
        if(!$check_roster->exists()){
            $roster = Roster::Create($request->all());
            if($roster){
                if($type=='start_time')
                    $request->merge(['roster_id' => $roster->id,'start_time' => $time]);
                elseif($type=='end_time')
                    $request->merge(['roster_id' => $roster->id,'end_time' => $time]);
                RosterTimetable::Create($request->all());
                return json_encode('Create New');
            }
        }
        else{
            $roster_id = $check_roster->first()->id;

            //check if roster timetable exists
            $check_roster_timetable = RosterTimetable::where('roster_id',$roster_id)->where('date',$request->date);
            if($check_roster_timetable->exists()){
                if($type=='start_time')
                   $update_field = 'start_time';
                elseif($type=='end_time')
                    $update_field = 'end_time';
                $check_roster_timetable->update([$update_field => $time]);
                return json_encode('Roster exists and timetable exists');
            }
            else{
                if($type=='start_time')
                    $request->merge(['roster_id' => $roster_id,'start_time' => $time]);
                elseif($type=='end_time')
                    $request->merge(['roster_id' => $roster_id,'end_time' => $time]);
                RosterTimetable::Create($request->all());
                return json_encode('Roster exits timetable doesnot');
            }
        }
        
    }

    public function ajax_update_roster(Request $request)
    {
        $rule = ['type' => 'required',
                'roster_id' => 'required|numeric',
                'date' => 'required',
                'time' => 'required|date_format:H:i',
                ];
        $msg = ['type.required' => 'Type Missing',
                'roster_id.required' => 'Roster ID missing',
                'date.required' => 'Date Missing',
                'time.required' => 'Please Select Time',
                'time.date_format' => 'Please select correct time from dropdown',
                ];

        $validate = Validator::make($request->all(), $rule, $msg);
        if($validate->fails()){
            return response($validate->errors(),401);
        }

        $type = $request->type;
        $roster_id = $request->roster_id;
        $time = $request->time;
        $date = $request->date;
        if($type=='start_time')
            $update_field = 'start_time';
        elseif($type=='end_time')
            $update_field = 'end_time';

        //Check if Timetable exists
        $check_roster_timetable = RosterTimetable::where('roster_id',$roster_id)->where('date',$date);
        if($check_roster_timetable->exists()){
            $update_roster = RosterTimetable::where('roster_id',$roster_id)->whereDate('date',$date)->update([$update_field => $time]);
            if($update_roster)
                return json_encode('Updated Successfully');
        }
        else{
            $request->merge([$update_field => $time]);
            RosterTimetable::create($request->all());
            return json_encode('Roster Timetable Created');
        }
    }

    public function ajaxCheckIfRosterExists(Request $request)
    {
        $check = Roster::where('employee_id',$request->employee_id)->where('client_id',$request->client_id)->where('full_date',$request->year_month);
        if($check->exists())
            return response()->json(['error'=>'Roster Already Exists for selected User and Client for this month, Try editing existing Record Instead'],401);
        else
            return response()->json(['success'=>'Safe to Proceed']);
    }

    public function ajaxRosterDetails(Request $request)
    {
        $roster_details = Roster::where('rosters.id',$request->roster_id)
                        ->with('client','employee','timetable')
                        ->first();
        // return json_encode($roster_details);
        $view = view('backend.modals.render.roster_details')->with([
           'roster_details' => $roster_details ])->render();

        $response = [
           'status' => true,
           'title' => 'Roster Details',
           'html' => $view
        ];
       return response()->json($response);
    }

    public function rosterNotify(Request $request)
    {
        $rule = [
            'email' => 'required|email',
        ];
        $msg = [
            'email.required' => 'Email Missing',
            'email.email' => 'Email Format Error',
        ];

        $validate = Validator::make($request->all(), $rule, $msg);
        
        if($validate->fails()){
            return response($validate->errors(),401);
        }

        $mailData = [
            'email_type' => 'rosterNotify',
            'username'   => $request['email'],
            'subject'    => 'Roster Update Notification',
            'message'    => 'Your Roster has been updated for the month of '.$request["year_month"].' by your employer, Please check your updated roster from your dashboard.',
        ];

        Mail::send(new sendMail($mailData));  
        return json_encode('Employee has been Notified');
    }

}