<?php
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

Auth::routes();

// -------------------------------------------------------------------

Route::get('/dummy', 'Reports@Dummywork')->name('dummy');
// ----------------Reportssss----------------------
Route::get('/report/audit/{id}', 'Reports@get_report')->name('report.audit');
Route::get('/report/rem/{id}', 'Reports@get_reme_report')->name('report.reme');
Route::get('/report/postrem', 'Reports@get_postreme_report')->name('postreport.reme');

Route::get('/report/asset/{id}', 'Reports@get_asset_report')->name('report.asset');
Route::get('/report/remediation', 'Reports@remediation_report')->name('report.remediation');
Route::post('/your-ajax-endpoint', 'Reports@getRemediationByBusinessUnits');

Route::post('/your-ajax-endpoints/{id}', 'Reports@getAssetsByFilters');


// -------------------------------------------------------------------

Route::get('language/{lang}', function ($lang) {
    \Session::put('locale', $lang);
    return redirect()->back();
})->middleware('language');

Route::get('/verify-your-email', function () {
    if (auth()->user()->is_email_varified == 1) {
        return redirect('dashboard');
    }
    return view('login_mail_verify');
})->middleware(['auth', '2fa']);

Route::get('home', function () {
    return redirect('dashboard');
})->name('home');

Route::redirect('/', 'login');
Route::post('/login_post', 'HomeController@login_post')->name('login_post');
Route::get('/reload-captcha', 'HomeController@reloadCaptcha');
Route::get('send_code', 'UsersController@send_code')->middleware(['auth', '2fa']);
Route::get('/logout', 'Auth\LoginController@logout');

Route::get('/2fa', 'PasswordSecurityController@show2faform');
Route::post('/2fa', 'PasswordSecurityController@enable2fa')->name('enable2fa');
Route::post('/generate2fasecret', 'PasswordSecurityController@generate2fasecret')->name('generate2fasecret');
Route::post('/disable2fa', 'PasswordSecurityController@disable2fa')->name('disable2fa');

