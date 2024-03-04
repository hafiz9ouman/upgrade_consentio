<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\GroupSection;
use App\UserResponse;
use \Carbon\Carbon;
use App\UserFormLink;
use App\SubForm;
use App\Question;
use App\Form;
use App\User;

class AuditFormsController extends Controller{
    // Checking permission for this user
    public function checkPermition($permition){
        $permitions = explode("," ,DB::table('module_permissions_users')->where('user_id', Auth::user()->id)->pluck('allowed_module')[0]);
        $have_Permition = true;
        if (!in_array($permition, $permitions)){
            $have_Permition = false;
        }
        return $have_Permition;
    }

    // This Function Will Return A list of Audits Forms Assigned to this user org
    public function audits(){
        if (!$this->checkPermition("Manage Audits")) { 
            return redirect('dashboard');
        }
        
        $client_id = Auth::user()->client_id;
        if (session('locale') == 'fr') {
            $audit_forms = DB::table('forms')
                ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', '=', 'forms.group_id')
                ->leftjoin('sub_forms', 'forms.id', '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = ' . $client_id))
                ->where('client_forms.client_id', '=', $client_id)
                ->where('type', 'audit')
                ->selectRaw('audit_questions_groups.group_name_fr as group_name, forms.title_fr as title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.group_id as group_id, forms.date_created')
                ->groupBy('forms.id')
                ->orderBy('date_created')
                ->get();
        }else{
            $audit_forms = DB::table('forms')
                ->join('audit_questions_groups', 'audit_questions_groups.id', '=', 'forms.group_id')
                ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
                ->leftjoin('sub_forms', 'forms.id', '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = ' . $client_id))
                ->where('client_forms.client_id', '=', $client_id)
                ->where('type', 'audit')
                ->selectRaw('audit_questions_groups.group_name as group_name, forms.title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.group_id as group_id, forms.date_created')
                ->groupBy('forms.id')
                ->orderBy('date_created')
                ->get();
        }
        // echo "<pre>";
        // print_r($audit_forms);
        // exit;
        return view('forms.audits.audit_forms_list', ['user_type' => 'client', 'forms_list' => $audit_forms, 'type' => "audit"]);
    }

    // Audit forms Assigned to current logedin in User
    public function client_audit_forms(){

        if (!$this->checkPermition("Assigned Audits")){

            return redirect('dashboard');

        }

        $user_id   = Auth::id();
        $sub_forms = DB::table('sub_forms')
            ->join("forms", "sub_forms.parent_form_id", "forms.id")
            ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
            ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
            ->leftjoin('user_form_links', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->where('user_form_links.user_id', $user_id)
            ->where('type', 'audit')
            ->select(
                'sub_forms.parent_form_id',
                'forms.title as title', 
                'sub_forms.client_id',
                'sub_forms.other_id',
                'sub_forms.other_number',
                'forms.title_fr as title_fr', 
                'sub_forms.title as sub_form_title', 
                'sub_forms.title_fr as sub_form_title_fr',
                'form_link_id as form_link',
                'audit_questions_groups.group_name',
                'audit_questions_groups.group_name_fr',
                'assets.asset_number',
                'assets.name as asset_name',
                'user_form_links.form_link_id'
            )
        ->get();

        // print("<pre>");
        // print_r($sub_forms);
        // exit;

        return view('forms.audits.client_subform', ['sub_forms' => $sub_forms]);

    }

    public function view_form($id = 1){
        $form_details   = Form::with('group.sections.questions')->find($id);
        return view('forms.audits.show_form', compact('form_details'));
    }

    // Completed form by login user (And Other Organization User for Org Admin )
    public function completed_audits_old(){
        if (!$this->checkPermition("Completed Audits")) { 
            return redirect('dashboard');
        }
        $client_id = Auth::user()->client_id;
        $role_id = Auth::user()->role;
        $mytime = Carbon::now();
        $result = null;

        if ((Auth::user()->role == 2 || Auth::user()->role == 3) || (Auth::user()->role == 3 && Auth::user()->user_type == 1)) {
            $ext_forms = DB::table('user_form_links as exf')
                ->join('sub_forms', 'exf.sub_form_id', '=', 'sub_forms.id')
                ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                ->where('exf.client_id', $client_id)
                ->where('is_internal', 0)
                ->where('forms.type', 'audit')
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
            
            if ($role_id == 2) {
                $ext_forms = DB::table('user_form_links as exf')
                    ->join('sub_forms', 'exf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('forms.type', 'audit')
                    ->where('exf.client_id', $client_id)
                    ->where('is_locked', 1)
                    ->where('is_internal', 0)
                    ->select('*', DB::raw('exf.user_email as email, SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as ex_completed_forms, COUNT(exf.user_email) as total_external_users_count, forms.title as form_title, forms.title_fr as form_title_fr, sub_forms.title as subform_title, sub_forms.title_fr as subform_title_fr, "External" as user_type'))
                    ->groupBy('sub_forms.id')
                ->get();

                $int_forms = DB::table('user_form_links as uf')
                    ->join('users', 'users.id', '=', 'uf.user_id')
                    ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('forms.type', 'audit')
                    ->where('uf.client_id', $client_id)
                    ->where('is_internal', 1)
                    ->where('uf.is_locked', 1)
                    ->select('*', DB::raw('users.email,
                                        SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as in_completed_forms,
                                        COUNT(users.email) as total_internal_users_count,
                                        forms.title as form_title,
                                        forms.title_fr as form_title_fr,
                                        sub_forms.title as subform_title,
                                        sub_forms.title_fr as subform_title_fr,
                                        form_link_id as form_link,
                                        "Internal" as user_type'))
                    ->groupBy('sub_forms.id')
                ->get();
                $all_forms = $int_forms->merge($ext_forms);

                $all_form_data = json_decode(json_encode($all_forms), true);

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

                DB::table('tmp_Data')->where('user_id', auth::user()->id)->truncate();

            } else {
                $client_id = Auth::user()->id;
                $int_forms = DB::table('user_form_links as uf')

                    ->join('users', 'users.id', '=', 'uf.user_id')
                    ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('uf.user_id', $client_id)
                    ->select('*', DB::raw(
                                        'users.email,
                                        SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as in_completed_forms,
                                        COUNT(users.email) as total_internal_users_count,
                                        forms.title as form_title,
                                        forms.title_fr as form_title_fr,
                                        sub_forms.title as subform_title,
                                        sub_forms.title_fr as subform_title_fr,
                                        form_link_id as form_link,
                                        "Internal" as user_type'))
                    ->groupBy('sub_forms.id')
                    ->get();
                $completed_forms = $int_forms;

            }

            if (count($completed_forms) > 0) {
                foreach ($completed_forms as $data) {
                    if ($mytime <= $data->expiry_time) {
                        $result[] = $data;
                    }
                }

            }
            if ($completed_forms == null) {$completed_forms = [];}

            if (Auth::user()->role == 1) {
                $user_type = 'admin';
            } else {
                $user_type = 'client';
            }
            return view('forms.audits.completed_forms_list', compact('completed_forms', 'user_type'));
        }

    }

    public function completed_audits(){
        if (!$this->checkPermition("Completed Audits")){ 
            return redirect('dashboard');
        }
        $client_id  = Auth::user()->client_id;
        $role_id    = Auth::user()->role;
        $mytime     = Carbon::now();
        $result     = null;
        $completed_forms = [];
        if (Auth::user()->role == 2){    
            $ext_forms = DB::table('sub_forms')
                ->join('user_form_links as ufl', 'ufl.sub_form_id', 'sub_forms.id')
                ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
                ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
                ->where('ufl.is_locked', 1)
                ->where('ufl.is_internal', 0)
                ->where('ufl.client_id', $client_id)
                ->groupby('sub_forms.id')
                ->select('*', DB::raw(
                    'ufl.user_email as email, 
                    forms.title as form_title, 
                    forms.title_fr as form_title_fr, 
                    sub_forms.title as subform_title, 
                    sub_forms.id as id, 
                    sub_forms.title_fr as subform_title_fr, 
                    sub_forms.other_id, 
                    sub_forms.other_number, 
                    assets.asset_number, 
                    assets.name as asset_name, 
                    audit_questions_groups.group_name,
                    audit_questions_groups.group_name_fr,
                    "External" as user_type'
                    ))
            ->get();

            $int_forms = DB::table('sub_forms')
                ->join('user_form_links as ufl', 'ufl.sub_form_id', 'sub_forms.id')
                ->join('users', 'users.id', 'ufl.user_id')
                ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
                ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
                ->where('ufl.is_locked', 1)
                ->where('ufl.is_internal', 1)
                ->where('ufl.client_id', $client_id)
                ->groupby('sub_forms.id')
                ->select('ufl.*', DB::raw('
                    users.email,
                    users.name,
                    forms.title as form_title,
                    forms.title_fr as form_title_fr,
                    sub_forms.id as id, 
                    sub_forms.title as subform_title,
                    sub_forms.title_fr as subform_title_fr,
                    sub_forms.other_id, 
                    sub_forms.other_number,
                    form_link_id as form_link,
                    assets.asset_number, 
                    assets.name as asset_name,
                    audit_questions_groups.group_name,
                    audit_questions_groups.group_name_fr, 
                    "Internal" as user_type'
                ))
                ->orderby('sub_forms.id')
            ->get();
            
            $completed_forms = $int_forms->merge($ext_forms);
            $completed_forms = $completed_forms->unique('sub_form_id');
        }
        else{
            $int_forms = DB::table('user_form_links as ufl')
                ->join('users', 'users.id', 'ufl.user_id')
                ->join('sub_forms', 'sub_forms.id', 'ufl.sub_form_id')
                ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
                ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
                ->where('ufl.user_id', Auth::user()->id)
                ->where('is_locked', 1)
                ->select('*', DB::raw(
                    'users.email,
                    users.client_id,
                    forms.title as form_title,
                    forms.title_fr as form_title_fr,
                    sub_forms.title as subform_title,
                    sub_forms.title_fr as subform_title_fr,
                    sub_forms.other_id, 
                    sub_forms.other_number,
                    form_link_id as form_link,
                    audit_questions_groups.group_name,
                    audit_questions_groups.group_name_fr,
                    assets.asset_number, 
                    assets.name as asset_name, 
                    "Internal" as user_type'))
                ->get();
            $completed_forms = $int_forms;
        }
        if (Auth::user()->role == 1){
            $user_type = 'admin';
        } else{
            $user_type = 'client';
        }

        return view('forms.audits.completed_forms_list', compact('completed_forms', 'user_type'));

    }

    public function org_pending_forms(){
        if (!$this->checkPermition("Completed Audits")){ 
            return redirect('dashboard');
        }
        $client_id  = Auth::user()->client_id;
        $role_id    = Auth::user()->role;
        $mytime     = Carbon::now();
        $result     = null;
        $completed_forms = [];

        if (Auth::user()->role == 2){    

            $sub_form_ids = DB::table('user_form_links')->where('is_locked', 1)->pluck('sub_form_id')->toArray();

            $ext_forms = DB::table('sub_forms')
                ->join('user_form_links as ufl', 'ufl.sub_form_id', 'sub_forms.id')

                ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')

                ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')

                ->where('ufl.is_locked', '!=', 1)
                ->where('ufl.is_internal', 0)
                ->where('ufl.client_id', $client_id)
                ->whereNotIn('sub_forms.id', $sub_form_ids)
                ->groupby('sub_forms.id')
                ->select('*', DB::raw(
                    'ufl.user_email as email, 
                    forms.title as form_title, 
                    forms.title_fr as form_title_fr, 
                    sub_forms.title as subform_title, 
                    sub_forms.id as id, 
                    sub_forms.title_fr as subform_title_fr, 
                    sub_forms.other_id, 
                    sub_forms.other_number, 
                    assets.asset_number, 
                    assets.name as asset_name, 
                    audit_questions_groups.group_name,
                    audit_questions_groups.group_name_fr,
                    "External" as user_type'
                    ))
            ->get();

            $int_forms = DB::table('sub_forms')
                ->join('user_form_links as ufl', 'ufl.sub_form_id', 'sub_forms.id')
                ->join('users', 'users.id', 'ufl.user_id')
                ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
                ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
                ->where('ufl.is_locked', 0)
                ->where('ufl.is_internal', 1)
                ->where('ufl.client_id', $client_id)
                ->whereNotIn('sub_forms.id', $sub_form_ids)
                ->groupby('sub_forms.id')
                ->select('ufl.*', DB::raw('
                    users.email,
                    users.name,
                    forms.title as form_title,
                    forms.title_fr as form_title_fr,
                    sub_forms.id as id, 
                    sub_forms.title as subform_title,
                    sub_forms.title_fr as subform_title_fr,
                    sub_forms.other_id, 
                    sub_forms.other_number, 
                    form_link_id as form_link,
                    assets.asset_number, 
                    assets.name as asset_name,
                    audit_questions_groups.group_name,
                    audit_questions_groups.group_name_fr, 
                    "Internal" as user_type'
                ))
                ->orderby('sub_forms.id')
            ->get();
            
            $completed_forms = $int_forms->merge($ext_forms);
            $completed_forms = $completed_forms->unique('sub_form_id');
        }
        else{
            $int_forms = DB::table('user_form_links as ufl')
                ->join('users', 'users.id', 'ufl.user_id')
                ->join('sub_forms', 'sub_forms.id', 'ufl.sub_form_id')
                ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
                ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
                ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
                ->where('ufl.user_id', Auth::user()->id)
                ->where('is_locked', 0)
                ->select('*', DB::raw(
                    'users.email,
                    users.client_id,
                    forms.title as form_title,
                    forms.title_fr as form_title_fr,
                    sub_forms.title as subform_title,
                    sub_forms.title_fr as subform_title_fr,
                    sub_forms.other_id, 
                    sub_forms.other_number, 
                    form_link_id as form_link,
                    audit_questions_groups.group_name,
                    audit_questions_groups.group_name_fr,
                    assets.asset_number, 
                    assets.name as asset_name, 
                    "Internal" as user_type'))
                ->get();
            $completed_forms = $int_forms;
        }

        if (Auth::user()->role == 1){
            $user_type = 'admin';
        } else{
            $user_type = 'client';
        }
        return view('forms.audits.pending_forms_list', compact('completed_forms', 'user_type'));

    }

    public function audit_subforms_list($form_id = 1){
        $client_id = Auth::user()->client_id;
        $form_info = DB::table('forms')->find($form_id);
        if (empty($form_info)) {
            return redirect('Forms/FormsList');
        }
        $client_user_list = DB::table('users')->where('client_id', $client_id)->pluck('name');

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
            ->leftjoin('assets', 'assets.id', 'sub_forms.asset_id')
            ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
            ->where('parent_form_id', '=', $form_id)
            ->select('sub_forms.*', 'assets.name AS asset_name', 'assets.asset_number', 'forms.title as parent_form_title', 'audit_questions_groups.group_name', 'audit_questions_groups.group_name_fr');
        if (Auth::user()->role == 1) {
            $subforms_list = $subforms_list->get();
        } else {
            $subforms_list = $subforms_list->where('sub_forms.client_id', [$client_id, Auth::id()])->get();
        }
        // dd($subforms_list);

        foreach ($subforms_list as $key => $subforms) {
            if (($sf_index = array_search($subforms->id, $int_ids_list)) !== false) {
                $subforms_list[$key]->internal_users_count = $internal_users_count[$sf_index]->internal_users_count;
            }

            if (($sf_index = array_search($subforms->id, $ext_ids_list)) !== false) {
                $subforms_list[$key]->external_users_count = $external_users_count[$sf_index]->external_users_count;
            }
        }
        
        return view('forms.audits.subform', [
            'user_type'     => ((Auth::user()->role == 1) ? ('admin') : ('client')),
            'title'         => 'Client SubForms',
            'heading'       => 'Client SubForms',
            'form_info'     => $form_info,
            'sub_forms'     => $subforms_list,
            'client_users'  => $client_user_list
        ]);
    }

    public function create_subform(Request $req){
        // dd($req->all());
        $title          = $req->input('subform_title');
        $title_fr       = $req->input('subform_title_fr');
        $item_type      = $req->input('item_type');
        $form_id        = $req->input('form_id');
        $expiry_time    = date('Y-m-d H:i:s', strtotime("+10 days"));
        $client_id      = Auth::user()->client_id;
        $asset_id       = $req->input('asset_id');
        $other_id       = $req->input('other_id');

        if($asset_id){
            $form_group             = DB::table('forms')->find($form_id)->group_id;
            $parent_form_id         = DB::table('sub_forms')->where('asset_id', $asset_id)->pluck('parent_form_id');
            $already_group_assigned = DB::table('forms')->whereIn('id', $parent_form_id)->where('group_id', $form_group)->count();

            if ($already_group_assigned > 0) {
                return response()->json(['status' => 'error', 'msg' => __('Sub-form already exists with Same Asset Item')]);
            }
        }
        else{
            $form_group             = DB::table('forms')->find($form_id)->group_id;
            $parent_form_id         = DB::table('sub_forms')->where('other_id', $other_id)->pluck('parent_form_id');
            $already_group_assigned = DB::table('forms')->whereIn('id', $parent_form_id)->where('group_id', $form_group)->count();

            if ($already_group_assigned > 0) {
                return response()->json(['status' => 'error', 'msg' => __('Sub-form already exists with Same Other Item')]);
            }
        }

        $existing_subform = DB::table('sub_forms')->where('parent_form_id', '=', $form_id)->where('client_id', '=', $client_id)->where('title', '=', $title)->where('title_fr', '=', $title_fr)->first();
        if (Auth::user()->role == 2 && !empty($existing_subform)) {
            return response()->json(['status' => 'error', 'msg' => __('Sub-form by this name already exists')]);
        }

        if($other_id){
            $latest_assigned_number = 0;
            if (DB::table('sub_forms')->where('client_id', $client_id)->orderby('other_number', 'DESC')->count() > 0) {
                $latest_assigned_number =  DB::table('sub_forms')->where('client_id', $client_id)->orderby('other_number', 'DESC')->first()->other_number;
            }
            $subform_id = DB::table('sub_forms')->insertGetId([
                'title'         => $title,
                'title_fr'      => $title_fr,
                'item_type'      => $item_type,
                'parent_form_id' => $form_id,
                'client_id'     => $client_id,
                'client_id'     => $client_id,
                'asset_id'      => $asset_id,
                'other_id'      => $other_id,
                'other_number'  => $latest_assigned_number + 1,
                'expiry_time'   => $expiry_time
    
            ]);
        }
        else{
            $subform_id = DB::table('sub_forms')->insertGetId([
                'title'         => $title,
                'title_fr'      => $title_fr,
                'item_type'      => $item_type,
                'parent_form_id' => $form_id,
                'client_id'     => $client_id,
                'client_id'     => $client_id,
                'asset_id'      => $asset_id,
                'other_id'      => $other_id,
                'expiry_time'   => $expiry_time
    
            ]);

        }

        

        //$this->assign_subform_to_client_users($client_id, $form_id, $subform_id);

        return response()->json(['status' => 'success', 'msg' => 'Sub-form created']);

    }

    public function add_comment_against_question(Request $request){
        try {
            if ($request->type == 2) {
                $response =  DB::table('user_responses')
                    ->where('sub_form_id', $request->f_id)
                    ->where('question_id', $request->q_id)->update([
                        'additional_comment' => $request->comment
                ]);
            }else{
                $response =  DB::table('user_responses')
                    ->where('sub_form_id', $request->f_id)
                    ->where('question_id', $request->q_id)->update([
                        'admin_comment' => $request->comment
                ]);
            }
            return $response;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        
    }

    public function add_rating_against_question(Request $request){
        try {
            $response =  DB::table('user_responses')->where('sub_form_id', $request->sub_form_id)
                ->where('form_id', $request->form_id)
                ->where('question_id', $request->q_id)->update([
                    'rating' => $request->rating
                ]);
            return $response;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
        
    }

    public function get_assets($form_id){ 
        try {
            // $assets_whom_form_assined = DB::table('sub_forms')->where('client_id', Auth::user()->client_id)->whereNotNull('asset_id')->pluck('asset_id')->toArray();
            $assets = DB::table('assets')->where("client_id", Auth::user()->client_id)->get();
            return response()->json([
                "status"  => 200,
                "assets"  => $assets
            ]);
            
        } catch (\Exception $ex) {
            throw $ex;
            return response()->json([
                "status"  => 400,
                "message" => $ex->getMessage()
            ]);
        }
    }
    
    public function organization_all_forms_list($subform_id = ''){
        if (empty($subform_id)) {
            return abort('404');
        }
        $parent_form_id = DB::table('sub_forms')->where('id', '=', $subform_id)->pluck('parent_form_id')->first();

        if (!$parent_form_id) {
            return abort('404');
        }

        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();

        $int_form_user_list = DB::table('user_form_links')
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('users', 'users.id', '=', 'user_form_links.user_id')
            ->where('user_form_links.sub_form_id', '=', $subform_id)
            ->where('user_form_links.is_internal', 1)
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "internal", is_locked'))->get();

        $ext_form_user_list = DB::table('user_form_links')
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->where('user_form_links.sub_form_id', '=', $subform_id)
            ->where('user_form_links.is_internal', 0)
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "external", is_locked'))->get();

        if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1') {
            $form_user_list = $ext_form_user_list;
        } else {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
        }

        // return $form_user_list;

        $user_type = 'client';
        if (Auth::user()->role == 1) {
            $user_type = 'admin';
        }
        return view('forms.audits.org_subforms_list', compact('form_user_list', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }

    public function subform_assignees($subform_id){
        $client_id = Auth::user()->client_id;

        if (Auth::user()->role == 3 && Auth::user()->user_type == 1) {
            $client_id = Auth::user()->client_id;
        }

        // actual
        $company_users = DB::table('users')
            ->leftJoin('user_form_links', 'users.id', '=', 'user_form_links.user_id')
            ->where('users.client_id', '=', $client_id)
            ->select('users.*', DB::raw('count(user_form_links.user_id) as forms_count'))
            ->groupBy('users.id')
            ->get()
            ->toArray();
        // actual end

        $subform_info = DB::table('sub_forms')
            ->where('id', '=', $subform_id)
            ->first();
        /*
            SELECT users.id FROM users
            JOIN  ON users.id = .user_id
            AND users.client_id = 23
            WHERE .id = 1
         */

        $assigned_users = DB::table('users')
            ->join('user_form_links', 'users.id', '=', 'user_form_links.user_id')
            ->where('users.client_id', '=', $client_id)
            ->where('user_form_links.sub_form_id', '=', $subform_id)
            ->pluck('users.id')
            ->toArray();

        return view('subforms.subforms_assignee_list', [
            'subform_info' => $subform_info,
            'company_users' => $company_users,
            'assigned_users' => $assigned_users,
        ]);
    }

    public function in_users_show_form_old($form_link_id){
        $info  = DB::table('user_form_links')->where('form_link_id', $form_link_id)->select('client_id', 'user_id', 'sub_form_id')->first();
        if (empty($info)){
            return abort('404');
        }

        $client_id      = $info->client_id;
        $user_id        = $info->user_id; 
        $subfirm_id     = $info->sub_form_id; 

        $form_type      = DB::table('forms')
                        ->join('sub_forms', 'forms.id', 'sub_forms.parent_form_id')
                        ->where('sub_forms.id', $subfirm_id)
                        ->pluck('forms.type')->first();

        
        if (session('locale') == 'fr') {
            $form_info = DB::table('user_form_links')
                ->join('sub_forms', 'user_form_links.sub_form_id', '=', 'sub_forms.id')
                ->join('form_questions', 'sub_forms.parent_form_id', '=', 'form_questions.form_id')
                ->join('questions', 'form_questions.question_id', '=', 'questions.id')
                ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id')

                ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))

                ->where('user_form_links.form_link_id', '=', $form_link_id)
                ->where('form_questions.display_question', 'yes')
                ->orderBy('sort_order')
                ->select('*',
                    'questions.options_fr as options',
                    // 'questions.question_fr as question',
                    'questions.question_info_fr as question_info',
                    'questions.question_comment_fr as question_comment',
                    // 'question.title_fr as title',
                    'user_form_links.client_id',
                    'user_form_links.id as uf_id',
                    'questions.id as q_id',
                    'afs.id as afs_sec_id',
                    'afs.sec_num as afs_sec_num',
                    'afs.section_title_fr as admin_sec_title',
                    'cfs.id as cfs_sec_id',
                    'cfs.section_title as client_sec_title',
                    'user_form_links.user_id as u_id',
                    'user_form_links.expiry_time as form_expiry_time',
                    'sub_forms.parent_form_id as form_id')
                ->get();
        } 
        else {
            $form_info = DB::table('user_form_links')
                ->join('sub_forms', 'user_form_links.sub_form_id', '=', 'sub_forms.id')
                ->join('form_questions', 'sub_forms.parent_form_id', '=', 'form_questions.form_id')
                ->join('questions', 'form_questions.question_id', '=', 'questions.id')
                ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id')

                ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                
                ->where('user_form_links.form_link_id', '=', $form_link_id)
                ->where('form_questions.display_question', 'yes')
                ->orderBy('sort_order')
                ->select('*',
                    'user_form_links.client_id',
                    'user_form_links.id as uf_id',
                    'questions.id as q_id',
                    'afs.id as afs_sec_id',
                    'afs.sec_num as afs_sec_num',
                    'afs.section_title as admin_sec_title',
                    'cfs.id as cfs_sec_id',
                    'cfs.section_title as client_sec_title',
                    'user_form_links.user_id as u_id',
                    'user_form_links.expiry_time as form_expiry_time',
                    'sub_forms.parent_form_id as form_id')
                ->get();
        }

        $expiry_note = '';


        if (isset($form_info[0]) && strtotime(date('Y-m-d')) > strtotime($form_info[0]->form_expiry_time)) {
            if (Auth::check()) {
                if ((Auth::user()->role == 2 || Auth::user()->user_type == 1) && Auth::user()->client_id == $client_id) {
                    if ($form_info[0]->is_locked != '1') {
                        $expiry_note = 'The user failed to submit form before expiry time .';
                    }
                }
            } 
        }else if (isset($form_info[0]) && !$form_info[0]->is_accessible) {
            return view('user_form_not_accessible');
        }

        $ub_form_id  = DB::table('user_form_links')->where('form_link_id', $form_link_id)->first()->sub_form_id;
        $filled_info = DB::table('user_responses')
            ->join('questions', 'questions.id', '=', 'user_responses.question_id')
            ->where('user_responses.sub_form_id', $ub_form_id)
            ->select('user_responses.attachment','user_responses.sub_form_id', 'user_responses.rating', 'user_responses.admin_comment', 'question_key', 'question_response', 'question_id', 'additional_comment', 'additional_info', 'type', 'custom_case')
        ->get();

        $custom_responses = [];

        $question_key_index = [];
        foreach ($filled_info as $key => $user_response) {
            if ($user_response->type == 'mc'){
                $user_response->question_response = explode(', ', $user_response->question_response);
            }

            if ($user_response->custom_case == '1') {
                $custom_responses[$user_response->question_key] = $user_response->question_response;
            }

            $question_key_index[$user_response->question_key] =[

                'question_response' => $user_response->question_response,
                'question_id'       => $user_response->question_id,
                'question_comment'  => $user_response->additional_comment,
                'question_type'     => $user_response->type,
                'additional_resp'   => $user_response->additional_info,
                'rating'            => $user_response->rating,
                'admin_comment'     => $user_response->admin_comment,
                'attachment'        => $user_response->attachment,

            ];
        }

        // if ($id != 2 )
        {
            $custom_fields = '';
            $num_of_questions = 0;
            foreach ($form_info as $key => $questions) {
                if (trim($questions->type) == 'cc') {
                    $fields_info = json_decode($questions->question_info);
                    $field_html = '';
                    $is_asset_case = false;

                    $fill_custom_response = [];

                    if (isset($custom_responses[$questions->form_key])) {
                        $fill_custom_response = json_decode($custom_responses[$questions->form_key]);
                    }
                    if ($fields_info == null) {

                        $fields_info = [];
                    }

                    foreach ($fields_info as $fkey => $field) {
                        if ($fkey == 'mc') {
                            $value = '';

                            if (isset($field->data) && gettype($field->data) == 'string') {
                                $case_name = '';
                                if (strtolower($field->data) == 'not sure' || strtolower($field->data) == 'not applicable') {
                                    $case_name = 'case-name="Not Sure"';
                                    $mc_selected = 'es-selected';
                                }

                                $value = $field->data;
                                $mc_selected = '';

                                if (isset($fill_custom_response->mc)) {
                                    $filled_resp = $fill_custom_response->mc;
                                    $mc_selected = 'es-selected';
                                }

                                $field_html .= '<section class="options">';
                                $field_html .= '<ul id="easySelectable" class="easySelectable">';
                                $field_html .= '<li class="es-selectable ' . $mc_selected . '" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" ' . $case_name . ' value="' . $value . '" type="mc">' . ucfirst(strtolower(trim($value))) . '</li>';
                                $field_html .= '</ul></section>';

                            }

                        }

                        // added case for sc
                        if ($fkey == 'sc') {
                            $value = '';
                            $sc_fields = [];

                            if (gettype($field) == 'array') {
                                $sc_fields = $field;
                            } else {
                                $sc_fields[0] = $field;
                            }

                            $field_html .= '<section class="options">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';

                            foreach ($sc_fields as $sc_field) {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string') {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable') {
                                        $case_name = 'case-name="Not Sure"';
                                        $mc_selected = 'es-selected';
                                    }

                                    $value = $sc_field->data;
                                    $mc_selected = '';

                                    if (isset($fill_custom_response->sc)) {
                                        $filled_resp = $fill_custom_response->sc;
                                        if (strtolower($filled_resp) == strtolower($value)) {
                                            $mc_selected = 'es-selected';
                                        }
                                    }

                                    $field_html .= '<li class="es-selectable not-unselectable ' . $mc_selected . '" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" ' . $case_name . ' value="' . $value . '" type="sc">' . ucfirst(strtolower(trim($value))) . '</li>';

                                }
                            }

                            $field_html .= '</ul></section>';
                        }
                        // added case for sc

                        if ($fkey == 'dd') {
                            $field_comment = '';
                            if (isset($field->comment)) {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data)) {
                                if ($field->data == 'assets') {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('assets')->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                    $field_html .= '<select class="form form-control" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" type="dd" case-name="assets">';

                                    $selected = '';
                                    if (!isset($fill_custom_response->dd) || (isset($fill_custom_response->dd) && $fill_custom_response->dd == '')) {
                                        $selected = 'selected';
                                    }
                                    $field_html .= '<option value="Select Option" ' . $selected . '>Select Option</option>';
                                    foreach ($assets_query as $akey => $aquery) {
                                        $selected = '';
                                        if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $aquery->name) {
                                            $selected = 'selected';
                                        }
                                        $field_html .= '<option value="' . $aquery->name . '" ' . $selected . '>' . $aquery->name . '</option>';
                                    }
                                    $field_html .= '</select></div>';

                                }
                                if ($field->data == 'country_list') {
                                    $countries = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                    $field_html .= '---<select data-parentSection="' . $questions->question_num . '" class="form form-control" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name) {
                                        $selected = '';
                                        if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $country_name) {
                                            $selected = 'selected';
                                        }
                                        $field_html .= '<option value="' . $country_name . '" ' . $selected . '>' . $country_name . '</option>';
                                    }
                                    $field_html .= '</select></div>';
                                }

                                $num_of_questions++;
                            }
                        }

                        if ($fkey == 'qa') {
                            $qa_fields = [];

                            $field_name_array = '';
                            $multi_qa_case_str = '';
                            $json_format = false;

                            if (gettype($field) == 'array') {
                                $qa_fields = $field;
                                $field_name_array = '[]';
                                $multi_qa_case_str = 'case-name="multi-qa"';
                                $json_format = true;

                            } else {
                                $qa_fields[0] = $field;
                            }

                            $asset_attr_str = '';
                            if ($is_asset_case === true) {
                                $asset_attr_str = 'case-name="assets"';
                            }

                            $filled_resp = '';
                            if (isset($fill_custom_response->qa)) {
                                $filled_resp = $fill_custom_response->qa;
                            }

                            $qa_fill_index = 0;

                            foreach ($qa_fields as $qa_key => $qa_field) {
                                $field_comment = '';
                                if (isset($qa_field->comment)) {
                                    $field_comment = $qa_field->comment;
                                }

                                if (gettype($filled_resp) == 'array') {
                                    $tbox_val = isset($filled_resp[$qa_key]) ? ($filled_resp[$qa_key]) : ('');
                                } else {
                                    $tbox_val = $filled_resp;
                                }

                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                $field_html .= '<form>
                                                    <label></label>
                                                    <textarea data-parentSection=' . $questions->question_num . ' name="' . $questions->form_key . '_' . $questions->q_id . $field_name_array . '" q-id="' . $questions->q_id . '" ' . $multi_qa_case_str . ' ' . $asset_attr_str . ' rows="4" cols="50" custom="1" type="qa">' . $tbox_val . '</textarea>
                                                </form>
                                            </div>';

                            }

                            $num_of_questions++;
                        }

                    }

                    $field_html .= '<div id="perc-bar-' . $questions->q_id . '" class="barfiller hidden">
                                                <div class="tipWrap">
                                                    <span class="tip"></span>
                                                    </div>
                                                    <span class="fill" id="fill-bar-' . $questions->q_id . '" data-percentage="0"></span>
                                            </div>';

                    $form_info[$key]->custom_fields = $field_html;
                    $form_info[$key]->num_questions = $num_of_questions;
                }

            }
        }

        $user_info = DB::table('users')
            ->join('user_form_links', 'user_form_links.user_id', '=', 'users.id')
            ->where('user_form_links.form_link_id', '=', $form_link_id)
            ->select('*')->first();

        $hidden_pb = false;
        $ratings = DB::table('evaluation_rating')->get();
        if (isset($form_info[0]) && !empty($form_info[0])) {
            $form_type = DB::table('forms')->where('id', '=', $form_info[0]->form_id)->pluck('type')->first();
            if ($form_type == 'sar') {
                $hidden_pb = true;
            }
        }

        if (count($form_info) > 0) {
            return view('forms.audits.in_user_form_sec_wise', [
                'form_type'     => $form_type,
                'questions'     => $form_info,
                'hide_pb'       => $hidden_pb,
                'filled'        => $question_key_index,
                'user_info'     => $user_info,
                'title'         => !empty($form_info) ? ($form_info[0]->title) : ('title'),
                'heading'       => !empty($form_info) ? ($form_info[0]->title) : ('heading'),
                'expiry_note'   => $expiry_note,
                'eval_ratings'  => $ratings
            ]);
        } else {

            return redirect()->back()->with('top_bar_message', __('There is no questions in form'));
        }

    }

    public function in_users_show_form($form_link_id){
        $user_form_link_info  = DB::table('user_form_links')->where('form_link_id', $form_link_id)->first();
        if(empty($user_form_link_info)){
            return abort('404');
        }

        $expiry_note  = "";

        // if (strtotime(date('Y-m-d')) > strtotime($user_form_link_info->expiry_time)){
        //     if (Auth::user()->role == 2){
        //         if ($user_form_link_info->is_locked != '1') {
        //             $expiry_note = 'The user failed to submit form before expiry time .';
        //         }
        //     }
        // }
        // else if (!$user_form_link_info->is_accessible) {
        //     return view('user_form_not_accessible');
        // }

        $client_id  = $user_form_link_info->client_id;
        $user_id    = $user_form_link_info->user_id; 
        $subfirm_id = $user_form_link_info->sub_form_id;

        $form_details  = SubForm::with('form.group.sections.questions')->find($subfirm_id);
        foreach ($form_details->form->group->sections as $section) {
            foreach ($section->questions as  $question){
                $question->responses = UserResponse::where('sub_form_id', $subfirm_id)->where('question_id',  $question->id)->first();
            }
        }

        // return $form_details;
        $ratings       = DB::table('evaluation_rating')->where('owner_id', Auth::user()->client_id)->get();
        $asset         = DB::table('assets')->find($form_details->asset_id);

        if (count($form_details->form->group->sections) > 0) {
            return view('forms.audits.internal_user_form_filling', [
                'user_form_link_info' => $user_form_link_info,
                'form_details'        => $form_details,
                'eval_ratings'        => $ratings,
                'expiry_note'         => $expiry_note,
                'asset'               => $asset
            ]);

        } else {
            return redirect()->back()->with('top_bar_message', __('There is no questions in form'));
        }

    }

    // form submission for organization / company users
    public function ajax_int_user_submit_form(Request $req){
        $custom_case = 0;
        // if ($req->hasFile('img-' . $req->input('question-id'))) {
        //     $question_id = $req->input('question-id');
        //     $user_form_id = $req->input('user-form-id');
        //     $form_link_id = $req->input('form-link-id');
        //     $form_id = $req->input('form-id');
        //     $subform_id = $req->input('subform-id');
        //     $user_email = $req->input('email');
        //     $user_id = $req->input('user-id');
        //     $question_key = $req->input('question-key');
        //     $img_dir_path = "SAR_img_ids/$user_id/";
        //     $destinationpath = public_path($img_dir_path);
        //     $file = $req->file('img-' . $req->input('question-id'));
        //     $filename = $file->getClientOriginalName();
        //     $img_name = uniqid() . $filename;
        //     $file->move($destinationpath, $img_name);
        //     $file_path = $img_dir_path . $img_name;
            
        //     DB::table('user_responses')
        //         ->updateOrInsert(
        //             [
        //                 'form_id'           => $form_id,
        //                 'sub_form_id'       => $subform_id,
        //                 'question_id'       => $question_id,
        //                 'question_key'      => $question_key,
        //             ],
        //             ['user_form_id' => $user_form_id, 'user_id' => $user_id, 'user_email' => 0,  'question_response' => $file_path, "is_internal" => 1, "q_type" => 'im', 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
        //         );

        //     return;
        // }
        // else
        if($req->type == 'qa' || $req->type == 'dc' || $req->type == 'sc'){
            DB::table('user_responses')
                ->updateOrInsert(
                    [
                        'form_id'      => $req->form_id,
                        'sub_form_id'  => $req->sub_form_id,
                        'question_id'  => $req->q_id
                    ],
                    [
                        'question_key'      => $req->question_key,
                        'user_form_id'      => $req->user_form_id, 
                        'user_id'           => Auth::user()->id,   
                        'question_response' => $req->ansswer, 
                        'custom_case'       => $custom_case, 
                        'is_internal'       => 1, 
                        'type'              => 'audit',
                        "q_type"            => $req->type,
                        'created'           => date('Y-m-d H:i:s')
                    ]
                );
            return "ok";
        }
        elseif($req->type == 'mc'){
            DB::table('user_responses')
                ->updateOrInsert([
                    'form_id'      => $req->form_id,
                    'sub_form_id'  => $req->sub_form_id,
                    'question_id'  => $req->q_id,
                ],
                [
                    'question_key'      => 'mc-'.$req->q_id,
                    'user_form_id'      => $req->user_form_id, 
                    'user_id'           => Auth::user()->id,   
                    'question_response' => implode(',', $req->ansswer), 
                    'custom_case'       => $custom_case, 
                    'is_internal'       => 1, 
                    'type'              => 'audit',
                    "q_type"            => $req->type,
                    'created'           => date('Y-m-d H:i:s')
                ]
            );
            return "ok";
        }
    }

    public function in_add_attachment_to_question(Request $req){
        // return $req->all();  
        $custom_case = 0;
        if ($req->hasFile('img-' . $req->input('q_id'))){
            $img_dir_path       = "SAR_img_ids/".Auth::user()->id."/";
            $destinationpath    = public_path($img_dir_path);
            $file               = $req->file('img-' . $req->input('q_id'));
            $filename           = $file->getClientOriginalName();
            $img_name           = uniqid() . $filename;
            $file_path          = $img_dir_path . $img_name;
            $file->move($destinationpath, $img_name);
            
            DB::table('user_responses')->updateOrInsert(
                [
                    'form_id'      => $req->form_id,
                    'sub_form_id'  => $req->sub_form_id,
                    'question_id'  => $req->q_id,
                ],
                [
                    'user_form_id'      => $req->user_form_id, 
                    'user_id'           => Auth::user()->id,   
                    'attachment'        => $file_path, 
                    'type'              => 'audit',
                    'custom_case'       => $custom_case, 
                    'is_internal'       => 1, 
                    'created'           => date('Y-m-d H:i:s')
                ]
            );

            return "ok";
        }
        return "file not Attaching";

    }

    // get answered Question count and total questions  
    public function get_question_count($group, $sub_form_id){
        try {
            $section_ids         = array_unique(GroupSection::where('group_id', $group)->pluck('id')->toArray());        
            $total_questions     = Question::whereIn('section_id', $section_ids)->count();
            $responded_questions = DB::table('user_responses')->whereNotNull('question_response')->where('sub_form_id', $sub_form_id)->count();
            $added_ratting       = DB::table('user_responses')->where('sub_form_id', $sub_form_id)->where('rating', "!=", '0')->count();
            $week_questions      = DB::table('user_responses')->where('sub_form_id', $sub_form_id)->whereIn('rating', [3,4])->count();
            $remediation_added   = DB::table('remediation_plans')->where('sub_form_id', $sub_form_id)->count();
            $remediation_plan    = DB::table('remediation_plans')->where('sub_form_id', $sub_form_id)->first();
            $remediation_plan    = DB::table('remediation_plans')->where('sub_form_id', $sub_form_id)->first();


            $sections = GroupSection::where('group_id', $group)->get();   

            foreach ($sections as $section) {
                $question_ids                 = Question::where('section_id', $section->id)->pluck('id');
                $section->total_questions     = Question::where('section_id', $section->id)->count();
                $section->responded_questions = DB::table('user_responses')->whereNotNull('question_response')->whereIn('question_id', $question_ids)->where('sub_form_id', $sub_form_id)->where('type', 'audit')->count();

                $section->rated_questions = DB::table('user_responses')->where('rating', "!=", 0)->whereIn('question_id', $question_ids)->where('sub_form_id', $sub_form_id)->where('type', 'audit')->count();
            }
            
            $data = [
                'total_questions'       => $total_questions,
                'responded_questions'   => $responded_questions,
                'added_ratting'         => $added_ratting,
                'week_questions'        => $week_questions,
                'remediation_added'     => $remediation_added,
                'remediation_plan'      => $remediation_plan,
                'sections'              => $sections,
                'section_ids'           => $section_ids
            ];
            return response()->json($data, 200);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    // get answered Question count and total questions  

    public function ex_users_show_form_old($client_id, $user_id, $client_email, $subform_id, $user_email, $date_time){

        $accoc_info = [
            'client_id' => $client_id,
            'user_id' => $user_id,
            'client_email' => $client_email,
            'subform_id' => $subform_id,
            'user_email' => $user_email,
            'date_time' => $date_time];
        $form_link_id = implode('/', $accoc_info);

        $accoc_info = [
            'client_id'     => $client_id,
            'user_id'       => $user_id,
            'client_email'  => base64_decode($client_email),
            'subform_id'    => $subform_id,
            'user_email'    => base64_decode($user_email),
            'date_time'     => base64_decode($date_time)
        ];

        $client_id = DB::table('user_form_links')->where('user_form_links.form_link', '=', $form_link_id)->pluck('client_id')->first();
        
        if (empty($client_id)) {
            return abort('404');
        }

        if (session('locale') == 'fr') {
            $form_info = DB::table('user_form_links')
                ->join('sub_forms', 'user_form_links.sub_form_id', '=', 'sub_forms.id')
                ->join('form_questions', 'sub_forms.parent_form_id', '=', 'form_questions.form_id')
                ->join('questions', 'form_questions.question_id', '=', 'questions.id')
                ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id')
                ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                ->where('user_form_links.form_link', '=', $form_link_id)
                ->orderBy('sort_order')
                ->select('*',
                    'questions.options_fr as options',
                    'questions.question_info_fr as question_info',
                    'questions.question_comment_fr as question_comment',
                    'form_questions.form_id as form_id',
                    'user_form_links.id as uf_id',
                    'afs.id as afs_sec_id',
                    'afs.sec_num as afs_sec_num',
                    'afs.section_title_fr as admin_sec_title',
                    'cfs.id as cfs_sec_id',
                    'cfs.section_title as client_sec_title',
                    'questions.id as q_id',
                    'user_form_links.user_email as u_email',
                    'user_form_links.expiry_time as form_expiry_time')
                ->get();

            
        } else {
            $form_info = DB::table('user_form_links')
                ->join('sub_forms', 'user_form_links.sub_form_id', '=', 'sub_forms.id')
                ->join('form_questions', 'sub_forms.parent_form_id', '=', 'form_questions.form_id')
                ->join('questions', 'form_questions.question_id', '=', 'questions.id')
                ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id')
                ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                ->where('user_form_links.form_link', '=', $form_link_id)
                ->orderBy('sort_order')
                ->select('*',
                    'form_questions.form_id as form_id',
                    'user_form_links.id as uf_id',
                    'afs.id as afs_sec_id',
                    'afs.sec_num as afs_sec_num',
                    'afs.section_title as admin_sec_title',
                    'cfs.id as cfs_sec_id',
                    'cfs.section_title as client_sec_title',
                    'questions.id as q_id',
                    'user_form_links.user_email as u_email',
                    'user_form_links.expiry_time as form_expiry_time')
                ->get();
        }
        if (empty($form_info)) {
            return abort('404');
        }

        $expiry_note = '';

        if (isset($form_info[0]) && strtotime(date('Y-m-d')) > strtotime($form_info[0]->form_expiry_time)) {
            if (Auth::check()) {
                if ((Auth::user()->role == 2 || Auth::user()->user_type == 1) && Auth::user()->client_id == $client_id) {
                    if ($form_info[0]->is_locked != '1') {
                        $expiry_note = __('The user failed to submit form before expiry time.');
                    }
                }
            }
        } else if (isset($form_info[0]) && !$form_info[0]->is_accessible) {
            return view('user_form_not_accessible');
        }
        
        $sub_form_id  = DB::table('user_form_links')->where('form_link', $form_link_id)->first()->sub_form_id;
        $filled_info = DB::table('user_responses')
            ->join('questions', 'questions.id', '=', 'user_responses.question_id')
            ->where('user_responses.sub_form_id', $sub_form_id)
            ->select('user_responses.attachment', 'user_responses.sub_form_id', 'user_responses.rating','question_key', 'question_response', 'question_id', 'additional_comment', 'additional_info', 'type', 'custom_case')
        ->get();


        // return $filled_info;
        $custom_responses = [];

        $question_key_index = [];

        foreach ($filled_info as $key => $user_response) {
            if ($user_response->type == 'mc') {
                $user_response->question_response = explode(', ', $user_response->question_response);
            }

            if ($user_response->custom_case == '1') {
                $custom_responses[$user_response->question_key] = $user_response->question_response;
            }

            $question_key_index[$user_response->question_key] =
                [
                'question_response'     => $user_response->question_response,
                'question_id'           => $user_response->question_id,
                'question_comment'      => $user_response->additional_comment,
                'question_type'         => $user_response->type,
                'additional_resp'       => $user_response->additional_info,
                'attachment'            => $user_response->attachment
            ];
        }

        //if ($id != 2)
        {

            $custom_fields = '';
            $num_of_questions = 0;

            foreach ($form_info as $key => $questions) {

                //echo '<pre>';print_r($questions);exit;
                //echo $questions->q_id."<br>";
                // dd($form_info);

                if (trim($questions->type) == 'cc') {
                    // dd($questions);
                    //echo '<pre>';print_r($questions);exit;
                    //echo $questions->type."--";

                    //echo 'inside';exit;
                    $fields_info = json_decode($questions->question_info);
                    $field_html = '';
                    $is_asset_case = false;

                    $fill_custom_response = [];

                    if (isset($custom_responses[$questions->form_key])) {
                        $fill_custom_response = json_decode($custom_responses[$questions->form_key]);
                        //     echo "<pre>";
                        //   print_r($fill_custom_response);
                        //   echo "</pre>";
                        //   exit;
                    }
                    // echo '<pre>';print_r($fields_info);exit;
                    // dd('walala');
                    if ($fields_info == null) {

                        $fields_info = [];
                    }

                    foreach ($fields_info as $fkey => $field) {

                        if ($fkey == 'mc') {
                            // dd($field);
                            $value = '';
                            $sc_fields = [];

                            if (gettype($field) == 'array') {
                                $sc_fields = $field;
                            } else {
                                $sc_fields[0] = $field;
                            }

                            $field_html .= '<section class="options">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';

                            foreach ($sc_fields as $sc_field) {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string') {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable') {
                                        $case_name = 'case-name="Not Sure"';
                                        //$mc_selected = 'es-selected';
                                    }

                                    $value = $sc_field->data;
                                    $mc_selected = '';

                                    if (isset($fill_custom_response->sc)) {
                                        $filled_resp = $fill_custom_response->sc;
                                        if (strtolower($filled_resp) == strtolower($value)) {
                                            $mc_selected = 'es-selected';
                                        }
                                    }

                                    $field_html .= '<li data-parentSection=' . $questions->question_num . ' class="es-selectable not-unselectable ' . $mc_selected . '" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" ' . $case_name . ' value="' . $value . '" type="mc">' . ucfirst(strtolower(trim($value))) . '</li>';

                                }
                            }

                            $field_html .= '</ul></section>';
                            // dd($field_html);
                        }

                        // added case for sc
                        //echo 'asdf';exit;
                        if ($fkey == 'sc') {
                            $value = '';
                            $sc_fields = [];

                            if (gettype($field) == 'array') {
                                $sc_fields = $field;
                            } else {
                                $sc_fields[0] = $field;
                            }

                            $field_html .= '<section class="options">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';

                            foreach ($sc_fields as $sc_field) {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string') {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable') {
                                        $case_name = 'case-name="Not Sure"';
                                        //$mc_selected = 'es-selected';
                                    }

                                    $value = $sc_field->data;
                                    $mc_selected = '';

                                    if (isset($fill_custom_response->sc)) {
                                        $filled_resp = $fill_custom_response->sc;
                                        if (strtolower($filled_resp) == strtolower($value)) {
                                            $mc_selected = 'es-selected';
                                        }
                                    }

                                    $field_html .= '<li data-parentSection=' . $questions->question_num . ' class="es-selectable not-unselectable ' . $mc_selected . '" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" ' . $case_name . ' value="' . $value . '" type="sc">' . ucfirst(strtolower(trim($value))) . '</li>';

                                }
                            }

                            $field_html .= '</ul></section>';
                        }

                        if ($fkey == 'dd') {
                            //echo 'aaaaaaaaa';exit;
                            $field_comment = '';
                            if (isset($field->comment)) {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data)) {
                                if ($field->data == 'assets') {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('assets')->where('client_id', $client_id)->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6>' . $field_comment . '</h6>';
                                    // dd($questions);
                                    // //print_r($assets_query);exit;
                                    // if($questions->question == 'What is the name of the asset you are assessing?' )
                                    // {
                                    $field_html .= '<select data-parentSection=' . $questions->question_num . ' class="selectpicker form form-control" multiple data-live-search="true" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" type="dd" case-name="assets">';
                                    // }
                                    // else{
                                    //     $field_html .= '<select data-parentSection='.$questions->question_num.' class="selectpicker form form-control" multiple data-live-search="true" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" type="dd" case-name="assets">';

                                    // }

                                    $selected = '';

                                    //$multi_select = $fill_custom_response->dd;

                                    //$multi_data=$multi_select;
                                    // dd($multi_data[0]);

                                    if (!isset($fill_custom_response->dd) || (isset($fill_custom_response->dd) && $fill_custom_response->dd == 'Select Option')) {
                                        $selected = 'selected';
                                    }

                                    $field_html .= '<option value="Select Option" ' . $selected . '>Select Option</option>';
                                    foreach ($assets_query as $akey => $aquery) {
                                        $selected = '';

                                        // if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $aquery->name)
                                        // {
                                        //     $selected = 'selected';
                                        // }
                                        $field_html .= '<option value="' . $aquery->name . '" ' . $selected . '>' . $aquery->name . '</option>';

                                    }
                                    $field_html .= '</select></div>';

                                }
                                if ($field->data == 'country_list') {
                                    $countries = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6>' . $field_comment . '</h6>';
                                    $field_html .= '<select data-parentSection="' . $questions->question_num . '"  class="form form-control" name="' . $questions->form_key . '_' . $questions->q_id . '" q-id="' . $questions->q_id . '" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name) {
                                        $selected = '';
                                        if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $country_name) {
                                            $selected = 'selected';
                                        }
                                        $field_html .= '<option value="' . $country_name . '" ' . $selected . '>' . $country_name . '</option>';
                                    }
                                    $field_html .= '</select></div>';
                                    // dd($field_html);
                                }

                                $num_of_questions++;
                            }
                            //echo $field_html;exit;
                        }

                        if ($fkey == 'qa') {
                            //  echo "<pre>";
                            //  print_r($field);
                            //  echo "</pre>";
                            //  exit;

                            $qa_fields = [];

                            $field_name_array = '';
                            $multi_qa_case_str = '';
                            $json_format = false;

                            if (gettype($field) == 'array') {
                                $qa_fields = $field;
                                $field_name_array = '[]';
                                $multi_qa_case_str = 'case-name="multi-qa"';
                                $json_format = true;

                            } else {
                                $qa_fields[0] = $field;
                            }

                            $asset_attr_str = '';
                            if ($is_asset_case === true) {
                                $asset_attr_str = 'case-name="assets"';
                            }

                            $filled_resp = '';
                            if (isset($fill_custom_response->qa)) {
                                $filled_resp = $fill_custom_response->qa;
                            }

                            $qa_fill_index = 0;

                            foreach ($qa_fields as $qa_key => $qa_field) {

                                $field_comment = '';
                                if (isset($qa_field->comment)) {
                                    $field_comment = $qa_field->comment;
                                }

                                if (gettype($filled_resp) == 'array') {
                                    $tbox_val = isset($filled_resp[$qa_key]) ? ($filled_resp[$qa_key]) : ('');
                                } else {
                                    $tbox_val = $filled_resp;
                                }

                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                $field_html .= '<form>
                                        <label></label>
                                        <textarea data-parentSection=' . $questions->question_num . '  name="' . $questions->form_key . '_' . $questions->q_id . $field_name_array . '" q-id="' . $questions->q_id . '" ' . $multi_qa_case_str . ' ' . $asset_attr_str . ' rows="4" cols="50" custom="1" type="qa" id="qa_nameabc" onfocusout="hideFields()">' . $tbox_val . '</textarea>


                                    </form>
                                </div>';
                                // dd($questions);
                                if (($questions->question_num == 1.1 || $questions->question_num == 4.1 || $questions->question_num == 6.1) && ($questions->question == 'What is the name of the asset you are assessing?' || $questions->question == 'What assets are used to collect store and process the data' || $questions->question == 'What assets are used to process the data for this activity?')) {
                                    $field_html .= '<div class="full_assetz">';
                                    $field_html .= '<form>
                                    <label>Hosting Type</label>
                                    <input type="text" id="tesst" name="testtt" class="form-data ssd" onfocusout="update_form_data_request()">
                                    <label>Country</label>
                                    <input type="text" name="country" id="tesst_c" class="form-data ssd" onfocusout="update_form_data_request()">
                                    </form>
                                    </div>';

                                }

                            }

                            $num_of_questions++;
                        }

                    }

                    $field_html .= '<div id="perc-bar-' . $questions->q_id . '" class="barfiller hidden">
                                    <div class="tipWrap">
                                        <span class="tip"></span>
                                        </div>
                                        <span class="fill" id="fill-bar-' . $questions->q_id . '" data-percentage="0"></span>
                                </div>';

                    $form_info[$key]->custom_fields = $field_html;
                    $form_info[$key]->num_questions = $num_of_questions;
                }

            }

        }

        $user_info = DB::table('users')
            ->join('user_form_links', 'user_form_links.user_id', '=', 'users.id')
            ->where('user_form_links.form_link_id', '=', $form_link_id)
            ->select('*')
        ->first();

       
        $hidden_pb = false;
        if (isset($form_info[0]) && !empty($form_info[0])) {
            $form_type = DB::table('forms')->where('id', '=', $form_info[0]->form_id)->pluck('type')->first();
            if ($form_type == 'sar') {
                $hidden_pb = true;
            }
        }
        if (count($form_info) > 0) {
            return view('forms.audits.ex_user_form_sec_wise', ['questions' => $form_info,
                'hide_pb' => $hidden_pb,
                'client_id' => $client_id,
                'filled' => $question_key_index,
                'accoc_info' => $accoc_info,
                'user_info' => $user_info,
                'expiry_note' => $expiry_note]);
        } else {
            return redirect()->back()->with('top_bar_message', __('There is no questions in form'));
        }

    }

    public function ex_users_show_form($client_id, $user_id, $client_email, $subform_id, $user_email, $date_time){
        $accoc_info = [
            'client_id' => $client_id,
            'user_id' => $user_id,
            'client_email' => $client_email,
            'subform_id' => $subform_id,
            'user_email' => $user_email,
            'date_time' => $date_time];
        $form_link_id = implode('/', $accoc_info);

        $accoc_info = [
            'client_id'     => $client_id,
            'user_id'       => $user_id,
            'client_email'  => base64_decode($client_email),
            'subform_id'    => $subform_id,
            'user_email'    => base64_decode($user_email),
            'date_time'     => base64_decode($date_time)
        ];

        $user_form_link_info = DB::table('user_form_links')->where('user_form_links.form_link', $form_link_id)->first();
        if (empty($user_form_link_info)) {
            return abort('404');
        }
        $expiry_note    = "";
        if (strtotime(date('Y-m-d')) > strtotime($user_form_link_info->expiry_time)){
            if (Auth::user()->role == 2){
                if ($user_form_link_info->is_locked != '1') {
                    $expiry_note = 'The user failed to submit form before expiry time .';
                }
            }
        }
        else if (!$user_form_link_info->is_accessible) {
            return view('user_form_not_accessible');
        }
        $client_id  = $user_form_link_info->client_id;
        $user_email = $user_form_link_info->user_email; 
        $subfirm_id = $user_form_link_info->sub_form_id;


        // $form_details   = SubForm::with('form.group.sections.questions.responses')->find($subfirm_id);
        $form_details  = SubForm::with('form.group.sections.questions')->find($subfirm_id);
        foreach ($form_details->form->group->sections as $section) {
            foreach ($section->questions as  $question){
                $question->responses = UserResponse::where('sub_form_id', $subfirm_id)->where('question_id',  $question->id)->first();
            }
        }
        if (count($form_details->form->group->sections) > 0) {
            $ratings = DB::table('evaluation_rating')->where('owner_id', $user_form_link_info->client_id)->get();
            $asset   = DB::table('assets')->find($form_details->asset_id);
            return view('forms.audits.external_user_form_filling', [
                'user_form_link_info' => $user_form_link_info,
                'form_details'        => $form_details,
                'eval_ratings'        => $ratings,
                'expiry_note'         => $expiry_note,
                'asset'               => $asset
            ]);
        } else {
            return redirect()->back()->with('top_bar_message', __('There is no questions in form'));
        }

    }

    public function ajax_ext_user_submit_form(Request $req){
        $custom_case = 0;
        // return $req->all();
        // if ($req->hasFile('img-' . $req->input('question-id'))) {
        //     $question_id = $req->input('question-id');
        //     $user_form_id = $req->input('user-form-id');
        //     $form_link_id = $req->input('form-link-id');
        //     $form_id = $req->input('form-id');
        //     $subform_id = $req->input('subform-id');
        //     $user_email = $req->input('user_email');
        //     $user_id = $req->input('user-id');
        //     $question_key = $req->input('question-key');
        //     $img_dir_path = "SAR_img_ids/$user_id/";
        //     $destinationpath = public_path($img_dir_path);
        //     $file = $req->file('img-' . $req->input('question-id'));
        //     $filename = $file->getClientOriginalName();
        //     $img_name = uniqid() . $filename;
        //     $file->move($destinationpath, $img_name);
        //     $file_path = $img_dir_path . $img_name;
            
        //     DB::table('user_responses')
        //         ->updateOrInsert(
        //             [
        //                 'form_id'           => $form_id,
        //                 'sub_form_id'       => $subform_id,
        //                 'question_id'       => $question_id,
        //                 'question_key'      => $question_key,
        //             ],
        //             ['user_form_id' => $user_form_id, 'user_id' => 0, 'user_email' => $user_email,  'question_response' => $file_path, "is_internal" => 0, "q_type" => 'im', 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
        //         );

        //     return "file uploaded";
        // }
        // else
        if($req->type == 'qa' || $req->type == 'dc' || $req->type == 'sc'){
            DB::table('user_responses')
                ->updateOrInsert(
                    [
                        'form_id'      => $req->form_id,
                        'sub_form_id'  => $req->sub_form_id,
                        'question_id'  => $req->q_id,
                    ],
                    [
                        'question_key'      => $req->type."-".$req->q_id,
                        'user_form_id'      => $req->user_form_id, 
                        'user_email'        => $req->user_email,   
                        'question_response' => $req->ansswer, 
                        'custom_case'       => $custom_case, 
                        'type'              => 'audit',
                        'is_internal'       => 0, 
                        "q_type"            => $req->type,
                        'created'           => date('Y-m-d H:i:s')
                    ]
                );
            return "Answer saved";
        }
        elseif($req->type == 'mc'){
            DB::table('user_responses')
                ->updateOrInsert(
                    [
                        'form_id'      => $req->form_id,
                        'sub_form_id'  => $req->sub_form_id,
                        'question_id'  => $req->q_id,
                        
                    ],
                    [
                        'question_key'      => $req->type."-".$req->q_id,
                        'user_form_id'      => $req->user_form_id, 
                        'user_email'        => $req->user_email,  
                        'type'              => 'audit',
                        'question_response' => implode(',', $req->ansswer), 
                        'custom_case'       => $custom_case, 
                        'is_internal'       => 0, 
                        "q_type"            => $req->type,
                        'created'           => date('Y-m-d H:i:s')
                    ]
                );
            return "MC added uccessfully";
        }
    }

    public function ex_add_attachment_to_question(Request $req){
        if ($req->hasFile('img-' . $req->input('q_id'))){
            $question_id            = $req->input('q_id');
            $user_form_id           = $req->input('user_form_id');
            $form_link_id           = $req->input('form-link-id');
            $form_id                = $req->input('form_id');
            $sub_form_id            = $req->input('sub_form_id');
            $user_email             = $req->input('user_email');
            $img_dir_path           = "SAR_img_ids/".uniqid()."/";
            $destinationpath        = public_path($img_dir_path);
            $file                   = $req->file('img-' . $req->input('q_id'));
            $filename               = $file->getClientOriginalName();
            $img_name               = uniqid() . $filename;
            $file_path              = $img_dir_path . $img_name;
            $file->move($destinationpath, $img_name);
            
            DB::table('user_responses')->updateOrInsert(
                [
                    'form_id'           => $form_id,
                    'sub_form_id'       => $sub_form_id,
                    'question_id'       => $question_id,
                ],
                [
                    'user_form_id'      => $user_form_id, 
                    'user_email'        => $user_email, 
                    'user_id'           => 0,  
                    'type'              => 'audit',
                    'attachment'        => $file_path, 
                    "is_internal"       => 0, 
                    'custom_case'       => 1, 
                    'created'           => date('Y-m-d H:i:s')
                ]
            );

            return "ok";
        }

        return "file not Attaching";

    }

    public function show_success_msg(){
        $template = 'users.ex_user_app';
        $user_type = 'ex';
        $user_role = '';
        $user_type = '';
        $is_super = '';

        if (Auth::check()) {
            $template = 'admin.client.client_app';
            $user_type = 'in';
            $user_role = Auth::user()->role;
            $is_super = Auth::user()->user_type;

        }

        return view('forms.form_submit_msg', compact('template', 'user_type', 'user_role', 'is_super'));
    }

    public function ajax_lock_user_audit_form(Request $request){
        $client_id = null;
        if ($request->client_id) {
            $client_id = $request->client_id;
        } else {

            $client_id = auth()->user()->client_id;
        }
        $form_link_id = $request->input('sub_form_id');
        $user_type = $request->input('user_type');
        $link_id_field = '';
        

        switch ($user_type) {
            case 'ex':
                $link_id_field = 'sub_form_id';
                $update = DB::table('user_form_links')->where('sub_form_id', $request->sub_form_id)->where('user_email', $request->user_email)
                ->update([
                    'is_locked' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                break;
            case 'in':
                $link_id_field = 'sub_form_id';
                $update = DB::table('user_form_links')->where('sub_form_id', $request->sub_form_id)->where('user_id', $request->user_id)
                ->update([
                    'is_locked' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                break;
        }
         UserFormLink::where("sub_form_id", $form_link_id)
                ->where('is_locked', 0)
                ->update(["is_locked" => 1]);
        return $update;
    }

    public function audit_assignees($form_id = 1){
        if (Auth::user()->role != 1) {
            return abort('404');
        }

        $client_role = 4;
        $client_list = DB::table('users')->where('role', $client_role)->get();

        /* assigned forms
        SELECT *
        FROM `forms`
        JOIN  client_forms ON forms.id = client_forms.form_id
        WHERE forms.id = 2
         */

        $selected_form = DB::table('forms')
            ->where('id', '=', $form_id)
            ->first();

        $forms_info = DB::table('forms')
            ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
            ->where('forms.id', '=', $form_id)
            ->select('*')
            ->get();
        
        
        //echo 'LIST OF FORM ASSIGNED COMPANIES.......';
        //echo '<pre>';print_r($forms_info);exit;
        $assigned_client_ids = [];

        foreach ($forms_info as $form) {
            $assigned_client_ids[] = $form->client_id;
        }

        return view('forms.audits.audit_forms_assignee_list', ['user_type' => 'admin', 'selected_form' => $selected_form,
            'assigned_client_ids' => $assigned_client_ids,
            'form_id' => $form_id,
            'client_list' => $client_list]);
    }
}
