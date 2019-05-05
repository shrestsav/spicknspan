<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsDate;

class Attendance extends Model
{
	use FormatsDate;
    protected $fillable = ['client_id', 'employee_id', 'check_in', 'check_in_location','check_in_image','check_out', 'diff_hour'];
    protected $dates = ['created_at','check_in','check_out'];
}