Route::middleware(['auth', '2fa', 'is_email_varified'])->group(function () {

    // -------------------------------- Dashboard --------------------------------
    Route::get('/dashboard', 'UsersController@dashboard')->name('dashboard');
    Route::get('/dashboard-new', 'UsersController@dashboard_2')->name('new-dashboard');

    // -------------------------------- Remediation Plan --------------------------------
    Route::get('audit/remediation', 'RemediationController@remediation_plans')->name('audit.list');
    Route::get('audit/remediation/add/{id}', 'RemediationController@add_new_remediation_plan')->name('add_new_remediation');
    Route::get('audit/remediation/controls/{id}', 'RemediationController@remediation_control')->name('remediation_control');
    Route::post('audit/remediation/add', 'RemediationController@add_new_remediation_db')->name('add_new_remediation_db');
    Route::get('audit/remediation/{id}', 'RemediationController@single_remediation')->name('single_remediation');
    Route::get('audit/remediation/details/{id}', 'RemediationController@get_remediation_details')->name('get_remediation_details');
    Route::post('audit/remediation/update/{id}', 'RemediationController@update_remediation_details')->name('update_remediation_details');

    // -------------------------------- Remediation Plans Old Routes --------------------------------
    Route::get("remediation-plans", "RemediationController@remediation_plans");
    Route::get("add_remediation_plans", "RemediationController@add_remediation_plans");
    Route::post("add_remediation", "RemediationController@add_remediation")->name("add_remediation");
    Route::get("delete-remediation-plans/{id}", "RemediationController@delete_remediation_plans");
    Route::get("edit-remediation-plans/{id}", "RemediationController@edit_remediation_plans");
    Route::post("update-remediation", "RemediationController@update_remediation");

    // -------------------------------- AUDIT FORMS --------------------------------
    Route::get('audit/list', 'AuditFormsController@audits')->name('audit.list');
    Route::get('audit/assssigned', 'AuditFormsController@client_audit_forms')->name('audit.assssigned');
    Route::get('audit/completed', 'AuditFormsController@completed_audits')->name('audit.completed');
    Route::get('audit/pending', 'AuditFormsController@org_pending_forms')->name('audit.pending');
    Route::post('audit/add/comment', 'AuditFormsController@add_comment_against_question')->name('audit.add_comment_against_question');
    Route::post('audit/add/rating', 'AuditFormsController@add_rating_against_question')->name('audit.add_rating_against_question');

    Route::get('audit/sub-form/{id}', 'AuditFormsController@audit_subforms_list')->name('audit.sub-form');
    Route::get('audit/get-assets/{id}', 'AuditFormsController@get_assets')->name('audit.get-assets');
    Route::post('audit/add/subform', 'AuditFormsController@create_subform')->name('gen__audit_subform');
    Route::get('/audit/external/assigned/{id}', 'AuditFormsController@organization_all_forms_list')->name('org_all_forms_list');
    Route::get('/audit/internal/assigned/{id}', 'AuditFormsController@subform_assignees')->name('org_all_forms_list');
    Route::get('audit/internal/{link_id}', 'AuditFormsController@in_users_show_form')->name('user_audit_link');
    Route::post('audit/ajaxsubmituseraudit', 'AuditFormsController@ajax_int_user_submit_form')->name('ajax_int_user_submit_audit');
    Route::post('audit/add/attachment', 'AuditFormsController@in_add_attachment_to_question')->name('add_attachment_to_question');
    Route::get('audit/form/{id}', 'AuditFormsController@view_form')->name('view_audit_form');

    // ------------------------------------------ SAR FORMS -----------------------------------------------
    Route::get('SAR/SubFormAssignees/{id}', 'Forms@subform_assignees')->name('sar_subform_assignees');
    Route::get('SAR/OrgSubFormsList/{id}', 'Forms@organization_all_forms_list')->name('sar_org_all_forms_list');
    Route::get('SAR/CompanyUserForm/{link_id}', 'Forms@in_users_show_form')->name('sar_user_form_link');
    Route::get('SAR/ShowSARAssignees/{form_id}', 'SARForm@assignee_list')->name('show_SAR_assignee_list');
    Route::get('SAR/SARCompletedFormsList', 'SARForm@sar_completed_forms_list');
    Route::get('SAR/SARInCompletedFormsList', 'SARForm@sar_incompleted_forms_list');
    Route::get('FormSettings/SARExpirySettings', 'SARForm@sar_expiry_settings_get');
    Route::post('FormSettings/SARExpirySettings', 'SARForm@sar_expiry_settings_post');
    Route::post('SAR/ChangeRequestStatus', 'SARForm@change_sar_request_status_post');

    // ------------------------------------------ FORMS -----------------------------------------------
    Route::get('Forms/UserForm/{id}', 'Forms@show_form')->name('user_form_link');
    Route::get('Forms/CompanyUserForm/{link_id}', 'Forms@in_users_show_form')->name('user_form_link');
    Route::get('/Forms/UserFormsList', 'Forms@forms_list')->name('forms_list');

    Route::get('/Forms/CompanyUsersSubFormsList/{id}', 'Forms@subforms_email_list')->name('send_subforms_list');
    Route::get('/Forms/OrgSubFormsList/{id}', 'Forms@organization_all_forms_list')->name('org_all_forms_list');
    Route::get('/all_generated_forms', 'Forms@organization_all_forms_list_all')->name('all_generated_forms');
    Route::get('/Forms/ExtUserSendSubFormsList/{id}', 'Forms@ext_users_subforms_email_list')->name('ext_users_send_subforms_list');
    Route::post('Forms/AsgnSubFormToExUsers', 'Forms@assign_subform_to_external_users')->name('assign_subforms_to_external_users');
    Route::get('Forms/SubFormsList/{id}', 'Forms@subforms_list')->name('subforms_list');

    Route::get('Forms/SubFormToSend/{id}', 'Forms@send_email_subforms_list')->name('subform_to_send');
    Route::get('Forms/AddSubForm/{id}', 'Forms@add_subform')->name('add_subform');
    Route::get('Forms/EditSubform', 'SubformActions@edit_subform')->name('edit_subform');
    Route::get('Forms/DeleteSubform', 'SubformActions@delete_subform')->name('delete_subform');
    Route::post('Forms/GenerateSubForm', 'Forms@create_subform')->name('gen_subform');
    Route::get('Forms/CreateTestForm', 'Forms@create_test_user_form')->name('create_test_usr_form'); //
    Route::get('Forms/SendForm/{id}', 'Forms@send_form_link_to_users')->name('send_form_to_users');
    Route::get('Forms/AdminSite', 'AdminController@index')->name('admin_site');
    Route::get('Forms/FormAssignees/{id}', 'Forms@form_assignees')->name('admin_assignees');
    Route::get('Audit/Assignees/{id}', 'AuditFormsController@audit_assignees')->name('admin_audit_assignees');
    Route::get('Forms/SubFormAssignees/{id}', 'Forms@subform_assignees')->name('subform_assignees');
    Route::post('Forms/AssignFormToClient', 'Forms@assign_form_to_client')->name('assign_form_to_client');
    Route::post('Forms/AssignSubFormToUsers', 'Forms@ajax_assign_subform_to_users')->name('assign_subform_to_users');

    Route::get('Forms/FormsList', 'Forms@forms_list')->name('client_site'); //
    Route::get('Forms/All_Generated_Forms', 'Forms@organization_all_forms_list_all')->name('client_site_all_generated_forms');
    Route::get('Forms/CompletedFormsList', 'Forms@completed_forms_list')->name('client_site');
    Route::get('Forms/ViewForm/{id}', 'Forms@view_form')->name('view_form');
    Route::get('Forms/UserSite', 'Forms@user_site')->name('user_site');
    Route::get('Forms/ClientUserFormsList', 'Forms@client_user_subforms_list')->name('client_user_subforms_list');
    Route::get('FormSettings/SubFormsExpirySettings', 'Forms@show_subforms_expiry_settings')->name('subforms_expiry_settings');
    Route::post('FormSettings/SubFormsExpirySettings', 'Forms@save_subforms_expiry_settings')->name('subforms_expiry_settings');
    Route::post('/formsettings/unlock_form', 'Forms@unlock_form')->name('unlock_form');
    Route::post('/formsettings/change_form_access', 'Forms@change_form_access')->name('change_form_access');

    Route::get('users_management', 'UserManagementController@users_management');
    Route::get('add_user', 'UserManagementController@add_user');
    Route::get('edit_user/{id}', 'UserManagementController@edit_user');
    Route::post('users/change_status', 'UserManagementController@change_status');
    Route::post('store_user', 'UserManagementController@store_user');
    Route::post('delete_user', 'UserManagementController@delete_user');
    Route::post('store_edit/{id}', 'UserManagementController@store_edit');

    Route::get('/Orgusers/permissions/{id}', 'UserManagementController@permissions');
    Route::post('orgusers/permissions/store', 'UserManagementController@permissions_store');

    // ------------------------------------------ Incident Register -----------------------------------------------
    Route::get('/incident', 'IncidentRegisterController@index');
    Route::get('/add_inccident', 'IncidentRegisterController@create');
    Route::post('/save_inccident', 'IncidentRegisterController@add');
    Route::get('/edit_incident/{id}', 'IncidentRegisterController@edit_incident');
    Route::post('/update_inccident', 'IncidentRegisterController@add');
    Route::post('/incident/delete', 'IncidentRegisterController@destroy');

    // ------------------------------------------ Reports -----------------------------------------------
    Route::get('reports/global/inventory', 'Reports@global_data_inventory')->name('summary_reports_all');
    Route::get('reports/detailed/inventory', 'Reports@detailed_data_inventory')->name('detail_data_inventory_report');

    Route::get('report_export/{cat_id}', 'Reports@export_detail_data');
    Route::get('/reports/AssetsReports/{id}', 'Reports@response_reports')->name('assets_reports');
    Route::get('/reports/DataInvReports/{id}', 'Reports@response_reports')->name('data_inventory_reports');
    Route::get('/reports/AssetsReportsEx/{id}', 'Reports@response_reports_external_users')->name('assets_reports_ex');
    Route::get('/reports/DataInvReportsEx/{id}', 'Reports@response_reports_external_users')->name('data_inventory_reports_ex');
    Route::get('/reports/AssetsReportsReg/{id}', 'Reports@response_reports_registered_users')->name('assets_reports_in');
    Route::get('/reports/DataInvReportsReg/{id}', 'Reports@response_reports_registered_users')->name('data_inventory_reports_in');
    Route::get('reports/SummaryReports', 'Reports@summary_reports_sfw')->name('summary_reports');

    // ------------------------------------------ Activites -----------------------------------------------
    Route::get('/activities', 'ActivitiesController@index')->name('activity_list');

});

