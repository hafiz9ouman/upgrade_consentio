<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserResponse extends Model{
    protected $table = "user_responses";

    public function ratings(){
        return $this->belongsTo(EvaluationRating::class, 'rating');
    }
}
