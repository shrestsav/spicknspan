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
use Illuminate\Support\Facades\Mail;
use App\Mail\sendMail;

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
        $users = User::select('users.id',
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
                              'user_details.currency',
                              'user_details.description',
                              'user_details.date_of_birth',
                              'user_details.employment_start_date',
                              'user_details.documents')
                        ->join('user_details','user_details.user_id','=','users.id')
                        ->whereHas('roles', function ($query) use ($user_type) {
                                $query->where('name', '=', $user_type);
                             });
        $clients = User::select('id','name')->whereHas('roles', function ($query) {
                                $query->where('name', '=', 'client');
                             });
        if(Entrust::hasRole('contractor')){
            $users->where('added_by','=',$userId);
            $clients->where('added_by','=',$userId);
        }
        $users = $users->get();
        $clients = $clients->get();
        return view('backend.pages.user.index',compact('users','clients'));
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
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required',
            'contact' => 'required',
            'employment_start_date' => 'required',
            'timezone' => 'required',
        ]);

        $userId   = Auth::id();
        $client_ids =  json_encode($request->client_ids);

        //Decrypt User_type
        $decrypt_user_type = decrypt($request['utilisateur']);
        $user = User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'user_type' => $decrypt_user_type,
            'mark_default' => $request['mark_default'],
            'added_by' => $userId,
            'client_ids' => $client_ids,
            'timezone' => $request['timezone'],
        ]);

        if($user){

          $user_id = $user->id;
          $fileName = '';
          //Save User Photo 
          if ($request->hasFile('photo')) {
              $photo = $request->file('photo');
              $fileName = 'dp_user_'.$user_id.'.'.$photo->getClientOriginalExtension();
              $uploadDirectory = public_path('files'.DS.'users'.DS.$user_id);
              $photo->move($uploadDirectory, $fileName);
          } 

          $db_arr = [];
          
          if ($request->hasFile('documents')) {
              $documents = $request->file('documents');
              $count = 1;
              foreach($documents as $document){
                $documentName = $user_id.'_document_'.$count.'_'.$document->getClientOriginalName();
                $uploadDirectory = public_path('files'.DS.'users'.DS.$user_id);
                $document->move($uploadDirectory, $documentName);
                // $db_arr['document_'.$count] = $documentName;
                $db_arr[] = $documentName;
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
              'currency' => $request['currency'],
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

          $mailData = [
            'email_type' => 'registration',
            'name'       => $request['name'],
            'username'   => $request['email'],
            'password'   => $request['password'],
            'subject'    => 'Greetings '.$request['name'].': Welcome to Spick and Span Team',
            'message'    => 'These are your login credentials for Spick and Span Portal. Please change your password immediately after you receive this email for User privacy',
          ];

          Mail::send(new sendMail($mailData));
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
	      $user_details = User::with('detail')->get();
	      return $user_details;
    }

    public function ajax_user_details(Request $request)
    {
        $user_details = User::select(
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
                              'user_details.currency',
                              'user_details.description',
                              'user_details.date_of_birth',
                              'user_details.employment_start_date',
                              'user_details.documents')
                        ->join('user_details','user_details.user_id','=','users.id')
                        ->where('users.id','=',$request->user_id)
                        ->first();

        // return json_encode($user_details);
        $view = view('backend.modals.render.user_details')->with([
           'user_details' => $user_details ])->render();

        $response = [
           'status' => true,
           'title' => $user_details->name,
           'html' => $view
        ];
       return response()->json($response);

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
                              'users.client_ids',
                              'user_details.photo',
                              'user_details.address',
                              'user_details.gender',
                              'user_details.contact',
                              'user_details.hourly_rate',
                              'user_details.annual_salary',
                              'user_details.currency',
                              'user_details.description',
                              'user_details.date_of_birth',
                              'user_details.employment_start_date',
                              'user_details.documents')
                        ->join('user_details','user_details.user_id','=','users.id')
                        ->where('users.id','=',$id);

        $clients = User::select('id','name')->whereHas('roles', function ($query) {
                                $query->where('name', '=', 'client');
                             });

        if(Entrust::hasRole('contractor')){
            $user->where('added_by','=',Auth::id());
            $clients->where('added_by','=',Auth::id());
        }

        $user = $user->first();
        $clients = $clients->get();
        return view('backend.pages.user.edit',compact('user','clients'));
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
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'gender' => 'required',
            'contact' => 'required',
            'employment_start_date' => 'required',
            'timezone' => 'required',
        ]);

        //Update Client_ids of user
        $previous_client_ids = json_decode(User::find($id)->client_ids);
        $deleted_client_ids = json_decode($request->deleted_client_ids);
        $added_client_ids = json_decode($request->added_client_ids);
      
        if(!$deleted_client_ids)
          $deleted_client_ids = [];
        if(!$added_client_ids)
          $added_client_ids = [];
          
        $after_delete = array_values(array_diff($previous_client_ids, $deleted_client_ids));
        $updated_client_ids = json_encode(array_merge($after_delete,$added_client_ids));

        $update_user = User::where('id','=',$id)
                            ->update([
                              'name' => $request->name,
                              'email' => $request->email,
                              'timezone' => $request->timezone,
                              'client_ids' => $updated_client_ids,
                              ]);

        if($update_user){
          $fileName = '';
          //Save User Photo 
          if ($request->hasFile('photo')) {
              $photo = $request->file('photo');
              $fileName = 'dp_user_'.$id.'.'.$photo->getClientOriginalExtension();
              $uploadDirectory = public_path('files'.DS.'users'.DS.$id);
              $photo->move($uploadDirectory, $fileName);
          } 

          $db_arr =  json_decode($request->left_user_doc_array, true);
          $doc_del =  json_decode($request->del_user_doc_array, true);

          if($doc_del){
            $documentDirectory = public_path('files'.DS.'users'.DS.$id);
            foreach($doc_del as $del){
              \File::delete(public_path('files'.DS.'users'.DS.$id.DS).$del);
            }
          }

          if ($request->hasFile('documents')) {
              $documents = $request->file('documents');
              $count = 1;
              foreach($documents as $document){
                $documentName = $id.'_document_'.$count.'_'.$document->getClientOriginalName();
                $uploadDirectory = public_path('files'.DS.'users'.DS.$id);
                $document->move($uploadDirectory, $documentName);
                $db_arr[] = $documentName;
                $count++;
              }  
          }

          $update_user_details = UserDetail::where('user_id','=',$id)
                 ->update([
                            'gender' => $request->gender,
                            'hourly_rate' => $request->hourly_rate,
                            'address' => $request->address,
                            'contact' => $request->contact,
                            'date_of_birth' => $request->date_of_birth,
                            'photo' => $fileName,
                            'annual_salary' => $request->annual_salary,
                            'currency' => $request->currency,
                            'description' => $request->description,
                            'employment_start_date' => $request->employment_start_date,
                            'documents' => json_encode($db_arr),
                         ]);
        }

        // if($request->user_type=='employee')
        //     $route = 'user_employee.index';
        // if($request->user_type=='client')
        //     $route =  'user_client.index';
        // if($request->user_type=='contractor')
        //     $route =  'user_contractor.index';
        // if($request->user_type=='company')
        //     $route =  'user_company.index';
        // return redirect()->route($route)->with('message', 'Updated Successfully');
        return redirect()->back()->with('message', 'Updated Successfully');
    }
    
    //Not in use
    public function ajax_delete_documents(Request $request)
    {
      $user_id = $request->user_id;
      $user_documents_json = UserDetail::where('user_id',$user_id)->first()->documents;
      $user_documents = json_decode($user_documents_json, true);
      $fileName = $user_documents[$request->document_id];
      unset($user_documents[$request->document_id]);
      $updated_user_documents_json = json_encode($user_documents);
      $update_user_documents = UserDetail::where('user_id',$request->user_id)
                                          ->update(['documents' => $updated_user_documents_json]);
      if($update_user_documents){
        $documentDirectory = public_path('files'.DS.'users'.DS.$user_id);
        \File::delete(public_path('files'.DS.'users'.DS.$user_id.DS).$fileName);
      }
      return json_encode($documentDirectory);

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

        return view('backend.pages.user.edit',compact('user'));
    }

    public function password_edit()
    {
        return view('backend.pages.user.profile');
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
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::where('id',$id)->delete();
        return redirect()->back()->with('message','Deleted Successfully');
    }
}
