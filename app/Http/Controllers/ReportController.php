<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class ReportController extends Controller
{
	/**
     * @var User
     */
    private $user;

	public function __construct(User $user){
        $this->user = $user;
    }

    public function wagesReport(Request $request){
    	$employees = $this->user->employeeList();
    	$clients = $this->user->clientList();
    	return view('backend.reports.wages',compact('employees','clients'));
    }
}
