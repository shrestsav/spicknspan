<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        //Check for Admin
        //Return true if auth user type is admin
        $gate->define('isAdmin',function($user){
            return $user->user_type == 'admin';
        });

        //Check for Employee
        //Return true if auth user type is employee
        $gate->define('isEmployee',function($user){
            return $user->user_type == 'employee';
        });

        //Check for Contractor
        //Return true if auth user type is contractor
        $gate->define('isContractor',function($user){
            return $user->user_type == 'contractor';
        });

        //Check for Client
        //Return true if auth user type is client
        $gate->define('isClient',function($user){
            return $user->user_type == 'client';
        });
    }
}
