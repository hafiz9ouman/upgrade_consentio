<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\PasswordSecurity;
use Auth;
use Lang;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use DB;
class Admin extends Controller
{
    
    public function edit_form($form_id)
    {
        $form = DB::table('forms')->where('id', $form_id)->first();
      //  echo "<pre>";print_r($form);exit;
        return view('forms.edit_form_info', compact('form'));
    }
    
    public function edit_form_act(Request $request, $form_id){

          $this->validate($request, [
            'name' => 'required',
            'name_fr' => 'required',

            ],
            [
                'name.required' => __('Form English name cannot be empty'),
                'name_fr.required' => __('Form French  name cannot be empty'),

            ]           
         );        
        $title      = $request->name;
        $title_fr   = $request->name_fr;
        $id         = $request->id;
        
        $validatedData = $request->validate([
                'name' => 'required',
        ]); 
        
        DB::table('forms')
            ->where('id', $id)
            ->update(['title' => $title , 'title_fr' => $title_fr ]);

        if (DB::table('forms')->find($id)->type == 'audit'){
            return redirect('Forms/AdminFormsList/audit');
        }        
        return redirect('Forms/AdminFormsList');
        
    }  
     // classification data edit form

     public function edit_classification ($id)
    {
        $form = DB::table('data_classifications')->where('id', $id)->first();
      //  echo "<pre>";print_r($form);exit;
        return view('forms.edit_classification', compact('form'));
    }
    
    public function edit_classification_act (Request $request, $id)
    {

          $this->validate($request, [
            'classification_name_en' => 'required',
            'classification_name_fr' => 'required',

            ],
            [
                'classification_name_en.required' => __('Form English name cannot be empty'),
                'classification_name_fr.required' => __('Form French  name cannot be empty'),

            ]
            
         );
        $title_en = $request->classification_name_en;
        $title_fr = $request->classification_name_fr;
        $id    = $request->id;
        
        // $validatedData = $request->validate([
        //         'classification_name_en' => 'required',
        // ]); 
        
        DB::table('data_classifications')
            ->where('id', $id)
            ->update(['classification_name_en' => $title_en , 'classification_name_fr' => $title_fr ]);
            
        return redirect('/data-classification');
        
    }   
    

     public function edit_impact ($id)
    {
        $form = DB::table('impact')->where('id', $id)->first();
      //  echo "<pre>";print_r($form);exit;
        return view('forms.edit_impact', compact('form'));
    }
    
     public function edit_impact_act (Request $request, $id)
    {

          $this->validate($request, [
            'impact_name_en' => 'required',
            'impact_name_fr' => 'required',

            ],
            [
                'impact_name_en.required' => __('Form English name cannot be empty'),
                'impact_name_fr.required' => __('Form French  name cannot be empty'),

            ]
            
         );
        $title_en = $request->impact_name_en;
        $title_fr = $request->impact_name_fr;
        $id    = $request->id;
        
        // $validatedData = $request->validate([
        //         'classification_name_en' => 'required',
        // ]); 
        
        DB::table('impact')
            ->where('id', $id)
            ->update(['impact_name_en' => $title_en , 'impact_name_fr' => $title_fr ]);
            
        return redirect('/impact');
        
    }       


    public function evaluation_rating(){
        $data = DB::table('evaluation_rating')->whereNull('owner_id')->get();
        return view("admin.users.evaluation_rating",["data"=>$data]);
    }
    public function edit_evaluation($id){
        $data = DB::table("evaluation_rating")->where("id",$id)->get();
        return view("admin.users.edit_evaluation",["data"=>$data]);
    }
    public function update_evaluation(Request $req){
        DB::table('evaluation_rating')
            ->where('id', $req->id)
            ->update([
                'assessment' => $req->assessment , 
                'rating' => $req->rating, 
                'color' => $req->color, 
                'text_color' => $req->text_color, 
            ]);
            
        return redirect('/evaluation_rating');
    }
    
    public function site_admins ()
    {
        if (!Auth::check()) {
    
        return redirect('');
        }
        if (Auth::user()->role == 1)
        {
            $users = User::where('role',1)->get();
            
            return view('admin.users.site_admins', compact('users'));
        }
        else
        {
            return abort('404');
        }
    }
    
