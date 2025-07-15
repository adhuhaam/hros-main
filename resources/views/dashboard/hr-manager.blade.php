@extends('layouts.app')

@section('title', 'HR Manager Dashboard - HR Management System')

@section('header', 'HR Manager Dashboard')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .stat-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .department-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">HR Manager Dashboard</h1>
                <p class="text-green-100 mt-1">Manage your workforce effectively</p>
            </div>
            <div class="text-right">
                <p class="text-green-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-green-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Employees -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Active: {{ number_format($stats['active_employees']) }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_leaves']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Requires approval</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- New Employees This Month -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New This Month</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['new_employees_this_month']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Recent hires</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-user-plus text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Expiring Contracts -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Expiring Contracts</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['expiring_contracts']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Needs renewal</p>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-calendar-times text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Department Distribution -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Department Distribution</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if($departmentStats->count() > 0)
                    @foreach($departmentStats as $dept)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $dept->department ?? 'Unknown' }}</p>
                                    <p class="text-2xl font-bold text-blue-600">{{ $dept->count }}</p>
                                </div>
                                <div class="p-2 rounded-full bg-blue-100 text-blue-600">
                                    <i class="fa-solid fa-building text-lg"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-8">
                        <i class="fa-solid fa-building text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No department data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Employees -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Recently Added Employees</h3>
                <a href="{{ route('employees.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($recentEmployees->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentEmployees as $employee)
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-medium text-gray-900">{{ $employee->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $employee->designation ?? 'Employee' }} - {{ $employee->department ?? 'Department' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Hired: {{ \Carbon\Carbon::parse($employee->date_of_join)->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    @php
                                        $statusClasses = '';
                                        if($employee->employment_status === 'Active') {
                                            $statusClasses = 'bg-green-100 text-green-800';
                                        } else {
                                            $statusClasses = 'bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $employee->employment_status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-users text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No recent employees</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Leave Requests -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Pending Leave Requests</h3>
                <a href="{{ route('leaves.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($pendingLeaves->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingLeaves as $leave)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $leave->employee->name ?? 'Unknown Employee' }}</p>
                                        <p class="text-sm text-gray-600">{{ $leave->leave_type ?? 'Leave' }} - {{ $leave->days_requested }} days</p>
                                        <p class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} to 
                                            {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Pending
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-calendar-check text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No pending leave requests</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Expiring Contracts Alert -->
    @if($expiringContracts->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Expiring Contracts</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($expiringContracts->take(5) as $employee)
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fa-solid fa-exclamation-triangle text-red-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $employee->name }}</p>
                                <p class="text-sm text-gray-600">{{ $employee->designation ?? 'Employee' }} - {{ $employee->department ?? 'Department' }}</p>
                                <p class="text-xs text-red-600">
                                    Contract expires: {{ \Carbon\Carbon::parse($employee->date_of_join)->addYears(2)->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                Expiring Soon
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
            @if($expiringContracts->count() > 5)
                <div class="mt-4 text-center">
                    <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        View all {{ $expiringContracts->count() }} expiring contracts →
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('employees.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                    <i class="fa-solid fa-user-plus text-blue-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-blue-900">Add Employee</span>
                </a>
                
                <a href="{{ route('leaves.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-calendar-check text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Review Leaves</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-file-contract text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Contracts</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-chart-bar text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Reports</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 