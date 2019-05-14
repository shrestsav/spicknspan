<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncidentReport extends Model
{
    protected $fillable= ['user_id','type','person_involved','occupation','employer_id','contact','location','date','medical_treatment','cease_work','attended_authorities','desc_what','desc_how','desc_why','desc_immediate_actions','desc_relevant_controls'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employer()
    {
        return $this->belongsTo(User::class,'employer_id','id');
    }

}
