<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Mail;
use App\PasswordSecurity;
use App\User;
use App\Country;
use Lang;
class SARForm extends Controller
{
    public function __construct()
    {

    }

    public function assignee_list ($form_id = 14)
    {
        
         $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            // if(Auth::user()->role != 3 ){
            if(!in_array('SAR Forms', $assigned_permissions)){
                return redirect('dashboard');
            // }
        }

        $client_id = Auth::user()->client_id;
        $form_info = DB::table('forms')->find($form_id);
        // dd($form_info);
        
        
        if (empty($form_info))
        {  
            return redirect('Forms/FormsList');
        }
        
        //$client_id = 1; // logged in as user
        $client_user_list = DB::table('users')->where('client_id', '=', $client_id)->pluck('name');
        // dd($client_user_list);
        
        /* 
        internal users count
        SELECT sub_forms.id, count(sub_forms.id) as internal_users_count FROM sub_forms
        JOIN user_forms on sub_forms.id = user_forms.sub_form_id
        GROUP by sub_forms.id   

        external users count
        SELECT sub_forms.id, count(sub_forms.id) as external_users_count FROM sub_forms
        JOIN external_users_forms ON sub_forms.id = external_users_forms.sub_form_id
        GROUP by sub_forms.id       
         */
         
        $internal_users_count = DB::table('sub_forms')
                                  ->join('user_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
                                  ->select('sub_forms.id', DB::raw('count(sub_forms.id) as internal_users_count'))
                                  ->groupBy('sub_forms.id')->get()->toArray();         
                
        $external_users_count = DB::table('sub_forms')
                                  ->join('external_users_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
                                  ->select('sub_forms.id', DB::raw('count(sub_forms.id) as external_users_count'))
                                  ->groupBy('sub_forms.id')->get()->toArray();

        $int_ids_list = array_column($internal_users_count, 'id');
                                  
        $ext_ids_list = array_column($external_users_count, 'id');
        

        $subforms_list    = DB::table('sub_forms')
                              ->where('parent_form_id', '=', $form_id)
                              ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                              ->select('sub_forms.*', 'forms.title as parent_form_title');
                              //->get();
        
        if (Auth::user()->role == 1) {
            $subforms_list    = $subforms_list->get();
        }
        else {
            $subforms_list    = $subforms_list->where('client_id', [$client_id, Auth::id()])->get();
        }
        
        foreach ($subforms_list as $key => $subforms)
        {
            if (($sf_index = array_search($subforms->id, $int_ids_list)) !== false)
            {
                $subforms_list[$key]->internal_users_count = $internal_users_count[$sf_index]->internal_users_count;
            }
            
            if (($sf_index = array_search($subforms->id, $ext_ids_list)) !== false)
            {
                $subforms_list[$key]->external_users_count = $external_users_count[$sf_index]->external_users_count;
            }
        }
  // dd($form_info);
        return view('sar.sar_form_assignment', [
                                  'user_type'    => ((Auth::user()->role == 1)?('admin'):('client')),
                                  'title'        => 'Client SubForms',
                                  'heading'      => 'Client SubForms',
                                  'form_info'    => $form_info,
                                  'sub_forms'    => $subforms_list,
                                  'client_users' => $client_user_list
                               ]);
    }
    
    // public function sar_completed_forms_list ()
    // {
    //     $client_id = Auth::user()->client_id;

    //     if (Auth::user()->role == 2 || (Auth::user()->role == 3 && Auth::user()->user_type == 1))
    //     {
    //         /*
    //         SELECT sub_forms.id, external_users_forms.user_email, sub_forms.title as subform_title, forms.title as form_title, 'external' as user_type,
    //         SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as completed_forms,
    //         COUNT(external_users_forms.user_email) as total_external_users_count FROM `external_users_forms`
    //         JOIN sub_forms ON sub_forms.id = external_users_forms.sub_form_id
    //         JOIN forms     ON forms.id     = sub_forms.parent_form_id
    //         JOIN sar_requests ON sar_requests.user_form_id    = uf.id
    //         WHERE is_locked = 1
    //         AND   external_users_forms.client_id = 120
    //         GROUP BY sub_forms.id        
    //         */
        
    //         $ext_forms = DB::table('external_users_forms as exf')
    //                                 ->join('sub_forms',    'exf.sub_form_id',           '=', 'sub_forms.id')
    //                                 ->join('forms',        'forms.id',                  '=', 'sub_forms.parent_form_id')
    //                                 ->join('sar_requests', 'sar_requests.user_form_id', '=', DB::raw('exf.id AND sar_requests.user_type = "ex"'))
				//     ->where('forms.code', '=', 'f10')
    //                                 ->where('exf.client_id', $client_id)
    //                                 ->where('is_locked', 1)
    //                                 ->select('*', DB::raw('exf.user_email as email,
    //                                                       SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as ex_completed_forms,
    //                                                       COUNT(exf.user_email) as total_external_users_count,
    //                                                       forms.title as form_title, 
    //                                                       sub_forms.title as subform_title, 
    //                                                       "External" as user_type,
    //                                                       sar_requests.id as request_id,
    //                                                       sar_requests.status as sar_request_status'))
    //                                 ->groupBy('exf.id')
    //                                 ->get();

    //         /*
    //         SELECT sub_forms.id, users.email, sub_forms.title as subform_title, forms.title as form_title, 'internal' as user_type,
    //         SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as completed_forms,
    //         COUNT(users.email) as total_internal_users_count 
    //         FROM `user_forms`
    //         JOIN users        ON users.id                     = user_forms.user_id
    //         JOIN sub_forms    ON sub_forms.id                 = user_forms.sub_form_id
    //         JOIN forms        ON forms.id                     = sub_forms.parent_form_id
    //         JOIN sar_requests ON sar_requests.user_form_id    = uf.id
    //         WHERE is_locked = 1
    //         AND   user_forms.client_id = 120
    //         GROUP BY sub_forms.id        
    //         */
        
    //         $int_forms = DB::table('user_forms as uf')
    //                                 ->join('users', 'users.id', '=', 'uf.user_id')
    //                                 ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
    //                                 ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
    //                                 ->join('sar_requests', 'sar_requests.user_form_id', '=', DB::raw('uf.id AND sar_requests.user_type = "in"'))                    
				//     ->where('forms.code', '=', 'f10')
    //                                 ->where('uf.client_id', $client_id)
    //                                 ->where('is_locked', 1)
    //                                 ->select('*', DB::raw('users.email,
    //                                                       SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as in_completed_forms,
    //                                                       COUNT(users.email) as total_internal_users_count,
    //                                                       forms.title as form_title, 
    //                                                       sub_forms.title as subform_title, 
    //                                                       form_link_id as form_link, 
    //                                                       "Internal" as user_type,
    //                                                       sar_requests.id as request_id,
    //                                                       sar_requests.status as sar_request_status'))
    //                                 ->groupBy('uf.id')
    //                                 ->get();
                                                       
    //         $completed_forms = $int_forms->merge($ext_forms);
            
    //         return view('sar.completed_forms_list', compact('completed_forms'));            
    //     }      
    // }
    
    public function sar_completed_forms_list ()
    {
            // dd('asdasd');
            $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            // if(Auth::user()->role != 3 ){
            if(!in_array('SAR Forms Submitted', $assigned_permissions)){
                return redirect('dashboard');
            } 
             // }

        // $subform_id=96;
            $subform_id = DB::table('sub_forms')->where('client_id' , '=', Auth::user()->client_id)
                      ->where('title', '=', 'SAR FORM')
                      ->pluck('id')->first();

          $client_id = Auth::user()->client_id;
        // dd($client_id);
	      
	  	   $parent_form_id = DB::table('sub_forms')
                        ->where('id', '=', $subform_id)
                      ->pluck('parent_form_id')->first();
                      // dd($parent_form_id);
                      
        if (!$parent_form_id)
        {
            // dd('asdasd');
	        return abort('404');
        }
                      
                      
        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first(); 
          // $parent_form_info = DB::table('forms')->where('id', 14)->first();          


        $int_form_user_list = DB::table('user_forms')->where('user_forms.client_id', '=', $client_id)
                                ->join('sub_forms',      'sub_forms.id',     '=', 'user_forms.sub_form_id')
                                ->join('users',      'users.id',     '=', 'user_forms.user_id')
                                ->where('sub_form_id', '=', $subform_id)
                                ->where('title' , '=' , 'SAR FORM')   // ahmad
                                ->select(DB::raw('*, user_forms.created as uf_created, user_forms.expiry_time as uf_expiry_time, "internal", is_locked'))->get();
                      
        $ext_form_user_list = DB::table('external_users_forms')
                                ->join('sub_forms',      'sub_forms.id',     '=', 'external_users_forms.sub_form_id')
                                ->where('external_users_forms.client_id', '=', $client_id)
                                ->where('sub_form_id', '=', $subform_id)
                                ->where('title' , '=' , 'SAR FORM')   // ahmad
                                ->select(DB::raw('*, external_users_forms.created as uf_created, external_users_forms.expiry_time as uf_expiry_time, "external", is_locked'))->get();
   
	    if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1')
	    {
	        $form_user_list = $ext_form_user_list;
	    }
	    else
	    {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
	    }
        // dd($form_user_list);
                                
                                
	    $user_type = 'client';          
        if (Auth::user()->role == 1) 
        {
            $user_type = 'admin';
        }
        return view('sar.completed_sar_forms_list', compact('form_user_list', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }
    public function sar_incompleted_forms_list ()
    {   
            $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            // if(Auth::user()->role != 3 ){
            if(!in_array('SAR Forms pending', $assigned_permissions)){
                return redirect('dashboard');
            }
        // }

        // $subform_id=96;
        $subform_id = DB::table('sub_forms')->where('client_id' , '=', Auth::user()->client_id)
                      ->where('title', '=', 'SAR FORM')
                      ->pluck('id')->first();
                      
        $client_id = Auth::user()->client_id;

	    // old
		$parent_form_id = DB::table('sub_forms')
                      ->where('id', '=', $subform_id)
                      ->pluck('parent_form_id')->first();
        // old

        // ahmad
 // $parent_form_id = DB::table('sub_forms')
 //                      ->where('id', '=', 'SAR FORM')
 //                      ->pluck('parent_form_id')->first();


                       // $subform_id =  DB::table('sub_forms')
                       //                ->where('id', '=', 'SAR FORM')
                       //                ->pluck('id')->first();
                      // ahmad



        // dd($parent_form_id);
                      
        // if (!$parent_form_id)
        // {
	       //  return abort('404');
        // }
                      
                      
                        
        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first(); 
                      // dd($parent_form_info);

          // $parent_form_info = DB::table('forms')->where('id', 14)->first();        


        $int_form_user_list = DB::table('user_forms')->where('user_forms.client_id' , $client_id)
                                ->join('sub_forms',      'sub_forms.id',     '=', 'user_forms.sub_form_id')
                                ->join('users',      'users.id',     '=', 'user_forms.user_id')
                                ->where('sub_form_id', '=', $subform_id)
                                ->where('title' , '=' , 'SAR FORM')   // ahmad
                                ->select(DB::raw('*, user_forms.created as uf_created, user_forms.expiry_time as uf_expiry_time, "internal", is_locked'))->get();


                      
        $ext_form_user_list = DB::table('external_users_forms')->where('external_users_forms.client_id' , $client_id)
                                ->join('sub_forms',      'sub_forms.id',     '=', 'external_users_forms.sub_form_id')
                                ->where('sub_form_id', '=', $subform_id)
                                ->where('title' , '=' , 'SAR FORM')   //ahmad
                                ->select(DB::raw('*, external_users_forms.created as uf_created, external_users_forms.expiry_time as uf_expiry_time, "external", is_locked'))->get();
   
	    if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1')
	    {

	        $form_user_list = $ext_form_user_list;
	    }
	    else
	    {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
            // dd($form_user_list);
	    }
                                
                                
	    $user_type = 'client';          
        if (Auth::user()->role == 1) 
        {
            $user_type = 'admin';
        }
        return view('sar.in_complete_sar_forms_list', compact('form_user_list', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }    
    
    public function sar_expiry_settings_get ()
    {   

          $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            if(!in_array('SAR Expiry Settings', $assigned_permissions)){
                return redirect('dashboard');
            }


        $sar_settings = DB::table('sar_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
        
        if (empty($sar_settings))
        {
            $sar_settings = DB::table('sar_admin_expiration_settings')->first();        
        }
        
        return view('sar.sar_settings', ['sar_settings' => $sar_settings]);
    }

    public function sar_expiry_settings_post (Request $request)
    {
        DB::table('sar_client_expiration_settings')
            ->updateOrInsert(
                ['client_id' => $request->input('client_id')],
                ['duration' => $request->input('duration'), 'period' => $request->input('period')]
            );
        
        return response()->json(['status' => 'success', 'msg' => 'updated']);
    }
    
    public function change_sar_request_status_post (Request $request)
    {
        $request_id = $request->input('request_num');
        $status     = $request->input('status');
        $warn       = $request->input('warn');
        
        if ($status == '1' && $warn == '1')
        {
            $due_date = DB::table('sar_requests')->where('id', '=', $request_id)->pluck('due_date')->first();
            
            if ($due_date)
            {
                if (strtotime(date('Y-m-d')) > strtotime(date('Y-m-d', strtotime($due_date))))
                {
                    return response()->json([
                                                'status' => 'warning', 
                                                'msg'    => __('Due date is expired. Continue updating status?')
                                            ]);
                }
            }
        }

        DB::table('sar_requests')->where('id', '=', $request_id)->update(['status' => $status]);

        return response()->json(['status' => 'success', 'msg' => __('Status updated')]);
    }
}