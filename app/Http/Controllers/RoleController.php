<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $query = Role::withCount('users');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('role_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $roles = $query->orderBy('role_name')->paginate(15);

        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $allPermissions = Permission::getGroupedByModule();
        
        return view('roles.create', compact('allPermissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|string|max:50|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role = Role::create([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        if ($request->permissions) {
            $role->setPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
                        ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $role->load(['users', 'permissions']);
        $groupedPermissions = Permission::getGroupedByModule();
        
        return view('roles.show', compact('role', 'groupedPermissions'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $allPermissions = Permission::getGroupedByModule();
        $rolePermissions = $role->getPermissionNames();
        
        return view('roles.edit', compact('role', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => ['required', 'string', 'max:50', Rule::unique('roles')->ignore($role->id)],
            'description' => 'nullable|string',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        if ($request->permissions) {
            $role->setPermissions($request->permissions);
        }

        return redirect()->route('roles.index')
                        ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting role if it has users
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has users assigned to it.');
        }

        // Prevent deleting Admin role
        if ($role->role_name === 'Admin') {
            return back()->with('error', 'Cannot delete the Admin role.');
        }

        $role->delete();

        return redirect()->route('roles.index')
                        ->with('success', 'Role deleted successfully.');
    }

    /**
     * Show permissions management for a role.
     */
    public function permissions(Role $role)
    {
        $allPermissions = Permission::getGroupedByModule();
        $rolePermissions = $role->getPermissionNames();
        
        return view('roles.permissions', compact('role', 'allPermissions', 'rolePermissions'));
    }

    /**
     * Update permissions for a role.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $role->setPermissions($request->permissions ?? []);

        return redirect()->route('roles.show', $role)
                        ->with('success', 'Permissions updated successfully.');
    }

    /**
     * Duplicate a role.
     */
    public function duplicate(Role $role)
    {
        $newRole = Role::create([
            'role_name' => $role->role_name . ' (Copy)',
            'description' => $role->description . ' (Duplicate)',
        ]);

        // Copy permissions
        $permissionIds = $role->permissions()->pluck('permissions.id')->toArray();
        $newRole->permissions()->sync($permissionIds);

        return redirect()->route('roles.edit', $newRole)
                        ->with('success', 'Role duplicated successfully. Please update the name and description.');
    }

    /**
     * Get all available permissions (API endpoint).
     */
    public function getAllPermissions()
    {
        return response()->json([
            'permissions' => Permission::getAllNames(),
            'grouped_permissions' => Permission::getGroupedByModule(),
        ]);
    }

    /**
     * Bulk update permissions for multiple roles.
     */
    public function bulkUpdatePermissions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_permissions' => 'required|array',
            'role_permissions.*.role_id' => 'required|exists:roles,id',
            'role_permissions.*.permissions' => 'array',
            'role_permissions.*.permissions.*' => 'string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        foreach ($request->role_permissions as $roleData) {
            $role = Role::find($roleData['role_id']);
            if ($role) {
                $role->setPermissions($roleData['permissions'] ?? []);
            }
        }

        return redirect()->route('roles.index')
                        ->with('success', 'Permissions updated for selected roles.');
    }

    /**
     * Export roles and permissions.
     */
    public function export()
    {
        $roles = Role::all();
        
        $filename = 'roles_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($roles) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            fputcsv($file, [
                'Role Name', 'Description', 'Permissions Count', 'Users Count', 'Permissions'
            ]);

            // Add data
            foreach ($roles as $role) {
                $permissions = $role->permissions ?? [];
                fputcsv($file, [
                    $role->role_name,
                    $role->description,
                    count($permissions),
                    $role->users()->count(),
                    implode('; ', $permissions),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Reset role to default permissions.
     */
    public function resetToDefaults(Role $role)
    {
        // Define default permissions for known roles
        $defaultPermissions = [
            'Admin' => Role::getAllPermissions(),
            'HR Manager' => [
                'dashboard.hr_manager',
                'employees.view', 'employees.create', 'employees.edit', 'employees.export', 'employees.import',
                'attendance.view', 'attendance.create', 'attendance.edit', 'attendance.approve', 'attendance.bulk_approve', 'attendance.export', 'attendance.report',
                'leaves.view', 'leaves.create', 'leaves.edit', 'leaves.approve', 'leaves.reject', 'leaves.export', 'leaves.report',
                'loans.view', 'loans.create', 'loans.edit', 'loans.approve', 'loans.reject', 'loans.export', 'loans.report',
                'medical.view', 'medical.create', 'medical.edit', 'medical.export',
                'warnings.view', 'warnings.create', 'warnings.edit',
                'users.view', 'users.create', 'users.edit', 'users.reset_password',
                'reports.view', 'reports.employee', 'reports.attendance', 'reports.leave', 'reports.loan',
            ],
            'Employee' => [
                'dashboard.employee',
                'attendance.check_in', 'attendance.check_out',
                'leaves.view', 'leaves.create',
                'loans.view', 'loans.create',
            ],
        ];

        if (isset($defaultPermissions[$role->role_name])) {
            $role->setPermissions($defaultPermissions[$role->role_name]);
            return redirect()->back()->with('success', 'Role permissions reset to defaults.');
        }

        return redirect()->back()->with('error', 'No default permissions defined for this role.');
    }

    /**
     * Get role statistics.
     */
    public function statistics()
    {
        $stats = [
            'total_roles' => Role::count(),
            'roles_with_users' => Role::has('users')->count(),
            'roles_without_users' => Role::doesntHave('users')->count(),
            'most_permissions' => Role::selectRaw('role_name, JSON_LENGTH(permissions) as permission_count')
                                    ->orderByRaw('JSON_LENGTH(permissions) DESC')
                                    ->first(),
            'role_user_counts' => Role::withCount('users')
                                    ->orderBy('users_count', 'desc')
                                    ->get(['role_name', 'users_count']),
            'permission_usage' => $this->getPermissionUsageStats(),
        ];

        return response()->json($stats);
    }

    /**
     * Get permission usage statistics.
     */
    private function getPermissionUsageStats()
    {
        $allPermissions = Role::getAllPermissions();
        $permissionCounts = [];

        foreach ($allPermissions as $permission) {
            $count = Role::whereJsonContains('permissions', $permission)->count();
            $permissionCounts[$permission] = $count;
        }

        // Sort by usage count
        arsort($permissionCounts);

        return $permissionCounts;
    }
} 