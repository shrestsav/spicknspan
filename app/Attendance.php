<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['client_id', 'employee_id', 'check_in', 'check_in_location','check_in_image','check_out', 'diff_hour'];
}
