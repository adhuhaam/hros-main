@extends('layouts.app')

@section('title', 'Role Details - ' . $role->role_name)

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Role Details</h1>
            <p class="text-gray-600 mt-2">{{ $role->role_name }}</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('roles.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                <i class="fa-solid fa-arrow-left mr-2"></i>Back to Roles
            </a>
            <a href="{{ route('roles.permissions', $role) }}" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                <i class="fa-solid fa-key mr-2"></i>Manage Permissions
            </a>
            @if($role->role_name !== 'Admin')
                <a href="{{ route('roles.edit', $role) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                    <i class="fa-solid fa-edit mr-2"></i>Edit Role
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Role Information -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Role Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Role Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->role_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role ID</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->id }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <p class="mt-1 text-sm text-gray-900">{{ $role->description ?: 'No description provided' }}</p>
            </div>
        </div>
    </div>

    <!-- Users with this Role -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Users with this Role</h2>
        @if($role->users->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Employee No
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($role->users as $user)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-sm font-medium text-blue-600">
                                                    {{ strtoupper(substr($user->staff_name, 0, 2)) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $user->staff_name }}</div>
                                            <div class="text-sm text-gray-500">{{ $user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->emp_no }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('users.show', $user) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fa-solid fa-users text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-900 mb-2">No users assigned</p>
                <p class="text-gray-600">No users are currently assigned to this role.</p>
            </div>
        @endif
    </div>

    <!-- Permissions Summary -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Permissions Summary</h2>
        @php
            $permissions = $role->permissions ?? [];
            $modules = config('modules');
            $modulePermissions = [];
            
            foreach ($modules as $moduleKey => $module) {
                $modulePermissions[$moduleKey] = [
                    'name' => $module['name'],
                    'icon' => $module['icon'],
                    'permissions' => array_intersect_key($module['permissions'], array_flip($permissions))
                ];
            }
        @endphp

        @if(count($permissions) > 0)
            <div class="space-y-4">
                @foreach($modulePermissions as $moduleKey => $moduleData)
                    @if(count($moduleData['permissions']) > 0)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center mb-3">
                                <i class="{{ $moduleData['icon'] }} text-xl text-blue-600 mr-3"></i>
                                <h3 class="text-lg font-medium text-gray-800">{{ $moduleData['name'] }}</h3>
                                <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ count($moduleData['permissions']) }} permission(s)
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($moduleData['permissions'] as $permission => $description)
                                    <div class="flex items-center p-2 bg-green-50 rounded">
                                        <i class="fa-solid fa-check text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-700">{{ $description }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                <!-- System Permissions -->
                @php
                    $systemPermissions = array_filter($permissions, function($permission) {
                        return strpos($permission, 'system.') === 0;
                    });
                @endphp
                @if(count($systemPermissions) > 0)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center mb-3">
                            <i class="fa-solid fa-cogs text-xl text-purple-600 mr-3"></i>
                            <h3 class="text-lg font-medium text-gray-800">System Permissions</h3>
                            <span class="ml-auto inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                {{ count($systemPermissions) }} permission(s)
                            </span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($systemPermissions as $permission)
                                <div class="flex items-center p-2 bg-purple-50 rounded">
                                    <i class="fa-solid fa-check text-purple-600 mr-2"></i>
                                    <span class="text-sm text-gray-700">{{ ucfirst(str_replace('system.', '', $permission)) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-8">
                <i class="fa-solid fa-lock text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-900 mb-2">No permissions assigned</p>
                <p class="text-gray-600">This role has no permissions assigned to it.</p>
                <a href="{{ route('roles.permissions', $role) }}" class="mt-4 inline-flex bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600">
                    <i class="fa-solid fa-key mr-2"></i>Assign Permissions
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 