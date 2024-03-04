<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $table = "audit_questions_groups";
    public function sections(){

        return $this->hasMany(GroupSection::class, "group_id");
    }
}
