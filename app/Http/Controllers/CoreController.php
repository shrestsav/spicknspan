<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\User;
use App\UserDetail;
use App\SiteAttendance;
use Excel;
use App\Exports\DataExport;
use App\Imports\DataImport;
use Illuminate\Http\Request;

class CoreController extends Controller
{
    public function import_from_excel(Request $request){
    	$request->validate([
            'file' => 'required|mimes:xlsx'
        ]);
    	$user_type = $request->user_type;
    	Excel::import(new DataImport($user_type),request()->file('file'));
    	return back()->with('message','Successfully Imported');
    }

    public function export_to_excel($page){
    	
    	if($page=='user_employee.index' || $page=='user_contractor.index' || $page=='user_client.index'){

    		if($page=='user_company.index'){
	    		$user_type = 'company';
	    	}
    		if($page=='user_employee.index'){
    			$user_type = 'employee';
	    	}
	    	if($page=='user_contractor.index'){
	    		$user_type = 'contractor';
	    	}
	    	if($page=='user_client.index'){
	    		$user_type = 'client';
	    	}
	    	$data = User::select(
								'users.name',
								'users.user_type',
								'users.email',
								'user_details.gender',
								'user_details.address',
								'user_details.contact',
								'user_details.date_of_birth',
								'user_details.hourly_rate',
								'user_details.annual_salary',
								'user_details.description',
								'user_details.employment_start_date')
						->join('user_details','user_details.user_id','=','users.id')
						->whereHas('roles', function ($query) use ($user_type) {
                                $query->where('name', '=', $user_type);
                             })
						->get();
    		$head = ['NAME','ROLE','EMAIL','GENDER','ADDRESS','CONTACT','DOB','HOURLY RATE','ANNUAL SALARY','DESCRIPTION','START DATE'];
    	}
    	elseif($page=='site.attendance'){
    		$user_type = 'Site Attendance Report';
    		$data = SiteAttendance::select('users.name',
    												   'buildings.name as building_name',
    												   'buildings.building_no',
    												   'rooms.name as room_name',
    												   'rooms.room_no',
    												   'rooms.description',
    												   'site_attendances.login',
	                                                   'site_attendances.created_at as date',)
                            ->join('rooms','rooms.id','=','site_attendances.room_id')
                            ->join('buildings','buildings.id','=','rooms.building_id')
                            ->join('users','users.id','=','site_attendances.user_id')
                            ->get();
            $head = ['NAME','BUILDING NAME','BUILDING No','DIVISION / AREA','ROOM No','DESCRIPTION','LOGIN TIME','DATE'];
    	}
    	return Excel::download(new DataExport($data,$head), $user_type.'s.xlsx');
    }
}
