<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SubformActions extends Controller
{
    
    public function __construct()
    {

   
    }
    
    public function edit_subform (Request $request)
    {
        $sb_id = $request->input('sb-id');
        $name  = $request->input('name');

        $status = 'error';
        $msg    = __('You are not authorized to peform this action'); 
        
        $client_id = DB::table('sub_forms')->where('id', $sb_id)->pluck('client_id')->first();
        
        if ((Auth::user()->client_id == $client_id) && (Auth::user()->role == 2 || (Auth::user()->role == 3 && Auth::user()->user_type == '1')))
        {
            $status = 'success';
            $msg    = __('Sub-form successfully updated');
            DB::table('sub_forms')->where('id', '=', $sb_id)->update(['title' => $name]);
        }
        

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
    
    public function delete_subform (Request $request)
    {
        $sb_id     = $request->input('sb-id');
        
        $status = 'error';
        $msg    = __('You are not authorized to peform this action'); 
        
        $client_id = DB::table('sub_forms')->where('id', $sb_id)->pluck('client_id')->first();

        if ((Auth::user()->client_id == $client_id) && (Auth::user()->role == 2 || (Auth::user()->role == 3 && Auth::user()->user_type == '1')))
        {
            $status = 'success';
            $msg    = __('Sub-form successfully removed');
            
            // SELECT DISTINCT external_user_form_id FROM external_users_filled_response where external_user_form_id IN (SELECT id FROM `external_users_forms` WHERE sub_form_id = 4)            
            
            //SELECT id FROM `external_users_forms` WHERE sub_form_id = 4
            $exf_id_list = DB::table('external_users_forms')->where('sub_form_id', $sb_id)->distinct()->pluck('id')->toArray();
            
            if (!empty($exf_id_list))
            {
                DB::table('external_users_filled_response')->whereIn('external_user_form_id', $exf_id_list)->delete();
            }
            
            DB::table('external_users_forms')->where('sub_form_id', '=', $sb_id)->delete();
            
            DB::table('internal_users_filled_response')->where('sub_form_id', '=', $sb_id)->delete();
            DB::table('user_forms')->where('sub_form_id', '=', $sb_id)->delete();
            
            DB::table('sub_forms')->where('id', '=', $sb_id)->delete();
        }
        

        return response()->json(['status' => $status, 'msg' => $msg]);
    }
 
    
    
}