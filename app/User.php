<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Entrust;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    //Traits method haru collide bhayera rakheko,,, User::find($id)->delete() garyo bhaney role delete hunxa,, so use User::where('id',$id)->delete() instead,, that way softdelete matra hunxa
    use SoftDeletes, EntrustUserTrait {

        SoftDeletes::restore insteadof EntrustUserTrait;
        EntrustUserTrait::restore insteadof SoftDeletes;

    }
    // use EntrustUserTrait;
    // use SoftDeletes; 

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password','user_type','mark_default','added_by','client_ids','timezone','avatar','g_id','f_id','access_token'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','provider_name', 'provider_id', 'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }

    public function site_attendance()
    {
        return $this->hasMany(SiteAttendance::class);
    }

    public function incident_reports()
    {
        return $this->hasMany(IncidentReport::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class,'added_by','id');
    }

    public function clients()
    {
        $client_ids = json_decode($this->client_ids);
        $clients =  User::whereIn('id',$client_ids)->get();
        return $clients;
    }

    public function employeeList()
    {
        $employees = $this->whereHas('roles', function ($query) {
                                $query->where('name', '=', 'employee')
                                      ->orWhere('name', '=', 'superAdmin')
                                      ->orWhere('name', '=', 'contractor');
                            });

        if(Entrust::hasRole(['employee'])){
          $employees->where('id','=',Auth::id());
        }
        elseif(Entrust::hasRole(['contractor'])){
          $employees->where('added_by','=',Auth::id());
        }
        return $employees->get();
    }

    public function clientList()
    {
        $clients = $this->whereHas('roles', function ($query) {
                          $query->where('name', '=', 'client');
                       });

        if(Entrust::hasRole(['contractor'])){
            $clients->where('added_by','=',Auth::id());
        }
        elseif(Entrust::hasRole(['employee'])){
            $client_ids  = json_decode(Auth::user()->client_ids);
            if($client_ids)
                $clients->whereIn('id',$client_ids);
        }

        return $clients->get();
    }
}
