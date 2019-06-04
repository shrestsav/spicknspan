<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\LeaveRequest;
use Illuminate\Http\Request;
use Entrust;

class LeaveRequestController extends Controller
{

    public function leaveRequests(Request $request)
    {
        $leave_requests = LeaveRequest::select('users.name',
                                                'users.added_by',
                                                'leave_requests.id',
                                                'leave_requests.user_id as employee_id',
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

        if($request->search_by_employee_id){
            $leave_requests->where('user_id','=',$request->search_by_employee_id);
        }
        if($request->search_by_lt){
            $leave_requests->where('leave_type','=',$request->search_by_lt);
        }
        if($request->search_by_status){
            $leave_requests->where('status','=',$request->search_by_status);
        }
        if($request->search_date_from_to){
          $search_date_from_to = explode("-", $request->search_date_from_to);
          $from = date('Y-m-d',strtotime($search_date_from_to[0]));
          $to = date('Y-m-d',strtotime($search_date_from_to[1]));
          $leave_requests->where('from','<=',$from);
          $leave_requests->where('to','>=',$to);
        }
        $leave_requests = $leave_requests->paginate(config('setting.rows'));
        return view('backend.pages.leave_app_form',compact('leave_requests'));
    }

    public function archivedleaveRequests(Request $request)
    {
        $leave_requests = LeaveRequest::onlyTrashed()
                                        ->select('users.name',
                                                'users.added_by',
                                                'leave_requests.id',
                                                'leave_requests.user_id as employee_id',
                                                'leave_requests.leave_type',
                                                'leave_requests.from',
                                                'leave_requests.to',
                                                'leave_requests.description',
                                                'leave_requests.status',
                                                'leave_requests.created_at')
                                        ->join('users','users.id','leave_requests.user_id');
                                        
        if(Entrust::hasRole('contractor')){
          $leave_requests->where('users.added_by',Auth::id());
        }
        if(!Entrust::hasRole(['contractor','superAdmin'])){
          $leave_requests->where('user_id','=',Auth::id());
        }         

        if($request->search_by_employee_id){
            $leave_requests->where('user_id','=',$request->search_by_employee_id);
        }
        if($request->search_by_lt){
            $leave_requests->where('leave_type','=',$request->search_by_lt);
        }
        if($request->search_by_status){
            $leave_requests->where('status','=',$request->search_by_status);
        }
        if($request->search_date_from_to){
          $search_date_from_to = explode("-", $request->search_date_from_to);
          $from = date('Y-m-d',strtotime($search_date_from_to[0]));
          $to = date('Y-m-d',strtotime($search_date_from_to[1]));
          $leave_requests->where('from','<=',$from);
          $leave_requests->where('to','>=',$to);
        }
        $leave_requests = $leave_requests->paginate(config('setting.rows'));
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
    public function archiveLeaveRequests(Request $request)
    {
        foreach ($request->sel_Rows as $rows) {
            LeaveRequest::where('id',$rows)->delete();
        }
        return json_encode('Archived Successfully');
    }
    public function undoArchiveLeaveApplication(Request $request)
    {
        foreach ($request->sel_Rows as $rows) {
            LeaveRequest::onlyTrashed()->where('id',$rows)->restore();
        }
        return json_encode('Restored Successfully');
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
