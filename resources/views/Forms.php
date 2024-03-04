

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
Use \Carbon\Carbon;

class Forms extends Controller
{
    
    public function __construct()
    {

    }
    
    public function ajax_update_form_section_heading (Request $request)
    {
        if ($request->input('is_admin') && $request->input('is_admin') == 1)
        {
            DB::table('admin_form_sections')
            ->where(['id'               => $request->input('form_section_id')])
            ->update(['section_title'   => $request->input('title')]);
        }
        else
        {
            DB::table('client_form_sections')
            ->updateOrInsert([
                                'client_id'            => $request->input('user_id'),
                                'admin_form_sec_id'    => $request->input('form_section_id'),
                                'form_id'              => $request->input('form_id'),
                            ],
                            [
                                'section_title'        => $request->input('title'),
                                'client_id'            => $request->input('user_id'),
                                'admin_form_sec_id'    => $request->input('form_section_id'),
                                'form_id'              => $request->input('form_id'),
                                'updated_by'           => $request->input('updated_by')
                            ]);
        }
    }   
    
    
    // form view only with fields disabled
    function view_form ($id = 1)
    {
        // in case if user is not admin, check if the form is assigned to company user or its related users
        if (Auth::user()->role != 1)
        {
            $client_id = Auth::user()->client_id;
            
            // get assignee list
            
            /*
            SELECT * FROM client_forms WHERE client_id = 76 AND form_id = 2            
            */
            
            $form_assignee = DB::table('client_forms')
                               ->where('client_id', '=', $client_id)
                               ->where('form_id',   '=', $id)
                               ->first();
                               
            // form is not assigned, don't allow the form with request id to view                   
            if (empty($form_assignee))
            {
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
                       ->join('form_questions', 'forms.id',                                      '=', 'form_questions.form_id')
                       ->join('questions',      'form_questions.question_id',                    '=', 'questions.id')
                       ->where('forms.id',                                                       '=', $id)
                       ->orderBy('form_questions.sort_order')
                       ->leftJoin('admin_form_sections as afs', 'questions.question_section_id', '=', 'afs.id');

        if (Auth::user()->role == 1)
        {
            $form_info = $form_info
                        ->select('*',
                                 'afs.id as afs_sec_id',
                                 'afs.section_title as admin_sec_title',
                                 'questions.question_section_id as q_sec_id')
                        ->get();

            $user_type = 'admin';
            $parent_template = 'admin.layouts.admin_app';
        }
        elseif (Auth::user()->role == 2)
        {
            // LEFT JOIN client_form_sections as cfs ON (cfs.admin_form_sec_id = afs.id AND cfs.client_id = 23) -- only in case of client
            
            // $client_id = Auth::id();
            $client_id = Auth::user()->client_id;


            $form_info = $form_info
                        ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = '.$client_id))
                        ->select('*',
                                 'afs.id as afs_sec_id',
                                 'afs.section_title as admin_sec_title',
                                 'cfs.id as cfs_sec_id',
                                 'cfs.section_title as client_sec_title',
                                 'questions.question_section_id as q_sec_id')
                        ->get();
            
            
            $user_type = 'client';
            $parent_template = 'admin.client.client_app';
        }
        elseif (Auth::user()->role == 3)
        {
            $client_id = Auth::user()->client_id;

            $form_info = $form_info
                        ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = '.$client_id))
                        ->select('*',
                                 'afs.id as afs_sec_id',
                                 'afs.section_title as admin_sec_title',
                                 'cfs.id as cfs_sec_id',
                                 'cfs.section_title as client_sec_title',
                                 'questions.question_section_id as q_sec_id')
                        ->get();
            
            $user_type = 'reg_user';
            $parent_template = 'admin.client.client_app';
        }
        else
        {
            $client_id = Auth::user()->client_id;

            $form_info = $form_info
                        ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = '.$client_id))
                        ->select('*',
                                 'afs.id as afs_sec_id',
                                 'afs.section_title as admin_sec_title',
                                 'cfs.id as cfs_sec_id',
                                 'cfs.section_title as client_sec_title',
                                 'questions.question_section_id as q_sec_id')
                        ->get();            
            
            $user_type = 'ext_user';
            $parent_template = 'admin.client.client_app';
        }
        
        if ($id != 2)
        {
            $custom_fields = '';
            foreach ($form_info as $key => $questions)
            {
                
                if (trim($questions->type) == 'cc')
                {
                    $fields_info = json_decode($questions->question_info);
                    $field_html = '';    
                    $is_asset_case = false;

                    foreach ($fields_info as $fkey => $field)
                    {
                        if ($fkey == 'mc')
                        {
                            $value = '';
                            
                            if (isset($field->data) && gettype($field->data) == 'string')
                            {
                                $case_name = '';
                                if (strtolower($field->data) == 'not sure' || strtolower($field->data) == 'not applicable')
                                {
                                    $case_name = 'case-name="Not Sure"';
                                    $mc_selected = 'es-selected';
                                }
                                
                                
                                $value = $field->data;
                                $mc_selected = '';
                                
                                if (isset($fill_custom_response->mc))
                                {
                                      $filled_resp = $fill_custom_response->mc;
                                      $mc_selected = 'es-selected';
                                }                               
                                
                                $field_html .= '<section class="options">';
                                $field_html .= '<ul id="easySelectable" class="easySelectable">';
                                $field_html .= '<li class="es-selectable '.$mc_selected.'" name="'.$questions->form_key.'" q-id="" custom="1" '.$case_name.' value="'.$value.'" type="mc">'.$value.'</li>';
                                $field_html .= '</ul></section>';                               
                                
                            }
                            
                        }
                        
                        // added case for sc 
                        if ($fkey == 'sc')
                        {
                            $value = '';
                            $sc_fields = [];
                            
                            if (gettype($field) == 'array')
                            {
                                $sc_fields = $field;
                            }
                            else
                            {
                                $sc_fields[0] = $field;
                            }

                            $field_html .= '<section class="options">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';                           

                            foreach ($sc_fields as $sc_field)
                            {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string')
                                {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable')
                                    {
                                        $case_name = 'case-name="Not Sure"';
                                        //$mc_selected = 'es-selected';
                                    }
                                    
                                    
                                    $value = $sc_field->data;
                                    $mc_selected = '';
                                      
                                    $field_html .= '<li class="es-selectable not-unselectable '.$mc_selected.'" name="'.$questions->form_key.'" q-id="" custom="1" '.$case_name.' value="'.$value.'" type="sc">'.$value.'</li>';
                                
                                }                               
                            }
                            
                            $field_html .= '</ul></section>';                           
                        } 
                        // added case for sc 
                        
                        if ($fkey == 'dd')
                        {
                            $field_comment = '';
                            if (isset($field->comment))
                            {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data))
                            {
                                if ($field->data == 'assets')
                                {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('questions')->where('question_category', '=', 1)->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';
                                    $field_html .= '<select class="form form-control" name="'.$questions->form_key.'" q-id="" custom="1" type="dd" case-name="assets">';
                                    foreach ($assets_query as $akey => $aquery)
                                    {
                                          $selected = '';
                                          $field_html .= '<option value="'.$aquery->question.'" '.$selected.'>'.$aquery->question.'</option>';
                                    }
                                    $field_html .= '</select></div>'; 
                                    
                                }
                                if ($field->data == 'country_list')
                                {
                                    $countries         = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';
                                    $field_html .= '<select class="form form-control" name="'.$questions->form_key.'" q-id="" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name)
                                    {
                                          $selected = '';                                       
                                          $field_html .= '<option value="'.$country_name.'" '.$selected.'>'.$country_name.'</option>';
                                    }
                                    $field_html .= '</select></div>';                                   
                                }
                            }
                        }
                        
                        if ($fkey == 'qa')
                        {
                            $qa_fields = [];
                            
                            $field_name_array = '';
                            $multi_qa_case_str = '';
                            $json_format = false;
                            
                            if (gettype($field) == 'array')
                            {
                                $qa_fields = $field;
                                $field_name_array = '[]';
                                $multi_qa_case_str = 'case-name="multi-qa"';
                                $json_format = true;

                            }
                            else
                            {
                                $qa_fields[0] = $field;
                            }
                            
                            $asset_attr_str = '';
                            if ($is_asset_case === true)
                            {
                                $asset_attr_str = 'case-name="assets"';
                            }
                            
                            $filled_resp = '';   
                            $qa_fill_index = 0;
      
                            foreach ($qa_fields as $qa_key => $qa_field)
                            {
                                $field_comment = '';
                                if (isset($qa_field->comment))
                                {
                                    $field_comment = $qa_field->comment;
                                }
                                
                                $tbox_val = '';
                                                            
                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';                              
                                $field_html .=  '<form>
                                        <label></label>
                                        <textarea  name="'.$questions->form_key.$field_name_array.'" q-id="" '.$multi_qa_case_str.' '.$asset_attr_str.' rows="4" cols="50" custom="1" type="qa" disabled>'.$tbox_val.'</textarea>
                                    </form>
                                </div>';                                
                                
                            }
                            
                        }

                    }
                         
                 $form_info[$key]->custom_fields = $field_html;
                }
                
            }           
        }
        // dd($questions);
        // dd($form_info);
      
                                    
        return view('forms.view_form_custom',
                                    ['user_type'      => $user_type,
                                    'form_id'         => $id,
                                    'parent_template' => $parent_template,
                                    'questions'       => $form_info,
                                    'title'           => !empty($form_info)?($form_info[0]->title):('title'),
                                    'heading'         => !empty($form_info)?($form_info[0]->title):('heading')]);                   
    }
    
    function in_users_show_form ($form_link_id)
    {
        // dd($form_link_id);
        // get the client_id and user_id of requested form
        $client_id = DB::table('user_forms')
                       ->where('user_forms.form_link_id','=',$form_link_id)
                       ->pluck('client_id')->first();
        
        $user_id   = DB::table('user_forms')
                       ->where('user_forms.form_link_id','=',$form_link_id)
                       ->pluck('user_id')->first();
                       // dd($user_id ."  ".  auth()->user()->id);
                       
        // show 404 if there is no user and client against requested form
        if (empty($client_id) || empty($user_id))
        {
            return abort('404');
        }                       
           
                       // ahmad removed 
        // if (Auth::user()->role != 1)
        // {
        //     // dd('first');
        //     // check if the client is not associated with the requested form link or if in case the user is not super user of client and form is not related to that user, show 404
        //     if ((Auth::user()->role == 2 && Auth::user()->client_id != $client_id) || (Auth::user()->role == 3 && Auth::id() != $user_id && Auth::user()->user_type != 1))
        //     {
        //              // dd('2nd');
        //         return abort('404');    
        //     }           
        // }
        // ahmad reeoved end 15 2 2021


        // dd('walla');
        
   

        $form_info = DB::table('user_forms')
                       ->join('sub_forms',      'user_forms.sub_form_id',     '=', 'sub_forms.id')
                       ->join('form_questions', 'sub_forms.parent_form_id',   '=', 'form_questions.form_id')
                       ->join('questions',      'form_questions.question_id', '=', 'questions.id')
                       ->leftJoin('admin_form_sections  as afs', 'questions.question_section_id', '=', 'afs.id')
                       ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = '.$client_id))
                       ->where('user_forms.form_link_id', '=', $form_link_id)
                       ->orderBy('sort_order')
                       ->select('*',
                                'user_forms.client_id',
                                'user_forms.id as uf_id',
                                'questions.id as q_id',
                                'afs.id as afs_sec_id',
                                'afs.sec_num as afs_sec_num',
                                'afs.section_title as admin_sec_title',
                                'cfs.id as cfs_sec_id',
                                'cfs.section_title as client_sec_title',
                                'user_forms.user_id as u_id',
                                'user_forms.expiry_time as form_expiry_time',
                                'sub_forms.parent_form_id as form_id')
                       ->get();
//                       echo "<pre>";
//      print_r($form_info);exit;
        $expiry_note = '';             
        if (isset($form_info[0]) && strtotime(date('Y-m-d')) > strtotime($form_info[0]->form_expiry_time))
        {
            if (Auth::check())
            {
                if ((Auth::user()->role == 2 || Auth::user()->user_type == 1) && Auth::user()->client_id == $client_id)
                {
                    if ($form_info[0]->is_locked != '1')
                    {
                        $expiry_note = 'The user failed to submit form before expiry time.';               
                    }
                }
                else
                {
                    // return view('user_form_expired');
                    // $expiry_note = 'Failed to submit form before expiry time.'; 
                }
            }
            else
            {
                // return view('user_form_expired');
                // $expiry_note = 'Failed to submit form before expiry time.'; 
            }
        }
        else if (isset($form_info[0]) && !$form_info[0]->is_accessible)
        {
            return view('user_form_not_accessible');
        }

      // fetch already filled info.
      /*
      SELECT *
      FROM  `user_forms`
      JOIN   internal_users_filled_response ON user_forms.user_id = internal_users_filled_response.user_id AND user_forms.sub_form_id = internal_users_filled_response.sub_form_id
      WHERE  user_forms.form_link_id = 'Vo2prUevT8OFzBYdlqAaVH91Zhw8xN2798Zk3raO'
      */

      $filled_info = DB::table('user_forms')
                     ->join('internal_users_filled_response',      'user_forms.user_id',     '=', DB::raw('internal_users_filled_response.user_id AND user_forms.sub_form_id = internal_users_filled_response.sub_form_id'))
                     ->join('questions',      'questions.id',     '=', 'internal_users_filled_response.question_id')
                     ->where('user_forms.form_link_id', '=', $form_link_id)
                     ->select('question_key', 'question_response', 'question_id', 'additional_comment', 'additional_info', 'type', 'custom_case')->get();

                     // dd($filled_info);

     $custom_responses = [];

      $question_key_index = [];
      // dd($filled_info);
      foreach ($filled_info as $key => $user_response)
      {
          if ($user_response->type == 'mc')
          {
              $user_response->question_response = explode(', ', $user_response->question_response);
          }
          
          if ($user_response->custom_case == '1')
          {
              $custom_responses[$user_response->question_key] = $user_response->question_response;
          }

          $question_key_index[$user_response->question_key] =
          [
                'question_response' => $user_response->question_response,
                'question_id'       => $user_response->question_id,
                'question_comment'  => $user_response->additional_comment,
                'question_type'     => $user_response->type,
                'additional_resp'   => $user_response->additional_info
          ];
      }

      
        //if ($id != 2)
        {
            $custom_fields = '';
            $num_of_questions = 0;
            foreach ($form_info as $key => $questions)
            {
                if (trim($questions->type) == 'cc')
                {
                    $fields_info   = json_decode($questions->question_info);
                    $field_html    = ''; 
                    $is_asset_case = false;
                    
                    $fill_custom_response = [];
                    
                    if (isset($custom_responses[$questions->form_key]))
                    {
                        $fill_custom_response = json_decode($custom_responses[$questions->form_key]);
                    }

                    
                    foreach ($fields_info as $fkey => $field)
                    {
                        if ($fkey == 'mc')
                        {
                            $value = '';
                            
                            if (isset($field->data) && gettype($field->data) == 'string')
                            {
                                $case_name = '';
                                if (strtolower($field->data) == 'not sure' || strtolower($field->data) == 'not applicable')
                                {
                                    $case_name = 'case-name="Not Sure"';
                                    $mc_selected = 'es-selected';
                                }
                                
                                
                                $value = $field->data;
                                $mc_selected = '';
                                
                                if (isset($fill_custom_response->mc))
                                {
                                      $filled_resp = $fill_custom_response->mc;
                                      $mc_selected = 'es-selected';
                                }                               
                                
                                $field_html .= '<section class="options">';
                                $field_html .= '<ul id="easySelectable" class="easySelectable">';
                                $field_html .= '<li class="es-selectable '.$mc_selected.'" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" '.$case_name.' value="'.$value.'" type="mc">'.$value.'</li>';
                                $field_html .= '</ul></section>';                               
                                
                            }
                            
                        }
                        
                        // added case for sc 
                        if ($fkey == 'sc')
                        {
                            $value = '';
                            $sc_fields = [];
                            
                            if (gettype($field) == 'array')
                            {
                                $sc_fields = $field;
                            }
                            else
                            {
                                $sc_fields[0] = $field;
                            }

                            $field_html .= '<section class="options">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';                           

                            foreach ($sc_fields as $sc_field)
                            {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string')
                                {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable')
                                    {
                                        $case_name = 'case-name="Not Sure"';
                                        $mc_selected = 'es-selected';
                                    }
                                    
                                    
                                    $value = $sc_field->data;
                                    $mc_selected = '';
                                    
                                    if (isset($fill_custom_response->sc))
                                    {
                                          $filled_resp = $fill_custom_response->sc;
                                          if (strtolower($filled_resp) == strtolower($value))
                                          {
                                                $mc_selected = 'es-selected';
                                          }
                                    }  
                                    
                                    $field_html .= '<li class="es-selectable not-unselectable '.$mc_selected.'" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" '.$case_name.' value="'.$value.'" type="sc">'.$value.'</li>';
                                    
                                }                               
                            }
                            
                            $field_html .= '</ul></section>';                           
                        } 
                        // added case for sc 
                        
                        if ($fkey == 'dd')
                        {
                            $field_comment = '';
                            if (isset($field->comment))
                            {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data))
                            {
                                if ($field->data == 'assets')
                                {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('assets')->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';
                                    $field_html .= '<select class="form form-control" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" type="dd" case-name="assets">';

                                    $selected = '';
                                    if (!isset($fill_custom_response->dd) || (isset($fill_custom_response->dd) && $fill_custom_response->dd == ''))
                                    {
                                        $selected = 'selected';
                                    }                                   
                                    $field_html .= '<option value="Select Option" '.$selected.'>Select Option</option>';
                                    foreach ($assets_query as $akey => $aquery)
                                    {
                                          $selected = '';
                                          if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $aquery->name)
                                          {
                                              $selected = 'selected';
                                          }
                                          $field_html .= '<option value="'.$aquery->name.'" '.$selected.'>'.$aquery->name.'</option>';
                                    }
                                    $field_html .= '</select></div>'; 

                                }
                                if ($field->data == 'country_list')
                                {
                                    $countries         = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';
                                    $field_html .= '<select class="form form-control" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name)
                                    {
                                          $selected = '';
                                          if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $country_name)
                                          {
                                              $selected = 'selected';
                                          }                                     
                                          $field_html .= '<option value="'.$country_name.'" '.$selected.'>'.$country_name.'</option>';
                                    }
                                    $field_html .= '</select></div>';                                   
                                }
                                            
                                $num_of_questions++;
                            }
                        }
                        
                        if ($fkey == 'qa')
                        {
                            $qa_fields = [];
                            
                            $field_name_array = '';
                            $multi_qa_case_str = '';
                            $json_format = false;
                            
                            if (gettype($field) == 'array')
                            {
                                $qa_fields = $field;
                                $field_name_array = '[]';
                                $multi_qa_case_str = 'case-name="multi-qa"';
                                $json_format = true;

                            }
                            else
                            {
                                $qa_fields[0] = $field;
                            }
                            
                            $asset_attr_str = '';
                            if ($is_asset_case === true)
                            {
                                $asset_attr_str = 'case-name="assets"';
                            }
                            
                            $filled_resp = '';
                            if (isset($fill_custom_response->qa))
                            {
                                  $filled_resp = $fill_custom_response->qa;
                            }   
                            
                            $qa_fill_index = 0;
      
                            foreach ($qa_fields as $qa_key => $qa_field)
                            {
                                $field_comment = '';
                                if (isset($qa_field->comment))
                                {
                                    $field_comment = $qa_field->comment;
                                }
                                
                                if (gettype($filled_resp) == 'array')
                                {
                                     $tbox_val = isset($filled_resp[$qa_key])?($filled_resp[$qa_key]):('');
                                }
                                else
                                {
                                    $tbox_val = $filled_resp;
                                }                               
                                
                                
                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';                              
                                $field_html .=  '<form>
                                        <label></label>
                                        <textarea  name="'.$questions->form_key.'_'.$questions->q_id.$field_name_array.'" q-id="'.$questions->q_id.'" '.$multi_qa_case_str.' '.$asset_attr_str.' rows="4" cols="50" custom="1" type="qa">'.$tbox_val.'</textarea>
                                    </form>
                                </div>';                                
                                
                            }
                            
                            $num_of_questions++;
                        }

                    }
                    
                 $field_html .= '<div id="perc-bar-'.$questions->q_id.'" class="barfiller hidden">
                                    <div class="tipWrap">
                                        <span class="tip"></span>
                                        </div>
                                        <span class="fill" id="fill-bar-'.$questions->q_id.'" data-percentage="0"></span>
                                </div>';     
                    
                 $form_info[$key]->custom_fields = $field_html;
                 $form_info[$key]->num_questions = $num_of_questions; 
                }
                
            }           
        }     

      /*
      SELECT *
      FROM   users
      JOIN   user_forms ON user_forms.user_id = users.id
      WHERE  user_forms.form_link_id = 'wPws9qe8zawIRRVQT7pKLsYqH1LIpzZDFPTctC03'
      */

        $user_info = DB::table('users')
                       ->join('user_forms',      'user_forms.user_id',     '=', 'users.id')
                       ->where('user_forms.form_link_id', '=', $form_link_id)
                       ->select('*')->first();
                       
        $hidden_pb = false;
        if (isset($form_info[0]) && !empty($form_info[0]))
        {
            $form_type = DB::table('forms')->where('id', '=', $form_info[0]->form_id)->pluck('type')->first();          
            if ($form_type == 'sar')
            {
                $hidden_pb = true;
            }
        }                      
                         // dd($question_key_index);
                                  
        return view('forms.in_user_form_sec_wise', ['questions' => $form_info,
                                  'hide_pb'      => $hidden_pb,
                                  'filled'         => $question_key_index,
                                  // 'fq_keys'   => $question_keys,
                                //  'accoc_info'=> $accoc_info,
                                  'user_info'      => $user_info,
                                  'title'          => !empty($form_info)?($form_info[0]->title):('title'),
                                  'heading'        => !empty($form_info)?($form_info[0]->title):('heading'),
                                  'expiry_note'    => $expiry_note]);                         
                                  

    }

    // submit form for REGISTERED USER (not used currently)

    // form submission for orgnization / company users
    public function ajax_int_user_submit_form (Request $req)
    {

        // dd($req->all());
//         echo "request ";
//         echo "<pre>";
//         print_r(($req->all()));
//         echo "</pre>";
//         exit;
        if ($req->hasFile('img'))
        {
            $user_form_id = $req->input('user-form-id');
            $form_link_id = $req->input('form-link-id');
            $form_id      = $req->input('form-id');
            $subform_id   = $req->input('subform-id');
            $user_email   = $req->input('email');
            $user_id      = $req->input('user-id');
            $question_id = $req->input('question-id');
            $question_key = $req->input('question-key');
            
            $img_dir_path = "SAR_img_ids/$user_id/";

            $destinationpath=public_path($img_dir_path);
            //File::delete($destinationpath);
            $file=$req->file('img');
            $filename = $file->getClientOriginalName();
            //$ext=$file->getClientOriginalExtension();
            //echo "file ".$filename."<br>";exit;
            $img_name =uniqid().$filename;
            //$destinationpath=public_path('img');
            $file->move($destinationpath,$img_name); 
            
            $file_path = $img_dir_path.$img_name;
            
            DB::table('internal_users_filled_response')
            ->updateOrInsert(
            ['user_form_id' => $user_form_id,
            'form_id'   => $form_id, 
            'sub_form_id' => $subform_id, 
            'question_id' => $question_id, 
            'question_key' => $question_key,
            'user_id'     => $user_id],
            ['question_response' => $file_path, 'custom_case' => 1,  'created' => date('Y-m-d H:i:s')]);            
            
            
            return;
        }        


        $user_form_id = $req->input('user-form-id');
        $form_link_id = $req->input('form-link-id');
        $form_id      = $req->input('form-id');
        $subform_id   = $req->input('subform-id');
        $user_email   = $req->input('email');
        $user_id      = $req->input('user-id');
        $curr_sec     = $req->input('curr-form-sec');   // user current form section
        $is_response_obj  = $req->input('is_response_obj');
        
        DB::table('user_forms')
          ->where([
                    'form_link_id'                           => $form_link_id,
                  ])
          ->update(['curr_sec'                   => $curr_sec]);        

        foreach ($req->all() as $post_key => $user_responses)
        {
            if (strpos($post_key, 'q-') !== false)
            {
                $custom_case = 0;
                $asset_questions = '';
                
                // print_r($user_responses);
                // exit;
                
                if ($is_response_obj == '1' && gettype($user_responses) == 'array')
                {
                    $custom_case = 1;
                    if ($req->input('mul_val_obj') == '1')
                    {
                        //$user_responses = json_encode($user_responses['response']);
                        
                    
                        
                        
                        if (isset($user_responses['response']['qa']))
                        {
                             $asset_questions = $user_responses['response']['qa'];

                        }
                        $user_responses = json_encode($user_responses['response']);

                                              
                        
                    }
                    elseif ($req->input('mul_val_obj') == '2')
                    {
                        $user_responses = array_column($user_responses, 'response');
                        
                        foreach ($user_responses as $key => $value)
                        {
                            $user_responses[$key] = $value[$key];
                        }
                        
                        $qa_response['qa'] = $user_responses;
                        
                        $user_responses = json_encode($qa_response);
                    }
                    else
                    {
                        $resp_array[$user_responses['type']] = $user_responses['response'];
                        $user_responses = json_encode($resp_array);
                    }
  
                    // print_r($asset_questions);
                    // exit;
                }                
                elseif (gettype($user_responses) == 'array')
                {
                    $user_responses = implode(', ', $user_responses);
                }
                
         

                $question_field_name = explode('_', $post_key);

                $question_key        = $question_field_name[0];
                $question_id         = $question_field_name[1];

                /*
                DB::statement('insert into filled_response
                (user_form_id, form_id, sub_form_id, question_id, question_key, user_id, question_response)
                values (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_form_id, $form_id, $subform_id, $question_id, $question_key, $user_id, $user_responses]);
                */
                if ($custom_case == 0)
                {
                        DB::statement('insert into internal_users_filled_response
                        (user_form_id, form_id, sub_form_id, question_id, question_key, custom_case, user_id, question_response, created)
                        values (?, ?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = "'.$user_responses.'"',
                        [$user_form_id, $form_id, $subform_id, $question_id, $question_key, $custom_case, $user_id, $user_responses, date('Y-m-d H:i:s')]);
                }
                else
                {
                    DB::table('internal_users_filled_response')
                    ->updateOrInsert(
                    ['user_form_id' => $user_form_id,
                    'form_id'   => $form_id, 
                    'sub_form_id' => $subform_id, 
                    'question_id' => $question_id, 
                    'question_key' => $question_key,
                    'user_id'     => $user_id],
                    ['question_response' => $user_responses, 'custom_case' => $custom_case,  'created' => date('Y-m-d H:i:s')]);
                    
                    if ($req->input('case_name') && $req->input('case_name') == 'assets')
                    {
                        //gettype($asset_questions);
                        $asset_questions = explode("\n", trim($asset_questions));
                        
                        if (!empty($asset_questions))
                        {
                            // echo 'st';
                            // print_r($asset_questions);
                            
                            foreach ($asset_questions as $asset_question)
                            {
                                if (!empty($asset_question) && !DB::table('assets')->where('name', $asset_question)->exists())
                                {
                                    DB::table('assets')->insert(
                                    ['name' => $asset_question]                                    
                                    );
                                }
                            }

                        }
                        
                    }
                }

            }

            if (strpos($post_key, 'c-') !== false)
            {
                $comment_field_name  = explode(':', $post_key);

                $comment_part        = $comment_field_name[0];
                $question_id         = $comment_field_name[1];

                $comment_part        = explode('-', $comment_part);

                $c                   = $comment_part[0];
                $key                 = $comment_part[1];

                $question_key        = 'q-'.$question_id;
                $question_id         = $key;

                /*
                echo "<pre>";
                print_r($question_id);
                echo "</pre>";
                exit;
                */

                if (DB::table('internal_users_filled_response')
                      ->where([
                            'user_form_id'      => $user_form_id,
                            'sub_form_id'       => $subform_id,
                            'form_id'           => $form_id,
                            'user_id'           => $user_id,
                            'question_id'       => $question_id
                          ])
                      ->exists())
                {
                    DB::table('internal_users_filled_response')
                  ->where([
                            'user_form_id'       => $user_form_id,
                            'sub_form_id'        => $subform_id,
                            'form_id'            => $form_id,
                            'user_id'            => $user_id,
                            'question_id'        => $question_id
                          ])
                  ->update(['additional_comment' => $user_responses]);
                }
                else
                {

                    $insert_data = [
                        'user_form_id'          => $user_form_id,
                        'sub_form_id'           => $subform_id,
                        'form_id'               => $form_id,
                        'question_id'           => $question_id,
                        'question_key'          => $question_key,
                        'user_id'               => $user_id,
                        'additional_comment'    => $user_responses,
                        'question_response'     => '',
                        'created'               => date('Y-m-d H:i:s')];

                    DB::table('internal_users_filled_response')->insert($insert_data);

                }

            }

            if (strpos($post_key, 'd-') !== false)
            {
                $date_field = explode('-', $post_key);

                $question_id         = $date_field[1];
                $question_key        = 'q-'.$question_id;

                if (DB::table('internal_users_filled_response')
                      ->where([
                            'user_form_id'      => $user_form_id,
                            'sub_form_id'       => $subform_id,
                            'form_id'           => $form_id,
                            'user_id'           => $user_id,
                            'question_id'       => $question_id,
                          ])
                      ->exists())
                {               
                    DB::table('internal_users_filled_response')
                      ->where([
                                'user_form_id'          => $user_form_id,
                                'sub_form_id'           => $subform_id,
                                'form_id'               => $form_id,
                                'user_id'               => $user_id,
                                'question_id'           => $question_id
                              ])
                      ->update(['additional_info'       => $user_responses, 
                                'question_response'     => 'Date Picker Option']);
                }
                else
                {
                    $insert_data = [
                        'user_form_id'          => $user_form_id,
                        'sub_form_id'           => $subform_id,
                        'form_id'               => $form_id,
                        'question_id'           => $question_id,
                        'question_key'          => $question_key,
                        'user_id'               => $user_id,
                        'additional_info'       => $user_responses,
                        'question_response'     => 'Date Picker Option',
                        'created'               => date('Y-m-d H:i:s')];

                    DB::table('internal_users_filled_response')->insert($insert_data);                  
                }
            }
        }

    }
    
    
   public function ajax_ext_user_submit_form (Request $req)
    {
        // dd($req->all())

        if(isset($req->qa_host)){
            DB::table('assets')->insert(
                ['name' => $req->qa_host, 'hosting_type' => $req->name_host , 'country' => $req->name_host_c , 'client_id' => Auth::user()->client_id]                                    
            );
        }

        if ($req->hasFile('img'))
        {           
            $user_form_id = $req->input('user-form-id');
            $form_link_id = $req->input('form-link-id');
            $form_id      = $req->input('form-id');
            $subform_id   = $req->input('subform-id');
            $user_email   = $req->input('email');
            $user_id      = $req->input('user-id');
            $question_id  = $req->input('question-id');
            $question_key = $req->input('question-key');
            
            $img_dir_path = "SAR_img_ids/$user_id/";

            $destinationpath=public_path($img_dir_path);
            //File::delete($destinationpath);
            $file=$req->file('img');
            $filename = $file->getClientOriginalName();
            //$ext=$file->getClientOriginalExtension();
            //echo "file ".$filename."<br>";exit;
            $img_name =uniqid().$filename;
            //$destinationpath=public_path('img');
            $file->move($destinationpath,$img_name); 
            
            $file_path = $img_dir_path.$img_name;
            
            DB::table('external_users_filled_response')
            ->updateOrInsert(
            ['external_user_form_id' => $user_id,
            'form_id'   => $form_id, 
            'question_id' => $question_id, 
            'question_key' => $question_key,
            'user_email'  => $user_email], 
            ['question_response' => $file_path, 'custom_case' => 1, 'created' => date('Y-m-d H:i:s')]);         
            
            
            return;
        } 
        
        $form_id = $req->input('form-id');
        $user_form_id = $req->input('user-form-id');
        $form_link_id = $req->input('form-link-id');
        $user_email   = $req->input('email');
        $user_id      = $req->input('user-id');
        $curr_sec     = $req->input('curr-form-sec');   // user current form section
        $is_response_obj  = $req->input('is_response_obj');
        
        DB::table('external_users_forms')
          ->where([
                    'id'                         => $user_id,
                  ])
          ->update(['curr_sec'                   => $curr_sec]);        

        foreach ($req->all() as $post_key => $user_responses)
        {
            if (strpos($post_key, 'q-') !== false)
            {
                $custom_case = 0;
                $asset_questions = '';
                
                // print_r($user_responses);
                // exit;
                
                if ($is_response_obj == '1' && gettype($user_responses) == 'array')
                {
                    $custom_case = 1;
                    if ($req->input('mul_val_obj') == '1')
                    {
                        //$user_responses = json_encode($user_responses['response']);
                        if (isset($user_responses['response']['qa']))
                        {
                            dd($user_responses['response']);
                             $asset_questions = $user_responses['response']['qa'];

                        }
                        $user_responses = json_encode($user_responses['response']);

                                              
                        
                    }
                    elseif ($req->input('mul_val_obj') == '2')
                    {
                        $user_responses = array_column($user_responses, 'response');
                        
                        foreach ($user_responses as $key => $value)
                        {
                            $user_responses[$key] = $value[$key];
                        }
                        
                        $qa_response['qa'] = $user_responses;
                        
                        $user_responses = json_encode($qa_response);
                    }
                    else
                    {
                        $resp_array[$user_responses['type']] = $user_responses['response'];
                        $user_responses = json_encode($resp_array);
                    }
  
                    // print_r($asset_questions);
                    // exit;
                }                
                elseif (gettype($user_responses) == 'array')
                {
                    $user_responses = implode(', ', $user_responses);
                }

                $question_field_name = explode('_', $post_key);

                $question_key        = $question_field_name[0];
                $question_id         = $question_field_name[1];
         
                /*
                DB::statement('insert into filled_response
                (user_form_id, question_id, question_key, user_id, question_response)
                values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_form_id, $question_id, $question_key, $user_id, $user_responses]);
                */
                
                if ($custom_case == 0)
                {               
                    DB::statement('insert into external_users_filled_response
                    (external_user_form_id, form_id, question_id, question_key, custom_case, user_email, question_response)
                    values (?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_id, $form_id, $question_id, $question_key, $custom_case, $user_email, $user_responses]);
                }
                else
                {
                    // echo "update or insert ".$custom_case;
                    // echo $user_responses;
                    // exit;
                    DB::table('external_users_filled_response')
                    ->updateOrInsert(
                    ['external_user_form_id' => $user_id,
                    'form_id'   => $form_id, 
                    'question_id' => $question_id, 
                    'question_key' => $question_key,
                    'user_email'  => $user_email], 
                    ['question_response' => $user_responses, 'custom_case' => $custom_case, 'created' => date('Y-m-d H:i:s')]);
                    
                    if ($req->input('case_name') && $req->input('case_name') == 'assets')
                    {
                        gettype($asset_questions);
                        $asset_questions = explode("\n", trim($asset_questions));
                        
                        if (!empty($asset_questions))
                        {
                            dd($asset_questions);
                            // echo 'st';
                            // print_r($asset_questions);
                            $response = DB::table('external_users_forms')->where(['id'=> $user_id])->first();    
                                                    foreach ($asset_questions as $asset_question)
                            {

                                if (!empty($asset_question) && !DB::table('assets')->where('name', $asset_question)->exists())
                                {
                                    // DB::table('assets')->insert(
                                    //     ['name' => $asset_question, 'client_id' => $response->client_id]                                    
                                    // );
                                }
                            }

                        }
                        
                    }
                }                
                
                /*
                DB::table('external_users_filled_response')
                ->updateOrInsert(
                    ['form_id' => $user_form_id, 'question_id' => $question_id, 'user_email' => $user_email, 'external_user_form_id' => $user_id],
                    ['question_id' => $question_id, 'question_key' => $question_key, 'user_email' => $user_email, 'question_response' => $user_responses]
                );  
            */              

            }

            if (strpos($post_key, 'c-') !== false)
            {
                $comment_field_name = explode(':', $post_key);

                $comment_part        = $comment_field_name[0];
                $question_id         = $comment_field_name[1];
                
                
                $comment_part = explode('-', $comment_part);
                
                
                $c  = $comment_part[0];
                $key = $comment_part[1];
                
                
                $question_key = 'q-'.$question_id;
                    
                $question_id = $key;

                
                /* echo "<pre>";
                print_r($question_id);
                echo "</pre>";
                exit;
 */

                if (DB::table('external_users_filled_response')
                  ->where([
                            'external_user_form_id'      => $user_form_id,
                            'form_id'                    => $form_id,
                            'user_email'                 => $user_email,
                            'question_id'                => $question_id
                          ])
                  ->exists())
                {
                    
                    //echo "if partr ";exit;
                    
                    DB::table('external_users_filled_response')
                  ->where([
                            'external_user_form_id'      => $user_form_id,
                            'form_id'                    => $form_id,
                            'user_email'                 => $user_email,
                            'question_id'                => $question_id
                          ])
                  ->update(['additional_comment' => $user_responses]);
                }
                else
                {
                    $insert_data = [
                        'external_user_form_id' => $user_form_id,
                        'form_id'               => $form_id,
                        'question_id'           => $question_id,
                        'question_key'          => $question_key,
                        'user_email'            => $user_email,
                        'additional_comment'    => $user_responses,
                        'question_response'     => ''];


                    // external_user_form_id, form_id, question_id, question_key, user_email, question_response
                    //[$user_id, $user_form_id, $question_id, $question_key, $user_email, $user_responses]

                    DB::table('external_users_filled_response')->insert($insert_data);


                }

            }
            
            if (strpos($post_key, 'd-') !== false)
            {
                $date_field = explode('-', $post_key);

                $question_id         = $date_field[1];              
                $question_key        = 'q-'.$question_id;
                
                if (DB::table('external_users_filled_response')
                      ->where([
                            'form_id' => $form_id,
                            'user_email' => $user_email,
                            'question_id'  => $question_id
                          ])
                      ->exists())
                {               
                    DB::table('external_users_filled_response')
                      ->where([
                                'form_id'               => $form_id,
                                'user_email'            => $user_email,
                                'question_id'           => $question_id
                              ])
                      ->update(['additional_info'       => $user_responses, 
                                'question_response'     => 'Date Picker Option']);
                }
                else
                {
                    $insert_data = [
                        'external_user_form_id' => $user_id,
                        'form_id'               => $form_id,
                        'question_id'           => $question_id,
                        'question_key'          => $question_key,
                        'user_email'            => $user_email,
                        'additional_info'       => $user_responses,
                        'question_response'     => 'Date Picker Option',
                        'created'               => date('Y-m-d H:i:s')];

                    DB::table('external_users_filled_response')->insert($insert_data);                  
                }       
            }            
        }       
    }

    public function ajax_lock_user_form (Request $request)
    {
            // dd($request->all());
            $client_id = null;
            if($request->client_id){
                $client_id =  $request->client_id;
            }
            else{

                $client_id = auth()->user()->client_id;
            }

            $form_link_id  = $request->input('link_id');
            $user_type     = $request->input('user_type');

            $table_name    = '';
            $link_id_field = '';

            switch ($user_type)
            {
                case 'ex':
                    $table_name    = 'external_users_forms';
                    $link_id_field = 'form_link';
                    break;
                case 'in':
                    $table_name    = 'user_forms';
                    $link_id_field = 'form_link_id';
                    break;          
            }

            $update = DB::table($table_name)
                        ->where($link_id_field, $form_link_id)
                        ->update(['is_locked' => 1]);
            
            $user_form_id = DB::table($table_name)->where($link_id_field, $form_link_id)->pluck('id')->first();

            // create sar request
            $form_type = DB::table($table_name)
                            ->join('sub_forms', $table_name.'.sub_form_id',  '=', 'sub_forms.id')
                            ->join('forms',     'sub_forms.parent_form_id', '=', 'forms.id');

            if ($user_type == 'in')
            {
                $form_type = $form_type->join('users', 'user_forms.user_id', '=', 'users.id');
            }
            
            $form_type = $form_type->where($link_id_field, $form_link_id)->first();

            if ($form_type->type == 'sar') 
            {
                // dd('walla');
                $expiration_info = DB::table('sar_client_expiration_settings')->where('client_id',  $client_id)->first();
                // dd($client_id);
                
                if (empty($expiration_info))
                {
                    $expiration_info = DB::table('sar_admin_expiration_settings')->first();
                }
                // dd($expiration_info);
                

                $due_date = date('Y-m-d', strtotime('+'.$expiration_info->duration.' '.$expiration_info->period, strtotime(date('Y-m-d H:i:s'))));                    

                DB::table('sar_requests')
                    ->updateOrInsert(
                        [
                            'user_type'       => $user_type, 
                            'user_form_id'    => $user_form_id,
                            'client_id'       =>   $client_id
                        ],
                        [
                            'submission_date' => date('Y-m-d H:i:s'),
                            'due_date'        => $due_date,                            
                            'status'          => 0
                        ]
                    );                    
            }
            
            return response()->json(['status' => 'success', 'msg' => 'Form successfully submitted']);
    }
    
    public function show_success_msg ()
    {
        $template = 'users.ex_user_app';
        $user_type = 'ex';
        $user_role = '';
        $user_type = '';
        
        if (Auth::check()) 
        {
            $template = 'admin.client.client_app';
            $user_type = 'in';
            $user_role = Auth::user()->role;
            $is_super  = Auth::user()->user_type;
            
        } 
        $is_super = ' ';
        return view('forms.form_submit_msg', compact('template', 'user_type', 'user_role', 'is_super'));
    }
    
    
    // form submission for external user
    public function ext_user_submit_form (Request $req)
    {       
        $user_form_id = $req->input('form-id');
        $form_link_id = $req->input('form-link-id');
        $user_email   = $req->input('email');
        $user_id      = $req->input('user-id');



        foreach ($req->all() as $post_key => $user_responses)
        {
            if (strpos($post_key, 'q-') !== false)
            {
                if (gettype($user_responses) == 'array')
                {
                    $user_responses = implode(', ', $user_responses);
                }

                $question_field_name = explode('_', $post_key);

                $question_key        = $question_field_name[0];
                $question_id         = $question_field_name[1];

         
                /*
                DB::statement('insert into filled_response
                (user_form_id, question_id, question_key, user_id, question_response)
                values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_form_id, $question_id, $question_key, $user_id, $user_responses]);
                */
                DB::statement('insert into external_users_filled_response
                (external_user_form_id, form_id, question_id, question_key, user_email, question_response)
                values (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE question_response = '.'"'.$user_responses.'"', [$user_id, $user_form_id, $question_id, $question_key, $user_email, $user_responses]);
                        
                
                
                
                /*
                DB::table('external_users_filled_response')
                ->updateOrInsert(
                    ['form_id' => $user_form_id, 'question_id' => $question_id, 'user_email' => $user_email, 'external_user_form_id' => $user_id],
                    ['question_id' => $question_id, 'question_key' => $question_key, 'user_email' => $user_email, 'question_response' => $user_responses]
                );  
            */              

            }

            if (strpos($post_key, 'c-') !== false)
            {
                $comment_field_name = explode('-', $post_key);

                $comment_key        = $comment_field_name[0];
                $question_id        = $comment_field_name[1];
                
                /*
                DB::table('external_users_filled_response')
                    ->updateOrInsert(['form_id' => $user_form_id, 'question_id' => $question_id, 'question_key' => $question_key, 'user_email' => $user_email, 'external_user_form_id' => $user_id],
                                     ['additional_comment' => $user_responses]
                    );
                */
                
                DB::table('external_users_filled_response')
                  ->where([
                            'external_user_form_id'      => $user_id,
                            'form_id' => $user_form_id,
                            'user_email' => $user_email,
                            'question_id'  => $question_id
                          ])
                  ->update(['additional_comment' => $user_responses]);              
                
  
            }

        }


        return view('users.show_msg', ['msg' => 'Your response has been submitted. Thanks ']);
    }

    // Admin form list
    public function all_forms_list ($type = '')
    {
        
      $this->middleware(['auth','2fa']);

      if (Auth::user()->role != 1)
      {
          return abort(404);
      }
        
       if (!empty($type) && $type == 'sar')
       {
                 $forms_info = DB::table('forms')
                      ->leftjoin('users',      'users.id',     '=', 'forms.user_id')
                      ->where('type', '=', 'sar')
                      ->select('title', 'users.id as user_id', 'forms.id as form_id', 'forms.date_created')
                      ->get();
       }
       else 
       {
            $forms_info = DB::table('forms')
                      ->leftjoin('users',      'users.id',     '=', 'forms.user_id')
                      //->where('type', '!=', 'sar')
                      //->where('users.id', '=', Auth::id())
                      ->select('title', 'users.id as user_id', 'forms.id as form_id', 'forms.date_created')
                      ->get();         
       }




          

      // echo session('user_id');

       return view('forms.forms_list', ['user_type' => 'admin', 'forms_list' => $forms_info, 'type' => $type]);         
    }
    
    
    // list of form assignees / clients (for admin)
    public function form_assignees ($form_id = 1)
    {   
        if (Auth::user()->role != 1)
        {
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
        
        $forms_info = DB::table('forms')
                      ->join('client_forms',      'forms.id',     '=', 'client_forms.form_id')
                      ->where('forms.id', '=', $form_id)                      
                      ->select('*')
                      ->get();

        $assigned_client_ids = [];
        
        foreach ($forms_info as $form)
        {
            $assigned_client_ids[] = $form->client_id;
        }
        return view('forms.forms_forms_assignee_list', ['user_type' => 'admin',
                                                        'assigned_client_ids' => $assigned_client_ids,
                                                        'form_id' => $form_id,
                                                        'client_list' => $client_list]);        
    }
    

    // list of forms for clients 
    public function forms_list ()
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
            if(!in_array('Manage Forms', $assigned_permissions)){
                return redirect('dashboard');
            // }
        }

         // dd('reight');

        // dd('walla');
      $this->middleware(['auth','2fa']);
    if(Auth::user()->role != 3)  {
      if (Auth::user()->role != 2 && Auth::user()->user_type != 1)
      {
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
                      ->join('client_forms',      'forms.id',     '=', 'client_forms.form_id')
                      ->leftjoin('sub_forms',     'forms.id',     '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = '.$client_id))
                      ->where('client_forms.client_id', '=', $client_id)
                      ->where('type', '!=' , 'sar')
                      ->selectRaw('forms.title, count(sub_forms.id) as subforms_count, user_id, forms.id as form_id, forms.date_created')
                      ->groupBy('forms.id')
                      ->orderBy('date_created')
                      ->get();
            // dd($forms_info);

                      
        $type = 'assessment';


       return view('forms.forms_list', ['user_type' => 'client', 'forms_list' => $forms_info, 'type' => $type]);               
    }
    
    public function completed_forms_list ()
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
            if(!in_array('Completed Forms', $assigned_permissions)){
                return redirect('dashboard');
            // }
        }


        $client_id = Auth::user()->client_id;
        $mytime = Carbon::now();
        $result=null;

        if ( (Auth::user()->role == 2 || Auth::user()->role == 3) || (Auth::user()->role == 3 && Auth::user()->user_type == 1))
        {
            
            /*
            SELECT sub_forms.id, external_users_forms.user_email, sub_forms.title as subform_title, forms.title as form_title, 'external' as user_type,
            SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as completed_forms,
            COUNT(external_users_forms.user_email) as total_external_users_count FROM `external_users_forms`
            JOIN sub_forms ON sub_forms.id = external_users_forms.sub_form_id
            JOIN forms     ON forms.id     = sub_forms.parent_form_id
            WHERE is_locked = 1
            AND   external_users_forms.client_id = 120
            GROUP BY sub_forms.id        
            */
        
            $ext_forms = DB::table('external_users_forms as exf')
                                    ->join('sub_forms', 'exf.sub_form_id', '=', 'sub_forms.id')
                                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                                    ->where('exf.client_id', $client_id)
                                    ->where('is_locked', 1)
                                    ->select('*', DB::raw('exf.user_email as email,
                                                           SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as ex_completed_forms,
                                                           COUNT(exf.user_email) as total_external_users_count,
                                                           forms.title as form_title, 
                                                           sub_forms.title as subform_title, 
                                                           "External" as user_type'))
                                    ->groupBy('sub_forms.id')
                                    ->get();

                                
            /*
            SELECT sub_forms.id, users.email, sub_forms.title as subform_title, forms.title as form_title, 'internal' as user_type,
            SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as completed_forms,
            COUNT(users.email) as total_internal_users_count 
            FROM `user_forms`
            JOIN users     ON users.id        = user_forms.user_id
            JOIN sub_forms ON sub_forms.id    = user_forms.sub_form_id
            JOIN forms     ON forms.id        = sub_forms.parent_form_id
            WHERE is_locked = 1
            AND   user_forms.client_id = 120
            GROUP BY sub_forms.id        
            */
        
            $int_forms = DB::table('user_forms as uf')
                                    ->join('users', 'users.id', '=', 'uf.user_id')
                                    ->join('sub_forms', 'uf.sub_form_id', '=', 'sub_forms.id')
                                    ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
                                    ->where('uf.client_id', $client_id)
                                    ->where('is_locked', 1)
                                    ->select('*', DB::raw('users.email,
                                                           SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as in_completed_forms,
                                                           COUNT(users.email) as total_internal_users_count,
                                                           forms.title as form_title, 
                                                           sub_forms.title as subform_title, 
                                                           form_link_id as form_link, 
                                                           "Internal" as user_type'))
                                    ->groupBy('sub_forms.id')
                                    ->get();
                                                       
            $completed_forms = $int_forms->merge($ext_forms);
            // dd($completed_forms);
            // dd($completed_forms);

            if(count($completed_forms) > 0){
              foreach ($completed_forms as $data) {
                if($mytime <= $data->expiry_time)
                    {
                        $result[] = $data;
                         }
            }
            $completed_forms = $result;
        }
     // tohandle null values
        if($completed_forms == null){$completed_forms = [];}  


       if(Auth::user()->role == 1){
         $user_type = 'admin';
       } 
           else {
            $user_type = 'client';
           }  

            
            return view('forms.completed_forms_list', compact('completed_forms' , 'user_type'));            
        }
        

        
    }
    
    // list of subforms 
    public function subforms_list ($form_id = 1)
    {    
        $this->middleware(['auth','2fa']);

        //$client_id = Auth::id();
        $client_id = Auth::user()->client_id;

        $form_info = DB::table('forms')->find($form_id);
        
        
        if (empty($form_info))
        {
            return redirect('Forms/FormsList');
        }
        
        //$client_id = 1; // logged in as user
        $client_user_list = DB::table('users')->where('client_id', '=', $client_id)->pluck('name');
        
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

        // dd($subforms_list);

        return view('subforms.subform', [
                                  'user_type'    => ((Auth::user()->role == 1)?('admin'):('client')),
                                  'title'        => 'Client SubForms',
                                  'heading'      => 'Client SubForms',
                                  'form_info'    => $form_info,
                                  'sub_forms'    => $subforms_list,
                                  'client_users' => $client_user_list
                               ]);
    }
    
    public function subform_assignees ($subform_id)
    {
        //$client_id = Auth::id();
        $client_id = Auth::user()->client_id;

        if (Auth::user()->role == 3 && Auth::user()->user_type == 1)
        {
            $client_id = Auth::user()->client_id;
        }
            // dd($client_id);
        // actual
        $company_users  = DB::table('users')
                            ->leftJoin('user_forms', 'users.id', '=', 'user_forms.user_id')
                            ->where('users.client_id', '=', $client_id)
                            ->select('users.*', DB::raw('count(user_forms.user_id) as forms_count'))
                            ->groupBy('users.id')
                            ->get()
                            ->toArray();
        // actual end


                             // dd($company_users);                       

                            
        $subform_info   =   DB::table('sub_forms')
                            ->where('id', '=', $subform_id)
                            ->first();     
        /*
        SELECT users.id FROM users 
        JOIN user_forms ON users.id = user_forms.user_id
        AND users.client_id = 23
        WHERE user_forms.id = 1     
        */                          
        
        $assigned_users = DB::table('users')
                            ->join('user_forms', 'users.id',    '=', 'user_forms.user_id')
                            ->where('users.client_id',          '=', $client_id)
                            ->where('user_forms.sub_form_id',   '=', $subform_id)
                            ->pluck('users.id')
                            ->toArray();

                        
                            
        return view ('subforms.subforms_assignee_list', [
            'subform_info'    => $subform_info,
            'company_users'   => $company_users,
            'assigned_users'  => $assigned_users 
        ]);                       
    }   

    public function create_subform (Request $req)
    {
      $title       = $req->input('subform_title');
      $form_id     = $req->input('form_id');
      $expiry_time = date('Y-m-d H:i:s', strtotime("+10 days"));
      
      
          $client_id = Auth::user()->client_id;

        
        
        // check if form already exists by this name
        $existing_subform = DB::table('sub_forms')
            ->where('parent_form_id',   '=', $form_id)
            ->where('client_id',        '=', $client_id)
            ->where('title',            '=', $title)
            ->first();
            
        if (Auth::user()->role == 2 && !empty($existing_subform))
        {
            return response()->json(['status' => 'error', 'msg' => 'Sub-form by this name already exists']);
        }
      
      // echo $expiry_time."<br>";
      // exit;

        $subform_id = DB::table('sub_forms')->insertGetId([
                                      'title'          => $title,
                                      'parent_form_id' => $form_id,
                                      'client_id'      => $client_id,
                                      'expiry_time'    => $expiry_time
                                 ]);

        //$this->assign_subform_to_client_users($client_id, $form_id, $subform_id);
      
       return response()->json(['status' => 'success', 'msg' => 'Sub-form created']);
    
    }
    
    

    
    public function ex_users_show_form ($client_id, $user_id, $client_email, $subform_id, $user_email, $date_time)
    {
//echo $user_email;exit;
        
        $accoc_info = ['client_id'    => $client_id,
                       'user_id'      => $user_id,
                       'client_email' => $client_email,
                       'subform_id'   => $subform_id,
                       'user_email'   => $user_email,
                       'date_time'    => $date_time];
        // dd($accoc_info);                       

        //echo '<pre>';print_r($accoc_info);exit;
        $form_link_id = implode('/', $accoc_info);
        // dd($form_link_id);



        $accoc_info = ['client_id'    => $client_id,
                       'user_id'      => $user_id,
                       'client_email' => base64_decode($client_email),
                       'subform_id'   => $subform_id,
                       'user_email'   => base64_decode($user_email),
                       'date_time'    => base64_decode($date_time)];

        $client_id = DB::table('external_users_forms')
                       ->where('external_users_forms.form_link','=',$form_link_id)
                       ->pluck('client_id')
                       ->first();


                       //->toSql();
       // echo $form_link_id;
        //echo '<br>';

        if (empty($client_id))
            {
                // dd('this is the isue');
            //echo '==============';exit;
            return abort('404');
           }
        //echo '................';exit;


     

        $form_info = DB::table('external_users_forms')
                       ->join('sub_forms',      'external_users_forms.sub_form_id',     '=', 'sub_forms.id')
                       ->join('form_questions', 'sub_forms.parent_form_id',   '=', 'form_questions.form_id')
                       ->join('questions',      'form_questions.question_id', '=', 'questions.id')
                       ->leftJoin('admin_form_sections  as afs', 'questions.question_section_id', '=', 'afs.id')
                       ->leftJoin('client_form_sections as cfs', 'cfs.admin_form_sec_id', '=', DB::raw('afs.id AND cfs.client_id = '.$client_id))
                       ->where('external_users_forms.form_link', '=', $form_link_id)
                       ->orderBy('sort_order')
                       ->select('*',
                                'form_questions.form_id as form_id',
                                'external_users_forms.id as uf_id',
                                'afs.id as afs_sec_id',
                                'afs.sec_num as afs_sec_num',
                                'afs.section_title as admin_sec_title',
                                'cfs.id as cfs_sec_id',
                                'cfs.section_title as client_sec_title',
                                'questions.id as q_id',
                                'external_users_forms.user_email as u_email',
                                'external_users_forms.expiry_time as form_expiry_time')
                       ->get();

                       
        if (empty($form_info))
        {

          
            return abort('404');
        }

        //echo 'not empty form_info';exit;
        // dd($form_info);
        $expiry_note = '';               
        if (isset($form_info[0]) && strtotime(date('Y-m-d')) > strtotime($form_info[0]->form_expiry_time))
        {
            if (Auth::check())
            {
                // $client_id = $form_info[0]->client_id;
                // dd($client_id);
                
                if ((Auth::user()->role == 2 || Auth::user()->user_type == 1) && Auth::user()->client_id == $client_id)
                {
                    if ($form_info[0]->is_locked != '1')
                    {
                        $expiry_note = 'The user failed to submit form before expiry time.';               
                    }                
                }
                else
                {
                    // return view('user_form_expired');
                        // $expiry_note = 'Failed to submit form before expiry time.';               

                }
            }
            else
            {
                // return view('user_form_expired');7
                        // $expiry_note = 'Failed to submit form before expiry time.';               

            }
        }
        else if (isset($form_info[0]) && !$form_info[0]->is_accessible)
        {
            return view('user_form_not_accessible');
        }        

      $filled_info = DB::table('external_users_forms')
                     ->join('external_users_filled_response',      'external_users_forms.id',     '=', 'external_users_filled_response.external_user_form_id')
                     ->join('questions',      'questions.id',     '=', 'external_users_filled_response.question_id')
                     ->where('external_users_forms.form_link', '=', $form_link_id)
                     ->get();
        
      $custom_responses = [];

      $question_key_index = [];

    // dd($filled_info);
      foreach ($filled_info as $key => $user_response)
      {
          if ($user_response->type == 'mc')
          {
                $user_response->question_response = explode(', ', $user_response->question_response);
          }
          
          if ($user_response->custom_case == '1')
          {
              $custom_responses[$user_response->question_key] = $user_response->question_response;
          }          

          $question_key_index[$user_response->question_key] =
          [
                'question_response' => $user_response->question_response,
                'question_id'       => $user_response->question_id,
                'question_comment'  => $user_response->additional_comment,
                'question_type'     => $user_response->type,
                'additional_resp'   => $user_response->additional_info
          ];
      }

        //if ($id != 2)
        {

            $custom_fields = '';
            $num_of_questions = 0;


            foreach ($form_info as $key => $questions)
            {

                //echo '<pre>';print_r($questions);exit;
                //echo $questions->q_id."<br>";
                // dd($form_info);
                
                if (trim($questions->type) == 'cc' )
                {    
                    // dd($questions);
                    //echo '<pre>';print_r($questions);exit;
                    //echo $questions->type."--";
                    
                    //echo 'inside';exit;
                    $fields_info   = json_decode($questions->question_info);
                    $field_html    = ''; 
                    $is_asset_case = false;
                    
                    $fill_custom_response = [];
                    
                    if (isset($custom_responses[$questions->form_key]))
                    {
                        $fill_custom_response = json_decode($custom_responses[$questions->form_key]);
                        //     echo "<pre>";
                        //   print_r($fill_custom_response);
                        //   echo "</pre>";
                        //   exit;
                    }
                      // echo '<pre>';print_r($fields_info);exit;
                    // dd('walala');
                    
                    foreach ($fields_info as $fkey => $field)
                    {
                        //echo '<pre>';print_r($field);
                        //echo $fkey;exit;
                        if ($fkey == 'mc')
                        {
                            $value = '';
                            
                            if (isset($field->data) && gettype($field->data) == 'string')
                            {
                                $case_name = '';
                                if (strtolower($field->data) == 'not sure' || strtolower($field->data) == 'not applicable')
                                {
                                    $case_name = 'case-name="Not Sure"';
                                    $mc_selected = 'es-selected';
                                }
                                
                                $value = $field->data;
                                $mc_selected = '';
                                
                                if (isset($fill_custom_response->mc))
                                {
                                      $filled_resp = $fill_custom_response->mc;
                                      $mc_selected = 'es-selected';
                                }                               
                                
                                $field_html .= '<section class="options">';
                                $field_html .= '<ul id="easySelectable" class="easySelectable">';
                                $field_html .= '<li data-parentSection='.$questions->question_num.' class="es-selectable '.$mc_selected.'" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" '.$case_name.' value="'.$value.'" type="mc">'.$value.'</li>';
                                $field_html .= '</ul></section>';                               
                                
                                
                            }
                            
                        }
                        
                        // added case for sc 
                        //echo 'asdf';exit;
                        if ($fkey == 'sc')
                        {
                            $value = '';
                            $sc_fields = [];
                            
                            if (gettype($field) == 'array')
                            {
                                $sc_fields = $field;
                            }
                            else
                            {
                                $sc_fields[0] = $field;
                            }

                            $field_html .= '<section class="options">';
                            $field_html .= '<ul id="easySelectable" class="easySelectable">';  


                            foreach ($sc_fields as $sc_field)
                            {
                                if (isset($sc_field->data) && gettype($sc_field->data) == 'string')
                                {
                                    $case_name = '';
                                    if (strtolower($sc_field->data) == 'not sure' || strtolower($sc_field->data) == 'not applicable')
                                    {
                                        $case_name = 'case-name="Not Sure"';
                                        //$mc_selected = 'es-selected';
                                    }
                                    
                                    
                                    $value = $sc_field->data;
                                    $mc_selected = '';
                                    
                                    if (isset($fill_custom_response->sc))
                                    {
                                          $filled_resp = $fill_custom_response->sc;
                                          if (strtolower($filled_resp) == strtolower($value))
                                          {
                                                $mc_selected = 'es-selected';
                                          }
                                    }  
                                    
                                    $field_html .= '<li data-parentSection='.$questions->question_num.' class="es-selectable not-unselectable '.$mc_selected.'" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" '.$case_name.' value="'.$value.'" type="sc">'.$value.'</li>';
                                    
                                }                               
                            }
                            
                            $field_html .= '</ul></section>';                           
                        } 
                        // added case for sc 
     

                        
                        if ($fkey == 'dd')
                        {
                            //echo 'aaaaaaaaa';exit;
                             $field_comment = '';
                            if (isset($field->comment))
                            {
                                $field_comment = $field->comment;
                            }
                            if (isset($field->data))
                            {
                                if ($field->data == 'assets')
                                {
                                    $is_asset_case = true;
                                    $assets_query = DB::table('assets')->where('client_id', $client_id)->get();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6>'.$field_comment.'</h6>';
                                    // dd($questions);
                                    // //print_r($assets_query);exit;
                                    // if($questions->question == 'What is the name of the asset you are assessing?' )
                                    // {
                                    $field_html .= '<select data-parentSection='.$questions->question_num.' class="selectpicker form form-control" multiple data-live-search="true" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" type="dd" case-name="assets">'; 
                                                     // }
                                                     // else{
                                                     //     $field_html .= '<select data-parentSection='.$questions->question_num.' class="selectpicker form form-control" multiple data-live-search="true" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" type="dd" case-name="assets">'; 

                                                     // }

                                    
                                    $selected = '';
                                    
                                    //$multi_select = $fill_custom_response->dd;
                                    
                                    //$multi_data=$multi_select;
                                    // dd($multi_data[0]);
                                    
                                    if (!isset($fill_custom_response->dd) || (isset($fill_custom_response->dd) && $fill_custom_response->dd == 'Select Option'))
                                    {
                                        $selected = 'selected';
                                    }
                                    
                                    $field_html .= '<option value="Select Option" '.$selected.'>Select Option</option>';
                                    foreach ($assets_query as $akey => $aquery)
                                    {
                                          $selected = '';

                                          
                                          // if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $aquery->name)
                                          // {
                                          //     $selected = 'selected';
                                          // }
                                          $field_html .= '<option value="'.$aquery->name.'" '.$selected.'>'.$aquery->name.'</option>';

                                    }
                                    $field_html .= '</select></div>';
                                    
                                }
                                if ($field->data == 'country_list')
                                {
                                    $countries         = new Country();
                                    $country_list = $countries->list();
                                    $field_html .= '<div>';
                                    $field_html .= '<h6>'.$field_comment.'</h6>';
                                    $field_html .= '<select data-parentSection='.$questions->question_num.' class="form form-control" name="'.$questions->form_key.'_'.$questions->q_id.'" q-id="'.$questions->q_id.'" custom="1" type="dd" case-name="asset-country">';
                                    foreach ($country_list as $country_key => $country_name)
                                    {
                                          $selected = '';
                                          if (isset($fill_custom_response->dd) && $fill_custom_response->dd == $country_name)
                                          {
                                              $selected = 'selected';
                                          }                                     
                                          $field_html .= '<option value="'.$country_name.'" '.$selected.'>'.$country_name.'</option>';
                                    }
                                    $field_html .= '</select></div>';                                   
                                }
                                            
                                $num_of_questions++;
                            }
                            //echo $field_html;exit;
                        }
                        
                        if ($fkey == 'qa')
                        {
                          //  echo "<pre>";
                          //  print_r($field);
                          //  echo "</pre>";
                          //  exit;
                            
                            $qa_fields = [];
                            
                            $field_name_array = '';
                            $multi_qa_case_str = '';
                            $json_format = false;
                            
                            if (gettype($field) == 'array')
                            {
                                $qa_fields = $field;
                                $field_name_array = '[]';
                                $multi_qa_case_str = 'case-name="multi-qa"';
                                $json_format = true;

                            }
                            else
                            {
                                $qa_fields[0] = $field;
                            }
                            
                            $asset_attr_str = '';
                            if ($is_asset_case === true)
                            {
                                $asset_attr_str = 'case-name="assets"';
                            }
                            
                            $filled_resp = '';
                            if (isset($fill_custom_response->qa))
                            {
                                  $filled_resp = $fill_custom_response->qa;
                            }   
                            
                            $qa_fill_index = 0;
      
                            foreach ($qa_fields as $qa_key => $qa_field)
                            {
                                

                                $field_comment = '';
                                if (isset($qa_field->comment))
                                {
                                    $field_comment = $qa_field->comment;
                                }

                                if (gettype($filled_resp) == 'array')
                                {
                                     $tbox_val = isset($filled_resp[$qa_key])?($filled_resp[$qa_key]):('');
                                }
                                else
                                {
                                    $tbox_val = $filled_resp;
                                }
                                
                                
                                
                                $field_html .= '<div>';
                                $field_html .= '<h6 class="question-comment">'.$field_comment.'</h6>';                              
                                $field_html .=  '<form>
                                        <label></label>
                                        <textarea data-parentSection='.$questions->question_num.'  name="'.$questions->form_key.'_'.$questions->q_id.$field_name_array.'" q-id="'.$questions->q_id.'" '.$multi_qa_case_str.' '.$asset_attr_str.' rows="4" cols="50" custom="1" type="qa" id="qa_nameabc" onfocusout="hideFields()">'.$tbox_val.'</textarea>


                                    </form>
                                </div>';   
                                   // dd($questions);
                                if(($questions->question_num == 1.1 || $questions->question_num == 4.1 || $questions->question_num == 6.1)&&($questions->question == 'What is the name of the asset you are assessing?' || $questions->question == 'What assets are used to collect store and process the data' || $questions->question == 'What assets are used to process the data for this activity?'  )){
                                    $field_html .= '<div class="full_assetz">';
                                    $field_html .= '<form>
                                    <label>Hosting Type</label>
                                    <input type="text" id="tesst" name="testtt" class="form-data ssd" onfocusout="update_form_data_request()">
                                    <label>Country</label>
                                    <input type="text" name="country" id="tesst_c" class="form-data ssd" onfocusout="update_form_data_request()">
                                    </form>
                                    </div>';

                                    // $field_html .= '<div class="full_asset">
                                    //     <label>Hosting Type</label>
                                    //     <input type="text" name="hosting_type" class="form-data">
                                    //     <label>Country</label>
                                    //     <input type="text" name="country" class="form-data">
                                    // </div>';

                                }
                                

                                
                            }

                            
                            
                            $num_of_questions++;
                        }

                    
                    }
                                  
                 $field_html .= '<div id="perc-bar-'.$questions->q_id.'" class="barfiller hidden">
                                    <div class="tipWrap">
                                        <span class="tip"></span>
                                        </div>
                                        <span class="fill" id="fill-bar-'.$questions->q_id.'" data-percentage="0"></span>
                                </div>';                    
                    
                 $form_info[$key]->custom_fields = $field_html;
                 $form_info[$key]->num_questions = $num_of_questions; 
                }
                
            }
//echo $field_html;exit;            
        }     
      
    //  echo "<pre>";
    //  print_r($form_info);
    //  echo "</pre>";
    //  exit; 

      /*
      SELECT *
      FROM   users
      JOIN   user_forms ON user_forms.user_id = users.id
      WHERE  user_forms.form_link_id = 'wPws9qe8zawIRRVQT7pKLsYqH1LIpzZDFPTctC03'
      */
        $user_info = DB::table('users')
                        ->join('user_forms',      'user_forms.user_id',     '=', 'users.id')
                        ->where('user_forms.form_link_id', '=', $form_link_id)
                        ->select('*')->first();
        // dd($user_info);


        /*
        return view('user_form2', ['questions' => $form_info,
                                  'filled'    => $question_key_index,
                                  // 'fq_keys'   => $question_keys,
                                  'accoc_info'=> $accoc_info,
                                  'user_info' => $user_info,
                                  'title'     => (isset($form_info[0]) && !empty($form_info))?($form_info[0]->title):('title'),
                                  'heading'   => (isset($form_info[0]) && !empty($form_info))?($form_info[0]->title):('heading')]);
                                  
                                  
        */
        $hidden_pb = false;
        if (isset($form_info[0]) && !empty($form_info[0]))
        {
            $form_type = DB::table('forms')->where('id', '=', $form_info[0]->form_id)->pluck('type')->first();          
            if ($form_type == 'sar')
            {
                $hidden_pb = true;          
            }
        }
        // dd($client_id."----"."askdhakjdhkahdkashdkjashdkjhaskd asdkhajksdh akhd kahd ");

        return view('forms.ex_user_form_sec_wise', ['questions'  => $form_info,
                                    'hide_pb'  => $hidden_pb,
                                     'client_id' => $client_id,
                                    'filled'     => $question_key_index,
                                    'accoc_info' => $accoc_info,
                                    'user_info'  => $user_info,
                                    'expiry_note'=> $expiry_note]);     
                                  
    }


 public function organization_all_forms_list ($subform_id = '')
    {
        if (empty($subform_id))
        {
            return abort('404');
        }
         // dd($subform_id);
        
        
        $parent_form_id = DB::table('sub_forms')
                      ->where('id', '=', $subform_id)
                      ->pluck('parent_form_id')->first();
                      
        if (!$parent_form_id)
        {
            return abort('404');
        }
                      
                      
        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();          


        $int_form_user_list = DB::table('user_forms')
                                ->join('sub_forms',      'sub_forms.id',     '=', 'user_forms.sub_form_id')
                                ->join('users',      'users.id',     '=', 'user_forms.user_id')
                                ->where('sub_form_id', '=', $subform_id)
                                ->select(DB::raw('*, user_forms.created as uf_created, user_forms.expiry_time as uf_expiry_time, "internal", is_locked'))->get();
                      
        $ext_form_user_list = DB::table('external_users_forms')
                                ->join('sub_forms',      'sub_forms.id',     '=', 'external_users_forms.sub_form_id')
                                ->where('sub_form_id', '=', $subform_id)
                                ->select(DB::raw('*, external_users_forms.created as uf_created, external_users_forms.expiry_time as uf_expiry_time, "external", is_locked'))->get();
   
        if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1')
        {
            $form_user_list = $ext_form_user_list;
        }
        else
        {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
        }
                                
                                
        $user_type = 'client';          
        if (Auth::user()->role == 1) 
        {
            $user_type = 'admin';
        }
        
        return view('subforms.org_subforms_list', compact('form_user_list', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }

    
    public function organization_all_forms_list_all ()
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
            if(!in_array('Generated Forms', $assigned_permissions)){
                return redirect('dashboard');
            }
            // }

         $parent_form_id = DB::table('sub_forms')
                      ->pluck('parent_form_id');
         $subform_id = DB::table('sub_forms')
                      ->pluck('id');

                      
        if (!$parent_form_id)
        {
            return abort('404');
        }
                      
                      
        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();          

        $client_id = Auth::user()->client_id;

        $int_form_user_list = DB::table('user_forms')->where('sub_forms.client_id' , $client_id)
                                ->join('sub_forms',      'sub_forms.id',     '=', 'user_forms.sub_form_id')
                                ->join('users',      'users.id',     '=', 'user_forms.user_id')
                                ->wherein('sub_form_id', $subform_id)
                                ->select(DB::raw('*, user_forms.created as uf_created, user_forms.expiry_time as uf_expiry_time, "internal", is_locked'))->get();
                      
        $ext_form_user_list = DB::table('external_users_forms')->where('sub_forms.client_id' , $client_id)
                                ->join('sub_forms',      'sub_forms.id',     '=', 'external_users_forms.sub_form_id')
                                ->wherein('sub_form_id',  $subform_id)
                                ->select(DB::raw('*, external_users_forms.created as uf_created, external_users_forms.expiry_time as uf_expiry_time, "external", is_locked'))->get();


                                // dd($ext_form_user_list);
   
        if (isset($_GET['ext_user_only']) && $_GET['ext_user_only'] == '1')
        {
            $form_user_list = $ext_form_user_list;
        }
        else
        {
            $form_user_list = $int_form_user_list->merge($ext_form_user_list);
        }
                                
                                
        $user_type = 'client';          
        if (Auth::user()->role == 1) 
        {
            $user_type = 'admin';
        }
        $all = 1;
        
        return view('subforms.org_subforms_list', compact('form_user_list','all', 'subform_id', 'user_type', 'parent_form_id', 'parent_form_info'));
    }





    
    
    
    // subforms email list for registered users
    public function subforms_email_list ($subform_id = 4)
    {
      $this->middleware(['auth','2fa']);

      if (Auth::user()->role != 2 && Auth::user()->user_type == 0)
      {
          return abort(404);
      }
      
       $user_type = 'client';          
        if (Auth::user()->role == 1) 
        {
            $user_type = 'admin';
        }  
        
        $parent_form_id = DB::table('sub_forms')
                      ->where('id', '=', $subform_id)
                      ->pluck('parent_form_id')->first();
                      
                      
        $parent_form_info = DB::table('forms')->where('id', $parent_form_id)->first();          
        

      //$client_user_list = DB::table('users')->where('client_id', '=', session('user_id'))->pluck('name');

      //echo "sub form id : ". $subform_id . "<br>";


      $form_user_list = DB::table('user_forms')
                      ->join('sub_forms',      'sub_forms.id',     '=', 'user_forms.sub_form_id')
                      ->join('users',      'users.id',     '=', 'user_forms.user_id')
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
    public function ext_users_subforms_email_list ($subform_id = 4)
    {
      $this->middleware(['auth','2fa']);

      /*
      if (Auth::user()->role != 2)
      {
          return abort(404);
      }
      */

      //$client_user_list = DB::table('users')->where('client_id', '=', session('user_id'))->pluck('name');

      //echo "sub form id : ". $subform_id . "<br>";
    /*
    SELECT * FROM `external_users_forms`
    WHERE sub_form_id = 8 AND client_id = 23
    */


      $form_user_list = DB::table('external_users_forms')
                      ->where('sub_form_id', '=', $subform_id)
        //            ->where('external_users_forms.client_id', '=', Auth::id())
                      ->select('*')->get();
            
        $user_type = 'client';          
        if (Auth::user()->role == 1) 
        {
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


    public function assign_subform_to_external_users (Request $req)
    {
          $subform_id     = $req->input('subform_id');
      $emails         = $req->input('emails');
      $client_id      = $req->input('client_id');
      $form_id        = $req->input('parent_form_id');

      
      /*
      SELECT * FROM `external_users_forms`
      WHERE user_email IN ('e1@e.com', 'e2@e.com')
      */
      $e_count    = DB::table('external_users_forms')
                      ->whereIn('user_email', $emails)
                      ->where('sub_form_id', '=', $subform_id)
                      ->count();
          
          $subform_info = DB::table('sub_forms')->where('id', '=', $subform_id)->first();
                      
      if ($e_count > 0) 
      {
        return response()->json(['status' => 'fail', 
                                 'msg'    => 'One of the emails is already present for this sub form. Failed to send email.']);               
      }
      
      $insert_data = [];
      foreach ($emails as $email) {
        $insert_data[] = ['sub_form_id' => $subform_id, 
                          'client_id'   => $client_id, 
                          'sub_form_id' => $subform_id, 
                          'user_email'  => $email, 
                          'sub_form_id' => $subform_id];
      }
      
      
      DB::table('external_users_forms')->insert($insert_data);
      
       $ex_users    =   DB::table('external_users_forms')
                    ->  where('sub_form_id', $subform_id)
                    ->  whereIn('user_email', $emails)
                    ->  where('email_sent', 0)
                    ->  select('*')
                    ->  get();
                    
        $links = [];
        
        //$client_id    = Auth::id();
        $client_id = Auth::user()->client_id;

        $client_email = Auth::user()->email;
                
                $client_info = DB::table('users')->where('id', $client_id)->first();

            $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
            
            if (empty($subform_settings))
            {
                $subform_settings = DB::table('subform_admin_expiration_settings')->first();        
            }
            
            $remaining_time_for_form = '+'.$subform_settings->duration.' '.$subform_settings->period;
        
        if (Auth::user()->role == 3 && Auth::user()->user_type == 1)
        {
            /*
            SELECT clients.email 
            FROM   users
            JOIN   users AS clients ON clients.id = users.client_id
            WHERE  users.id = 86
            */
            
            $client_info = DB::table('users')
                              ->join(DB::raw('users as clients'), 'clients.id', '=',  'users.client_id')
                              ->where('users.id', '=', $client_id)
                              ->select('clients.email', 'clients.id')
                              ->get()
                              ->first();
                    
                    

                              
            if (!empty($client_info))
            {
                $client_id    = $client_info->id;
                $client_email = $client_info->email;
            }
            
            
        }
                
                                
                    
        foreach ($ex_users as $user)
        {
            $form_link = $user->client_id.'/'.$user->id.'/'.base64_encode($client_email).'/'.$subform_id.'/'.base64_encode($user->user_email).'/'.base64_encode(date('Y-m-d H:i:s'));

                        $expiry_time = date('Y-m-d H:i:s', strtotime($remaining_time_for_form));
            
            DB::table('external_users_forms')
            ->where('sub_form_id', $subform_id)
            ->where('client_id', $client_id)
            ->where('user_email', $user->user_email)
            ->update(['form_link' => $form_link, 'email_sent' => 1, 'expiry_time' => $expiry_time]);
            $logo = Auth::user()->image_name;

             $data = array('name'=> 'User', 'form_link_id' => $form_link, 'user_form' => 'ExtUserForm', 'expiry_time' => $expiry_time, 'form_title' => $subform_info->title, 'client_info' => $client_info);
             
             //$data = array('name'=> $f_info->name, 'form_link_id' => $form_link);

             //Mail::send(['html'=>'test_email'], $data, function($message) use($user, $subform_info) {
                         //echo "sending mail ";exit;
                         Mail::send(['html'=>'form_email'], $data, function($message) use($user, $subform_info) {
                  $message->to($user->user_email, 'D3G Forms')->subject
                     ($subform_info->title);
                  $message->from('noreply@dev.d3grc.com','D3G Forms');

             });            
    
            
        }       
                      
      return response()->json(['status' => 'success', 'msg' => 'email sent']);
      
            /*
            $subform_id = DB::table('sub_forms')->insertGetId([
                                      'title'          => $title,
                                      'parent_form_id' => $form_id,
                                      'client_id'      => Auth::id(),
                                      'expiry_time'    => $expiry_time
                                ]);
                        
                               $insert_data [] = [
                                        //'form_link_id'   => Str::random(40),
                                        'form_link_id'   => $client_id.'/'.
                                                            $assoc_user->id.'/'.
                                                            base64_encode(Auth::user()->email).'/'.
                                                            $assoc_user->id.'/'.
                                                            base64_encode($assoc_user->email).'/'.
                                                            base64_encode(date('Y-m-d H:i:s'))
                                                            ,
                                        'sub_form_id'    => $subform_id,
                                        'client_id'      => $client_id,
                                        'user_id'        => $assoc_user->id
                                      ];
*/                                    
    /*
      echo "<pre>";
      print_r($emails);
      echo "</pre>";
      exit;*/
      
      //$expiry_time = date('Y-m-d H:i:s', strtotime("+10 days"));

      // echo $expiry_time."<br>";
      // exit;

/*
      $subform_id = DB::table('sub_forms')->insertGetId([
                                      'title'          => 'Title',
                                      'parent_form_id' => $form_id,
                                      'client_id'      => Auth::id(),
                                      'expiry_time'    => $expiry_time
                                ]);
            */                   
      //$this->assign_subform_to_client_users(Auth::id(), $form_id, $subform_id);                        

    }   
    
    
    public function assign_form_to_client (Request $request)
    {
        $client_ids = $request->input('ids');
        $form_id    = $request->input('form_id');
        
        $form_info = DB::table('forms')->where('id', '=', $form_id)->first();
        
/*      echo "<pre>";
        print_r($form_info);
        echo "</pre>";
        exit; */
        
        DB::table('client_forms')->where('form_id', '=', $form_id)->delete();
        
        if ($form_info->code == 'f10') 
        {
            DB::table('sub_forms')->where('parent_form_id', '=', $form_info->id)->delete();
        }       
        
        $expiry_time    = date('Y-m-d H:i:s', strtotime("+10 days"));
        
        $insert_data = [];
        
        foreach ($client_ids as $client_id)
        {
            $insert_data[] = ['client_id' => $client_id, 'form_id' => $form_id];
            
            if ($form_info->code == 'f10') 
            {
                DB::table('sub_forms')->insert(['title'          => $form_info->title, 
                                                'client_id'      => $client_id, 
                                                'parent_form_id' => $form_info->id,
                                                'expiry_time'    => $expiry_time]);
            }           
        }
        
        DB::table('client_forms')->insert($insert_data);
        

        
        echo json_encode(['status' => 'success']);
    }
    
    // for assignment of those users which were added after sub-form was created
    public function ajax_assign_subform_to_users (Request $req)
    {
        // dd($req->all());
        
        $subform_id = $req->input('subform_id');
        $user_ids   = $req->input('asgn_ids');
        $d_user_ids = $req->input('del_ids');
        $client_id  = Auth::user()->client_id;
        $sb_title   = $req->input('subform_title');
                
                $client_info = DB::table('users')->where('id', $client_id)->first();
                // dd($client_info);
                
            $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
            
            if (empty($subform_settings))
            {
                $subform_settings = DB::table('subform_admin_expiration_settings')->first();        
            }
            
            $remaining_time_for_form = '+'.$subform_settings->duration.' '.$subform_settings->period;                
        
        
        if (isset($user_ids) && !empty($user_ids))
        {

            $org_name = DB::table('users')->where('id', Auth::user()->client_id)->pluck('name');
          
            foreach ($user_ids as $key=>$user_id)
            {
                // echo "key ->>>> ".$key;
                    $exist = DB::table('user_forms')->where('client_id', $client_id)->where('sub_form_id', $subform_id)->where('user_id', $user_id)->first();
                if ($exist == null) 
                {
                    // echo  "in if condition  \n";

                    
                    $form_link_id   = Str::random(40);
                    $expiry_time    = date('Y-m-d H:i:s', strtotime($remaining_time_for_form));
                    
                    $insert_data[] = [
                                        'form_link_id'   => $form_link_id,
                                        'sub_form_id'    => $subform_id,
                                        'client_id'      => $client_id,
                                        'user_id'        => $user_id,
                                        'expiry_time'    => $expiry_time
                                        
                    ]; 

                                
                 
                    DB::table('user_forms')->insert($insert_data);
                    $insert_data = null;
                    
                    
                    $user_email = DB::table('users')->where('id', $user_id)->pluck('email')->first();
                    
                    
                    $data = array('name'=> $org_name, 'form_link_id' => $form_link_id, 'user_form' => 'CompanyUserForm', 'expiry_time' => $expiry_time, 'client_info' => $client_info);

                    $email_info = ['email' => $user_email, 'title' => $sb_title, 'client_info' => $client_info];

                    Mail::send(['html'=>'form_email'], $data, function($message) use($email_info) {
                        $message->to($email_info['email'], 'D3G Forms')
                                ->subject($email_info['title']);
                        $message->from('noreply@dev.d3grc.com','D3G Forms');

                    }); 
                                     
                    
                    
                    
                }
                 // echo '<pre>';    
                    // print_r($insert_data);

                // echo 'abc     ---      ';
                    // print_r($insert_data);
                // exit();
    
            }           
        }
        
        if (isset($d_user_ids) && !empty($d_user_ids))
        {
            // dd($d_user_ids);
           $flaag =  DB::table('user_forms')->where('sub_form_id', $subform_id)->whereIn('user_id', $d_user_ids)->delete();      
           // dd($flaag. " deleted");     
        }       

        return response()->json(['status' => 'success', 'msg' => 'Information updated about forms assignment']);
    
    }
    
    // this function is used when sub-form is created and then users are assigned   
    public function assign_subform_to_client_users ($client_id, $form_id, $subform_id)
    {
        //user_id           = 1;
        $user_id            = 'all';

        //$client_id          = Auth::id();
        //$client_id          = 7;

        //if ($req->user_id == 'all')
        
        $client_id    = Auth::user()->client_id;
        $client_email = Auth::user()->email;
        $client_name  = Auth::user()->name;
        
        if (Auth::user()->role == 3 && Auth::user()->user_type == 1)
        {
            /*
            SELECT clients.email 
            FROM   users
            JOIN   users AS clients ON clients.id = users.client_id
            WHERE  users.id = 86
            */
            
            $client_info = DB::table('users')
                              ->join(DB::raw('users as clients'), 'clients.id', '=',  'users.client_id')
                              ->where('users.id', '=', $client_id)
                              ->select('clients.email', 'clients.id', 'clients.name')
                              ->get()
                              ->first();
                              
            if (!empty($client_info))
            {
                $client_id    = $client_info->id;
                $client_email = $client_info->email;
                $client_name  = $client_info->name;
            }
            
            $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
            
            if (empty($subform_settings))
            {
                $subform_settings = DB::table('subform_admin_expiration_settings')->first();        
            }
            
            $remaining_time_for_form = '+'.$subform_settings->duration.' '.$subform_settings->period;
        }        
        

        $insert_data = [];

        if ($user_id == 'all')
        {
            //$client_user_id_list    = DB::table('users')->where('client_id', '=', $client_id)->pluck('id');
            
            $client_user_id_list    = DB::table('users')->where('client_id', '=', $client_id)->select('*')->get();

            if (!empty($client_user_id_list))
            {
                foreach ($client_user_id_list as $assoc_user)
                {
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
                    $form_link_id   = Str::random(40);

                    $insert_data [] = [
                                        'form_link_id'   => $form_link_id,
                                        'sub_form_id'    => $subform_id,
                                        'client_id'      => $client_id,
                                        'user_id'        => $assoc_user->id,
                                        'expiry_time'    => date('Y-m-d H:i:s', strtotime($remaining_time_for_form))
                    ];

                    $data = array('name'=> $client_name, 'form_link_id' => $form_link_id, 'user_form' => 'CompanyUserForm');

                    $email_info = ['email' => $client_email, 'title' => 'Survey form'];

       /*             echo "<pre>";
                    print_r($data);
                                print_r($email_info);

                    echo "</pre>";
                    exit;

                    if (Mail::send(['html'=>'test_email'], $data, function($message) use($email_info) {
                        $message->to($email_info['email'], 'D3G Forms')
                                ->subject($email_info['title']);
                        $message->from('noreply@dev.d3grc.com','D3G Forms');

                    }))
                    {
                        echo "mail sent ";
                    }
                    else
                    {
                        echo "mail not sent ";
                    }

                    */

                    Mail::send(['html'=>'test_email'], $data, function($message) use($email_info) {
                        $message->to($email_info['email'], 'D3G Forms')
                                ->subject($email_info['title']);
                        $message->from('noreply@dev.d3grc.com','D3G Forms');

                    });


                }
            }


            // echo "<pre>";
            // print_r($client_user_id_list);
            // echo "</pre>";
            // get all client's $users

        }
        else
        {
            $form_link_id = Str::random(40);
            $insert_data = [
            /*                                  'form_link_id' => $client_id.'/'.
                                                $assoc_user->id.'/'.
                                                base64_encode(Auth::user()->email).'/'.
                                                $assoc_user->id.'/'.
                                                base64_encode($assoc_user->email).'/'.
                                                base64_encode(date('Y-m-d H:i:s')),*/
                              'form_link_id'   => $form_link_id,
                              'sub_form_id'    =>  $sub_form_id,
                              'user_id'        =>  $user_id,
                              'expiry_time'    => date('Y-m-d H:i:s', strtotime($remaining_time_for_form))
                           ];

            $data = array('name'=> Auth::user()->name, 'form_link_id' => $form_link_id, 'user_form' => 'CompanyUserForm');

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
                    $message->from('noreply@dev.d3grc.com','D3G Forms');

                }))
                {
                    echo "mail sent ";
                }
                else
                {
                    echo "mail not sent ";
                }

            */
            
            Mail::send(['html'=>'test_email'], $data, function($message) use($email_info) {
                $message->to($email_info['email'], 'D3G Forms')
                        ->subject($email_info['title']);
                $message->from('noreply@dev.d3grc.com','D3G Forms');
            });

        }

        DB::table('user_forms')->insert($insert_data);

        /* echo "<pre>";
        print_r($insert_data);
        echo "</pre>";
        exit; */
    }   
    
    
    // Forms/SendForm/{id}
    public function send_form_link_to_users ($sub_form_id)
    {
      //$this->middleware(['auth','2fa']);

      if (Auth::user()->role != 2 && Auth::user()->user_type != 1)
      {
          return abort(404);
      }

      /*
      SELECT *
      FROM  `user_forms`
      JOIN  sub_forms ON sub_forms.id = user_forms.sub_form_id
      JOIN  users     ON users.id     = user_forms.user_id
      WHERE sub_form_id = 4
      */

      $forms_info = DB::table('user_forms')
                      ->join('sub_forms',      'sub_forms.id',     '=', 'user_forms.sub_form_id')
                      ->join('users',      'users.id',     '=', 'user_forms.user_id')
                      ->where('sub_form_id', '=', $sub_form_id)
                      ->select('form_link_id', 'email', 'name', 'title', 'users.id as user_id', 'sub_forms.client_id')->get();

    
    /*
      echo "<pre>";
      print_r($forms_info);
      echo "</pre>";
      exit; 
    */

       foreach ($forms_info as $f_info)
       { 
         $data = array('name'=> $f_info->name, 'form_link_id' => $f_info->form_link_id, 'user_form' => 'ExtUserForm');
         
         //$data = array('name'=> $f_info->name, 'form_link_id' => $form_link);

         Mail::send(['html'=>'test_email'], $data, function($message) use($f_info) {
              $message->to($f_info->email, 'D3G Forms')->subject
                 ($f_info->title);
              $message->from('noreply@dev.d3grc.com','D3G Forms');

         });
       }
       
      $msg =  "Forms email Sent. Check your inbox.";
      
      return view('show_msg', compact('msg'));
      
        // get form users
    }

    public function client_user_subforms_list ()
    {

            
            // if()

            $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null ){
                               foreach ($data as $value) {
                               $assigned_permissions = explode(',',$value);
                                                       }
                          }

        if(Auth::user()->role != 3 ){
            if(!in_array('My Assigned Forms', $assigned_permissions)){
                return redirect('dashboard');
            }
        }
    

        $user_id   = Auth::id();
        //$client_id = Auth::user()->client_id;
        //$client_id = 23;

        /*
        if ($client_id == 0)
        {
            return abort(404);
        }
        */

        /*
            SELECT *
            FROM  `sub_forms`
            LEFT JOIN user_forms ON sub_forms.id = user_forms.sub_form_id
            WHERE sub_forms.client_id = 23

        */
        
        $sub_forms = DB::table('sub_forms')
                ->leftjoin('user_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
                ->where('user_id', $user_id)
                //->where('sub_forms.client_id', $client_id)
                ->get();
        // dd($sub_forms);        
                
                
        return view('client_subform', ['sub_forms' => $sub_forms]);
                
    }


  public function report()
  {
    return view('report');
  }

  public function users_management()
  {   
      $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            if(!in_array('Users Management', $assigned_permissions)){
                return redirect('dashboard');
            }

      if (Auth::user()->role != 2)
      {
             return abort('404');
      }
    $id =Auth::user()->id;
    $client_id = Auth::user()->client_id;
    //$user = DB::table('users')->where('client_id',$client_id)->where('role',3)->get();
    // SELECT u.name, a.name FROM `users` u JOIN users a ON u.created_by = a.id
    
    $user = DB::table('users as u')
              ->select('u.*', DB::raw('a.name as added_by'))
              ->join(DB::raw('users a'), 'u.created_by', '=', 'a.id')
              ->where('u.client_id',$client_id)
              ->orderBy('u.id','DESC')
			  ->where('u.role',3)->get();
			  

              // dd($user);
    
    return view('users_management', compact('user'));
  }

  public function permissions($id)
    {   
        // dd('sad');
        $granted_permissions;
        $granted_permissions = DB::table('module_permissions_users')->where('user_id' , $id)->first();
        if($granted_permissions == null)
             {
             $granted_permissions = [' ' , ' '];
             // dd($granted_permissions);
             }
         elseif ($granted_permissions != null) {
                 # code...
                 $granted_permissions = explode(',',$granted_permissions->allowed_module);
                 // dd($granted_permissions);
             }    
        $permissions = DB::table('module_permissions')->pluck('module');
        // dd('asdasd');
        return view('org-user-permission.add-remove-permission' , compact('permissions' , 'granted_permissions'  , 'id'));
    }

     public function permissions_store(Request $request){
        // dd($request->all());
       $is_assigned_any_permissions = DB::table('module_permissions_users')->where('user_id' , $request->id)->first();
       if($is_assigned_any_permissions != null){
       $data = $request->permiss;
       // dd($data);
       if($data == null)
       {
              $data = ['nodata , nodata'];
       }
            $new = implode(',', $data);
            $result = DB::table('module_permissions_users')->where('user_id' , $request->id)->update([ 
                "user_id" => $request->id,
               "allowed_module" => $new
            ]);

            \Session::flash('success', Lang::get('Permission set for user'));
             return redirect('users_management');
            // dd($result . 'record updated');
       }
       elseif ($is_assigned_any_permissions == null) {
            # code...
            $data = $request->permiss;
            if($data == null)
               {
                     $data = ['nodata , nodata'];
               }
             $new = implode(',', $data);
            $result = DB::table('module_permissions_users')->insert([ 
                "user_id" => $request->id,
               "allowed_module" => $new
            ]);
            // dd('permission set');
            \Session::flash('success', Lang::get('Permission set for user'));
             return redirect('users_management');

        } 
       
  

    }

   public function add_user()
  {
    return view('add_user');
  }

  public function edit_user($id)
  {
        $user= DB::table('users')->where('id',$id)->first();
        $client_id = $user->client_id;
   
        //$administrator = DB::table('users')->where('id',$client_id)->first();

        $company_id = Auth::user()->client_id;
 
        
        if ($client_id != $company_id) {
            return abort('404');
        }
        
        $company_name1 = DB::table('users')->where('id',$company_id)->first();
        if($company_name1==""){
            $company_name = $administrator;
        } else{
           $company_name = $company_name1;
        }
        // print_r($company_name);exit;
    return view('edit_user',compact('user','company_name'));    
  }

  public function change_status(Request $request) 
    { 
         $data = array(
           "user_type" => $request->input('status')
          );
         Auth::user()->where("id", $request->input("id"))->update($data);  
    } 

    public function delete_user(Request $request)
    {
      $id = $request->input("id");
      $data = DB::table('users')->where('id',$id)->first();
      $test = $data->image_name;
      $destinationpath=public_path("img/$test");
                File::delete($destinationpath);
        
        Auth::user()->where("id", $id)->delete(); 
    } 

    public function store_user(Request $request)
    {

       $this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'Please provide proper name to proceed.',
        ],
    
    );

        $slider = $request['slider'];        
        $value = $request['optradio'];
        $any = $request->input('email');
        $company_id = Auth::user()->client_id;
        
        // if ($request->hasFile('images')) {
        //     $request->validate([
        //         'images' => 'dimensions:max_width=800,max_height=600',
        //     ]);             
        
            // $image_size = $request->file('images')->getsize();
            
            // if ( $image_size > 1000000 ) {
            //     return redirect('add_user')->with('alert', 'Maximum size of Image 1MB!')->withInput();            
            // }            
        // }

        
        $test = DB::table('users')->where('email','=',$any)->first();

        $inputs = [
        'password' => $request->password,
                ];
         $rules = [
        'password' => [
            'required',
            'string',
            'min:8',             // must be at least 8 characters in length
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
        ],
    ];
    $validation = \Validator::make( $inputs, $rules );

    if ( $validation->fails() ) {
          return redirect('add_user')->with('alert', 'Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!')->withInput();            
           }
           elseif($request->password != $request->rpassword)
                {
            return redirect('add_user')->with('alert', 'Password did not match!')->withInput();
                }
        elseif (empty($test)) {
            $imgname ='';
            // if($request->hasfile('images')){
            //     $request->validate([
            //         'images' => 'dimensions:max_width=800,max_height=600',
            //     ]);                 
            //     $file=$request->file('images');
            //     $filename = $file->getClientOriginalName();
            //     $ext=$file->getClientOriginalExtension();
            //     $imgname=uniqid().$filename;
            //     $destinationpath=public_path('/img');
            //     $file->move($destinationpath,$imgname);
            // }
            if($request->base_string ){
                $ext = explode('/', mime_content_type($request->base_string))[1];
                $img = $request->base_string;
               
                     $file_name = 'image_'.time().'.jpg';
                     @list($type, $img) = explode(';', $img);
                     @list(, $img)      = explode(',', $img);
                     if($img!=""){
                       \Storage::disk('public')->put($file_name,base64_decode($img));
                       File::move(storage_path().'/app/public/'.$file_name , 'public/img2/'.$file_name);  
                       $imgname = $file_name;                
                     }
         
            }
            // print_r($imgname);exit();
      if($slider== "on"){
        $data = array(
           "name" => $request->input('name'),
           "email" => $request->input('email'),
           "company"=> $request->input('company'),
           "role" => 3,
           "image_name" => $imgname,
           "tfa" => 1,
           "client_id" => $company_id,
           "created_by"=>  Auth::user()->id,
        );
        }else{
             $data = array(
           "name" => $request->input('name'),
           "email" => $request->input('email'),
           "company"=> $request->input('company'),
           "role" => 3,
           "image_name" => $imgname,
           "tfa" => 0,
           "client_id" => $company_id,
           "created_by"=>  Auth::user()->id,
        );
        }
        if($request->input('password')) { 
            $data['password'] = bcrypt($request->input('password'));
        }


        if($request->input('id')) {
            User::where("id", $request->input("id"))->update($data);
            $insert_id = $request->input("id");
        } else { 
            $insert_id = User::insertGetId($data);
        }
        \Session::flash('success', Lang::get('general.success_message'));
        return redirect('users_management');
        }
        else
        {
            return redirect('add_user')->with('alert', 'Email already exists!')->withInput();
        }          
    }

    public function store_edit(Request $request, $id)
    {

             $this->validate($request, [
            'name' => 'required',
        ],
        [
            'name.required' => 'Please provide proper name to proceed.',
        ],
    
    );
            $slider = $request['slider'];
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
            'min:8',             // must be at least 8 characters in length
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
        ],
    ];
    $validation = \Validator::make( $inputs, $rules );

    if($request->password!=""){
    if ( $validation->fails() ) {
          return redirect('edit_user/'.$id)->with('alert', 'Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!')->withInput();            
           }elseif($request->password != $request->rpassword)
                {
            return redirect('edit_user/'.$id)->with('alert', 'Password did not match!');
                } 
                else{      
           if($request->base_string ){
                $ext = explode('/', mime_content_type($request->base_string))[1];
                $img = $request->base_string;
               
                     $file_name = 'image_'.time().'.jpg';
                     @list($type, $img) = explode(';', $img);
                     @list(, $img)      = explode(',', $img);
                     if($img!=""){
                       \Storage::disk('public')->put($file_name,base64_decode($img));
                       File::move(storage_path().'/app/public/'.$file_name , 'public/img2/'.$file_name);  
                       $imgname = $file_name;                
                     }
         
            }
            else{
                $imgname =$previous_image;
            }
            $record = array(
           "name" => $request->input('name'),
           "image_name" => $imgname,
        );
        if($request->input('password')) { 
            $record['password'] = bcrypt($request->input('password'));
        }
        if($request->input('id')) {

            // $destinationpath=public_path("img/$test");
            // File::delete($destinationpath);
            User::where("id", $request->input("id"))->update($record);
            $insert_id = $request->input("id");
        } else { 
            $insert_id =  User::insertGetId($record);
        }            
        $fa = User::where("id", $request->input("id"))->first();
        if($fa->tfa==0){                
                DB::table('password_securities')->where('user_id',$id)->delete();
            }
            return redirect("users_management");
        }
    }
    else{
         if($request->password != $request->rpassword)
                {
            return redirect('edit_user/'.$id)->with('alert', 'Password did not match!');
                } 
                else{      
                    
             if($request->base_string ){
                $ext = explode('/', mime_content_type($request->base_string))[1];
                $img = $request->base_string;
               
                     $file_name = 'image_'.time().'.jpg';
                     @list($type, $img) = explode(';', $img);
                     @list(, $img)      = explode(',', $img);
                     if($img!=""){
                       \Storage::disk('public')->put($file_name,base64_decode($img));
                       File::move(storage_path().'/app/public/'.$file_name , 'public/img2/'.$file_name);  
                       $imgname = $file_name;                
                     }
         
            }
            else{
                $imgname =$previous_image;
            }

            if($slider=="on"){
            $record = array(
           "name" => $request->input('name'),
           "image_name" => $imgname,
           "tfa" => 1,           
        );
        }else{
            $record = array(
           "name" => $request->input('name'),
           "image_name" => $imgname,
           "tfa" => 0,           
        );
        }

        if($request->input('password')) { 
            $record['password'] = bcrypt($request->input('password'));
        }
        if($request->input('id')) {

            // $destinationpath=public_path("img/$test");
            // File::delete($destinationpath);
            User::where("id", $request->input("id"))->update($record);           
            $insert_id = $request->input("id");
            
        } else { 
            $insert_id =  User::insertGetId($record);
        }

       $fa = User::where("id", $request->input("id"))->first();
        if($fa->tfa==0){                
                DB::table('password_securities')->where('user_id',$id)->delete();
            }
            return redirect("users_management");
        }
    }
    }
    
    public function assign_section_category (Request $req)
    {
        $form_id  = $req->input('form_id');
        $ctgry_id = $req->input('ctg_id');
        $sec_name = $req->input('sec_name');
        
        if (DB::table('questions')
              ->where('form_id',               $form_id)
              ->where('question_section',      $sec_name)
              ->update(['question_category' => $ctgry_id]))
        {
            return response()->json(['status' => 'success', 'msg' => 'Category against this section updated']);     
        }
        
    }
    
    public function show_subforms_expiry_settings ()
    {
     // dd('aslkdjaskldj')
        
  $user = Auth::user()->id;
            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            if(!in_array('Sub Forms Expiry Settings', $assigned_permissions)){
                return redirect('dashboard');
            }



        $subform_settings = DB::table('subform_client_expiration_settings')->where('client_id', Auth::user()->client_id)->first();
        
        if (empty($subform_settings))
        {
            $subform_settings = DB::table('subform_admin_expiration_settings')->first();        
        }
        
        return view('subforms.subforms_expiry_settings', ['subform_settings' => $subform_settings]);
    }
    
    public function save_subforms_expiry_settings (Request $request)
    {
        DB::table('subform_client_expiration_settings')
            ->updateOrInsert(
                ['client_id' => $request->input('client_id')],
                ['duration' => $request->input('duration'), 'period' => $request->input('period')]
            );
        
        return response()->json(['status' => 'success', 'msg' => 'updated']);       
    }   

}
