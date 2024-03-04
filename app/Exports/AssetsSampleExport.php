<?php

namespace App\Exports;

use App\Asset;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;

class AssetsSampleExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function headings():array{
        return [
            "Name","Asset Type","Hosting Type","Hosting Provider","Country","City","State","Longitude","Latitude","Impact","Data Classiication","Tier","Organization","It Owner","Business Owner","Business Unit","Internal 3rd Party","Data Subject Volume",];
    
     }

    public function collection()
    {
        $data = [
            ["Testing", "Server", "Dummy Data", "Hostinger", "Pakistan", "Lahore", "Punjab", "34.1234", "74.5678", "low", "public", "tier 2", "Testing Org", "Dummy IT", "Test Business", "789", "internal", "DSV"], 
        ];

        return collect($data);      
    }
}
