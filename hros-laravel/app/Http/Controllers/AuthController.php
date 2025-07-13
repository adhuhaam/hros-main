<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            
            // Update last login
            $user->update(['last_login' => now()]);
            
            $request->session()->regenerate();

            // Redirect based on role
            return $this->redirectBasedOnRole($user->role);
        }

        return redirect()->back()
            ->withErrors(['username' => 'Invalid credentials'])
            ->withInput();
    }

    /**
     * Handle the logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle the forgot password request.
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

        // Here you would typically send a password reset email
        // For now, we'll just show a success message
        return redirect()->back()
            ->with('success', 'Password reset link has been sent to your email.');
    }

    /**
     * Show the profile page.
     */
    public function profile()
    {
        $user = Auth::user();
        return view('auth.profile', compact('user'));
    }

    /**
     * Update the user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Update basic info
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update password if provided
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Current password is incorrect'])
                    ->withInput();
            }

            $user->update([
                'password' => Hash::make($request->new_password)
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectBasedOnRole($role)
    {
        switch ($role) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Information Officer':
                return redirect()->route('info-officer.dashboard');
            case 'Xpat Officer':
                return redirect()->route('xpat-officer.dashboard');
            case 'Leave Officer':
                return redirect()->route('leave-officer.dashboard');
            case 'HR Manager':
                return redirect()->route('hr-manager.dashboard');
            case 'Payroll Officer':
                return redirect()->route('payroll-officer.dashboard');
            case 'Supervisor':
                return redirect()->route('supervisor.dashboard');
            case 'Other Staff':
                return redirect()->route('other-staff.dashboard');
            case 'reception':
                return redirect()->route('reception.dashboard');
            default:
                return redirect()->route('dashboard');
        }
    }
}