// Routes Without Authantication

Route::get('audit/external/{client_id}/{user_id}/{client_email}/{subform_id}/{user_email}/{date_time}', 'AuditFormsController@ex_users_show_form')->name('external_audit_link');
Route::post('audit/add/additional-comment', 'AuditFormsController@add_comment_against_question')->name('audit.add_additional_comment_against_question');
// Route::post('audit/add/check-section-status', 'AuditFormsController@check_section_status')->name('audit.check_section_status');
Route::post('audit/ajaxsubmitexternalaudit', 'AuditFormsController@ajax_ext_user_submit_form')->name('ajax_ext_user_submit_audit');
Route::get('audit/count/{group}/{sub_form}', 'AuditFormsController@get_question_count')->name('get_question_count');
Route::post('audit/add/attachment/ex', 'AuditFormsController@ex_add_attachment_to_question')->name('ex_add_attachment_to_question');
Route::post('audit/lock', 'AuditFormsController@ajax_lock_user_audit_form')->name('ajax_lock_user_audit_form');
Route::get('audit/success', 'AuditFormsController@show_success_msg')->name('show_audit_success_msg');
Route::get('SAR/ExtUserForm/{client_id}/{user_id}/{client_email}/{subform_id}/{user_email}/{date_time}', 'Forms@ex_users_show_form')->name('sar_ext_user_form_link');
Route::get('Forms/UserForm/{client_id}/{user_id}/{client_email}/{subform_id}/{user_email}/{date_time}', 'Forms@show_form')->name('user_form_link');
Route::get('Forms/ExtUserForm/{client_id}/{user_id}/{client_email}/{subform_id}/{user_email}/{date_time}', 'Forms@ex_users_show_form')->name('ext_user_form_link');

