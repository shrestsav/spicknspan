<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

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
}