    public function add_admin ()
    {
        if (Auth::user()->role == 1)
        {
            return view('admin.users.add_admin');
        }
        else
        {
            return abort('404');
        }        
    }
    
    public function edit_admin($id) 
    { 
        if(Auth::user()->role==1) {
            $user = User::find($id);
            return view('admin.users.edit_admin', compact("user"));
        }
        else {
            return redirect('dashboard');
        }
    }
    
    //Minor Changes for Image
    public function edit_admin_act (Request $request , $id) 
    { 

             $this->validate($request, [
            'name' => 'required',
            ],
            [
                'name.required' => __('Please provide proper name to proceed'),
            ]
            
         );

            // dd($request->all());
            $data = User::where("id", $request->input("id"))->first();  
            
            $name     = $request->name;
            $password = $request->password;
            
            // if ($request->hasFile('images'))
            // {
            //     $request->validate([
            //         'images' => 'dimensions:max_width=800,max_height=600',
            //     ]); 
                
            //     $image_size = $request->file('images')->getsize();
                 

            //     if ( $image_size > 1000000 ) 
            //     {
            //         return redirect('edit_admin/'.$id)->with('alert', 'Maximum size of Image 1MB!')->withInput();            
            //     }                
            // }

            $test = $data->image_name;
            $inputs = [
                'password' => $password,
            ];
            
            $rules = [
                'password' => [
                'string',
                'min:8',             // must be at least 8 characters in length
                'regex:/[a-z]/',      // must contain at least one lowercase letter
                'regex:/[A-Z]/',      // must contain at least one uppercase letter
                'regex:/[0-9]/',      // must contain at least one digit
                ],
            ];
    $validation = \Validator::make( $inputs, $rules );

    if($request->password!=""){
    if ( $validation->fails() ) {
          return redirect('edit_admin/'.$id)->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();            
           }elseif($request->password != $request->rpassword)
                {
            return redirect('edit_admin/'.$id)->with('alert', __('Password did not match!'));
                } 
                else{      
           
            // if($request->hasfile('images')){
            //     $destinationpath=public_path("img/$test");
            //     File::delete($destinationpath);
            //     $file=$request->file('images');
            //     $filename = str_replace(' ', '', $file->getClientOriginalName());
            //     $ext=$file->getClientOriginalExtension();
            //     $imgname=uniqid().$filename;
            //     $destinationpath=public_path('img');
            //     $file->move($destinationpath,$imgname);
            // }

            // if($request->base_string ){
            //             // dd('yess');
            //         $ext = explode('/', mime_content_type($request->base_string))[1];
            //         $img = $request->base_string;
            //         $file_name = 'image_'.time().'.jpg';
            //         @list($type, $img) = explode(';', $img);
            //         @list(, $img)      = explode(',', $img);
            //         if($img!=""){
            //             \Storage::disk('public')->put($file_name,base64_decode($img));
            //             File::move(storage_path().'/app/public/'.$file_name , 'public/img/'.$file_name); 
            //             $imgname = $file_name;
            //         }
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
                        File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                        $imgname = $file_name;
                    }
                }
            }
            else{
                        // dd('noo');

                $imgname =$test;
            }
            $record = array(
           "name" => $request->input('name'),
           "image_name" => $imgname,
        );
        if($request->input('password')) { 
            $record['password'] = bcrypt($request->input('password'));
        }
        if($request->input('id')) {
            
            User::where("id", $request->input("id"))->update($record);
            $insert_id = $request->input("id");
        } else { 
            $insert_id =  User::insertGetId($record);
        }            
        $fa = User::where("id", $request->input("id"))->first();
       
