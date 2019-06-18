<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Wages;
use App\Attendance;
use Carbon\Carbon;

class ReportController extends Controller
{
	/**
     * @var User
     */

    private $user;

	public function __construct(User $user){
        $this->user = $user;
    }

    public function index(){
        return view('backend.reports.index');
    }

    public function wagesFilterItems(Request $request){
        $employees = $this->user->employeeList();
        $clients = $this->user->clientList();
        return compact('employees','clients');
    }

    public function wagesReport(Request $request){
        $from = date('Y-m-d', strtotime('-2 days', strtotime($request->range[0])));
        $to = date('Y-m-d', strtotime('+2 days', strtotime($request->range[1])));
        $actual_from = Carbon::parse($request->range[0])->format('Y-m-d');
        $actual_to = Carbon::parse($request->range[1])->format('Y-m-d');

        $attendances = Attendance::select('attendances.client_id',
                                          'attendances.employee_id',
                                          'attendances.check_in',
                                          'attendances.check_out',
                                          'client.name as client_name',
                                          'employee.name as employee_name',
                                          'wages.hourly_rate as wage',)
                                ->join('users as employee','attendances.employee_id','=','employee.id')
                                ->join('users as client','attendances.client_id','=','client.id')
                                ->leftJoin("wages",function($join){
                                    $join->on("wages.employee_id","=","attendances.employee_id")
                                        ->on("wages.client_id","=","attendances.client_id");
                                })
                                ->where('attendances.client_id',$request->client_id)
                                ->where('attendances.employee_id',$request->employee_id)
                                ->whereDate('attendances.check_in','>=',$from)
                                ->whereDate('attendances.check_in','<=',$to)
                                ->orderBy('attendances.check_in', 'desc')
                                ->get()
                                ->toArray();
        $attendances = collect($attendances);
        $attendances = $attendances->where('local_check_in.date','>=',$actual_from)->where('local_check_in.date','<=',$actual_to);

        $grouped_attendances = $attendances->groupBy(['local_check_in.date','employee_id'])->toArray();  
        // return $grouped_attendances;
        $datas = [];
        foreach($grouped_attendances as $date => $grouped_attendance){
            foreach($grouped_attendance as $id => $details){
                $totalHour = 0;
                foreach($details as $detail){
                  if($detail['check_in'] && $detail['check_out']){
                    $startTime = \Carbon\Carbon::parse($detail['check_in']);
                    $endTime = \Carbon\Carbon::parse($detail['check_out']);
                    $totalDuration = $endTime->diffInSeconds ($startTime);
                    $totalHour += $totalDuration/(60*60);
                  }
                }
                
                //Create new sets of data array
                $datas[] = [
                    'date' => $date,
                    'id' => $id,
                    'client_name' => $details[0]['client_name'],
                    'employee_name' => $details[0]['employee_name'],
                    'attended_hours' => $totalHour,
                    'wage' => $details[0]['wage'],
                    'totalwage' => $totalHour*$details[0]['wage'],
                ];

            }

        }

        return $datas;
    }

}
