<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\GroupSection;
use App\Question;
use App\Group;
use DB;

class Groups extends Controller
{
    public function groups_restore(){
        try {
            // Path to your SQL file
            $sqlFilePath = storage_path('group.sql');
            // dd($sqlFilePath);

            // Check if the file exists
            if (File::exists($sqlFilePath)) {
                // Read the contents of the SQL file
                $sqlQueries = File::get($sqlFilePath);

                // Execute the queries
                DB::unprepared($sqlQueries);

                return redirect('group/list')->with('msg', 'Special Question Group Restored');
            } else {
                return redirect('group/list')->with('msg', 'Restore File is missing');
            }
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    }

    public function group_backup($id){
        try {
            // Creating Form

            $old_form       = DB::table('audit_questions_groups')->where('id', $id)->first();
            // dd($old_form);

            $new_form_id    = DB::table('audit_questions_groups_backup')->updateOrInsert([
                "id"                  => $old_form->id,
                "group_name"          => $old_form->group_name,
                "group_name_fr"       => $old_form->group_name_fr,
                "created_at"          => $old_form->created_at,
                "updated_at"          => $old_form->updated_at,
            ]);

            // Group Created Successfully

            // Create Sections
            $old_sections = DB::table('group_section')->where('group_id', $old_form->id)->get();
            // dd($old_sections);

            foreach($old_sections as $old_section){
                // Create Section
                $new_section_id = DB::table('group_section_backup')->updateOrInsert([
                    "id"                => $old_section->id,
                    "section_title"     => $old_section->section_title,
                    "section_title_fr"  => $old_section->section_title_fr,
                    "group_id"          => $old_section->group_id,
                    "number"            => $old_section->number,
                    "created_at"        => $old_section->created_at,
                    "updated_at"        => $old_section->updated_at,
                ]);

                // Get Old Questions
                $old_questions = DB::table("group_questions")->where('section_id', $old_section->id)->get();
                // dd($old_questions);

                foreach($old_questions as $old_question){
                    $new_question_id = DB::table('group_questions_backup')->updateOrInsert([
                            "id"                            => $old_question->id,
                            "question"                      => $old_question->question,
                            "question_fr"                   => $old_question->question_fr,
                            "question_short"                => $old_question->question_short,
                            "question_short_fr"             => $old_question->question_short_fr,
                            "question_num"                  => $old_question->question_num,
                            "question_comment"              => $old_question->question_comment,
                            "question_comment_fr"           => $old_question->question_comment_fr,
                            "additional_comments"           => $old_question->additional_comments,
                            "question_assoc_type"           => $old_question->question_assoc_type,
                            "parent_question"               => $old_question->parent_question,
                            "is_parent"                     => $old_question->is_parent,
                            "parent_q_id"                   => $old_question->parent_q_id,
                            "form_key"                      => $old_question->form_key,
                            "type"                          => $old_question->type,
                            "options"                       => $old_question->options,
                            "options_fr"                    => $old_question->options_fr,
                            "is_data_inventory_question"    => $old_question->is_data_inventory_question,   
                            "accepted_formates"             => $old_question->accepted_formates,
                            "dropdown_value_from"           => $old_question->dropdown_value_from,
                            "attachment_allow"              => $old_question->attachment_allow,
                            "not_sure_option"               => $old_question->not_sure_option,             
                            "control_id"                    => $old_question->control_id,
                            "section_id"                    => $old_question->section_id,
                            "created_at"                    => $old_question->created_at,
                            "updated_at"                    => $old_question->updated_at,
                    ]);

                    $old_options = DB::table("options_link")->where('question_id', $old_question->id)->get();
                    // dd($old_options);

                    ////option link
                    foreach($old_options as $old_option){
                        DB::table('options_link_backup')->updateOrInsert([
                            'id'            => $old_option->id,
                            'option_en'     => $old_option->option_en,
                            'option_fr'     => $old_option->option_fr,
                            'question_id'   => $old_option->question_id,
                            'form_id'       => $old_option->form_id,
                            'created_at'    => $old_option->created_at,
                            'updated_at'    => $old_option->updated_at,
                        ]);
                    }
                    /////
    
                }
            }

            return redirect('group/list')->with('msg', __('Group Backup Generated Successfully'));  
        } 
        catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function group_restore($id){
        try {
            // Creating Form

            $old_form       = DB::table('audit_questions_groups_backup')->where('id', $id)->first();
            // dd($old_form);

            $new_form = DB::table('audit_questions_groups')->updateOrInsert(
                ['id' => $old_form->id], // Check if a record with this ID exists
                [
                    "group_name"          => $old_form->group_name,
                    "group_name_fr"       => $old_form->group_name_fr,
                    "created_at"          => $old_form->created_at,
                    "updated_at"          => $old_form->updated_at,
                ]
            );
            // Group Created Successfully

            // Delete Section
            $new_sec = DB::table('group_section')->where('group_id', $old_form->id)->get();
            DB::table('group_section')->where('group_id', $old_form->id)->delete();
            foreach($new_sec as $new_se){
                DB::table("group_questions")->where('section_id', $new_se->id)->delete();
            }
            // Create Sections
            $old_sections = DB::table('group_section_backup')->where('group_id', $old_form->id)->get();
            // dd($old_sections);

            foreach($old_sections as $old_section){
                // Create Section
                $new_section_id = DB::table('group_section')->updateOrInsert(
                    ["id" => $old_section->id],
                    [
                        "section_title"     => $old_section->section_title,
                        "section_title_fr"  => $old_section->section_title_fr,
                        "group_id"          => $old_section->group_id,
                        "number"            => $old_section->number,
                        "created_at"        => $old_section->created_at,
                        "updated_at"        => $old_section->updated_at,
                    ]
                );

                // Get Old Questions
                $old_questions = DB::table("group_questions_backup")->where('section_id', $old_section->id)->get();
                // dd($old_questions);

                foreach($old_questions as $old_question){
                    $new_question_id = DB::table('group_questions')->updateOrInsert(
                        ["id" => $old_question->id],
                        [
                                "question"                      => $old_question->question,
                                "question_fr"                   => $old_question->question_fr,
                                "question_short"                => $old_question->question_short,
                                "question_short_fr"             => $old_question->question_short_fr,
                                "question_num"                  => $old_question->question_num,
                                "question_comment"              => $old_question->question_comment,
                                "question_comment_fr"           => $old_question->question_comment_fr,
                                "additional_comments"           => $old_question->additional_comments,
                                "question_assoc_type"           => $old_question->question_assoc_type,
                                "parent_question"               => $old_question->parent_question,
                                "is_parent"                     => $old_question->is_parent,
                                "parent_q_id"                   => $old_question->parent_q_id,
                                "form_key"                      => $old_question->form_key,
                                "type"                          => $old_question->type,
                                "options"                       => $old_question->options,
                                "options_fr"                    => $old_question->options_fr,
                                "is_data_inventory_question"    => $old_question->is_data_inventory_question,   
                                "accepted_formates"             => $old_question->accepted_formates,
                                "dropdown_value_from"           => $old_question->dropdown_value_from,
                                "attachment_allow"              => $old_question->attachment_allow,
                                "not_sure_option"               => $old_question->not_sure_option,             
                                "control_id"                    => $old_question->control_id,
                                "section_id"                    => $old_question->section_id,
                                "created_at"                    => $old_question->created_at,
                                "updated_at"                    => $old_question->updated_at,
                        ]
                    );

                    $old_options = DB::table("options_link_backup")->where('question_id', $old_question->id)->get();
                    // dd($old_options);

                    ////option link
                    foreach($old_options as $old_option){
                        DB::table('options_link')->updateOrInsert(
                            ['id'  => $old_option->id],
                            [
                                'option_en'     => $old_option->option_en,
                                'option_fr'     => $old_option->option_fr,
                                'question_id'   => $old_option->question_id,
                                'form_id'       => $old_option->form_id,
                                'created_at'    => $old_option->created_at,
                                'updated_at'    => $old_option->updated_at,
                            ]
                        );
                    }
                    /////
                }
                
            }
            
            return redirect('group/list')->with('msg', __('Group Restored Successfully'));  
        } 
        catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }

    public function Show_backup_groups()
    {
        $forms_info = DB::table('audit_questions_groups_backup')->orderBy('created_at', 'desc')->get();
        // dd($forms_info);
        return view('forms.audits.backup_list', ['user_type' => 'admin', 'forms_list' => $forms_info]);

    }

    // ----------------- GROUPS CRUD --------------------- //
    
    public function list(){
        try {
            $groups = Group::orderBy('id', 'desc')->get();
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

            $groupName = $request->input('group_name');
            $groupNameFr = $request->input('group_name_fr');

            $group = Group::where('group_name', $groupName)
                ->orWhere('group_name_fr', $groupNameFr)
                ->get();
            
            if($group->isNotEmpty()){
                return redirect()->back()->with('message', 'Group Name Already Exist');
            }
    
            $group = new Group;
            $group->insert([
                'group_name'    => $request->group_name,
                'group_name_fr' => $request->group_name_fr 
            ]);
            return redirect('group/list')->with('msg', 'New Group Successfully Added');
    
        } catch (\Exception $ex) {
            return redirect()->back()->with('message', $ex->getMessage());
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

            $groupName = $request->input('group_name');
            $groupNameFr = $request->input('group_name_fr');
            $idToExclude = $id;

            $group = Group::where(function ($query) use ($groupName, $groupNameFr) {
                $query->where('group_name', $groupName)
                      ->orWhere('group_name_fr', $groupNameFr);
            })
            ->whereNotIn('id', [$idToExclude])
            ->get();
            
            if($group->isNotEmpty()){
                return redirect()->back()->with('msg', 'Group Name Already Exist');
            }

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

                    ////option link
                    if(isset($question->options) && isset($question->options_fr) && $question->type !="qa"){
                        $opt = explode(", ", $question->options);
                        $opt_fr = explode(", ", $question->options_fr);
                        foreach($opt as $index => $op){
                            DB::table('options_link')->insert([
                                'option_en'     => $op,
                                'option_fr'     => $opt_fr[$index],
                                'question_id'   => $question->id,
                                'form_id'       => $question->section_id,
                            ]);
                        }
                    }
                    /////
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
            $dublicate=GroupSection::where('section_title', strtoupper($request->section_title))->where('group_id', $request->group_id)->count();
            // dd($dublicate);
            if($dublicate > 0){
                return response()->json([
                    'status' => 200,
                    'success' => "Section Name Already Exist",
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
                'question_title_short'  => 'required',
                'question_title_short_fr'=> 'required',
                'question_options'      => 'required_if:type,mc|min:1',
                'question_options_fr'   => 'required_if:type,mc|min:1',
                'question_options'      => 'required_if:type,sc|min:1',
                'question_options_fr'   => 'required_if:type,sc|min:1',
                'section_id'            => 'required',
                'control_id'            => 'required',
                ],[
                'question_title.required'           => __('English Question Can Not Be Empty.'),
                'question_title_fr.required'        => __('French Question Can Not Be Empty.'),
                'question_title_short.required'     => __('English Short Question Can Not Be Empty.'),
                'question_title_short_fr.required'  => __('French Short Question Can Not Be Empty.'),
                'question_options.required_if'      => __('English Question Options Can Not Be Empty.'),
                'question_options_fr.required_if'   => __('French Question Options Can Not Be Empty.'),
                'question_options.min'              => __('Please provide at least one English option to proceed'),
                'question_options_fr.min'           => __('Please provide at least one French option to proceed'),
                'q_type.required'                   => __('No Question is selected.'),
                'type.required'                     => __('Please select question type.'),
                'control_id.required'               => __('Control ID Can Not Be Empty.')
            ]);

            if($request->add_attachments_box){
                // dd("attachment true");
                if(!$request->attachment){
                    return response()->json([
                        'status'    => false,
                        'error'     => 'Please Select at least one Attachment Option',
                    ], 200);
                }
            }

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

            if(isset($request->question_options) && isset($request->question_options_fr)){
                $opt = explode(",", $request->question_options);
                $opt_fr = explode(",", $request->question_options_fr);
                $count = count($opt);
                $count_fr = count($opt_fr);
                if($count != $count_fr){
                    return response()->json([
                        'status'    => false,
                        'error'     => 'English & French Options Count Doesn`t Match.',
                    ], 200);
                }
            }

            $allow_attach = 0;
            if($request->add_attachments_box) $allow_attach = 1;
            
            $section_number         = GroupSection::find( $request->section_id);
            $question_number        = Question::where('section_id', $request->section_id)->orderBy('question_num', 'DESC')->pluck('question_num')->first();
            
            if($question_number){
                $question_number = $question_number + 1/100;
            }
            else{
                $question_number = 1/100;
                $question_number = $section_number->number + $question_number;
            }
            // $question_number        = ($question_number + 1)/100;
            // $final_question_number  = ($section_number->number + $question_number);
            // $final_question_number  = number_format($final_question_number, 2);
            $final_question_number  = number_format($question_number, 2);
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

            ////option link
            if(isset($question->options) && isset($question->options_fr) && $question->type !="qa"){
                $opt = explode(", ", $question->options);
                $opt_fr = explode(", ", $question->options_fr);
                foreach($opt as $index => $op){
                    DB::table('options_link')->insert([
                        'option_en'     => $op,
                        'option_fr'     => $opt_fr[$index],
                        'question_id'   => $question->id,
                        'form_id'       => $question->section_id,
                    ]);
                }
            }
            /////

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
                case 'edit_con_id':
                    $group = GroupSection::find($question->section_id);
                    $check = DB::table('group_questions')
                    ->join('group_section', 'group_section.id', 'group_questions.section_id')
                    ->where('group_section.group_id', $group->group_id)
                    ->whereNotIn('group_questions.id', [$request->q_id])
                    ->pluck('control_id')
                    ->toArray();
                    if (in_array($request->val, $check)) {
                        return response()->json([
                            'status'  => true,
                            'code'    => 200,
                            'success' => "Control Id Exists",
                        ], 200);
                    }
                    // dd($check);
                    $question->control_id = $request->val;
                    break;
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
                case 'edit_en_o':
                    $question->options = str_replace(",", ", ", implode(",",array_map('trim', explode(',', $request->val))));
                    break;
                case 'edit_fr_o':
                    $question->options_fr = str_replace(",", ", ", implode(",",array_map('trim', explode(',', $request->val))));
                    break;
                default:
                    break;
            }
            
            if($request->name == "edit_en_o" || $request->name == "edit_fr_o"){
                $opt = explode(", ", $question->options);
                $opt_fr = explode(", ", $question->options_fr);
                $count = count($opt);
                $count_fr = count($opt_fr);
                if($count != $count_fr){
                    return response()->json([
                        'status' => true,
                        'code' => 200,
                        'success' => "English & French Option Count Doesn`t Match.",
                    ], 200);
                }
            }
            $question->save();
            ////option link
            if($request->name == "edit_en_o"){
                $opt = explode(", ", $question->options);
                $opt_fr = explode(", ", $question->options_fr);
                foreach($opt_fr as $index => $op){
                    DB::table('options_link')->where('question_id', $question->id)->where('option_fr', $op)->update([
                        'option_en'     => $opt[$index],
                    ]);
                }
            }
            if($request->name == "edit_fr_o"){
                $opt = explode(", ", $question->options);
                $opt_fr = explode(", ", $question->options_fr);
                foreach($opt as $index => $op){
                    DB::table('options_link')->where('question_id', $question->id)->where('option_en', $op)->update([
                        'option_fr'     => $opt_fr[$index],
                    ]);
                }
            }
            /////
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