Route::post('Forms/AjaxSubmitExtUserForm', 'Forms@ajax_ext_user_submit_form')->name('ajax_ext_user_submit_form');
Route::post('Forms/AjaxSubmitUserForm', 'Forms@ajax_int_user_submit_form')->name('ajax_int_user_submit_form');
Route::post('Forms/LockUserForm', 'Forms@ajax_lock_user_form')->name('ajax_lock_user_form');
Route::get('Forms/FormSuccess', 'Forms@show_success_msg')->name('show_success_msg');
//  end

Route::post('/save_images', 'PackagesController@save_images')->name('save_images');
Route::post('package/save_related_product', 'PackagesController@save_related_product');

Route::middleware(['auth', '2fa', 'is_email_varified'])->group(function () {
    Route::get('/Forms/SecCategory/{id}', 'Forms@section_category');
    Route::post('/Forms/UpdateFormSection/', 'Forms@ajax_update_form_section_heading')->name('update_form_section_heading');
    Route::post('/updateSorting', 'Forms@updateSorting')->name('updateSorting');
    Route::post('/Forms/AsgnSecCategory/', 'Forms@assign_section_category');
    Route::post('/Forms/AsgnSecCategory/', 'Forms@assign_section_category')->name('asgn_sec_ctgry');
});

Route::post('Reports/CompanyReportsPDF', 'Reports@summary_reports_all_PDF')->name('summary_reports_all_pdf')->middleware(['auth', '2fa', 'is_email_varified']);
Route::view('question', 'admin.question.questionForm');
Route::post('/profile/edit', 'PackagesController@profile_edit');

Route::get('Reports/DownloadCompanyReportsPDF/{pdfname}', 'Reports@download_generated_pdf')->name('download_summary_reports_all_pdf')->middleware(['auth', '2fa', 'is_email_varified']);

