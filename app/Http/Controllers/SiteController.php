<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Building;
use App\QuestionTemplate;
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
        $s_user_id   = session('user_id');
        $s_added_by  = session('added_by');
        
        $buildings = Building::all()->where('added_by','=',$s_user_id);
        $questionTemplate = QuestionTemplate::all()->where('added_by','=',$s_user_id);
        $rooms = Room::select(
                            'rooms.id',
                            'rooms.name',
                            'rooms.description',
                            'rooms.building_id',
                            'rooms.question_id',
                            'buildings.building_no',
                            'rooms.room_no')
                        ->join('buildings','rooms.building_id','=','buildings.id')
                        ->join('question_template','rooms.question_id','=','question_template.id')
                        ->get();
        return view('backend.pages.sites',compact('buildings','rooms', 'questionTemplate'));
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
        $s_user_id   = session('user_id');
        $s_added_by  = session('added_by');
        // print_r([$request->all(), 'added_by'=>$s_added_by]);
        // die();
        Building::create([
            'name'              => $request['name'],
            'building_no'       => $request['building_no'],
            'address'           => $request['address'],
            'description'       => $request['description'],
            'image'             => $request['image'],
            'gps_coordinates'   => $request['gps_coordinates'],
            'added_by'          => $s_user_id] );
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
        // return $request->all();
        $s_user_id   = session('user_id');
        $s_added_by  = session('added_by');
        
        Room::create([
            'building_id'       => $request['building_id'],
            'name'              => $request['name'],
            'room_no'           => $request['room_no'],
            'description'       => $request['description'],
            'image'             => $request['image'],
            'question_id'       => $request['question_id'],
            'added_by'          => $s_user_id] );
        return redirect(route('site.index') . '#area_division')->with('message', 'Room Added Successfully');
    }

    public function delete_room(Request $request, $id)
    {
        $room = Room::find($id); 
        $room->delete(); //delete the id
        return redirect(route('site.index') . '#area_division')->with('message', 'Room Deleted Successfully');
    }

    public function generate_qr($id)
    {   
        $pngImage = \QrCode::format('png')
                            ->size(400)
                            ->generate($id);
        return view('backend.pages.printqr',compact('pngImage'));
        // return response($pngImage)->header('Content-type','image/png');
    }
}