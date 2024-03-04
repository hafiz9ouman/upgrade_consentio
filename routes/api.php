<?php
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Schema;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Support\Facades\DB;

    Route::get('add/groups', function(){
        try {
            Schema::dropIfExists('audit_questions_groups');
            Schema::create('audit_questions_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('group_name')->nullable();
                $table->string('group_name_fr')->nullable();
                $table->timestamps();
            });
            return "Table <b>audit_questions_groups</b> Successfully Created With MUL";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/group_section', function(){
        try {
            Schema::dropIfExists('group_section');
            Schema::create('group_section', function (Blueprint $table) {
                $table->increments('id');
                $table->string('section_title')->nullable();
                $table->string('section_title_fr')->nullable();
                $table->unsignedBigInteger('group_id')->nullable();
                $table->unsignedBigInteger('number')->default(1);
                $table->timestamps();
            });
            return "Table <b>group_section</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/group_questions', function(){
        try {
            Schema::dropIfExists('group_questions');
            Schema::create('group_questions', function (Blueprint $table) {
                $table->increments('id');
                $table->text('question', 500)->nullable();
                $table->text('question_fr', 500)->nullable();
                $table->text('question_short', 500)->nullable();
                $table->text('question_short_fr', 500)->nullable();
                $table->string('question_num')->nullable();
                $table->text('question_comment', 500)->nullable();
                $table->text('question_comment_fr', 500)->nullable();
                $table->text('additional_comments', 500)->nullable();
                $table->text('question_assoc_type', 500)->nullable();
                $table->Integer('parent_question')->nullable();
                $table->boolean('is_parent')->nullable();
                $table->Integer('parent_q_id')->nullable();
                $table->string('form_key')->nullable();
                $table->string('type')->nullable();
                $table->text('options', 500)->nullable();
                $table->text('options_fr', 500)->nullable();
                $table->boolean('is_data_inventory_question')->nullable();
                $table->string('accepted_formates')->default(0);
                $table->Integer('dropdown_value_from')->nullable();
                $table->Integer('attachment_allow')->default(0);
                $table->string('not_sure_option', 50)->nullable();
                $table->string('control_id', 50)->nullable();
                $table->Integer('section_id')->nullable();
                $table->timestamps();
            });
            return "Table <b>group_questions</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/user_form_links', function(){
        try {
            Schema::dropIfExists('user_form_links');
            Schema::create('user_form_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('sub_form_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedInteger('percent_completed')->nullable();
                $table->text('form_link')->nullable();
                $table->text('form_link_id')->nullable();
                $table->integer('is_locked')->default(0);
                $table->integer('is_accessible')->default(1);
                $table->integer('curr_sec')->default(1);
                $table->integer('email_sent')->default(0);
                $table->string('user_email')->nullable();
                $table->dateTime('expiry_time')->nullable();
                $table->boolean('is_internal')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
            });

            return "Table <b>user_form_links</b> Successfully Created";
        } catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/user_responses', function(){
        try {
            Schema::dropIfExists('user_responses');
            Schema::create('user_responses', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('user_form_id')->nullable();
                $table->unsignedBigInteger('form_id')->nullable();
                $table->unsignedBigInteger('sub_form_id')->nullable();
                $table->unsignedBigInteger('question_id')->nullable();
                $table->unsignedBigInteger('rating')->default(0);
                $table->integer('custom_case')->nullable();
                $table->string('question_key')->nullable();
                $table->boolean('is_internal')->default(0);
                $table->string('user_email')->default(0);
                $table->string('user_id')->default(0);
                $table->string('type')->nullable();
                $table->text('q_type', 50)->nullable();
                $table->string('question_response')->nullable();
                $table->string('additional_comment')->nullable();
                $table->string('additional_info')->nullable();
                $table->string('admin_comment')->nullable();
                $table->string('attachment')->nullable();
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
            });
            $query = "ALTER TABLE `user_responses` ADD UNIQUE KEY `user_form_id` (`user_form_id`,`question_id`,`user_id`, `user_email`,`sub_form_id`) USING BTREE";
            DB::select($query);

            return "Table <b>user_responses</b> Successfully Created With MUL";
        } catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/remediation_plans', function(){
        try {
            Schema::dropIfExists('remediation_plans');
            Schema::create('remediation_plans', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('sub_form_id')->nullable();
                $table->unsignedBigInteger('control_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('person_in_charge')->nullable();
                $table->unsignedBigInteger('post_remediation_rating')->nullable();
                $table->text('proposed_remediation')->nullable();
                $table->text('completed_actions')->nullable();
                $table->date('eta')->nullable();
                $table->string('status')->default(0);
                $table->timestamps();
            });
            return "Table <b>remediation_plans</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/assets', function(){
        try {
            Schema::dropIfExists('assets');
            Schema::create('assets', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('asset_type')->nullable();
                $table->string('hosting_type')->nullable();
                $table->string('hosting_provider')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('lat')->nullable();
                $table->string('lng')->nullable();
                $table->unsignedBigInteger('impact_id')->nullable();
                $table->unsignedBigInteger('data_classification_id')->nullable();
                $table->string('tier')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('asset_number')->nullable()->comment("This will use for uniquely identification of organization asset with (org_id-this_number)");
                $table->string('it_owner')->nullable();
                $table->string('business_owner')->nullable();
                $table->string('internal_3rd_party')->nullable();
                $table->string('data_subject_volume')->nullable();
                $table->string('business_unit')->nullable();
                $table->timestamps();
            });
            return "Table <b>assets</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/matrix', function(){
        try {
            Schema::dropIfExists('asset_tier_matrix');
            Schema::create('asset_tier_matrix', function (Blueprint $table) {
                $table->increments('id');
                $table->string('impact_id')->nullable();
                $table->string('data_classification_id')->nullable();
                $table->string('tier_value')->nullable();
                $table->timestamps();
            });


            $create_asset_tier_matrix = [
                ['impact_id'=>1, 'data_classification_id'=> 1, 'tier_value'=>'tier 3'],
                ['impact_id'=>1, 'data_classification_id'=>2 , 'tier_value'=>'tier 3'],
                ['impact_id'=>1, 'data_classification_id'=>3 , 'tier_value'=>'tier 3'],
                ['impact_id'=>1, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>1, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>2, 'data_classification_id'=>1 , 'tier_value'=>'tier 3'],
                ['impact_id'=>2, 'data_classification_id'=>2 , 'tier_value'=>'tier 3'],
                ['impact_id'=>2, 'data_classification_id'=>3 , 'tier_value'=>'tier 2'],
                ['impact_id'=>2, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>2, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>3, 'data_classification_id'=>1 , 'tier_value'=>'tier 3'],
                ['impact_id'=>3, 'data_classification_id'=>2 , 'tier_value'=>'tier 3'],
                ['impact_id'=>3, 'data_classification_id'=>3 , 'tier_value'=>'tier 2'],
                ['impact_id'=>3, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>3, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>4, 'data_classification_id'=>1 , 'tier_value'=>'tier 2'],
                ['impact_id'=>4, 'data_classification_id'=>2 , 'tier_value'=>'tier 2'],
                ['impact_id'=>4, 'data_classification_id'=>3 , 'tier_value'=>'tier 2'],
                ['impact_id'=>4, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>4, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>1 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>2 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>3 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>5 , 'tier_value'=>'tier 1']
            ];
            DB::table('asset_tier_matrix')->insert($create_asset_tier_matrix); 
            return "Table <b>asset_tier_matrix</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/all', function(){
        try {

            Schema::dropIfExists('audit_questions_groups');
            Schema::create('audit_questions_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('group_name')->nullable();
                $table->string('group_name_fr')->nullable();
                $table->timestamps();
            });

            Schema::dropIfExists('group_section');
            Schema::create('group_section', function (Blueprint $table) {
                $table->increments('id');
                $table->string('section_title')->nullable();
                $table->string('section_title_fr')->nullable();
                $table->unsignedBigInteger('group_id')->nullable();
                $table->unsignedBigInteger('number')->nullable();
                $table->timestamps();
            });

            Schema::dropIfExists('group_questions');
            Schema::create('group_questions', function (Blueprint $table) {
                $table->increments('id');
                $table->text('question', 500)->nullable();
                $table->text('question_fr', 500)->nullable();
                $table->text('question_short', 500)->nullable();
                $table->text('question_short_fr', 500)->nullable();
                $table->string('question_num')->nullable();
                $table->text('question_comment', 500)->nullable();
                $table->text('question_comment_fr', 500)->nullable();
                $table->text('additional_comments', 500)->nullable();
                $table->text('question_assoc_type', 500)->nullable();
                $table->Integer('parent_question')->nullable();
                $table->boolean('is_parent')->nullable();
                $table->Integer('parent_q_id')->nullable();
                $table->string('form_key')->nullable();
                $table->string('type')->nullable();
                $table->text('options', 500)->nullable();
                $table->text('options_fr', 500)->nullable();
                $table->boolean('is_data_inventory_question')->nullable();
                $table->string('accepted_formates')->default(0);
                $table->Integer('dropdown_value_from')->nullable();
                $table->Integer('attachment_allow')->default(0);
                $table->string('not_sure_option', 50)->nullable();
                $table->string('control_id', 50)->nullable();
                $table->Integer('section_id')->nullable();
                $table->timestamps();
            });

            Schema::dropIfExists('user_form_links');
            Schema::create('user_form_links', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('sub_form_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedInteger('percent_completed')->nullable();
                $table->text('form_link')->nullable();
                $table->text('form_link_id')->nullable();
                $table->integer('is_locked')->default(0);
                $table->integer('is_accessible')->default(1);
                $table->integer('curr_sec')->default(1);
                $table->integer('email_sent')->default(0);
                $table->string('user_email')->nullable();
                $table->dateTime('expiry_time')->nullable();
                $table->boolean('is_internal')->default(0);
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated_at')->nullable();
            });

            Schema::dropIfExists('user_responses');
            Schema::create('user_responses', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('user_form_id')->nullable();
                $table->unsignedBigInteger('form_id')->nullable();
                $table->unsignedBigInteger('sub_form_id')->nullable();
                $table->unsignedBigInteger('question_id')->nullable();
                $table->unsignedBigInteger('rating')->default(0);
                $table->integer('custom_case')->nullable();
                $table->string('question_key')->nullable();
                $table->boolean('is_internal')->default(0);
                $table->string('user_email')->default(0);
                $table->string('user_id')->default(0);
                $table->text('q_type', 50)->nullable();
                $table->string('type')->nullable();
                $table->string('question_response')->nullable();
                $table->string('additional_comment')->nullable();
                $table->string('additional_info')->nullable();
                $table->string('admin_comment')->nullable();
                $table->string('attachment')->nullable();
                $table->dateTime('created')->default(DB::raw('CURRENT_TIMESTAMP'));
                $table->dateTime('updated')->default(DB::raw('CURRENT_TIMESTAMP'));
            });

            Schema::dropIfExists('remediation_plans');
            Schema::create('remediation_plans', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedBigInteger('sub_form_id')->nullable();
                $table->unsignedBigInteger('control_id')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('person_in_charge')->nullable();
                $table->unsignedBigInteger('post_remediation_rating')->nullable();
                $table->text('proposed_remediation')->nullable();
                $table->text('completed_actions')->nullable();
                $table->date('eta')->nullable();
                $table->string('status')->default(0);
                $table->timestamps();
            });

            Schema::dropIfExists('assets');
            Schema::create('assets', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('asset_type')->nullable();
                $table->string('hosting_type')->nullable();
                $table->string('hosting_provider')->nullable();
                $table->string('country')->nullable();
                $table->string('city')->nullable();
                $table->string('state')->nullable();
                $table->string('lat')->nullable();
                $table->string('lng')->nullable();
                $table->unsignedBigInteger('impact_id')->nullable();
                $table->unsignedBigInteger('data_classification_id')->nullable();
                $table->string('tier')->nullable();
                $table->unsignedBigInteger('client_id')->nullable();
                $table->unsignedBigInteger('asset_number')->nullable()->comment("This will use for uniquely identification of organization asset with (org_id-this_number)");
                $table->string('it_owner')->nullable();
                $table->string('business_owner')->nullable();
                $table->string('internal_3rd_party')->nullable();
                $table->string('data_subject_volume')->nullable();
                $table->string('business_unit')->nullable();
                $table->timestamps();
            });

            Schema::dropIfExists('asset_tier_matrix');
            Schema::create('asset_tier_matrix', function (Blueprint $table) {
                $table->increments('id');
                $table->string('impact_id')->nullable();
                $table->string('data_classification_id')->nullable();
                $table->string('tier_value')->nullable();
                $table->timestamps();
            });
            
            $create_asset_tier_matrix = [
                ['impact_id'=>1, 'data_classification_id'=> 1, 'tier_value'=>'tier 3'],
                ['impact_id'=>1, 'data_classification_id'=>2 , 'tier_value'=>'tier 3'],
                ['impact_id'=>1, 'data_classification_id'=>3 , 'tier_value'=>'tier 3'],
                ['impact_id'=>1, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>1, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>2, 'data_classification_id'=>1 , 'tier_value'=>'tier 3'],
                ['impact_id'=>2, 'data_classification_id'=>2 , 'tier_value'=>'tier 3'],
                ['impact_id'=>2, 'data_classification_id'=>3 , 'tier_value'=>'tier 2'],
                ['impact_id'=>2, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>2, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>3, 'data_classification_id'=>1 , 'tier_value'=>'tier 3'],
                ['impact_id'=>3, 'data_classification_id'=>2 , 'tier_value'=>'tier 3'],
                ['impact_id'=>3, 'data_classification_id'=>3 , 'tier_value'=>'tier 2'],
                ['impact_id'=>3, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>3, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>4, 'data_classification_id'=>1 , 'tier_value'=>'tier 2'],
                ['impact_id'=>4, 'data_classification_id'=>2 , 'tier_value'=>'tier 2'],
                ['impact_id'=>4, 'data_classification_id'=>3 , 'tier_value'=>'tier 2'],
                ['impact_id'=>4, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>4, 'data_classification_id'=>5 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>1 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>2 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>3 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>4 , 'tier_value'=>'tier 1'],
                ['impact_id'=>5, 'data_classification_id'=>5 , 'tier_value'=>'tier 1']
            ];
            
            DB::table('asset_tier_matrix')->insert($create_asset_tier_matrix); 
            return "<b>All Table</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/module_permissions', function(){
        try {
            Schema::dropIfExists('module_permissions');
            Schema::create('module_permissions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('module')->nullable();
                $table->string('module_title')->nullable();
                $table->timestamps();
            });

            $module_permissions = [
                [
                    'id'=>1, 
                    'module'=> "Dashboard", 
                    'module_title'=>'Dashboard'
                ],
                [
                    'id'=>2, 
                    'module'=> "Manage Forms", 
                    'module_title'=>'Manage Assessments'
                ],
                [
                    'id'=>3, 
                    'module'=> "My Assigned Forms", 
                    'module_title'=>'Assigned Assessments'
                ], 
                [
                    'id'=>4, 
                    'module'=> "Completed Forms", 
                    'module_title'=>'Completed Assessments'
                ],
                [
                    'id'=>5, 
                    'module'=> "Generated Forms", 
                    'module_title'=>'Pending Assessments'
                ],
                [
                    'id'=>6, 
                    'module'=> "Manage Audits", 
                    'module_title'=>'Manage Audits'
                ],
                [
                    'id'=>7, 
                    'module'=> "Assigned Audits", 
                    'module_title'=>'Assigned Audits'
                ],
                [
                    'id'=>8, 
                    'module'=> "Completed Audits", 
                    'module_title'=>'Completed Audits'
                ],
                [
                    'id'=>9, 
                    'module'=> "Generated Audits", 
                    'module_title'=>'Generated Audits'
                ],

                [
                    'id'=>10, 
                    'module'=> "SAR Forms", 
                    'module_title'=>"SAR Forms"
                ],

                [
                    'id'=>11, 
                    'module'=> "SAR Forms Submitted", 
                    'module_title'=>'SAR Forms Submitted'
                ],
                [
                    'id'=>12, 
                    'module'=> "SAR Forms pending", 
                    'module_title'=>'SAR Forms pending'
                ],
                [
                    'id'=>13, 
                    'module'=> "Users Management", 
                    'module_title'=>'Users Management'
                ],
                [
                    'id'=>14, 
                    'module'=> "Global Data Inventory", 
                    'module_title'=>'Global Data Inventory'
                ],
                [
                    'id'=>15, 
                    'module'=> "Detailed Data Inventory", 
                    'module_title'=>'Detailed Data Inventory'
                ],
                [
                    'id'=>16, 
                    'module'=> "Assets List", 
                    'module_title'=>'Assets List'
                ],
                [
                    'id'=>17, 
                    'module'=> "Activities List", 
                    'module_title'=>'Activities List'
                ],
                [
                    'id'=>18, 
                    'module'=> "Incident Register", 
                    'module_title'=>'Incident Register'
                ],
                [
                    'id'=>19, 
                    'module'=> "Sub Forms Expiry Settings", 
                    'module_title'=>'Sub Forms Expiry Settings'
                ],
                [
                    'id'=>20, 
                    'module'=> "SAR Expiry Settings", 
                    'module_title'=>'SAR Expiry Settings'
                ],
                [
                    'id'=>21, 
                    'module'=> "Data Elements", 
                    'module_title'=>'Data Elements'
                ],
                [
                    'id'=>22, 
                    'module'=> "Data Classification", 
                    'module_title'=>'Data Classification'
                ],
                [
                    'id'=>23, 
                    'module'=> "Evaluation Rating", 
                    'module_title'=>'Evaluation Rating'
                ],
                [
                    'id'=>24, 
                    'module'=> "Remediation Plans", 
                    'module_title'=>'Remediation Plans'
                ],
            ];
            DB::table('module_permissions')->insert($module_permissions); 
            return "Table <b>module_permissions</b> Successfully Created";
        } 
        catch (\Exception $th) {
            return $th->getMessage();
        }
    });



    Route::get('add/sub_forms', function () {
        try {
            Schema::dropIfExists('sub_forms');
            Schema::create('sub_forms', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title')->nullable();
                $table->string('title_fr')->nullable();
                $table->integer('parent_form_id')->nullable();
                $table->integer('client_id')->nullable();
                $table->string('item_type')->nullable();
                $table->integer('other_number')->nullable();
                $table->string('other_id')->nullable();
                $table->integer('asset_id')->nullable();
                $table->datetime('expiry_time')->nullable();
                $table->timestamps();
            });
            return "ok";
        } catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::get('add/eval', function () {
        try {
            Schema::dropIfExists('evaluation_rating');
            Schema::create('evaluation_rating', function (Blueprint $table) {
                $table->increments('id');
                $table->string('assessment')->nullable();
                $table->string('rating')->nullable();
                $table->string('color')->nullable();
                $table->string('text_color')->nullable();
                $table->integer('owner_id')->nullable();
                $table->integer('rate_level')->nullable();
                $table->timestamps();
            });

            $evaluation_rating_data = [
                [
                    'assessment'=>"Implemented & tested",
                    'rating'=>"Good",
                    'color'=>"#037428",
                    'text_color'=>"#fff",
                    'rate_level'=>1
                ],
                [
                    'assessment'=>"Fully implemented",
                    'rating'=>"Satisfactory",
                    'color'=>"#DEEE91",
                    'text_color'=>"#000",
                    'rate_level'=>2
                ],
                [
                    'assessment'=>"Partially implemented",
                    'rating'=>"Marginal",
                    'color'=>"#FF8C01",
                    'text_color'=>"#fff",
                    'rate_level'=>3
                ],
                [
                    'assessment'=>"Not implemented",
                    'rating'=>"Weak",
                    'color'=>"#ED2938",
                    'text_color'=>"#fff",
                    'rate_level'=>4
                ],
                [
                    'assessment'=>"N/A",
                    'rating'=>"N/A",
                    'color'=>"#808080",
                    'text_color'=>"#fff",
                    'rate_level'=>5
                ]
                ];
                DB::table('evaluation_rating')->insert($evaluation_rating_data); 
                return "evaluation_rating added";
        } catch (\Exception $th) {
            return $th->getMessage();
        }
    });

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });







