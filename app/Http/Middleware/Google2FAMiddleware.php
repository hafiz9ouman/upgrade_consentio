<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\Google2FAAuthenticator;

class Google2FAMiddleware
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
        // $authenticator = app(Google2FAAuthenticator::class)->boot($request);
        // if($authenticator->isAuthenticated()){
        //     return $next($request);
        // }
        // return $authenticator->makeRequestOneTimePasswordResponse();
        // if(auth()->user()->tfa == 1 && auth()->user()->is_email_varified == 0){
        //     return redirect()->route('enable2fa')->with('message', __('Please complete 2FA verification'));
        // }
        
        return $next($request);
    }
}
