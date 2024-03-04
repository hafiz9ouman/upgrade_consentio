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

class FormSettings extends Controller
{
    
    public function unlock_form (Request $request)
    {
        $table          = 'external_users_forms';
        $form_link_attr = 'form_link';
        $link           = $request->input('link');
        $lock_status    = $request->input('lock_status');
        
        if ($request->input('user_type') == 'in') {
            $table     = 'user_forms';
            $form_link_attr = 'form_link_id';
        }
        
        DB::table($table)->where($form_link_attr, $link)->update(['is_locked' => $lock_status]);
            
        return response()->json(['status' => 'success', 'msg' => 'status changed']);
    }
    
    public function change_form_access (Request $request)
    {
        $table    = 'external_users_forms';
        $form_link_attr = 'form_link';
        $link = $request->input('link');
        $action = $request->input('action');
        
        if ($request->input('user_type') == 'in') {
            $table     = 'user_forms';
            $form_link_attr = 'form_link_id';
        }
 
        DB::table($table)->where($form_link_attr, $link)->update(['is_accessible' => $action]);
            
        return response()->json(['status' => 'success', 'msg' => 'status changed']);
    }    
    
}