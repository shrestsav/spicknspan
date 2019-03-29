<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RosterTimetable extends Model
{
	protected $table = "rosters_timetable";
    protected $fillable = ['rosters_id', 'full_date', 'start_time', 'end_time','total_hours'];
}