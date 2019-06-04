<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
	use SoftDeletes;
	
    protected $fillable = ['user_id','leave_type','from','to','description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
