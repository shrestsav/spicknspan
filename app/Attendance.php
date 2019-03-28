<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['client_id', 'employee_id', 'check_in', 'check_out', 'diff_hour'];
}
