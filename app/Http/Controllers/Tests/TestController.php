<?php

namespace App\Http\Controllers\Tests;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestController extends Controller
{
	public function index()
	{
    	return view('test');
	}

	public function get(Request $request)
	{
		
    	return view('test');
	}
	
}
