<?php namespace App\Http\Middleware;

use Auth;
use Closure;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    
    public function handle($request, Closure $next){
        if (!Auth::check()) {
            return redirect('');
        }
        if(auth()->user()->tfa == 1 && auth()->user()->is_email_varified == 0){
            return redirect()->route('enable2fa')->with('message', __('Please complete 2FA verification'));
        }
        if(auth()->user()->is_email_varified == 0 ){
            return redirect('verify-your-email');
        }
        if ($request->user()->role != 1) {
            return redirect('dashboard');
        }

        return $next($request);
    }
}
