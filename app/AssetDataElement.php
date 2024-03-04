<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AssetDataElement extends Model
{
    protected $table = "assets_data_elements";
    public $timestamps = false;
    protected $fillable = ["name","section_id", "owner_id","d_classification_id"];
}
