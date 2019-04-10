<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
use Session;

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
    public function index()
    {
        $userId   = Auth::id();
        $userType = Auth::user()->user_type;
        $addedBy  = Auth::user()->added_by;        
        $timezone = Auth::user()->detail->timezone;
        session(['timezone' => $timezone, 'user_id' => $userId, 'user_type' => $userType, 'added_by' => $addedBy]);
        return view('backend.pages.dashboard');
    }
}
