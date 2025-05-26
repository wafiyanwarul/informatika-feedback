<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user = \Illuminate\Support\Facades\Auth::user();

        // Customize this check based on your user model
        if (!($user->is_admin ?? false)) { // or $user->role === 'admin' or $user->hasRole('admin')
            return response()->json(['error' => 'Unauthorized - Admin access required'], 403);
        }

        return $next($request);
    }
}
