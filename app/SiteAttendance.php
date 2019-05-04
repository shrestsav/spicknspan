<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasLocalDates;
use App\Traits\FormatsDate;

class SiteAttendance extends Model
{
	// use HasLocalDates;
	use FormatsDate;
    protected $dates = ['login'];
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
