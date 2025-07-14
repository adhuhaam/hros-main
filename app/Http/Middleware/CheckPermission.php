<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please log in to access this page.');
        }

        $user = auth()->user();

        // If user is admin, allow access to everything
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Check if user has any of the required permissions
        if (!empty($permissions)) {
            $hasPermission = false;
            
            foreach ($permissions as $permission) {
                if ($user->hasPermission($permission)) {
                    $hasPermission = true;
                    break;
                }
            }

            if (!$hasPermission) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'error' => 'Insufficient permissions to access this resource.',
                        'required_permissions' => $permissions
                    ], 403);
                }

                return redirect()->back()->with('error', 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }
} 