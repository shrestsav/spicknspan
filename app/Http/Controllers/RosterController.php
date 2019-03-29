<?php

namespace App\Http\Controllers;

use App\Roster;
use App\RosterTimetable;
use DB;
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
        $employee = 'employee';
        $employee = User::select(
                                'users.id',
                                'users.name',
                                'users.email',
                                'users.user_type')
                        ->where('users.user_type','=',$employee)->get();

        $client = 'client';
        $client = User::select(
                                'users.id',
                                'users.name',
                                'users.email',
                                'users.user_type')
                        ->where('users.user_type','=',$client)->get();

        $rosters = Roster::all();
        $rosters = json_decode($rosters, true);
        
        $rostersTimetable = RosterTimetable::all();
        $rostersTimetable = json_decode($rostersTimetable, true);

        return view('backend.pages.roster',compact('rosters', 'rostersTimetable', 'employee', 'client'));
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
        $arr_employee_id   = $request['employee_id'];
        $arr_client_id     = $request['client_id'];

        $j = 0;
        $x = 0;
        foreach ($arr_employee_id as $emp_id) {

            $full_date  =  $request['full_date'];
            $month_part = explode('-', $full_date);
            $month = $month_part[1];

            if(($month == '01') || ($month == '03') || ($month == '05') || ($month == '07') || ($month == '08') || ($month == '10') || ($month == '12')){
                $k = 31;
            }
            elseif(($month == '04') || ($month == '06') || ($month == '09') || ($month == '11')){
                $k = 30;
            }
            else{
                $k = 28;
            }

            //check if the same data is added already
            $check   = DB::table('rosters')
                            ->where('rosters.employee_id', '=', $emp_id)
                            ->where('rosters.client_id', '=', $arr_client_id[$j])
                            ->where('rosters.full_date', '=', $full_date)
                            ->get();
            $check = json_decode($check, true);

            //add the data if it is new
            if(empty($check)){
                echo 'new record';
                Roster::create(['employee_id'=> $emp_id,'client_id'=>$arr_client_id[$j],'full_date'=>$request['full_date']]);
                $last_id = DB::getPdo()->lastInsertId();

                for ($i = 1; $i <= $k; $i++) {
                    $start_time    = $request['start_time_'.$i];
                    $end_time      = $request['end_time_'.$i];
                    $full_date     =  $request['full_date'].'-'.$i;
                    $timeIn        = \Carbon\Carbon::parse($start_time);
                    $timeOut       = \Carbon\Carbon::parse($end_time);
                    $diffInHours   = round($timeOut->diffInMinutes($timeIn) / 60);
                    
                    $roster_arr = [
                        'rosters_id'    => $last_id,
                        $full_date      =  $request['full_date'],
                        'full_date'     => $full_date.'-'.$i,
                        'start_time'    => $request['start_time_'.$i],
                        'end_time'      => $request['end_time_'.$i],
                        'total_hours'   => $diffInHours
                    ];
                    RosterTimetable::create( $roster_arr);
                }
            }
            else{
                //edit the current data
                echo 'old record';
                $old_rosters_id = $request['old_rosters_id'];
                // print_r($old_rosters_id);

                for ($i = 1; $i <= $k; $i++) {
                    $start_time    = $request['start_time_'.$i];
                    $end_time      = $request['end_time_'.$i];
                    $full_date     =  $request['full_date'].'-'.$i;
                    $timeIn        = \Carbon\Carbon::parse($start_time);
                    $timeOut       = \Carbon\Carbon::parse($end_time);
                    $diffInHours   = round($timeOut->diffInMinutes($timeIn) / 60);
                    
                    $full_date      =  $request['full_date'];
                    print_r($request['start_time_'.$i]);
                    print_r($request['end_time_'.$i]);
                    $roster_arr = [
                        'rosters_id'    => $old_rosters_id[$x],
                        'full_date'     => $full_date.'-'.$i,
                        'start_time'    => $request['start_time_'.$i],
                        'end_time'      => $request['end_time_'.$i],
                        'total_hours'   => $diffInHours
                    ];
                    RosterTimetable::where('id', '589')->update($roster_arr);
                    // print_r($roster_arr);
                }                
            }
            $j++; $x++;
        }
        // return redirect()->back()->with('message', 'Roster Added Successfully');
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
    public function destroy(Roster $roster)
    {
        //
    }
}
