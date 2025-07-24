<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsParent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the authenticated user has the role of 'parent'
        if (Auth::check() && Auth::user()->role === 'parent') {
            return $next($request);
        }

        // If the user is not a parent, return a 403 Forbidden response
        return response()->json(['message' => 'Access denied. Only parents are allowed.'], 403);
    }
}
