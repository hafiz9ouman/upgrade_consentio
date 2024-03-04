<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\EvaluationRating;
use App\RemediationPlan;
use App\UserResponse;
use App\UserFormLink;
use App\SubForm;
use App\Question;
use App\User;
use Auth;

class RemediationController extends Controller{

    public function remediation_plans(){
        $remediation_plans = [];
        if(Auth::user()->role == 2){
            $remediation_plans = DB::table("remediation_plans")
                ->join("sub_forms","sub_forms.id", "remediation_plans.sub_form_id")
                ->join("forms","forms.id", "sub_forms.parent_form_id")
                ->join("audit_questions_groups","audit_questions_groups.id", "forms.group_id")
                // ->join("assets", "assets.id", "sub_forms.asset_id")
                ->select(
                    "remediation_plans.client_id", 
                    "remediation_plans.id as remediation_plan_id", 
                    "remediation_plans.person_in_charge", 
                    "remediation_plans.sub_form_id as sub_form_id",
                    "sub_forms.title as form_title",
                    "sub_forms.title_fr as form_title_fr",
                    "sub_forms.item_type as type",
                    "sub_forms.other_number as asset_number",
                    "sub_forms.other_id as name",
                    "audit_questions_groups.group_name",
                    "audit_questions_groups.group_name_fr"
                )
                ->groupby("remediation_plans.sub_form_id")
                ->having("remediation_plans.client_id", Auth::user()->client_id)->get();  
                // dd($remediation_plans);

                foreach($remediation_plans as $plan){
                    if ($plan->type != "others") {
                        $sub_form           = SubForm::find($plan->sub_form_id);
                        $asset              =  DB::table('assets')->find($sub_form->asset_id);
                        $plan->asset_number = $asset->asset_number;
                        $plan->name         = $asset->name;
                    }
                }
        }
        else{
            $remediation_plans = DB::table("remediation_plans")
                ->join("sub_forms","sub_forms.id", "remediation_plans.sub_form_id")
                ->join("forms","forms.id", "sub_forms.parent_form_id")
                ->join("audit_questions_groups","audit_questions_groups.id", "forms.group_id")
                // ->join("assets", "assets.id", "sub_forms.asset_id")
                ->select(
                    "remediation_plans.client_id", 
                    "remediation_plans.id as remediation_plan_id", 
                    "remediation_plans.person_in_charge", 
                    "remediation_plans.sub_form_id as sub_form_id",
                    "sub_forms.title as form_title",
                    "sub_forms.title_fr as form_title_fr",
                    "sub_forms.item_type as type",
                    "sub_forms.other_number as asset_number",
                    "sub_forms.other_id as name",
                    "audit_questions_groups.group_name",
                    "audit_questions_groups.group_name_fr"
                )
                ->groupby("remediation_plans.sub_form_id")
                ->where("remediation_plans.person_in_charge", Auth::user()->id)->get();
                // dd($remediation_plans);
            
            foreach($remediation_plans as $plan){
                $sub_form = SubForm::find($plan->sub_form_id);

                if ($sub_form->item_type != "others") {
                    $asset                  =  DB::table('assets')->find($sub_form->asset_id);
                    $plan->form_title       = $sub_form->title;
                    $plan->form_title_fr    = $sub_form->title_fr;
                    $plan->asset_number     = $asset->asset_number;
                    $plan->name             = $asset->name;
                    $plan->type             = $sub_form->item_type;
                }else{
                    $plan->form_title       = $sub_form->title;
                    $plan->form_title_fr    = $sub_form->title_fr;
                    $plan->type             = $sub_form->item_type;
                    $plan->asset_number     = $sub_form->other_number;
                    $plan->name             = $sub_form->other_id;
                }
            }
        }    
        return view("remediation.remediation_plans", compact('remediation_plans'));
    }

