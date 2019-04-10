<?php

namespace App\Http\Controllers;

use App\User;
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
        $buildings = Building::all();
        $users = User::all();
        $questionTemplate = QuestionTemplate::all();
        $rooms = Room::select('rooms.id','rooms.name','rooms.description','rooms.building_id','rooms.question_id','buildings.building_no','rooms.room_no')->join('buildings','rooms.building_id','=','buildings.id')->leftJoin('question_template','rooms.question_id','=','question_template.id')->get();
        // return $rooms;
        return view('backend.pages.sites',compact('buildings','rooms', 'users', 'questionTemplate'));
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
        // return $request->all();
        Room::create($request->all());
        return redirect(route('site.index') . '#area_division')->with('message', 'Area/Division Added Successfully');
    }

    public function delete_room(Request $request, $id)
    {
        $room = Room::find($id); 
        $room->delete(); //delete the id
        return redirect()->back()->with('message', 'Area/Division Deleted Successfully');
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
