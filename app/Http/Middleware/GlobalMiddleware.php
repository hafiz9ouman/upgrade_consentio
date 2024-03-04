<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use Session;

class GlobalMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        if(Auth::check()){
            if(Auth::user()->is_blocked == "Yes"){
                Session::flush();    
                return redirect('/')->with('status', __('Your_account_is_currently_blocked'));
            }
        }

        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
        
        return $response;
    }
}