    public function old_remediation_plans(){
        try {
            $remediation_plans = [];
            $users = User::where('client_id', Auth::user()->client_id)->get();
            $query = DB::table("remediation_plans")
                        ->join("group_questions", "group_questions.id", "remediation_plans.control_id")
                        ->join("user_responses", "user_responses.question_id", "group_questions.id")
                        ->join("sub_forms","sub_forms.id", "user_responses.sub_form_id")
                        ->join("assets", "assets.id", "sub_forms.asset_id")
                        ->join("evaluation_rating", "evaluation_rating.id", "user_responses.rating") 
                        ->select(
                            "remediation_plans.*", 
                            "group_questions.question_short as question",
                            "group_questions.question_short_fr as question_fr",
                            "group_questions.control_id as control_id",
                            "user_responses.rating",
                            "user_responses.question_response",
                            "user_responses.admin_comment",
                            "user_responses.question_response",
                            "evaluation_rating.rating",
                            "group_questions.id as q_id",
                            "sub_forms.id as sub_form_id",
                            "sub_forms.title as sub_form_title",
                            "sub_forms.title_fr as sub_form_title_fr",
                            "assets.name as assets_name",
                            "assets.asset_number"
                        )
                        ->groupby("remediation_plans.id");
            if (Auth::user()->role == 2) {
               $remediation_plans =  $query->having("remediation_plans.client_id", Auth::user()->client_id)->get();
            }else{
                $remediation_plans =  $query->having("remediation_plans.person_in_charge", Auth::user()->id)->get();
            }                    
              
            $eval_ratings = DB::table('evaluation_rating')->select("id", "assessment", "rating")->get();
            

            $form_info    = DB::table("sub_forms")
                            ->join("assets", "assets.id", "sub_forms.asset_id")
                            ->where("sub_forms.id", $remediation_plans[0]->sub_form_id)
                            ->select(
                                "sub_forms.title as form_title", 
                                "sub_forms.title_fr as form_title_fr", 
                                "assets.name",
                                "assets.asset_number",
                            )
            ->get();

            $data = compact("eval_ratings", "remediation_plans",  "form_info",  "users" );
            return view("remediation.single_remediation", $data);

        } catch(\Exception $ex){
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function single_remediation($sub_form_id){
        try {
            $users = User::where('client_id', Auth::user()->client_id)->get();
            if(Auth::user()->role == 2){
                $remediation_plans  = DB::table("remediation_plans")
                        ->join("group_questions", "group_questions.id", "remediation_plans.control_id")
                        ->join("group_section", "group_section.id", "group_questions.section_id")
                        ->join("audit_questions_groups", "audit_questions_groups.id", "group_section.group_id")
                        ->join("user_responses", "user_responses.question_id", "group_questions.id") 
                        ->join("sub_forms","sub_forms.id", "remediation_plans.sub_form_id")
                        ->leftjoin("assets", "assets.id", "sub_forms.asset_id")
                        ->join("evaluation_rating", "evaluation_rating.id", "user_responses.rating") 
                        ->select(
                            "remediation_plans.*", 
                            "audit_questions_groups.group_name", 
                            "audit_questions_groups.group_name_fr", 
                            "group_questions.question_short as question_short",
                            "group_questions.question_short_fr as question_short_fr",
                            "group_questions.question as question",
                            "group_questions.question_fr as question_fr",
                            "group_questions.control_id as control_id",
                            "group_questions.id as q_id",
                            "group_questions.dropdown_value_from",
                            "group_questions.type",
                            "user_responses.rating",
                            "user_responses.question_response",
                            "user_responses.admin_comment",
                            "evaluation_rating.rating",
                            "evaluation_rating.color",
                            "evaluation_rating.text_color",
                            "sub_forms.id as sub_form_id",
                            "sub_forms.title as sub_form_title",
                            "sub_forms.title_fr as sub_form_title_fr",
                            "sub_forms.item_type as type",
                            "sub_forms.other_id as other_name",
                            "sub_forms.other_number as other_number",
                            "assets.name as assets_name",
                            "assets.asset_number as assets_number"

                            // "assets.name as assets_name",
                            // "assets.asset_number",
                            // "assets.business_unit",
                        )
                        ->where('sub_forms.id', $sub_form_id)
                        ->where('user_responses.sub_form_id', $sub_form_id)
                ->get();
                // dd($remediation_plans);
                foreach($remediation_plans as $plan){
                    if ($plan->type != "others") {
                        $sub_form                   = SubForm::find($plan->sub_form_id);
                        $asset                      =  DB::table('assets')->find($sub_form->asset_id);
                        $plan->business_unit        = $asset->asset_number;
                        $plan->assets_name          = $asset->name;
                    }
                }
                // print("<pre>");print_r($remediation_plans); exit;
            }else{
                $remediation_plans  = DB::table("remediation_plans")
                        ->join("group_questions", "group_questions.id", "remediation_plans.control_id")
                        ->join("group_section", "group_section.id", "group_questions.section_id")
                        ->join("audit_questions_groups", "audit_questions_groups.id", "group_section.group_id")
                        ->join("user_responses", "user_responses.question_id", "group_questions.id") 
                        ->join("sub_forms","sub_forms.id", "remediation_plans.sub_form_id")
                        ->leftjoin("assets", "assets.id", "sub_forms.asset_id")
                        ->join("evaluation_rating", "evaluation_rating.id", "user_responses.rating") 
                        ->select(
                            "remediation_plans.*", 
                            "audit_questions_groups.group_name", 
                            "audit_questions_groups.group_name_fr",
                            "group_questions.question_short as question_short",
                            "group_questions.question_short_fr as question_short_fr",
                            "group_questions.question as question",
                            "group_questions.question_fr as question_fr",
                            "group_questions.control_id as control_id",
                            "group_questions.id as q_id",
                            "group_questions.dropdown_value_from",
                            "group_questions.type",
                            "user_responses.rating",
                            "user_responses.question_response",
                            "user_responses.admin_comment",
                            "evaluation_rating.rating",
                            "evaluation_rating.color",
                            "evaluation_rating.text_color",
                            "sub_forms.id as sub_form_id",
                            "sub_forms.title as sub_form_title",
                            "sub_forms.title_fr as sub_form_title_fr",
                            "sub_forms.item_type as type",
                            "sub_forms.other_id as other_name",
                            "sub_forms.other_number as other_number",
                            "assets.name as assets_name",
                            "assets.asset_number as assets_number"
                        )
                        ->where('sub_forms.id', $sub_form_id)
                        ->where('user_responses.sub_form_id', $sub_form_id)
                        ->where('remediation_plans.person_in_charge', Auth::user()->id)

                ->get();  
                foreach($remediation_plans as $plan){
                    if ($plan->type != "others") {
                        $sub_form                   = SubForm::find($plan->sub_form_id);
                        $asset                      = DB::table('assets')->find($sub_form->asset_id);
                        $plan->business_unit        = $asset->asset_number;
                        $plan->assets_name          = $asset->name;
                    }
                }
            }
            

            foreach ($remediation_plans as $question) {
                if ($question->type == "dc") {
                    $dynmc_values_dropdown = [];
                    switch ($question->dropdown_value_from){
                        case '1':
                            $dynmc_values_dropdown = DB::table("assets_data_elements")->where('id', $question->question_response)->select('name')->first();
                            break;
                        case '2':
                            $dynmc_values_dropdown = DB::table("assets")->where('id', $question->question_response)->select('name')->first();
                            break;
                        case '3':
                            $dynmc_values_dropdown = DB::table("countries")->where('id', $question->question_response)->select('country_name AS name')->first();
                            break;
                        case '4':
                            $dynmc_values_dropdown = DB::table("data_classifications")->where('id', $question->question_response)->select('classification_name_en AS name')->first();
                            break;
                        case '5':
                            $dynmc_values_dropdown = DB::table("impact")->where('id', $question->question_response)->select('impact_name_en AS name')->first();
                            break;
                        case '6':
                            $dynmc_values_dropdown = DB::table("asset_tier_matrix")->where('id', $question->question_response)->select('tier_value AS name')->first();
                            break;
                    }
                $question->question_response = $dynmc_values_dropdown->name;
                }
            }

            $eval_ratings = DB::table('evaluation_rating')->select("id", "assessment", "rating", "color", "text_color")->where('owner_id', Auth::user()->client_id)->get();

            $form_info    = DB::table("sub_forms")
                            ->join("assets", "assets.id", "sub_forms.asset_id")
                            ->where("sub_forms.id", $remediation_plans[0]->sub_form_id)
                            ->select(
                                "sub_forms.title as form_title", 
                                "sub_forms.title_fr as form_title_fr", 
                                "assets.name",
                                "assets.asset_number",
                            )
            ->get();

            $data = compact("eval_ratings", "remediation_plans",  "form_info",  "users" );
            return view("remediation.single_remediation", $data);

        } catch(\Exception $ex){
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function update_remediation_details(Request $request ,$id){
        try {
            $name = $request['name'];
            $remediation          = RemediationPlan::find($id);
            $remediation->$name   = $request->val;
            $remediation->save();
            return response()->json(["status"   => true,"message"  => "Remediation Successfully Updated"],200);
        } catch(\Exception $ex){
            return response()->json(["status"   => false, "error"  => $ex->getMessage()],200);
        }
    }

    public function add_new_remediation_plan($sub_form_id){
        try {
            if(!SubForm::where('id', $sub_form_id)->whereNotNull('asset_id')->count()){
                $asset = SubForm::where("sub_forms.id", $sub_form_id)->select("title", "title_fr", "item_type as type", "other_number as asset_number", "other_id as name")->first();
            }
            else{
                $asset = DB::table('assets')->join('sub_forms', "sub_forms.asset_id", "assets.id")->select('assets.*', 'sub_forms.*', 'sub_forms.item_type as type' )->where("sub_forms.id", $sub_form_id)->first();
            }
            return view("remediation.add_remediation", compact('asset'));
        } 
        catch(\Exception $ex){

            return redirect()->back()->with('msg', $ex->getMessage());

        }
    }

    public function remediation_control($sub_form_id){
        try {
            // $asset          = DB::table('assets')->join('sub_forms', "sub_forms.asset_id", "assets.id")->where("sub_forms.id", $sub_form_id)->first();

            $users          = User::where('client_id', Auth::user()->client_id)->get();
            $questions      = DB::table('group_questions')
                                ->join('user_responses', 'user_responses.question_id', 'group_questions.id')
                                ->join('evaluation_rating', 'evaluation_rating.id', 'user_responses.rating')
                                ->where("user_responses.sub_form_id", $sub_form_id)
                                ->whereIn('user_responses.rating', [3,4])
                                ->select("group_questions.type", "group_questions.dropdown_value_from", "group_questions.id as q_id", "group_questions.question_short", "group_questions.question_short_fr", "user_responses.sub_form_id", "user_responses.sub_form_id", "user_responses.question_response", "user_responses.admin_comment", "evaluation_rating.rating")
                                ->get();
            $count = 0;
            foreach ($questions as $question) {
                if ($question->type == "dc") {
                    $dynmc_values_dropdown = [];
                    switch ($question->dropdown_value_from){
                        case '1':
                            $dynmc_values_dropdown = DB::table("assets_data_elements")->where('id', $question->question_response)->select('name')->first();
                            break;
                        case '2':
                            $dynmc_values_dropdown = DB::table("assets")->where('id', $question->question_response)->select('name')->first();
                            break;
                        case '3':
                            $dynmc_values_dropdown = DB::table("countries")->where('id', $question->question_response)->select('country_name AS name')->first();
                            break;
                        case '4':
                            $dynmc_values_dropdown = DB::table("data_classifications")->where('id', $question->question_response)->select('classification_name_en AS name')->first();
                            break;
                        case '5':
                            $dynmc_values_dropdown = DB::table("impact")->where('id', $question->question_response)->select('impact_name_en AS name')->first();
                            break;
                        case '6':
                            $dynmc_values_dropdown = DB::table("asset_tier_matrix")->where('id', $question->question_response)->select('tier_value AS name')->first();
                            break;
                    }
                }
                $count++;
                if ($question->type == "dc") {
                    $question->question_response = $dynmc_values_dropdown->name;
                }
                $question->remediation_user_id = $users[0]->id;
                $question->proposed_remediation = "";
                $question->client_id = Auth::user()->client_id;
            }
            $data = [
                "questions"     => $questions, 
                "users"         => $users,
                "count"         => $count
            ];
            return response()->json($data, 200);

        } catch(\Exception $ex){

            return response()->json([
                "status"   => 400,
                "message"  => $ex->getMessage()
            ],200);
        }
    }

    public function add_new_remediation_db(Request $request){
        try {

            // UserFormLink::where("sub_form_id", $request->questions[0]['sub_form_id'])
            //     ->where('is_locked', 0)
            //     ->update(["is_locked" => 1]);
            foreach ($request->questions as $question) {
                $remediation                        = new RemediationPlan;
                $remediation->sub_form_id           = $question["sub_form_id"];
                $remediation->person_in_charge      = $question["remediation_user_id"];
                $remediation->proposed_remediation  = $question["proposed_remediation"];
                $remediation->control_id            = $question["q_id"];
                $remediation->client_id             = $question["client_id"];
                $remediation->save();
            }

            return response()->json(["status"   => true,"message"  => "Remediation Successfully Added"],200);
        } catch(\Exception $ex){
            return response()->json(["status"   => false, "error"  => $ex->getMessage()],200);
        }
    }

    // No Purpose understand 
    public function get_remediation_details($id){
        try {
            return response()->json($data,200);

        } catch(\Exception $ex){

            return response()->json([
                "status"   => 400,
                "message"  => $ex->getMessage()
            ],200);
        }
    }
}
