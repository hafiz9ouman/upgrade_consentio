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
            "Testing", "Server", "Dummy Data", "Hostinger", "Pakistan", "Lahore", "Punjab", "low", "public", "tier 2", "sert", "Dummy IT", "Test Owner", "HR", "internal", "DSV"];
    
     }

    public function collection()
    {
        $data = [
            [], 
        ];

        return collect($data);      
    }
}
