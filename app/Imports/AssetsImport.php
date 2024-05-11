<?php
namespace App\Imports;
use App\Asset;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpClient\HttpClient;
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

        // Check if it's the first row
        static $firstRow = true;
        if ($firstRow) {
            $firstRow = false;
            return null;
        }
        //dd($row[7]);
        $client_id = Auth::user()->client_id;
        //dd($client_id);

        //check Asset Type
        if(strtolower($row[1]) == 'serveur'){
            $row[1] = "Server";
        }
        if(strtolower($row[1]) == 'base de données'){
            $row[1] = "Database";
        }
        if(strtolower($row[1]) == 'stockage physique'){
            $row[1] = "Physical Storage";
        }
        if(strtolower($row[1]) == 'autre'){
            $row[1] = "Other";
        }
        if(strtolower($row[1]) == 'site internet'){
            $row[1] = "Website";
        }

        //check Hosting Type
        if(strtolower($row[2]) == 'nuage'){
            $row[2] = "Cloud";
        }
        if(strtolower($row[2]) == 'sur site'){
            $row[2] = "On-Premise";
        }
        if(strtolower($row[2]) == 'pas certain'){
            $row[2] = "Not Sure";
        }
        if(strtolower($row[2]) == 'hybride'){
            $row[2] = "Hybrid";
        }

        //check Tier
        if(strtolower($row[9]) == 'les joyaux de la couronne'){
            $row[9] = "Crown Jewels";
        }
        if(strtolower($row[9]) == 'niveau 1'){
            $row[9] = "tier 1";
        }
        if(strtolower($row[9]) == 'niveau 2'){
            $row[9] = "tier 3";
        }
        if(strtolower($row[9]) == 'niveau 3'){
            $row[9] = "tier 3";
        }

        //check internal or 3rd party
        if(strtolower($row[13]) == 'interne'){
            $row[13] = "internal";
        }
        if(strtolower($row[13]) == '3ème partie'){
            $row[13] = "3rd Party";
        }
        
        $data = ucwords($row[7]);
        $impact= DB::table('impact')->where('impact_name_en', $data)->get();
        if(count($impact) == 0 ){
            $impact= DB::table('impact')->where('impact_name_fr', $data)->get();
        }
        // dd($impact);
        $row[7]= $impact;

        $var = ucfirst($row[8]);
        $data_class= DB::table('data_classifications')->where('classification_name_en', $var)->where('organization_id', $client_id)->get();
        if(count($data_class) == 0 ){
            $data_class= DB::table('data_classifications')->where('classification_name_fr', $var)->where('organization_id', $client_id)->get();
        }
        // dd($data_class);
        $row[8]= $data_class;

        $address = urlencode($row[4] . ' ' . $row[5]); // Concatenate city and country for the address

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
            'query' => [
                'address' => $address,
                'key' => 'AIzaSyDaCml5EZAy3vVRySTNP7_GophMR8Niqmg', // Replace with your API key
            ]
        ]);
        // dd($response);

        if ($response->getStatusCode() === 200) {
            $content = $response->toArray(); // Convert response to array
            // dd($content);

            if (isset($content['results'][0]['geometry']['location'])) {
                $latitude = $content['results'][0]['geometry']['location']['lat'];
                $longitude = $content['results'][0]['geometry']['location']['lng'];

                // Now you can use $latitude and $longitude as needed
            }

        }

        
        $data1=1;
        
        if (DB::table('assets')->where('client_id', $client_id)->orderby('asset_number', 'DESC')->count() > 0) {
            //dd('ok');
            $latest_assigned_number =  DB::table('assets')->where('client_id', $client_id)->orderby('asset_number', 'DESC')->first();
            // dd($latest_assigned_number);
            $asset_number= $latest_assigned_number->asset_number+1;
            // dd($row[16]);
            // dd($row[16][0]->asset_number);

            return new Asset([
                "name" => $row[0],
                "asset_type" => $row[1],
                "hosting_type" => $row[2],
                "hosting_provider" => $row[3],
                "country" => $row[4],
                "city" => $row[5],
                "state" => $row[6],
                "impact_id" => $row[7][0]->id,
                "data_classification_id" => $row[8][0]->id,
                "tier" => $row[9],
                "it_owner" => $row[10],
                "business_owner" => $row[11],
                "business_unit" => $row[12],
                "internal_3rd_party" => $row[13],
                "data_subject_volume" => $row[14],
                "asset_number" => $asset_number,
                "client_id"=> $client_id,
                "lat" => $latitude,
                "lng" => $longitude,
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
                "impact_id" => $row[7][0]->id,
                "data_classification_id" => $row[8][0]->id,
                "tier" => $row[9],
                "it_owner" => $row[10],
                "business_owner" => $row[11],
                "business_unit" => $row[12],
                "internal_3rd_party" => $row[13],
                "data_subject_volume" => $row[14],
                "client_id"=> $client_id,
                "asset_number" => $data1,
                "lat" => $latitude,
                "lng" => $longitude,
            ]);

        }
    }
}
