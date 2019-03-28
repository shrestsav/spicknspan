<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wages extends Model
{
   protected $fillable = ['employee_id', 'client_id', 'hourly_rate'];
}
