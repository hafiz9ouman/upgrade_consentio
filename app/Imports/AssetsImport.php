<?php
namespace App\Imports;
use App\Asset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class AssetsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        //dd($row[9]);
       $client_id =  DB::table('users')->where('id', Auth::user()->client_id)->select('id')->get()->toArray();
       $row[12]= $client_id;
       //dd($row[12]);
        
        $data = ucwords($row[9]);
        $impact= DB::table('impact')->where('impact_name_en', $data)->get();
        //dd($impact);
        $row[9]= $impact;

        $var = ucfirst($row[10]);
        $data_class= DB::table('data_classifications')->where('classification_name_en', $var)->where('organization_id', $row[12][0]->id)->get();
        //dd($data_class);
        $row[10]= $data_class;

        
        $data1=1;
        
        if (DB::table('assets')->where('client_id', $row[12][0]->id)->orderby('asset_number', 'DESC')->count() > 0) {
            //dd('ok');
            $latest_assigned_number =  DB::table('assets')->where('client_id', $row[12][0]->id)->orderby('asset_number', 'DESC')->get();
            //dd($latest_assigned_number);
            $row[18]= $latest_assigned_number;
            // dd($row[18]);
            // dd($row[18][0]->asset_number);

            return new Asset([
                "name" => $row[0],
                "asset_type" => $row[1],
                "hosting_type" => $row[2],
                "hosting_provider" => $row[3],
                "country" => $row[4],
                "city" => $row[5],
                "state" => $row[6],
                "lng" => $row[7],
                "lat" => $row[8],
                "impact_id" => $row[9][0]->id,
                "data_classification_id" => $row[10][0]->id,
                "tier" => $row[11],
                "client_id"=> $row[12][0]->id,
                "it_owner" => $row[13],
                "business_owner" => $row[14],
                "business_unit" => $row[15],
                "internal_3rd_party" => $row[16],
                "data_subject_volume" => $row[17],     
                "asset_number" => $row[18][0]->asset_number+1,     
            ]);
        }
        else{
            return new Asset([
                "name" => $row[0],
                "asset_type" => $row[1],
                "hosting_type" => $row[2],
                "hosting_provider" => $row[3],
                "country" => $row[4],
                "city" => $row[5],
                "state" => $row[6],
                "lng" => $row[7],
                "lat" => $row[8],
                "impact_id" => $row[9][0]->id,
                "data_classification_id" => $row[10][0]->id,
                "tier" => $row[11],
                "client_id"=> $row[12][0]->id,
                "it_owner" => $row[13],
                "business_owner" => $row[14],
                "business_unit" => $row[15],
                "internal_3rd_party" => $row[16],
                "data_subject_volume" => $row[17],     
                "asset_number" => $data1,     
            ]);

        }
    }
}
