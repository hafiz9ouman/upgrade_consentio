<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Auth;
use App\Package;
use App\Related_product;
use App\Attibute;
use App\PaksageImage;
use App\Ticket;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\File;
use DB;
use Lang;
use Session;
use App\PasswordSecurity;

class PackagesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        //    $fa=PasswordSecurity::where('user_id',Auth::user()->id)->first();
        // if($fa->google2fa_enable==0){
        //     redirect('/2fa');
        // }

        
    }

    public function index() 
    { 
        $client = User::where('role',2)->get();
		$user = User::where('role',3)->orderBy('id','DESC')->get();
        return view('admin.selectClient.home', compact("user", "client"));
    }
    
    

    public function saveClient(Request $request, $id)
    {
        $clientid = Input::get('team');
        // print_r($id);exit();
        User::where('id',$id)->update(['client_id'=> $clientid]);
        return redirect('selectClient')->with('alert', __('Client linked Successfully'));
    }

    public function client()
    {
        $client = User::where('role',2)->get();
        // print_r($client);exit();
        return view('admin.client.home', compact('client'));
    }

    public function showUser($id)
    {
        $user = User::where('client_id',$id)->get();
        $user_id = $id;
        // echo "<pre>";
        // print_r($user[0]->client_id);
        // echo "</pre>";
        // exit;
        return view('admin.users.showUser', compact('user', 'user_id'));
    }

      public function profile($id)
    {
        $client = DB::table('users')->where('id',$id)->first();
        
        if (!$client)
        {
            return abort('404');
        }
        
        $client_id = $client->client_id;
        if (Auth::user()->role != 1) {
            if (Auth::user()->role == 2) {
                
                $client_id_a = Auth::user()->client_id;

                if ($client->client_id != $client_id_a)
                    return abort('404');
    
            }
            elseif (Auth::user()->role == 3) {
                if (Auth::id() != $id) {
                    return abort('404');
                }
            }            
        } 
   

        $administrator = DB::table('users')->where('id',$client_id)->first();
        // print_r($administrator);exit();
        
        $company_id = $administrator->client_id;
        $company_name1 = DB::table('users')->where('id',$company_id)->first();
        if($company_name1==""){
            $company_name = $administrator;
        } else{
           $company_name = $company_name1;
        }
        // print_r($company_name);exit;

        return view('profile', compact('client','company_name'));
    }
    // Minor Changes for Image
    public function profile_edit(Request $request)
    {
          $this->validate($request, [
            'name' => 'required',
            ],
            [
                'name.required' => __('Name can not be empty'),
            ]
            
         );

        // dd($request->all());
        $id = $request->input('id');
        $data = User::where("id", $id)->first();
        
        if (Auth::user()->role == 1) {
            if (Auth::user()->role == 2) {
                
                $client_id = Auth::user()->client_id;
                
                if ($data->client_id != $client_id)
                    return abort('404');
    
            }
            elseif (Auth::user()->role == 3) {
                if (Auth::id() != $id) {
                    return abort('404');
                }
            }            
        }        
        
        
        $test = $data->image_name;
            
        // if ($request->hasFile('images')) {
            
        //     // $request->validate([
        //     //     'images' => 'dimensions:max_width=800,max_height=600',
        //     // ]);            
            
        //     $image_size = $request->file('images')->getsize();
            

        //     if ( $image_size > 1000000) {
        //         return redirect('profile/'.$id)->with('alert', 'Maximum 1 MB image size is allowed')->withInput();            
        //     }
        // }
            
         $inputs = [
        'password' => $request->password,
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
          return redirect('profile/'.$id)->with('alert', __('Password must be Min 8 Characters, Alphanumeric with an Upper and lower case!'))->withInput();            
           }elseif($request->password != $request->rpassword)
                {
            return redirect('profile/'.$id)->with('alert', __('Password did not match!'))->withInput();
                }else{

                    // if($request->base_string ){
                    //     $ext = explode('/', mime_content_type($request->base_string))[1];
                    //     $img = $request->base_string;
                       
                    //          $file_name = 'image_'.time().'.jpg';
                    //          @list($type, $img) = explode(';', $img);
                    //          @list(, $img)      = explode(',', $img);
                    //          if($img!=""){
                    //            \Storage::disk('public')->put($file_name,base64_decode($img));
                    //            $path  = 'public/img/'.$file_name;
                    //                  if(auth()->user()->role == 3){
                    //                         $path  = 'public/img2/'.$file_name;
                    //                   }
                    //            File::move(storage_path().'/app/public/'.$file_name , $path);  
                    //            $imgname = $file_name;                
                    //          }
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
                                $path  = 'public/img/'.$file_name;
                                        if(auth()->user()->role == 3){
                                                $path  = 'public/img2/'.$file_name;
                                        }
                                File::move(storage_path().'/app/public/'.$file_name , $path);
                                $imgname = $file_name;
                            }
                        }
                    }

                    else{
                        $imgname =$test;
                        // print_r($imgname);exit();
                    }
        $data = array(            
            'name'=>$request->input('name'),
            'email'=>$request->input('email'),
            'password'=>bcrypt($request->input('password')),
            'image_name'=>$imgname
        );
        DB::table('users')->where('id',$id)->update($data);
        // $client = DB::table('users')->where('id',$id)->first();
        // return view('profile', compact('client'));
        return redirect('dashboard');
                }
                }
                else{
              
                   if($request->password != $request->rpassword)
                {
             return redirect('profile/'.$id)->with('alert', __('Password did not match!'));
                }   

                else{    
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
                                $path  = 'public/img/'.$file_name;
                                        if(auth()->user()->role == 3){
                                                $path  = 'public/img2/'.$file_name;
                                        }
                                File::move(storage_path().'/app/public/'.$file_name , $path);
                                $imgname = $file_name;
                            }
                        }
                    }  
            // if($request->base_string ){
            //         $ext = explode('/', mime_content_type($request->base_string))[1];
            //         $img = $request->base_string;
                    
            //         $file_name = 'image_'.time().'.jpg';
            //         @list($type, $img) = explode(';', $img);
            //         @list(, $img)      = explode(',', $img);
            //         if($img!=""){
            //         \Storage::disk('public')->put($file_name,base64_decode($img));
            //         $path  = 'public/img/'.$file_name;
            //                 if(auth()->user()->role == 3){
            //                     $path  = 'public/img2/'.$file_name;
            //                 }
            //         File::move(storage_path().'/app/public/'.$file_name , $path);  
            //         $imgname = $file_name;                
            //     }
            // }
                    else{
                        $imgname =$test;
                        // print_r($imgname);exit();
                    }

            $data = array(
           "name" => $request->input('name'),
           "image_name" => $imgname,
        );
        if($request->input('password')) { 
            $data['password'] = bcrypt($request->input('password'));
        }
        if($request->input('id')) {

            
            // print_r($destinationpath);exit();
            User::where("id", $request->input("id"))->update($data);
            
            $insert_id = $request->input("id");
        }else{ 
            $insert_id =  User::insertGetId($data);
        }
            return redirect("dashboard");
        }
    }
    }

    public function send_email($id)
    {
        // print_r("expression");exit();
        $client = DB::table('users')->where('id',$id)->first();
        $user = DB::table('users')->where('client_id',$id)->get();
        return view('send_email', compact('user','client'));
    }
    
}
