<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\GroupSection;
use App\Question;
use App\Group;

class Groups extends Controller
{

    // ----------------- GROUPS CRUD --------------------- //
    
    public function list(){
        try {
            $groups = Group::get();
            return view("groups.group_list", compact('groups'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function add(){
        try {
            return view("groups.add");
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function save(Request $request){
        try {
            $validated = $request->validate([
                'group_name'    => 'required|max:255',
                'group_name_fr' => 'required|max:255'
            ]);
    
            $group = new Group;
            $group->insert([
                'group_name'    => $request->group_name,
                'group_name_fr' => $request->group_name_fr 
            ]);
            return redirect('group/list')->with('msg', 'New Group Successfully Added');
    
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function edit($id){
        try {
            $group = Group::find($id);
            return view("groups.edit", compact('group'));
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function update($id, Request $request){
        try {
            $validated = $request->validate([
                'group_name'    => 'required|max:255',
                'group_name_fr' => 'required|max:255'
            ]);
            $group = Group::find($id);
            $group->group_name      = $request->group_name;
            $group->group_name_fr   = $request->group_name_fr;
            $group->save();
            return redirect('group/list')->with('msg', 'New Group Successfully Updated');    
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function delete($id){
        try {
            Group::find($id)->delete();
            return redirect()->back()->with('msg', 'Group Deleted Successfully');   
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function duplicate($id){
        try {
            $old_group =  Group::with('sections', 'sections.questions')->find($id);
            $group      = new Group;
            $group_id   = $group->insertGetId([
                'group_name'    => $old_group->group_name.' - '.time(),
                'group_name_fr' => $old_group->group_name_fr.' - '.time() 
            ]);

            foreach ($old_group->sections as $old_section) {
                $section = new GroupSection;
                $section->section_title     = strtoupper($old_section->section_title);
                $section->section_title_fr  = strtoupper($old_section->section_title_fr);
                $section->group_id          = $group_id;
                $section->number            = $old_section->number;
                $section->save();
                $section_id = $section->id;
                foreach ($old_section->questions as $old_questions){
                    $question                        = new Question;
                    $question->question              = $old_questions->question;
                    $question->question_fr           = $old_questions->question_fr;
                    $question->question_short        = $old_questions->question_short;
                    $question->question_short_fr     = $old_questions->question_short_fr;
                    $question->question_num          = $old_questions->question_num;
                    $question->type                  = $old_questions->type;
                    $question->options               = $old_questions->options;
                    $question->options_fr            = $old_questions->options_fr;
                    $question->dropdown_value_from   = $old_questions->dropdown_value_from;
                    $question->control_id            = $old_questions->control_id;
                    $question->not_sure_option       = $old_questions->not_sure_option;
                    $question->attachment_allow      = $old_questions->attachment_allow;
                    $question->accepted_formates     = $old_questions->accepted_formates;
                    $question->question_comment      = $old_questions->question_comment;
                    $question->question_comment_fr   = $old_questions->question_comment_fr;
                    $question->section_id            = $section_id;
                    $question->save();
                }
            }

            return redirect()->back()->with('msg', 'Group Deleted Successfully');   
        } 
        catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    // ----------------- Groups Sections Management  ----------------- //
    
    public function add_section_to_group(Request $request){
        try {
            $validator = \Validator::make($request->all(), [
                'group_id'              => 'required',
                'section_title'         => 'required',
                'section_title_fr'      => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => null
                ], 200);
            }

            $section_number = GroupSection::where('group_id', $request->group_id)->count();
            $section_number = $section_number + 1;
            $section = new GroupSection;
            $section->section_title     = strtoupper($request->section_title);
            $section->section_title_fr  = strtoupper($request->section_title_fr);
            $section->group_id          = $request->group_id;
            $section->number            = $section_number;

            $section->save();
            return response()->json([
                'status' => true,
                'success' => "Section Successfully Added",
            ], 200);
        } catch (\Exception $ex){

            return response()->json([
                'status' => false,
                'error' => $ex->getMessage(),
            ], 200);
        }
    }

    public function update_section_to_group(Request $request){
        try {
            $validator = \Validator::make($request->all(), [
                'section_id'            => 'required',
                'section_title'         => 'required',
                'section_title_fr'      => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'status'    => false,
                    'error'     => $validator->errors()->first(),
                    'data'      => null
                ], 200);
            }
            $section                    = GroupSection::find($request->section_id);
            $section->section_title     = strtoupper($request->section_title);
            $section->section_title_fr  = strtoupper($request->section_title_fr);
            $section->save();
            return response()->json([
                'status'  => true,
                'success' => "Section Successfully Updated",
            ], 200);
        } catch (\Exception $ex){

            return response()->json([
                'status' => false,
                'error'  => $ex->getMessage(),
            ], 200);
        }
    }

    // ----------------- Groups Question Management  --------------------- //

    public function add_question($id){
        try {
            $group = Group::with('sections', 'sections.questions')->find($id);
            return view("groups.add_question_group", compact('group')); 
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }
    
    public function return_question($id){
        try {
            $group = Group::with('questions')->find($id);
            return response()->json($group, 200);
        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    public function add_question_to_group(Request $request){
        try {

            // 'control_id'            => 'required',
            // 'control_id.required'               => __('Control Id is required.'),
            $validator = \Validator::make($request->all(), [
                'type'                  => 'required',
                'question_title'        => 'required',
                'question_title_fr'     => 'required',
                'question_options'      => 'required_if:type,mc|min:1',
                'question_options_fr'   => 'required_if:type,mc|min:1',
                'question_options'      => 'required_if:type,sc|min:1',
                'question_options_fr'   => 'required_if:type,sc|min:1',
                'section_id'            => 'required',
                ],[
                'question_title.required'           => __('English Question Can Not Be Empty.'),
                'question_title_fr.required'        => __('French Question Can Not Be Empty.'),
                'question_options.required_if'      => __('English Question Options Can Not Be Empty.'),
                'question_options_fr.required_if'   => __('French Question Options Can Not Be Empty.'),
                'question_options.min'              => __('Please provide at least one English option to proceed'),
                'question_options_fr.min'           => __('Please provide at least one French option to proceed'),
                'q_type.required'                   => __('No Question is selected.'),
                'type.required'                     => __('Please select question type.')
            ]);

            if('control_id'){}

            if (Question::where('section_id', $request->section_id)->where('control_id', $request->control_id)->count() > 0) {
                return response()->json([
                    'status'    => false,
                    'error'     => 'This  control id already assigned to an other Question',
                ], 200);
            }

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'error' => $validator->errors()->first(),
                ], 200);
            }

            $allow_attach = 0;
            if($request->add_attachments_box) $allow_attach = 1;
            
            $section_number         = GroupSection::find( $request->section_id);
            $question_number        = Question::where('section_id', $request->section_id)->count();
            $final_question_number  = ($section_number->number) . ".". ($question_number + 1);
            $question = new Question;
            $question->question              = $request->question_title;
            $question->question_fr           = $request->question_title_fr;
            $question->question_short        = $request->question_title_short;
            $question->question_short_fr     = $request->question_title_short_fr;
            $question->question_num          = $final_question_number;
            $question->type                  = $request->type;
            $question->options               = str_replace(",", ", ", implode(",",array_map('trim', explode(',', $request->question_options))));
            $question->options_fr            = str_replace(",", ", ", implode(",",array_map('trim', explode(',', $request->question_options_fr))));
            $question->section_id            = $request->section_id;
            $question->dropdown_value_from   = $request->dropdown_value_from;
            $question->not_sure_option       = $request->add_not_sure_box;
            $question->attachment_allow      = $allow_attach;
            if (is_string($request->attachment)) {
                $question->accepted_formates = $request->attachment;
            }else{
                $question->accepted_formates = json_encode($request->attachment);
            }
            if (isset($request->question_coment)){
                $question->question_comment  = $request->question_coment;
            }
            if (isset($request->question_coment_fr)){
                $question->question_comment_fr   = $request->question_coment_fr;
            }
            if ($request->has('control_id')){
                $question->control_id   = $request->control_id;
            }
            $question->save();
            return response()->json([
                'status' => true,
                'success' => "Question Successfully Added",
            ], 200);
        } catch (\Exception $ex){

            return response()->json([
                'status' => false,
                'error' => $ex->getMessage(),
            ], 200);
        }
    }

    public function delete_question($id){
        try {
            Question::find($id)->delete();
            return redirect()->back()->with('msg', 'Question Deleted Successfully');   
        } catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function group_list(){
        try {

            $groups = Group::get();
            return response()->json($groups, 200);

        } catch (\Exception $ex) {
            return response()->json($ex->getMessage(), 400);
        }
    }

    public function update_question(Request $request){
        try {
            $question = Question::find($request->q_id);
            switch ($request->name) {
                case 'edit_en_q':
                    $question->question = $request->val;
                    break;
                case 'edit_fr_q':
                    $question->question_fr = $request->val;
                    break;
                case 'edit_en_c':
                    $question->question_comment = $request->val;
                    break;
                case 'edit_fr_c':
                    $question->question_comment_fr = $request->val;
                    break;
                default:
                    break;
            }
            $question->save();
            return response()->json([
                'status' => true,
                'success' => "Successfully Updated",
            ], 200);

        } catch (\Exception $ex) {
            return response()->json([
                'status' => false,
                'error' => $ex->getMessage(),
            ], 200);
        }
    }
}
