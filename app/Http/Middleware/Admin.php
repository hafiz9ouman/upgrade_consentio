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

        return $next($request);
    }
}
