@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Welcome, {{ auth()->user()->staff_name }}!</h1>
        <p class="text-gray-600 mt-2">Role: {{ auth()->user()->role_name }}</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- User Info Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">User Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Employee Number</label>
                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->emp_no }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Username</label>
                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->username }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->email }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Role</label>
                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->role_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Designation</label>
                <p class="mt-1 text-sm text-gray-900">{{ auth()->user()->des }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Permissions</label>
                <p class="mt-1 text-sm text-gray-900">{{ count(auth()->user()->getPermissions()) }} permissions</p>
            </div>
        </div>
    </div>

    <!-- Accessible Modules -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Your Accessible Modules</h2>
        @php
            $modules = config('modules');
            $accessibleModules = [];
            foreach ($modules as $moduleKey => $module) {
                if (auth()->user()->hasPermission($module['permission'])) {
                    $accessibleModules[] = $module;
                }
            }
        @endphp

        @if(count($accessibleModules) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($accessibleModules as $module)
                    <a href="{{ route($module['route']) }}" 
                       class="block p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                        <div class="flex items-center">
                            <i class="{{ $module['icon'] }} text-2xl text-blue-600 mr-3"></i>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">{{ $module['name'] }}</h3>
                                <p class="text-sm text-gray-600">{{ $module['description'] }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fa-solid fa-lock text-4xl text-gray-300 mb-4"></i>
                <p class="text-lg font-medium text-gray-900 mb-2">No modules accessible</p>
                <p class="text-gray-600">Contact your administrator to get access to modules.</p>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('profile') }}" 
               class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 transition-colors">
                <i class="fa-solid fa-user text-xl text-blue-600 mr-3"></i>
                <span class="text-gray-900">View Profile</span>
            </a>
            
            @if(auth()->user()->hasPermission('attendance.check_in'))
                <a href="{{ route('attendance.check-in-page') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors">
                    <i class="fa-solid fa-clock text-xl text-green-600 mr-3"></i>
                    <span class="text-gray-900">Check In/Out</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('leaves.create'))
                <a href="{{ route('leaves.create') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-yellow-300 hover:bg-yellow-50 transition-colors">
                    <i class="fa-solid fa-calendar-plus text-xl text-yellow-600 mr-3"></i>
                    <span class="text-gray-900">Request Leave</span>
                </a>
            @endif

            @if(auth()->user()->hasPermission('loans.create'))
                <a href="{{ route('loans.create') }}" 
                   class="flex items-center p-3 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors">
                    <i class="fa-solid fa-money-bill text-xl text-purple-600 mr-3"></i>
                    <span class="text-gray-900">Apply for Loan</span>
                </a>
            @endif
        </div>
    </div>
</div>
@endsection 