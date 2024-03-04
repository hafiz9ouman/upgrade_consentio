<?php

namespace App\Exports;

use App\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class AssetsExport implements FromCollection,WithHeadings
{
    private $client_id;

    public function __construct($client_id) 
    {
        $this->client_id = $client_id;

    }

    public function headings():array{
        return [
            "Name","Asset Type","Hosting Type","Hosting Provider","Country","City","State","Longitude","Latitude","Impact","Data Classiication","Tier","Organization","It Owner","Business Owner","Business Unit","Internal 3rd Party","Data Subject Volume",];

     }
    public function collection()
    {
        $check = DB::table('assets')
                ->join("data_classifications", "data_classifications.id", "assets.data_classification_id")
                ->join("impact",  "impact.id",  "assets.impact_id")
                ->join("users",  "users.id",  "assets.client_id")
                ->where("assets.client_id",$this->client_id)
                ->select("assets.name","assets.asset_type","assets.hosting_type","assets.hosting_provider","assets.country","assets.city","assets.state","assets.lng","assets.lat","impact.impact_name_en","data_classifications.classification_name_en","assets.tier","users.name as user_names","assets.it_owner","assets.business_owner","assets.business_unit","assets.internal_3rd_party","assets.data_subject_volume")
                ->orderBy("assets.id","ASC")
                ->get();
        return $check;
    }
}