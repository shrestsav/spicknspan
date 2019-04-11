<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permission = [
            [
                'name' => 'check_in_out',
                'display_name' => 'Check In Out',
                'description' => 'Check In and Out Module for Employee'
            ],
            [
                'name' => 'qr_login',
                'display_name' => 'QR Login to Rooms',
                'description' => 'Login to Building Rooms using QR Scanner'
            ],
            [
                'name' => 'view_site_attendance',
                'display_name' => 'View Site Attendance',
                'description' => 'View Records of Site Attendance by Employees'
            ],
            [
                'name' => 'delete_site_attendance',
                'display_name' => 'Delete Site Attendance',
                'description' => 'Delete Site Attendance Records of Employees'
            ],
            [
                'name' => 'create_employee',
                'display_name' => 'Create Employee',
                'description' => 'Create New Employee'
            ],
            [
                'name' => 'edit_employee',
                'display_name' => 'Edit Employee',
                'description' => 'Edit Employee Records'
            ],
            [
                'name' => 'delete_employee',
                'display_name' => 'Delete Employee Records',
                'description' => 'Delete Employee Records'
            ],
            [
                'name' => 'view_employee',
                'display_name' => 'View Employee Records',
                'description' => 'View all Employee Records'
            ],
            [
                'name' => 'create_client',
                'display_name' => 'Create Client',
                'description' => 'Create New Client'
            ],
            [
                'name' => 'edit_client',
                'display_name' => 'Edit Client',
                'description' => 'Edit Client Records'
            ],
            [
                'name' => 'delete_client',
                'display_name' => 'Delete Client Records',
                'description' => 'Delete Client Records'
            ],
            [
                'name' => 'view_client',
                'display_name' => 'View Client Records',
                'description' => 'View all Client Records'
            ],
            [
                'name' => 'create_contractor',
                'display_name' => 'Create Contractor',
                'description' => 'Create New Contractor'
            ],
            [
                'name' => 'edit_contractor',
                'display_name' => 'Edit Contractor',
                'description' => 'Edit Contractor Records'
            ],
            [
                'name' => 'delete_contractor',
                'display_name' => 'Delete Contractor Records',
                'description' => 'Delete Contractor Records'
            ],
            [
                'name' => 'view_contractor',
                'display_name' => 'View Contractor Records',
                'description' => 'View all Contractor Records'
            ],
            [
                'name' => 'create_site',
                'display_name' => 'Create Site',
                'description' => 'Create New Site'
            ],
            [
                'name' => 'edit_site',
                'display_name' => 'Edit Site',
                'description' => 'Edit Site Records'
            ],
            [
                'name' => 'delete_site',
                'display_name' => 'Delete Site Records',
                'description' => 'Delete Site Records'
            ],
            [
                'name' => 'view_site',
                'display_name' => 'View Site Records',
                'description' => 'View all Site Records'
            ]
        ];
        foreach ($permission as $key => $value) {
            Permission::create($value);
        }
    }
}
