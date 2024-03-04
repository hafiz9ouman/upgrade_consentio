<?php

namespace App\Imports;

use Exception;
use App\AssetDataElement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class ImportAssetDataElement implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
      
        $section_name = $row[1];
        $section = DB::table('sections')->where('section_name',$section_name)->select('id')->get()->toArray();
        // if($section == false)
        // {
        //     return redirect()->back()->with('error','Data Element Group Data do not match!');
        // }
        $dc_name = $row[2];
        $data_classification = DB::table('data_classifications')->where("classification_name_en",$dc_name)->where("organization_id",Auth::user()->client_id)->select('id')->get()->toArray();
        // $owner_id =  DB::table('users')->where('id', )->select('id')->get()->toArray();
        $id = Auth::user()->client_id;
        // $row[3]= $owner_id;
        return new AssetDataElement([
            "name" => $row[0],
            "section_id" => $section[0]->id,
            "d_classification_id" => $data_classification[0]->id,
            "owner_id"=>$id,
        ]);

}
}
