<?php

use Illuminate\Database\Seeder;
use App\User;
use App\UserDetail;
use App\Role;
use App\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        DB::table('users')->delete();

        //1) Create Admin Role
        $role = ['name' => 'superAdmin', 'display_name' => 'Super Admin', 'description' => 'Full Permission'];
        $role = Role::create($role);

        //2) Set Role Permissions
        // Get all permission, swift through and attach them to the role
        $permission = Permission::get();
        foreach ($permission as $key => $value) {
            $role->attachPermission($value);
        }

        //3) Create Admin User
        $user = ['name' => 'Admin User', 
                 'email' => 'superadmin@admin.com', 
                 'password' => Hash::make('admin12345'),
                 'added_by' => '1',
                 'inspection' => '1'];
        $user = User::create($user);
        
        //4) Set User Role
        $user->attachRole($role);

        //5) Create Admin UserDetail
        $user_details = ['user_id' => $user->id, 'gender' => 'male', 'contact' => '9800'];
        $user_details = UserDetail::create($user_details);
        
        // 6) Create Other Roles
        $other_roles = [
                            [
                                'name' => 'employee', 
                                'display_name' => 'Employee', 
                                'description' => 'Roles for Employees'
                            ],
                            [
                                'name' => 'client',
                                'display_name' => 'Client',
                                'description' => 'Roles for Clients'
                            ],
                            [
                                'name' => 'contractor',
                                'display_name' => 'Contractor',
                                'description' => 'Roles for Contractors'
                            ],
                        ];

        foreach ($other_roles as $key => $value) {
            Role::create($value);
        }
    }
}
