@extends('layouts.app')

@section('title', 'Role Management')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Role Management</h1>
            <p class="text-gray-600 mt-2">Manage user roles and their permissions</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('roles.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                <i class="fa-solid fa-plus mr-2"></i>Create New Role
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

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" action="{{ route('roles.index') }}" class="flex gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Search roles by name or description..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                <i class="fa-solid fa-search mr-2"></i>Search
            </button>
            @if(request('search'))
                <a href="{{ route('roles.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600">
                    <i class="fa-solid fa-times mr-2"></i>Clear
                </a>
            @endif
        </form>
    </div>

    <!-- Roles List -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Role Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Users
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($roles as $role)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-user-shield text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $role->role_name }}
                                            @if($role->role_name === 'Admin')
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Admin
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: {{ $role->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    {{ $role->description ?: 'No description provided' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $role->users_count }} user(s)
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @php
                                        $permissions = $role->permissions ?? [];
                                        $permissionCount = count($permissions);
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $permissionCount }} permission(s)
                                    </span>
                                    @if($permissionCount > 0)
                                        <div class="mt-1 text-xs text-gray-500">
                                            {{ implode(', ', array_slice($permissions, 0, 3)) }}
                                            @if($permissionCount > 3)
                                                <span class="text-gray-400">+{{ $permissionCount - 3 }} more</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('roles.show', $role) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="View Role">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('roles.permissions', $role) }}" 
                                       class="text-green-600 hover:text-green-900" 
                                       title="Manage Permissions">
                                        <i class="fa-solid fa-key"></i>
                                    </a>
                                    @if($role->role_name !== 'Admin')
                                        <a href="{{ route('roles.edit', $role) }}" 
                                           class="text-indigo-600 hover:text-indigo-900" 
                                           title="Edit Role">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    title="Delete Role"
                                                    onclick="return confirm('Are you sure you want to delete this role?')">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                <div class="flex flex-col items-center py-8">
                                    <i class="fa-solid fa-user-shield text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium text-gray-900 mb-2">No roles found</p>
                                    <p class="text-gray-600">Get started by creating your first role.</p>
                                    <a href="{{ route('roles.create') }}" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600">
                                        <i class="fa-solid fa-plus mr-2"></i>Create Role
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($roles->hasPages())
        <div class="mt-6">
            {{ $roles->links() }}
        </div>
    @endif
</div>
@endsection 