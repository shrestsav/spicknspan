<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roster extends Model
{
   	protected $fillable = ['employee_id', 'client_id', 'full_date','added_by'];

   	public function employee()
    {
        return $this->belongsTo(User::class,'employee_id','id');
    }

   	public function client()
    {
        return $this->belongsTo(User::class,'client_id','id');
    }
}