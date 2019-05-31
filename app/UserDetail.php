<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
   	protected $fillable = ['user_id','photo','address','gender','contact','hourly_rate','annual_salary','description','date_of_birth','employment_start_date','documents'];

   	public function user()
    {
        return $this->belongsTo(User::class);
    }
}
