<?php

namespace App\Http\Controllers;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Swift_SmtpTransport;
use \Carbon\Carbon;
use Swift_Mailer;
use App\Country;
use App\User;
use Lang;
use Mail;

class UserManagementController extends Controller{

    public function users_management(){
        $user = Auth::user()->id;
        $assigned_permissions = array();
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');

        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);

            }
        }
        if (!in_array('Users Management', $assigned_permissions)) {
            return redirect('dashboard');
        }

        if (Auth::user()->role != 2) {
            return abort('404');
        }
        $id = Auth::user()->id;
        $client_id = Auth::user()->client_id;
        //$user = DB::table('users')->where('client_id',$client_id)->where('role',3)->get();
        // SELECT u.name, a.name FROM `users` u JOIN users a ON u.created_by = a.id

        $user = DB::table('users as u')
            ->select('u.*', DB::raw('a.name as added_by'))
            ->join(DB::raw('users a'), 'u.created_by', '=', 'a.id')
            ->where('u.client_id', $client_id)
            ->orderBy('u.id', 'DESC')
            ->where('u.role', 3)->get();

        // dd($user);

        return view('users_management', compact('user'));
    }

    public function add_user(){
        return view('add_user');
    }

    public function edit_user($id){
        $user = DB::table('users')->where('id', $id)->first();
        $client_id = $user->client_id;

        // $administrator = DB::table('users')->where('id',$client_id)->first();

        $company_id = Auth::user()->client_id;

        if ($client_id != $company_id) {
            return abort('404');
        }

        $company_name1 = DB::table('users')->where('id', $company_id)->first();
        if ($company_name1 == "") {
            $company_name = $administrator;
        } else {
            $company_name = $company_name1;
        }
        // print_r($company_name);exit;
        return view('edit_user', compact('user', 'company_name'));
    }

    public function change_status(Request $request){
        $data = array(
            "user_type" => $request->input('status'),
        );
        Auth::user()->where("id", $request->input("id"))->update($data);
    }

    public function delete_user(Request $request){
        $id = $request->input("id");
        $data = DB::table('users')->where('id', $id)->first();
        $test = $data->image_name;
        $destinationpath = public_path("img/$test");
        File::delete($destinationpath);

        Auth::user()->where("id", $id)->delete();
    }

    public function store_edit(Request $request,$id){
        //dd($request->all());

        $this->validate($request, [
            'name' => 'required',
        ],
            [
                'name.required' => __('Please provide proper name to proceed.'),
            ]

        );

        $mail_verification = $request['mail_verification'];
        $any = $request->input('email');
        $company_id = Auth::user()->client_id;

        // if ($request->hasFile('images')) {
        //     $request->validate([
        //         'images' => 'dimensions:max_width=800,max_height=600',
        //     ]);

        // $image_size = $request->file('images')->getsize();

        // if ( $image_size > 1000000 ) {
        //     return redirect('add_user')->with('alert', 'Maximum size of Image 1MB!')->withInput();
        // }
        // }

        $test = DB::table('users')->where('id', '=', $id)->first();
        //dd($test);

        if($request->password != null){
            $inputs = [
                'password' => $request->password,
            ];
            $rules = [
                'password' => [
                    'required',
                    'string',
                    'min:8', // must be at least 8 characters in length
                    'regex:/[a-z]/', // must contain at least one lowercase letter
                    'regex:/[A-Z]/', // must contain at least one uppercase letter
                    'regex:/[0-9]/', // must contain at least one digit
                ],
            ];
            $validation = \Validator::make($inputs, $rules);
    
            if ($validation->fails()) {
                return redirect()->back()->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();
            } elseif ($request->password != $request->rpassword) {
                return redirect()->back()->with('alert', __('Password did not match!'))->withInput();
            }
        }

        if ($test){
            $imgname = '';
            // if($request->hasfile('images')){
            //     $request->validate([
            //         'images' => 'dimensions:max_width=800,max_height=600',
            //     ]);
            //     $file=$request->file('images');
            //     $filename = $file->getClientOriginalName();
            //     $ext=$file->getClientOriginalExtension();
            //     $imgname=uniqid().$filename;
            //     $destinationpath=public_path('/img');
            //     $file->move($destinationpath,$imgname);
            // }
            
            if ($request->base_string) {
                if ($request->base_string != null) {
                    $img = $request->base_string;
                    $base = preg_replace('/^data:image\/\w+;base64,/', '', $img);
                    $type = explode(';', $img)[0];
                    $type = explode('/', $type)[1];
                    $file_name = 'image_' . time() . '.' . $type;
                    @list($type, $img) = explode(';', $img);
                    @list(, $img) = explode(',', $img);
                    if ($img != "") {
                        \Storage::disk('public')->put($file_name, base64_decode($img));
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img2/' . $file_name);
                        $imgname = $file_name;
                    }
                }
            }
            if($request->base_string == null){
                $imgname = $test->image_name;
            }
            //print_r($imgname);exit();
            if ($mail_verification == "on") {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "role" => 3,
                    "image_name" => $imgname,
                    "is_email_varified" => 0,
                    "client_id" => $company_id,
                    "created_by" => Auth::user()->id,
                );
            } else {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "role" => 3,
                    "image_name" => $imgname,
                    "is_email_varified" => 1,
                    "client_id" => $company_id,
                    "created_by" => Auth::user()->id,
                );
            }
            if ($request->input('password')) {
                $data['password'] = bcrypt($request->input('password'));
            }

            if ($request->input('id')) {
                User::where("id", $request->input("id"))->update($data);
                $insert_id = $request->input("id");
            } else {
                $insert_id = User::insertGetId($data);
                $all_permissions = DB::table('module_permissions')->pluck('module')->toArray();
                $all_permissions_string = implode(",", $all_permissions);
                DB::table('module_permissions_users')->insert([
                    'user_id' => $insert_id,
                    'allowed_module' => $all_permissions_string,
                ]);

            }
            \Session::flash('success', Lang::get('general.success_message'));
            return redirect('users_management');
        } else {
            return redirect()->back()->with('alert', __('Wrong ID'))->withInput();
        }
    }

    public function store_user(Request $request){
        // dd($request->all());

        $this->validate($request, [
            'name' => 'required',
        ],
            [
                'name.required' => __('Please provide proper name to proceed.'),
            ]

        );

        $mail_verification = $request['mail_verification'];
        $value = $request['optradio'];
        $any = $request->input('email');
        $company_id = Auth::user()->client_id;

        // if ($request->hasFile('images')) {
        //     $request->validate([
        //         'images' => 'dimensions:max_width=800,max_height=600',
        //     ]);

        // $image_size = $request->file('images')->getsize();

        // if ( $image_size > 1000000 ) {
        //     return redirect('add_user')->with('alert', 'Maximum size of Image 1MB!')->withInput();
        // }
        // }

        $test = DB::table('users')->where('email', '=', $any)->first();

        $inputs = [
            'password' => $request->password,
        ];
        $rules = [
            'password' => [
                'required',
                'string',
                'min:8', // must be at least 8 characters in length
                'regex:/[a-z]/', // must contain at least one lowercase letter
                'regex:/[A-Z]/', // must contain at least one uppercase letter
                'regex:/[0-9]/', // must contain at least one digit
            ],
        ];
        $validation = \Validator::make($inputs, $rules);

        if ($validation->fails()) {
            return redirect('add_user')->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();
        } elseif ($request->password != $request->rpassword) {
            return redirect('add_user')->with('alert', __('Password did not match!'))->withInput();
        } elseif (empty($test)) {
            $imgname = '';
            // if($request->hasfile('images')){
            //     $request->validate([
            //         'images' => 'dimensions:max_width=800,max_height=600',
            //     ]);
            //     $file=$request->file('images');
            //     $filename = $file->getClientOriginalName();
            //     $ext=$file->getClientOriginalExtension();
            //     $imgname=uniqid().$filename;
            //     $destinationpath=public_path('/img');
            //     $file->move($destinationpath,$imgname);
            // }
            
            if ($request->base_string) {
                if ($request->base_string != null) {
                    $img = $request->base_string;
                    $base = preg_replace('/^data:image\/\w+;base64,/', '', $img);
                    $type = explode(';', $img)[0];
                    $type = explode('/', $type)[1];
                    $file_name = 'image_' . time() . '.' . $type;
                    @list($type, $img) = explode(';', $img);
                    @list(, $img) = explode(',', $img);
                    if ($img != "") {
                        \Storage::disk('public')->put($file_name, base64_decode($img));
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img2/' . $file_name);
                        $imgname = $file_name;
                    }
                }
            }
            // print_r($imgname);exit();
            if ($mail_verification == "on") {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "company" => $request->input('company'),
                    "role" => 3,
                    "image_name" => $imgname,
                    "is_email_varified" => 0,
                    "client_id" => $company_id,
                    "created_by" => Auth::user()->id,
                );
            } else {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "company" => $request->input('company'),
                    "role" => 3,
                    "image_name" => $imgname,
                    "is_email_varified" => 1,
                    "client_id" => $company_id,
                    "created_by" => Auth::user()->id,
                );
            }
            if ($request->input('password')) {
                $data['password'] = bcrypt($request->input('password'));
            }

            if ($request->input('id')) {
                User::where("id", $request->input("id"))->update($data);
                $insert_id = $request->input("id");
            } else {
                $insert_id = User::insertGetId($data);
                $all_permissions = DB::table('module_permissions')->pluck('module')->toArray();
                $all_permissions_string = implode(",", $all_permissions);
                DB::table('module_permissions_users')->insert([
                    'user_id' => $insert_id,
                    'allowed_module' => $all_permissions_string,
                ]);

            }
            \Session::flash('success', Lang::get('general.success_message'));
            return redirect('users_management');
        } else {
            return redirect('add_user')->with('alert', __('Email already exists!'))->withInput();
        }
    }

    public function permissions($id){
        // dd('sad');
        $granted_permissions;
        $granted_permissions = DB::table('module_permissions_users')->where('user_id', $id)->first();
        //dd($granted_permissions);
        if ($granted_permissions == null) {
            $granted_permissions = [' ', ' '];
            //dd($granted_permissions);
        } elseif ($granted_permissions != null) {
            $granted_permissions = explode(',', $granted_permissions->allowed_module);
            //dd($granted_permissions);
        }
        $permissions = DB::table('module_permissions')->pluck('module');
        //dd($permissions);
        return view('org-user-permission.add-remove-permission', compact('permissions', 'granted_permissions', 'id'));
    }

    public function permissions_store(Request $request){
        // dd($request->all());
        $is_assigned_any_permissions = DB::table('module_permissions_users')->where('user_id', $request->id)->first();
        if ($is_assigned_any_permissions != null) {
            $data = $request->permiss;
            // dd($data);
            if ($data == null) {
                $data = ['nodata , nodata'];
            }
            $new = implode(',', $data);
            $result = DB::table('module_permissions_users')->where('user_id', $request->id)->update([
                "user_id" => $request->id,
                "allowed_module" => $new,
            ]);

            \Session::flash('success', Lang::get('Permission set for user'));
            return redirect('users_management');
            // dd($result . 'record updated');
        } elseif ($is_assigned_any_permissions == null) {
            # code...
            $data = $request->permiss;
            if ($data == null) {
                $data = ['nodata , nodata'];
            }
            $new = implode(',', $data);
            $result = DB::table('module_permissions_users')->insert([
                "user_id" => $request->id,
                "allowed_module" => $new,
            ]);
            // dd('permission set');
            \Session::flash('success', Lang::get('Permission set for user'));
            return redirect('users_management');

        }

    }
}
