<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Validation\Validator;

class BackupRestoreController extends Controller
{
    public function view_import(){
        // $forms_info = DB::table('forms_backup')->where('type', 'assessment')->orderBy('date_created', 'desc')->get();
        // dd($forms_info);
        return view('forms.backup_list');

    }
    public function all_backup(){
        try {
            // Fetch data from the tables
            $data = [
                'import_type'           =>"All",
                'forms'                 =>DB::table('forms')->get()->toArray(),
                'admin_form_sections'   =>DB::table('admin_form_sections')->get()->toArray(),
                'questions'             =>DB::table('questions')->get()->toArray(),
                'form_questions'        =>DB::table('form_questions')->get()->toArray(),
                'options_link'          =>DB::table('options_link')->get()->toArray(),
                'audit_questions_groups' =>DB::table('audit_questions_groups')->get()->toArray(),
                'group_section'         =>DB::table('group_section')->get()->toArray(),
                'group_questions'       =>DB::table('group_questions')->get()->toArray(),
            ];

            // Convert data to JSON string
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);

            // Encrypt the JSON data
            $encryptedData = Crypt::encryptString($jsonData);

            // Define the filename with a timestamp for uniqueness
            $filename = 'alldata_' . now()->format('Y_m_d_H_i_s') . '.json';

            // Store the data as a JSON file in the storage
            Storage::put($filename, $encryptedData);

            // Provide a download response for the JSON file
            return response()->download(storage_path("app/{$filename}"));
        } catch (\Exception $e) {
            // Handle errors and provide an appropriate response
            return response()->json(['alert' => 'Data export failed: ' . $e->getMessage()], 500);
        }
    }
    public function form_backup($id){
        // dd("ok");
        try {

            $name = DB::table('forms')->where('id', $id)->pluck('title')->first();

            $data = [
                'import_type'           =>"Form",
                'forms'                 =>DB::table('forms')->where('id', $id)->get()->toArray(),
                'admin_form_sections'   =>DB::table('admin_form_sections')->where('form_id', $id)->get()->toArray(),
                'questions'             =>DB::table('questions')->where('form_id', $id)->get()->toArray(),
                'form_questions'        =>DB::table('form_questions')->where('form_id', $id)->get()->toArray(),
                'options_link'          =>DB::table('options_link')->where('form_id', $id)->get()->toArray(),
            ];
            // dd($data);

            // Convert data to JSON string
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);

            // Encrypt the JSON data
            $encryptedData = Crypt::encryptString($jsonData);
    
            $filename = $name . '_' . now()->format('Y_m_d_H_i_s') . '.json';
            Storage::put($filename, $encryptedData);
    
            return response()->download(storage_path("app/{$filename}"));
        } 
        catch (\Exception $ex) {
            return redirect()->back()->with('alert', $ex->getMessage());
        }
    }
    public function group_backup($id){
        // dd("ok");
        try {
            $name = DB::table('audit_questions_groups')->where('id', $id)->pluck('group_name')->first();

            // Fetch all sections for the group
            $sections = DB::table('group_section')->where('group_id', $id)->get();

            // Fetch all questions for the sections in a single query
            $sectionIds = $sections->pluck('id')->toArray();
            $questions = DB::table('group_questions')->whereIn('section_id', $sectionIds)->get();
            

            // Fetch data from the tables
            $data = [
                'import_type'            =>"Group",
                'audit_questions_groups' =>DB::table('audit_questions_groups')->where('id', $id)->get()->toArray(),
                'group_section'          =>DB::table('group_section')->where('group_id', $id)->get()->toArray(),
                'group_questions'        =>$questions->toArray(),
                'options_link'           =>DB::table('options_link')->where('group_id', $id)->get()->toArray(),
            ];

            // Convert data to JSON string
            $jsonData = json_encode($data, JSON_PRETTY_PRINT);

            // Encrypt the JSON data
            $encryptedData = Crypt::encryptString($jsonData);

            // Define the filename with a timestamp for uniqueness
            $filename = $name . '_' . now()->format('Y_m_d_H_i_s') . '.json';
            // dd($questions, $name, $data, $filename);
            // Store the data as a JSON file in the storage
            Storage::put($filename, $encryptedData);

            // Provide a download response for the JSON file
            return response()->download(storage_path("app/{$filename}"));
        } 
        catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }
    public function import_json(Request $request){
        // dd($request->all());
        try {
            $request->validate([
                'import_file' => 'required|file',
            ]);
    
            $file = $request->file('import_file');
            $originalExtension = $file->getClientOriginalExtension();
            if($originalExtension != 'json'){
                return redirect()->back()->with('alert', 'Please Upload Json File');
            }
            // dd($originalExtension);

            // Decrypt the content of the uploaded file
            $decryptedContent = Crypt::decryptString(file_get_contents($file));
            // Decode decrypted JSON string into an array
            $data = json_decode($decryptedContent, true);
            // dd($data);

            // $data = json_decode(file_get_contents($file), true);
            // dd($data);
            if ($data['import_type']=="Form") {
                // dd($data['forms']);
                // Update or insert 'forms' data
                if (isset($data['forms'])) {
                    foreach ($data['forms'] as $form) {
                        $subforms = DB::table('sub_forms')->where('parent_form_id', $form['id'])->count();
                        if($subforms > 0){
                            return redirect()->back()->with('msg', "Import Failed: This Form is already used by organization");
                        }
                        $form_count = DB::table('forms')->where('id', $form['id'])->count();
                        // Delete Forms
                        if($form_count > 0){
                            DB::table('admin_form_sections')->where('form_id', $form['id'])->delete();
                            DB::table('questions')->where('form_id', $form['id'])->delete();
                            DB::table('form_questions')->where('form_id', $form['id'])->delete();
                            DB::table('options_link')->where('form_id', $form['id'])->delete();
                        }

                        // Assuming 'id' is the unique identifier for the 'forms' table
                        DB::table('forms')->updateOrInsert(['id' => $form['id']], $form);
                    }
                }
                // Update or insert 'admin_form_sections' data
                if (isset($data['admin_form_sections'])) {
                    foreach ($data['admin_form_sections'] as $admin_form_section) {
                        // Assuming 'id' is the unique identifier for the 'forms' table
                        // dd("ok", $admin_form_section);
                        DB::table('admin_form_sections')->updateOrInsert(['id' => $admin_form_section['id']], $admin_form_section);
                    }
                }

                // Update or insert 'questions' data
                if (isset($data['questions'])) {
                    foreach ($data['questions'] as $question) {
                        DB::table('questions')->updateOrInsert(['id' => $question['id']], $question);
                    }
                }

                // Update or insert 'form_questions' data
                if (isset($data['form_questions'])) {
                    foreach ($data['form_questions'] as $form_question) {
                        DB::table('form_questions')->updateOrInsert(['fq_id' => $form_question['fq_id']], $form_question);
                    }
                }

                // Update or insert 'options_link' data
                if (isset($data['options_link'])) {
                    foreach ($data['options_link'] as $options_link) {
                        DB::table('options_link')->updateOrInsert(['id' => $options_link['id']], $options_link);
                    }
                }
                // Insert other tables' data here
    
                return redirect('Forms/AdminFormsList')->with('message', __('Form Restored Successfully'));
            }
            if ($data['import_type']=="Group") {
                // dd("ok", $data);
                // Update or insert 'forms' data
                if (isset($data['audit_questions_groups'])) {
                    foreach ($data['audit_questions_groups'] as $audit_questions_group) {
                        $form_id = DB::table('forms')->where('group_id', $audit_questions_group['id'])->pluck('id')->first();
                        $subforms = DB::table('sub_forms')->where('parent_form_id', $form_id)->count();
                        if($subforms > 0){
                            return redirect()->back()->with('msg', "Import Failed: This Group is already used by organization");
                        }
                        // dd($audit_questions_group);
                        $group_count = DB::table('audit_questions_groups')->where('id', $audit_questions_group['id'])->count();
                        
                        // Fetch all sections for the group
                        $sections = DB::table('group_section')->where('group_id', $audit_questions_group['id'])->get();

                        // Fetch all questions for the sections in a single query
                        $sectionIds = $sections->pluck('id')->toArray();

                        // Delete Forms
                        if($group_count > 0){
                            DB::table('group_section')->where('group_id', $audit_questions_group['id'])->delete();
                            DB::table('group_questions')->whereIn('section_id', $sectionIds)->delete();
                            DB::table('options_link')->where('group_id', $audit_questions_group['id'])->delete();
                        }
                        // dd($audit_questions_group);
                        // Assuming 'id' is the unique identifier for the 'forms' table
                        DB::table('audit_questions_groups')->updateOrInsert(['id' => $audit_questions_group['id']], $audit_questions_group);
                    }
                }
                // Update or insert 'admin_form_sections' data
                if (isset($data['group_section'])) {
                    foreach ($data['group_section'] as $group_sec) {
                        // Assuming 'id' is the unique identifier for the 'forms' table
                        // dd("ok", $admin_form_section);
                        DB::table('group_section')->updateOrInsert(['id' => $group_sec['id']], $group_sec);
                    }
                }

                // Update or insert 'questions' data
                if (isset($data['group_questions'])) {
                    foreach ($data['group_questions'] as $group_question) {
                        DB::table('group_questions')->updateOrInsert(['id' => $group_question['id']], $group_question);
                    }
                }

                // Update or insert 'options_link' data
                if (isset($data['options_link'])) {
                    foreach ($data['options_link'] as $options_link) {
                        DB::table('options_link')->updateOrInsert(['id' => $options_link['id']], $options_link);
                    }
                }
                // Insert other tables' data here
    
                return redirect('group/list')->with('msg', __('Group Restored Successfully'));  
            }
            if ($data['import_type']=="All") {
                // dd($data['forms']);
                $forms_imported = 0;
                $groups_imported = 0;
                // Update or insert 'forms' data
                if (isset($data['forms'])) {
                    foreach ($data['forms'] as $form) {
                        $form_count = DB::table('forms')->where('id', $form['id'])->count();
                        // Delete Forms
                        if($form_count > 0){
                            DB::table('admin_form_sections')->where('form_id', $form['id'])->delete();
                            DB::table('questions')->where('form_id', $form['id'])->delete();
                            DB::table('form_questions')->where('form_id', $form['id'])->delete();
                            DB::table('options_link')->where('form_id', $form['id'])->delete();
                        }

                        // Assuming 'id' is the unique identifier for the 'forms' table
                        DB::table('forms')->updateOrInsert(['id' => $form['id']], $form);
                        $forms_imported++;
                    }
                }
                // Update or insert 'admin_form_sections' data
                if (isset($data['admin_form_sections'])) {
                    foreach ($data['admin_form_sections'] as $admin_form_section) {
                        // Assuming 'id' is the unique identifier for the 'forms' table
                        // dd("ok", $admin_form_section);
                        DB::table('admin_form_sections')->updateOrInsert(['id' => $admin_form_section['id']], $admin_form_section);
                    }
                }

                // Update or insert 'questions' data
                if (isset($data['questions'])) {
                    foreach ($data['questions'] as $question) {
                        DB::table('questions')->updateOrInsert(['id' => $question['id']], $question);
                    }
                }

                // Update or insert 'form_questions' data
                if (isset($data['form_questions'])) {
                    foreach ($data['form_questions'] as $form_question) {
                        DB::table('form_questions')->updateOrInsert(['fq_id' => $form_question['fq_id']], $form_question);
                    }
                }

                // Update or insert 'options_link' data
                if (isset($data['options_link'])) {
                    foreach ($data['options_link'] as $options_link) {
                        DB::table('options_link')->updateOrInsert(['id' => $options_link['id']], $options_link);
                    }
                }
                
                // Update or insert Groups
                if (isset($data['audit_questions_groups'])) {
                    foreach ($data['audit_questions_groups'] as $audit_questions_group) {
                        // dd($audit_questions_group);
                        $group_count = DB::table('audit_questions_groups')->where('id', $audit_questions_group['id'])->count();
                        
                        // Fetch all sections for the group
                        $sections = DB::table('group_section')->where('group_id', $audit_questions_group['id'])->get();

                        // Fetch all questions for the sections in a single query
                        $sectionIds = $sections->pluck('id')->toArray();

                        // Delete Forms
                        if($group_count > 0){
                            DB::table('group_section')->where('group_id', $audit_questions_group['id'])->delete();
                            DB::table('group_questions')->whereIn('section_id', $sectionIds)->delete();
                            DB::table('options_link')->where('group_id', $audit_questions_group['id'])->delete();
                        }
                        // dd($audit_questions_group);
                        // Assuming 'id' is the unique identifier for the 'forms' table
                        DB::table('audit_questions_groups')->updateOrInsert(['id' => $audit_questions_group['id']], $audit_questions_group);
                        $groups_imported++;
                    }
                }
                // Update or insert 'admin_form_sections' data
                if (isset($data['group_section'])) {
                    foreach ($data['group_section'] as $group_sec) {
                        // Assuming 'id' is the unique identifier for the 'forms' table
                        // dd("ok", $admin_form_section);
                        DB::table('group_section')->updateOrInsert(['id' => $group_sec['id']], $group_sec);
                    }
                }

                // Update or insert 'questions' data
                if (isset($data['group_questions'])) {
                    foreach ($data['group_questions'] as $group_question) {
                        DB::table('group_questions')->updateOrInsert(['id' => $group_question['id']], $group_question);
                    }
                }

                // Update or insert 'options_link' data
                if (isset($data['options_link'])) {
                    foreach ($data['options_link'] as $options_link) {
                        DB::table('options_link')->updateOrInsert(['id' => $options_link['id']], $options_link);
                    }
                }

                $message = "$forms_imported Forms and $groups_imported Groups Restored successfully";
    
                return redirect('Forms/AdminFormsList')->with('message', __($message));
            }
            return redirect()->back()->with('msg', "Something is wrong!");
        } 
        catch (\Exception $ex) {
            return redirect()->back()->with('msg', $ex->getMessage());
        }
    }
}
