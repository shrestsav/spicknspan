<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupportMail extends Model
{
    protected $fillable = ['name', 'email', 'contact', 'subject','message'];
}
