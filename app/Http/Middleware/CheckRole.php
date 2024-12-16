<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if ($request->user()->role !== $role) {
            return response()->json(['message' => 'Forbidden'], 403);  // 403 Forbidden if role doesn't match
        }

        return $next($request);
    }
}
