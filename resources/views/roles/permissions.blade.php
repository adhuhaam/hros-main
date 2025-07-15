@extends('layouts.app')

@section('title', 'Role Permissions - ' . $role->role_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Role Permissions</h1>
            <p class="text-gray-600 mt-2">Manage permissions for role: <strong>{{ $role->role_name }}</strong></p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('roles.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                <i class="fa-solid fa-arrow-left mr-2"></i>Back to Roles
            </a>
            <a href="{{ route('roles.edit', $role) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                <i class="fa-solid fa-edit mr-2"></i>Edit Role
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('roles.permissions.update', $role) }}">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Module Permissions</h2>
                <p class="text-gray-600 mb-4">Select which modules and permissions this role should have access to:</p>
            </div>

            @php
                $modules = config('modules');
            @endphp

            <div class="space-y-6">
                @foreach($modules as $moduleKey => $module)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <i class="{{ $module['icon'] }} text-xl text-blue-600 mr-3"></i>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $module['name'] }}</h3>
                                    <p class="text-sm text-gray-600">{{ $module['description'] }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           class="module-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           data-module="{{ $moduleKey }}"
                                           {{ in_array($module['permission'], $rolePermissions) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm font-medium text-gray-700">Module Access</span>
                                </label>
                            </div>
                        </div>

                        <div class="ml-8">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($module['permissions'] as $permission => $description)
                                    <label class="flex items-center p-2 rounded hover:bg-gray-50">
                                        <input type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission }}"
                                               class="permission-checkbox h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                               data-module="{{ $moduleKey }}"
                                               {{ in_array($permission, $rolePermissions) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700">{{ $description }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Additional System Permissions -->
            <div class="border border-gray-200 rounded-lg p-4 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fa-solid fa-cogs text-xl text-purple-600 mr-3"></i>
                    System Permissions
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    <label class="flex items-center p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" name="permissions[]" value="system.admin" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" {{ in_array('system.admin', $rolePermissions) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">System Administrator</span>
                    </label>
                    <label class="flex items-center p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" name="permissions[]" value="system.settings" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" {{ in_array('system.settings', $rolePermissions) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">System Settings</span>
                    </label>
                    <label class="flex items-center p-2 rounded hover:bg-gray-50">
                        <input type="checkbox" name="permissions[]" value="system.logs" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" {{ in_array('system.logs', $rolePermissions) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">System Logs</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('roles.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-md hover:bg-gray-600">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-md hover:bg-blue-600">
                    <i class="fa-solid fa-save mr-2"></i>Save Permissions
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Module checkbox functionality
    const moduleCheckboxes = document.querySelectorAll('.module-checkbox');
    
    moduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const moduleKey = this.dataset.module;
            const permissionCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${moduleKey}"]`);
            
            permissionCheckboxes.forEach(permCheckbox => {
                permCheckbox.checked = this.checked;
            });
        });
    });

    // Permission checkbox functionality
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    
    permissionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const moduleKey = this.dataset.module;
            const moduleCheckbox = document.querySelector(`.module-checkbox[data-module="${moduleKey}"]`);
            const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${moduleKey}"]`);
            const checkedPermissions = Array.from(modulePermissions).filter(cb => cb.checked);
            
            // Update module checkbox based on permission checkboxes
            if (checkedPermissions.length === 0) {
                moduleCheckbox.checked = false;
            } else if (checkedPermissions.length === modulePermissions.length) {
                moduleCheckbox.checked = true;
            } else {
                moduleCheckbox.indeterminate = true;
            }
        });
    });

    // Initialize module checkboxes
    moduleCheckboxes.forEach(checkbox => {
        const moduleKey = checkbox.dataset.module;
        const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${moduleKey}"]`);
        const checkedPermissions = Array.from(modulePermissions).filter(cb => cb.checked);
        
        if (checkedPermissions.length > 0 && checkedPermissions.length < modulePermissions.length) {
            checkbox.indeterminate = true;
        }
    });
});
</script>
@endsection 