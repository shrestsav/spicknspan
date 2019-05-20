<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $fillable = ['user_id','leave_type','from','to','description'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
