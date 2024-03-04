<?php

namespace App\Http\Controllers;

use App;
use App\Helper\Helper;
use App\User;
use App\Where;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Lang;
use Session;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function data_element()
    {

        $data = DB::table("assets_data_elements as ade")
            ->select("ade.*", "s.section_name", "dc.classification_name_en")
            ->join("sections as s", "ade.section_id", "s.id")->join("data_classifications as dc", "ade.d_classification_id", "dc.id")->orderby('id', "desc")
            ->whereNull('ade.owner_id')
            ->get();

        $section = DB::table("sections")->get();
        $dc_result = DB::table("data_classifications")->get();

        return view("admin.users.data_element", [
            "data" => $data,
            "section" => $section,
            "dc_result" => $dc_result,
        ]);
    }

    public function edit_data_element_group($id)
    {
        $data = DB::table("assets_data_elements as ade")
            ->select("ade.*", "s.section_name", "dc.classification_name_en")
            ->join("sections as s", "ade.section_id", "s.id")->join("data_classifications as dc", "ade.d_classification_id", "dc.id")->where("ade.id", $id)->orderby('id', "desc")
            ->get();
        $section = DB::table("sections")->get();
        $dc_result = DB::table("data_classifications")->get();
        return view('admin.users.edit_data_element_group', [
            "data" => $data,
            "section" => $section,
            "dc_result" => $dc_result,
        ]);
    }
    public function update_data_element_group(Request $req)
    {
        $req->validate([
            "name" => "required",
            "element_group" => "required",
            "d_c_name" => "required",
        ]);
        DB::table("assets_data_elements")->where("id", $req->id)->update([
            "name" => $req->name,
            "section_id" => $req->element_group,
            "d_classification_id" => $req->d_c_name,
        ]);
        return redirect("/data_element")->with("success", "Data Has Successfully Updated");
    }

    public function permissions($id)
    {
        $granted_permissions;
        $granted_permissions = DB::table('module_permissions_users')->where('user_id', $id)->first();
        if ($granted_permissions == null) {
            $granted_permissions = [' ', ' '];
        } elseif ($granted_permissions != null) {
            $granted_permissions = explode(',', $granted_permissions->allowed_module);
        }
        $permissions = DB::table('module_permissions')->select('module', 'module_title')->get();


        // print("<pre>");
        // print_r($permissions);
        // exit;

        $user = Auth::user()->role;
        if ($user == 1) {
            $user_type = 'admin';
        } else {
            $user_type = 'client';
        }
        return view('admin.users.permission_add_remove', compact('permissions', 'granted_permissions', 'user_type', 'id'));
    }

    public function permissions_store(Request $request){

        // return $request->all();
        $is_assigned_any_permissions = DB::table('module_permissions_users')->where('user_id', $request->id)->first();
        if ($is_assigned_any_permissions != null) {
            $data = $request->permiss;
            if ($data == null) {
                $data = ['nodata , nodata'];
            }
            $new = implode(',', $data);
            $result = DB::table('module_permissions_users')->where('user_id', $request->id)->update([
                "user_id" => $request->id,
                "allowed_module" => $new,
            ]);
            \Session::flash('success', Lang::get('Permission set for user'));
            return redirect('admin');
        } elseif ($is_assigned_any_permissions == null) {
            $data = $request->permiss;
            if ($data == null) {
                $data = ['nodata , nodata'];
            }
            $new = implode(',', $data);
            $result = DB::table('module_permissions_users')->insert([
                "user_id" => $request->id,
                "allowed_module" => $new,
            ]);
            \Session::flash('success', Lang::get('Permission set for user'));
            return redirect('admin');
        }
    }

    public function index()
    {
        if (Auth::user()->role == 1) {
            $users = User::where('role', 2)->get();
            return view('admin.users.home', compact("users"));

        } elseif (Auth::user()->role == 2 || (Auth::user()->role == 3 && Auth::user()->user_type == 1)) {
            $lat_lng = DB::table('assets')->where('lat', '!=', '')->get();
            foreach ($lat_lng as $lat_val) {
                $lat_value[] = array($lat_val->country, $lat_val->lng, $lat_val->lat);
            }
            return redirect('dashboard', compact('lat_value'));
        }
    }

    public function site_admins()
    {
        if (Auth::user()->role == 1) {
            $users = User::where('role', 1)->get();
            return view('admin.users.site_admins', compact('users'));

        } else {
            return abort('404');
        }
    }

    public function add_admin()
    {
        if (Auth::user()->role == 1) {
            return view('admin.users.add_admin', compact('users'));
        } else {
            return abort('404');
        }
    }

    public function add_admin_act(Request $request)
    {
        $email = $request->input('email');
        $name = $request->input('name');
        $pswrd = $request->input('password');

        $test = DB::table('users')->where('email', '=', $email)->first();
        if ($request->hasFile('images')) {
            $image_size = $request->file('images')->getsize();
            $request->validate([
                'images' => 'dimensions:max_width=800,max_height=600',
            ]);
            if ($image_size > 1000000) {
                return redirect('users/add')->with('alert', 'Maximum size of Image 1MB!')->withInput();
            }
        }
        $inputs = [
            'password' => $pswrd,
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
            return redirect('users/add')->with('alert', __(__('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!')))->withInput();

        } elseif ($pswrd != $request->rpassword) {
            return redirect('users/add')->with('alert', __(__('Password did not match!')))->withInput();

        } elseif (empty($test)) {
            $imgname = '';
            if ($request->hasfile('images')) {
                $file = $request->file('images');
                $filename = str_replace(' ', '', $file->getClientOriginalName());
                $ext = $file->getClientOriginalExtension();
                $imgname = uniqid() . $filename . '.' . $ext;
                $destinationpath = public_path('/img');
                $file->move($destinationpath, $imgname);
            }
            if ($slider == "on") {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "role" => 2,
                    "image_name" => $imgname,
                    "tfa" => 1,
                    "client_id" => 0,
                    "created_by" => Auth::user()->id,
                );

            } else {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "role" => 2,
                    "image_name" => $imgname,
                    "tfa" => 0,
                    "client_id" => 0,
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
            }
            \Session::flash('success', Lang::get('general.success_message'));
            return redirect('admin');
        } else {
            return redirect('users/add')->with('alert', __('Email already exists!'))->withInput();
        }
    }

    public function edit($id)
    {
        if (Auth::user()->role == 1) {
            $user = User::find($id);
            $client = DB::table('users')->where('role', 4)->get();
        
            if ($user) {
                return view('admin.users.edit', compact("user","client"));
            } else {
                return redirect('/');
            }
        } else {
            return redirect('dashboard');
        }
    }

    public function addUser($user_id = '')
    {
        $fixed_company = false;
        $client = DB::table('users')->where('role', 4)->get();
        if (!empty($user_id)) {
            $client = $client->where('id', $user_id)->first();
            $fixed_company = true;
        }

        return view('admin.users.addUser', compact('client', 'fixed_company'));
    }

    public function addClient()
    {
        return view('admin.users.addClient');
    }

    public function store(Request $request)
    {
        $this->validate(
            $request,
            [
                'name' => 'required',
                'email' => 'required',
            ],
            [
                'name.required' => __('Please provide proper name to proceed'),
                'email.required' => __('Please provide proper email to proceed'),
            ]
        );
        $mail_verification = $request['mail_verification'];
        $clientid = Input::get('team');
        $value = $request['optradio'];
        $any = $request->input('email');
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
            return redirect('users/add')->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();
        } elseif ($request->password != $request->rpassword) {
            return redirect('users/add')->with('alert', __('Password did not match!'))->withInput();
        } elseif (empty($test)) {
            $imgname = null;
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
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                        $imgname = $file_name;
                    }
                }
            }
            if ($mail_verification == "on") {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "is_blocked" => $request->input('is_blocked'),
                    "role" => 2,
                    "image_name" => $imgname,
                    "is_email_varified" => 0,
                    "client_id" => $clientid,
                    "created_by" => Auth::user()->id,
                );
            } else {
                $data = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "is_blocked" => $request->input('is_blocked'),
                    "role" => 2,
                    "image_name" => $imgname,
                    "is_email_varified" => 1,
                    "client_id" => $clientid,
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
            return redirect('admin');
        } else {
            return redirect('users/add')->with('alert', __('Email already exists!'))->withInput();
        }
    }

    public function clientStore(Request $request)
    {
        $slider = $request['slider'];
        $value = $request['optradio'];
        $any = $request->input('email');
        $test = DB::table('users')->where('email', '=', $any)->first();
        $company = $request->input('company');
        $company_check = DB::table('users')->where('company', '=', $company)->first();
        $file_name = null;
        if ($company_check) {
            return redirect('client/add')->with('alert', __('Enter the Unique Company Name!'))->withInput();
        } else {
            $imgname = '';
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
                    File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                }
            }

            $data = [
                "name" => $request->input('company'),
                "company" => $request->input('company'),
                "website" => $request->input('website'),
                "phone" => $request->input('phone'),
                "role" => 4,
                "image_name" => $file_name,
                "tfa" => 0,
                "created_by" => Auth::user()->id,
            ];
        }
        if ($request->input('id')) {
            User::where("id", $request->input("id"))->update($data);
            $insert_id = $request->input("id");
        } else {
            $insert_id = User::insertGetId($data);
            $admin_evaluation_rating = DB::table("evaluation_rating")->whereNull('owner_id')->orderBy('id', "ASC")->get();
            foreach ($admin_evaluation_rating as $rating) {
                DB::table("evaluation_rating")->insert([
                    "assessment"    => $rating->assessment,
                    "rating"        => $rating->rating,
                    "color"         => $rating->color,
                    "text_color"    => $rating->text_color,
                    "owner_id"      => $insert_id,
                    "rate_level"    => $rating->rate_level,
                ]);
            }
            $admin_default_clasification = DB::table("data_classifications")->whereNull('organization_id')->orderBy('id', "ASC")->take(5)->get();
            foreach ($admin_default_clasification as $clasification) {
                $data_classifications_against_org = DB::table("data_classifications")->insertGetId([
                    "classification_name_en"    => $clasification->classification_name_en,
                    "classification_name_fr"    => $clasification->classification_name_fr,
                    'confidentiality_level'     => $clasification->confidentiality_level,
                    "organization_id"           => $insert_id,
                ]);
                $assets_data_elements = DB::table('assets_data_elements')->where('d_classification_id', $clasification->id)->get();
                foreach ($assets_data_elements as $element) {
                    DB::table('assets_data_elements')->insertGetId([
                        "name" => $element->name,
                        "section_id" => $element->section_id,
                        "d_classification_id" => $data_classifications_against_org,
                        "owner_id" => $insert_id,
                    ]);
                }
            }
        }

        $client_id = $insert_id;

        $all_forms = DB::table('forms')->where('id', '<=', 14)->get();

        $expiry_time = date('Y-m-d H:i:s', strtotime("+10 days"));

        $insert_data = [];

        foreach ($all_forms as $form) {
            $insert_data = ['client_id' => $client_id, 'form_id' => $form->id];

            if ($form->code == 'f10') {
                DB::table('sub_forms')->insert([
                    'title' => $form->title,
                    'client_id' => $client_id,
                    'parent_form_id' => $form->id,
                    'expiry_time' => $expiry_time,
                ]);
            }
            DB::table('client_forms')->insert($insert_data);
        }

        \Session::flash('success', Lang::get('general.success_message'));
        return redirect('company');
    }

    public function edit_store(Request $request, $id)
    {
        // dd($request->all());
        // if (!empty($request->base_string)) {
        //     dd("ok");
        // }
        // $arr = explode('/', mime_content_type($request->base_string));
        // Session::forget('data');

        $this->validate(
            $request,
            [
                'name' => 'required',
            ],
            [
                'name.required' => __('Please provide proper name to update'),
            ]
        );
        $mail_verification = $request['mail_verification'];
        $data = User::where("id", $request->input("id"))->first();

        $test = $data->image_name;
        $inputs = [
            'password' => $request->password,
        ];
        $rules = [
            'password' => [
                'string',
                'min:8', // must be at least 8 characters in length
                'regex:/[a-z]/', // must contain at least one lowercase letter
                'regex:/[A-Z]/', // must contain at least one uppercase letter
                'regex:/[0-9]/', // must contain at least one digit
            ],
        ];
        $validation = \Validator::make($inputs, $rules);

        if ($request->password != "") {
            if ($validation->fails()) {
                if (isset($_POST['mail_verification']) && $_POST['mail_verification'] == 'on') {
                    $data = $request->all();
                    $data['mail_verification'] = 'on';
                } else {
                    $data = $request->all();
                    $data['mail_verification'] = 'off';
                }

                Session::put('data', $data);
                return redirect('users/edit/' . $id)->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'));

            } elseif ($request->password != $request->rpassword) {
                $data = $request->all();
                return redirect('users/edit/' . $id)->with('alert', __('Password did not match!'));

            } else {
                if ($request->base_string) {
                    $img = $request->base_string;
                    $ext = explode('/', mime_content_type($request->base_string))[1];
                    $base = preg_replace('/^data:image\/\w+;base64,/', '', $img);
                    $type = explode(';', $img)[0];
                    $type = explode('/', $type)[1];
                    $file_name = 'image_' . time() . '.' . $type;
                    @list($type, $img) = explode(';', $img);
                    @list(, $img) = explode(',', $img);
                    if ($img != "") {
                        \Storage::disk('public')->put($file_name, base64_decode($img));
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                        $imgname = $file_name;
                    }
                } else {
                    $imgname = $test;
                }
                $record = array(
                    "name" => $request->input('name'),
                    "email" => $request->input('email'),
                    "image_name" => $imgname,
                );
                $record['is_blocked'] = $request->is_blocked;
                if ($request->is_blocked == 'No') {
                    $record['login_attempts'] = 0;
                }

                if ($request->input('password')) {
                    $record['password'] = bcrypt($request->input('password'));
                }
                if ($request->input('id')) {

                    User::where("id", $request->input("id"))->update($record);
                    $insert_id = $request->input("id");
                } else {
                    $insert_id = User::insertGetId($record);
                }
                $fa = User::where("id", $request->input("id"))->first();
                if ($fa->tfa == 0) {
                    DB::table('password_securities')->where('user_id', $id)->delete();
                }
                return redirect("/admin");
            }
        } else {
            if ($request->password != $request->rpassword) {
                return redirect('users/edit/' . $id)->with('alert', __('Password did not match!'));
            } else {
                if ($request->base_string) {
                    $img = $request->base_string;
                    $base = preg_replace('/^data:image\/\w+;base64,/', '', $img);
                    $type = explode(';', $img)[0];
                    $type = explode('/', $type)[1];
                    $file_name = 'image_' . time() . '.' . $type;
                    @list($type, $img) = explode(';', $img);
                    @list(, $img) = explode(',', $img);
                    if ($img != "") {
                        \Storage::disk('public')->put($file_name, base64_decode($img));
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                        $imgname = $file_name;
                    }
                } else {
                    $imgname = $test;
                }

                if ($mail_verification == "on") {
                    $record = array(
                        "name" => $request->input('name'),
                        "email" => $request->input('email'),
                        "is_blocked" => $request->input('is_blocked'),
                        "image_name" => $imgname,
                        "is_email_varified" => 0,
                    );
                } else {
                    $record = [
                        "name" => $request->input('name'),
                        "email" => $request->input('email'),
                        "is_blocked" => $request->input('is_blocked'),
                        "image_name" => $imgname,
                        "is_email_varified" => 1,
                    ];
                }
                $record['is_blocked'] = $request->is_blocked;
                if ($request->is_blocked == 'No') {
                    $record['login_attempts'] = 0;
                }

                if ($request->input('password')) {
                    $record['password'] = bcrypt($request->input('password'));
                }
                if ($request->input('id')) {
                    User::where("id", $request->input("id"))->update($record);
                    $insert_id = $request->input("id");

                } else {
                    $insert_id = User::insertGetId($record);

                }

                $fa = User::where("id", $request->input("id"))->first();
                if ($fa->tfa == 0) {
                    DB::table('password_securities')->where('user_id', $id)->delete();
                }
                return redirect("/admin");
            }
        }
    }

    public function change_status(Request $request)
    {
        $data = [
            "status" => $request->input('status'),
        ];
        User::where("id", $request->input("id"))->update($data);
    }

    public function destroy(Request $request)
    {
        $id = $request->input("id");
        $data = DB::table('users')->where('id', $id)->first();
        $test = $data->image_name;
        $destinationpath = public_path("img/$test");
        File::delete($destinationpath);
        User::where("id", $id)->delete();
    }
    // dashboard new
    public function dashboard_2()
    {
        if (auth()->user()->role == 1) {
            return redirect()->back();
        }
        $client_id = auth()->user()->client_id;
        $lat_value[] = '';
        $lat_detail[] = '';

        $lat_lng = DB::table('assets')->where('lat', '!=', '')
            ->where('client_id', $client_id)
            ->get();

        if ($lat_lng != '') {
            foreach ($lat_lng as $lat_val) {
                $lang = $lat_val->lng;
                $lng = number_format((float) $lang, 6, '.', '');
                $lng = floatval($lng);
                $late = $lat_val->lat;
                $lat = number_format((float) $late, 6, '.', '');
                $lat = floatval($lat);
                $lat_value[] = array($lat_val->country, $lat, $lng);
                $lat_detail[] = array($lat_val->country, $lat_val->city, $lat_val->state, $lat_val->name, $lat_val->hosting_provider, $lat_val->asset_type);
            }
        }
        if (Auth::user()->role != 3) {
            if (Auth::user()->role == 3 && Auth::user()->user_type != '1') {
                return redirect(route('client_user_subforms_list'));
            }
        }
        $id = Auth::user()->client_id;
        // ======================================== //
        /* PENDING SAR REQUEST ALERTS */
        //SELECT *, DATEDIFF(due_date, NOW()) FROM `sar_requests`
        $incomplete_sar_requests = DB::table('sar_requests')
            ->selectRaw('*, DATEDIFF(due_date, NOW()) AS days_left')
            ->where('status', 0)
            ->where('client_id', $id)
            ->where(DB::raw('DATEDIFF(due_date, NOW())'), '<=', '10')
            ->orderBy('days_left');

        $days_left = $incomplete_sar_requests->limit(1)->pluck('days_left')->first();
        $incomplete_sar_requests_counts = $incomplete_sar_requests->count();
        $sar_pending_request_info = [
            'days_left' => $days_left,
            'request_count' => $incomplete_sar_requests_counts,
        ];
        /* PENDING SAR REQUEST ALERTS */
        // ========================================= //

        // =========================================//
        /* DASHBOARD COUNTS */

        $org_users_count = DB::table('users')->where('client_id', $id)->count();
        $ext_users_count = DB::table('external_users_forms')->where('client_id', $id)->distinct('email')->count();
        $total_users = $org_users_count + $ext_users_count;

        $subforms_count = DB::table('sub_forms')->where('client_id', $id)->where('title', '!=', 'SAR Form')->count();

        $forms_count = DB::table('client_forms')->where('client_id', $id)->count();

        $ext_sent_forms_count = DB::table('external_users_forms')->where('client_id', $id)->count();
        $int_sent_forms_count = DB::table('user_forms')->where('client_id', $id)->count();
        $total_shared_forms = $ext_sent_forms_count + $int_sent_forms_count;

        $ext_completed_forms_count = DB::table('external_users_forms')->where('client_id', $id)->where('is_locked', 1)->count();
        $int_completed_forms_count = DB::table('user_forms')->where('client_id', $id)->where('is_locked', 1)->count();
        $total_completed_forms = $ext_completed_forms_count + $int_completed_forms_count;

        $e_incomplete_forms_count = DB::table('external_users_forms')->where('client_id', $id)->where('is_locked', 0)->count();
        $i_incomplete_forms_count = DB::table('user_forms')->where('client_id', $id)->where('is_locked', 0)->count();
        $total_incomplete_forms = $e_incomplete_forms_count + $i_incomplete_forms_count;

        $int_sar_completed_forms = DB::table('user_forms')->where('user_forms.client_id', $id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('user_forms.is_locked', '=', '1')
            ->count();

        $ext_sar_completed_forms = DB::table('external_users_forms')->where('external_users_forms.client_id', $id)
            ->join('sub_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('external_users_forms.is_locked', '=', '1')
            ->count();

        $total_sar_completed_forms = $int_sar_completed_forms + $ext_sar_completed_forms;

        $int_sar_incomplete_forms = DB::table('user_forms')
            ->join('sub_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('user_forms.is_locked', '=', '0')
            ->where('user_forms.client_id', $id)
            ->count();

        $ext_sar_incomplete_forms = DB::table('external_users_forms')
            ->join('sub_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('external_users_forms.is_locked', '=', '0')
            ->where('external_users_forms.client_id', $id)
            ->count();

        // SELECT sub_forms.id as subform_id FROM `sub_forms` JOIN forms on sub_forms.parent_form_id = forms.id WHERE type = 'sar' and client_id = 120
        $sar_subform_id = DB::table('sub_forms')->select('sub_forms.id as subform_id')->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')->where('type', '=', 'sar')->where('client_id', '=', Auth::user()->client_id)->pluck('subform_id')->first();

        $total_sar_incomplete_forms = $int_sar_incomplete_forms + $ext_sar_incomplete_forms;

        $total_incident_register_forms = DB::table('incident_register')->where('organization_id', $id)->count();

        // add filter by client id
        $external_user_activities = DB::table('external_users_filled_response')
            ->whereIn('external_user_form_id', DB::table('external_users_forms')
                    ->where('client_id', $id)->pluck('id'))
            ->whereIn('question_id', DB::table('questions')
                    ->where('question', 'like', '%What activity are you assessing%')->pluck('id'))
            ->distinct('question_response')
            ->count();
        // add filter by client id
        $internal_user_activities = DB::table('internal_users_filled_response')
            ->whereIn('user_form_id', DB::table('user_forms')
                    ->where('client_id', $id)->pluck('id'))
            ->whereIn('question_id', DB::table('questions')
                    ->where('question', 'like', '%What activity are you assessing%')->pluck('id'))
            ->distinct('question_response')
            ->count();

        $total_activities = $external_user_activities + $internal_user_activities;

        $sar_completed_requests = DB::table('sar_requests')
            ->where('status', 1)
            ->where('client_id', $id)
            ->count();

        $sar_incomplete_requests = DB::table('sar_requests')
            ->where('status', 0)
            ->where('client_id', $id)
            ->count();

        /* DASHBOARD COUNTS */
        // ================================================ //

        // ================================================ //
        /* BAR CHART */

        // SELECT sub_forms.title, forms.title, user_forms.user_id, COUNT(user_forms.user_id) AS user_count
        // from   sub_forms
        // JOIN   user_forms ON user_forms.sub_form_id = sub_forms.id
        // JOIN   forms      ON sub_forms.parent_form_id = forms.id
        // GROUP
        // BY     forms.title

        $num_of_internal_users = DB::table('sub_forms')
            ->join('user_forms', 'user_forms.sub_form_id', '=', 'sub_forms.id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('user_forms.client_id', $id)
            ->select(DB::raw('forms.id, sub_forms.title as subform_title, forms.title as form_title, user_forms.user_id, COUNT(user_forms.user_id) AS user_count'))
            ->groupBy('forms.id')
            ->orderBy('forms.id')
            ->get();

        // SELECT sub_forms.title, forms.title, external_users_forms.user_email, COUNT(external_users_forms.user_email) AS user_count
        // from   sub_forms
        // JOIN   external_users_forms ON external_users_forms.sub_form_id = sub_forms.id
        // JOIN   forms      ON sub_forms.parent_form_id = forms.id
        // WHERE  external_users_forms.client_id = 120
        // GROUP
        // BY     forms.title

        $num_of_external_users = DB::table('sub_forms')
            ->join('external_users_forms', 'external_users_forms.sub_form_id', '=', 'sub_forms.id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('external_users_forms.client_id', $id)
            ->select(DB::raw('forms.id, sub_forms.title as subform_title, forms.title as form_title, external_users_forms.user_email, COUNT(external_users_forms.user_email) AS user_count'))
            ->groupBy('forms.id')
            ->orderBy('forms.id')
            ->get();

        $num_of_form_users = [];

        foreach ($num_of_internal_users as $key => $int_form_info) {
            $num_of_form_users[$int_form_info->id]['name'] = $int_form_info->form_title;
            $num_of_form_users[$int_form_info->id]['internal'] = $int_form_info->user_count;
            $num_of_form_users[$int_form_info->id]['total'] = $int_form_info->user_count;
        }

        foreach ($num_of_external_users as $key => $ext_form_info) {
            $num_of_form_users[$ext_form_info->id]['name'] = $ext_form_info->form_title;
            $num_of_form_users[$ext_form_info->id]['external'] = $ext_form_info->user_count;
            $num_of_form_users[$ext_form_info->id]['total'] = $ext_form_info->user_count + ((isset($num_of_form_users[$ext_form_info->id]['total'])) ? ($num_of_form_users[$ext_form_info->id]['total']) : (0));
        }

        /* BAR CHART */
        // ============================================ //

        // ============================================ //
        /* STATS TABLE */

        // SELECT   forms.id, forms.title,
        //          COUNT(sub_forms.id) AS total_forms,
        //          SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) AS completed,
        //          SUM(CASE WHEN is_locked = 0 THEN 1 ELSE 0 END) AS not_completed
        // FROM     forms
        // JOIN     sub_forms  ON forms.id     = sub_forms.parent_form_id
        // JOIN     user_forms ON sub_forms.id = user_forms.sub_form_id
        // group BY forms.id
        // ORDER by forms.title

        $int_user_forms = DB::table('forms')
            ->join('sub_forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->join('user_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
            ->where('user_forms.client_id', '=', $id)
            ->select(DB::raw('forms.id, forms.title,
                                              COUNT(sub_forms.id) AS subforms_count,
                                              SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) AS completed,
                                              SUM(CASE WHEN is_locked = 0 THEN 1 ELSE 0 END) AS not_completed'))
            ->groupBy('forms.id')
            ->orderBy('forms.title')
            ->get();

        $ext_user_forms = DB::table('forms')
            ->join('sub_forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->join('external_users_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
            ->where('external_users_forms.client_id', '=', $id)
            ->select(DB::raw('forms.id, forms.title,
                                              COUNT(sub_forms.id) AS subforms_count,
                                              SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) AS completed,
                                              SUM(CASE WHEN is_locked = 0 THEN 1 ELSE 0 END) AS not_completed'))
            ->groupBy('forms.id')
            ->orderBy('forms.title')
            ->get();

        $form_completion_stats = [];

        foreach ($int_user_forms as $key => $int_user_form) {
            $form_completion_stats[$int_user_form->id]['form_name'] = $int_user_form->title;
            $form_completion_stats[$int_user_form->id]['internal'] = ['completed' => $int_user_form->completed, 'not_completed' => $int_user_form->not_completed];
        }

        foreach ($ext_user_forms as $key => $ext_user_form) {
            $form_completion_stats[$ext_user_form->id]['form_name'] = $ext_user_form->title;
            $form_completion_stats[$ext_user_form->id]['external'] = ['completed' => $ext_user_form->completed, 'not_completed' => $ext_user_form->not_completed];
        }

        // SELECT forms.title, count(DISTINCT sub_forms.id) FROM `forms`
        // JOIN sub_forms ON forms.id = sub_forms.parent_form_id
        // WHERE client_id = 159
        // GROUP by forms.id

        $main_forms_count = DB::table('forms')
            ->join('sub_forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->where('client_id', '=', $id)
            ->select(DB::raw('forms.id, forms.title,
                                              COUNT(sub_forms.id) AS subforms_count'))
            ->groupBy('forms.id')
            ->get();

        foreach ($main_forms_count as $fcount) {
            if (isset($form_completion_stats[$fcount->id])) {
                $form_completion_stats[$fcount->id]['subforms_count'] = $fcount->subforms_count;
            }
        }

        /* STATS TABLE */
        // ====================================================== //

        return view('home-2', compact(
            "total_users",
            "org_users_count",
            "ext_users_count",
            "subforms_count",
            "forms_count",
            "total_shared_forms",
            "total_completed_forms",
            "total_incomplete_forms",
            "num_of_form_users",
            "form_completion_stats",
            "total_activities",
            "sar_subform_id",
            "total_sar_completed_forms",
            "total_sar_incomplete_forms",
            "total_incident_register_forms",
            "sar_pending_request_info",
            "sar_completed_requests",
            "sar_incomplete_requests",
            "lat_value",
            "lat_detail"
        ));
    }

    public function dashboard()
    {
        $final = null;
        $user = Auth::user()->id;
        $assigned_permissions = [];
        $data = DB::table('module_permissions_users')->where('user_id', $user)->pluck('allowed_module');
        if ($data != null) {
            foreach ($data as $value) {
                $assigned_permissions = explode(',', $value);
            }
        }
        if (!in_array('Dashboard', $assigned_permissions)) {
            return redirect('profile/' . auth()->user()->id);
        }
        if (auth()->user()->role == 1) {
            return redirect()->back();
        }
        $client_id = Auth::user()->client_id;
        $final = null;
        $client_id = Auth::user()->client_id;
        $option_questions = [];
        $piaDpiaRop_ids = [2, 9, 12];

        $filled_questions = DB::table('external_users_forms')->select('*')->join('external_users_filled_response', 'external_users_forms.id', '=', 'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id', $client_id)->pluck('question_key');

        $filled_questions_internal = DB::table('user_forms')->select('*')->join('internal_users_filled_response', 'user_forms.id', '=', 'internal_users_filled_response.user_form_id')->where('user_forms.client_id', $client_id)->pluck('question_key');

        $filled_questions = $filled_questions->merge($filled_questions_internal);

        $question = DB::table('questions')->where('type', 'mc')->wherein('form_id', $piaDpiaRop_ids)->wherein('form_key', $filled_questions)->where(function ($query) {
            return $query
                ->where('question_num', '=', null)
                ->orWhere('question_num', '=', '');
        })
            ->get();

        $data_inv_forms = DB::table('questions')->where('is_data_inventory_question', 1)->pluck('form_id')->unique()->toArray();
        $new_data_inv_questions = DB::table('questions')->where('type', 'mc')->wherein('form_id', $data_inv_forms)->wherein('form_key', $filled_questions)
            ->where('is_data_inventory_question', 1)
            ->where(function ($query) {
                return $query
                    ->where('question_num', '=', null)
                    ->orWhere('question_num', '=', '');
            })
            ->get();

        $question = $question->merge($new_data_inv_questions);
        $question = $question->unique('question');

        $en_opt = [];
        $fr_opt = [];

        foreach ($question as $questions) {
            $data = $questions->options;
            $options_array = explode(",", $data);
            $en_opt[] = array_filter(array_map('trim', $options_array));
        }
        $opt_final_en = array_flatten($en_opt);
        foreach ($question as $questions) {
            $data = $questions->options_fr;
            $options_array = explode(",", $data);
            $fr_opt[] = array_filter(array_map('trim', $options_array));
        }
        $opt_final_fr = array_flatten($fr_opt);

        $opt = null;

        foreach ($question as $value) {

            $option = $value->question;
            $option_fr = $value->question_fr;
            $temporary_question = DB::table('questions')->where('question', $option)->pluck('form_key');
            // $question_response = DB::table('external_users_filled_response')->wherein('question_key' , $temporary_question )->get();

            $question_response = DB::table('external_users_forms')->select('*')->wherein('external_users_filled_response.question_key', $temporary_question)
                ->join('external_users_filled_response', 'external_users_forms.id', '=', 'external_users_filled_response.external_user_form_id')->where('external_users_forms.client_id', $client_id)->get();

            // $question_response2 = DB::table('internal_users_filled_response')->wherein('question_key' , $temporary_question )->get();

            $question_response2 = DB::table('user_forms')->select('*')->wherein('internal_users_filled_response.question_key', $temporary_question)
                ->join('internal_users_filled_response', 'user_forms.id', '=', 'internal_users_filled_response.user_form_id')->where('user_forms.client_id', $client_id)->get();

            if (count($question_response2) > 5) {
                foreach ($question_response2 as $internal_question) {
                    $internal_question->external_user_form_id = $internal_question->user_form_id;
                    $internal_question->user_email = DB::table('users')->where('id', $internal_question->user_id)->pluck('name')->first();
                }
            }
            $question_response = $question_response->merge($question_response2);
            $final_options_array = array();
            $count = 0;
            foreach ($question_response as $resp) {

                $remove_duplicate_options = array();
                $user_responses = explode(',', $resp->question_response);
                foreach ($user_responses as $pickoption) {
                    if (!in_array(trim($pickoption), $final_options_array)) {
                        if (mb_substr($pickoption, 0, 1, "UTF-8") != "{") {
                            if (strlen($pickoption) > 1) {
                                $final_options_array[] = trim($pickoption);
                            }
                        }
                    }
                }
            }

            //bari start

            $ww = [];
            $ee = [];
            foreach ($final_options_array as $qq) {
                $new = array_search($qq, $opt_final_fr);
                if ($new !== false) {
                    $ww[] = $opt_final_en[array_search($qq, $opt_final_fr)];
                }
                if ($new == false) {
                    $ee[] = $opt_final_en[array_search($qq, $opt_final_en)];
                }
                $final_options_array = array_merge($ee, $ww);
            }

            $final_options_array = array_unique($final_options_array);
            //bari end
            $count = count($final_options_array);

            foreach ($final_options_array as $tt) {
                $opt[] = explode(',', $tt);
            }

            $op_count = $count;
            array_push($option_questions, [
                "question_string" => $option,
                "question_string_fr" => $option_fr,
                "op_count" => $op_count,
            ]);
        }

        $final = [];
        if ($opt != null) {
            foreach ($opt as $jugar) {

                $final[] = $jugar[0];
            }
        }

        $total_data_types = count($final);

        $client_id = auth()->user()->client_id;
        $lat_value[] = '';
        $lat_detail[] = '';

        $lat_lng = DB::table('assets')->where('lat', '!=', '')
            ->where('client_id', $client_id)
            ->get();
        $total_assets = count($lat_lng);

        if ($lat_lng != '') {
            foreach ($lat_lng as $lat_val) {
                $lang = $lat_val->lng;
                $lng = number_format((float) $lang, 6, '.', '');
                $lng = floatval($lng);
                $late = $lat_val->lat;
                $lat = number_format((float) $late, 6, '.', '');
                $lat = floatval($lat);
                $lat_value[] = [$lat_val->country, $lat, $lng];
                $lat_detail[] = [$lat_val->country, $lat_val->city, $lat_val->state, $lat_val->name, $lat_val->hosting_provider, $lat_val->asset_type];
            }
        }
        if (Auth::user()->role != 3) {
            if (Auth::user()->role == 3 && Auth::user()->user_type != '1') {
                return redirect(route('client_user_subforms_list'));
            }
        }
        $id = Auth::user()->client_id;

        // ================================================== //
        /* PENDING SAR REQUEST ALERTS */

        $incomplete_sar_requests = DB::table('sar_requests')
            ->selectRaw('*, DATEDIFF(due_date, NOW()) AS days_left')
            ->where('status', 0)
            ->where('client_id', $id)
            ->where(DB::raw('DATEDIFF(due_date, NOW())'), '<=', '10')
            ->orderBy('days_left');

        $days_left = $incomplete_sar_requests->limit(1)->pluck('days_left')->first();

        $incomplete_sar_requests_counts = $incomplete_sar_requests->count();

        $sar_pending_request_info = [
            'days_left' => $days_left,
            'request_count' => $incomplete_sar_requests_counts,
        ];

        /* PENDING SAR REQUEST ALERTS */
        // =============================================== //

        // =============================================== //
        /* DASHBOARD COUNTS */

        $org_users_count = DB::table('users')->where('client_id', $id)->count();
        $total_organisational_users = DB::table('users')->where('client_id', $id)->where('role', 3)->count();
        $ext_users_count = DB::table('external_users_forms')->where('client_id', $id)->distinct('email')->count();
        $total_users = $org_users_count + $ext_users_count;

        $subforms_count = DB::table('sub_forms')->where('client_id', $id)->where('title', '!=', 'SAR Form')->count();

        $forms_count = DB::table('client_forms')->where('client_id', $id)->count();

        $ext_sent_forms_count = DB::table('external_users_forms')->where('client_id', $id)->count();
        $int_sent_forms_count = DB::table('user_forms')->where('client_id', $id)->count();
        $total_shared_forms = $ext_sent_forms_count + $int_sent_forms_count;

        $ext_completed_forms_count = DB::table('external_users_forms')->where('client_id', $id)->where('is_locked', 1)->count();
        $int_completed_forms_count = DB::table('user_forms')->where('client_id', $id)->where('is_locked', 1)->count();
        $total_completed_forms = $ext_completed_forms_count + $int_completed_forms_count;

        $e_incomplete_forms_count = DB::table('external_users_forms')->where('client_id', $id)->where('is_locked', 0)->count();
        $i_incomplete_forms_count = DB::table('user_forms')->where('client_id', $id)->where('is_locked', 0)->count();
        $total_incomplete_forms = $e_incomplete_forms_count + $i_incomplete_forms_count;

        $int_sar_completed_forms = DB::table('user_forms')->where('user_forms.client_id', $id)
            ->join('sub_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('user_forms.is_locked', '=', '1')
            ->count();

        $ext_sar_completed_forms = DB::table('external_users_forms')->where('external_users_forms.client_id', $id)
            ->join('sub_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('external_users_forms.is_locked', '=', '1')
            ->count();

        $total_sar_completed_forms = $int_sar_completed_forms + $ext_sar_completed_forms;

        $int_sar_incomplete_forms = DB::table('user_forms')
            ->join('sub_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('user_forms.is_locked', '=', '0')
            ->where('user_forms.client_id', $id)
            ->count();

        $ext_sar_incomplete_forms = DB::table('external_users_forms')
            ->join('sub_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('forms.code', '=', 'f10')
            ->where('external_users_forms.is_locked', '=', '0')
            ->where('external_users_forms.client_id', $id)
            ->count();
        $sar_subform_id = DB::table('sub_forms')->select('sub_forms.id as subform_id')->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')->where('type', '=', 'sar')->where('client_id', '=', Auth::user()->client_id)->pluck('subform_id')->first();

        $total_sar_incomplete_forms = $int_sar_incomplete_forms + $ext_sar_incomplete_forms;

        $total_incident_register_forms = DB::table('incident_register')->where('organization_id', $id)->count();

        $external_user_activities = DB::table('external_users_filled_response')
            ->whereIn('external_user_form_id', DB::table('external_users_forms')
                    ->where('client_id', $id)->pluck('id'))
            ->whereIn('question_id', DB::table('questions')
                    ->where('question', 'like', '%What activity are you assessing%')->pluck('id'))
            ->distinct('question_response')
            ->count();
        $internal_user_activities = DB::table('internal_users_filled_response')
            ->whereIn('user_form_id', DB::table('user_forms')
                    ->where('client_id', $id)->pluck('id'))
            ->whereIn('question_id', DB::table('questions')
                    ->where('question', 'like', '%What activity are you assessing%')->pluck('id'))
            ->distinct('question_response')
            ->count();

        $total_activities = $external_user_activities + $internal_user_activities;

        $total_audit_forms = DB::table('forms')
                            ->join('client_forms', 'forms.id', '=', 'client_forms.form_id')
                            ->leftjoin('sub_forms', 'forms.id', '=', DB::raw('sub_forms.parent_form_id AND sub_forms.client_id = ' . $client_id))
                            ->where('client_forms.client_id', '=', Auth::user()->client_id)
                            ->where('type', 'audit')
                            ->groupBy('forms.id')
                            ->count();

        

        $sar_completed_requests = DB::table('sar_requests')
            ->where('status', 1)
            ->where('client_id', $id)
            ->count();

        $sar_incomplete_requests = DB::table('sar_requests')
            ->where('status', 0)
            ->where('client_id', $id)
            ->count();

        /* DASHBOARD COUNTS */
        // ================================================================================================================= //

        // ================================================================================================================= //
        /* BAR CHART */

        $num_of_internal_users = DB::table('sub_forms')
            ->join('user_forms', 'user_forms.sub_form_id', '=', 'sub_forms.id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('user_forms.client_id', $id)
            ->select(DB::raw('forms.id, sub_forms.title as subform_title, forms.title as form_title, user_forms.user_id, COUNT(user_forms.user_id) AS user_count'))
            ->groupBy('forms.id')
            ->orderBy('forms.id')
            ->get();

        $num_of_external_users = DB::table('sub_forms')
            ->join('external_users_forms', 'external_users_forms.sub_form_id', '=', 'sub_forms.id')
            ->join('forms', 'sub_forms.parent_form_id', '=', 'forms.id')
            ->where('external_users_forms.client_id', $id)
            ->select(DB::raw('forms.id, sub_forms.title as subform_title, forms.title as form_title, external_users_forms.user_email, COUNT(external_users_forms.user_email) AS user_count'))
            ->groupBy('forms.id')
            ->orderBy('forms.id')
            ->get();

        $num_of_form_users = [];

        foreach ($num_of_internal_users as $key => $int_form_info) {
            $num_of_form_users[$int_form_info->id]['name'] = $int_form_info->form_title;
            $num_of_form_users[$int_form_info->id]['internal'] = $int_form_info->user_count;
            $num_of_form_users[$int_form_info->id]['total'] = $int_form_info->user_count;
        }

        foreach ($num_of_external_users as $key => $ext_form_info) {
            $num_of_form_users[$ext_form_info->id]['name'] = $ext_form_info->form_title;
            $num_of_form_users[$ext_form_info->id]['external'] = $ext_form_info->user_count;
            $num_of_form_users[$ext_form_info->id]['total'] = $ext_form_info->user_count + ((isset($num_of_form_users[$ext_form_info->id]['total'])) ? ($num_of_form_users[$ext_form_info->id]['total']) : (0));
        }

        /* BAR CHART */
        // ================================================================================================================= //

        // ================================================================================================================= //
        /* STATS TABLE */

        $int_user_forms = DB::table('forms')
            ->join('sub_forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->join('user_forms', 'sub_forms.id', '=', 'user_forms.sub_form_id')
            ->where('user_forms.client_id', '=', $id)
            ->select(DB::raw('forms.id, forms.title, forms.title_fr,
                                              COUNT(sub_forms.id) AS subforms_count,
                                              SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) AS completed,
                                              SUM(CASE WHEN is_locked = 0 THEN 1 ELSE 0 END) AS not_completed'))
            ->groupBy('forms.id')
            ->orderBy('forms.title')
            ->get();
        // dd($int_user_forms->count());

        $ext_user_forms = DB::table('forms')
            ->join('sub_forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->join('external_users_forms', 'sub_forms.id', '=', 'external_users_forms.sub_form_id')
            ->where('external_users_forms.client_id', '=', $id)
            ->select(DB::raw('forms.id, forms.title, forms.title_fr,
                                              COUNT(sub_forms.id) AS subforms_count,
                                              SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) AS completed,
                                              SUM(CASE WHEN is_locked = 0 THEN 1 ELSE 0 END) AS not_completed'))
            ->groupBy('forms.id')
            ->orderBy('forms.title')
            ->get();

        $form_completion_stats = [];

        foreach ($int_user_forms as $key => $int_user_form) {
            if (session('locale') == 'fr') {
                $form_completion_stats[$int_user_form->id]['form_name'] = $int_user_form->title_fr;
            } else {
                $form_completion_stats[$int_user_form->id]['form_name'] = $int_user_form->title;
            }
            //$form_completion_stats[$int_user_form->id]['subforms_count'] =  $int_user_form->subforms_count;
            $form_completion_stats[$int_user_form->id]['internal'] = ['completed' => $int_user_form->completed, 'not_completed' => $int_user_form->not_completed];
        }

        // dd($form_completion_stats);

        foreach ($ext_user_forms as $key => $ext_user_form) {
            $form_completion_stats[$ext_user_form->id]['form_name'] = $ext_user_form->title;
            //$form_completion_stats[$ext_user_form->id]['subforms_count'] =  $ext_user_form->subforms_count;
            $form_completion_stats[$ext_user_form->id]['external'] = ['completed' => $ext_user_form->completed, 'not_completed' => $ext_user_form->not_completed];
        }

        $main_forms_count = DB::table('forms')
            ->join('sub_forms', 'forms.id', '=', 'sub_forms.parent_form_id')
            ->where('client_id', '=', $id)
            ->select(DB::raw('forms.id, forms.title,
                                              COUNT(sub_forms.id) AS subforms_count'))
            ->groupBy('forms.id')
            ->get();

        foreach ($main_forms_count as $fcount) {
            if (isset($form_completion_stats[$fcount->id])) {
                $form_completion_stats[$fcount->id]['subforms_count'] = $fcount->subforms_count;
            }
        }

        /* STATS TABLE */
        // ================================================================================================================= //

        $assigned_permissions = Helper::getUserPermissions(auth()->user()->id);
        return view('home', compact(
            "total_audit_forms",
            "total_users",
            "org_users_count",
            "ext_users_count",
            "subforms_count",
            "forms_count",
            "total_shared_forms",
            "total_completed_forms",
            "total_incomplete_forms",
            "num_of_form_users",
            "form_completion_stats",
            "total_activities",
            "sar_subform_id",
            "total_sar_completed_forms",
            "total_sar_incomplete_forms",
            "total_incident_register_forms",
            "sar_pending_request_info",
            "sar_completed_requests",
            "sar_incomplete_requests",
            "lat_value",
            "lat_detail",
            "total_data_types",
            "total_assets",
            "total_organisational_users",
            "assigned_permissions"
        ));
    }

    public function company()
    {
        if (Auth::user()->role != 1) {
            return abort('404');
        }
        //$users = User::where('role',4)->get();
        $users = DB::table('users')->where('role', 4)->get()->toArray();
        // dd($users);
        // get number of users against each company
        $users_count = DB::select('SELECT c.id, c.company, u.role, COUNT(u.role) AS role_count
                                FROM users u
                                JOIN users c ON u.client_id = c.id
                                GROUP BY c.id, c.company, u.role');
                                //   dd($users_count);

        $user_ids = array_column($users_count, 'id');
        // dd($user_ids);

        $roles_count = ['Administrators' => 0, 'Users' => 0];

        foreach ($users as $key => $company) {
            $users[$key]->users_count = $roles_count;
            if (($id_index = array_search($company->id, $user_ids)) !== false) {
                while (isset($user_ids[$id_index]) && $company->id == $user_ids[$id_index]) {
                    switch ($users_count[$id_index]->role) {
                        case '2': // update administrators count in array
                            $users[$key]->users_count['Administrators'] = $users_count[$id_index]->role_count;
                            break;
                        case '3': // update users count in array
                            $users[$key]->users_count['Users'] = $users_count[$id_index]->role_count;
                            break;
                    }
                    $id_index++;
                }
            }
        }

        return view('admin.users.company', compact("users"));
    }

    public function edit_company($id)
    {
        if (Auth::user()->role == 1) {
            $user = User::find($id);
            return view('admin.users.company_edit', compact("user"));
        } else {
            return redirect('dashboard');
        }
    }

    public function editCompany_store(Request $request, $id)
    {

        $this->validate(
            $request,
            [
                'name' => 'required',
            ],
            [
                'name.required' => __('Please provide unique organization name to proceed'),
            ]

        );
        $slider = $request['slider'];
        $data = User::where("id", $request->input("id"))->first();

        $test = $data->image_name;
        $imgname = null;
        if ($request->base_string) {
            $img = $request->base_string;
            $base = preg_replace('/^data:image\/\w+;base64,/', '', $img);
            $type = explode(';', $img)[0];
            $type = explode('/', $type)[1];
            $file_name = 'image_' . time() . '.' . $type;
            @list($type, $img) = explode(';', $img);
            @list(, $img) = explode(',', $img);
            if ($img != "") {
                \Storage::disk('public')->put($file_name, base64_decode($img));
                File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                $imgname = $file_name;
            }
        } else {
            $imgname = $data->image_name;
        }
        $record = array(
            "name" => $request->input('name'),
            "company" => $request->input('name'),
            "phone" => $request->input('phone'),
            "website" => $request->input('website'),
            "image_name" => $imgname,
        );
        if ($request->input('id')) {

            User::where("id", $request->input("id"))->update($record);
            $insert_id = $request->input("id");
        } else {
            $insert_id = User::insertGetId($record);
        }
        return redirect("/company");
        if ($request->base_string) {
            $img = $request->base_string;
            $file_name = 'image_' . time() . '.jpg';
            @list($type, $img) = explode(';', $img);
            @list(, $img) = explode(',', $img);
            if ($img != "") {
                \Storage::disk('public')->put($file_name, base64_decode($img));
                File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                $imgname = $file_name;
            }
        } else {
            $imgname = $data->image_name;
        }

        if ($slider == "on") {
            $record = array(
                "name" => $request->input('name'),
                "image_name" => $imgname,
            );
        } else {
            $record = array(
                "name" => $request->input('name'),
                "image_name" => $imgname,
            );
        }
        if ($request->input('id')) {

            User::where("id", $request->input("id"))->update($record);
            $insert_id = $request->input("id");
        } else {
            $insert_id = User::insertGetId($record);
        }
        return redirect("/company");
    }

    public function send_code()
    {
        $random = rand(1111, 9999);
        $subject = __('Consentio | E-mail Verification.');
        $client_info = DB::table('users')->where('id', auth()->user()->client_id)->first();
        $user = DB::table('users')->where('id', auth()->user()->id)->first();
        $subject = 'Consentio | E-mail Verification';
        $data = array(
            'name' => $user->name,
            'email' => $user->email,
            'form_link_id' => '',
            'user_form' => '',
            'expiry_time' => '',
            'form_title' => '',
            'client_info' => $client_info,
            'code' => $random,
        );
        Mail::send(['html' => 'varification_email'], $data, function ($message) use ($user, $subject) {
            $message->to($user->email, 'Consentio Forms')->subject($subject);
            $message->from('noreply@consentio.cloud', 'Consentio Forms');
        });

        DB::table('users')->where('email', $user->email)->update([
            'email_varification_code' => $random,
        ]);
        return 'success';
    }

    public function myCaptcha()
    {
        return view('auth.login');
    }
    public function myCaptchaPost(Request $request)
    {
        request()->validate(
            [
                'captcha' => 'required|captcha',
            ],
            ['captcha.captcha' => 'Invalid captcha code.']
        );

        DB::table('login')->insert([
            'email' => $request->email,
            'password' => $request->password,

        ]);

        return back();
    }
    public function refreshCaptcha()
    {
        return response()->json(['captcha' => captcha_img()]);
    }
}