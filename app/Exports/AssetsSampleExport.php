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
            "Name","Asset Type","Hosting Type","Hosting Provider","Country","City","State","Impact","Data Classiication","Tier","It Owner","Business Owner","Business Unit","Internal 3rd Party","Data Subject Volume",];
    
     }

    public function collection()
    {
        $data = [
            ["Testing", "Server", "Dummy Data", "Hostinger", "Pakistan", "Lahore", "Punjab", "low", "public", "tier 2", "Dummy IT", "Test Owner", "HR", "internal", "DSV"], 
        ];

        return collect($data);      
    }
}
