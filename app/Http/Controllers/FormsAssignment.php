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
    
    public function change_lock_status (Request $request)
    {
        $table    = 'external_users_forms';
        $form_link_attr = 'form_link';
        $link = $request->input('link');
        
        if ($request->input('user_type') == 'in') {
            $table     = 'user_form_links';
            $form_link_attr = 'form_link_id';
        }
 
        $action = 0;
        if ($request->input('action') == 'lock') {
            $action = 1;
        }
        
        DB::table($table)->where($form_link_attr, $link)->update(['is_locked' => $action]);
            
        return response()->json(['status' => 'success', 'msg' => __('status changed')]);
    }
    
}