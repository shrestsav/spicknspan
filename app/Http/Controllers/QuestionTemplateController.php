<?php

namespace App\Http\Controllers;

use App\QuestionTemplate;
use App\Question;
use App\User;
use DB;
use Auth;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class QuestionTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $userId   = Auth::id();

        $qTemplate = QuestionTemplate::all();
        if(Entrust::hasRole('contractor')){
            $qTemplate = $qTemplate ->where('added_by', '=', $userId);
        }
        return view('backend.pages.question_template.list', compact('qTemplate'));
    }

    public function addMore()
    {
        return view('backend.pages.question_template.add');
    }


    public function addMorePost(Request $request)
    {
        $userId   = Auth::id();

        $rules = [];
        foreach($request->input('name') as $key => $value) {
            $rules["name.{$key}"] = 'required';
        }
        $validator = Validator::make($request->all(), $rules);

        $template_title = $request->input('question_template_title');
        QuestionTemplate::create(['template_title'=>$template_title, 'added_by'=>$userId]);
        $last_id = DB::getPdo()->lastInsertId();

        if ($validator->passes()) {
            foreach($request->input('name') as $key => $value) {                
                Question::create(['template_id'=>$last_id, 'name'=>$value]);
            }
            return response()->json(['success'=>'done']);
        }
        return response()->json(['error'=>$validator->errors()->all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.pages.question_template.add');
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
        QuestionTemplate::where('id','=',$id)->delete();
        Question::where('template_id','=',$id)->delete();
        return redirect()->back()->with('message','Question Template Deleted Successfully');
    }
}
