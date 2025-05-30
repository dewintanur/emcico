<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadOnlyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (in_array(Auth::user()->role, ['it', 'admin']) && !$request->isMethod('get')) {
            return response()->json(['message' => 'Akses terbatas: hanya bisa melihat'], 403);
        }

        return $next($request);
    }
}
