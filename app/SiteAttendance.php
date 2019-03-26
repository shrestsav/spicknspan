<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteAttendance extends Model
{
    protected $fillable = ['user_id','room_id','login'];
}
