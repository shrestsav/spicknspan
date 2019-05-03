<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\UserDetail;
use App\Role;
use Entrust;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;

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
        $userId   = Auth::id();

        if(\Route::current()->getName() == 'user_company.index'){
            $user_type = 'company';
        }
        elseif(\Route::current()->getName() == 'user_employee.index'){
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
                        ->whereHas('roles', function ($query) use ($user_type) {
                                $query->where('name', '=', $user_type);
                             });

        if(Entrust::hasRole('contractor')){
            $users = $users->where('added_by','=',$userId);
        }
        $users = $users->get();
        // return $users;
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
      // return $request->all();
     
        $userId   = Auth::id();

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required',
            'contact' => 'required',
            'employment_start_date' => 'required',
            'timezone' => 'required',
        ]);
        
        //Decrypt User_type
        $decrypt_user_type = decrypt($request['utilisateur']);
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'user_type' => $decrypt_user_type,
            'mark_default' => $request['mark_default'],
            'added_by' => $userId,
            'timezone' => $request['timezone'],
        ]);

        $user_id = $user->id;

        //Save User Photo 
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $fileName = 'dp_user_'.$user_id.'.'.$photo->getClientOriginalExtension();
            $uploadDirectory = public_path('files'.DS.'users'.DS.$user_id);
            $photo->move($uploadDirectory, $fileName);
        } else {
          $fileName = 'no_image';
        }

        $db_arr = [];
        if ($request->hasFile('documents')) {
            $documents = $request->file('documents');
            $count = 1;
            foreach($documents as $document){
              $documentName = $user_id.'_document_'.$count.'_'.$document->getClientOriginalName();
              $uploadDirectory = public_path('files'.DS.'users'.DS.$user_id);
              $document->move($uploadDirectory, $documentName);
              $db_arr['document_'.$count] = $documentName;
              $count++;
            }  
        }
        //Update User Details Table
        UserDetail::create([
            'user_id' => $user_id,
            'photo' => $fileName,
            'address' => $request['address'],
            'gender' => $request['gender'],
            'contact' => $request['contact'],
            'hourly_rate' => $request['hourly_rate'],
            'annual_salary' => $request['annual_salary'],
            'description' => $request['description'],
            'date_of_birth' => $request['date_of_birth'],
            'employment_start_date' => $request['employment_start_date'],
            'documents' => json_encode($db_arr),
        ]);

        $roles = Role::pluck('name','id');
        foreach($roles as $role_id => $role){
          if($role==$decrypt_user_type){
            $user->attachRole($role_id);
          }
        }

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
        $user = User::select(
                              'users.id',
                              'users.name',
                              'users.email',
                              'users.user_type',
                              'users.timezone',
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
                        ->where('users.id','=',$id)
                        ->first();

        return view('backend.pages.edit_people',compact('user'));
    }

    public function profile_edit($id)
    {
        $user = User::select(
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
                        ->where('users.id','=',$id)->first();

        return view('backend.pages.edit_people',compact('user'));
    }

    public function password_edit()
    {
        return view('backend.pages.change_password');
    }

    public function password_update(Request $request, $id)
    {
        $user_old_password = \Auth::user()->getAuthPassword();
        if( Hash::check(Input::get('old_password'), $user_old_password)) { 
            $validatedData = $request->validate([
                'password' => 'required|string|min:8|confirmed'
            ]);
            $update_user = User::where('id','=',$id)
                          ->update(['password' => Hash::make($request['password'])]);
            return redirect()->back()->with('message','Password Updated Successfully');
        } else {
            return redirect()->back()->with('error','Old & New Password do not match.');
      }
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
        $update_user = User::where('id','=',$id)
                      ->update(['name' => $request->name,
                                'email' => $request->email,
                                'timezone' => $request->timezone,
                                  ]);
        $update_user_details = UserDetail::where('user_id','=',$id)
               ->update([
                          'gender' => $request->gender,
                          'hourly_rate' => $request->hourly_rate,
                          'address' => $request->address,
                          'contact' => $request->contact,
                          'date_of_birth' => $request->date_of_birth,
                          'photo' => $request->photo,
                          'annual_salary' => $request->annual_salary,
                          'description' => $request->description,
                          'employment_start_date' => $request->employment_start_date
                       ]);
        if($request->user_type=='employee')
            $route = 'user_employee.index';
        if($request->user_type=='client')
            $route =  'user_client.index';
        if($request->user_type=='contractor')
            $route =  'user_contractor.index';
        if($request->user_type=='company')
            $route =  'user_company.index';
        return redirect()->route($route)->with('message', 'Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id','=',$id)->delete();
        UserDetail::where('user_id','=',$id)->delete();
        return redirect()->back()->with('message','Deleted Successfully');
    }
}
