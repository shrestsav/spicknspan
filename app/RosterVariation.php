<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RosterVariation extends Model
{

   protected $fillable = [];

   public function approved_by()
   {
    return $this->belongsTo(User::class,'approved_id','id');
   }
}