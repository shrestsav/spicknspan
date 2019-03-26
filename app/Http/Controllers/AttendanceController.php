<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Client;
use App\Attendance;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clients = Client::all();
        
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
        $attendance_lists = Attendance::select('attendances.id','clients.name','attendances.check_in','attendances.check_out','attendances.created_at')->join('clients','attendances.client_id','=','clients.id')->get();
        return view('backend.pages.attendance_list',compact('attendance_lists'));
    }
    public function checkin(Request $request)
    {
        $client_id = $request->client;

        //Check if already Logged In
        $attendance_check = Attendance::where('client_id',$client_id)->whereDate('created_at',\Carbon\Carbon::today());
        if(!$attendance_check->exists()){
            $carbon = now();
            $current_date_time = $carbon->toDateTimeString();
            $check_in = new Attendance;
            $check_in->client_id = $client_id;
            $check_in->check_in = $current_date_time;
            $check_in->save();
        }
        else{
            return  redirect()->back()->withErrors('Client Already Logged In for Today');
        }
        return redirect()->back()->with('message', 'Client Logged in Successfully');
    }
    public function checkout(Request $request)
    {
        $client_id = $request->client;
        $carbon = now();
        $current_date_time = $carbon->toDateTimeString();
        $check_in = Attendance::where('client_id',$client_id)->whereDate('created_at',\Carbon\Carbon::today())->update(['check_out'=>$current_date_time]);
        
        return redirect()->back();
    }

}
