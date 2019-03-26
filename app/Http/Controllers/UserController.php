<?php

namespace App\Http\Controllers;

use App\User;
use App\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        //
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        //Check if Admin
        // if(!\Gate::allows('isAdmin')){
        //     abort(403,"Sorry, You can do this action");
        // }
        
        if(\Route::current()->getName() == 'user_employee.index'){
            $user_type = 'employee';
        }
        elseif(\Route::current()->getName() == 'user_contractor.index'){
            $user_type = 'contractor';
        }
        elseif(\Route::current()->getName() == 'user_client.index'){
            $user_type = 'client';
        }
        $users = User::select(
                                'users.id',
                                'users.name',
                                'users.email',
                                'users.user_type',
                                'user_details.photo',
                                'user_details.address',
                                'user_details.gender',
                                'user_details.contact',
                                'user_details.hourly_rate',
                                'user_details.annual_salary',
                                'user_details.description',
                                'user_details.date_of_birth',
                                'user_details.employment_start_date',
                                'user_details.documents')
                        ->join('user_details','user_details.user_id','=','users.id')
                        ->where('users.user_type','=',$user_type)->get();
        return view('backend.pages.people',compact('users'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'required',
            'address' => 'required',
            'description' => 'required',
            'date_of_birth' => 'required',
            'employment_start_date' => 'required',
        ]);

        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'user_type' => $request['user_type'],
        ]);

        $user_id = $user->id;
        
        UserDetail::create([
            'user_id' => $user_id,
            'photo' => 'Test Data Only',
            'address' => $request['address'],
            'gender' => $request['gender'],
            'contact' => $request['contact'],
            'hourly_rate' => $request['hourly_rate'],
            'annual_salary' => $request['annual_salary'],
            'description' => $request['description'],
            'date_of_birth' => $request['date_of_birth'],
            'employment_start_date' => $request['employment_start_date'],
        ]);
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
}
