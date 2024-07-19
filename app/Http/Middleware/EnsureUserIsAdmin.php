<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $user = auth()->user();

        if ($user && $user->role) { // Assuming 'username' is a unique field in your 'admins' table
            return $next($request);
        }

        return response()->json(['message' => "Unauthorized"], 403);

    }
}
