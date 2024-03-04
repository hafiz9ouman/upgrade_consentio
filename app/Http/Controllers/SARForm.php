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
use \Carbon\Carbon;
class SARForm extends Controller
{
    public function __construct()
    {

    }

    public function assignee_list ()
    {
        
        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');

        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);

            }
        }
        // if(Auth::user()->role != 3 ){
        if (!in_array('SAR Forms', $assigned_permissions)) {
            return redirect('dashboard');
            // }
        }

        // dd('reight');

        // dd('walla');
        $this->middleware(['auth', '2fa']);
        if (Auth::user()->role != 3) {
            if (Auth::user()->role != 2 && Auth::user()->user_type != 1) {
                return abort(404);
            }
        }

        $client_id = Auth::user()->client_id;

        //$this->middleware(['auth']);
        /*
            SELECT forms.id, forms.title, count(sub_forms.id) as subforms_count
            FROM  `forms`
            JOIN   client_forms ON forms.id = client_forms.form_id
            JOIN   sub_forms    ON forms.id = sub_forms.parent_form_id AND sub_forms.client_id = 120
            WHERE  client_forms.client_id = 120
            GROUP
            BY     forms.id
         */
        $forms_info = DB::table('forms')
            ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
            ->leftjoin('sub_forms', 'forms.id', '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = ' . $client_id))
            ->where('client_forms.client_id', '=', $client_id)
            ->where('type', 'sar')
            ->selectRaw('forms.title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.date_created')
            ->groupBy('forms.id')
            ->orderBy('date_created')
            ->get();

        if (session('locale') == 'fr') {
            $forms_info = DB::table('forms')
                ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
                ->leftjoin('sub_forms', 'forms.id', '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = ' . $client_id))
                ->where('client_forms.client_id', '=', $client_id)
                ->where('type', 'sar')
                ->selectRaw('forms.title_fr as title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.date_created')
                ->groupBy('forms.id')
                ->orderBy('date_created')
                ->get();
        }

        $type = 'sar';

        return view('sar.sar_form_assignment', ['user_type' => 'client', 'forms_list' => $forms_info, 'type' => $type]);
        
    }

    public function subforms_list($form_id=1){

        $this->middleware(['auth', '2fa']);

        //$client_id = Auth::id();
        $client_id = Auth::user()->client_id;

        $form_info = DB::table('forms')->find($form_id);

        if (empty($form_info)) {
            return redirect('Forms/FormsList');
        }

        //$client_id = 1; // logged in as user
        $client_user_list = DB::table('users')->where('client_id', '=', $client_id)->pluck('name');

        /*
            internal users count
            SELECT sub_forms.id, count(sub_forms.id) as internal_users_count FROM sub_forms
            JOIN  on sub_forms.id = .sub_form_id
            GROUP by sub_forms.id

            external users count
            SELECT sub_forms.id, count(sub_forms.id) as external_users_count FROM sub_forms
            JOIN user_form_links ON sub_forms.id = user_form_links.sub_form_id
            GROUP by sub_forms.id
         */

        $internal_users_count = DB::table('sub_forms')
            ->join('user_form_links', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->groupBy('sub_forms.id')
            ->select('user_form_links.is_internal', 'sub_forms.id', DB::raw('count(IF(user_form_links.is_internal = "1", sub_forms.id, NULL)) as internal_users_count'))
            ->get()
        ->toArray();

        $external_users_count = DB::table('sub_forms')
            ->join('user_form_links', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->groupBy('sub_forms.id')
            ->select('user_form_links.is_internal', 'sub_forms.id', DB::raw( 'count( IF(user_form_links.is_internal = "0", sub_forms.id, NULL)) as external_users_count'))
            ->get()
        ->toArray();

        

        $int_ids_list = array_column($internal_users_count, 'id');

        $ext_ids_list = array_column($external_users_count, 'id');

        $subforms_list = DB::table('sub_forms')
            ->where('parent_form_id', '=', $form_id)
            ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->select('sub_forms.*', 'forms.title as parent_form_title');
        //->get();

        if (Auth::user()->role == 1) {
            $subforms_list = $subforms_list->get();
        } else {
            $subforms_list = $subforms_list->where('client_id', [$client_id, Auth::id()])->get();
        }

        foreach ($subforms_list as $key => $subforms) {
            if (($sf_index = array_search($subforms->id, $int_ids_list)) !== false) {
                $subforms_list[$key]->internal_users_count = $internal_users_count[$sf_index]->internal_users_count;
            }

            if (($sf_index = array_search($subforms->id, $ext_ids_list)) !== false) {
                $subforms_list[$key]->external_users_count = $external_users_count[$sf_index]->external_users_count;
            }
        }

        // dd($subforms_list);

        return view('sar.subform', [
            'user_type' => ((Auth::user()->role == 1) ? ('admin') : ('client')),
            'title' => 'Client SubForms',
            'heading' => 'Client SubForms',
            'form_info' => $form_info,
            'sub_forms' => $subforms_list,
            'client_users' => $client_user_list,
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
            
            
            
    
    public function sar_completed_forms_list (){
        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');
        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);
            }
        }
        if (!in_array('SAR Forms Submitted', $assigned_permissions)) {
            return redirect('dashboard');
        }
        $client_id = Auth::user()->client_id;
        $role_id = Auth::user()->role;
        $mytime = Carbon::now();
        $result = null;


        if ((Auth::user()->role == 2 || Auth::user()->role == 3) || (Auth::user()->role == 3 && Auth::user()->user_type == 1)) {
            /*
                SELECT sub_forms.id, user_form_links.user_email, sub_forms.title as subform_title, forms.title as form_title, 'external' as user_type,
                SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as completed_forms,
                COUNT(user_form_links.user_email) as total_external_users_count FROM `user_form_links`
                JOIN sub_forms ON sub_forms.id = user_form_links.sub_form_id
                JOIN forms     ON forms.id     = sub_forms.parent_form_id
                WHERE is_locked = 1
                AND   user_form_links.client_id = 120
                GROUP BY sub_forms.id
             */
            $ext_forms = DB::table('user_form_links as exf')
                ->join('sub_forms', 'exf.sub_form_id', '=', 'sub_forms.id')
                ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                ->where('forms.type', 'sar')
                ->where('exf.client_id', $client_id)
                ->select('*', DB::raw('exf.user_email as email,
                                    SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as ex_completed_forms,
                                    COUNT(exf.user_email) as total_external_users_count,
                                    forms.title as form_title,
                                    forms.title_fr as form_title_fr,
                                    sub_forms.title as subform_title,
                                    sub_forms.title_fr as subform_title_fr,
                                    "External" as user_type'))
                ->groupBy('sub_forms.id')
                ->get();

            /*
                SELECT sub_forms.id, users.email, sub_forms.title as subform_title, forms.title as form_title, 'internal' as user_type,
                SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as completed_forms,
                COUNT(users.email) as total_internal_users_count
                FROM ` `
                JOIN users     ON users.id        =  .user_id
                JOIN sub_forms ON sub_forms.id    =  .sub_form_id
                JOIN forms     ON forms.id        = sub_forms.parent_form_id
                WHERE is_locked = 1
                AND    .client_id = 120
                GROUP BY sub_forms.id
             */
            if ($role_id == 2) {
                $ext_forms = DB::table('user_form_links as exf')
                    ->join('sub_forms', 'exf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('forms.type', 'sar')
                    ->where('exf.client_id', $client_id)
                    ->where('exf.is_locked', 1)
                    ->where('exf.is_internal', 0)
                    ->select('*', DB::raw('exf.user_email as email,
                        forms.title as form_title,
                        forms.title_fr as form_title_fr,
                        sub_forms.title as subform_title,
                        sub_forms.title_fr as subform_title_fr,
                        "External" as user_type'))
                    ->orderBy('exf.created', 'desc')
                    ->get();
                    // dd($ext_forms);

                $int_forms = DB::table('user_form_links as uf')
                    ->join('users', 'users.id', '=', 'uf.user_id')
                    ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('forms.type', 'sar')
                    ->where('uf.client_id', $client_id)
                    ->where('uf.is_locked', 1)
                    ->select('*', DB::raw('users.email,
                                        forms.title as form_title,
                                        forms.title_fr as form_title_fr,
                                        sub_forms.title as subform_title,
                                        sub_forms.title_fr as subform_title_fr,
                                        form_link_id as form_link,
                                        "Internal" as user_type'))
                    ->orderBy('uf.created', 'desc')
                    ->get();
                    // dd($int_forms);
                $all_forms = $int_forms->merge($ext_forms);

                $all_form_data = json_decode(json_encode($all_forms), true);
                // dd($all_forms);
                // dd($all_form_data);

                foreach ($all_form_data as $data) {
                    DB::Table('tmp_Data')->insert([
                        'form_link_id' => $data['form_link_id'] ?? "",
                        'percent_completed' => $data['percent_completed'] ?? "",
                        'is_locked' => $data['is_locked'] ?? "",
                        'is_accessible' => $data['is_accessible'] ?? "",
                        'sub_form_id' => $data['sub_form_id'] ?? "",
                        'client_id' => $data['client_id'] ?? "",
                        'created' => $data['created'] ?? "",
                        'updated' => $data['updated'] ?? "",
                        'expiry_time' => $data['expiry_time'] ?? "",
                        'name' => $data['name'] ?? "",
                        'is_email_varified' => $data['is_email_varified'] ?? "",
                        'email_varification_code' => $data['email_varification_code'] ?? "",
                        'browser_check_code' => $data['browser_check_code'] ?? "",
                        'email' => $data['email'] ?? "",
                        'website' => $data['website'] ?? "",
                        'role' => $data['role'] ?? "",
                        'company' => $data['company'] ?? "",
                        'status' => $data['status'] ?? "",
                        'created_by' => $data['created_by'] ?? "",
                        'image_name' => $data['image_name'] ?? "",
                        'tfa' => $data['tfa'] ?? "",
                        'remember_token' => $data['remember_token'] ?? "",
                        'created_at' => $data['created_at'] ?? "",
                        'updated_at' => $data['updated_at'] ?? "",
                        'rememberme_browser_type' => $data['rememberme_browser_type'] ?? "",
                        'title' => $data['title'] ?? "",
                        'title_fr' => $data['title_fr'] ?? "",
                        'parent_form_id' => $data['parent_form_id'] ?? "",
                        'lang' => $data['lang'] ?? "",
                        'code' => $data['code'] ?? "",
                        'comments' => $data['comments'] ?? "",
                        'type' => $data['type'] ?? "",
                        'date_created' => $data['date_created'] ?? "",
                        'expiry' => $data['expiry'] ?? "",
                        'date_updated' => $data['date_updated'] ?? "",
                        'in_completed_forms' => $data['in_completed_forms'] ?? "",
                        'total_internal_users_count' => $data['total_internal_users_count'] ?? "",
                        'total_external_users_count' => $data['total_external_users_count'] ?? "",
                        'ex_completed_forms' => $data['ex_completed_forms'] ?? "",
                        'form_title' => $data['form_title'] ?? "",
                        'subform_title' => $data['subform_title'] ?? "",
                        'subform_title_fr' => $data['subform_title_fr'] ?? "",
                        'form_link' => $data['form_link'] ?? "",
                        'user_type' => $data['user_type'],
                        'user_id' => auth::user()->id,
                    ]);
                }

                $completed_forms = DB::Table('tmp_Data')->where('user_id', auth::user()->id)
                                    ->orwhere('is_locked', 1)
                                    ->where('in_completed_forms', 1)
                                    ->orderby('updated_at', 'desc')->get();
                                    // dd($completed_forms);

                DB::table('tmp_Data')->where('user_id', auth::user()->id)->truncate();
                // dd("admin");
            } else {
                // dd("user side");
                $client_id = Auth::user()->id;
                $int_forms = DB::table('user_form_links as uf')

                    ->join('users', 'users.id', '=', 'uf.user_id')
                    ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('forms.type', 'sar')
                    ->where('uf.user_id', $client_id)
                    ->where('is_locked', 1)
                    ->select('*', DB::raw('users.email,
                                SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as in_completed_forms,
                                COUNT(users.email) as total_internal_users_count,
                                forms.title as form_title,
                                forms.title_fr as form_title_fr,
                                sub_forms.title as subform_title,
                                sub_forms.title_fr as subform_title_fr,
                                form_link_id as form_link,
                                "Internal" as user_type'))
                    ->orderBy('uf.created', 'desc')
                    ->get();
                
                $completed_forms = $int_forms;
            }

            // dd($completed_forms);

            if (count($completed_forms) > 0) {
                foreach ($completed_forms as $data) {
                    if ($mytime <= $data->expiry_time) {
                        $result[] = $data;
                    }
                }
                //  $completed_forms = $result;

            }
            // dd($completed_forms);
            // tohandle null values
            if ($completed_forms == null) {$completed_forms = [];}

            if (Auth::user()->role == 1) {
                $user_type = 'admin';
            } else {
                $user_type = 'client';
            }

            return view('sar.completed_sar_forms_list', compact('completed_forms', 'user_type'));
        }
    }

    public function sar_incompleted_forms_list ()
    {   
        if (!empty($subform_id)) {
            return abort('404');
        }
        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');

        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);

            }
        }
        // if(Auth::user()->role != 3 ){
        if (!in_array('SAR Forms pending', $assigned_permissions)) {
            return redirect('dashboard');
        }
        // }

        $parent_form_id = DB::table('sub_forms')->pluck('parent_form_id');
        $subform_id = DB::table('sub_forms')->pluck('id');
        //dd($subform_id);

        if (!$parent_form_id) {
            return abort('404');
        }

        // $forms=DB::table('sub_forms')->get();
        // dd($forms->count());

        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();

        $client_id = Auth::user()->client_id;
        $user_id = Auth::user()->id;

        $int_form_user_list = DB::table('user_form_links')->where('sub_forms.client_id', $client_id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
            ->join('users', 'users.id', '=', 'user_form_links.user_id')
            ->where('forms.type', 'sar')
            ->where('user_form_links.is_internal', 1)
            ->where('user_form_links.is_locked', 0)
            // ->where('user_form_links.user_id', $user_id)
            ->wherein('sub_form_id', $subform_id)
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "internal", is_locked'))
            ->orderBy('user_form_links.created', 'desc')
            ->get();
        $ext_form_user_list = DB::table('user_form_links')->where('sub_forms.client_id', $client_id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
            ->wherein('sub_form_id', $subform_id)
            ->where('user_form_links.is_internal', 0)
            ->where('user_form_links.is_locked', 0)
            ->where('forms.type', 'sar')
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "external", is_locked'))
            ->orderBy('user_form_links.created', 'desc')
            ->get();

        if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1') {
            $form_user_list = $ext_form_user_list;
        } else {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
        }

        if(Auth::user()->role == 3){
            $int_form_user_list = DB::table('user_form_links')->where('sub_forms.client_id', $client_id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
            ->join('users', 'users.id', '=', 'user_form_links.user_id')
            ->where('forms.type', 'sar')
            ->where('user_form_links.is_internal', 1)
            ->where('user_form_links.is_locked', 0)
            ->where('user_form_links.user_id', $user_id)
            ->wherein('sub_form_id', $subform_id)
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "internal", is_locked'))
            ->orderBy('user_form_links.created', 'desc')
            ->get();

            $form_user_list = $int_form_user_list;
        }

        

        $user_type = 'client';
        if (Auth::user()->role == 1) {
            $user_type = 'admin';
            // dd($user_type);
        }
        $all = 1;
        // dd($user_type);

        return view('sar.in_complete_sar_forms_list', compact('form_user_list', 'all', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
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