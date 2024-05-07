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
        if(session('locale') == 'fr'){
            return [
                "Nom", "Type d'actif", "Type d'hébergement", "Fournisseur d'hébergement", "Pays", "Ville", "État", "Impact", "Classification des données", "Niveau", "Responsable informatique", "Responsable métier", "Unité opérationnelle", "Tierce partie interne", "Volume des sujets de données"];
        }
        else{
            return [
                "Name","Asset Type","Hosting Type","Hosting Provider","Country","City","State","Impact","Data Classiication","Tier","IT Owner","Business Owner","Business Unit","Internal 3rd Party","Data Subject Volume"];    
        }
     }

    public function collection()
    {
        if(session('locale') == 'fr'){
            $data = [
                ["Test", "Serveur", "Données fictives", "Hostinger", "Pakistan", "Lahore", "Punjab", "faible", "public", "niveau 2", "Test IT", "Propriétaire de test", "RH", "interne", "VDS"]
            ];
        }
        else{
            $data = [
                ["Testing", "Server", "Dummy Data", "Hostinger", "Pakistan", "Lahore", "Punjab", "low", "public", "tier 2", "Dummy IT", "Test Owner", "HR", "internal", "DSV"], 
            ];
        }

        return collect($data);      
    }
}
