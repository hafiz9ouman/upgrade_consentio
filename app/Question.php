<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = "group_questions";
    public function responses(){
        return $this->hasOne(UserResponse::class, 'question_id');
    }
}
