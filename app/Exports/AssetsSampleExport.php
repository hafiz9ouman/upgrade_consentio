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
                "Nom de l'actif*", "Type d'actif*", "Type d'hébergement*", "Fournisseur d'hébergement", "Pays*", "Ville", "État", "Impacte*", "Classification des données*", "Niveau*", "Propriétaire informatique", "Propriétaire d'entreprise", "Unité commerciale", "Interne ou tiers", "Volume de données sensibles","Nombre d'utilisateurs","Fournisseur","Liste des types de données dans l'application","Conservation des données","Notes","Description"];
        }
        else{
            return [
                "Asset Name*","Asset type*","Hosting Type*","Hosting Provider","Country*","City","State","Impact*","Data Classiication*","Asset Tier*","IT Owner","Business Owner","Business Unit","Internal or 3rd Party","Volume of Sensitive Data","Number of Users","Supplier","List of Data Type in Application","Data Retention","Notes","Description"];    
        }
     }

    public function collection()
    {
        if(session('locale') == 'fr'){
            $data = [
                ["Test", "Serveur", "Hybride", "Hostinger", "Pakistan", "Lahore", "Punjab", "faible", "publique", "niveau 2", "Test IT", "Propriétaire de test", "RH", "interne", "0-100","5","website","int","0-30 jours","Fr Notes","Fr Description"]
            ];
        }
        else{
            $data = [
                ["Testing", "Server", "Hybrid", "Hostinger", "Pakistan", "Lahore", "Punjab", "low", "public", "tier 2", "Dummy IT", "Test Owner", "HR", "internal", "0-100","5","website","int","0-30 days","En Notes","En Description"],
            ];
        }

        return collect($data);      
    }
}
