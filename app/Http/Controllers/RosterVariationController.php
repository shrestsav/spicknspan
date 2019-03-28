<?php

namespace App\Http\Controllers;

use DB;
use App\Attendance;
use App\Roster;
use App\User;
use Illuminate\Http\Request;

class RosterVariationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_lists   = User::all();
        $roster_lists = Roster::all();
        $variations   = DB::table('attendances')->orWhere('attendances.status', '=', 2)->orWhere('attendances.status', '=', 3)->get();
        return view('backend.pages.roster_variation',compact('variations', 'roster_lists', 'user_lists'));
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

    public function statusAccept(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        $active_status = Attendance::where('id',$id)->update(['status'=>1]);
        return redirect()->back()->with('message', 'Variation Approved');
    }

    public function statusDecline(Request $request, $id)
    {
        $attendance = Attendance::find($id);
        $active_status = Attendance::where('id',$id)->update(['status'=>3]);
        return redirect()->back()->with('message', 'Variation Decline');
    }
    
}
