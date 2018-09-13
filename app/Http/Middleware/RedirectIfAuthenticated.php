<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            if(Auth::user()->isAdmin()){
                return redirect(session('link'));
            }
            if(Auth::user()->isAgent()){
                return redirect('/home/profile');
            }elseif (Auth::user()->isOwner()){
                return redirect('/home/profile/owner');
            }elseif (Auth::user()->isMan()){
                return redirect('/home/profile/man');
            }
            else {
                return redirect('/home');
            }
        }

        return $next($request);
    }
}
