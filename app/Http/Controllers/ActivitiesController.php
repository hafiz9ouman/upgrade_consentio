<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;

class ActivitiesController extends Controller
{
    public function __construct()
    {

   
    }
    
    public function index ()
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
            if(!in_array('Activities List', $assigned_permissions)){
                return redirect('dashboard');
            }
          // }


        $client_id = Auth::user()->client_id;
//         $company_external_users    = DB::table('external_users_forms')->distinct('user_email')->pluck('user_email')->toArray();
//         $external_user_activities  = DB::table('external_users_filled_response')
//                                        ->selectRaw('*, GROUP_CONCAT(question_response) as merged_responses')
//                                        ->whereIn('external_user_form_id', DB::table('external_users_forms')
//                                        ->where('client_id', $id)->pluck('id'))
//                                        ->whereIn('question_id', DB::table('questions')
//                                                                   ->where('question', 'like', '%What activity are you assessing%')->orWhere('question', 'like', '%which department is involved in this activity%')->pluck('id'))
//                                        ->whereIn('user_email', $company_external_users)
//                                        ->groupBy('external_user_form_id')
// //                                       ->distinct('question_response')
//                                        ->get();
//         // add filter by client id

//         $company_internal_users    = DB::table('user_forms')->where('client_id', Auth::user()->client_id)->distinct('id')->pluck('user_id')->toArray();
//         $internal_user_activities  = DB::table('internal_users_filled_response')
//                                        ->selectRaw('*, GROUP_CONCAT(question_response) as merged_responses')
//                                        ->whereIn('user_form_id', DB::table('user_forms')
//                                        ->where('client_id', $id)->pluck('id'))
//                                        ->whereIn('question_id', DB::table('questions')
//                                                                   ->where('question', 'like', '%What activity are you assessing%')->orWhere('question', 'like', '%which department is involved in this activity%')->pluck('id'))
//                                        ->whereIn('user_id', $company_internal_users)
//                                        ->groupBy('user_form_id')
// //                                       ->distinct('question_response')
//                                        ->get();
                                    
//         //$activity_list = $internal_user_activities->merge($external_user_activities)->unique();
//         $activity_list = $internal_user_activities->merge($external_user_activities);
        
        // return view('activities.activities', ['activity_list' => $activity_list, 'user_type' => (Auth::user()->role == 1)?('admin'):('client')]);

        $act = DB::table('questions')->where('question' , 'What activity are you assessing?')->pluck('form_key');

        $filled_questions = DB::table('external_users_forms')->select('*' )->wherein('external_users_filled_response.question_key' , $act )
          ->join('external_users_filled_response' , 'external_users_forms.id' , '=' ,'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id' , $client_id)->get();
         foreach ($filled_questions as $key => $value) {
              $value->form_type = 'external';
          } 

           $filled_questions_internal = DB::table('user_forms')->select('*' )->wherein('internal_users_filled_response.question_key' , $act)
          ->join('internal_users_filled_response' , 'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->get();
          foreach ($filled_questions_internal as $key => $value) {
              $value->form_type = __('internal');
          } 



          $filled_questions = $filled_questions->merge($filled_questions_internal);
          // dd($filled_questions);
          foreach($filled_questions as $fq){
            if($fq->form_type == 'external'){
                $fq->form_link = url('Forms/ExtUserForm/'.$fq->form_link);
            }
            else{
                    
                $fq->form_link = url('Forms/CompanyUserForm/'.$fq->form_link_id);
              }
            if(!isset($fq->user_email)){
               $fq->user_email = DB::table('users')->where('id' , $fq->user_id)->pluck('name')->first();

            }
            }
            
          
          // dd($filled_questions);
        // dd($filled_questions);
         $user_type = (Auth::user()->role == 1)?('admin'):('client');


        return view('activities.activities' , compact('filled_questions' , 'user_type'));

    }
}