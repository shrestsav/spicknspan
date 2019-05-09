<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\SupportMail;
use App\User;
use Entrust;
use Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {    
      $supportMails = [];
      $superUsers = [];
      $assignedTasks = [];
      
      if(Entrust::hasRole('superAdmin')){
          $supportMails = SupportMail::select('support_mails.id',
                                              'support_mails.email',
                                              'support_mails.assigned_to',
                                              'support_mails.name',
                                              'support_mails.contact',
                                              'support_mails.subject',
                                              'support_mails.message',
                                              'support_mails.created_at',
                                              'users.name as assigned_to_name')
                                      ->leftJoin('users','users.id','=','support_mails.assigned_to')
                                      ->where('status',0)
                                      ->orWhere('status',1)
                                      ->get();
          $superUsers = User::whereHas('roles', function ($query) {
                                $query->where('name', '=', 'contractor')
                                      ->orWhere('name', '=', 'superAdmin');
                             })->get();
      }

      if(Entrust::hasRole(['contractor','superAdmin'])){
        $assignedTasks = SupportMail::where('assigned_to',Auth::id())->where('status',0)->get();
      }

      return view('backend.pages.dashboard',compact('supportMails','superUsers','assignedTasks'));
    }

    public function assignSupportTask(Request $request)
    {

        $validatedData = $request->validate([
                            'type' => 'required',
                            'support_message_id' => 'required'
                          ]);
        if($request->type=='assign'){
            $validatedData = $request->validate([
                            'assign_user_id' => 'required'
                          ]);
            $assign = SupportMail::where('id',$request->support_message_id)
                            ->update(['assigned_to' => $request->assign_user_id]);

            return back()->with('message','Assigned Succesfully');
        }
        if($request->type=='mark_done'){
            $mark_done = SupportMail::where('id',$request->support_message_id)
                            ->update(['status' => 3]);
            return back()->with('message','Marked as Done');
        }
    }
}
