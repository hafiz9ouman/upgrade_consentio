<?php

namespace App\Exports;

use App\AssetDataElement;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\DB;

class AssetElementSample implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        return [
            "Data Elements","Data Element Group","Data Classification",];

     }

    public function collection()
    {
       return DB::table("assets_data_elements as ade")
            ->select("ade.name","sec.section_name","dc.classification_name_en")
            ->join("sections as sec","ade.section_id","sec.id")
            ->join("data_classifications as dc","ade.d_classification_id","dc.id")
            ->take(5)->get();
    }
}
