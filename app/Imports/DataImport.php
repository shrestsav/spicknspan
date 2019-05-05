<?php

namespace App\Imports;

use App\User;
use App\UserDetail;
use App\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;

class DataImport implements ToCollection, WithHeadingRow
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
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
                    'user_type'    => $this->data, 
                    'password' => \Hash::make($row['password']),
                ]);

                $roles = Role::pluck('name','id');

                foreach($roles as $role_id => $role){
                  if($role==$this->data){
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
}
