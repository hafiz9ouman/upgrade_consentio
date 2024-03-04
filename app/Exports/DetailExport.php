<?php

namespace App\Exports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\PasswordSecurity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class DetailExport implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */

    public $cat_id;

	 function __construct($cat_id) {
	        $this->cat_id = $cat_id;
	 }
    public function collection()
    {
        
    }

    public function view(): view
    {

    	 $category_id = $this->cat_id;
		 //***********************************Global Data
    if($category_id == 1){
      $final=null;
      
        $user = Auth::user()->id;

            $assigned_permissions =array();
            $data = DB::table('module_permissions_users')->where('user_id' , $user)->pluck('allowed_module');

            if($data != null){
                 foreach ($data as $value) {
                $assigned_permissions = explode(',',$value);
                 
            }
            }
            // if(Auth::user()->role != 3 ){
            if(!in_array('Detailed Data Inventory', $assigned_permissions)){
                return redirect('dashboard');
            }
          
        $client_id = Auth::user()->client_id;
        $option_questions = array();
        $piaDpiaRop_ids = [2,9,12];
        
          $filled_questions = DB::table('external_users_forms')->select('*' )
          ->join('external_users_filled_response' , 'external_users_forms.id' , '=' ,'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id' , $client_id)->pluck('question_key');

           $filled_questions_internal = DB::table('user_forms')->select('*' )
          ->join('internal_users_filled_response' , 'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->pluck('question_key');
        
          $filled_questions = $filled_questions->merge($filled_questions_internal);
       
        $question  = DB::table('questions')->where('type','mc')->wherein('form_id' , $piaDpiaRop_ids )->wherein('form_key' , $filled_questions )
                                           ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                          ->get();

        $data_inv_forms = DB::table('questions')->where('is_data_inventory_question' , 1)->pluck('form_id')->unique()->toArray();
        $new_data_inv_questions =   DB::table('questions')->where('type','mc')->wherein('form_id' , $data_inv_forms )->wherein('form_key' , $filled_questions )
                                           ->where('is_data_inventory_question' , 1)
                                           ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                          ->get();                                        
        // dd($question);
        $question = $question->merge($new_data_inv_questions);
        $question = $question->unique('question');
        // dd($question);

        //bari start
  $en_opt = array();
  $fr_opt = array();

  foreach($question as $questions){
  $data = $questions->options;
  $options_array = explode(",",$data);
  $en_opt[] = array_filter(array_map('trim', $options_array));
  }
  $opt_final_en =array_flatten($en_opt);
  foreach($question as $questions){
  $data = $questions->options_fr;
  $options_array = explode(",",$data);
  $fr_opt[] = array_filter(array_map('trim', $options_array));
  }
  $opt_final_fr =array_flatten($fr_opt);

        //bari end
        // echo "<pre>";
        // print_r($question);
       //   exit();
         $opt=null;



          foreach ($question as $value) {


             $option = $value->question;
             $option_fr = $value->question_fr;

             $temporary_question  = DB::table('questions')->where('question' , $option )->pluck('form_key');
                 // $question_response = DB::table('external_users_filled_response')->wherein('question_key' , $temporary_question )->get();
                 
                 $question_response = DB::table('external_users_forms')->select('*' )->wherein('external_users_filled_response.question_key' , $temporary_question )
                       ->join('external_users_filled_response' , 'external_users_forms.id' , '=' ,'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id' , $client_id)->get();


                  // $question_response2 = DB::table('internal_users_filled_response')->wherein('question_key' , $temporary_question )->get();

                   $question_response2 = DB::table('user_forms')->select('*' )->wherein('internal_users_filled_response.question_key' , $temporary_question )
                         ->join('internal_users_filled_response' , 'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->get();


                  // dd($question_response);
                  if(count($question_response2) > 5){
                    foreach($question_response2 as $internal_question){
                      $internal_question->external_user_form_id = $internal_question->user_form_id;
                      $internal_question->user_email = DB::table('users')->where('id' , $internal_question->user_id)->pluck('name')->first();
                    }
                  }
                  $question_response = $question_response->merge($question_response2);
                 // dd($question_response2);
                        $final_options_array= array();
                        $count = 0;
                        foreach ($question_response as $resp) {
                       
                            $remove_duplicate_options = array();
                          $user_responses = explode(',',$resp->question_response);
                          foreach ($user_responses as $pickoption) {
                        if(!in_array(trim($pickoption), $final_options_array))
                        { 
                          // dd(str_contains($pickoption, "UTF-8"));
                          if(mb_substr($pickoption, 0, 1, "UTF-8") != "{" )
                          {
                            if(strlen($pickoption) >1){
                                  $final_options_array[]=trim($pickoption);
                      }

                                                                            }
                        }
                                   
                                                     }
                        }
                        //bari start

                              $ww = array();
$ee = array();
foreach($final_options_array as $qq){
$new = array_search($qq, $opt_final_fr);
if($new !== false){
$ww[] = $opt_final_en[array_search($qq, $opt_final_fr)];
}
if($new == false){
$ee[] = $opt_final_en[array_search($qq, $opt_final_en)];
}
$final_options_array = array_merge($ee, $ww);
// echo "<pre>";
// print_r($ddd);
}
// exit;
// $ddd = array_merge($final_options_array, $ww);
$final_options_array = array_unique($final_options_array);
                        //bari end
                          $count =count($final_options_array);
              $op_total= $final_options_array;

                        foreach ($final_options_array as $tt ) {
                      $opt[] = explode(',',$tt);
                                                          }
                        // print_r($final_options_array);

             // $temp = DB::table('questions')->where('id',$value->id)->pluck('options')->first();
                 
           //       $op_count = $count;
           //       array_push($option_questions, array(
              // "question_string" => $option,
              // "op_count" => $op_count
             //         )); 
             $op_count = $count;
array_push($option_questions, array(
"question_string" => $option,
"question_string_fr" => $option_fr,
"op_count" => $op_count,
"op_total" => $op_total,
)); 

          }

          $final=null;
          if($opt != null){
              foreach ($opt as $jugar) {
              
                  $final[]=$jugar[0];
                # code...
              }
            }
            // dd($final);
        $mc_ids  = DB::table('questions')->where('type', 'mc' )->wherein('form_id' , $piaDpiaRop_ids )
                                            ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                           ->pluck('form_key');
        $data_inv_forms = DB::table('questions')->where('is_data_inventory_question' , 1)->pluck('form_id')->unique()->toArray();
        $new_data_inv_mc_ids =   DB::table('questions')->where('type', 'mc' )->wherein('form_id' , $data_inv_forms )
                                            ->where('is_data_inventory_question' , 1)
                                            ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                           ->pluck('form_key'); 
        $mc_ids = $mc_ids->merge($new_data_inv_mc_ids);                                                                                       
            $emails = DB::table('external_users_forms')->select('*' )
                ->join('external_users_filled_response' , 'external_users_forms.id' , '=' ,'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id' , $client_id)->wherein('question_key' , $mc_ids)->get();
                // dd($emails);

             // $emails = DB::table('external_users_filled_response')->wherein('question_key' , $mc_ids)->select('user_email','external_user_form_id')->get();
   
                 $emails_internal = DB::table('user_forms')->select('*' )
                  ->join('internal_users_filled_response' , 'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->wherein('question_key' , $mc_ids)->get();
                  // dd($emails_internal);
   
        // $emails_internal = DB::table('internal_users_filled_response')->wherein('question_key' , $mc_ids)->select('user_id as user_email'  ,'user_form_id as external_user_form_id' )->get();
         if(count($emails_internal) > 5){
          foreach($emails_internal as $internal_question){
                      $internal_question->external_user_form_id = $internal_question->user_form_id;
                      $internal_question->user_email = DB::table('users')->where('id' , $internal_question->user_id)->pluck('name')->first();
                    }
                  }

                    $emails=$emails -> merge($emails_internal);


        $emails = $emails->unique('external_user_form_id');
        // dd($emails);

        
          $data = array();
          $flag = false;
          $count = 0;
          // dd($emails);
          if(count($emails) >0){
          foreach($emails as $users){
                    // print_r($users->external_user_form_id);
                  $ex_user_res  = DB::table('external_users_filled_response')->wherein('question_key' , $mc_ids)->where('external_user_form_id' , $users->external_user_form_id )->get();

                  $question_response2 = DB::table('internal_users_filled_response')->wherein('question_key' , $mc_ids )->where('user_form_id' , $users->external_user_form_id )->get();
                  if(count($question_response2) > 0){
                    foreach($question_response2 as $internal_question){
                      $internal_question->external_user_form_id = $internal_question->user_form_id;
                      $internal_question->user_email = DB::table('users')->where('id' , $internal_question->user_id)->pluck('name')->first();
                                             }
                                         }
                                          $ex_user_res = $ex_user_res ->merge($question_response2);

                        $rr=array();

                   // dd($users);
                  foreach ($ex_user_res as $ex_u_res) {
                         // echo "<pre>";
                         // print_r($ex_u_res->user_email.$users->user_email );
                         // // continue;
                    if($ex_u_res->user_email == $users->user_email){
                        $flag = true;
                        $tempo =  $ex_u_res->question_response;
                        $exusres = explode(',',$tempo);
                        foreach ($exusres as $tt ) 
                        {
                  $rr[] = explode(',',$tt);
                      }
                     
                    }

                 if($flag == true){
                    $finalar = array();
              foreach ($rr as $jugar) {
                $finalar[]=$jugar[0];
            }
            $flag = false;
                 }


                  }
         
              $exuserfrmid = DB::table('external_users_forms')->where('id' ,$users->external_user_form_id)->pluck('sub_form_id')->first();
              $form_name = DB::table('sub_forms')->where('id' ,$exuserfrmid)->pluck('title')->first();
              $form_name_fr = DB::table('sub_forms')->where('id' ,$exuserfrmid)->pluck('title_fr')->first();

              if($form_name == null) {
                 $exuserfrmid = DB::table('user_forms')->where('id' ,$users->external_user_form_id)->pluck('sub_form_id')->first();
                 $form_name = DB::table('sub_forms')->where('id' ,$exuserfrmid)->pluck('title')->first();
              }
               if($form_name_fr == null) {
                 $exuserfrmid = DB::table('user_forms')->where('id' ,$users->external_user_form_id)->pluck('sub_form_id')->first();
                 $form_name_fr = DB::table('sub_forms')->where('id' ,$exuserfrmid)->pluck('title_fr')->first();
              }

      //   dd($form_name, $form_name_fr);

          //  array_push($data, array(
          //  "email" => $users->user_email,
          //  "response" => $finalar,
          //  "sub_form_title" => $form_name
          // ));
            //bari start
             $finalar= array_filter($finalar);

$fin = array();
foreach($finalar as $finall){
$fin[] = trim($finall);
}
$finalar = $fin;

if(count($finalar) > 0){
$language = 'en';
}
else{
$language = 'undefined';
}
$finalar_fr = array();
foreach($finalar as $selectedOptions){
$selectedOptions = trim($selectedOptions);
$index = null;
$check = array_search($selectedOptions,$opt_final_en);
if($check === false){
$language = 'fr';
}else{
$language = 'en';
}
if($language == 'fr'){
$finalar_fr[] = $opt_final_en[array_search($selectedOptions, $opt_final_fr) ];
}
if($language == 'en'){
$finalar_fr[] = $opt_final_fr[array_search($selectedOptions, $opt_final_en) ];
}
}
if($language == 'en'){
$op_count = $count;
array_push($data, array(
"email" => $users->user_email,
"response" => $finalar,
"response_fr" => $finalar_fr,
"sub_form_title" => $form_name,
"sub_form_title_fr" => $form_name_fr
));
$finalar_fr[] = $finalar = array();
}
if($language == 'fr'){
$temp = array();
$temp = $finalar_fr;
$finalar_fr = $finalar;
$finalar = $temp;
$op_count = $count;
array_push($data, array(
"email" => $users->user_email,
"response" => $finalar,
"response_fr" => $finalar_fr,
"sub_form_title" => $form_name,
"sub_form_title_fr" => $form_name_fr
));
$finalar_fr[] = $finalar = array();
}
              //bari end

          }
      }
        $final_fr = array();
        if(!isset($final)){
          $final = NULL;
        }
        else{
            if(count($final) > 0){
$language = 'en';}
else{
$language = 'undefined';
}
foreach($final as $final1){

$final_fr[] = $opt_final_fr[array_search($final1, $opt_final_en) ];
}
        }






               return view('global_report_export',compact('final','data','option_questions','final_fr'));
    }
    else{

             $piaDpiaRop_ids = [2,9,12];
            $client_id = Auth::user()->client_id;
            $option_questions = array();
            // $filled_questions = DB::table('external_users_filled_response')->pluck('question_key');          
            $filled_questions = DB::table('external_users_forms')->select('*' )
             ->join('external_users_filled_response' , 'external_users_forms.id' , '=' ,'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id' , $client_id)->pluck('question_key');
          // $filled_questions = DB::table('external_users_filled_response')->pluck('question_key');
          // $filled_questions_internal = DB::table('internal_users_filled_response')->pluck('question_key');
           $filled_questions_internal = DB::table('user_forms')->select('*' )
          ->join('internal_users_filled_response' , 'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->pluck('question_key');
          $filled_questions = $filled_questions->merge($filled_questions_internal);
          $question = [];
          // if($category_id == 1){  
        $question  = DB::table('questions')->where('type','mc')
                           ->wherein('form_id' , $piaDpiaRop_ids )
                           ->wherein('form_key' , $filled_questions )
                                             ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                             ->get();
        $data_inv_forms = DB::table('questions')->where('is_data_inventory_question' , 1)
                            ->pluck('form_id')->unique()->toArray();                                           
        $new_data_inv_questions =   DB::table('questions')->where('type','mc')
                                  ->wherein('form_id' , $data_inv_forms )
                                  ->wherein('form_key' , $filled_questions )
                                                  ->where('is_data_inventory_question' , 1)
                                                  ->where( function($query) {
                                                     return $query
                                                            ->where('question_num', '=', null)
                                                            ->orWhere('question_num', '=', '');
                                                    })
                                                 ->get();                                        
        $question = $question->merge($new_data_inv_questions);
        $question = $question->unique('question'); 
         $question = $question->unique('question');
         $opt=null;
         foreach ($question as $value) {

             $option = $value->question;
             $temporary_question  = DB::table('questions')->where('question' , $option )->pluck('form_key');
                 $question_response = DB::table('external_users_forms')->select('*' )
                            ->wherein('external_users_filled_response.question_key' , $temporary_question )
                                      ->join(
                                             'external_users_filled_response' ,
                                             'external_users_forms.id' , '=' ,
                                             'external_users_filled_response.external_user_form_id')
                                      ->where('external_users_forms.client_id' , $client_id)->get();

                  $question_response2 = DB::table('user_forms')->select('*' )
                                      ->wherein('internal_users_filled_response.question_key' , $temporary_question )
                                      ->join(
                                             'internal_users_filled_response' ,
                                              'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->get();


                  if(count($question_response2) > 5){
                    foreach($question_response2 as $internal_question){
                      $internal_question->external_user_form_id = $internal_question->user_form_id;
                      $internal_question->user_email = DB::table('users')->where('id' , $internal_question->user_id)->pluck('name')->first();
                    }
                  }
                  $question_response = $question_response->merge($question_response2);
              
                        $final_options_array= array();
                        $count = 0;
                        foreach ($question_response as $resp) {
                                $remove_duplicate_options = array();
                              $user_responses = explode(',',$resp->question_response);
                                foreach ($user_responses as $pickoption) {
                              if(!in_array(trim($pickoption), $final_options_array))
                              { 
                                  // dd(str_contains($pickoption, "UTF-8"));
                                  // if(mb_substr($pickoption, 0, 1, "UTF-8") != "{" )
                                  // {
                                    if(strlen($pickoption) >1){
                                          $final_options_array[]=trim($pickoption);
                              }                                    // }
                              }
                          }
                        }
                          $count =count($final_options_array);
                            $tot_options =$final_options_array; 

                          foreach ($final_options_array as $tt ) {
                                $opt[] = explode(',',$tt);
                }
                 $op_count = $count;
                 array_push($option_questions, array(
              "question_string" => $option,
              "op_count" => $op_count,
                  "total_op" =>$tot_options,
                  "user_responses" =>$question_response,
                     ));   
          }
          
          // dd($option_questions);
          $final=null;
          if($opt != null){
              foreach ($opt as $jugar) {
              
                  $final[]=$jugar[0];
                # code...
              }
            }
             $mc_ids = [];
             // if($category_id == 1){
               $mc_ids  = DB::table('questions')->where('type', 'mc' )->wherein('form_id' , $piaDpiaRop_ids )
                                         ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                         ->pluck('form_key');
        $data_inv_forms = DB::table('questions')->where('is_data_inventory_question' , 1)->pluck('form_id')->unique()->toArray();
        $new_data_inv_mc_ids =   DB::table('questions')->where('type', 'mc' )->wherein('form_id' , $data_inv_forms )
                                            ->where('is_data_inventory_question' , 1)
                                            ->where( function($query) {
                                               return $query
                                                      ->where('question_num', '=', null)
                                                      ->orWhere('question_num', '=', '');
                                              })
                                           ->pluck('form_key'); 
        $mc_ids = $mc_ids->merge($new_data_inv_mc_ids); 

           
       


            $emails = DB::table('external_users_forms')->select('*' )
                ->join('external_users_filled_response' , 'external_users_forms.id' , '=' ,'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id' , $client_id)->wherein('question_key' , $mc_ids)->get();

           
                 $emails_internal = DB::table('user_forms')->select('*' )
                  ->join('internal_users_filled_response' , 'user_forms.id' , '=' ,'internal_users_filled_response.user_form_id')->where('user_forms.client_id' , $client_id)->wherein('question_key' , $mc_ids)->get();
             
         if(count($emails_internal) > 5){
          foreach($emails_internal as $internal_question){
                      $internal_question->external_user_form_id = $internal_question->user_form_id;
                      $internal_question->user_email = DB::table('users')->where('id' , $internal_question->user_id)->pluck('name')->first();
                    }
                  }

                    $emails=$emails -> merge($emails_internal);
                    // dd($emails);


        $emails = $emails->unique('external_user_form_id');
          $data = array();
          $flag = false;
          $count = 0;
          if(count($emails) >0){
          foreach($emails as $users){
                  // dd($users);
            $users->question_string = DB::table('questions')->where('id' , $users->question_id)->pluck('question')->first();
            $users->question_string_fr = DB::table('questions')->where('id' , $users->question_id)->pluck('question_fr')->first();

            $users->sub_form_name = DB::table('sub_forms')->where('id' , $users->sub_form_id)->pluck('title')->first();
            $users->sub_form_name = DB::table('sub_forms')->where('id' , $users->sub_form_id)->pluck('title_fr')->first();

                  $ex_user_res  = DB::table('external_users_filled_response')
                                ->wherein('question_key' , $mc_ids)
                                ->where('external_user_form_id' , $users->external_user_form_id )
                                ->get();

                  $question_response2 = DB::table('internal_users_filled_response')
                                    ->wherein('question_key' , $mc_ids )
                                    ->where('user_form_id' , $users->external_user_form_id )
                                    ->get();

                  if(count($question_response2) > 0){
                        foreach($question_response2 as $internal_question){
                          $internal_question->external_user_form_id = $internal_question->user_form_id;
                          $internal_question->user_email = DB::table('users')
                                          ->where('id' , $internal_question->user_id)
                                          ->pluck('name')
                                          ->first();
                                             }
                                         }
                  $ex_user_res = $ex_user_res->merge($question_response2);
                  // dd($ex_user_res);

// $finalar= array_filter($finalar);
// $fin = array();
//   foreach($finalar as $finall){
//     $fin[] = trim($finall);
//   }
// $finalar = $fin;

//   if(count($finalar) > 0){
//     $language = 'en';
//   }
//   else{
//     $language = 'undefined';
//   }
// $finalar_fr = array();
// foreach($finalar as $selectedOptions){
// $selectedOptions = trim($selectedOptions);
// $index = null;
// $check = array_search($selectedOptions,$opt_final_en);
// if($check === false){
// $language = 'fr';
// }else{
// $language = 'en';
// }
// if($language == 'fr'){
// $finalar_fr[] = $opt_final_en[array_search($selectedOptions, $opt_final_fr) ];
// }
// if($language == 'en'){
// $finalar_fr[] = $opt_final_fr[array_search($selectedOptions, $opt_final_en) ];
// }
// }
// if($language == 'en'){
// $op_count = $count;
// array_push($data, array(
// "email" => $users->user_email,
// "response" => $finalar,
// "response_fr" => $finalar_fr,
// "sub_form_title" => $form_name,
// "sub_form_title_fr" => $form_name_fr
// ));
// $finalar_fr[] = $finalar = array();
// }
// if($language == 'fr'){
// $temp = array();
// $temp = $finalar_fr;
// $finalar_fr = $finalar;
// $finalar = $temp;
// $op_count = $count;
// array_push($data, array(
// "email" => $users->user_email,
// "response" => $finalar,
// "response_fr" => $finalar_fr,
// "sub_form_title" => $form_name,
// "sub_form_title_fr" => $form_name_fr
// ));
// $finalar_fr[] = $finalar = array();
// }



                  $users->all_users_responses = $ex_user_res;
                  // dd();
                  $rr=array();

                  foreach ($ex_user_res as $ex_u_res) {
                        if($ex_u_res->user_email == $users->user_email){
                            $flag = true;
                            $tempo =  $ex_u_res->question_response;
                            $exusres = explode(',',$tempo);
                            // dd($exusres,$ex_user_res  ,$users);
                              foreach ($exusres as $tt ) {
                           $rr[] = explode(',',$tt);
                            }
                          }
                       if($flag == true){
                            $finalar = array();
                          foreach ($rr as $jugar) {
                            $finalar[]=$jugar[0];
                        }
                        $flag = false;
                       }


            }
         
              $exuserfrmid = DB::table('external_users_forms')->where('id' ,$users->external_user_form_id)->pluck('sub_form_id')->first();
              $form_name = DB::table('sub_forms')->where('id' ,$exuserfrmid)->pluck('title')->first();
              if($form_name == null) {
                 $exuserfrmid = DB::table('user_forms')->where('id' ,$users->external_user_form_id)->pluck('sub_form_id')->first();
                 $form_name = DB::table('sub_forms')->where('id' ,$exuserfrmid)->pluck('title')->first();
              }
           array_push($data, array(
            "email" => $users->user_email,
            "response" => $finalar,
            "sub_form_title" => $form_name,
          ));

          }
          // dd($emails);
      }
    

            $user_type = 'client';
            $heading = 'Heading';
              // dd($final , $data  , $option_questions);
              // dd($emails);
              

               return view('response_reports',compact('final','data','option_questions' ,'emails' ,'heading' , 'user_type'));

    }}
}
