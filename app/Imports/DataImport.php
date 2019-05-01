<?php

namespace App\Imports;

use App\User;
use App\UserDetail;
use App\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Collection;
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
        foreach ($rows as $row) 
        {
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
                'date_of_birth'         =>  $row['dob'],
                'contact'               =>  $row['contact'],
                'hourly_rate'           =>  $row['hourly_rate'],
                'annual_salary'         =>  $row['annual_salary'],
                'description'           =>  $row['description'],
                'employment_start_date' =>  $row['start_date'],
            ]);
        }
    }
}
