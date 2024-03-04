<?php

namespace App\Http\Middleware;

use Closure;

class is_email_varified
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
        if(auth()->user()->is_email_varified == 0 ){
            if(auth()->user()->role != 1){
            return redirect('verify-your-email');
        }
        }
        return $next($request);
    }
}
