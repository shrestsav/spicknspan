<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Building;
use App\Room;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $buildings = Building::all();
        $rooms = Room::select('rooms.id','rooms.name','rooms.description','rooms.building_id','buildings.building_no','rooms.room_no')->join('buildings','rooms.building_id','=','buildings.id')->get();
        // return $rooms;
        return view('backend.pages.sites',compact('buildings','rooms'));
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
        Building::create($request->all());
        return redirect()->back()->with('message', 'Building Added Successfully');

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
    public function store_room(Request $request)
    {
        Room::create($request->all());
        return redirect()->back()->with('message', 'Room Added Successfully');
    }
    public function generate_qr($id)
    {   
        // $rooms = Room::where('id',$id)->pluck('building_id')->first();
        $pngImage = \QrCode::format('png')
                            ->size(500)
                            ->generate($id);
 
        return response($pngImage)->header('Content-type','image/png');
    }
}
