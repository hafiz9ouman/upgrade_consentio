<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public $timestamps = false;
    protected $fillable = ["name","asset_type","asset_number","hosting_type","hosting_provider","country","city","state","lng","lat","impact_id","data_classification_id","tier","client_id","it_owner","business_owner","business_unit","internal_3rd_party","data_subject_volume"];
}
