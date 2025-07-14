<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = auth()->user();

        // If user is admin, allow access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required roles
        if (!empty($roles)) {
            $hasRole = false;
            
            foreach ($roles as $role) {
                if ($user->hasRole($role)) {
                    $hasRole = true;
                    break;
                }
            }

            if (!$hasRole) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Insufficient role privileges to access this resource.',
                        'required_roles' => $roles,
                        'user_role' => $user->role_name
                    ], 403);
                }

                return redirect()->back()->with('error', 'Your role does not have access to this page.');
            }
        }

        return $next($request);
    }
} 