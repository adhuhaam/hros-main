@php
    $user = auth()->user();
    $modules = config('modules');
@endphp

<div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
    <div class="flex items-center justify-between p-4 border-b">
        <div class="flex items-center">
            <img src="/assets/images/logos/dark-logo.svg" alt="Logo" class="h-8">
            <span class="ml-2 text-lg font-semibold text-gray-800">HRoS</span>
        </div>
        <button id="sidebar-close" class="lg:hidden text-gray-500 hover:text-gray-700">
            <i class="ti ti-x text-xl"></i>
        </button>
    </div>
    
    <nav class="mt-4">
        <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
            Navigation
        </div>
        
        <ul class="space-y-1">
            <!-- Dashboard - Always visible for authenticated users -->
            <li>
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fa-solid fa-house mr-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Dynamic Modules based on permissions -->
            @foreach($modules as $moduleKey => $module)
                @if($user->hasPermission($module['permission']))
                    <li>
                        <a href="{{ route($module['route']) }}" 
                           class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs($module['route'].'*') ? 'bg-blue-50 text-blue-600' : '' }}">
                            <i class="{{ $module['icon'] }} mr-3"></i>
                            <span>{{ $module['name'] }}</span>
                        </a>
                    </li>
                @endif
            @endforeach

            <!-- Role Management - Admin only -->
            @if($user->hasPermission('roles.view'))
                <li>
                    <a href="{{ route('roles.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('roles.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fa-solid fa-user-shield mr-3"></i>
                        <span>Role Management</span>
                    </a>
                </li>
            @endif

            <!-- User Management - Admin only -->
            @if($user->hasPermission('users.view'))
                <li>
                    <a href="{{ route('users.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fa-solid fa-users-cog mr-3"></i>
                        <span>User Management</span>
                    </a>
                </li>
            @endif

            <!-- Reports - Based on permissions -->
            @if($user->hasAnyPermission(['reports.employee', 'reports.attendance', 'reports.leave', 'reports.loan', 'reports.payroll']))
                <li>
                    <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fa-solid fa-chart-bar mr-3"></i>
                        <span>Reports</span>
                    </a>
                </li>
            @endif

            <!-- Settings - Admin only -->
            @if($user->hasPermission('settings.view'))
                <li>
                    <a href="{{ route('settings.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fa-solid fa-cog mr-3"></i>
                        <span>Settings</span>
                    </a>
                </li>
            @endif
            
            <!-- Profile -->
            <li class="border-t mt-4 pt-4">
                <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600 {{ request()->routeIs('profile*') ? 'bg-blue-50 text-blue-600' : '' }}">
                    <i class="fa-solid fa-user mr-3"></i>
                    <span>Profile</span>
                </a>
            </li>
            
            <!-- Logout -->
            <li>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600">
                        <i class="fa-solid fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </li>
        </ul>
    </nav>
</div> 