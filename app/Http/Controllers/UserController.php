<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with(['role', 'employee']);

        // Filter by role
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('staff_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('emp_no', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('staff_name')->paginate(20);
        $roles = Role::all();

        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        $employees = Employee::whereDoesntHave('user')->orderBy('name')->get();

        return view('users.create', compact('roles', 'employees'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_no' => 'required|string|max:10|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'staff_name' => 'nullable|string|max:225',
            'des' => 'required|string|max:225',
            'email' => 'nullable|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify employee exists
        $employee = Employee::where('emp_no', $request->emp_no)->first();
        if (!$employee) {
            return back()->withErrors(['emp_no' => 'Employee not found.'])->withInput();
        }

        $user = User::create([
            'emp_no' => $request->emp_no,
            'username' => $request->username,
            'staff_name' => $request->staff_name ?: $employee->name,
            'des' => $request->des,
            'email' => $request->email,
            'password' => $request->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load(['role', 'employee']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $employees = Employee::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles', 'employees'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'emp_no' => ['required', 'string', 'max:10', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'staff_name' => 'nullable|string|max:225',
            'des' => 'required|string|max:225',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'role_id' => 'required|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify employee exists if emp_no changed
        if ($request->emp_no !== $user->emp_no) {
            $employee = Employee::where('emp_no', $request->emp_no)->first();
            if (!$employee) {
                return back()->withErrors(['emp_no' => 'Employee not found.'])->withInput();
            }
        }

        $user->update([
            'emp_no' => $request->emp_no,
            'username' => $request->username,
            'staff_name' => $request->staff_name,
            'des' => $request->des,
            'email' => $request->email,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        // Prevent deleting own account
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Prevent deleting the last admin user
        if ($user->isAdmin() && User::withRole('Admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last admin user.');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully.');
    }

    /**
     * Show form to reset user password.
     */
    public function showResetPasswordForm(User $user)
    {
        return view('users.reset-password', compact('user'));
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user->updatePassword($request->password);

        return redirect()->route('users.index')
                        ->with('success', 'Password reset successfully.');
    }

    /**
     * Activate/deactivate user.
     */
    public function toggleStatus(User $user)
    {
        // This would require an 'is_active' field in the users table
        // For now, we'll just return a message
        return back()->with('info', 'User status toggle functionality can be implemented with an is_active field.');
    }

    /**
     * Export users list.
     */
    public function export(Request $request)
    {
        $query = User::with(['role', 'employee']);

        // Apply same filters as index
        if ($request->filled('role_id')) {
            $query->where('role_id', $request->role_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                  ->orWhere('staff_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('emp_no', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Employee No', 'Username', 'Full Name', 'Designation', 'Email', 'Role', 'Created At'
            ]);

            // Add data
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->emp_no,
                    $user->username,
                    $user->staff_name,
                    $user->des,
                    $user->email,
                    $user->role->role_name ?? 'N/A',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show user profile.
     */
    public function profile()
    {
        $user = auth()->user();
        $user->load(['role', 'employee']);
        
        return view('users.profile', compact('user'));
    }

    /**
     * Update user profile.
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'staff_name' => 'nullable|string|max:225',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify current password if new password is provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        $updateData = [
            'staff_name' => $request->staff_name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = $request->password;
        }

        $user->update($updateData);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Get user permissions for API.
     */
    public function permissions(User $user)
    {
        return response()->json([
            'user' => $user->only(['id', 'username', 'staff_name', 'role_id']),
            'role' => $user->role->only(['id', 'role_name', 'description']),
            'permissions' => $user->getPermissions(),
            'accessible_modules' => $user->getAccessibleModules(),
        ]);
    }
} 