//  Login Image SETTINGS
Route::get('/login_img_settings', 'HomeController@login_img_settings')->middleware(['auth']);
Route::post('/update_login_img', 'HomeController@update_login_img')->middleware(['auth']);
Route::post('reset_password', 'HomeController@reset');

// Data Classification
Route::get('/data-classification', 'HomeController@data_classification')->middleware(['auth']);
Route::get('/front/data-classification', 'Forms@data_classification')->middleware(['auth']);

// Edit form front classification
Route::get('/front/edit-classification/{id}', 'Forms@edit_classification')->name('front_edit_classification');
Route::post('/front/edit-classification/{id}', 'Forms@edit_classification_act')->name('front_edit_classification_act');

// Impact
Route::get('/impact', 'HomeController@impact')->middleware(['auth']);

// ASSETS
Route::get('assets', 'AssetsController@index')->name('asset_list')->middleware(['auth', '2fa', 'is_email_varified']);
Route::post('assets', 'AssetsController@index')->name('asset_list') /*->middleware(['auth','2fa' ,'is_email_varified'])*/;
Route::get("assets_data_elements", "AssetsController@asset_data_elements")->name("asset_data_elements");
Route::post("data-element-group", "AssetsController@dataElementGroup");
Route::get('elements-data', "AssetsController@element_data");
Route::get("edit-data-element/{id}", "AssetsController@edit_data_element");
Route::post("update_data_element", "AssetsController@update_data_element");
Route::get('import-element-data', "AssetsController@import_data_element")->name("import-data-element");
Route::post('import-element-data', "AssetsController@import_data_element")->name("import-data-element");
Route::get("import-data-element-sample", "AssetsController@DataElementSample");
Route::post("update_asset_data_element", "AssetsController@asset_elements_update")->name("update_asset_eleme");
Route::get('add_asset', 'AssetsController@add_asset')->name('add_asset')->middleware(['auth', '2fa', 'is_email_varified']);
Route::get('export-asset/{client_id}', "AssetsController@exportAssets")->name("export-asset");
Route::get('export-elements-data/{client_id}', "AssetsController@exportElementData")->name("export-elements-data");
Route::get('import-asset', "AssetsController@importAssets");
Route::post('import-asset-data', "AssetsController@importAssetsData")->name('import');
Route::get('export-sample-data', 'AssetsController@exportSampleData');
Route::post('delete_asset', 'AssetsController@delete_asset')->middleware(['auth']);
Route::post('asset_update', 'AssetsController@asset_update')->name('asset_update');
Route::post('asset_add', 'AssetsController@asset_add')->name('asset_add')->middleware(['auth', '2fa', 'is_email_varified']);
Route::post('update_asset', 'AssetsController@update_asset')->name('update_asset')->middleware(['auth', '2fa', 'is_email_varified']);
Route::get('data-element-sample', "AssetsController@data_element_sample");
Route::get('asset_delete/{id}', 'AssetsController@asset_delete');
Route::get('asset_edit/{id}', 'AssetsController@asset_edit')->name('asset_edit')->middleware(['auth', '2fa', 'is_email_varified']);
Route::post('asset_edit/{id}', 'AssetsController@asset_edit');
Route::get("view-assets/{id}", "AssetsController@view_assets");

// Ajax call
Route::post("asset_matrix_tier", "AssetsController@asset_matrix")->name("getData");
Route::get("evaluation_rate", "AssetsController@evaluation_rating")->name("evaluation_rat");
Route::get('edit-evalution/{id}', "AssetsController@edit_evalution");
Route::post("update_evalution_rating", "AssetsController@update_evalution_rating");

