<?php

namespace App\Imports;

use App\User;
use App\UserDetail;
use App\Role;
use App\Wages;
use App\Roster;
use App\RosterTimetable;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Exception;

class DataImport implements ToCollection, WithHeadingRow
{
    private $data;

    public function __construct($import_type,$data)
    {
        $this->data = $data;
        $this->import_type = $import_type;
    }
    public function collection(Collection $rows)
    {
        // if($rows->filter()->isNotEmpty()){
        //     Validator::make($rows->toArray(), [
        //         '*.name' => ['required', 'string', 'max:255'],
        //         '*.email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //         '*.password' => ['required', 'string', 'min:6'],
        //         '*.gender' => ['required', 'string'],
        //         '*.contact' => ['required'],
        //      ])->validate();
        // }
        if($this->import_type == 'users'){
            foreach ($rows as $row) 
            {
                if($row->filter()->isNotEmpty()){

                    $rules = [
                            'name' => ['required', 'string', 'max:255'],
                            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                            'password' => ['required', 'string', 'min:6'],
                            'gender' => ['required', 'string'],
                            'contact' => ['required'],
                            ];

                    $customMessages = [
                            'email.unique' => $row['email'].':  This :attribute already exists.',
                            'password.min' => $row['email'].':  Password must be minimum of 6 characters.',
                            'required' => $row['email'].':  :attribute cannot be left empty.',
                            
                        ];

                    $validator = Validator::make($row->toArray(), $rules, $customMessages);
                    $validator->validate();

                    $user = User::create([
                        'name'     => $row['name'],
                        'email'    => $row['email'], 
                        'user_type'    => $this->data['user_type'], 
                        'password' => \Hash::make($row['password']),
                    ]);

                    $roles = Role::pluck('name','id');

                    foreach($roles as $role_id => $role){
                      if($role==$this->data['user_type']){
                        $user->attachRole($role_id);
                      }
                    }
                    
                    UserDetail::create([
                        'user_id'               =>  $user->id,
                        'address'               =>  $row['address'],
                        'gender'                =>  $row['gender'],
                        'date_of_birth'         =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['dob']),
                        'contact'               =>  $row['contact'],
                        'hourly_rate'           =>  $row['hourly_rate'],
                        'annual_salary'         =>  $row['annual_salary'],
                        'description'           =>  $row['description'],
                        'employment_start_date' =>  \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['start_date']),
                    ]);
                }
            }
        }
        if($this->import_type == 'wages'){
            foreach ($rows as $row) 
            {
                if($row->filter()->isNotEmpty()){
                    $rules = [
                        'employee_email' => ['required', 'string', 'email', 'max:255','exists:users,email'],
                        'client_email' => ['required', 'string', 'email', 'max:255','exists:users,email'],
                        'base_hourly_rate' => ['required', 'numeric'],
                    ];

                    $customMessages = [
                        'employee_email.exists' => $row['employee_email'].':  Staff :attribute doesnot exist in our system.',
                        'client_email.exists' => $row['client_email'].':  Client :attribute doesnot exist in our system.',
                    ];
                    $validator = Validator::make($row->toArray(), $rules, $customMessages);
                    // $validator->validate();
                    if ($validator->fails()) {
                        $errors = collect($validator->errors());
                        throw new exception($errors);
                    }
                    
                    $get_client = User::where('email',$row['client_email'])->first();
                    $get_employee = User::where('email',$row['employee_email'])->first();

                    if($get_client->hasRole('client')){
                        $get_employee_id = $get_employee->id;
                        $get_client_id = $get_client->id;

                        $check_if_exists = Wages::where('employee_id',$get_employee_id)->where('client_id',$get_client_id)->exists();
                        if(!$check_if_exists){
                            $wages = Wages::create([
                                'employee_id' => $get_employee_id,
                                'client_id'   => $get_client_id, 
                                'hourly_rate' => $row['base_hourly_rate'], 
                                'added_by'    => $this->data['added_by'],
                            ]);
                        }
                        else{
                            $error = ['Records exists for given Employee '.$row['employee_email'].' and client '.$row['client_email']];
                            throw new exception(json_encode($error));
                        }
                    }
                    else{
                        $error = [$row['client_email'].' is not enlisted as an employee for this system'];
                        throw new exception(json_encode($error));
                    }
                }
            }
        }
        if($this->import_type == 'rosters'){
            foreach ($rows as $row) 
            {
                if($row->filter()->isNotEmpty()){
                    $rules = [
                        'employee_email' => ['required', 'string', 'email', 'max:255','exists:users,email'],
                        'client_email' => ['required', 'string', 'email', 'max:255','exists:users,email'],
                        'month' => ['required'],
                    ];

                    $customMessages = [
                        'employee_email.exists' => $row['employee_email'].':  :attribute doesnot exist in our system.',
                        'client_email.exists' => $row['client_email'].':  :attribute doesnot exist in our system.',
                    ];
                    $validator = Validator::make($row->toArray(), $rules, $customMessages);

                    if ($validator->fails()) {
                        $errors = collect($validator->errors());
                        throw new exception($errors);
                    }
                    $get_client = User::where('email',$row['client_email'])->first();
                    $get_employee = User::where('email',$row['employee_email'])->first();

                    if($get_client->hasRole('client')){
                        $get_employee_id = $get_employee->id;
                        $get_client_id = $get_client->id;

                        $check_if_exists = Roster::where('employee_id',$get_employee_id)->where('client_id',$get_client_id)->where('full_date',$row['month']);
                        if(!$check_if_exists->exists()){
                            $roster = Roster::create([
                                'employee_id' => $get_employee_id,
                                'client_id'   => $get_client_id,
                                'full_date'   => $row['month'], 
                                'added_by'    => $this->data['added_by'], 
                            ]);
                        }
                        else{
                            $roster = $check_if_exists->first();
                            // $error = ['Records exists for given Employee '.$row['employee_email'].' and client '.$row['client_email']];
                            // throw new exception(json_encode($error));
                        }

                        //Populate TimeTable 
                        $year_month = explode('-', $row['month']);
                        for($i=1; $i<=32; $i++){
                            $range = trim($row[$i]," ");
                            if($range!='' && $range!=null){
                                $timeRange = explode('-', $range);
                                
                                $timetable = RosterTimetable::updateOrCreate(
                                    [
                                        'roster_id' => $roster->id, 
                                        'date' => $year_month[0].'-'.$year_month[1].'-'.$i,
                                    ],
                                    [
                                        'roster_id' => $roster->id,
                                        'date' => '2019-'.$year_month[1].'-'.$i,
                                        'start_time' => $timeRange[0], 
                                        'end_time' => $timeRange[1], 
                                    ]
                                );
                            }
                            // $timetable = RosterTimetable::create([
                            //     'roster_id' => $roster->id,
                            //     'date' => '2019-'.$year_month[1].'-'.$i,
                            //     'start_time' => $timeRange[0], 
                            //     'end_time' => $timeRange[1], 
                            // ]);
                        }

                    }
                    else{
                        $error = [$row['client_email'].' is not enlisted as an employee for this system'];
                        throw new exception(json_encode($error));
                    }
                }
            }
        }
    }
}
