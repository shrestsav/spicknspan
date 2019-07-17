<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Building;
use App\QuestionTemplate;
use App\Room;
use Auth;
use Entrust;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $questionTemplate = QuestionTemplate::select('template_title',
                                                     'category_id',
                                                     'name',
                                                     'added_by');
        $buildings = Building::select('id',
                                     'name',
                                     'building_no',
                                     'address',
                                     'description',
                                     'added_by');
        $rooms = Room::select('rooms.id',
                              'rooms.name',
                              'rooms.description',
                              'rooms.building_id',
                              'rooms.question_id',
                              'buildings.building_no',
                              'buildings.id as building_id',
                              'question_template.template_title',
                              'rooms.room_no')
                    ->join('buildings','rooms.building_id','=','buildings.id')
                    ->whereNull('buildings.deleted_at')
                    ->leftJoin('question_template','rooms.question_id','=','question_template.id');

        if(Entrust::hasRole('contractor')){
            $buildings->where('added_by','=',Auth::id());
            $rooms->where('added_by','=',Auth::id());
            $questionTemplate->where('added_by','=',Auth::id());
        }
        $site_building_search = $buildings->get();
        $site_room_search = $rooms->get();

        if($request->all()){
            if($request->B_search_by_id)
                $buildings->where('id',$request->B_search_by_id);
            if($request->B_search_by_address)
                $buildings->where('address',$request->B_search_by_address);
            if($request->R_search_by_id)
                $rooms->where('rooms.id',$request->R_search_by_id);
            if($request->R_search_by_building_id)
                $rooms->where('buildings.id',$request->R_search_by_building_id);
        }
        $buildings = $buildings->get();
        $rooms = $rooms->get();
        $questionTemplate = $questionTemplate->get(); 

        return view('backend.pages.sites',compact('buildings','rooms', 'questionTemplate','site_building_search', 'site_room_search'));
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
        Building::create([
            'name'              => $request['name'],
            'building_no'       => $request['building_no'],
            'address'           => $request['address'],
            'description'       => $request['description'],
            'image'             => $request['image'],
            'gps_coordinates'   => $request['gps_coordinates'],
            'added_by'          => Auth::id()] );
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
        Room::create([
                    'building_id'  => $request['building_id'],
                    'name'         => $request['name'],
                    'room_no'      => $request['room_no'],
                    'description'  => $request['description'],
                    'image'        => $request['image'],
                    'question_id'  => $request['question_id'],
                    'added_by'     => Auth::id()] );

        return redirect(route('site.index') . '#area_division')->with('message', 'Room Added Successfully');
    }

    public function delete_room($id)
    {
        $room = Room::find(decrypt($id))->delete(); 
        return redirect()->back()->with('message','Room Deleted Successfully');
    }

    public function delete_building($id)
    {
        $building = Building::find(decrypt($id))->delete(); 
        return redirect()->back()->with('message','Building Deleted Successfully');
    }

    public function generate_qr($json)
    {   
        $array = json_decode($json);
        $pngImage = [];
        $room_no = Room::select('room_no')->whereIn('id',$array)->pluck('room_no')->toArray();
        // return var_dump($room_no);
        foreach ($array as $room_id) {
            $pngImage[] = \QrCode::format('png')
                            ->size(150)
                            ->generate($room_id);
        }
        return view('backend.pages.printqr',compact('pngImage','room_no'));
        // return response($pngImage)->header('Content-type','image/png');
    }

}