<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        // Rate limiting
        $this->ensureIsNotRateLimited($request);

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput($request->only('username'));
        }

        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            
            // Check if user is active
            if (!$user->isActive()) {
                Auth::logout();
                return redirect()->back()
                    ->withErrors(['username' => 'Your account has been deactivated.'])
                    ->withInput($request->only('username'));
            }

            // Update last login
            $user->updateLastLogin();

            // Clear rate limiting
            RateLimiter::clear($this->throttleKey($request));

            // Log successful login
            $this->logSecurityEvent('successful_login', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->redirectToDashboard();
        }

        // Increment rate limiting
        RateLimiter::hit($this->throttleKey($request));

        // Log failed login
        $this->logSecurityEvent('failed_login', [
            'username' => $request->username,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return redirect()->back()
            ->withErrors(['username' => 'Invalid Employee No or password!'])
            ->withInput($request->only('username'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        if ($user) {
            // Log logout event
            $this->logSecurityEvent('logout', [
                'user_id' => $user->id,
                'username' => $user->username,
                'ip' => $request->ip()
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'You have been successfully logged out.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Generate password reset token
            $token = Str::random(60);
            
            // Store token in database (you might want to create a password_resets table)
            // For now, we'll just show a success message
            
            // Log password reset request
            $this->logSecurityEvent('password_reset_requested', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            return redirect()->back()
                ->with('success', 'Password reset instructions have been sent to your email.');
        }

        return redirect()->back()
            ->withErrors(['email' => 'We could not find a user with that email address.']);
    }

    /**
     * Show profile page
     */
    public function profile()
    {
        $user = Auth::user();
        $employee = $user->employee;

        return view('auth.profile', compact('user', 'employee'));
    }

    /**
     * Update profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Check current password if provided
        if ($request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }
        }

        // Update user
        $user->update([
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('new_password')) {
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        // Update employee name if different
        if ($user->employee && $user->employee->name !== $request->name) {
            $user->employee->update(['name' => $request->name]);
        }

        return redirect()->back()
            ->with('success', 'Profile updated successfully.');
    }

    /**
     * Redirect to appropriate dashboard based on user role
     */
    protected function redirectToDashboard()
    {
        $user = Auth::user();
        $role = $user->role;

        if (!$role) {
            return redirect()->route('other.dashboard');
        }

        $dashboardRoute = $role->dashboard_route ?? 'other.dashboard';
        
        return redirect()->route($dashboardRoute);
    }

    /**
     * Ensure the login request is not rate limited.
     */
    protected function ensureIsNotRateLimited(Request $request)
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('username')).'|'.$request->ip());
    }

    /**
     * Log security events
     */
    protected function logSecurityEvent($event, $details = [])
    {
        // You can implement logging to database or file here
        \Log::info('SECURITY: ' . $event, $details);
    }

    /**
     * Check if user is authenticated and redirect if not
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'profile', 'updateProfile']);
        $this->middleware('auth')->only(['logout', 'profile', 'updateProfile']);
    }
}