Route::group(["middleware" => 'admin'], function () {

    Route::get('/admin', 'UsersController@index')->name('admin_def');
    Route::get('/site_admins', 'Admin@site_admins')->name('site_admins');
    Route::get('/add_admin', 'Admin@add_admin')->name('add_admin');
    Route::post('/add_admin', 'Admin@add_admin_act')->name('add_admin_act');
    Route::get('/edit_admin/{id}', 'Admin@edit_admin')->name('edit_admin');
    Route::post('/edit_admin/{id}', 'Admin@edit_admin_act')->name('edit_admin_act');
    Route::get('edit_form/{id}', 'Admin@edit_form')->name('edit_form_info');
    Route::post('edit_form/{id}', 'Admin@edit_form_act')->name('edit_form_info_act');
    Route::get("evaluation_rating", "Admin@evaluation_rating")->name('evaluation_rating');
    Route::get("edit-evaluation/{id}", "Admin@edit_evaluation")->name('edit_evaluation');
    Route::post("update_evaluation", "Admin@update_evaluation");

    //Edit form classification
    Route::get('edit-classification/{id}', 'Admin@edit_classification')->name('edit_classification');
    Route::post('edit-classification/{id}', 'Admin@edit_classification_act')->name('edit_classification_act');
    //Edit Impact
    Route::get('edit-impact/{id}', 'Admin@edit_impact')->name('edit_impact');
    Route::post('edit-impact/{id}', 'Admin@edit_impact_act')->name('edit_impact_act');

    //Edit form classification
    Route::get("data_element", "UsersController@data_element");
    Route::post('admin-data-element-group', 'UsersController@data_element_group');
    Route::get('edit-data-element-group/{id}', "UsersController@edit_data_element_group");
    Route::post('update_data_element_group', "UsersController@update_data_element_group");
    Route::get('/company', 'UsersController@company')->middleware(['auth']);

    Route::get('selectClient', 'PackagesController@index')->middleware(['auth']);
    Route::get('client', 'PackagesController@client')->middleware(['auth']);
    Route::post('saveClient/{id}', 'PackagesController@saveClient')->middleware(['auth']);
    Route::get('client/user/{id}', 'PackagesController@showUser')->middleware(['auth']);
    Route::get('/users/edit/{id}', 'UsersController@edit');

    Route::get('/users/edit_company/{id}', 'UsersController@edit_company')->middleware(['auth']);
    Route::get('/users/permissions/{id}', 'UsersController@permissions')->middleware(['auth']);
    Route::get('/users/detail/{id}', 'UsersController@detail')->middleware(['auth']);
    Route::get('/users/add/{id}', 'UsersController@addUser')->middleware(['auth']);
    Route::post('/users/store', 'UsersController@store')->middleware(['auth']);
    Route::post('users/permissions/store', 'UsersController@permissions_store')->middleware(['auth']);
    Route::post('/users/delete', 'UsersController@destroy')->middleware(['auth']);
    Route::post('/users/edit_store/{id}', 'UsersController@edit_store')->middleware(['auth']);
    Route::post('/users/editCompany_store/{id}', 'UsersController@editCompany_store')->middleware(['auth']);
    Route::post('/client/store', 'UsersController@clientStore')->middleware(['auth']);
    Route::get('/client/add', 'UsersController@addClient')->middleware(['auth']);
    Route::get('Forms/AdminFormsList/{type?}', 'Forms@all_forms_list')->name('admin_forms_list')->middleware(['auth']);

    Route::get('Forms/Add-new-form', 'Forms@add_new_form')->name('add_new_form')->middleware(['auth']);
    Route::post('Forms/Add-new-form', 'Forms@store_new_form')->name('store_new_form')->middleware(['auth']);
    Route::get('Forms/{form_id}/add/questions', 'Forms@add_form_questions')->name('add_form_questions')->middleware(['auth']);

    Route::post('change_question_title', 'Forms@chnageQuestionLabel')->name('chnageQuestionLabel')->middleware(['auth']);
    Route::post('update_cc_options', 'Forms@update_cc_options')->name('update_cc_options')->middleware(['auth']);
    Route::post('update_sc_comment', 'Forms@update_sc_comment')->name('update_sc_comment')->middleware(['auth']);

    Route::post('form/add/section/to/form', 'Forms@add_section_to_form')->name('add_section_to_form')->middleware(['auth']);
    Route::post('form/add/question/to/form', 'Forms@add_question_to_form')->name('add_question_to_form')->middleware(['auth']);
    Route::post('form/add/special_question/to/form', 'Forms@add_special_question_to_form')->name('add_special_question_to_form')->middleware(['auth']);
    Route::post('update_options', 'Forms@update_options')->name('update_options')->middleware(['auth']);
    Route::post('update_options_fr', 'Forms@update_options_fr')->name('update_options_fr')->middleware(['auth']);

    Route::post('change_question_comment', 'Forms@change_question_comment')->name('change_question_comment')->middleware(['auth']);
    Route::post('delete_question', 'Forms@delete_question')->name('delete_question')->middleware(['auth']);

    // ------------------------------------------ Users -----------------------------------------------
    Route::get('/users/edit/{id}', 'UsersController@edit');
    Route::get('/users/detail/{id}', 'UsersController@detail');
    Route::get('/users/add', 'UsersController@addUser');
    Route::post('/users/store', 'UsersController@store');
    Route::post('/users/delete', 'UsersController@destroy');
    Route::post('verify_code', 'HomeController@verify_code');
    Route::post('/users/edit_store/{id}', 'UsersController@edit_store');
    Route::post('/client/store', 'UsersController@clientStore');
    Route::get('/client/add', 'UsersController@addClient');

    // ------------------------------------------ GROUPS CRUD -----------------------------------------------
    Route::get('group/list', 'Groups@list')->name('groups_list');
    Route::get('group/add', 'Groups@add')->name('group_add');
    Route::post('group/save', 'Groups@save')->name('group_save');
    Route::get('group/edit/{id}', 'Groups@edit')->name('group_edit');
    Route::post('group/update/{id}', 'Groups@update')->name('group_update');
    Route::get('group/delete/{id}', 'Groups@delete')->name('group_delete');
    Route::get('group/duplicate/{id}', 'Groups@duplicate')->name('group_duplicate');

    // ------------------------------------------ GROUPS Questions -----------------------------------------------
    Route::post('group/section/add', 'Groups@add_section_to_group')->name('add_section_to_group');
    Route::post('group/section/update', 'Groups@update_section_to_group')->name('update_section_to_group');

    // ------------------------------------------ GROUPS Questions -----------------------------------------------
    Route::get('group/question/add/{id}', 'Groups@add_question')->name('group_add_quetion');
    Route::get('group/question/get/{id}', 'Groups@return_question')->name('return_question');
    Route::post('group/question/add', 'Groups@add_question_to_group')->name('add_question_to_group');
    Route::post('group/question/update', 'Groups@update_question')->name('update_group_question');
    Route::post('group/question/delete/{id}', 'Groups@delete_question')->name('delete_question');

    Route::get('/groups/get', 'Groups@group_list')->name('group_list');
    // Route::post('group/multi-question/add',          'Groups@add_multi_question_to_group')->name('add_multi_question_to_group');
    // Route::post('group/special/question/add',       'Groups@add_special_question')->name('add_special_question');

});

