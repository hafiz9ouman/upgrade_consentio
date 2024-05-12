<?php

namespace App\Exports;

use App\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Session;
use DB;

class AssetsExport implements FromCollection,WithHeadings
{
    private $client_id;

    public function __construct($client_id) 
    {
        $this->client_id = $client_id;

    }

    public function headings():array{
        if(session('locale') == 'fr'){
            return [
                "Élément d'audit", "Type d'actif", "Type d'hébergement", "Fournisseur d'hébergement", "Pays", "Ville", "État", "Impacte", "Classification des données", "Niveau", "Propriétaire informatique", "Propriétaire d'entreprise", "Unité commerciale", "Interne ou tiers", "Volume de données sensibles",
            ];
        }else{
            return [
                "Audit Item","Asset Type","Hosting Type","Hosting Provider","Country","City","State","Impact","Data Classiication","Asset Tier","Organization","IT Owner","Business Owner","Business Unit","Internal 3rd Party","Data Subject Volume",
            ];
        }
     }
    public function collection()
    {
        if(session('locale') == 'fr'){
            $check = DB::table('assets')
                ->join("data_classifications", "data_classifications.id", "assets.data_classification_id")
                ->join("impact",  "impact.id",  "assets.impact_id")
                ->join("users",  "users.id",  "assets.client_id")
                ->where("assets.client_id",$this->client_id)
                ->select("assets.name","assets.asset_type","assets.hosting_type","assets.hosting_provider","assets.country","assets.city","assets.state","impact.impact_name_fr","data_classifications.classification_name_fr","assets.tier","users.name as user_names","assets.it_owner","assets.business_owner","assets.business_unit","assets.internal_3rd_party","assets.data_subject_volume")
                ->orderBy("assets.id","ASC")
                ->get();
            foreach($check as $data){
                if(strtolower($data->asset_type) == 'server'){
                    $data->asset_type = "Serveur";
                }
                if(strtolower($data->asset_type) == 'database'){
                    $data->asset_type = "base de données";
                }
                if(strtolower($data->asset_type) == 'physical storage'){
                    $data->asset_type = "Stockage Physique";
                }
                if(strtolower($data->asset_type) == 'Other'){
                    $data->asset_type = "Autre";
                }
                if(strtolower($data->asset_type) == 'website'){
                    $data->asset_type = "Site Internet";
                }
        
                //check Hosting Type
                if(strtolower($data->hosting_type) == 'cloud'){
                    $data->hosting_type = "Nuage";
                }
                if(strtolower($data->hosting_type) == 'on-premise'){
                    $data->hosting_type = "Sur Site";
                }
                if(strtolower($data->hosting_type) == 'not sure'){
                    $data->hosting_type = "Pas Certain";
                }
                if(strtolower($data->hosting_type) == 'hybrid'){
                    $data->hosting_type = "Hybride";
                }
        
                //check Tier
                if(strtolower($data->tier) == 'crown jewels'){
                    $data->tier = "Les joyaux de la couronne";
                }
                if(strtolower($data->tier) == 'tier 1'){
                    $data->tier = "Niveau 1";
                }
                if(strtolower($data->tier) == 'tier 2'){
                    $data->tier = "Niveau 3";
                }
                if(strtolower($data->tier) == 'tier 3'){
                    $data->tier = "Niveau 3";
                }
        
                //check internal or 3rd party
                if(strtolower($data->internal_3rd_party) == 'internal'){
                    $data->internal_3rd_party = "Interne";
                }
                if(strtolower($data->internal_3rd_party) == '3rd party'){
                    $data->internal_3rd_party = "3ème Partie";
                }
            }
        }else{
            $check = DB::table('assets')
                ->join("data_classifications", "data_classifications.id", "assets.data_classification_id")
                ->join("impact",  "impact.id",  "assets.impact_id")
                ->join("users",  "users.id",  "assets.client_id")
                ->where("assets.client_id",$this->client_id)
                ->select("assets.name","assets.asset_type","assets.hosting_type","assets.hosting_provider","assets.country","assets.city","assets.state","impact.impact_name_en","data_classifications.classification_name_en","assets.tier","users.name as user_names","assets.it_owner","assets.business_owner","assets.business_unit","assets.internal_3rd_party","assets.data_subject_volume")
                ->orderBy("assets.id","ASC")
                ->get();
        }
        // dd($check);
        return $check;
    }
}