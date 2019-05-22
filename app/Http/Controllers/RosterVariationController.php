<?php

namespace App\Http\Controllers;

use DB;
use App\Attendance;
use App\Roster;
use App\RosterTimetable;
use App\User;
use Illuminate\Http\Request;
use Auth;

class RosterVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //ShreStsaV
        $r_date = [];
        $roster_variations = Roster::select('rosters.id',
                                            'rt.status',
                                            'rt.date',
                                            'rt.start_time',
                                            'rt.end_time',
                                            'rosters.employee_id',
                                            'rosters.client_id',
                                            'client.name as client_name',
                                            'employee.name as employee_name')
                                    ->join('roster_timetables as rt','rt.roster_id','=','rosters.id')
                                    ->join('users as employee','rosters.employee_id','=','employee.id')
                                    ->join('users as client','rosters.client_id','=','client.id')
                                    ->where('rt.start_time','!=',null)
                                    ->get();
         // return $roster_variations;                               
        $attendances = Attendance::select('attendances.client_id',
                                         'attendances.employee_id',
                                         'attendances.check_in',
                                         'attendances.check_out',
                                         'client.name as client_name',
                                         'employee.added_by as added_by',
                                         'employee.name as employee_name')
                                  ->join('users as employee','attendances.employee_id','=','employee.id')
                                  ->join('users as client','attendances.client_id','=','client.id')
                                  ->orderBy('attendances.check_in','desc')
                                  ->get()
                                  ->toArray();

        $att_colln = collect($attendances);

        //Collect all rostered dates in array 
        foreach($roster_variations as $r_variation){
            $r_date[] = \Carbon\Carbon::parse($r_variation->date)->format('Y-m-d');
            $ros_startTime = \Carbon\Carbon::parse($r_variation->start_time);
            $ros_endTime = \Carbon\Carbon::parse($r_variation->end_time);
            $ros_totalDuration = $ros_endTime->diffInSeconds($ros_startTime);
            $r_variation['roster_period'] = $ros_totalDuration;
        }

        // Filter only attendances with roster matching dates in users timezone
        $att_colln = $att_colln->whereIn('local_check_in.date',$r_date);

        $grouped_attendances = $att_colln->groupBy(['local_check_in.date','employee_id'])->toArray();

        // Loop all attendances records and merge the check total hours to $roster_variations
        foreach($grouped_attendances as $date => $grouped_attendance){
            foreach($grouped_attendance as $id => $details){
                $count = count($details);
                $totalSec = 0;
                foreach($details as $detail){
                    if($detail['check_in'] && $detail['check_out']){
                        $startTime = \Carbon\Carbon::parse($detail['check_in']);
                        $endTime = \Carbon\Carbon::parse($detail['check_out']);
                        $totalDuration = $endTime->diffInSeconds($startTime);
                        $totalSec += $totalDuration;
                    }
                }
                foreach($roster_variations as $r_variation){
                    if($r_variation->date==$details[0]['local_check_in']['date'] && $r_variation->client_id==$details[0]['client_id'] && $r_variation->employee_id==$details[0]['employee_id']){
                        $r_variation['variation'] = $r_variation['roster_period']-$totalSec;
                        $r_variation['attended_period'] = $totalSec;
                    }
                }
            }
        }  
        
        return view('backend.pages.roster_variation',compact('roster_variations'));
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

    public function statusAccept(Request $request, $id, $date)
    {
        $approve = RosterTimetable::where('roster_id',$id)->whereDate('date',$date)->update(['status' => 1, 'approved_by' => Auth::id()]);
        if($approve)
            return redirect()->back()->with('message', 'Variation Approved');

        return redirect()->back()->with('error', 'Failed');
    }

    public function statusDecline(Request $request, $id, $date)
    {
        $decline = RosterTimetable::where('roster_id',$id)->whereDate('date',$date)->update(['status'=>2, 'approved_by' => Auth::id()]);
        if($decline)
            return redirect()->back()->with('message', 'Variation Decline');

        return redirect()->back()->with('error', 'Failed');
    }
    
}
