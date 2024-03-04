<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
//use Auth;


// 
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Http\Requests\ValidateSecretRequest;
use App;
use Session;
use DB;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */
	protected $maxAttempts = 30;
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    // protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { 
	//$2y$10$m8aUexORLETQd4bu0eIVPeGqNT96QERKFete.jDFZT8.rpGJre6xC    111111
/*
DB::table('users')->where('id',383)->update([
						'password' => '$2y$10$m8aUexORLETQd4bu0eIVPeGqNT96QERKFete.jDFZT8.rpGJre6xC',
						]);



	$users = DB::table('users')->get();
			echo '<pre>';print_r($users);
			print_r($_POST);exit;


*/
		
        $this->middleware('guest')->except('logout');
    }
public function getBrowser() {

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

foreach ($browsers as $regex => $value) {
if (preg_match($regex, $user_agent)) { $browser = $value; }
}

return $browser;
}	
    public function redirectTo() {
		
        $user = auth()->user();
        echo 'succes';exit;
        switch($user->role) {
            case 1:
                return '/admin';
            default:
                return '/dashboard';
        }
    }

    // public function showLoginForm()
    // {
    //     if(!session()->has('url.intended'))
    //     {
    //         session(['url.intended' => url()->previous()]);
    //     }
    //     // dd('asdasd');
    //     return view('auth.login');
    // }
protected function sendFailedLoginResponse(Request $request)
{


}



    private function authenticated(Request $request, Authenticatable $user)
    {

        // dd(Auth::user());
        // admin
        // dashboard

	
	        if( Auth::user()->role == 1 )
        {
echo '<pre>';print_r(Auth::user());exit;
            // redirect to login if loggedIn.
            return redirect('/admin');
            
        } elseif( Auth::user()->role == 2 ) {
            // company 
//echo '<pre>';print_r(Auth::user());exit;
            return redirect('/dashboard');
        }

        return redirect('dashboard');
        
    }




}
