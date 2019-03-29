<?php

namespace App\Http\Controllers;

use App\Roster;
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

        $rosters = DB::table('rosters')->distinct()->get(['employee_id', 'client_id']);

        $rosters = json_decode($rosters, true);
        // print_r($rosters);
        return view('backend.pages.roster',compact('rosters', 'employee', 'client'));
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

            // print_r($month);

            for ($i = 1; $i <= $k; $i++) {
                $start_time    = $request['start_time_'.$i];
                $end_time      = $request['end_time_'.$i];

                $full_date      =  $request['full_date'].'-'.$i;
                $timeIn         = \Carbon\Carbon::parse($start_time);
                $timeOut        = \Carbon\Carbon::parse($end_time);
                $diffInHours    = round($timeOut->diffInMinutes($timeIn) / 60);
                
                $roster_arr = [
                'employee_id'   => $emp_id,
                'client_id'     => $arr_client_id[$j],
                $full_date      =  $request['full_date'],
                'full_date'     => $full_date.'-'.$i,
                'start_time'    => $request['start_time_'.$i],
                'end_time'      => $request['end_time_'.$i],
                'total_hours'   => $diffInHours
            ];
                Roster::create( $roster_arr);
            }
            $j++;
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
    public function destroy(Roster $roster)
    {
        //
    }
}
