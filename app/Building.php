<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $fillable = ['name','building_no','address','description','image','gps_coordinates'];
}
