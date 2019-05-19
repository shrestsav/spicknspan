<?php

namespace App\Http\Controllers;

use App\Roster;
use App\RosterTimetable;
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
    public function index()
    {
        if(isset($_GET['full_date'])){
            $date_filter = $_GET['full_date'];
        } else {
            $date_filter = '2019-05';
        }

        $employees = User::select('users.id',
                                 'users.name',
                                 'users.email',
                                 'users.user_type');
                        
        $clients = User::select( 'users.id',
                                'users.name',
                                'users.email',
                                'users.user_type')
                        ->where('users.user_type','=','client');

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
                        ->where('full_date','=',$date_filter)
                        ->where('start_time','!=',null);

        if(Entrust::hasRole('contractor')){
            $clients = $clients->where('users.added_by','=',Auth::id());
            $employees = $employees ->where('users.added_by','=',Auth::id());
            $rosters = $rosters ->where('added_by','=',Auth::id());
        }

        $employees = $employees->get();
        $clients = $clients->get();
        $rosters = $rosters->get();
        $rosters = $rosters->groupBy(['client_id','employee_id']);

        return view('backend.pages.roster',compact('rosters','employees', 'clients'));
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
        $j = 0;
        $x = 0;
        $full_dates = '';

        $arr_employee_id   = $request['employee_id'];
        $arr_client_id     = $request['client_id'];
        $full_date         = $request['full_date_add'];
        // echo $full_date;
        // die();
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
                // die();
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
                // die();
                for ($i = 1; $i <= $k; $i++) {

                    $start_time    = $request['start_time_'.($j)][$i-'1'];
                    $end_time      = $request['end_time_'.($j)][$i-'1'];


                    $full_dates  = \Carbon\Carbon::parse($full_date.'-'.$i);
                    $full_dates  = $full_dates->toDateString();

                    // echo '<br>'.$roster_id.'<br>'.$full_dates.'<br>'.$start_time.'<br>'.$end_time.'<br>'.$diffInHours.'<br>';
                    
                    RosterTimetable::where('roster_id', '=', $roster_id)
                        ->whereDate('full_date', '=', $full_dates)
                        ->update(['start_time' => $start_time, 'end_time' => $end_time]);
                    // echo $full_dates; die();
                }
            } 
            $x++; $j++;
        }
        return redirect()->back()->with('message', 'Roster Added Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Roster  $roster
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::table("rosters")->delete($id);

        return response()->json(['success'=>"Roster Deleted successfully.", 'tr'=>'tr_'.$id]);
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        // echo $ids;
        // die();
        DB::table("rosters")->whereIn('id',explode(",",$ids))->delete();
        return response()->json(['success'=>"Rosters Deleted successfully."]);
    }

    public function ajax_store_roster(Request $request)
    {   
        $type = $request->type;
        $client_id = $request->client_id;
        $employee_id = $request->employee_id;
        $time = $request->time;
        $full_date = Carbon::parse($request->date)->format('Y-m');
        $request->merge(['full_date' => $full_date,'added_by' => Auth::id()]);

        //Check if exists
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
                return json_encode('Roster exits timetable not');
            }
        }
        
    }

    public function ajax_update_roster(Request $request)
    {
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

}