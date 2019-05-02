<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteAttendance extends Model
{
    protected $fillable = ['user_id','room_id','login'];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function room()
    {
    	return $this->belongsTo(Room::class);
    }
}
