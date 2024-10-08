<?php

namespace App\Http\Controllers;
use App\User;
use App\Asset;
use Validator;
use DataTables;
use App\AssetDataElement;
use Illuminate\Http\Request;
use App\Exports\AssetsExport;
use App\Imports\AssetsImport;
use App\Imports\ImportAssetDataElement;
use App\Imports\importAssetsDataElement;
use Illuminate\Support\Facades\DB;
use App\Exports\AssetsSampleExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Validators\ValidationException;
use App\Exports\AssetDataElementExport;
use App\Exports\FirstHundredRowsExport;
use Storage;


class AssetsController extends Controller
{

    public function __construct(){
    }

    public function tests (){
        echo 'here';exit;
        return view('admin.client.test');
    }

    public function asset_data_elements(Request $req){
        if ($req->ajax()) {
            $user_id = Auth::User()->client_id;
            $data = DB::table("assets_data_elements")->where("owner_id", Auth::user()->client_id)->get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                          return  '<a href="edit-data-element/'.$row->id.'" class="btn btn-primary text-light" > Edit</a>';
                    })
                    ->addColumn('section_id', function($row){
                    $data =  DB::table("sections")->where("id",$row->section_id)->get();
                    foreach($data as $val){
                        return  $val->section_name;
                    }                
                    })
                    ->addColumn('d_classification_id', function($row){
                    $data =  DB::table("data_classifications")->where("id",$row->d_classification_id)->get();
                        foreach($data as $val){
                            return $val->classification_name_en;
                        }
                    })
                    ->rawColumns(['action','section_id',"d_classification_id"])
                    ->make(true);
        }


        $elements = DB::table("assets_data_elements")->where("owner_id", Auth::user()->client_id)->get();
        foreach ($elements as  $element) {
            $section                = DB::table("sections")->where('id', $element->section_id)->get();
            $element->section       =  $section[0]->section_name;
            $data_classifications   =  DB::table("data_classifications")->where("id", $element->d_classification_id)->get();
            $element->classification_name_en       =  $data_classifications[0]->classification_name_en;
            $element->classification_name_fr       =  $data_classifications[0]->classification_name_fr;

            
        }
        $section = DB::table("sections")->get();
        $data_classifications =  DB::table("data_classifications")->where("organization_id",Auth::user()->client_id)->get();
        return view("assets.asset_data_element",[
            "elements"                  =>$elements,
            "section"               =>$section,
            'data_classifications'  => $data_classifications
        ]);
    }

    public function edit_data_element($id){
        $data = DB::table("assets_data_elements as ade")
        ->select("ade.*","s.section_name","dc.classification_name_en")
        ->join("sections as s","ade.section_id","s.id")->join("data_classifications as dc","ade.d_classification_id","dc.id")->where("ade.id",$id)->orderby('id',"desc")->get();
        $section = DB::table("sections")->get();
        $dc_result = DB::table("data_classifications")->where('organization_id', Auth::user()->client_id)->get();
            return view('assets.edit_data_element',[
            "data"=>$data,
            "section"=>$section,
            "dc_result"=>$dc_result
        ]);
    }

    public function update_data_element(Request $req){
        $req->validate([
                "name" => "required",
                "element_group" => "required",
                "d_c_name" => "required",
            ]);
                DB::table("assets_data_elements")
                ->where("id",$req->id)
                ->update([
                    "name" =>$req->name,
                    "section_id" =>$req->element_group,
                    "d_classification_id" =>$req->d_c_name,
                ]);
                return redirect("/assets_data_elements")->with("success","Data Has Successfully Updated");
    }

    function evaluation_rating(){
        $org_id = Auth::user()->client_id;
        $data = DB::table("evaluation_rating")->where("owner_id", $org_id)->orderby("id","desc")->get();
        return view("assets.evaluation_rating",["data"=>$data]);
    }

    public function edit_evalution($id){
        $data = DB::table("evaluation_rating")->where("id",$id)->first();
        return view("assets.edit_evalution",["data"=>$data]);
    }

    public function update_evalution_rating(Request $req){
        $req->validate([
            "assessment" => "required",
            "rating" => "required",
            "color" => "required",
            "text_color" => "required",
        ]);
        DB::table("evaluation_rating")
            ->where("id",$req->id)
            ->update([
                "assessment" => $req->assessment,
                "rating" => $req->rating,
                "color" => $req->color,
                "text_color" => $req->text_color,
                "owner_id" => Auth::user()->client_id,
        ]);
         
        return redirect("/evaluation_rate")->with("success","Data Has Successfully Inserted");
    } 

    function dataElementGroup(Request $req){
        $req->validate([
            "new_element" => "required",
            "element_group" => "required",
        ]);

        DB::table("assets_data_elements")->insert([
            "name" =>  $req->new_element,
            "section_id" => $req->element_group,
            "d_classification_id" => 2,
            "owner_id" => Auth::user()->id,
        ]);
        return back()->with("success","Data Has Successfully Inserted");
    }

    function element_data(){
        return view("assets.import_data_element");
    }

    public function import_data_element(Request $req){  
        try{
            Excel::import(new ImportAssetDataElement, $req->file('import_file'));
            return redirect('assets_data_elements')->with("success","You are successfully Imported");
        }
        catch(\Exception $exception)
        {
            if(session('locale') == 'fr'){
                return redirect()->back()->with(['msg'=>'Veuillez sélectionner le fichier approprié']);
            }
            else{
                return redirect()->back()->with(['msg'=>'Please Select Appropriate File']);
            }
        }
    }

    public function asset_elements_update(Request $req){
        $d_id = $_POST['ae_id'];
        $d_c = $_POST['d_c_id'];
        $asset_name = $_POST['asset_name'];

        DB::table("assets_data_elements")
            ->where("id",$d_id)
            ->update([
                "name" => $asset_name,
                "d_classification_id" => $d_c,
                "section_id" => $req->section,
            ]);
    }

    public function index(Request $req){
        if($req->imp && $req->classification_id){

            $data_class    = DB::table('data_classifications')->find($req->classification_id);
            $asset_matrix  = DB::table("asset_tier_matrix")->where("impact_id", $req->imp)->where("data_classification_id", $data_class->confidentiality_level)->get();
            return $asset_matrix;
            
        }
        else{
            $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');
            if($data != null){
                foreach ($data as $value) {
                    $assigned_permissions = explode(',',$value); 
                }
            }
            if(!in_array('Assets List', $assigned_permissions)){
                return redirect('dashboard');
            }  
            $asset_data_element = DB::table("assets_data_elements")->get();
            $client_id = Auth::user()->client_id;


            $asset_list = DB::table('assets')
                            ->join('data_classifications', 'data_classifications.id', 'assets.data_classification_id')
                            ->join('impact', 'impact.id', 'assets.impact_id')
                            ->whereNotNull('name')->where('client_id', $client_id)->orderBy('assets.id','DESC')
                            ->select(
                                'assets.id',
                                'assets.client_id',
                                'assets.asset_type',
                                'assets.name',
                                'assets.hosting_provider',
                                'assets.hosting_type',
                                'assets.country',
                                'assets.city',
                                'assets.state',
                                'assets.asset_number',
                                'data_classifications.classification_name_en',
                                'data_classifications.classification_name_fr',
                                'impact.impact_name_en',
                                'impact.impact_name_fr',
                                'assets.tier',
                                'assets.it_owner',
                                'assets.business_owner',
                                'assets.business_unit',
                                'assets.internal_3rd_party',
                                'assets.data_subject_volume'
                                )
                            ->get();

            $countries = DB::table('countries')->get();
            $impact = DB::table("impact")->get();
            $dt_classification = DB::table("data_classifications")->where('organization_id', Auth::user()->client_id)->get();

            // =============================================== //
        /* MAP LAT LNG */

        $user_type = Auth::user()->role;
        $user_id = Auth::user()->id;
        $client_id = Auth::user()->client_id;
        
        $lat_value[] = '';
        $lat_detail[] = '';

        $lat_lng = DB::table('assets')->where('lat', '!=', '')
            ->where('client_id', $client_id)
            ->get();
        $total_assets = count($lat_lng);

        if ($lat_lng != '') {
            foreach ($lat_lng as $lat_val) {
                $lang = $lat_val->lng;
                $lng = number_format((float) $lang, 6, '.', '');
                $lng = floatval($lng);
                $late = $lat_val->lat;
                $lat = number_format((float) $late, 6, '.', '');
                $lat = floatval($lat);
                $lat_value[] = [$lat_val->country, $lat, $lng];
                $lat_detail[] = [$lat_val->country, $lat_val->city, $lat_val->state, $lat_val->name, $lat_val->hosting_provider, $lat_val->asset_type];
            }
        }

        /* MAP LAT LNG */
        // ================================================================================================================= //

                
            // print("<pre>");print_r($asset_list);exit;
            return view('assets.assets', ["asset_data_element"=>$asset_data_element,"impact"=>$impact,"dt_classification"=>$dt_classification,"lat_value"=>$lat_value,"lat_detail"=>$lat_detail,'asset_list' => $asset_list, 'countries' => $countries, 'user_type' => (Auth::user()->role == 1)?('admin'):('client')]);
        }
    }

    public function view_assets($id){
        $data = DB::table('assets')->where('id',$id)->get()->first();
        $cont = DB::table('countries')->where('country_name',$data->country)->get();
        $countries = DB::table('countries')->get();
        return view('assets.view_assets',['data' => $data, 'cont' => $cont, 'countries' => $countries, 'user_type' => (Auth::user()->role == 1)?('admin'):('client')]);
    }
    
    public function add_asset(Request $request){
        $asset = $request->input('asset');
        $client_id = Auth::user()->client_id;
        $status = 'error';
        $title  = __('The asset could not be added');
        $msg    = __('Something went wrong while inserting the asset');      
        if (DB::table('assets')->where('name', trim($asset))->exists()){

            $status = 'error';
            $title  = __('Duplicate Asset');
            $msg    = __('This asset is already present');

        }
        else{
            if (DB::table('assets')->insert(['name' => trim($asset) , 'client_id' => $client_id])){
                $status = 'success';
                $title  = __('Asset Added');
                $msg    = __('Asset Added successfully!');                
            }
        }
        return response()->json(['status' => $status, 'title' => $title, 'msg' => $msg]);
    }

    public function asset_update(Request $request){
        $name = $request->name;

        if (DB::table('assets')->where('name',$request->first_name)->update(['name' => $name]))
            {
                return redirect()->back()->with('message', __('Asset Updated successfully!'));                
            }

        return redirect()->back()->with('message', __('Asset Updated Unsuccessfully!'));
    }

    public function asset_delete($id){

        if (DB::table('assets')->where('id',$id)->delete())
            {
                return redirect()->back()->with('message', __('Asset Delete successfully!'));                
            }

        return redirect()->back()->with('message', __('Asset Delete Unsuccessfully!'));
    }

    public function asset_add(Request $request){
        
        
        $request->validate([
            'name' => 'required|max:255',
            'hosting_type' => 'required',
            ],
            [
            'name.required' => __('Please provide proper asset name to proceed.'),
            'hosting_type.required' => __('Please Provide Hosting Type.')
            ]
            );
        
        $client_id = Auth::user()->client_id;

        if (DB::table('assets')->where('name', $request->name)->where('client_id' , '=' , $client_id)->exists()){
            $status = 'error';
            $title  = __('Already Exists');
            $msg    = __('The requested asset was already exists');            

            return response()->json(['status' => $status, 'title' => $title, 'msg' => $msg]);
            // return response()->json($request);
        }

        $latest_assigned_number = 0;
        if (DB::table('assets')->where('client_id', $client_id)->orderby('asset_number', 'DESC')->count() > 0) {
            $latest_assigned_number =  DB::table('assets')->where('client_id', $client_id)->orderby('asset_number', 'DESC')->first()->asset_number;
        }
        //dd($request->all());

        $asset_record =  DB::table('assets')->insert([
            'asset_type'            => $request->asset_type,
            'name'                  => $request->name ,
            'hosting_type'          => $request->hosting_type ,
            'hosting_provider'      => $request->hosting_provider ,
            'country'               => $request->country ,
            'city'                  => $request->city , 
            'state'                 => $request->state , 
            'impact_id'               => $request->impact , 
            'data_classification_id'  => $request->data_classification , 
            'tier'                  => $request->tier_sub_filed , 
            'lng'                   => $request->lng , 
            'lat'                   => $request->lat , 
            'client_id'             => $client_id,
            'it_owner'              => $request->it_owner,
            'business_owner'        => $request->business_owner,
            'business_unit'         => $request->business_unit,
            'internal_3rd_party'    => $request->internal_3rd_party,
            'data_subject_volume'   => $request->data_subject_volume,
            'supplier'              => $request->supplier,
            'list_data_type'        => $request->data_type,
            'data_retention'        => $request->data_retention,
            'no_of_user'            => $request->no_users,
            'notes'                 => $request->notes,
            'description'           => $request->description,
            'asset_number'          => $latest_assigned_number + 1
            ]
        );
        // $asset_record = new asset;
        // $asset_record->asset_type            = $request->asset_type;
        // $asset_record->name                  = $request->name;
        //     $asset_record->hosting_type         = $request->hosting_type;
        //     $asset_record->hosting_provider      = $request->hosting_provider;
        //     $asset_record->country               = $request->country;
        //     $asset_record->city                  = $request->city;
        //     $asset_record->state                 = $request->state; 
        //     $asset_record->lng                   = $request->lng; 
        //     $asset_record->lat                   = $request->lat; 
        //     $asset_record->client_id             = $client_id;
        //     $asset_record->it_owner             = $request->it_owner;
        //     $asset_record->business_owner       = $request->business_owner;
        //     $asset_record->business_unit        = $request->business_unit;
        //     $asset_record->internal_3rd_party   = $request->internal_3rd_party;
        //     $asset_record->data_subject_volume  = $request->data_subject_volume;
        //     $asset_record->asset_number         = $latest_assigned_number + 1;
        // $asset_record->save();


        $status = 'success';
        $title  = __('Added');
        $msg    = __('The requested asset was successfully added');            

        return response()->json(['status' => $status, 'title' => $title, 'msg' => $msg]);
        return response()->json($request);

        $client_id = Auth::user()->client_id;

        if (DB::table('assets')->where('client_id' , '!='  , $client_id)->where('name', $request->name)->exists())
        {
            return redirect()->back()->with('message', __('This asset is already present!'));
        }else
        {
            if($request->hasfile('image')){
                $image = $request->file('image');
                $imageName = time() . "." .$image->extension();
                $imagePath = public_path() . '/img';
                $image->move($imagePath, $imageName);
                $imageDbPath = $imageName;
            }

            if (DB::table('assets')->insert([
                'asset_type' => $request->asset_type,
                'name' => $request->name ,
                'hosting_type' => $request->hosting_type ,
                'hosting_provider' => $request->hosting_provider ,
                'country' => $request->country ,
                'city' => $request->city , 
                'state' => $request->state , 
                'lng' => $request->lng , 
                'lat' => $request->lat , 
                'client_id' => $client_id,
                'it_owner'              => $request->it_owner,
                'business_owner'        => $request->business_owner,
                'internal_3rd_party'    => $request->internal_3rd_party,
                'supplier'              => $request->supplier,
                'list_data_type'        => $request->data_type,
                'data_retention'        => $request->data_retention,
                'no_of_user'            => $request->no_users,
                'notes'                 => $request->notes,
                'description'           => $request->description,
                'data_subject_volume'   => $request->data_subject_volume

            ]))

            {
                return redirect()->back()->with('message', __('Asset Added successfully!'));                
            }   
        }
        return redirect()->back()->with('message', __('Request Denide!'));
    }

    public function asset_edit(Request $request,$id){
        if($request->imp && $request->dc_val){
            $impact = $request->imp; 
            $dc_value = $request->dc_val;

            $data_class    = DB::table('data_classifications')->find($dc_value);
            $asset_matrix  = DB::table("asset_tier_matrix")->where("impact_id", $impact)->where("data_classification_id", $data_class->confidentiality_level)->get();
            return $asset_matrix;

            // $asset_matrix = DB::table("asset_tier_matrix")->where("impact_id",$impact)->where("data_classification_id",$dc_value)->get();
            // return $asset_matrix;
        }else{
            $data = DB::table('assets')->where('id',$id)->get()->first();
            $cont = DB::table('countries')->where('country_name',$data->country)->get();
            $countries = DB::table('countries')->get();
            $impact = DB::table("impact")->get();
            $dt_classification = DB::table("data_classifications")->where('organization_id', Auth::user()->client_id)->get();
            
            return view('assets.assets', ["impact"=>$impact,"dt_classification"=>$dt_classification,'data' => $data, 'cont' => $cont, 'countries' => $countries, 'user_type' => (Auth::user()->role == 1)?('admin'):('client')]);
        }

    }

    public function update_asset(Request $request){
        //dd($request->all());
        $request->validate([
                'name' => 'required|max:255',
                'hosting_type' => 'required',
            ],
            [
                'hosting_type.required' => __('Please Provide Hosting Provider.')
            ]
        );

        DB::table('assets')->where('id',$request->id)->update([
            'asset_type' => $request->asset_type,
            'hosting_type' => $request->hosting_type ,
            'hosting_provider' => $request->hosting_provider ,
            'country' => $request->country ,
            'city' => $request->city , 
            'lng' => $request->lng , 
            'lat' => $request->lat , 
            'state' => $request->state,
            'impact_id'               => $request->impact , 
            'data_classification_id'  => $request->data_classification , 
            'tier'                  => $request->tier_sub_filed ,
            'it_owner' => $request->it_owner,
            'business_unit' => $request->business_unit,
            'business_owner' => $request->business_owner,
            'internal_3rd_party' => $request->internal_3rd_party,
            'supplier'              => $request->supplier,
            'list_data_type'        => $request->data_type,
            'data_retention'        => $request->data_retention,
            'no_of_user'            => $request->no_users,
            'notes'                 => $request->notes,
            'description'           => $request->description,
            'data_subject_volume' => $request->data_subject_volume
        ]);
        $status = 'success';
        $title  = __('Removed');
        $msg    = __('The requested asset was successfully removed');            
        return response()->json(['status' => $status, 'title' => $title, 'msg' => $msg]);
        return response()->json($request);
        if($request->hasfile('image')){
            $image = $request->file('image');
            $imageName = time() . "." .$image->extension();
            $imagePath = public_path() . '/img';
            $image->move($imagePath, $imageName);
            $imageDbPath = $imageName;
            if (DB::table('assets')->where('id',$request->id)->update([
                'asset_type' => $request->asset_type,
                'hosting_type' => $request->hosting_type ,
                'hosting_provider' => $request->hosting_provider ,
                'country' => $request->country ,
                'city' => $request->city , 
                'state' => $request->state
            ])){
                return redirect('assets')->with('message', __('Asset Updated successfully!'));
            }
        }else{
            if (DB::table('assets')->where('id',$request->id)->update([
            'asset_type' => $request->asset_type,
            'hosting_type' => $request->hosting_type ,
            'hosting_provider' => $request->hosting_provider ,
            'country' => $request->country ,
            'city' => $request->city , 
            'state' => $request->state
            ])){
                return redirect('assets')->with('message', __('Asset Updated successfully!'));
            }
            return redirect('assets')->with('message', __('Asset Updated successfully!'));
        }
    }

    public function delete_asset (Request $request){
        $asset_id = $request->id;
        $check_subform = DB::table('sub_forms')->where('asset_id', $asset_id)->count();
        if($check_subform > 0){
            $status = 'error';
            $title  = __('Unable to Delete');
            $msg    = __('Asset already assign to Audit');  
        }else{
            DB::table('assets')->where('id', $asset_id)->delete();
            $status = 'success';
            $title  = __('Removed');
            $msg    = __('The requested asset was successfully removed');  
        }       

        return response()->json(['status' => $status, 'title' => $title, 'msg' => $msg]);
    }

    public function exportAssets($client_id){
        return Excel::download(new AssetsExport($client_id), 'assets.xlsx');
    }

    public function exportElementData($client_id){
         return Excel::download(new AssetDataElementExport($client_id), 'elementData.xlsx');
    }

    public function importAssets(){
        return view('assets.import');
    }

    // public function importAssetsData(Request $req){
    //     try{
    //         Excel::import(new AssetsImport, $req->file('import_file'));
    //         return redirect('assets')->with("success","Your are successfully Imported");
    //     }
    //     catch(\Exception $exception)
    //     {
    //         if(session('locale') == 'fr'){
    //             return redirect()->back()->with(['msg'=>'Veuillez sélectionner le fichier approprié']);
    //         }
    //         else{
    //             return redirect()->back()->with(['msg'=>'Please Select Appropriate File']);
    //         }
    //     }
        
    // }
    public function importAssetsData(Request $req){
        // Define the validation rules
        $rules = [
            'import_file' => 'required|file|mimes:xlsx,xls,csv'
        ];

        // Create the validator instance
        $validator = Validator::make($req->all(), $rules);

        // Check if validation fails
        if ($validator->fails()) {
            if(session('locale')=='fr'){
                return redirect()->back()->with(['msg'=>'Veuillez télécharger le fichier Excel']);
            }else{
                return redirect()->back()->with(['msg'=>'Please Upload Excel File']);
            }
        }
            
        try{
            // Load the file and get the first 100 rows
            $data = Excel::toCollection(null, $req->file('import_file'))->first()->take(200);

            // Generate a unique file name for storing in storage
            $fileName = 'first_100_rows_' . uniqid() . '.xlsx';

            // Store the first 100 rows in an Excel file in storage
            Excel::store(new FirstHundredRowsExport($data), $fileName, 'public');

            // Get the full path of the stored Excel file
            $filePath = storage_path('app/public/' . $fileName);

            // Load the Excel file
            $rows = Excel::toArray(null, $filePath);

            // Check if there are data rows (ignoring the first row)
            $dataRows = array_slice($rows[0], 1);
            
            if (empty($dataRows)) {
                // Delete the temporary Excel file
                Storage::delete('public/' . $fileName);
            
                // Redirect to the assets page with an error message
                if (session('locale') == 'fr') {
                    return redirect('import-asset')->with('msg', 'Le fichier Excel est vide.');
                } else {
                    return redirect('import-asset')->with('msg', 'The Excel file is empty.');
                }
            }

            // Import the data from the stored Excel file using the AssetsImport class
            Excel::import(new AssetsImport, $filePath);

            // Delete the temporary Excel file after import (optional)
            Storage::delete('public/' . $fileName);

            // Redirect to the assets page with a success message
            if(session('locale')=='fr'){
                return redirect('assets')->with('success', 'Vous avez importé les actifs avec succès.');
            }else{
                return redirect('assets')->with('success', 'You have successfully imported the data.');
            }
            ///////------
            // // Load the file and get the first 100 rows
            // $data = Excel::toCollection(null, $req->file('import_file'))->first()->take(100);

            // // Export the first 100 rows
            // $export = new FirstHundredRowsExport($data);

            // return Excel::download($export, 'first_100_rows.xlsx');
                
            // // Import the data using the AssetsImport class
            // Excel::import(new AssetsImport, $req->file('import_file'));
            
            // // Redirect to the assets page with a success message
            // return redirect('assets')->with('success', 'You have successfully imported the data.');
        }catch (ValidationException $e) {
            // Collect the validation errors
            $failures = $e->failures();
            $errorMessages = [];

            foreach ($failures as $failure) {
                $errorMessages[] = "Row {$failure->row()}: " . implode(', ', $failure->errors());
            }

            // Redirect to the previous page with the error messages
            return redirect()->back()->withErrors($errorMessages)->withInput();
        }
    }

    public function DataElementSample(){
        return Excel::download(new AssetDataElementExport, 'sample.xlsx');
        return redirect('import-asset')->with("success","You are successfully Sample Exported");
    }

    public function exportSampleData(){
        // return Excel::download(new AssetsSampleExport, 'sample.xlsx');
        if(session('locale')=='fr'){
            $FilePath = storage_path('Sample Asset_French.xlsx');
            if (!File::exists($FilePath)) {
                return redirect('import-asset')->with("msg", "File not found.");
            }
            return response()->download($FilePath, 'Sample Asset_French.xlsx');
        }else{
            $FilePath = storage_path('Sample Asset_English.xlsx');
            if (!File::exists($FilePath)) {
                return redirect('import-asset')->with("msg", "File not found.");
            }
            return response()->download($FilePath, 'Sample Asset_English.xlsx');
        }
        
    }

    public function save_asset_data_elements(Request $req){
        DB::table("assets_data_elements")->insert([
            "name"                  =>  $req->name,
            "section_id"            => $req->element_group,
            "d_classification_id"   => $req->element_classification,
            "owner_id"              => Auth::user()->client_id,
        ]);
        return back()->with("success", "Data Has Successfully Inserted");
    }

}