<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
   protected $fillable = ['employee_id', 'client_id', 'full_date', 'start_time', 'end_time','total_hours'];
}