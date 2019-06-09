<?php

namespace App\Http\Controllers;

use App\Attendance;
use App\User;
use App\UserDetail;
use App\SiteAttendance;
use App\Roster;
use App\Wages;
use Excel;
use App\Exports\DataExport;
use App\Imports\DataImport;
use Illuminate\Http\Request;
use Auth;
use Entrust;
use Carbon\Carbon;

use Illuminate\Support\Facades\Validator;

class CoreController extends Controller
{
  //Method for Importing Excel Files
  public function import_from_excel(Request $request){
  	$request->validate([
      'file' => 'required|mimes:xlsx'
    ]);
  	$user_type = $request->user_type;
  	Excel::import(new DataImport($user_type),request()->file('file'));
  	return back()->with('message','Successfully Imported');
  }

  //Method for Exporting Excel Files
  public function export_to_excel(Request $request){

  	$type = $request->type;

  	if($type=='employee' || $type=='contractor' || $type=='client'){

      $data = User::select('users.name',
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
                        ->whereHas('roles', function ($query) use ($type) {
                            $query->where('name', '=', $type);
                          })
                        ->get();
      $head = ['NAME','ROLE','EMAIL','GENDER','ADDRESS','CONTACT','DOB','HOURLY RATE','ANNUAL SALARY','DESCRIPTION','START DATE'];
    }
    elseif($type=='site_attendance'){
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
    elseif($type=='wages'){
      $data = Wages::select('employee.name as employee_name',
                            'client.name as client_name',
                            'wages.hourly_rate')
                          ->join('users as employee','employee.id','wages.employee_id')
                          ->join('users as client','client.id','wages.client_id');
      if(Entrust::hasRole('contractor')){
        $wages->where('wages.added_by','=',Auth::id());
      }
      $data = $data->get();
      $head = ['Employee','Client','Hourly Rate ($)'];
    }
    elseif($type=='roster'){
      $data = Roster::select('id','full_date','client_id','employee_id')
                    ->with('timetable','employee','client')
                    ->where('full_date',$request->year_month)
                    ->orderBy('created_at','desc');
      $year_month = explode('-', $request->year_month);
      $all_days = $this->dates_month($year_month[1],$year_month[0]);

      if($request->employee_id)
        $data->where('employee_id',$request->employee_id);
      if($request->client_id)
        $data->where('client_id',$request->client_id);
      
      $data = $data->get();

      foreach($data as $d){
        $d['employee_name'] = $d->employee->name;
        $d['client_name'] = $d->client->name;
        foreach ($all_days as $key => $date) {
          $d[$date] = '';
        }
        foreach ($d->timetable as $t) {
          $day = Carbon::parse($t->date)->format('j');
          $s_time = Carbon::parse($t->start_time)->format('h:i A');
          $e_time = Carbon::parse($t->end_time)->format('h:i A');
          $d[$all_days[$day]]=$s_time.' - '.$e_time;
        }

        unset($d['client_id'],
              $d['timetable'],
              $d['employee_id'],
              $d['employee'],
              $d['client'],
              $d['id']);
        $head = ['Month','Employee Name','Client Name'];
        foreach ($all_days as $key => $date) {
          $head[] = $date;
        }
      }
    }

    if(!count($data)){
      return back()->with('error','Table Seems to be Empty');
    }

    return Excel::download(new DataExport($data,$head), $type.'.xlsx');
  }

  public function sysIndex(Request $request){
    return 'Settings';
    return view('backend.pages.sys_settings');
  }


  /**
   * Returns all days of perticular month
   */
  public function dates_month($month, $year)
  {
      $num = cal_days_in_month(CAL_GREGORIAN, $month, $year);
      $dates_month = array();

      for ($i = 1; $i <= $num; $i++) {
          $mktime = mktime(0, 0, 0, $month, $i, $year);
          $date = date("D-M-d", $mktime);
          $dates_month[$i] = $date;
      }

      return $dates_month;
  }


}
