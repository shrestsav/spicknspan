<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\LeaveRequest;
use Illuminate\Http\Request;
use Entrust;

class LeaveRequestController extends Controller
{

    public function leaveRequests()
    {
        $leave_requests = LeaveRequest::select('users.name',
                                                'users.added_by',
                                                'leave_requests.id',
                                                'leave_requests.user_id',
                                                'leave_requests.leave_type',
                                                'leave_requests.from',
                                                'leave_requests.to',
                                                'leave_requests.description',
                                                'leave_requests.status',
                                                'leave_requests.created_at',)
                                        ->join('users','users.id','leave_requests.user_id');
                                        
        if(Entrust::hasRole('contractor')){
          $leave_requests->where('users.added_by',Auth::id());
        }
        if(!Entrust::hasRole(['contractor','superAdmin'])){
          $leave_requests->where('user_id','=',Auth::id());
        }                  
        $leave_requests = $leave_requests->get();
        return view('backend.pages.leave_app_form',compact('leave_requests'));
    }
    public function createLeaveRequests(Request $request)
    {
        $from_to = explode('-', $request->from_to);
        $from = Carbon::parse($from_to[0]);
        $to = Carbon::parse($from_to[1]);
        $request->merge(['from'=>$from,'to'=>$to,'user_id' => Auth::id()]);
        LeaveRequest::create($request->all());
        return back()->with('message','Leave Application Submitted Successfully');
    }
    public function updateStatus(Request $request)
    {
        if($request->type=='approve'){
            LeaveRequest::where('id',$request->id)->update(['status'=>'1']);
            return back()->with('message','Application Approved');
        }
        if($request->type=='deny'){
            LeaveRequest::where('id',$request->id)->update(['status'=>'2']);
            return back()->with('message','Application Denied');
        }  
    }

    public function ajaxUserLeaveRecord(Request $request)
    {
    	$year_month = explode('-',$request->year_month);
    	$user_id = $request->employee_id;
    	$year = $year_month[0];
    	$month = $year_month[1];
    	$leave_records = LeaveRequest::where(function ($query) use ($year,$month,$user_id) {
							                $query->where('user_id',$user_id)
							                	  ->where('status','1')
							                	  ->whereMonth('from',$month)
							                      ->whereYear('from',$year);    
							            })
							    	->orWhere(function ($query) use ($year,$month,$user_id) {
							                $query->where('status','1')
							                      ->where('user_id',$user_id)
							                      ->whereMonth('to',$month)
							                      ->whereYear('to',$year);
							            })
    								->get();
    	return json_encode($leave_records);
    }

}
