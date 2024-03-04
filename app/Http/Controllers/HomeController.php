<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;
use Session;
use App\User;
use App\Cart;
use App\Ticket;
use App\Package;
use App\Faq;
use App\Blog;
use DB;
use Illuminate\Support\Facades\File;
use Sentinel;
use Rminder;
use Mail;
use App\PasswordSecurity;
use Swift_Mailer;
use Swift_SmtpTransport;
use Swift_Attachment;


class HomeController extends Controller
{
    public function __construct() {
    }

    function getBrowser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = "N/A";
        $browsers = array(
            '/msie/i' => 'Internet explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/mobile/i' => 'Mobile browser'
        );
        foreach ($browsers as $regex => $value) 
        {
            if (preg_match($regex, $user_agent)) { $browser = $value; }
        }
        return $browser;
    }   

    public function checkUserBlockage($email){

        //$result= $this->comman_model->get('users',array("email"=>$email));
        $result = DB::table('users')->where('email',$email)->first();
        //echo '<pre>';print_r($result);exit;

             if($result){
                $login_attempts = $result->login_attempts+1;
                if($login_attempts>5){
                    DB::table('users')->where('email', $email)->update(['login_attempts'=>$login_attempts,'is_blocked'=>'Yes']);
                

               // $this->comman_model->update('users', array('email'=>$email), array('login_attempts'=>$login_attempts));
                }else{
                    DB::table('users')->where('email', $email)->update(['login_attempts' => $login_attempts]);

       

                 }
                }
             return 6-$login_attempts;

    }

    public function send_code( ){
        $random = rand(1111,9999);
        $subject = __('Consentio | E-mail Verification.');
        $client_info = DB::table('users')->where('id'  , auth()->user()->client_id)->first();
        $user = DB::table('users')->where('id' , auth()->user()->id)->first();
        // echo "<pre>";
        // print_r($user);
        // echo "<br><br><br>";
        // echo "auth email ", auth()->user()->email;
        // exit ;
        $subject = 'Consentio | E-mail Verification';
        $data = array(  
            'name'=>      $user->name,
            'email'=> $user->email,
            'form_link_id' => '',
            'user_form' => '',
            'expiry_time' => '',
            'form_title' => '',
            'client_info' => $client_info ,
            'code' => $random
        );
        $name    = $user->name;
        $email   =  $user->email;
        /*from swift mailer*/
        $transport = new Swift_SmtpTransport(env('MAIL_HOST'), env('MAIL_PORT'), env('MAIL_ENCRYPTION'));
        $transport->setUsername(env('mail_username'));
        $transport->setPassword(env('MAIL_PASSWORD'));
        $swift_mailer = new Swift_Mailer($transport);
        Mail::setSwiftMailer($swift_mailer);        
        $reciever_email = $email;
        $sender_email = env('MAIL_FROM_ADDRESS');        
        Mail::send(['html'=>'varification_email'], $data, function($message) use($reciever_email , $sender_email, $subject ) {
            $message->to($reciever_email, 'Consentio Forms')->subject
            ($subject);
            $message->from($sender_email,$sender_email);
        });
        DB::table('users')->where('email' , $user->email)->update(['email_varification_code' => $random]);                               
        return redirect('verify-your-email')->with('message' , __('Verification code is sent, Please check your email '));
    }

    public function login_post_copy(Request $request) {
        echo '1111';exit;
    }

    public function login_post(Request $request){
        // request()->validate([
        //     'captcha' => 'required|min:1|captcha'
        // ],['captcha.captcha'=>'Invalid captcha code.']);

        $userdata = array(
            'email'     => $request->email,
            'password'  => $request->password,
        );
        $errors = array();
        $emailExists = DB::table('users')->where('email',$request->email)->first();

        if(!$emailExists){
            return redirect()->back()->with('status', 'Login Failed Wrong User Credentials');
            $errors['email_not_exist'] = 'Login Failed Wrong User Credentials';
        }

        $emailBlocked = DB::table('users')->where('email',$request->email)->where('is_blocked','Yes')->first();
        if($emailBlocked){
            return redirect()->back()->with('status', 'You account is currently blocked. Please contact your customer care team for further support.');
            $errors['account_blocked'] = 'You account is currently blocked. Please contact your customer care team for further support.';  
        }
        $login_attempts = $emailExists->login_attempts;
        if($login_attempts>5){
            Session::flush();    
            return redirect()->back()->with('status', 'You account is currently blocked. Please contact your customer care team for further support.');
        }
        if (Auth::attempt($userdata)) {
            // return Auth::user();
            $rememberme = $emailExists->rememberme; 
            $rememberme_browser_name = $emailExists->rememberme_browser_name; 
            $rememberme_browser_type = $emailExists->rememberme_browser_type; 
            if($rememberme=='No' || ($rememberme=='Yes' && $rememberme_browser_name!=$this->getBrowser())){
                $this->send_code(); 
            }else{
                DB::table('users')->where('email' , $emailExists->email)->update([
                    'email_varification_code' => '',
                    'is_email_varified' => 1
                ]);
            }
            return redirect('/dashboard');

        }else{
            $login_attempts = $this->checkUserBlockage($request->email);
    	    return redirect()->back()->with('status', 'Please enter an accurate email and password. You account will be blocked after '.$login_attempts.' login attempts.');                           
        } 
        return redirect('/dashboard');
    }

    public function index(){
        if (Auth::user()->role == 1)
        {
            //return 'admin redirect';
            return redirect('/admin');

        }
        if ((Auth::user()->role == 2 || Auth::user()->role == 3) && Auth::user()->tfa == 1)
        {
            
            return redirect('2fa');
            //return 'client redirect';
        }
        return redirect('dashboard');   
    }

    public function test(){
        /*
            $users = DB::table('assets')->where('client_id','')->get();
            echo '<pre>';print_r($users);
        */   
        //DB::table('form_questions')->where('fq_id','266')->delete();exit;
        //DB::table('form_questions')->where('fq_id', '641')->update(['sort_order' => '34']);
        //DB::table('questions')->where('question_section_id', '104')->update(['question_section_id' => '28','question_num' => '3.5']);
        //  DB::table('questions')->where('id', '640150')->update(['question' => 'To what extent has the risk to the data already been mitigated?','question_fr' => 'Dans quelle mesure le risque pour les données a-t-il déjà été atténué ?']);
        DB::table('questions')->where('id', '640154')->update(['question_fr' => '']);
        exit;

        //exit;
        /*DB::table('questions')->where('id', '640134')->update(['options_fr' => 'Oui,  Non']);

        $users = DB::table('questions')->where('id','640134')->get();

                    echo '<pre>';print_r($users);
                    exit;
        */

        /*$this->query="SELECT fq.fq_id,fq.question_id,fq.sort_order,fq.display_question,qs.id,qs.question,qs.question_fr,qs.question_num,qs.form_id as qs_form_id,qs.options,qs.options_fr
                                            FROM form_questions AS fq 
                                            JOIN questions qs 
                                                    ON fq.question_id = qs.id WHERE fq.form_id = 9  ORDER BY fq.sort_order ASC";


        $questions = DB::select($this->query);
        echo '<pre>';print_r($questions);exit;*/






        echo '******************form_questions**********************';
        $users = DB::table('form_questions')->where('form_id', '10')->orderBy('sort_order','asc')->get();
            echo '<pre>';print_r($users);
            echo '=======================';
            


            exit;
            
           /* DB::table('questions')->insert([
                    "question" => 'Additional Comments',
                    "question_fr" => 'Additional Comments FR',
                    "question_info" => '',
                    "question_info_fr" => '',
                    "question_num" => '',
                    "is_assets_question" => 0,
                    "question_comment" => '',
                    "question_comment_fr" => '',
                    "additional_comments" => 0,
                    "question_assoc_type" => 0,
                    "parent_question" => 0,
                    "is_parent" => 0,
                    "parent_q_id" => '11222333',
                    "form_key" => 'q-217',
                    "type" => 'qa',
                    "is_data_inventory_question" => 0,
                    "options" => '',
                    "options_fr" => '',
                    "question_section" => '',
                    "question_section_id" => 27,
                    "question_category" => 2,
                    "form_id" => 10,
                    "created_at" => '2019-08-05 07:00:00',
                    "updated_at" => '2019-08-05 07:00:00',
                    "display" => 'yes'
                        ]);
                DB::table('form_questions')->insert([

                         "form_id" => 10,
                    "question_id" => 640154,
                    "sort_order" => 16,
                    "display_question" => 'yes'
                ]);

                exit;
                */

            echo '<br>******************questions**********************';
            $users = DB::table('questions')->where('id',217)->get();
            echo '<pre>';print_r($users);
            $users = DB::table('questions')->where('id',640154)->get();
            echo '<pre>';print_r($users);
            $users = DB::table('questions')->where('id',640129)->get();
            echo '<pre>';print_r($users);
            $users = DB::table('questions')->where('id',640130)->get();
            echo '<pre>';print_r($users);

            exit;
            echo '-------user_forms';
            $users = DB::table('user_form_links')->get();
                        echo '<pre>';print_r($users);

            echo '-------sub_forms';
            $users = DB::table('sub_forms')->get();
                        echo '<pre>';print_r($users);
            echo '-------users';
            $users = DB::table('users')->get();
                        echo '<pre>';print_r($users);


                        exit;




        $query3 = 'delete from external_users_forms';
        DB::select($query3);
       

        $query3 = 'select * from external_users_forms';

        $records = DB::select($query3);
        echo '<pre>';print_r($records);exit;

            $users = DB::table('forms')->where('id','10')->get();
                        echo '<pre>';print_r($users);
                        exit;
            DB::table('questions')->where('id','640128')->delete();


                        exit;


            /*DB::table('form_questions')->where('question_id', '640133')->update(['form_id' => '9','sort_order' => '23']);
            DB::table('form_questions')->where('question_id', '640134')->update(['form_id' => '9','sort_order' => '23']);
            DB::table('form_questions')->where('question_id', '640135')->update(['form_id' => '9','sort_order' => '23']);

            DB::table('questions')->where('id', '640133')->update(['form_id' => '9','question_num' => '1.4']);
            DB::table('questions')->where('id', '640134')->update(['form_id' => '9','question_comment' => '']);
            DB::table('questions')->where('id', '640135')->update(['form_id' => '9','question_section_id' => '18']);
            */
                    
            //DB::table('questions')->where('fq_id','673')->delete();

            /*$users = DB::table('questions')->where('id','640133')->get();
                        echo '<pre>';print_r($users);
            $users = DB::table('questions')->where('id','640134')->get();
                        echo '<pre>';print_r($users);
            $users = DB::table('questions')->where('id','640135')->get();
                        echo '<pre>';print_r($users);
            echo '<br>--------------------<br>';
            $users = DB::table('form_questions')->where('question_id','640133')->get();
                        echo '<pre>';print_r($users);
            $users = DB::table('form_questions')->where('question_id','640134')->get();
                        echo '<pre>';print_r($users);
            $users = DB::table('form_questions')->where('question_id','640135')->get();
                        echo '<pre>';print_r($users);
            */


                        exit;
            $users = DB::table('questions')->where('id','168')->get();
                        echo '<pre>';print_r($users);            
                        exit;

                        $users = DB::table('form_questions')->where('question_id','475')->get();
                        echo '<pre>';print_r($users);
                    exit;
            /*
            $query = "ALTER TABLE questions add display_question varchar(3) DEFAULT 'yes'";
                DB::select($query);


            */

        //DB::table('questions')->where('id', 87)->update(['type' => 'sc']);

        //$users = DB::table('form_questions')->get();
        //          echo '<pre>';print_r($users);
        //exit;
        //$users = DB::table('questions')->get();
        //          echo '<pre>';print_r($users);
 
        $client_id=Auth::user()->client_id;
        $ext_forms = DB::table('external_users_forms as exf')
        ->join('sub_forms', 'exf.sub_form_id', '=', 'sub_forms.id')
        ->join('forms', 'forms.id', '=', 'sub_forms.parent_form_id')
        ->where('exf.client_id', $client_id)
        // ->where('is_locked', 1)
        ->select('*', DB::raw('exf.user_email as email,
        SUM(CASE WHEN is_locked = 1 THEN 1 ELSE 0 END) as ex_completed_forms,
        COUNT(exf.user_email) as total_external_users_count,
        forms.title as form_title,
        forms.title_fr as form_title_fr,
        sub_forms.title as subform_title,
        sub_forms.title_fr as subform_title_fr,
        "External" as user_type'))
        //->where('exf.user_email','=','xaeem.ds@gmail.com')
        ->groupBy('sub_forms.id')
        ->get();
                    echo '<pre>';print_r($ext_forms);

        exit;


        //DB::table('users')->update(['rememberme' => 'No']);

        DB::table('users')->where('email', 'siteadmin@consentio.cloud')->update(['is_blocked' => 'No','rememberme' => 'No','login_attempts' => 0,'is_email_varified'=>0 ]);

        //DB::table('module_permissions_users')->where('user_id', 383)->update(['user_id' => 323]);

        //DB::table('users')->where('id', 346)->update(['password' => '$2y$10$m8aUexORLETQd4bu0eIVPeGqNT96QERKFete.jDFZT8.rpGJre6xC' ]);

        $users = DB::table('module_permissions_users')->where('user_id' , 323)->get();
                    echo '<pre>';print_r($users);
                    print_r($_POST);

        $users = DB::table('users')->where('email','smalltime59@yahoo.com')->get();
                    echo '<pre>';print_r($users);
                    print_r($_POST);exit;


        echo 'here';exit;
        return view('admin.client.test');
    }

    public function custom_login ($company_id){
        //echo "company Id : ".$company_id."<br>";
    }
    
    public function login_img_settings (){
        $responce = DB::table('login_img_settings')->first();
        // dd($responce);
        return view('login_img_settings', compact('responce'));
    }

    public function update_login_img (Request $request){
     
        if ($request->hasFile('image')) {
            $validator = \Validator::make($request->all(), [
                'image' => 'dimensions:max_width=300,max_height=41'
            ]);
                if ($validator->fails()) {
                    return redirect()->back()->with('status', __('The image has invalid image dimensions.'));
                }     
            
            $image_size = $request->file('image')->getsize();
            if ( $image_size > 1000000 ) {
                return redirect()->back()->with('status', __('Maximum size of Image 1MB!'))->withInput();            
            }            

            $img_name = '';
            $file = $request->file('image');
            $filename = $file->getClientOriginalName();
            $ext = $file->getClientOriginalExtension();
            $img_name = uniqid().$filename;
            $destination_path = public_path('/image');
            $file->move($destination_path, $img_name);
    
            $responce = DB::table('login_img_settings')->update(['image' => $img_name]);
      
            return redirect()->back()->with('status', __('Image Updated Successfully'));
        }
        else{
            return redirect()->back()->with('status', __('No Image Found'));            
        }
    }

    public function verify_code(Request $request){
       
                $code = $request->code;

        $rememberme = $request->rememberme;

        $user_id = auth()->user()->id;
         if(auth()->user()->email_varification_code == $code){
                  DB::table('users')->where('id' , auth()->user()->id)->update([
                      'is_email_varified' => 1,
                  ]);
                  if($rememberme=='Yes'){
                        $this->updatewithbrowser($rememberme);
                  }
                  return ["status" => "success" , "message" =>  __('Your Email is Verified Successfully, You Will Be Redirected To Dashboard In A Moment') ];
                }else{
                    return ["status" => "Error" , "message" =>  __('Verification Code Is Wrong! Please Provide Correct Verification Code To Proceed') ];
                }
    }


    public function updatewithbrowser($rememberme){

     
        $updatedata = array();
        $useragent=$_SERVER['HTTP_USER_AGENT'];

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4)))
        {
        $rememberme_browser_type = 'mobile';
        $rememberme_browser_name = $this->getBrowser();
        $rememberme_start_date = date('Y-m-d');


        //echo "Mobile browser: " . $this->getBrowser();

        //echo '<pre>';print_r($_SERVER);


        }else{
            $rememberme_browser_type = 'desktop';
            $rememberme_browser_name = $this->getBrowser();
            $rememberme_start_date = date('Y-m-d');
        //echo "Desktop browser: " . $this->getBrowser();
        }
        $updatedata['rememberme_browser_type'] = $rememberme_browser_type;
        $updatedata['rememberme_browser_name'] = $rememberme_browser_name;
        $updatedata['rememberme_start_date'] = $rememberme_start_date;
        $updatedata['rememberme'] = $rememberme;
        //echo '<pre>';print_r($updatedata);exit;
        DB::table('users')->where('id', auth()->user()->id)->update($updatedata);
        }
            public function reloadCaptcha(){
                    return response()->json(['captcha'=> captcha_img()]);
                }

                // DATA CLASSIFICATION ADMIN
            public function data_classification (){
                $data = DB::table('data_classifications')->orderBy("id", "ASC")->whereNull('organization_id')->get();
                // dd($responce);
        return view('data_classification', compact('data'));
    }
    // DATA CLASSIFICATION ADMIN

    // Impact
    public function impact(){
        $data = DB::table('impact')->get();
        return view('impact', compact('data'));
    } 
    // Impact 
}
