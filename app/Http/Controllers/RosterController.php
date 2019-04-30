<?php

namespace App\Http\Controllers;

use App\Roster;
use App\RosterTimetable;
use DB;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RosterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $userId   = Auth::id();
        $userType = Auth::user()->user_type;
        
        if(isset($_GET['full_date'])){
            $date_filter = $_GET['full_date'];
        } else {
            $date_filter = '2019-04';
        }

        $employee = User::select(
                                'users.id',
                                'users.name',
                                'users.email',
                                'users.user_type')
                        ->where('users.user_type','=','employee');
        if($userType == 'contractor'){
            $employee = $employee ->where('users.added_by','=',$userId);
        }
        $employee = $employee->get();

        $client = User::select(
                                'users.id',
                                'users.name',
                                'users.email',
                                'users.user_type')
                        ->where('users.user_type','=','client');
        if($userType == 'contractor'){
            $client = $client ->where('users.added_by','=',$userId);
        }
        $client = $client->get();

        $rosters = Roster::all()->where('full_date','=',$date_filter);
        if($userType == 'contractor'){
            $rosters = $rosters ->where('added_by','=',$userId);
        }

        $n_rosters = json_decode($rosters, true);

        if($n_rosters == []) {
            // echo 'empty';
            $arr_rosters = '';
        } else {
            // echo 'not empty';
            foreach($rosters as $abcd)
            {
                $arr_rosters[] = $abcd->toArray();
            }
        }
        
        $rostersTimetable = RosterTimetable::all();
        $rostersTimetable = json_decode($rostersTimetable, true);

        return view('backend.pages.roster',compact('arr_rosters', 'rostersTimetable', 'employee', 'client'));
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

        $userId   = Auth::id();
        $userType = Auth::user()->user_type;

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
                Roster::create(['employee_id'=> $emp_id,'client_id'=>$arr_client_id[$j],'full_date'=>$full_date,'added_by'=>$userId]);
                $last_id = DB::getPdo()->lastInsertId();

                for ($i = 1; $i <= $k; $i++) {
                    $start_time    = $request['start_time_'.$i];
                    $end_time      = $request['end_time_'.$i];
                    $timeIn        = \Carbon\Carbon::parse($start_time);
                    $timeOut       = \Carbon\Carbon::parse($end_time);
                    $diffInHours   = round($timeOut->diffInMinutes($timeIn) / 60);
                    
                    $full_dates  = \Carbon\Carbon::parse($full_date.'-'.$i);
                    $full_dates  = $full_dates->toDateString();

                    $roster_arr = [
                        'rosters_id'    => $last_id,
                        'full_date'     => $full_dates,
                        'start_time'    => $request['start_time_'.$i],
                        'end_time'      => $request['end_time_'.$i],
                        'total_hours'   => $diffInHours
                    ];
                    RosterTimetable::create( $roster_arr);
                }
            }
            else{
                //edit the current data
                $old_rosters_id = $request['old_rosters_id'];
                $rosters_id     = $old_rosters_id[$x];
                echo 'old record';
                // die();
                for ($i = 1; $i <= $k; $i++) {

                    $start_time    = $request['start_time_'.($j)][$i-'1'];
                    $end_time      = $request['end_time_'.($j)][$i-'1'];
                    // if($i == 1){
                    //     echo $request['start_time_'.'0']['2'];
                    //     die();
                    // }
                    $timeIn        = \Carbon\Carbon::parse($start_time);
                    $timeOut       = \Carbon\Carbon::parse($end_time);
                    $diffInHours   = round($timeOut->diffInMinutes($timeIn) / 60);
                    // echo $full_dates; die();

                    $full_dates  = \Carbon\Carbon::parse($full_date.'-'.$i);
                    $full_dates  = $full_dates->toDateString();

                    // echo '<br>'.$rosters_id.'<br>'.$full_dates.'<br>'.$start_time.'<br>'.$end_time.'<br>'.$diffInHours.'<br>';
                    
                    RosterTimetable::where('rosters_id', '=', $rosters_id)
                        ->whereDate('full_date', '=', $full_dates)
                        ->update(['start_time' => $start_time, 'end_time' => $end_time, 'total_hours' => $diffInHours]);
                    // echo $full_dates; die();
                }
            } 
            $x++; $j++;
        }
        return redirect()->back()->with('message', 'Roster Added Successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Roster  $roster
     * @return \Illuminate\Http\Response
     */
    public function show(Roster $roster)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Roster  $roster
     * @return \Illuminate\Http\Response
     */
    public function edit(Roster $roster)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Roster  $roster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
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
}