Auth::routes();
Route::post('/2faverify', function () {
    $role_routes = [
        1 => 'admin_def',
        2 => 'dashboard',
        3 => 'dashboard',
    ];

    $user_role = Auth::user()->role;

    if (Auth::user()->user_type == '1') {
        $user_role = 2;
    }

    return redirect(route($role_routes[$user_role]));
})->name('2faverify')->middleware('2fa');

// Route::get('/__clear__', function () {

//      Artisan::call('cache:clear');
//      Artisan::call('route:clear');
//      Artisan::call('config:clear');
//      Artisan::call('route:cache');
//      Artisan::call('config:cache');
//      Artisan::call('view:clear');

//      return 'cache cleard';
// });
// Route::get('/info', function () {
//      dd(App::VERSION());
// });

// Route::get('/test_work', function () {
//      $query = "ALTER TABLE user_forms
//      ADD COLUMN updated_at VARCHAR(25) DEFAULT Null ";
//      DB::select($query);
// });

// Route::prefix("test")->group(function () {
//      Route::get("/check-email", function () {
//          return "check email";
//      });
// });
// Route::get('/test',                     'HomeController@test')->name('test');
// Route::get('/myredirect',               'HomeController@my_redirect')->name('myredirect');
// Route::get('/login',                    'Auth/LoginController@login')->name('login');

//  Not in use yet

