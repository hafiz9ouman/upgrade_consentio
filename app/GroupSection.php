<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupSection extends Model{

    protected $table = "group_section";
    public function questions(){

        return $this->hasMany(Question::class, 'section_id');
        
    }
}