            return redirect("site_admins");
        }
    }
    else
    {
         if($request->password != $request->rpassword)
         {
            return redirect('users/edit/'.$id)->with('alert', __('Password did not match!'));
         } 
         else
         { 
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
                 
            // if($request->base_string ){
            //     // dd('yess');
            //     $ext = explode('/', mime_content_type($request->base_string))[1];
            //     $img = $request->base_string;
            //          $file_name = 'image_'.time().'.jpg';
            //          @list($type, $img) = explode(';', $img);
            //          @list(, $img)      = explode(',', $img);
            //          if($img!=""){
            //            \Storage::disk('public')->put($file_name,base64_decode($img));
            //            File::move(storage_path().'/app/public/'.$file_name , 'public/img/'.$file_name); 
            //             $imgname = $file_name;

            // }
          
            } else{
                        // dd('noo');

                $imgname =$test;
            }
        
            $record = array(
               "name" => $request->input('name'),
               "image_name" => $imgname,
               "tfa" => 0,           
            );

            if($request->input('password')) 
            { 
                $record['password'] = bcrypt($request->input('password'));
            }
        
            if($request->input('id')) 
            {
                User::where("id", $request->input("id"))->update($record);           
                $insert_id = $request->input("id");
            
            } 
            else 
            { 
                $insert_id =  User::insertGetId($record);
            }

            $fa = User::where("id", $request->input("id"))->first();

            return redirect("site_admins");
        }
    }
    }    
    // Some Changes for Image.
    public function add_admin_act (Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
            ],
            [
                'name.required' => __('Please provide proper name to proceed'),
                'email.required' => __('Please provide proper email to proceed'),
            ]
            
         );
        // dd($request->all());
        $email = $request->input('email');
        $name  = $request->input('name');
        $pswrd = $request->input('password');

        $test = DB::table('users')->where('email','=',$email)->first();
        
        // if ($request->hasFile('images')) {
            
        //     $request->validate([
        //         'images' => 'dimensions:max_width=800,max_height=600',
        //     ]);             
           
        //     $image_size = $request->file('images')->getsize();
        
        //     if ( $image_size > 1000000 ) {
        //         return redirect('add_admin')->with('alert', 'Maximum size of Image 1MB!')->withInput();            
        //     }            
         
        // }

        $inputs = [
        'password' => $pswrd,
                ];
         $rules = [
        'password' => [
            'required',
            'string',
            'min:8',              // must be at least 8 characters in length
            'regex:/[a-z]/',      // must contain at least one lowercase letter
            'regex:/[A-Z]/',      // must contain at least one uppercase letter
            'regex:/[0-9]/',      // must contain at least one digit
        ],
    ];
    $validation = \Validator::make( $inputs, $rules);

    if ($validation->fails()) 
    {
         return redirect('add_admin')->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();            
    }
    elseif($pswrd != $request->rpassword)
    {
        return redirect('add_admin')->with('alert', __('Password did not match!'))->withInput();
    }
    elseif (empty($test)) {
        
        $imgname ='';
        
        // if($request->hasfile('images')){
        //     $file=$request->file('images');
        //     $filename = str_replace(' ', '', $file->getClientOriginalName());
        //     $ext=$file->getClientOriginalExtension();
        //     $imgname=uniqid().$filename.'.'.$ext;
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
                    File::move(storage_path() . '/app/public/' . $file_name, 'public/img/' . $file_name);
                    $imgname = $file_name;
                }
            }
        }
        // it work on local but on live didnot
        //  if($request->base_string ){
        //         $ext = explode('/', mime_content_type($request->base_string))[1];
        //         $img = $request->base_string;
        //              $file_name = 'image_'.time().'.jpg';
        //              @list($type, $img) = explode(';', $img);
        //              @list(, $img)      = explode(',', $img);
        //              if($img!=""){
        //                \Storage::disk('public')->put($file_name,base64_decode($img));
        //                File::move(storage_path().'/app/public/'.$file_name , 'public/img/'.$file_name); 
        //                 $imgname = $file_name;

        //              }
          
        //     }
            // print_r($imgname);exit();

        $data = array(
            "name" => $request->input('name'),
            "email" => $request->input('email'),
            "role" => 1,
            "image_name" => $imgname,
            "tfa" => 0,
            "client_id" => 0,
            "created_by" =>Auth::user()->id,
        );
            
        if($request->input('password')) { 
            $data['password'] = bcrypt($request->input('password'));
        }

        if($request->input('id')) {
            User::where("id", $request->input("id"))->update($data);
            $insert_id = $request->input("id");
        } else { 
            $insert_id =  User::insertGetId($data);
        }
        \Session::flash('success', Lang::get('general.success_message'));
        return redirect('site_admins');
        }
        else
        {
            return redirect('add_admin')->with('alert', __('Email already exists!'))->withInput();
        }   
    }
    
}