Route::get('Forms/TestEmail', 'HomeController@test_email')->name('test_email');
Route::get('/profile/{id}', 'PackagesController@profile');
Route::get('/terms', 'HomeController@terms');
Route::get('/privacy', 'HomeController@privacy');
Route::get('/subscriber', 'HomeController@subscriber');
Route::get('/howtoplay', 'HomeController@howtoplay');

Route::get('export', function () {
    try {
        $check = DB::table('assets')
            ->join("data_classifications", "data_classifications.id", "assets.data_classification_id")
            ->join("impact", "impact.id", "assets.impact_id")
            ->join("users", "users.id", "assets.client_id")
            ->select("assets.name", "assets.asset_type", "assets.hosting_type", "assets.hosting_provider", "assets.country", "assets.city", "assets.state", "assets.lng", "assets.lat", "impact.impact_name_en", "data_classifications.classification_name_en", "assets.tier", "users.name as user_names", "assets.it_owner", "assets.business_owner", "assets.business_unit", "assets.internal_3rd_party", "assets.data_subject_volume")
        // ->orderBy("assets.id","ASC")
            ->first();
        print("<pre>");
        print_r($check);
        exit;
    } catch (\Exception $th) {
        return $th->getMessage();
    }
});

// Tables

Route::get('zest/add/tables', function () {
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
            ['impact_id' => 1, 'data_classification_id' => 1, 'tier_value' => 'tier 3'],
            ['impact_id' => 1, 'data_classification_id' => 2, 'tier_value' => 'tier 3'],
            ['impact_id' => 1, 'data_classification_id' => 3, 'tier_value' => 'tier 3'],
            ['impact_id' => 1, 'data_classification_id' => 4, 'tier_value' => 'tier 1'],
            ['impact_id' => 1, 'data_classification_id' => 5, 'tier_value' => 'tier 1'],
            ['impact_id' => 2, 'data_classification_id' => 1, 'tier_value' => 'tier 3'],
            ['impact_id' => 2, 'data_classification_id' => 2, 'tier_value' => 'tier 3'],
            ['impact_id' => 2, 'data_classification_id' => 3, 'tier_value' => 'tier 2'],
            ['impact_id' => 2, 'data_classification_id' => 4, 'tier_value' => 'tier 1'],
            ['impact_id' => 2, 'data_classification_id' => 5, 'tier_value' => 'tier 1'],
            ['impact_id' => 3, 'data_classification_id' => 1, 'tier_value' => 'tier 3'],
            ['impact_id' => 3, 'data_classification_id' => 2, 'tier_value' => 'tier 3'],
            ['impact_id' => 3, 'data_classification_id' => 3, 'tier_value' => 'tier 2'],
            ['impact_id' => 3, 'data_classification_id' => 4, 'tier_value' => 'tier 1'],
            ['impact_id' => 3, 'data_classification_id' => 5, 'tier_value' => 'tier 1'],
            ['impact_id' => 4, 'data_classification_id' => 1, 'tier_value' => 'tier 2'],
            ['impact_id' => 4, 'data_classification_id' => 2, 'tier_value' => 'tier 2'],
            ['impact_id' => 4, 'data_classification_id' => 3, 'tier_value' => 'tier 2'],
            ['impact_id' => 4, 'data_classification_id' => 4, 'tier_value' => 'tier 1'],
            ['impact_id' => 4, 'data_classification_id' => 5, 'tier_value' => 'tier 1'],
            ['impact_id' => 5, 'data_classification_id' => 1, 'tier_value' => 'tier 1'],
            ['impact_id' => 5, 'data_classification_id' => 2, 'tier_value' => 'tier 1'],
            ['impact_id' => 5, 'data_classification_id' => 3, 'tier_value' => 'tier 1'],
            ['impact_id' => 5, 'data_classification_id' => 4, 'tier_value' => 'tier 1'],
            ['impact_id' => 5, 'data_classification_id' => 5, 'tier_value' => 'tier 1'],
        ];
        DB::table('asset_tier_matrix')->insert($create_asset_tier_matrix);

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

        return "<b>All Table</b> Successfully Created";
    } catch (\Exception $th) {
        return $th->getMessage();
    }
});


