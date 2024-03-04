<?php

namespace App\Http\Controllers;
use App\Country;
use App\User;
use App\Form;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Lang;
use Mail;
use Swift_Mailer;
use Swift_SmtpTransport;
use \Carbon\Carbon;

class Forms extends Controller{

    public function data_classification(){
        $data = DB::table('data_classifications')
            ->where('organization_id', Auth::user()->client_id)
            ->get();
        return view('forms/data_classication', compact('data'));
    }
    
    public function __construct(){}

    public function edit_classification($id){

        $form = DB::table('data_classifications')->where('id', $id)->first();
        //  echo "<pre>";print_r($form);exit;
        return view('forms.front_edit_classification', compact('form'));
    }

    public function edit_classification_act(Request $request, $id){

        $this->validate($request, [
            'classification_name_en' => 'required',
            'classification_name_fr' => 'required',

        ],
            [
                'classification_name_en.required' => __('Form English name cannot be empty'),
                'classification_name_fr.required' => __('Form French  name cannot be empty'),

            ]

        );
        $title_en = $request->classification_name_en;
        $title_fr = $request->classification_name_fr;
        $id = $request->id;

        DB::table('data_classifications')
            ->where('id', $id)
            ->update(['classification_name_en' => $title_en, 'classification_name_fr' => $title_fr]);

        return redirect('/front/data-classification');

    }

    public function ajax_update_form_section_heading(Request $request){
        // dd($request->all());
        if ($request->input('is_admin') && $request->input('is_admin') == 1) {

            if ($request->old_title != $request->title || $request->old_title_fr != $request->title_fr) {

                $title_update = DB::table('admin_form_sections')
                    ->where('form_id', $request->form_id)
                    ->where('id', '!=', $request->form_section_id)
                    ->where('section_title', $request->title)->count();

                $title_fr_update = DB::table('admin_form_sections')
                    ->where('form_id', $request->form_id)
                    ->where('id', '!=', $request->form_section_id)
                    ->where('section_title_fr', $request->title_fr)->count();

                if ($title_update > 0) {
                    // return redirect()->back()->with('message' , __('English Section Title Already Exists, Please provide a unique section title  '));
                    return response()->json([
                        "status" => 400,
                        "msg" => "English Section Title Already Exists, Please provide a unique section title",
                    ]);
                } else if ($title_fr_update > 0) {
                    //   return redirect()->back()->with('message' , __('French Section Title Already Exists, Please provide a unique section title  '));
                    return response()->json([
                        "status" => 400,
                        "msg" => "French Section Title Already Exists, Please provide a unique section title ",
                    ]);
                } else {
                    DB::table('admin_form_sections')
                        ->where('form_id', $request->form_id)
                        ->where(['id' => $request->input('form_section_id')])
                        ->update([
                            'section_title' => $request->input('title'),
                            'section_title_fr' => $request->input('title_fr'),
                        ]);

                    return response()->json(['status' => 200]);

                }
            } else {
                return response()->json([
                    "status" => 200,

                ]);
            }
        } else {
            DB::table('client_form_sections')
                ->updateOrInsert([
                    'client_id' => $request->input('user_id'),
                    'admin_form_sec_id' => $request->input('form_section_id'),
                    'form_id' => $request->input('form_id'),
                ],
                    [
                        'section_title' => $request->input('title'),
                        'client_id' => $request->input('user_id'),
                        'admin_form_sec_id' => $request->input('form_section_id'),
                        'form_id' => $request->input('form_id'),
                        'updated_by' => $request->input('updated_by'),
                    ]);
        }
    }

    // form view only with fields disabled
    public function view_form($id = 1){

        // in case if user is not admin, check if the form is assigned to company user or its related users
        if (Auth::user()->role != 1) {
            $client_id = Auth::user()->client_id;

            // get assignee list

            /*
            SELECT * FROM client_forms WHERE client_id = 76 AND form_id = 2
             */

            $form_assignee = DB::table('client_forms')
                ->where('client_id', '=', $client_id)
                ->where('form_id', '=', $id)
                ->first();

            // form is not assigned, don't allow the form with request id to view
            if (empty($form_assignee)) {
                return abort('404');
            }

        }

        $form_info = DB::table('forms')
            ->join('form_questions', 'forms.id', '=', 'form_questions.form_id')
            ->join('questions', 'form_questions.question_id', '=', 'questions.id')
            ->where('forms.id', '=', $id)
            ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id');

        if (Auth::user()->role == 1) {

            $form_info = $form_info

                ->select('*', 'afs.id as afs_sec_id', 'afs.section_title as admin_sec_title', 'questions.question_section_id as q_sec_id')
                ->orderBy('form_questions.sort_order')
                ->get();

            $user_type = 'admin';
            $parent_template = 'admin.layouts.admin_app';
        } elseif (Auth::user()->role == 2) {
            // LEFT JOIN client_form_sections as cfs ON (cfs.admin_form_sec_id = afs.id AND cfs.client_id = 23) -- only in case of client

            // $client_id = Auth::id();
            $client_id = Auth::user()->client_id;
            if (session('locale') == 'fr') {
                $form_info = $form_info

                    ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                    ->select('*',
                        'questions.options_fr as options',
                        'questions.question_info_fr as question_info',
                        'questions.question_comment_fr as question_comment',
                        'afs.id as afs_sec_id',
                        'afs.section_title_fr as admin_sec_title',
                        'cfs.id as cfs_sec_id',
                        'cfs.section_title as client_sec_title',
                        'questions.question_section_id as q_sec_id')
                    ->orderBy('form_questions.sort_order')

                    ->get();
            } else {

                $form_info = $form_info

                    ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                    ->select('*',
                        'questions.options',
                        'questions.question_info',
                        'questions.question_comment',
                        'afs.id as afs_sec_id',
                        'afs.section_title as admin_sec_title',
                        'cfs.id as cfs_sec_id',
                        'cfs.section_title as client_sec_title',
                        'questions.question_section_id as q_sec_id')
                    ->orderBy('form_questions.sort_order')

                    ->get();
            }

            $user_type = 'client';
            $parent_template = 'admin.client.client_app';
        } elseif (Auth::user()->role == 3) {
            $client_id = Auth::user()->client_id;

            // $form_info = $form_info
            //             ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = '.$client_id))
            //             ->select('*',
            //                      'afs.id as afs_sec_id',
            //                      'afs.section_title as admin_sec_title',
            //                      'cfs.id as cfs_sec_id',
            //                      'cfs.section_title as client_sec_title',
            //                      'questions.question_section_id as q_sec_id')
            //             ->get();
            if (session('locale') == 'fr') {
                $form_info = $form_info

                    ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                    ->select('*',
                        'questions.options_fr as options',
                        'questions.question_info_fr as question_info',
                        'questions.question_comment_fr as question_comment',
                        'afs.id as afs_sec_id',
                        'afs.section_title_fr as admin_sec_title',
                        'cfs.id as cfs_sec_id',
                        'cfs.section_title as client_sec_title',
                        'questions.question_section_id as q_sec_id')

                    ->get();
            } else {

                $form_info = $form_info

                    ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                    ->select('*',
                        'questions.options',
                        'questions.question_info',
                        'questions.question_comment',
                        'afs.id as afs_sec_id',
                        'afs.section_title as admin_sec_title',
                        'cfs.id as cfs_sec_id',
                        'cfs.section_title as client_sec_title',
                        'questions.question_section_id as q_sec_id')

                    ->get();
            }

            $user_type = 'reg_user';
            $parent_template = 'admin.client.client_app';
        } else {
            $client_id = Auth::user()->client_id;
            if (session('locale') == 'fr') {
                $form_info = $form_info
                    ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                    ->select('*',
                        'questions.options_fr as options',
                        'questions.question_info_fr as question_info',
                        'questions.question_comment_fr as question_comment',
                        'afs.id as afs_sec_id',
                        'afs.section_title_fr as admin_sec_title',
                        'cfs.id as cfs_sec_id',
                        'cfs.section_title as client_sec_title',
                        'questions.question_section_id as q_sec_id')

                ->get();
            } else {

                $form_info = $form_info

                    ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                    ->select('*',
                        'questions.options',
                        'questions.question_info',
                        'questions.question_comment',
                        'afs.id as afs_sec_id',
                        'afs.section_title as admin_sec_title',
                        'cfs.id as cfs_sec_id',
                        'cfs.section_title as client_sec_title',
                        'questions.question_section_id as q_sec_id')

                ->get();
            }

            $user_type = 'ext_user';
            $parent_template = 'admin.client.client_app';
        }
        //echo '<pre>';print_r($form_info);exit;
        if ($id != 2) {
            $custom_fields = '';

            foreach ($form_info as $key => $questions) {

                if (trim($questions->type) == 'cc') {
                    $fields_info = json_decode($questions->question_info);

                    $field_html = '';
                    $is_asset_case = false;

                    if ($fields_info == null) {

                        $fields_info = [];
                    }
                    // dd($fields_info);
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
                                $field_html .= '<li class="es-selectable ' . $mc_selected . '" name="' . $questions->form_key . '" q-id="" custom="1" ' . $case_name . ' value="' . $value . '" type="mc">' . $value . '</li>';
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
                                        //$mc_selected = 'es-selected';
                                    }

                                    $value = $sc_field->data;
                                    $mc_selected = '';

                                    $field_html .= '<li class="es-selectable not-unselectable ' . $mc_selected . '" name="' . $questions->form_key . '" q-id="" custom="1" ' . $case_name . ' value="' . $value . '" type="sc">' . $value . '</li>';

                                }
                            }

                            $field_html .= '</ul></section>';
                        }

                        if ($fkey == 'dd') {
                            $field_comment = '';
                            if (isset($field->comment)) {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data)) {
                                if ($field->data == 'assets') {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('questions')->where('question_category', '=', 1)->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                    $field_html .= '<select class="form form-control" name="' . $questions->form_key . '" q-id="" custom="1" type="dd" case-name="assets">';
                                    foreach ($assets_query as $akey => $aquery) {
                                        $selected = '';
                                        $field_html .= '<option value="' . $aquery->question . '" ' . $selected . '>' . $aquery->question . '</option>';
                                    }
                                    $field_html .= '</select></div>';

                                }
                                if ($field->data == 'country_list') {
                                    $countries = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                    $field_html .= '<select class="form form-control" name="' . $questions->form_key . '" q-id="" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name) {
                                        $selected = '';
                                        $field_html .= '<option value="' . $country_name . '" ' . $selected . '>' . $country_name . '</option>';
                                    }
                                    $field_html .= '</select></div>';
                                }
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
                            $qa_fill_index = 0;

                            foreach ($qa_fields as $qa_key => $qa_field) {
                                $field_comment = '';
                                if (isset($qa_field->comment)) {
                                    $field_comment = $qa_field->comment;
                                }

                                $tbox_val = '';

                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                $field_html .= '<form>
                                                <label></label>
                                                <textarea  name="' . $questions->form_key . $field_name_array . '" q-id="" ' . $multi_qa_case_str . ' ' . $asset_attr_str . ' rows="4" cols="50" custom="1" type="qa" disabled>' . $tbox_val . '</textarea>
                                            </form>
                                        </div>';

                            }

                        }

                    }

                    $form_info[$key]->custom_fields = $field_html;
                }

            }
        }

        return view('forms.view_form_custom',
            ['user_type' => $user_type,
                'form_id' => $id,
                'parent_template' => $parent_template,
                'questions' => $form_info,
                'title' => count($form_info) > 0 ? ($form_info[0]->title) : ('title'),
                'heading' => count($form_info) > 0 ? ($form_info[0]->title) : ('heading')]);
    }

    // submit form for REGISTERED USER (not used currently)
    public function in_users_show_form($form_link_id){
        $info  = DB::table('user_form_links')->where('form_link_id',$form_link_id)->select('client_id', 'user_id', 'sub_form_id')->first();
        // show 404 if there is no user and client against requested form
        if (empty($info)){
            return abort('404');
        }
        $client_id      = $info->client_id;
        $user_id        = $info->user_id; 
        $subfirm_id     = $info->sub_form_id; 

        $form_type = DB::table('forms')
                        ->join('sub_forms', 'forms.id', 'sub_forms.parent_form_id')
                        ->where('sub_forms.id', $subfirm_id)
                        ->pluck('forms.type')->first();

        if (session('locale') == 'fr') {

            $form_info = DB::table('user_form_links')
                ->join('sub_forms', 'user_form_links.sub_form_id', 'sub_forms.id')
                ->join('form_questions', 'sub_forms.parent_form_id', 'form_questions.form_id')
                ->join('questions', 'form_questions.question_id', 'questions.id')
                ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', 'afs.id')
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
        } else {
            $form_info = DB::table('user_form_links')
                ->join('sub_forms', 'user_form_links.sub_form_id', 'sub_forms.id')
                ->join('form_questions', 'sub_forms.parent_form_id',  'form_questions.form_id')
                ->join('questions', 'form_questions.question_id',  'questions.id')
                ->leftJoin('admin_form_sections as afs', 'questions.question_section_id',  'afs.id')
                ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id',  DB::raw('afs.id AND cfs.client_id = ' . $client_id))
                ->where('user_form_links.form_link_id',  $form_link_id)
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
                        $expiry_note = 'The user failed to submit form before expiry time.';
                    }
                }
            } 
        }else if (isset($form_info[0]) && !$form_info[0]->is_accessible) {
            return view('user_form_not_accessible');
        }

        $filled_info = DB::table('user_form_links')
            ->join('user_responses', 'user_form_links.user_id', '=', DB::raw('user_responses.user_id AND user_form_links.sub_form_id = user_responses.sub_form_id'))
            ->join('questions', 'questions.id', '=', 'user_responses.question_id')
            ->where('user_form_links.form_link_id', '=', $form_link_id)
            ->select('question_key', 'question_response', 'question_id', 'additional_comment', 'additional_info', 'questions.type', 'custom_case')->get();
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
                'question_response' => $user_response->question_response,
                'question_id' => $user_response->question_id,
                'question_comment' => $user_response->additional_comment,
                'question_type' => $user_response->type,
                'additional_resp' => $user_response->additional_info,
            ];
        }

        // if ($id != 2)
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
        if (isset($form_info[0]) && !empty($form_info[0])) {
            $form_type = DB::table('forms')->where('id', '=', $form_info[0]->form_id)->pluck('type')->first();
            if ($form_type == 'sar') {
                $hidden_pb = true;
            }
        }
        if (count($form_info) > 0) {
            return view('forms.in_user_form_sec_wise', [
                'form_type'     => $form_type,
                'questions'     => $form_info,
                'hide_pb'       => $hidden_pb,
                'filled'        => $question_key_index,
                'user_info'     => $user_info,
                'title'         => !empty($form_info) ? ($form_info[0]->title) : ('title'),
                'heading'       => !empty($form_info) ? ($form_info[0]->title) : ('heading'),
                'expiry_note'   => $expiry_note]);
        } else {

            return redirect()->back()->with('top_bar_message', __('There is no questions in form'));
        }

    }

    // form submission for orgnization / company users
    public function ajax_int_user_submit_form(Request $req){
        if ($req->hasFile('img-' . $req->input('question-id'))) {
            $question_id = $req->input('question-id');
            $user_form_id = $req->input('user-form-id');
            $form_link_id = $req->input('form-link-id');
            $form_id = $req->input('form-id');
            $subform_id = $req->input('subform-id');
            $user_email = $req->input('email');
            $user_id = $req->input('user-id');
            $question_key = $req->input('question-key');

            $img_dir_path = "SAR_img_ids/$user_id/";

            $destinationpath = public_path($img_dir_path);
            //File::delete($destinationpath);
            $file = $req->file('img-' . $req->input('question-id'));
            $filename = $file->getClientOriginalName();
            //$ext=$file->getClientOriginalExtension();
            //echo "file ".$filename."<br>";exit;
            $img_name = uniqid() . $filename;
            //$destinationpath=public_path('img');
            $file->move($destinationpath, $img_name);

            $file_path = $img_dir_path . $img_name;

            DB::table('internal_users_filled_response')
                ->updateOrInsert(
                    [
                        'user_form_id'     => $user_form_id,
                        'form_id'          => $form_id,
                        'sub_form_id'      => $subform_id,
                        'question_id'      => $question_id,
                        'question_key'     => $question_key,
                        'user_id'          => $user_id,
                        // 'user_email'       => 0,
                    ],
                    ['question_response' => $file_path, 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
                );
            
            DB::table('user_responses')
                ->updateOrInsert(
                    [
                        'user_form_id'      => $user_form_id,
                        'form_id'           => $form_id,
                        'sub_form_id'       => $subform_id,
                        'question_id'       => $question_id,
                        'question_key'      => $question_key,
                        'user_id'           => $user_id ],
                    [   'question_response' => $file_path, "is_internal" => 1, 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
                );

            return;
        }

        $user_form_id = $req->input('user-form-id');
        $form_link_id = $req->input('form-link-id');
        $form_id = $req->input('form-id');
        $subform_id = $req->input('subform-id');
        $user_email = $req->input('email');
        $user_id = $req->input('user-id');
        $curr_sec = $req->input('curr-form-sec'); // user current form section
        $is_response_obj = $req->input('is_response_obj');

        DB::table('user_form_links')->where(['form_link_id' => $form_link_id,])->update(['curr_sec' => $curr_sec]);

        foreach ($req->all() as $post_key => $user_responses) {
            if (strpos($post_key, 'q-') !== false) {
                $custom_case = 0;
                $asset_questions = '';

                if ($is_response_obj == '1' && gettype($user_responses) == 'array') {
                    $custom_case = 1;
                    if ($req->input('mul_val_obj') == '1') {
                        echo 'mul_val_obj-1';exit;
                        if (isset($user_responses['response']['qa'])) {
                            $asset_questions = $user_responses['response']['qa'];
                        }
                        $user_responses = json_encode($user_responses['response']);

                    } elseif ($req->input('mul_val_obj') == '2') {
                        //echo 'mul_val_obj-2';exit;
                        $user_responses = array_column($user_responses, 'response');

                        foreach ($user_responses as $key => $value) {
                            $user_responses[$key] = $value[$key];
                        }

                        $qa_response['qa'] = $user_responses;

                        $user_responses = json_encode($qa_response);

                    } else {
                        //echo 'mul_val_obj-3';exit;
                        $resp_array[$user_responses['type']] = $user_responses['response'];
                        $user_responses = json_encode($resp_array);
                    }
                } 

                elseif (gettype($user_responses) == 'array') {
                    $user_responses = implode(', ', $user_responses);
                }

                $question_field_name = explode('_', $post_key);
                $question_key = $question_field_name[0];
                $question_id = $question_field_name[1];

                /*
                    DB::statement('insert into filled_response
                    (user_form_id, form_id, sub_form_id, question_id, question_key, user_id, question_response)
                    values (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_form_id, $form_id, $subform_id, $question_id, $question_key, $user_id, $user_responses]);
                */

                if ($custom_case == 0) {
                    // echo $custom_case.'----1';exit;
                    DB::statement('insert into internal_users_filled_response(user_form_id, form_id, sub_form_id, question_id, question_key, custom_case, user_id, question_response, created)
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = "' . $user_responses . '"',
                        [$user_form_id, $form_id, $subform_id, $question_id, $question_key, $custom_case, $user_id, $user_responses, date('Y-m-d H:i:s')]);
                    
                    DB::statement('insert into user_responses(user_form_id, form_id, sub_form_id, question_id, question_key, custom_case, user_id, question_response, created, is_internal)
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = "' . $user_responses . '"',
                        [$user_form_id, $form_id, $subform_id, $question_id, $question_key, $custom_case, $user_id, $user_responses, date('Y-m-d H:i:s'), 1]);
                } else {

                    DB::table('internal_users_filled_response')
                        ->updateOrInsert(
                            [
                                'user_form_id'          => $user_form_id,
                                'form_id'               => $form_id,
                                'sub_form_id'           => $subform_id,
                                'question_id'           => $question_id,
                                'question_key'          => $question_key,
                                'user_id'               => $user_id],
                            [   'question_response'     => $user_responses, 'custom_case' => $custom_case, 'created' => date('Y-m-d H:i:s')]);

                    DB::table('user_responses')
                        ->updateOrInsert(
                            [   'user_form_id'      => $user_form_id,
                                'form_id'           => $form_id,
                                'sub_form_id'       => $subform_id,
                                'question_id'       => $question_id,
                                'question_key'      => $question_key,
                                'user_id'           => $user_id],
                            [   'question_response' => $user_responses, 'custom_case' => $custom_case, 'is_internal' => 1, 'created' => date('Y-m-d H:i:s')]);
                    // 
                    if ($req->input('case_name') && $req->input('case_name') == 'assets') {
                        gettype($asset_questions); //was uncommented by qaiser on 20-06-2022
                        $asset_questions = explode("\n", trim($asset_questions));

                        if (!empty($asset_questions)) {
                            // print_r($asset_questions);
                            // exit;
                            foreach ($asset_questions as $asset_question) {
                                if (!empty($asset_question) && !DB::table('assets')->where('name', $asset_question)->exists()) {
                                    DB::table('assets')->insert(
                                        ['name' => $asset_question]
                                    );
                                }
                            }
                        }
                    }
                }
            }

            if (strpos($post_key, 'c-') !== false) {
                echo 'c-';exit;
                $comment_field_name = explode(':', $post_key);

                $comment_part = $comment_field_name[0];
                $question_id = $comment_field_name[1];

                $comment_part = explode('-', $comment_part);

                $c = $comment_part[0];
                $key = $comment_part[1];

                $question_key = 'q-' . $question_id;
                $question_id = $key;

                /*
                echo "<pre>";
                print_r($question_id);
                echo "</pre>";
                exit;
                 */

                if (DB::table('internal_users_filled_response')
                    ->where([
                        'user_form_id' => $user_form_id,
                        'sub_form_id' => $subform_id,
                        'form_id' => $form_id,
                        'user_id' => $user_id,
                        'question_id' => $question_id,
                    ])
                    ->exists()) {
                    DB::table('internal_users_filled_response')
                        ->where([
                            'user_form_id' => $user_form_id,
                            'sub_form_id' => $subform_id,
                            'form_id' => $form_id,
                            'user_id' => $user_id,
                            'question_id' => $question_id,
                        ])
                        ->update(['additional_comment' => $user_responses]);
                } else {

                    $insert_data = [
                        'user_form_id' => $user_form_id,
                        'sub_form_id' => $subform_id,
                        'form_id' => $form_id,
                        'question_id' => $question_id,
                        'question_key' => $question_key,
                        'user_id' => $user_id,
                        'additional_comment' => $user_responses,
                        'question_response' => '',
                        'created' => date('Y-m-d H:i:s')];

                    DB::table('internal_users_filled_response')->insert($insert_data);

                }

            }

            if (strpos($post_key, 'd-') !== false) {
                echo 'd-';exit;
                $date_field = explode('-', $post_key);

                $question_id = $date_field[1];
                $question_key = 'q-' . $question_id;

                if (DB::table('internal_users_filled_response')
                    ->where([
                        'user_form_id' => $user_form_id,
                        'sub_form_id' => $subform_id,
                        'form_id' => $form_id,
                        'user_id' => $user_id,
                        'question_id' => $question_id,
                    ])
                    ->exists()) {
                    DB::table('internal_users_filled_response')
                        ->where([
                            'user_form_id' => $user_form_id,
                            'sub_form_id' => $subform_id,
                            'form_id' => $form_id,
                            'user_id' => $user_id,
                            'question_id' => $question_id,
                        ])
                        ->update(['additional_info' => $user_responses,
                            'question_response' => 'Date Picker Option']);
                } else {
                    $insert_data = [
                        'user_form_id' => $user_form_id,
                        'sub_form_id' => $subform_id,
                        'form_id' => $form_id,
                        'question_id' => $question_id,
                        'question_key' => $question_key,
                        'user_id' => $user_id,
                        'additional_info' => $user_responses,
                        'question_response' => 'Date Picker Option',
                        'created' => date('Y-m-d H:i:s')];
                    echo '<pre>';
                    print_r($insert_data);exit;
                    DB::table('internal_users_filled_response')->insert($insert_data);
                }
            }   
        }
    }

    public function ajax_ext_user_submit_form(Request $req){
        
        if ($req->hasFile('img-' . $req->input('question-id'))) {
            $user_form_id       = $req->input('user-form-id');
            $form_link_id       = $req->input('form-link-id');
            $form_id            = $req->input('form-id');
            $subform_id         = $req->input('subform-id');
            $user_email         = $req->input('email');
            $user_id            = $req->input('user-id');
            $question_id        = $req->input('question-id');
            $question_key       = $req->input('question-key');
            $img_dir_path       = "SAR_img_ids/$user_id/";
            $destinationpath    = public_path($img_dir_path);
            $file               = $req->file('img-' . $req->input('question-id'));
            $filename           = $file->getClientOriginalName();
            $img_name           = uniqid() . $filename;
            $file_path          = $img_dir_path . $img_name;

            $file->move($destinationpath, $img_name);

            DB::table('external_users_filled_response')
                ->updateOrInsert(
                    [
                        'external_user_form_id' => $user_id,
                        'form_id' => $form_id,
                        'question_id' => $question_id,
                        'question_key' => $question_key,
                        'user_email' => $user_email],
                    ['question_response' => $file_path, 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
                );

            DB::table('user_responses')
                ->updateOrInsert(
                    [
                        'user_form_id'  => $user_id,
                        'form_id'       => $form_id,
                        'question_id'   => $question_id,
                        'question_key'  => $question_key,
                        'user_email'    => $user_email],
                    ['question_response' => $file_path, 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
                );
            return;
        }

        $form_id            = $req->input('form-id');
        $user_form_id       = $req->input('user-form-id');
        $form_link_id       = $req->input('form-link-id');
        $user_email         = $req->input('email');
        $user_id            = $req->input('user-id');
        $curr_sec           = $req->input('curr-form-sec');
        $is_response_obj    = $req->input('is_response_obj');

        DB::table('user_form_links')
            ->where([
                'id' => $user_id,
            ])
        ->update(['curr_sec' => $curr_sec]);

        foreach ($req->all() as $post_key => $user_responses) {

            if (strpos($post_key, 'q-') !== false) {
                $custom_case = 0;
                $asset_questions = '';

                if ($is_response_obj == '1' && gettype($user_responses) == 'array') {
                    $custom_case = 1;
                    if ($req->input('mul_val_obj') == '1') {
                        if (isset($user_responses['response']['qa'])) {
                            $asset_questions = $user_responses['response']['qa'];

                        }
                        $user_responses = json_encode($user_responses['response']);

                    } 
                    elseif ($req->input('mul_val_obj') == '2') {
                        $user_responses = array_column($user_responses, 'response');

                        foreach ($user_responses as $key => $value) {
                            $user_responses[$key] = $value[$key];
                        }

                        $qa_response['qa'] = $user_responses;

                        $user_responses = json_encode($qa_response);
                    } 
                    else {
                        $resp_array[$user_responses['type']] = $user_responses['response'];
                        $user_responses = json_encode($resp_array);
                    }
                } elseif (gettype($user_responses) == 'array') {
                    $user_responses = implode(', ', $user_responses);
                }

                $question_field_name = explode('_', $post_key);

                $question_key = $question_field_name[0];
                $question_id = $question_field_name[1];

                if ($custom_case == 0) {
                    DB::statement('insert into external_users_filled_response
                    (external_user_form_id, form_id, question_id, question_key, custom_case, user_email, question_response)
                    values (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = ' . '"' . $user_responses . '"', [$user_id, $form_id, $question_id, $question_key, $custom_case, $user_email, $user_responses]);
                   
                    // dev 
                    DB::table('user_responses')
                        ->updateOrInsert(
                            [
                                'user_form_id'  => $user_id,
                                'form_id'       => $form_id,
                                'question_id'   => $question_id,
                                'question_key'  => $question_key,
                                'user_email'    => $user_email ],
                            ['question_response' => $user_responses, 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]
                        );

                    // DB::statement('insert into user_responses(user_form_id, form_id, question_id, question_key, custom_case, user_email, question_response)
                    // values (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = ' . '"' . $user_responses . '"', [$user_id, $form_id, $question_id, $question_key, $custom_case, $user_email, $user_responses]);
                } else {
                    DB::table('external_users_filled_response')
                        ->updateOrInsert(
                            ['external_user_form_id' => $user_id,
                                'form_id'       => $form_id,
                                'question_id'   => $question_id,
                                'question_key'  => $question_key,
                                'user_email'    => $user_email,
                                'user_id'       => 0
                            ],
                            ['question_response' => $user_responses, 'custom_case' => $custom_case, 'created' => date('Y-m-d H:i:s')]);

                    DB::table('user_responses')
                        ->updateOrInsert(
                            [
                                'user_form_id'   => $user_id,
                                'form_id'        => $form_id,
                                'question_id'    => $question_id,
                                'question_key'   => $question_key,
                                'user_email'     => $user_email],
                            ['question_response' => $user_responses, 'custom_case' => $custom_case, 'created' => date('Y-m-d H:i:s')]);

                    if ($req->input('case_name') && $req->input('case_name') == 'assets') {
                        gettype($asset_questions);
                        $asset_questions = explode("\n", trim($asset_questions));

                        if (!empty($asset_questions)) {
                            $response = DB::table('user_form_links')->where(['id' => $user_id])->first();
                            foreach ($asset_questions as $asset_question) {

                                if (!empty($asset_question) && !DB::table('assets')->where('name', $asset_question)->exists()) {
                                    // DB::table('assets')->insert(
                                    //     ['name' => $asset_question, 'client_id' => $response->client_id]
                                    // );
                                }
                            }

                        }

                    }
                }

                /*
                    DB::table('')
                    ->updateOrInsert(
                    ['form_id' => $user_form_id, 'question_id' => $question_id, 'user_email' => $user_email, 'external_user_form_id' => $user_id],
                    ['question_id' => $question_id, 'question_key' => $question_key, 'user_email' => $user_email, 'question_response' => $user_responses]
                    );
                    */

            }

            if (strpos($post_key, 'c-') !== false) {
                $comment_field_name = explode(':', $post_key);

                $comment_part = $comment_field_name[0];
                $question_id = $comment_field_name[1];

                $comment_part = explode('-', $comment_part);

                $c = $comment_part[0];
                $key = $comment_part[1];

                $question_key = 'q-' . $question_id;

                $question_id = $key;

                /* echo "<pre>";
                print_r($question_id);
                echo "</pre>";
                exit;
                 */

                if (DB::table('external_users_filled_response')
                    ->where([
                        'external_user_form_id' => $user_form_id,
                        'form_id' => $form_id,
                        'user_email' => $user_email,
                        'question_id' => $question_id,
                    ])
                    ->exists()) {

                    //echo "if partr ";exit;

                    DB::table('external_users_filled_response')
                        ->where([
                            'external_user_form_id' => $user_form_id,
                            'form_id' => $form_id,
                            'user_email' => $user_email,
                            'question_id' => $question_id,
                        ])
                        ->update(['additional_comment' => $user_responses]);
                } else {
                    $insert_data = [
                        'external_user_form_id' => $user_form_id,
                        'form_id' => $form_id,
                        'question_id' => $question_id,
                        'question_key' => $question_key,
                        'user_email' => $user_email,
                        'additional_comment' => $user_responses,
                        'question_response' => ''];

                    // external_user_form_id, form_id, question_id, question_key, user_email, question_response
                    //[$user_id, $user_form_id, $question_id, $question_key, $user_email, $user_responses]

                    DB::table('external_users_filled_response')->insert($insert_data);

                }

            }

            if (strpos($post_key, 'd-') !== false) {

                $date_field = explode('-', $post_key);

                $question_id = $date_field[1];
                $question_key = 'q-' . $question_id;
                // dd(DB::table('external_users_filled_response')
                //       ->where([
                //             'form_id' => $form_id,
                //             'user_email' => $user_email,
                //             'question_id'  => $question_id,
                //             'external_user_form_id' => $req['user-form-id']
                //           ])
                //       ->get());
                if (DB::table('external_users_filled_response')
                    ->where([
                        'form_id' => $form_id,
                        'user_email' => $user_email,
                        'question_id' => $question_id,
                        'external_user_form_id' => $req['user-form-id'],
                    ])
                    ->exists()) {

                    DB::table('external_users_filled_response')
                        ->where([
                            'form_id' => $form_id,
                            'user_email' => $user_email,
                            'question_id' => $question_id,
                        ])
                        ->update(['additional_info' => $user_responses,
                            'question_response' => 'Date Picker Option']);
                } else {

                    $insert_data = [
                        'external_user_form_id' => $user_id,
                        'form_id' => $form_id,
                        'question_id' => $question_id,
                        'question_key' => $question_key,
                        'user_email' => $user_email,
                        'additional_info' => $user_responses,
                        'question_response' => 'Date Picker Option',
                        'created' => date('Y-m-d H:i:s')];

                    DB::table('external_users_filled_response')->insert($insert_data);
                }
            }
        }
    }

    public function ajax_lock_user_form(Request $request){
        $client_id = null;
        if ($request->client_id) {
            $client_id = $request->client_id;
        } else {

            $client_id = auth()->user()->client_id;
        }
        $form_link_id = $request->input('link_id');
        $user_type = $request->input('user_type');

        $table_name = '';
        $link_id_field = '';

        switch ($user_type) {
            case 'ex':
                $table_name = 'user_form_links';
                $link_id_field = 'form_link';
                break;
            case 'in':
                $table_name = 'user_form_links';
                $link_id_field = 'form_link_id';
                break;
        }
        // echo $table_name;
        // exit;
        $update = DB::table($table_name)
            ->where($link_id_field, $form_link_id)
            ->update([
                'is_locked' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return $update;
        $user_form_id = DB::table($table_name)->where($link_id_field, $form_link_id)->pluck('id')->first();


        // create sar request
        $form_type = DB::table($table_name)
            ->join('sub_forms', $table_name . '.sub_form_id', '=', 'sub_forms.id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id');

        if ($user_type == 'in') {
            $form_type = $form_type->join('users', 'user_form_links.user_id', '=', 'users.id');
        }

        $form_type = $form_type->where($link_id_field, $form_link_id)->first();

        if ($form_type->type == 'sar') {
            // dd('walla');
            $expiration_info = DB::table('sar_client_expiration_settings')->where('client_id', $client_id)->first();
            // dd($client_id);

            if (empty($expiration_info)) {
                $expiration_info = DB::table('sar_admin_expiration_settings')->first();
            }
            // dd($expiration_info);

            $due_date = date('Y-m-d', strtotime('+' . $expiration_info->duration . ' ' . $expiration_info->period, strtotime(date('Y-m-d H:i:s'))));

            DB::table('sar_requests')
                ->updateOrInsert(
                    [
                        'user_type' => $user_type,
                        'user_form_id' => $user_form_id,
                        'client_id' => $client_id,
                    ],
                    [
                        'submission_date' => date('Y-m-d H:i:s'),
                        'due_date' => $due_date,
                        'status' => 0,
                    ]
                );
        }

        return response()->json(['status' => 'success', 'msg' => __('Form successfully submitted')]);
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

    // form submission for external user
    public function ext_user_submit_form(Request $req){
        $user_form_id = $req->input('form-id');
        $form_link_id = $req->input('form-link-id');
        $user_email = $req->input('email');
        $user_id = $req->input('user-id');

        foreach ($req->all() as $post_key => $user_responses) {
            if (strpos($post_key, 'q-') !== false) {
                if (gettype($user_responses) == 'array') {
                    $user_responses = implode(', ', $user_responses);
                }

                $question_field_name = explode('_', $post_key);

                $question_key = $question_field_name[0];
                $question_id = $question_field_name[1];

                /*
                DB::statement('insert into filled_response
                (user_form_id, question_id, question_key, user_id, question_response)
                values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_form_id, $question_id, $question_key, $user_id, $user_responses]);
                 */
                DB::statement('insert into external_users_filled_response
                (external_user_form_id, form_id, question_id, question_key, user_email, question_response)
                values (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = ' . '"' . $user_responses . '"', [$user_id, $user_form_id, $question_id, $question_key, $user_email, $user_responses]);

                /*
            DB::table('external_users_filled_response')
            ->updateOrInsert(
            ['form_id' => $user_form_id, 'question_id' => $question_id, 'user_email' => $user_email, 'external_user_form_id' => $user_id],
            ['question_id' => $question_id, 'question_key' => $question_key, 'user_email' => $user_email, 'question_response' => $user_responses]
            );
             */

            }

            if (strpos($post_key, 'c-') !== false) {
                $comment_field_name = explode('-', $post_key);

                $comment_key = $comment_field_name[0];
                $question_id = $comment_field_name[1];

                /*
                DB::table('external_users_filled_response')
                ->updateOrInsert(['form_id' => $user_form_id, 'question_id' => $question_id, 'question_key' => $question_key, 'user_email' => $user_email, 'external_user_form_id' => $user_id],
                ['additional_comment' => $user_responses]
                );
                 */

                DB::table('external_users_filled_response')
                    ->where([
                        'external_user_form_id' => $user_id,
                        'form_id' => $user_form_id,
                        'user_email' => $user_email,
                        'question_id' => $question_id,
                    ])
                    ->update(['additional_comment' => $user_responses]);

            }

        }

        return view('users.show_msg', ['msg' => __('Your response has been submitted. Thanks ')]);
    }

    // Admin form list
    public function all_forms_list($type = ''){
        //dd($type);
        if ($type == 'audit'){
            $forms_info = DB::table('forms')
            ->join('audit_questions_groups', 'audit_questions_groups.id', 'forms.group_id')
            ->where('forms.type', $type)->select('title', 'title_fr', 'group_name', 'forms.id as form_id', 'forms.date_created')
            ->get();
        } else {
            $forms_info = DB::table('forms')->where('type',"!=", 'audit')->select('title', 'title_fr', 'forms.id as form_id', 'forms.date_created')->get();
        }
        // dd($forms_info);
        return view('forms.forms_list', ['user_type' => 'admin', 'forms_list' => $forms_info, 'type' => $type]);
    }
        


    // list of form assignees / clients (for admin)
    public function form_assignees($form_id = 1){
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

        return view('forms.forms_forms_assignee_list', ['user_type' => 'admin', 'selected_form' => $selected_form,
            'assigned_client_ids' => $assigned_client_ids,
            'form_id' => $form_id,
            'client_list' => $client_list]);
    }

    // list of forms for clients
    public function forms_list(){

        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');

        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);

            }
        }
        // if(Auth::user()->role != 3 ){
        if (!in_array('Manage Forms', $assigned_permissions)) {
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
            ->where('type', 'assessment')
            ->selectRaw('forms.title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.date_created')
            ->groupBy('forms.id')
            ->orderBy('date_created')
            ->get();

        if (session('locale') == 'fr') {
            $forms_info = DB::table('forms')
                ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
                ->leftjoin('sub_forms', 'forms.id', '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = ' . $client_id))
                ->where('client_forms.client_id', '=', $client_id)
                ->where('type', 'assessment')
                ->selectRaw('forms.title_fr as title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.date_created')
                ->groupBy('forms.id')
                ->orderBy('date_created')
                ->get();
        }

        $type = 'assessment';

        return view('forms.forms_list', ['user_type' => 'client', 'forms_list' => $forms_info, 'type' => $type]);
    }

    public function completed_forms_list(){
        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');
        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);

            }
        }
        if (!in_array('Completed Forms', $assigned_permissions)) {
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
                ->where('forms.type', 'assessment')
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
                    ->where('forms.type', 'assessment')
                    ->where('exf.client_id', $client_id)
                    ->where('is_locked', 1)
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

                $int_forms = DB::table('user_form_links as uf')
                    ->join('users', 'users.id', '=', 'uf.user_id')
                    ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                    ->where('forms.type', 'assessment')
                    ->where('uf.client_id', $client_id)
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

                $completed_forms = DB::Table('tmp_Data')->where('user_id', auth::user()->client_id)
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
                    ->groupBy('sub_forms.id')
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
            //dd($completed_forms);
            // tohandle null values
            if ($completed_forms == null) {$completed_forms = [];}

            if (Auth::user()->role == 1) {
                $user_type = 'admin';
            } else {
                $user_type = 'client';
            }

            return view('forms.completed_forms_list', compact('completed_forms', 'user_type'));
        }

    }

    // list of subforms
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

        return view('subforms.subform', [
            'user_type' => ((Auth::user()->role == 1) ? ('admin') : ('client')),
            'title' => 'Client SubForms',
            'heading' => 'Client SubForms',
            'form_info' => $form_info,
            'sub_forms' => $subforms_list,
            'client_users' => $client_user_list,
        ]);
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

    public function create_subform(Request $req){
        $title = $req->input('subform_title');
        $title_fr = $req->input('subform_title_fr');

        $form_id = $req->input('form_id');
        $expiry_time = date('Y-m-d H:i:s', strtotime("+10 days"));

        $client_id = Auth::user()->client_id;

        // check if form already exists by this name
        $existing_subform = DB::table('sub_forms')
            ->where('parent_form_id', '=', $form_id)
            ->where('client_id', '=', $client_id)
            ->where('title', '=', $title)
            ->where('title_fr', '=', $title_fr)

            ->first();

        if (Auth::user()->role == 2 && !empty($existing_subform)) {
            return response()->json(['status' => 'error', 'msg' => __('Sub-form by this name already exists')]);
        }

        // echo $expiry_time."<br>";
        // exit;

        $subform_id = DB::table('sub_forms')->insertGetId([
            'title' => $title,
            'title_fr' => $title_fr,
            'parent_form_id' => $form_id,
            'client_id' => $client_id,
            'expiry_time' => $expiry_time,
        ]);

        //$this->assign_subform_to_client_users($client_id, $form_id, $subform_id);

        return response()->json(['status' => 'success', 'msg' => 'Sub-form created']);

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

        $client_id = DB::table('user_form_links')
            ->where('user_form_links.form_link', '=', $form_link_id)
            ->pluck('client_id')
        ->first();

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
                // $client_id = $form_info[0]->client_id;
                // dd($client_id);

                if ((Auth::user()->role == 2 || Auth::user()->user_type == 1) && Auth::user()->client_id == $client_id) {
                    if ($form_info[0]->is_locked != '1') {
                        $expiry_note = __('The user failed to submit form before expiry time.');
                    }
                } else {
                    // return view('user_form_expired');
                    // $expiry_note = 'Failed to submit form before expiry time.';

                }
            } else {
                // return view('user_form_expired');7
                // $expiry_note = 'Failed to submit form before expiry time.';

            }
        } else if (isset($form_info[0]) && !$form_info[0]->is_accessible) {
            return view('user_form_not_accessible');
        }

        $filled_info = DB::table('user_form_links')
            ->join('user_responses', 'user_form_links.id', '=', 'user_responses.user_form_id')
            ->join('questions', 'questions.id', '=', 'user_responses.question_id')
            ->where('user_form_links.form_link', '=', $form_link_id)
            ->get();

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
                'question_response' => $user_response->question_response,
                'question_id' => $user_response->question_id,
                'question_comment' => $user_response->additional_comment,
                'question_type' => $user_response->type,
                'additional_resp' => $user_response->additional_info,
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
            return view('forms.ex_user_form_sec_wise', ['questions' => $form_info,
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

        $user_type = 'client';
        if (Auth::user()->role == 1) {
            $user_type = 'admin';
        }

        return view('subforms.org_subforms_list', compact('form_user_list', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }

    public function organization_all_forms_list_all(){
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
        if (!in_array('Generated Forms', $assigned_permissions)) {
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

        $int_form_user_list = DB::table('user_form_links')->where('sub_forms.client_id', $client_id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
            ->join('users', 'users.id', '=', 'user_form_links.user_id')
            ->where('forms.type', 'assessment')
            ->where('user_form_links.is_internal', 1)
            ->wherein('sub_form_id', $subform_id)
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "internal", is_locked'))->get();
        $ext_form_user_list = DB::table('user_form_links')->where('sub_forms.client_id', $client_id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('forms', 'forms.id', 'sub_forms.parent_form_id')
            ->wherein('sub_form_id', $subform_id)
            ->where('user_form_links.is_internal', 0)
            ->where('forms.type', 'assessment')
            ->select(DB::raw('*, user_form_links.created as uf_created, user_form_links.expiry_time as uf_expiry_time, "external", is_locked'))
        ->get();

        if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1') {
            $form_user_list = $ext_form_user_list;
        } else {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
        }

        $user_type = 'client';
        if (Auth::user()->role == 1) {
            $user_type = 'admin';
            // dd($user_type);
        }
        $all = 1;
        // dd($user_type);

        return view('subforms.org_subforms_list', compact('form_user_list', 'all', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }

    // subforms email list for registered users
    public function subforms_email_list($subform_id = 4){
        $this->middleware(['auth', '2fa']);

        if (Auth::user()->role != 2 && Auth::user()->user_type == 0) {
            return abort(404);
        }

        $user_type = 'client';
        if (Auth::user()->role == 1) {
            $user_type = 'admin';
        }

        $parent_form_id = DB::table('sub_forms')
            ->where('id', '=', $subform_id)
            ->pluck('parent_form_id')->first();

        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();

        //$client_user_list = DB::table('users')->where('client_id', '=', session('user_id'))->pluck('name');

        //echo "sub form id : ". $subform_id . "<br>";

        $form_user_list = DB::table('user_form_links')
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('users', 'users.id', '=', 'user_form_links.user_id')
            ->where('sub_form_id', '=', $subform_id)
            ->select('*')->get();

        $title = 'User List';
        $heading = 'user list';

        //   echo "<pre>";
        //  print_r($form_user_list);
        //   echo "</pre>";
        //   exit;

        //$subforms_list    = DB::table('sub_forms')->where('parent_form_id', '=', $form_id)->get();

        return view('forms.users_send_form_list2_in', compact('form_user_list', 'subform_id', 'title', 'heading', 'user_type', 'parent_form_id', 'parent_form_info'));

    }

    // forms shared list for external users
    public function ext_users_subforms_email_list($subform_id = 4){
        $this->middleware(['auth', '2fa']);

        /*
        if (Auth::user()->role != 2)
        {
        return abort(404);
        }
         */

        //$client_user_list = DB::table('users')->where('client_id', '=', session('user_id'))->pluck('name');

        //echo "sub form id : ". $subform_id . "<br>";
        /*
        SELECT * FROM `user_form_links`
        WHERE sub_form_id = 8 AND client_id = 23
         */

        $form_user_list = DB::table('user_form_links')
            ->where('sub_form_id', '=', $subform_id)
        //            ->where('user_form_links.client_id', '=', Auth::id())
            ->select('*')->get();

        $user_type = 'client';
        if (Auth::user()->role == 1) {
            $user_type = 'admin';
        }

        $parent_form_id = DB::table('sub_forms')
            ->where('id', '=', $subform_id)
            ->pluck('parent_form_id')->first();

        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();

        $title = 'User List';
        $heading = 'user list';

        //$subforms_list    = DB::table('sub_forms')->where('parent_form_id', '=', $form_id)->get();

        return view('forms.users_send_form_list2', compact('form_user_list', 'subform_id', 'title', 'heading', 'parent_form_id', 'parent_form_info', 'user_type'));

    }

    public function assign_subform_to_external_users(Request $req){

        $subform_id = $req->input('subform_id');
        $emails = $req->input('emails');
        $client_id = $req->input('client_id');
        $form_id = $req->input('parent_form_id');
        
        $e_count = DB::table('user_form_links')->where('is_internal', 0)->whereIn('user_email', $emails)->where('sub_form_id', $subform_id)->count();

        if ($e_count > 0) {
            return response()->json(['status' => 'fail',
                'msg' => __('One of the emails is already present for this sub form. Failed to send email.')]);
        }

        $subform_info = DB::table('sub_forms')->where('id', '=', $subform_id)->first();
        $insert_data = [];
        foreach ($emails as $email) {
            $insert_data[] = [
                'sub_form_id'   => $subform_id,
                'client_id'     => $client_id,
                'sub_form_id'   => $subform_id,
                'user_email'    => $email,
                'sub_form_id'   => $subform_id
            ];
        }

        DB::table('user_form_links')->insert($insert_data);

        $ex_users = DB::table('user_form_links')
            ->where('sub_form_id', $subform_id)
            ->where('is_internal', 0)
            ->whereIn('user_email', $emails)
            ->where('email_sent', 0)
            ->select('*')
            ->get();

        $links = [];
        $client_id = Auth::user()->client_id;
        $client_email = Auth::user()->email;
        $client_info = DB::table('users')->where('id', $client_id)->first();
        $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
        if (empty($subform_settings)) {
            $subform_settings = DB::table('subform_admin_expiration_settings')->first();
        }

        $remaining_time_for_form = '+' . $subform_settings->duration . ' ' . $subform_settings->period;

        if (Auth::user()->role == 3 && Auth::user()->user_type == 1) {
            $client_info = DB::table('users')
                ->join(DB::raw('users as clients'), 'clients.id', '=', 'users.client_id')
                ->where('users.id', '=', $client_id)
                ->select('clients.email', 'clients.id')
                ->get()
                ->first();

            if (!empty($client_info)) {
                $client_id = $client_info->id;
                $client_email = $client_info->email;
            }

        }

        foreach ($ex_users as $user) {
            $form_link = $user->client_id . '/' . $user->id . '/' . base64_encode($client_email) . '/' . $subform_id . '/' . base64_encode($user->user_email) . '/' . base64_encode(date('Y-m-d H:i:s'));

            $expiry_time = date('Y-m-d H:i:s', strtotime($remaining_time_for_form));

            DB::table('user_form_links')
                ->where('sub_form_id', $subform_id)
                ->where('is_internal', 0)
                ->where('client_id', $client_id)
                ->where('user_email', $user->user_email)
                ->update(['form_link' => $form_link, 'email_sent' => 1, 'expiry_time' => $expiry_time]);
            $logo = Auth::user()->image_name;

            // dev
            $ExtUserForm = "ExtUserForm";
            $form_email  = "form_email";
            if (Form::find($subform_info->parent_form_id)->type == 'audit'){
                $ExtUserForm = "external";
                $form_email  = "audit_email";
            }

            $data = [ 'name' => 'User', 'form_link_id' => $form_link, 'user_form' => $ExtUserForm, 'expiry_time' => $expiry_time, 'form_title' => $subform_info->title, 'client_info' => $client_info ];

            $transport = new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION'));
            $transport->setUsername(env('mail_username'));
            $transport->setPassword(env('MAIL_PASSWORD'));
            $swift_mailer   = new Swift_Mailer($transport);
            Mail::setSwiftMailer($swift_mailer);
            $reciever_email = $user->user_email;
            $sender_email   = env('MAIL_FROM_ADDRESS');
            
            Mail::send(['html' => $form_email], $data, function ($message) use ($reciever_email, $sender_email, $subform_info) {
                $message->to($reciever_email, 'Consentio Forms')->subject
                    ($subform_info->title);
                $message->from($sender_email, $sender_email);
            });
        }

        return response()->json(['status' => 'success', 'msg' => __('email sent')]);
    }

    public function assign_form_to_client(Request $request){

        $client_ids = $request->input('ids');
        $form_id = $request->input('form_id');
        //echo '<pre>';print_r($_POST);exit;
        $form_info = DB::table('forms')->where('id', '=', $form_id)->first();
        if (isset($request->del_ids) && $request->del_ids != '') {
            $arrdelids = explode(',', $request->del_ids);
            DB::table('client_forms')->where('form_id', '=', $form_id)->wherein('client_id', $arrdelids)->delete();
            //echo '---'.$request->del_ids;exit;
        }
        if ($form_info->code == 'f10') {
            DB::table('sub_forms')->where('parent_form_id', '=', $form_info->id)->delete();
        }

        $expiry_time = date('Y-m-d H:i:s', strtotime("+10 days"));

        $insert_data = [];
        if (isset($request->ids) && $request->ids != '') {
            $arr_client_ids = explode(',', $client_ids);
            foreach ($arr_client_ids as $client_id) {
                $insert_data[] = ['client_id' => $client_id, 'form_id' => $form_id];

                if ($form_info->code == 'f10') {
                    DB::table('sub_forms')->insert(['title' => $form_info->title,
                        'client_id' => $client_id,
                        'parent_form_id' => $form_info->id,
                        'expiry_time' => $expiry_time]);
                }
            }

            $already = DB::table('client_forms')->where('form_id', '=', $form_id)->where('client_id', $client_id)->first();

            if ($already) {
            } else {
                DB::table('client_forms')->insert($insert_data);
            }

        }

        echo json_encode(['status' => 'success']);
    }

    // for assignment of those users which were added after sub-form was created
    public function ajax_assign_subform_to_users(Request $req){
        
        $subform_id = $req->input('subform_id');

        $user_ids = $req->input('asgn_ids');
        $d_user_ids = $req->input('del_ids');
        $client_id = Auth::user()->client_id;
        $sb_title = $req->input('subform_title');
        $client_info = DB::table('users')->where('id', $client_id)->first();
        $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
        if (empty($subform_settings)){
            $subform_settings = DB::table('subform_admin_expiration_settings')->first();
        }
        $remaining_time_for_form = '+' . $subform_settings->duration . ' ' . $subform_settings->period;

        if (isset($user_ids) && !empty($user_ids)){
            $org_name = DB::table('users')->where('id', Auth::user()->client_id)->pluck('name');
            $sub_form_info = DB::table('sub_forms')->where('id', $subform_id)->first();
            foreach ($user_ids as $key => $user_id) {

                $exist = DB::table('user_form_links')->where('client_id', $client_id)->where('sub_form_id', $subform_id)->where('user_id', $user_id)->first();

                if ($exist == null){
                    $form_link_id = Str::random(40);
                    $expiry_time = date('Y-m-d H:i:s', strtotime($remaining_time_for_form));
                    $insert_data = [
                        'form_link_id'  => $form_link_id,
                        'sub_form_id'   => $subform_id,
                        'client_id'     => $client_id,
                        'user_id'       => $user_id,
                        'is_internal'   => 1,
                        'expiry_time'   => $expiry_time,
                    ];
                    DB::table('user_form_links')->insert($insert_data);
                    $insert_data = null;

                    $user_email = DB::table('users')->where('id', $user_id)->pluck('email')->first();

                    $CompanyUserForm = "CompanyUserForm";
                    $form_email      = "form_email";
                    if (Form::find($sub_form_info->parent_form_id)->type == 'audit'){
                        $ExtUserForm = "internal";
                        $form_email  = "audit_email";
                    }

                    $data = array('name' => $org_name, 'form_link_id' => $form_link_id, 'user_form' => $ExtUserForm, 'expiry_time' => $expiry_time, 'client_info' => $client_info);

                    $transport = new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION'));
                    $transport->setUsername(env('mail_username'));
                    $transport->setPassword(env('MAIL_PASSWORD'));
                    $swift_mailer = new Swift_Mailer($transport);
                    Mail::setSwiftMailer($swift_mailer);
                    $reciever_email = $user_email;
                    $sender_email = env('MAIL_FROM_ADDRESS');
                    $subject = $sb_title;
                    Mail::send(['html' => $form_email], $data, function ($message) use ($reciever_email, $sender_email, $subject) {
                        $message->to($reciever_email, 'Consentio Forms')->subject
                            ($subject);
                        $message->from($sender_email, $sender_email);
                    });

                }
            }
        }

        if (isset($d_user_ids) && !empty($d_user_ids)) {
            $flaag = DB::table('user_form_links')->where('sub_form_id', $subform_id)->whereIn('user_id', $d_user_ids)->delete();
        }

        return response()->json(['status' => 'success', 'msg' => __('Information updated about forms assignment')]);

    }

    // this function is used when sub-form is created and then users are assigned
    public function assign_subform_to_client_users($client_id, $form_id, $subform_id){
        //user_id           = 1;
        $user_id = 'all';

        //$client_id          = Auth::id();
        //$client_id          = 7;

        //if ($req->user_id == 'all')

        $client_id = Auth::user()->client_id;
        $client_email = Auth::user()->email;
        $client_name = Auth::user()->name;

        if (Auth::user()->role == 3 && Auth::user()->user_type == 1) {
            /*
            SELECT clients.email
            FROM   users
            JOIN   users AS clients ON clients.id = users.client_id
            WHERE  users.id = 86
             */

            $client_info = DB::table('users')
                ->join(DB::raw('users as clients'), 'clients.id', '=', 'users.client_id')
                ->where('users.id', '=', $client_id)
                ->select('clients.email', 'clients.id', 'clients.name')
                ->get()
                ->first();

            if (!empty($client_info)) {
                $client_id = $client_info->id;
                $client_email = $client_info->email;
                $client_name = $client_info->name;
            }

            $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();

            if (empty($subform_settings)) {
                $subform_settings = DB::table('subform_admin_expiration_settings')->first();
            }

            $remaining_time_for_form = '+' . $subform_settings->duration . ' ' . $subform_settings->period;
        }

        $insert_data = [];

        if ($user_id == 'all') {
            //$client_user_id_list    = DB::table('users')->where('client_id', '=', $client_id)->pluck('id');

            $client_user_id_list = DB::table('users')->where('client_id', '=', $client_id)->select('*')->get();

            if (!empty($client_user_id_list)) {
                foreach ($client_user_id_list as $assoc_user) {
                    /* $insert_data [] = [
                    //'form_link_id'   => Str::random(40),
                    'form_link_id'   => $client_id.'/'.
                    $assoc_user->id.'/'.
                    base64_encode(Auth::user()->email).'/'.
                    $assoc_user->id.'/'.
                    base64_encode($assoc_user->email).'/'.
                    base64_encode(date('Y-m-d H:i:s')),
                    'sub_form_id'    => $subform_id,
                    'client_id'      => $client_id,
                    'user_id'        => $assoc_user->id
                    ]; */
                    $form_link_id = Str::random(40);

                    $insert_data[] = [
                        'form_link_id' => $form_link_id,
                        'sub_form_id' => $subform_id,
                        'client_id' => $client_id,
                        'user_id' => $assoc_user->id,
                        'expiry_time' => date('Y-m-d H:i:s', strtotime($remaining_time_for_form)),
                    ];

                    $data = array('name' => $client_name, 'form_link_id' => $form_link_id, 'user_form' => 'CompanyUserForm');

                    $email_info = ['email' => $client_email, 'title' => 'Survey form'];

                    /*             echo "<pre>";
                    print_r($data);
                    print_r($email_info);

                    echo "</pre>";
                    exit;

                    if (Mail::send(['html'=>'test_email'], $data, function($message) use($email_info) {
                    $message->to($email_info['email'], 'D3G Forms')
                    ->subject($email_info['title']);
                    $message->from('noreply@consentio.cloud','D3G Forms');

                    }))
                    {
                    echo "mail sent ";
                    }
                    else
                    {
                    echo "mail not sent ";
                    }

                     */

                    Mail::send(['html' => 'test_email'], $data, function ($message) use ($email_info) {
                        $message->to($email_info['email'], 'Consentio Forms')
                            ->subject($email_info['title']);
                        $message->from('noreply@consentio.cloud', 'Consentio Forms');

                    });

                }
            }
        } else {
            $form_link_id = Str::random(40);
            $insert_data = [

                'form_link_id' => $form_link_id,
                'sub_form_id' => $sub_form_id,
                'user_id' => $user_id,
                'expiry_time' => date('Y-m-d H:i:s', strtotime($remaining_time_for_form)),
            ];

            $data = array('name' => Auth::user()->name, 'form_link_id' => $form_link_id, 'user_form' => 'CompanyUserForm');

            $email_info = ['email' => Auth::user()->email, 'title' => 'Survey form'];

            /*
            echo "<pre>";
            print_r($data);
            print_r($email_info);
            echo "</pre>";
            exit;

            if (Mail::send(['html'=>'test_email'], $data, function($message) use($email_info) {
            $message->to($email_info['email'], 'D3G Forms')
            ->subject($email_info['title']);
            $message->from('noreply@consentio.cloud','D3G Forms');

            }))
            {
            echo "mail sent ";
            }
            else
            {
            echo "mail not sent ";
            }

             */

            Mail::send(['html' => 'test_email'], $data, function ($message) use ($email_info) {
                $message->to($email_info['email'], 'Consentio Forms')
                    ->subject($email_info['title']);
                $message->from('noreply@consentio.cloud', 'Consentio Forms');
            });

        }

        DB::table('user_form_links')->insert($insert_data);
    }

    // Forms/SendForm/{id}
    public function send_form_link_to_users($sub_form_id){
        //$this->middleware(['auth','2fa']);

        if (Auth::user()->role != 2 && Auth::user()->user_type != 1) {
            return abort(404);
        }

        /*
        SELECT *
        FROM  ``
        JOIN  sub_forms ON sub_forms.id = .sub_form_id
        JOIN  users     ON users.id     = .user_id
        WHERE sub_form_id = 4
         */

        $forms_info = DB::table('user_form_links')
            ->join('sub_forms', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->join('users', 'users.id', '=', 'user_form_links.user_id')
            ->where('sub_form_id', '=', $sub_form_id)
            ->select('form_link_id', 'email', 'name', 'title', 'users.id as user_id', 'sub_forms.client_id')->get();

        /*
        echo "<pre>";
        print_r($forms_info);
        echo "</pre>";
        exit;
         */

        foreach ($forms_info as $f_info) {
            $data = array('name' => $f_info->name, 'form_link_id' => $f_info->form_link_id, 'user_form' => 'ExtUserForm');

            //$data = array('name'=> $f_info->name, 'form_link_id' => $form_link);

            Mail::send(['html' => 'test_email'], $data, function ($message) use ($f_info) {
                $message->to($f_info->email, 'Consentio Forms')->subject
                    ($f_info->title);
                $message->from('noreply@consentio.cloud', 'Consentio Forms');

            });
        }

        $msg = "Forms email Sent. Check your inbox.";

        return view('show_msg', compact('msg'));

        // get form users
    }

    public function client_user_subforms_list(){
        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');

        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);
            }
        }

        if (Auth::user()->role != 3) {
            if (!in_array('My Assigned Forms', $assigned_permissions)) {
                return redirect('dashboard');
            }
        }
        $user_id = Auth::id();
        /*
            SELECT *
            FROM  `sub_forms`
            LEFT JOIN 
             ON sub_forms.id = 
            .sub_form_id
            WHERE sub_forms.client_id = 23
         */

        $sub_forms = DB::table('sub_forms')
            ->join("forms", "sub_forms.parent_form_id", "forms.id")
            ->leftjoin('user_form_links', 'sub_forms.id', '=', 'user_form_links.sub_form_id')
            ->where('user_form_links.user_id', $user_id)
            ->where('type', 'assessment')
            ->get();

        return view('client_subform', ['sub_forms' => $sub_forms]);

    }

    public function report(){
        return view('report');
    }

    

    

    public function store_edit(Request $request, $id){

        $this->validate($request, [
            'name' => 'required',
        ],
            [
                'name.required' => __('Please provide proper name to proceed.'),
            ]

        );
        $mail_verification = $request['mail_verification'];
        $data = User::where("id", $request->input("id"))->first();
        $previous_image = $data->image_name;
        $test = $data->image_name;
        $inputs = [
            'password' => $request->password,
        ];
        $rules = [
            'password' => [
                'required',
                'string',
                'min:8', // must be at least 8 characters in length
                'regex:/[a-z]/', // must contain at least one lowercase letter
                'regex:/[A-Z]/', // must contain at least one uppercase letter
                'regex:/[0-9]/', // must contain at least one digit
            ],
        ];
        $validation = \Validator::make($inputs, $rules);

        if ($request->password != "") {
            if ($validation->fails()) {
                return redirect('edit_user/' . $id)->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();
            } elseif ($request->password != $request->rpassword) {
                return redirect('edit_user/' . $id)->with('alert', __('Password did not match!'));
            } else {
                if ($request->base_string) {
                    $ext = explode('/', mime_content_type($request->base_string))[1];
                    $img = $request->base_string;

                    $file_name = 'image_' . time() . '.jpg';
                    @list($type, $img) = explode(';', $img);
                    @list(, $img) = explode(',', $img);
                    if ($img != "") {
                        \Storage::disk('public')->put($file_name, base64_decode($img));
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img2/' . $file_name);
                        $imgname = $file_name;
                    }

                } else {
                    $imgname = $previous_image;
                }
                $record = array(
                    "name" => $request->input('name'),
                    "image_name" => $imgname,
                );
                if ($request->input('password')) {
                    $record['password'] = bcrypt($request->input('password'));
                }
                if ($request->input('id')) {

                    // $destinationpath=public_path("img/$test");
                    // File::delete($destinationpath);
                    User::where("id", $request->input("id"))->update($record);
                    $insert_id = $request->input("id");
                } else {
                    $insert_id = User::insertGetId($record);
                }
                $fa = User::where("id", $request->input("id"))->first();
                if ($fa->tfa == 0) {
                    DB::table('password_securities')->where('user_id', $id)->delete();
                }
                return redirect("users_management");
            }
        } else {
            if ($request->password != $request->rpassword) {
                return redirect('edit_user/' . $id)->with('alert', __('Password did not match!'));
            } else {

                if ($request->base_string) {
                    $ext = explode('/', mime_content_type($request->base_string))[1];
                    $img = $request->base_string;

                    $file_name = 'image_' . time() . '.jpg';
                    @list($type, $img) = explode(';', $img);
                    @list(, $img) = explode(',', $img);
                    if ($img != "") {
                        \Storage::disk('public')->put($file_name, base64_decode($img));
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img2/' . $file_name);
                        $imgname = $file_name;
                    }

                } else {
                    $imgname = $previous_image;
                }

                if ($mail_verification == "on") {
                    $record = array(
                        "name" => $request->input('name'),
                        "image_name" => $imgname,
                        "is_email_varified" => 0,
                    );
                } else {
                    $record = array(
                        "name" => $request->input('name'),
                        "image_name" => $imgname,
                        "is_email_varified" => 1,
                    );
                }

                if ($request->input('password')) {
                    $record['password'] = bcrypt($request->input('password'));
                }
                if ($request->input('id')) {

                    // $destinationpath=public_path("img/$test");
                    // File::delete($destinationpath);
                    User::where("id", $request->input("id"))->update($record);
                    $insert_id = $request->input("id");

                } else {
                    $insert_id = User::insertGetId($record);
                }

                $fa = User::where("id", $request->input("id"))->first();
                if ($fa->tfa == 0) {
                    DB::table('password_securities')->where('user_id', $id)->delete();
                }
                return redirect("users_management");
            }
        }
    }

    public function assign_section_category(Request $req){
        $form_id = $req->input('form_id');
        $ctgry_id = $req->input('ctg_id');
        $sec_name = $req->input('sec_name');

        if (DB::table('questions')
            ->where('form_id', $form_id)
            ->where('question_section', $sec_name)
            ->update(['question_category' => $ctgry_id])) {
            return response()->json(['status' => 'success', 'msg' => __('Category against this section updated')]);
        }

    }

    public function show_subforms_expiry_settings(){
        // dd('aslkdjaskldj')

        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');

        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);

            }
        }
        if (!in_array('Sub Forms Expiry Settings', $assigned_permissions)) {
            return redirect('dashboard');
        }

        $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();

        if (empty($subform_settings)) {
            $subform_settings = DB::table('subform_admin_expiration_settings')->first();
        }

        return view('subforms.subforms_expiry_settings', ['subform_settings' => $subform_settings]);
    }

    public function save_subforms_expiry_settings(Request $request){
        DB::table('subform_client_expiration_settings')
            ->updateOrInsert(
                ['client_id' => $request->input('client_id')],
                ['duration' => $request->input('duration'), 'period' => $request->input('period')]
            );

        return response()->json(['status' => 'success', 'msg' => 'updated']);
    }

    public function add_new_form(){
        return view('forms.add_new_form');
    }

    public function store_new_form(Request $request){

        $this->validate($request, [
            'title' => 'required',
            'title_fr' => 'required',
            'type' => 'required',
            ],
            [
                'title.required' => __('Please provide proper English Form name to proceed.'),
                'title_fr.required' => __('Please provide proper French Form name to proceed.'),
                'type.required' => __('Please provide Form type to proceed.'),
            ]

        );
        $now = Carbon::now();

        $request['date_created'] = $request['date_updated'] = $now;
        $response = DB::table('forms')->insertGetId([
            'title'     => $request['title'],
            'title_fr'  => $request['title_fr'],
            'type'      => $request['type'],
            'group_id'  => $request['group_id'],
        ]);
        DB::table('forms')->where('id', $response)->update([
            'code' => 'f' . DB::table('forms')->where('id', $response)->pluck('id')->first(),
        ]);

        if ($request->type == "audit") {
            return redirect('Forms/AdminFormsList/audit')->with('message', __('From added successfully'));    
        }
        return redirect('Forms/AdminFormsList')->with('message', __('From added successfully'));

    }

    public function add_form_questions($id){
        // in case if user is not admin, check if the form is assigned to company user or its related users
        if (Auth::user()->role != 1) {

            $client_id = Auth::user()->client_id;
            // get assignee list

            /*
            SELECT * FROM client_forms WHERE client_id = 76 AND form_id = 2
             */

            $form_assignee = DB::table('client_forms')->where('client_id', $client_id)->where('form_id', $id)->first();
            // form is not assigned, don't allow the form with request id to view
            if (empty($form_assignee)) {
                return abort('404');
            }
        }
        // get user form questions
        /*
        SELECT    questions.question, questions.options, afs.section_title as admin_sec_title, cfs.section_title as client_sec_title, questions.question_section_id as q_sec_id,
        afs.id as a_sec_id, cfs.id as c_sec_id
        FROM      forms
        JOIN      form_questions
        ON        forms.id = form_questions.form_id
        JOIN      questions
        ON        form_questions.question_id = questions.id
        LEFT JOIN admin_form_sections  as afs ON  questions.question_section_id = afs.id
         */
        $form_info = DB::table('forms')
            ->join('form_questions', 'forms.id', '=', 'form_questions.form_id')
            ->join('questions', 'form_questions.question_id', '=', 'questions.id')
            ->where('forms.id', '=', $id)
            ->where('form_questions.display_question', 'yes')
            ->orderBy('form_questions.sort_order')
            ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id');

        $form_info = $form_info->select('*', 'afs.id as afs_sec_id', 'afs.section_title as admin_sec_title', 'questions.question_section_id as q_sec_id')->get();
        // return $form_info;
        $form_sections = DB::table('admin_form_sections')->where('form_id', $id)->get();

        $form_data = DB::table('forms')->where('id', $id)->first();

        $questions_data = DB::table('form_questions')
            ->join('forms', 'form_questions.form_id', '=', 'forms.id')
            ->join('questions', 'form_questions.question_id', '=', 'questions.id')
            ->where('forms.id', '=', $id)
            ->orderBy('form_questions.sort_order')
            ->select('*', 'questions.question_section_id as q_sec_id')
            ->get();

        $user_type = 'admin';
        $parent_template = 'admin.layouts.admin_app';
        if ($id != 2) {
            $custom_fields = '';
            foreach ($form_info as $key => $questions) {
                if (trim($questions->type) == 'cc') {
                    $fields_info = json_decode($questions->question_info);
                    $fields_info_fr = json_decode($questions->question_info_fr);

                    $field_html = '';
                    $is_asset_case = false;

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
                                $field_html .= '<li class="es-selectable ' . $mc_selected . '" name="' . $questions->form_key . '" q-id="" custom="1" ' . $case_name . ' value="' . $value . '" type="mc">' . $value . '</li>';
                                $field_html .= '</ul></section>';

                            }

                        }

                        // added case for sc
                        if ($fkey == 'sc') {
                            $value = '';
                            $sc_fields = [];
                            $sc_fields_fr = [];

                            if (gettype($field) == 'array') {
                                $sc_fields = $field;
                                $sc_fields_fr = isset($fields_info_fr->$fkey) ? $fields_info_fr->$fkey : $field;
                            } else {
                                $sc_fields[0] = $field;
                                $sc_fields_fr[0] = $fields_info_fr->$fkey;

                            }
                            // dd($sc_fields);

                            $field_html .= '<section class="options" id="select-en-' . $questions->form_key . '">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable" >';
                            $en_options = array();
                            foreach ($sc_fields as $sc_field) {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string') {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable') {
                                        $case_name = 'case-name="Not Sure"';
                                        //$mc_selected = 'es-selected';
                                    }

                                    $value = $sc_field->data;
                                    $mc_selected = '';
                                    $en_options[] = $value;

                                    $field_html .= '<li class="es-selectable not-unselectable ' . $mc_selected . '" name="' . $questions->form_key . '" q-id="" custom="1" ' . $case_name . ' value="' . $value . '" type="sc">' . $value . '</li>';

                                }
                            }

                            $field_html .= '</ul></section>';
                            //**************************for En edit options
                            $field_html .= '<p onClick="$(\'#select-en-' . $questions->form_key . '\').hide(500) , $(\'#txtarea-en-' . $questions->form_key . '\').show(500) " data-toggle="tooltip" data-placement="top" title="Click here to edit english question options" class="pull-right btn btn-sm btn-warning" >Edit English  Options</p>';

                            $field_html .= '<div style="display:none" class="row" style="margin-left: inherit; margin-top: 5px;" id="txtarea-en-' . $questions->form_key . '">';
                            $field_html .= '<textarea  class="form-control" id="text_area-en-' . $questions->form_key . '"  name="" q-id="" case-name="multi-qa" rows="4" cols="50" custom="1" type="">' . implode(',', $en_options) . '</textarea>';
                            $field_html .= '<input type="hidden" id="input-en-' . $questions->form_key . '" value="' . implode(',', $en_options) . '">';
                            $field_html .= '<a type="button" class="btn save_btn" style="color: white;background: #0f75bd;" onClick="update_sc_question(\'' . $questions->form_key . '\',\'en\')"  ><i class="fa fa-check-circle" style="font-size: 20px;" aria-hidden="true"></i></a>';
                            $field_html .= '<a  class="btn btn-danger" onClick="$(\'#select-en-' . $questions->form_key . '\').show(500) , $(\'#txtarea-en-' . $questions->form_key . '\').hide(500) " style="font-size: 20px;" aria-hidden="true"><i class="fa fa-times" style="font-size: 20px;" aria-hidden="true"></i></a></div>';
                            //***********************for french

                            $field_html .= '<section class="options" id="select-fr-' . $questions->form_key . '">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';
                            $fr_options = array();
                            foreach ($sc_fields_fr as $sc_field) {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string') {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable') {
                                        $case_name = 'case-name="Not Sure"';
                                        //$mc_selected = 'es-selected';
                                    }

                                    $value = $sc_field->data;
                                    $mc_selected = '';
                                    $fr_options[] = $value;

                                    $field_html .= '<li class="es-selectable not-unselectable ' . $mc_selected . '" name="' . $questions->form_key . '" q-id="" custom="1" ' . $case_name . ' value="' . $value . '" type="sc">' . $value . '</li>';

                                }
                            }

                            $field_html .= '</ul></section>';
                            $field_html .= '<p onClick="$(\'#select-fr-' . $questions->form_key . '\').hide(500) , $(\'#txtarea-fr-' . $questions->form_key . '\').show(500) " data-toggle="tooltip" data-placement="top" title="Click here to edit french question options" class="pull-right btn btn-sm btn-warning" >Edit French Options</p>';

                            $field_html .= '<div style="display:none" class="row" style="margin-left: inherit; margin-top: 5px;" id="txtarea-fr-' . $questions->form_key . '">';
                            $field_html .= '<textarea  class="form-control" id="text_area-fr-' . $questions->form_key . '"  name="" q-id="" case-name="multi-qa" rows="4" cols="50" custom="1" type="">' . implode(',', $fr_options) . '</textarea>';
                            $field_html .= '<input type="hidden" id="input-fr-' . $questions->form_key . '" value="' . implode(',', $fr_options) . '">';
                            $field_html .= '<a type="button" class="btn save_btn" style="color: white;background: #0f75bd;" onClick="update_sc_question(\'' . $questions->form_key . '\',\'fr\')"  ><i class="fa fa-check-circle" style="font-size: 20px;" aria-hidden="true"></i></a>';
                            $field_html .= '<a  class="btn btn-danger" onClick="$(\'#select-fr-' . $questions->form_key . '\').show(500) , $(\'#txtarea-fr-' . $questions->form_key . '\').hide(500) " style="font-size: 20px;" aria-hidden="true"><i class="fa fa-times" style="font-size: 20px;" aria-hidden="true"></i></a></div>';
                        }

                        // added case for sc
                        //ahmad

                        //ahmad end
                        if ($fkey == 'dd') {

                            $field_comment = '';
                            $field_comment_fr = '';
                            if (isset($fields_info_fr->$fkey->comment)) {
                                $field_comment_fr = $fields_info_fr->$fkey->comment;
                            }
                            if (isset($field->comment)) {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data)) {
                                if ($field->data == 'assets') {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('questions')->where('question_category', '=', 1)->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">' . $field_comment . '</h6>';
                                    $field_html .= '<select class="form form-control" name="' . $questions->form_key . '" q-id="" custom="1" type="dd" case-name="assets">';
                                    foreach ($assets_query as $akey => $aquery) {
                                        $selected = '';
                                        $field_html .= '<option value="' . $aquery->question . '" ' . $selected . '>' . $aquery->question . '</option>';
                                    }
                                    $field_html .= '</select></div>';

                                }
                                if ($field->data == 'country_list') {
                                    $countries = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment cc_cmment" id="comment-en-dd-' . $questions->form_key . '" >' . $field_comment . '</h6>';
                                    $field_html .= '<h6 class="question-comment cc_cmment" id="comment-en-dd-' . $questions->form_key . '">' . $field_comment_fr . '</h6>';
                                    $field_html .= '<select class="form form-control" name="' . $questions->form_key . '" q-id="" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name) {
                                        $selected = '';
                                        $field_html .= '<option value="' . $country_name . '" ' . $selected . '>' . $country_name . '</option>';
                                    }
                                    $field_html .= '</select></div>';
                                }
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
                            $qa_fill_index = 0;
                            // dd($fields_info_fr);
                            // dd($qa_fields);
                            // dd($fields_info_fr->$fkey);
                            foreach ($qa_fields as $qa_key => $qa_field) {
                                // dd($fields_info_fr->qa->comment , $fkey , $qa_fields );
                                $field_comment = '';
                                $field_comment_fr = '';
                                // dd($fields_info_fr->$fkey[$qa_key]->comment);
                                // if(isset($fields_info_fr->$fkey[$qa_key]->comment)){
                                // if(isset($fields_info_fr->qa->comment)){
                                if (isset($fields_info_fr->qa) && is_array($fields_info_fr->qa)) {

                                    $field_comment_fr = $fields_info_fr->qa[$qa_key]->comment;
                                } else {
                                    if (isset($fields_info_fr->qa)) {

                                        $field_comment_fr = $fields_info_fr->qa->comment;
                                    }
                                }
                                // }
                                if (isset($qa_field->comment)) {
                                    $field_comment = $qa_field->comment;
                                }

                                $tbox_val = '';
                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment cc_cmment" id="comment-en-qa-' . $questions->form_key . '-' . $qa_key . '">' . $field_comment . '</h6>';
                                $field_html .= '<h6 class="question-comment cc_cmment" id="comment-fr-qa-' . $questions->form_key . '-' . $qa_key . '">' . $field_comment_fr . '</h6>';

                                $field_html .= '<form>
                                        <label></label>
                                        <textarea  name="' . $questions->form_key . $field_name_array . '" q-id="" ' . $multi_qa_case_str . ' ' . $asset_attr_str . ' rows="4" cols="50" custom="1" type="qa" disabled>' . $tbox_val . '</textarea>
                                    </form>
                                </div>';

                            }

                        }

                    }

                    $form_info[$key]->custom_fields = $field_html;
                }
            }
        }

        $can_update = false;
        $sub_form_ids_of_this_form = DB::table('sub_forms')->where('parent_form_id', $id)->pluck('id');
        $total_form_submitions_internal = DB::table('user_form_links')->wherein('sub_form_id', $sub_form_ids_of_this_form)->count();
        $total_form_submitions_external = DB::table('user_form_links')->wherein('sub_form_id', $sub_form_ids_of_this_form)->count();

        if ($total_form_submitions_internal == 0 && $total_form_submitions_external == 0) {
            $can_update = true;
        }
        return view('forms.add_question_to_form', [
            'user_type' => $user_type,
            'form_id' => $id,
            'parent_template' => $parent_template,
            'questions' => $form_info,
            'title' => DB::table('forms')->where('id', $id)->pluck('title')->first(),
            'heading' => count($form_info) > 0 ? ($form_info[0]->title) : ('heading'),
            'questions_data' => $questions_data,
            'form_sections' => $form_sections,
            'form_data' => $form_data,
            'can_update' => $can_update,
        ]);
    }

    public function add_section_to_form(Request $request){
        $this->validate($request, 
            [
                'section_title' => 'required',
                'section_title_fr' => 'required',
                'question_title' => 'required',
                'question_title_fr' => 'required',
                'question_options' => 'required_if:q_type,mc|min:1',
                'question_options_fr' => 'required_if:q_type,mc|min:1',
                'question_options' => 'required_if:q_type,sc|min:1',
                'question_options_fr' => 'required_if:q_type,sc|min:1',
                'q_type' => 'required',
            ],
            [
                'section_title.required' => __('English Section Title Can Not Be Empty.'),
                'section_title_Fr.required' => __('French Section Title Can Not Be Empty.'),
                'question_title.required' => __('English Question Title Can Not Be Empty.'),
                'question_title_fr.required' => __('French Question Title Can Not Be Empty.'),
                'question_options.required_if' => __('English Question Options Can Not Be Empty.'),
                'question_options_fr.required_if' => __('French Question Options Can Not Be Empty.'),
                'question_options.min' => __('Please provide atleast one English option to proceed'),
                'question_options_fr.min' => __('Please provide atleast one French option to proceed'),
                'q_type.required' => __('Atleast one Question is mendatory to create New Section.'),
            ]
        );

        $allow_attach = 0;
        if ($request->add_attachments_box){
            $allow_attach = 1;
        }

        $check_title_exists = DB::table('admin_form_sections')->where('form_id', $request->form_id)->where('section_title', $request->section_title)->first();
        if ($check_title_exists != null) {
            return redirect()->back()->with('message', __('English Section Title Already Exists, Please provide a unique section title  '));
        }
        $check_title_exists = DB::table('admin_form_sections')->where('form_id', $request->form_id)->where('section_title_fr', $request->section_title_fr)->first();
        if ($check_title_exists != null) {
            return redirect()->back()->with('message', __('French Section Title Already Exists, Please provide a unique section title  '));
        }

        $last_section_num = DB::table('admin_form_sections')->where('form_id', $request->form_id)->orderBy('sec_num', 'desc')->pluck('sec_num')->first();
        if ($last_section_num == null) {
            $last_section_num = 0;
        }

        $section_id = DB::table('admin_form_sections')->insertGetId([
            'section_title' => $request['section_title'],
            'section_title_fr' => $request['section_title_fr'],
            'form_id' => $request['form_id'],
            'sec_num' => $last_section_num + 1,
        ]);
        $section_num = DB::table('admin_form_sections')->where('id', $section_id)->where('form_id', $request->form_id)->pluck('sec_num')->first();
        $last_question_num = DB::table('questions')->where('question_section_id', $section_id)->where('question_num', '!=', null)->count();
        if ($last_question_num == 0) {
            $last_question_num = 1;
        } else {
            $last_question_num++;
        }
        $question_number = $section_num . '.' . $last_question_num;
        $sort_order = DB::table('questions')->where('form_id', $request->form_id)->count();

        $question_id = DB::table('questions')->insertGetId([
            'question'              => $request->question_title,
            'question_fr'           => $request->question_title_fr,
            'question_short'        => $request->question_title_short,
            'question_short_fr'     => $request->question_title_short_fr,
            'question_num'          => $question_number,
            'options'               => str_replace(",", ", ", $request->question_options),
            'options_fr'            => str_replace(",", ", ", $request->question_options_fr),
            'question_section'      => ' ',
            'question_section_id'   => $section_id,
            'question_comment'      => $request->question_coment,
            'question_comment_fr'   => $request->question_coment_fr,
            'attachment_allow'      => $allow_attach,
            'attachments'           => json_encode($request->attachment),
            'dropdown_value_from'   => $request->dropdown_value_from,
            'not_sure_option'       => $request->add_not_sure_box,
            'question_category'     => 1,
            'form_id'               => $request->form_id,
            'type'                   => $request->q_type,
        ]);

        DB::table('questions')->where('id', $question_id)->update(['form_key' => 'q-' . $question_id]);

        // DB::table('form_questions')->insert([
        //     'form_id' => $request->form_id,
        //     'question_id' => $question_id,
        //     'sort_order'=>$sort_order++,
        // ]);
        $__sec_num = $section_num - 1;
        $child_sord_order_num = $__sec_num + ($last_question_num / 100);
        $child_sort_order = DB::table('form_questions')->insertGetId([
            'form_id' => $request->form_id,
            'question_id' => $question_id,
            'sort_order' => $child_sord_order_num,
        ]);

        return redirect()->back()->with('success', __('Successfully Added Section'));

    }

    public function add_custom_question(Request $request){
        dd($request->all());
    }

    public function add_special_question_to_form(Request $request){

        // return $request->all();
        $attachments = $request->add_attachments_box;
        for ($i = sizeof($request->add_attachments_box); $i < sizeof($request->s_question_title); $i++){ 
            $attachments[$i] = false;
        }

        $obj    = (object) null;
        $obj_fr = (object) null;

        //************************************************************************************************************************
        //fOR mULTI LEVEL QUESTIONS TYPE PARENT

        if ($request->q_type == 'parent' || $request->q_type == 'data') {
            $this->validate($request, 
                [
                    'question_title' => 'required',

                    's_question_title' => 'required|array',
                    's_question_title.*' => 'required|min:1',

                    's_question_title_fr' => 'required|array',
                    's_question_title_fr.*' => 'required|min:1',

                    's_q_type' => 'required|array',
                    's_q_type.*' => 'required|min:1',

                    's_question_options' => 'array|required_if:s_q_type.*,sc',
                    's_question_options_fr' => 'array|required_if:s_q_type.*,mc',

                    's_question_options' => 'array|required_if:s_q_type.*,mc',
                    's_question_options_fr' => 'array|required_if:s_q_type.*,sc',

                    's_question_options_fr.*' => 'required|min:1',
                    's_question_options.*' => 'required|min:1',
                ],
                [
                    's_q_type.*' => __('Please Select atleast one sub question to proceed'),
                    's_question_title.*' => __('Please Provide English Sub question title for all questions to proceed'),
                    's_question_title_fr.*' => __('Please Provide French Sub question title for all questions to proceed'),

                    'question_title.required' => __('English Question Title Can Not Be Empty.'),
                    'question_title_fr.required' => __('French Question Title Can Not Be Empty.'),
                    's_question_options.*' => __('Please provide English Question Options for all sub questions at.'),
                    's_question_options_fr.*' => __('Please provide French Question Options for all sub questions.'),

                ]
            );
            $section_num = DB::table('admin_form_sections')->where('id', $request->this_section_id)->where('form_id', $request->form_id)->pluck('sec_num')->first();
            $last_question_num = DB::table('questions')->where('question_section_id', $request->this_section_id)->where('question_num', '!=', null)->count();

            if ($last_question_num == 0) {
                $last_question_num = 1;
            } else {
                $last_question_num++;
            }
            $question_number = $section_num . '.' . $last_question_num;

            $sort_order = DB::table('questions')->where('form_id', $request->form_id)->count();

            $parent_question_array = array(
                'question' => $request->question_title,
                'question_fr' => $request->question_title_fr,
                'question_short' => $request->question_title_short,
                'question_short_fr' => $request->question_title_short_fr,
                'question_num' => $question_number,
                'question_section' => null,
                'type' => '',
                'question_section_id' => $request->this_section_id,
                'question_category' => 2,
                'form_id' => $request->form_id,
                'is_parent' => 1,
                // 'attachments' => json_encode($request->attachment),
            );

            if (isset($request->question_coment) && isset($request->question_coment_fr)) {
                $com_data = array(
                    'question_comment' => $request->question_coment,
                    'question_comment_fr' => $request->question_coment_fr,
                );
                $parent_question_array = array_merge($parent_question_array, $com_data);
            }

            if ($request->q_type == 'data') {
                $data_inv = ['is_data_inventory_question' => 1 ];
                $parent_question_array = array_merge($parent_question_array, $data_inv);
            }

            $parent_question_id = DB::table('questions')->insertGetId($parent_question_array);

            DB::table('questions')->where('id', $parent_question_id)->update(['form_key' => 'q-' . $parent_question_id]);

            $__sec_num = $section_num - 1;
            $parent_sord_order_num = $__sec_num + ($last_question_num / 100);
            $parent_sort_order = DB::table('form_questions')->insertGetId([
                'form_id' => $request->form_id,
                'question_id' => $parent_question_id,
                'sort_order' => $parent_sord_order_num,
            ]);

            if (is_array($request->s_q_type)) {
                foreach ($request->s_q_type as $key => $question_type) {
                    $allow_attach = 0;
                    $accepted_files = "";
                    if ($attachments[$key]){
                        $allow_attach = 1;
                        $accepted_files = json_encode($request->attachment[$key+1]);
                    }


                    $section_num = DB::table('admin_form_sections')->where('id', $parent_question_array['question_section_id'])->where('form_id', $request->form_id)->pluck('sec_num')->first();
                    $last_question_num = DB::table('questions')->where('question_section_id', $request->this_section_id)->where('question_num', '!=', null)->count();

                    if ($last_question_num == 0) 
                    {
                        $last_question_num = 1;
                    } 
                    else {
                        $last_question_num++;
                    }

                    $question_number = $section_num . '.' . $last_question_num;

                    $sort_order = DB::table('questions')->where('form_id', $request->form_id)->count();

                    if ($question_type == 'dd') {
                        $child_question_array = [];
                        $obj = (object) null;
                        $obj_fr = (object) null;
                        $data = (object) null;
                        $data_fr = (object) null;
                        $data = (object) array(
                            "comment" => $request->s_question_title[$key],
                            "data" => 'country_list',
                        );

                        $data_fr = (object) array(
                            "comment" => $request->s_question_title_fr[$key],
                            "data" => 'country_list',
                        );

                        $obj->dd = $data;
                        $obj_fr->dd = $data;

                        $child_question_array = array(
                            'question'              => $request->s_question_title[$key],
                            'question_fr'           => $request->s_question_title_fr[$key],
                            'question_short'        => $request->question_title_short,
                            'question_short_fr'     => $request->question_title_short_fr,
                            'question_num'          => null,
                            'options'               => ($request->s_question_options[$key] != '0') ? str_replace(',', ', ', $request->s_question_options[$key]) : '',
                            'options_fr'            => ($request->s_question_options_fr[$key] != '0') ? str_replace(',', ', ', $request->s_question_options_fr[$key]) : '',
                            'question_section'      => null,
                            'type'                  => 'cc',
                            'question_section_id'   => $request->this_section_id,
                            'question_category'     => 2,
                            'form_id'               => $request->form_id,
                            'question_info'         => json_encode($obj),
                            'question_info_fr'      => json_encode($obj_fr),
                            'parent_q_id'           => $parent_question_id,
                            'attachment_allow'      => $allow_attach,
                            'attachments'           => $accepted_files
                        );
                        if (isset($request->s_question_coment[$key]) && isset($request->s_question_coment_fr[$key])) {
                            $com_data = array(
                                'question_comment' => $request->s_question_coment[$key],
                                'question_comment_fr' => $request->s_question_coment_fr[$key],
                            );
                            $question_array = array_merge($child_question_array, $com_data);
                        }

                        $child_question_id = DB::table('questions')->insertGetId($child_question_array);

                        DB::table('questions')->where('id', $child_question_id)->update(['form_key' => 'q-' . $child_question_id]);

                        $__sec_num = $section_num - 1;
                        $child_sord_order_num = $__sec_num + ($last_question_num / 100);
                        $child_sort_order = DB::table('form_questions')->insertGetId([
                            'form_id' => $request->form_id,
                            'question_id' => $child_question_id,
                            'sort_order' => $child_sord_order_num,
                        ]);
                        continue;
                    } else {

                        $child_question_array = array(
                            'question'              => $request->s_question_title[$key],
                            'question_fr'           => $request->s_question_title_fr[$key],
                            'question_short'        => $request->question_title_short,
                            'question_short_fr'     => $request->question_title_short_fr,
                            'question_num'          => null,
                            'options'               => ($request->s_question_options[$key] != '0') ? str_replace(',', ', ', $request->s_question_options[$key]) : '',
                            'options_fr'            => ($request->s_question_options_fr[$key] != '0') ? str_replace(',', ', ', $request->s_question_options_fr[$key]) : '',
                            'question_section'      => null,
                            'type'                  => $question_type,
                            'question_section_id'   => $request->this_section_id,
                            'question_category'     => 2,
                            'form_id'               => $request->form_id,
                            'parent_q_id'           => $parent_question_id,
                            'attachment_allow'      => $allow_attach,
                            'attachments'           => $accepted_files
                        );
                    }

                    if (isset($request->s_question_coment[$key]) && isset($request->s_question_coment_fr[$key])) {
                        $com_data = array(
                            'question_comment' => $request->s_question_coment[$key],
                            'question_comment_fr' => $request->s_question_coment_fr[$key],
                        );
                        $child_question_array = array_merge($child_question_array, $com_data);
                    }

                    if ($request->q_type == 'data') {
                        $data_inv = array(
                            'is_data_inventory_question' => 1,
                        );
                        $child_question_array = array_merge($child_question_array, $data_inv);
                    }

                    $child_question_id = DB::table('questions')->insertGetId($child_question_array);

                    DB::table('questions')->where('id', $child_question_id)->update(['form_key' => 'q-' . $child_question_id]);

                    $__sec_num = $section_num - 1;
                    $child_sord_order_num = $__sec_num + ($last_question_num / 100);
                    $child_sort_order = DB::table('form_questions')->insertGetId([
                        'form_id' => $request->form_id,
                        'question_id' => $child_question_id,
                        'sort_order' => $child_sord_order_num,
                    ]);

                }
            }

            return redirect()->back()->with('success', __('Successfully Added Section'));

        }

        //************************************************************************************************************************
        //For Type cc
        // dd($request->all());//
        // if(is_array($request->s_q_type)){
        $this->validate($request, [
            'question_title' => 'required',

            's_question_title' => 'required|array',
            's_question_title.*' => 'required|min:1',

            's_question_title_fr' => 'required|array',
            's_question_title_fr.*' => 'required|min:1',

            's_q_type' => 'required|array',
            's_q_type.*' => 'required|min:1',

            's_question_options' => 'array|required_if:s_q_type.*,sc',
            's_question_options_fr' => 'array|required_if:s_q_type.*,mc',

            's_question_options' => 'array|required_if:s_q_type.*,mc',
            's_question_options_fr' => 'array|required_if:s_q_type.*,sc',

            // 's_question_options_fr.*'=>'required_unless:s_q_type.*,sc',
            // 's_question_options_fr.*'=>'required_unless:s_q_type.*,mc',

            // 's_question_options.*'=>'required_unless:s_q_type.*,mc',
            // 's_question_options.*'=>'required_unless:s_q_type.*,sc',

        ],
            [
                's_q_type.*' => __('Please Select atleast one sub question to proceed'),
                's_question_title.*' => __('Please Provide English Sub question title for all questions to proceed'),
                's_question_title_fr.*' => __('Please Provide French Sub question title for all questions to proceed'),

                'question_title.required' => __('English Question Title Can Not Be Empty.'),
                'question_title_fr.required' => __('French Question Title Can Not Be Empty.'),
                // 's_question_options.*' => 'Please provide English Question Options for all sub questions.',
                // 's_question_options_fr.*' => 'Please provide French Question Options for all sub questions.',

            ]
        );

        // dd('in array section' , $request->all());

        foreach ( $request->s_q_type as $key => $type ) {
            switch ($type) {
                case 'mc':
                    $obj->mc = [];
                    $obj_fr->mc = [];

                    foreach ($request->s_q_type as $array_key => $array_data) {
                        if ($array_data == $type) {

                            $data = (object) null;
                            $data_fr = (object) null;

                            $all_options = array();
                            $all_options_fr = array();

                            foreach ($request->s_question_options as $opt) {
                                if (strpos($opt, ',') !== false) {
                                    array_push($all_options, explode(",", $opt));
                                } else {
                                    $all_options[] = $opt;
                                }

                            }

                            if (is_array($all_options[0])) {
                                $all_options = $all_options[0];
                            }
                            if (count($all_options) > 1) {
                                foreach ($all_options as $o) {
                                    $data = (object) array(
                                        "data" => $o,
                                    );
                                    $obj->mc[] = $data;
                                }

                            } else {

                                $data = (object) array(
                                    "data" => $all_options[0],
                                );
                                $obj->mc = $data;

                            }
                            //---------------------------------For Fr
                            foreach ($request->s_question_options_fr as $opt) {
                                if (strpos($opt, ',') !== false) {
                                    array_push($all_options_fr, explode(",", $opt));
                                } else {
                                    $all_options_fr[] = $opt;
                                }

                            }

                            if (is_array($all_options_fr[0])) {
                                $all_options_fr = $all_options_fr[0];
                            }
                            if (count($all_options_fr) > 1) {
                                foreach ($all_options_fr as $o) {
                                    $data = (object) array(
                                        "data" => $o,
                                    );
                                    $obj_fr->mc[] = $data;
                                }

                            } else {

                                $data = (object) array(
                                    "data" => $all_options_fr[0],
                                );
                                $obj_fr->mc = $data;

                            }

                        }
                    }
                    break;
                case 'sc':
                    // $obj->sc=[];
                    // foreach ($request->s_q_type as $array_key=>$array_data) {
                    //      if($array_data == $type){
                    //       $data = (object)null;
                    //        $all_options = array();
                    //       foreach ($request->s_question_options as $opt) {
                    //       if (strpos($opt, ',') !== false) {
                    //                 array_push($all_options , explode(",",$opt));
                    //             }else{
                    //                 $all_options[] = $opt;
                    //             }

                    //       }
                    //          if(is_array($all_options[0])){
                    //             $all_options = $all_options[0];
                    //       }
                    //       if(count($all_options) > 1){
                    //       foreach ($all_options as $o) {
                    //            $data=(object)array(
                    //                     "data"   => $o,
                    //                     );
                    //                  $obj->sc[] = $data;
                    //       }

                    //   }
                    //       else{

                    //         $data=(object)array(
                    //                     "data"   => $all_options[0],
                    //                     );
                    //         $obj->sc = $data;

                    //       }

                    //   }
                    // }
                    $obj->sc = [];
                    $obj_fr->sc = [];

                    foreach ($request->s_q_type as $array_key => $array_data) {
                        if ($array_data == $type) {

                            $data = (object) null;
                            $data_fr = (object) null;

                            $all_options = array();
                            $all_options_fr = array();

                            foreach ($request->s_question_options as $opt) {
                                if (strpos($opt, ',') !== false) {
                                    array_push($all_options, explode(",", $opt));
                                } else {
                                    $all_options[] = $opt;
                                }

                            }

                            if (is_array($all_options[0])) {
                                $all_options = $all_options[0];
                            }
                            if (count($all_options) > 1) {
                                foreach ($all_options as $o) {
                                    $data = (object) array(
                                        "data" => $o,
                                    );
                                    $obj->sc[] = $data;
                                }

                            } else {

                                $data = (object) array(
                                    "data" => $all_options[0],
                                );
                                $obj->sc = $data;

                            }
                            //---------------------------------For Fr
                            foreach ($request->s_question_options_fr as $opt) {
                                if (strpos($opt, ',') !== false) {
                                    array_push($all_options_fr, explode(",", $opt));
                                } else {
                                    $all_options_fr[] = $opt;
                                }

                            }

                            if (is_array($all_options_fr[0])) {
                                $all_options_fr = $all_options_fr[0];
                            }
                            if (count($all_options_fr) > 1) {
                                foreach ($all_options_fr as $o) {
                                    $data = (object) array(
                                        "data" => $o,
                                    );
                                    $obj_fr->sc[] = $data;
                                }

                            } else {

                                $data = (object) array(
                                    "data" => $all_options_fr[0],
                                );
                                $obj_fr->sc = $data;

                            }

                        }
                    }
                    break;
                case 'qa':
                    $obj->qa = [];
                    $obj_fr->qa = [];

                    foreach ($request->s_q_type as $array_key => $array_data) {
                        if ($array_data == $type) {

                            $data = (object) null;
                            $data_fr = (object) null;

                            $data = (object) array(
                                "comment" => $request->s_question_title[$array_key],
                            );
                            $data_fr = (object) array(
                                "comment" => $request->s_question_title_fr[$array_key],
                            );
                            $obj->qa[] = $data;
                            $obj_fr->qa[] = $data_fr;

                        }
                    }
                    break;
                case 'dd':
                    $obj->dd = [];
                    $obj_fr->dd = [];

                    foreach ($request->s_q_type as $array_key => $array_data) {
                        // dd($array_data , $type);
                        if ($array_data == $type) {
                            $data = (object) null;
                            $data_fr = (object) null;

                            $data = (object) array(
                                "comment" => $request->s_question_title[$array_key],
                                "data" => 'country_list',
                            );
                            $data_fr = (object) array(
                                "comment" => $request->s_question_title_fr[$array_key],
                                "data" => 'country_list',
                            );
                            $obj->dd = $data;
                            $obj_fr->dd = $data;

                        }
                    }

                    break;

                default:
                    // code...
                    break;
            }

        }

        // dd(json_encode($obj),json_encode($obj_fr),$request->all());

        $section_num = DB::table('admin_form_sections')->where('id', $request->this_section_id)->where('form_id', $request->form_id)->pluck('sec_num')->first();
        $last_question_num = DB::table('questions')->where('question_section_id', $request->this_section_id)->where('question_num', '!=', null)->count();

        if ($last_question_num == 0) {
            $last_question_num = 1;
        } else {
            $last_question_num++;
        }
        $question_number = $section_num . '.' . $last_question_num;

        $sort_order = DB::table('questions')->where('form_id', $request->form_id)->count();

        $question_array = array(
            'question' => $request->question_title,
            'question_fr' => $request->question_title_fr,
            'question_short' => $request->question_title_short,
            'question_short_fr' => $request->question_title_short_fr,
            'question_num' => $question_number,
            'options' => $request->question_options,
            'options_fr' => $request->question_options_fr,
            'question_section' => 'ahmad saeed test',
            'question_section_id' => $request->this_section_id,
            'question_category' => 1,
            'question_info' => json_encode($obj),
            'question_info_fr' => json_encode($obj_fr),
            'form_id' => $request->form_id,
            'type' => 'cc',
        );

        if (isset($request->question_coment) && isset($request->question_coment)) {
            $com_data = array(
                'question_comment' => $request->question_coment,
                'question_comment_fr' => $request->question_coment_fr,
            );
            $question_array = array_merge($question_array, $com_data);
        }

        // dd($question_array);
        $question_id = DB::table('questions')->insertGetId($question_array);
        DB::table('questions')->where('id', $question_id)->update(['form_key' => 'q-' . $question_id]);

        $__sec_num = $section_num - 1;
        $sord_order_num = $__sec_num + ($last_question_num / 100);
        DB::table('form_questions')->insert([
            'form_id' => $request->form_id,
            'question_id' => $question_id,
            // 'sort_order'=>$sort_order++,
            'sort_order' => $sord_order_num,

        ]);

        return redirect()->back()->with('success', 'Successfully Added Section');

        // }
    }

    public function add_question_to_form(Request $request){
        $this->validate($request, [
            'question_title'        => 'required',
            'question_title_fr'     => 'required',
            'question_options'      => 'required_if:q_type,mc|min:1',
            'question_options_fr'   => 'required_if:q_type,mc|min:1',
            'question_options'      => 'required_if:q_type,sc|min:1',
            'question_options_fr'   => 'required_if:q_type,sc|min:1',
            'q_type'                => 'required',
         ], [
            'question_title.required'           => __('English Question Can Not Be Empty.'),
            'question_title_fr.required'        => __('French Question Can Not Be Empty.'),
            'question_options.required_if'      => __('English Question Options Can Not Be Empty.'),
            'question_options_fr.required_if'   => __('French Question Options Can Not Be Empty.'),
            'question_options.min'              => __('Please provide atleast one English option to proceed'),
            'question_options_fr.min'           => __('Please provide atleast one French option to proceed'),
            'q_type.required'                   => __('No Question is selected.'),
        ]);

        $allow_attach = 0;
        if ($request->add_attachments_box){
            $allow_attach = 1;
        }
        $section_num = DB::table('admin_form_sections')->where('id', $request->this_section_id)->where('form_id', $request->form_id)->pluck('sec_num')->first();

        $question_ids = DB::table('questions')->where('question_section_id', $request->this_section_id)->where('question_num', '!=', null)->pluck('id');

        $last_sort_order = DB::table('form_questions')->whereIn('question_id', $question_ids)->orderby('sort_order', 'DESC')->get();
        $last_sort_order = $last_sort_order[0]->sort_order;
        $last_sort_order = (int) explode('.', $last_sort_order)[1];
        $current_order = $last_sort_order + 1;
        $last_question_num = DB::table('questions')->where('question_section_id', $request->this_section_id)->where('question_num', '!=', null)->orderBy('question_num', 'DESC')->pluck('question_num')->first();
        $last_question_num = explode('.', $last_question_num)[1];

        if ($last_question_num == 0) {
            $last_question_num = 1;
        } else {
            $last_question_num++;
        }
        $question_number = $section_num . '.' . $last_question_num;
        $pre_sort_order = DB::table('form_questions')->where('form_id', $request->form_id)->orderBy('sort_order', 'desc')->first();

        $question_array = [
            'question'              => $request->question_title,
            'question_fr'           => $request->question_title_fr,
            'question_short'        => $request->question_title_short,
            'question_short_fr'     => $request->question_title_short_fr,
            'question_num'          => $question_number,
            'options'               => str_replace(",", ", ", $request->question_options),
            'options_fr'            => str_replace(",", ", ", $request->question_options_fr),
            'question_section'      => '',
            'question_section_id'   => $request->this_section_id,
            'question_category'     => 1,
            'form_id'               => $request->form_id,
            'type'                  => $request->q_type,
            'dropdown_value_from'   => $request->dropdown_value_from,
            'not_sure_option'       => $request->add_not_sure_box,
            'attachment_allow'      => $allow_attach,
            'attachments'           => json_encode($request->attachment),
        ];

        if (isset($request->question_coment) && isset($request->question_coment_fr)) {
            $com_data = array(
                'question_comment' => $request->question_coment,
                'question_comment_fr' => $request->question_coment_fr,
            );
            $question_array = array_merge($question_array, $com_data);
        }

        $question_id = DB::table('questions')->insertGetId($question_array);

        DB::table('questions')->where('id', $question_id)->update(['form_key' => 'q-' . $question_id]);

        $__sec_num = $section_num - 1;
        $sord_order_num = $__sec_num + ($current_order / 100);
        DB::table('form_questions')->insert([
            'form_id' => $request->form_id,
            'question_id' => $question_id,
            'sort_order' => $sord_order_num,
        ]);

        return redirect()->back()->with('success', __('Successfully Added Section'));

    }

    public function chnageQuestionLabel(Request $request){
        if (isset($request->question_title_fr)) {
            $question_id = explode('-', $request->question_id);
            // dd($question_id[0]);
            DB::table('questions')->where('id', $question_id[0])->update(['question_fr' => $request->question_title_fr]);
        } else {
            DB::table('questions')->where('id', $request->question_id)->update(['question' => $request->question_title]);
        }
    }

    public function update_options(Request $request){
        // dd($request->all());
        DB::table('questions')->where('id', $request->question_id)->update(['options' => str_replace(",", ", ", $request->updated_options)]);
        return redirect()->back()->with('success', __('English Options Updated Successfully'));

    }

    public function update_options_fr(Request $request){
        // dd($request->all());
        DB::table('questions')->where('id', $request->question_id)->update(['options_fr' => str_replace(",", ", ", $request->updated_options_fr)]);
        return redirect()->back()->with('success', __('French Options Updated Successfully'));

    }

    public function change_question_comment(Request $request){
        // dd($request->all());
        if (isset($request->question_comment_fr)) {
            $question_id = explode('-', $request->question_id);
            DB::table('questions')->where('id', $question_id[0])->update(['question_comment_fr' => $request->question_comment_fr]);
        } else {
            DB::table('questions')->where('id', $request->question_id)->update(['question_comment' => $request->question_comment]);
        }
    }

    //*********************************************************************************************
    
    public function delete_question(Request $request){
        $question = DB::table('questions')->where('id', $request->question_id)->first();
        $section = DB::table('admin_form_sections')->where('id', $request->question_section_id)->first();
        $all_current_section_questions = DB::table('questions')
            ->where('form_id', $question->form_id)
            ->where('question_section_id', $question->question_section_id)
            ->where('id', '>', (int) $question->id)
            ->orderBy('id', 'asc')
            ->get();
        switch ($request->question_type) {
            //**********************************************************
            //**********************************************************
            case 'normal':
                $this->delete_update_question_num($question, $all_current_section_questions);
                DB::table('questions')->where('id', $question->id)->delete();
                DB::table('form_questions')->where('question_id', $question->id)->delete();
                $this->del_section_if_there_is_no_question_in_id($question->question_section_id);
                break;
            //**********************************************************
            //**********************************************************
            case 'parent':
                $this->delete_update_question_num($question, $all_current_section_questions);
                $child_ids = DB::table('questions')->where('parent_q_id', $question->id)->pluck('id');
                DB::table('questions')->wherein('id', $child_ids)->delete();
                DB::table('form_questions')->wherein('question_id', $child_ids)->delete();
                DB::table('questions')->where('id', $question->id)->delete();
                $this->del_section_if_there_is_no_question_in_id($question->question_section_id);

                break;
            //***********************************************************
            //**********************************************************
            case 'child':
                DB::table('questions')->where('id', $question->id)->delete();
                DB::table('form_questions')->where('question_id', $question->id)->delete();
                $this->del_section_if_there_is_no_question_in_id($question->question_section_id);
                break;
            //***********************************************************
            //**********************************************************
            default:
                // code...
                break;
        }
    }
    
    public function delete_update_question_num($question, $question_array){
        foreach ($question_array as $key => $value) {
            if ($value->question_num != null) {
                $num = explode(".", $value->question_num);
                $new_number = $num[0] . '.' . ($num[1] - 1);
                DB::table('questions')->where('id', $value->id)->update(['question_num' => $new_number]);
                // dd($new_number);
                // dd('in upde numbering function',$value->question_num , $num[1]);
            }
            // dd($)
        }

    }
    
    public function del_section_if_there_is_no_question_in_id($sec_id){
        $question_count = DB::table('questions')->where('question_section_id', $sec_id)->count();
        if ($question_count > 0) {

        } else {
            DB::table('admin_form_sections')->where('id', $sec_id)->delete();
        }
    }

    public function unlock_form(Request $request){

        $table = 'user_form_links';
        $form_link_attr = 'form_link';
        $link = $request->input('link');
        $lock_status = $request->input('lock_status');

        if ($request->input('user_type') == 'in') {
            $table = 'user_form_links';
            $form_link_attr = 'form_link_id';
        }

        DB::table($table)->where($form_link_attr, $link)->update(['is_locked' => $lock_status]);

        return response()->json(['status' => 'success', 'msg' => __('status changed')]);
    }

    public function change_form_access(Request $request){
        $table = 'user_form_links';
        $form_link_attr = 'form_link';
        $link = $request->input('link');
        $action = $request->input('action');

        if ($request->input('user_type') == 'in') {
            $table = 'user_form_links';
            $form_link_attr = 'form_link_id';
        }

        DB::table($table)->where($form_link_attr, $link)->update(['is_accessible' => $action]);

        return response()->json(['status' => 'success', 'msg' => __('status changed')]);
    }

    public function update_sc_comment(Request $request){
        // dd($request->all());
        $question = DB::table('questions')->Where('id', $request->question_id)->first();
        // dd($question);
        $lan_type = ($request->type == 'fr') ? 'question_info_fr' : 'question_info';
        $data = json_decode($question->$lan_type);
        // $data = $data;
        $q_type = $request['q_type'];
        $json_data = $data->$q_type;
        // dd($json_data , $request->all());
        if ($q_type == 'dd') {
            $json_data->comment = $request->comment;
        }

        if ($q_type == 'qa') {
            if (is_array($json_data)) {
                // dd('is_array',$json_data , $request->all());
                $json_data[$request->number_of_comment]->comment = $request->comment;

            } else {
                // dd('not',$json_data , $request->all());
                $json_data->comment = $request->comment;
            }
            // foreach ($json_data as $value) {

            // $value->comment = $request->comment;
            // }
        }
        // dd($json_data);
        $data->$q_type = $json_data;
        DB::table('questions')->Where('id', $request->question_id)->update([
            $lan_type => json_encode($data),
        ]);
    }

    public function update_cc_options(Request $request){
        // dd($request->all());
        $question = DB::table('questions')->where('form_key', $request->form_key)->first();
        $type = ($request->type == 'fr') ? 'question_info_fr' : 'question_info';
        $data = json_decode($question->question_info);
        $data_fr = json_decode($question->question_info_fr);

        $sc_data = $data->sc;
        $sc_data_fr = $data_fr->sc;

        $all_options = explode(',', $request->new_options);
        $all_options_fr = explode(',', $request->new_options_fr);
        // dd($all_options , $all_options_fr);

        $new_options_array = [];
        $new_options_array_fr = [];

        $empty_obj = (object) null;
        $empty_obj_fr = (object) null;

        if (count($all_options) > 1) {
            foreach ($all_options as $o) {
                $empty_obj = (object) array(
                    "data" => $o,
                );
                $new_options_array[] = $empty_obj;
            }
        } else {
            $empty_obj = (object) array(
                "data" => $all_options[0],
            );
            $new_options_array = $empty_obj;
        }
        //***************fr
        if (count($all_options_fr) > 1) {
            foreach ($all_options_fr as $o) {
                $empty_obj_fr = (object) array(
                    "data" => $o,
                );
                $new_options_array_fr[] = $empty_obj_fr;
            }
        } else {
            $empty_obj_fr = (object) array(
                "data" => $all_options_fr[0],
            );
            $new_options_array_fr = $empty_obj_fr;
        }
        $data->sc = $new_options_array;
        $data_fr->sc = $new_options_array_fr;
        // dd($data , $data_fr);

        DB::table('questions')->where('form_key', $request->form_key)->update([
            'question_info' => json_encode($data),
            'question_info_fr' => json_encode($data_fr),

        ]);
    }

    public function updateSorting(Request $request){
        $form_id = $request->form_id;
        $id = $request->fq_id;
        $sort_order = $request->sort_order;

        $included = DB::table('form_questions')
            ->where('form_id', $form_id)
            ->where('fq_id', "!=", $id)
            ->where('sort_order', $sort_order)
            ->count();
        if ($included > 0) {
            return response()->json([
                "msg" => "Sorting Already Exist!",
            ]);
        } else {
            DB::table('form_questions')->where('fq_id', $id)
                ->update([
                    'sort_order' => $sort_order,
                ]);
            return response()->json([
                "status" => 200,
                "msg" => "Sort Order Updated Successfully!",
            ]);
        }
    }
}
