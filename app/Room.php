<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['building_id','room_no','name','description','image','question_id','added_by'];

    public function site_attendance()
    {
        return $this->hasMany(SiteAttendance::class);
    }
}
