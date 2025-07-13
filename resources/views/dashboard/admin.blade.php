@extends('layouts.app')

@section('title', 'Admin Dashboard - HR Management System')

@section('header', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        <!-- Total Employees -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Employees</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_employees'] }}</p>
                </div>
            </div>
        </div>

        <!-- Active Employees -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-user-check text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Employees</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_employees'] }}</p>
                </div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_leaves'] }}</p>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-money-bill text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Loans</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_loans'] }}</p>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                    <i class="fa-solid fa-calendar-check text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Present Today</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['today_attendance'] }}</p>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-user-times text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Absent Today</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['absent_today'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Leaves -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Leave Requests</h3>
            </div>
            <div class="p-6">
                @if($recentLeaves->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentLeaves as $leave)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $leave->employee->full_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $leave->leave_type }} - {{ $leave->days_requested }} days</p>
                                    <p class="text-xs text-gray-500">{{ $leave->start_date->format('M d, Y') }} to {{ $leave->end_date->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($leave->status === 'Pending') bg-yellow-100 text-yellow-800
                                        @elseif($leave->status === 'Approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $leave->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('leaves.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all leaves →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent leave requests</p>
                @endif
            </div>
        </div>

        <!-- Recent Employees -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recently Added Employees</h3>
            </div>
            <div class="p-6">
                @if($recentEmployees->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentEmployees as $employee)
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    @if($employee->profile_photo)
                                        <img class="h-10 w-10 rounded-full" src="{{ asset('storage/employees/photos/' . $employee->profile_photo) }}" alt="{{ $employee->full_name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-gray-600"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <p class="font-medium text-gray-900">{{ $employee->full_name }}</p>
                                    <p class="text-sm text-gray-600">{{ $employee->position }} - {{ $employee->department }}</p>
                                    <p class="text-xs text-gray-500">Hired: {{ $employee->hire_date->format('M d, Y') }}</p>
                                </div>
                                <div class="ml-auto">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        @if($employee->employment_status === 'Active') bg-green-100 text-green-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $employee->employment_status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('employees.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all employees →
                        </a>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent employees</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('employees.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                    <i class="fa-solid fa-user-plus text-blue-600 text-xl mr-3"></i>
                    <span class="font-medium text-blue-900">Add Employee</span>
                </a>
                
                <a href="{{ route('leaves.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                    <i class="fa-solid fa-calendar-plus text-green-600 text-xl mr-3"></i>
                    <span class="font-medium text-green-900">Create Leave</span>
                </a>
                
                <a href="{{ route('loans.create') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fa-solid fa-hand-holding-usd text-purple-600 text-xl mr-3"></i>
                    <span class="font-medium text-purple-900">New Loan</span>
                </a>
                
                <a href="{{ route('attendance.index') }}" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                    <i class="fa-solid fa-clock text-indigo-600 text-xl mr-3"></i>
                    <span class="font-medium text-indigo-900">Attendance</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection