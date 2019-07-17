<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Building extends Model
{
	use SoftDeletes;
	
    protected $fillable = ['name','building_no','address','description','image','gps_coordinates','added_by'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
