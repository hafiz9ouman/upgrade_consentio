<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    
    protected $table = "forms";

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
