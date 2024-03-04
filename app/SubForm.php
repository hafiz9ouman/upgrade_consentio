<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubForm extends Model
{
    
    protected $table="sub_forms";

    public function form()
    {
        return $this->belongsTo(Form::class, 'parent_form_id');
    }

    // public function user_form_link()
    // {
    //     return $this->hasMany(UserFormLink::class, 'sub_form_id');
    // }
}
