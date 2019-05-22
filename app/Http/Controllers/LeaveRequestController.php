<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\LeaveRequest;
use Illuminate\Http\Request;

class LeaveRequestController extends Controller
{

    public function leave_requests(Request $request)
    {
        if($request->all()){
            $from_to = explode('-', $request->from_to);
            $from = Carbon::parse($from_to[0]);
            $to = Carbon::parse($from_to[1]);
            $request->merge(['from'=>$from,'to'=>$to,'user_id' => Auth::id()]);
            LeaveRequest::create($request->all());
        }
        return view('backend.pages.leave_app_form');
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
