<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\User;
use App\UserDetail;
use Excel;
use App\Exports\DataExport;
use Illuminate\Http\Request;

class CoreController extends Controller
{
    public function import_from_excel(Request $request){
    	return 'import excel';
    }

    public function export_to_excel($page){
    	
    	if($page=='user_employee.index' || $page=='user_contractor.index' || $page=='user_client.index'){

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
						->where('users.user_type','=',$user_type)->get();
    		$head = ['NAME','ROLE','EMAIL','GENDER','ADDRESS','CONTACT','DOB','HOURLY RATE','ANNUAL SALARY','DESCRIPTION','START DATE'];
    	}
    	return Excel::download(new DataExport($data,$head), 'Users.xlsx');
    }
}
