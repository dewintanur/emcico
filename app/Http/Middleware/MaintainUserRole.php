<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintainUserRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (!session()->has('active_role')) {
                session(['active_role' => Auth::user()->role]);
            }
        }
        
        return $next($request);
    }
}

