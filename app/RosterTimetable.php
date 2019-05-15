<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RosterTimetable extends Model
{
    protected $fillable = ['roster_id', 'full_date', 'start_time', 'end_time'];
}