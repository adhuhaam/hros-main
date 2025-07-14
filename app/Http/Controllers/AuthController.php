<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->intended($this->getRedirectPath());
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->only('email'));
        }

        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds."
            ]);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($key);

            // Log successful login
            $this->logLoginActivity(Auth::user(), $request, 'success');

            return redirect()->intended($this->getRedirectPath());
        }

        // Increment rate limiter
        RateLimiter::hit($key);

        // Log failed login attempt
        $this->logLoginActivity(null, $request, 'failed');

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Log logout activity
        if ($user) {
            $this->logLoginActivity($user, $request, 'logout');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show the registration form (if enabled).
     */
    public function showRegistrationForm()
    {
        // Registration might be disabled in production
        if (!config('auth.allow_registration', false)) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled.');
        }

        $roles = Role::where('role_name', '!=', 'Admin')->get(); // Don't allow admin registration
        return view('auth.register', compact('roles'));
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        if (!config('auth.allow_registration', false)) {
            return redirect()->route('login')->with('error', 'Registration is currently disabled.');
        }

        $validator = Validator::make($request->all(), [
            'emp_no' => 'required|string|max:10|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'staff_name' => 'required|string|max:225',
            'des' => 'required|string|max:225',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify employee exists and is not already a user
        $employee = \App\Models\Employee::where('emp_no', $request->emp_no)->first();
        if (!$employee) {
            return back()->withErrors(['emp_no' => 'Employee not found in the system.'])->withInput();
        }

        // Prevent admin role self-registration
        $role = Role::find($request->role_id);
        if ($role->role_name === 'Admin') {
            return back()->withErrors(['role_id' => 'Cannot register as Admin.'])->withInput();
        }

        $user = User::create([
            'emp_no' => $request->emp_no,
            'username' => $request->username,
            'staff_name' => $request->staff_name,
            'des' => $request->des,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
        ]);

        // Auto-login after registration
        Auth::login($user);

        // Log registration
        $this->logLoginActivity($user, $request, 'register');

        return redirect($this->getRedirectPath())->with('success', 'Registration successful! Welcome to the system.');
    }

    /**
     * Show forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request.
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // In a real application, you would send a password reset email here
        // For now, we'll just show a success message
        return back()->with('success', 'Password reset instructions have been sent to your email address.');
    }

    /**
     * Get the redirect path after login based on user role.
     */
    private function getRedirectPath()
    {
        $user = Auth::user();
        
        if (!$user) {
            return '/dashboard';
        }

        return route($user->getDashboardRoute());
    }

    /**
     * Log login/logout activity.
     */
    private function logLoginActivity($user, Request $request, $type)
    {
        // In a real application, you might want to log this to a database table
        // For now, we'll use Laravel's built-in logging
        $data = [
            'type' => $type,
            'user_id' => $user ? $user->id : null,
            'username' => $user ? $user->username : $request->login,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now(),
        ];

        \Log::info('Auth Activity', $data);
    }

    /**
     * Check if user is authenticated (API endpoint).
     */
    public function checkAuth()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->load('role');

            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'staff_name' => $user->staff_name,
                    'email' => $user->email,
                    'role' => $user->role->role_name,
                    'permissions' => $user->getPermissions(),
                ],
            ]);
        }

        return response()->json(['authenticated' => false], 401);
    }

    /**
     * Get current user's information (API endpoint).
     */
    public function me()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $user->load(['role', 'employee']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'emp_no' => $user->emp_no,
                'username' => $user->username,
                'staff_name' => $user->staff_name,
                'des' => $user->des,
                'email' => $user->email,
                'role' => $user->role->role_name,
                'permissions' => $user->getPermissions(),
                'accessible_modules' => $user->getAccessibleModules(),
                'dashboard_route' => $user->getDashboardRoute(),
                'initials' => $user->initials,
                'full_name' => $user->full_name,
            ],
            'employee' => $user->employee ? [
                'emp_no' => $user->employee->emp_no,
                'name' => $user->employee->name,
                'designation' => $user->employee->designation,
                'department' => $user->employee->department,
                'phone' => $user->employee->phone,
                'email' => $user->employee->email,
            ] : null,
        ]);
    }

    /**
     * Change password.
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['errors' => ['current_password' => ['Current password is incorrect.']]], 422);
        }

        $user->updatePassword($request->password);

        return response()->json(['message' => 'Password changed successfully.']);
    }

    /**
     * Impersonate user (Admin only).
     */
    public function impersonate(User $user)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Store original user in session
        session(['impersonator' => Auth::id()]);
        
        Auth::login($user);

        return redirect($this->getRedirectPath())
                ->with('info', "You are now impersonating {$user->full_name}. Click 'Stop Impersonating' to return to your account.");
    }

    /**
     * Stop impersonating.
     */
    public function stopImpersonating()
    {
        if (!session('impersonator')) {
            return redirect()->route('dashboard')->with('error', 'You are not impersonating anyone.');
        }

        $originalUserId = session('impersonator');
        session()->forget('impersonator');

        $originalUser = User::find($originalUserId);
        if ($originalUser) {
            Auth::login($originalUser);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Stopped impersonating user.');
    }
}