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

class RosterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
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

        $employees = User::whereHas('roles', function ($query) {
                                $query->where('name', '=', 'employee')
                                      ->orWhere('name', '=', 'superAdmin');
                            });
                        
        $clients = User::whereHas('roles', function ($query) {
                            $query->where('name', '=', 'client');
                        });

        // This is used for pagination of roster as direct pagination on rosters query is not practical for this case, see the code and you'll get it
        $customPaginate = Roster::where('full_date','=',$year.'-'.$month);

        if($request->search_by_employee_id)
            $customPaginate->where('employee_id','=',$request->search_by_employee_id);
        if($request->search_by_client_id)
            $customPaginate->where('client_id','=',$request->search_by_client_id);
        if(Entrust::hasRole('contractor')){
            $clients->where('users.added_by','=',Auth::id());
            $employees->where('users.added_by','=',Auth::id());
            $customPaginate->where('added_by','=',Auth::id());
        }
        if(Entrust::hasRole('employee')){
            $employees->where('id','=',Auth::id());
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

        $employees = $employees->get();
        $clients = $clients->get();
        $rosters = $rosters->get();
        $rosters = $rosters->groupBy(['client_id','employee_id']);

        if(Entrust::hasRole('employee')){
            $clients = Auth::user()->clients(); 
        }

        return view('backend.pages.roster',compact('rosters','employees', 'clients','leaves','all_days','year','month','customPaginate'));
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
        //Not in use
        $j = 0;
        $x = 0;
        $full_dates = '';

        $arr_employee_id   = $request['employee_id'];
        $arr_client_id     = $request['client_id'];
        $full_date         = $request['full_date_add'];

        $month_part        = explode('-', $full_date);
        $month             = $month_part[1];

        if(($month == '01') || ($month == '03') || ($month == '05') || ($month == '07') || ($month == '08') || ($month == '10') || ($month == '12')){
            $k = 31;
        }
        elseif(($month == '04') || ($month == '06') || ($month == '09') || ($month == '11')){
            $k = 30;
        }
        elseif($month == '02'){
            $k = 28;
        }

        if(empty($arr_employee_id)){
            return redirect()->back()->with('error', 'No any rows to add/update.');
        }

        foreach ($arr_employee_id as $emp_id) {
            //check if the same data is added already
            $check  = DB::table('rosters')
                            ->where('rosters.employee_id', '=', $emp_id)
                            ->where('rosters.client_id', '=', $arr_client_id[$j])
                            ->where('rosters.full_date', '=', $full_date)
                            ->get();
            $check  = json_decode($check, true);

            //add the data if it is new
            if(empty($check)){
                echo 'new record';

                Roster::create(['employee_id'=> $emp_id,'client_id'=>$arr_client_id[$j],'full_date'=>$full_date,'added_by'=>Auth::id()]);
                $last_id = DB::getPdo()->lastInsertId();

                for ($i = 1; $i <= $k; $i++) {
                    $start_time    = $request['start_time_'.$i];
                    $end_time      = $request['end_time_'.$i];
                    
                    $full_dates  = \Carbon\Carbon::parse($full_date.'-'.$i);
                    $full_dates  = $full_dates->toDateString();

                    $roster_arr = [
                        'roster_id'    => $last_id,
                        'full_date'     => $full_dates,
                        'start_time'    => $request['start_time_'.$i],
                        'end_time'      => $request['end_time_'.$i]
                    ];
                    RosterTimetable::create( $roster_arr);
                }
            }
            else{
                //edit the current data
                $old_roster_id = $request['old_roster_id'];
                $roster_id     = $old_roster_id[$x];
                echo 'old record';

                for ($i = 1; $i <= $k; $i++) {

                    $start_time    = $request['start_time_'.($j)][$i-'1'];
                    $end_time      = $request['end_time_'.($j)][$i-'1'];


                    $full_dates  = \Carbon\Carbon::parse($full_date.'-'.$i);
                    $full_dates  = $full_dates->toDateString();


                    
                    RosterTimetable::where('roster_id', '=', $roster_id)
                        ->whereDate('full_date', '=', $full_dates)
                        ->update(['start_time' => $start_time, 'end_time' => $end_time]);

                }
            } 
            $x++; $j++;
        }
        return redirect()->back()->with('message', 'Roster Added Successfully');
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

}