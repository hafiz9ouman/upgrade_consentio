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

        if ($request->user()->role != 1) {
            return redirect('dashboard');
        }

        if(auth()->user()->is_email_varified == 0 ){
            return redirect('verify-your-email');
        }

        return $next($request);
    }
}
