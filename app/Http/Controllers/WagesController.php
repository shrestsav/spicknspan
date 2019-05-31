<?php

namespace App\Http\Controllers;

use App\Wages;
use App\User;
use Auth;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $wages = Wages::select('wages.id', 
                               'wages.employee_id', 
                               'wages.client_id', 
                               'wages.hourly_rate',
                               'employee.name as employee_name',
                               'client.name as client_name')
                        ->join('users as employee','employee.id','wages.employee_id')
                        ->join('users as client','client.id','wages.client_id');
      
        $employees = User::select('users.id','users.name','users.email','users.user_type')
                            ->whereHas('roles', function ($query) {
                                $query->where('name', '=', 'employee');
                            });

        $clients = User::select('users.id','users.name','users.email','users.user_type')
                        ->whereHas('roles', function ($query) {
                            $query->where('name', '=', 'client');
                        });

        if(Entrust::hasRole('contractor')){
            $wages->where('wages.added_by','=',Auth::id());
            $employees->where('users.added_by','=',Auth::id());
            $clients->where('users.added_by','=',Auth::id());
        }
        if($request->search_by_user_id)
            $wages->where('wages.employee_id','=',$request->search_by_user_id);
        if($request->search_by_client_id)
            $wages->where('wages.client_id','=',$request->search_by_client_id);

        $employees = $employees->get();
        $wages = $wages->get();
        $clients = $clients->get();

        return view('backend.pages.wages',compact('wages','employees','clients'));
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
        $validatedData = $request->validate([
            'employee_id' => 'required',
            'client_id' => 'required',
            'hourly_rate' => 'required',
        ]);

        // Check if already exists
        $check = Wages::where('employee_id',$request->employee_id)
                        ->where('client_id',$request->client_id)
                        ->exists();

        if($check){
            return redirect()->back()->withErrors('Wages for selected employee on client already exists');
        }

        $request->merge(['added_by' => Auth::id()]);
        Wages::create($request->all());
        return redirect()->back()->with('message', 'Added Successfully');
        
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
    public function edit(Request $request)
    {
        $wage_id = decrypt($request->wage_id);
        $wages = Wages::select('wages.id', 
                               'wages.employee_id', 
                               'wages.client_id', 
                               'wages.hourly_rate',
                               'employee.name as employee_name',
                               'client.name as client_name')
                        ->join('users as employee','employee.id','wages.employee_id')
                        ->join('users as client','client.id','wages.client_id')
                        ->where('wages.id','=',$wage_id)
                        ->first();

        $view = view('backend.modals.render.wages_edit')
                ->with(['wages' => $wages])
                ->render();

        $response = [
           'status' => true,
           'title' => $wages->employee_name,
           'html' => $view
        ];

       return response()->json($response);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $update = Wages::where('id',$request->wage_id)->update(['hourly_rate' => $request->hourly_rate]);
        if($update)
            return back()->with('message','Wages has been updated');
        else
            return back()->withErrors('Wage could not be updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $wages = Wages::find(decrypt($id))->delete(); 
        return redirect()->back()->with('message','Wages Deleted Successfully');
    }
}
