<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionTemplate extends Model
{
   protected $table = "question_template";
   protected $fillable = ['template_title', 'name'];
}
