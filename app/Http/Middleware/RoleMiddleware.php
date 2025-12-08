<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthenticated.'
            ], 401);
        }

        if (!$user || $user->role !== $role) {
            return response()->json([
                'error' => 'Unauthorized - role required: '.$role
            ], 403);
        }

        return $next($request);
    }
}
