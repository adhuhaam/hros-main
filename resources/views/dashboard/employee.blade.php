@extends('layouts.app')

@section('title', 'Employee Dashboard - HR Management System')

@section('header', 'My Dashboard')

@push('styles')
<style>
    .stat-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .progress-ring {
        transform: rotate(-90deg);
    }
    
    .progress-ring-circle {
        transition: stroke-dasharray 0.35s;
        transform-origin: 50% 50%;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="h-16 w-16 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-4">
                    <i class="fa-solid fa-user text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Welcome back, {{ $employee->name }}!</h1>
                    <p class="text-indigo-100 mt-1">{{ $employee->designation ?? 'Employee' }} - {{ $employee->department ?? 'Department' }}</p>
                    <p class="text-indigo-100 text-sm">Employee ID: {{ $employee->emp_no }}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-indigo-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-indigo-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Personal Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Leaves -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_leaves']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Approved: {{ number_format($stats['approved_leaves']) }}</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-calendar text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Leaves -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_leaves']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Loans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_loans']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Current loans</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-money-bill text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Attendance This Month -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Present This Month</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['attendance_this_month']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Days attended</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-calendar-check text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Personal Information -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Basic Details</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Full Name:</span>
                            <span class="font-medium">{{ $employee->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Employee ID:</span>
                            <span class="font-medium">{{ $employee->emp_no }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Designation:</span>
                            <span class="font-medium">{{ $employee->designation ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Department:</span>
                            <span class="font-medium">{{ $employee->department ?? 'Not specified' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Date of Joining:</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($employee->date_of_join)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Contact Information</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Contact Number:</span>
                            <span class="font-medium">{{ $employee->contact_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email:</span>
                            <span class="font-medium">{{ $employee->emp_email ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Emergency Contact:</span>
                            <span class="font-medium">{{ $employee->emergency_contact_number ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Employment Status:</span>
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
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- My Leave Requests -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">My Leave Requests</h3>
                <a href="{{ route('leaves.create') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Request Leave →
                </a>
            </div>
            <div class="p-6">
                @if($myLeaves->count() > 0)
                    <div class="space-y-4">
                        @foreach($myLeaves as $leave)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $leave->leave_type ?? 'Leave' }}</p>
                                    <p class="text-sm text-gray-600">{{ $leave->days_requested }} days</p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }} to 
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusClasses = '';
                                        if($leave->status === 'Pending') {
                                            $statusClasses = 'bg-yellow-100 text-yellow-800';
                                        } elseif($leave->status === 'Approved') {
                                            $statusClasses = 'bg-green-100 text-green-800';
                                        } else {
                                            $statusClasses = 'bg-red-100 text-red-800';
                                        }
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $leave->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('leaves.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all my leaves →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No leave requests yet</p>
                        <a href="{{ route('leaves.create') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            Request Leave
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- My Loans -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">My Loans</h3>
                <a href="{{ route('loans.create') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    Apply for Loan →
                </a>
            </div>
            <div class="p-6">
                @if($myLoans->count() > 0)
                    <div class="space-y-4">
                        @foreach($myLoans as $loan)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div>
                                    <p class="font-medium text-gray-900">Loan #{{ $loan->id }}</p>
                                    <p class="text-sm text-gray-600">{{ number_format($loan->amount, 2) }} {{ $loan->currency ?? 'MVR' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Applied: {{ \Carbon\Carbon::parse($loan->created_at)->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusClasses = '';
                                        if($loan->status === 'Pending') {
                                            $statusClasses = 'bg-yellow-100 text-yellow-800';
                                        } elseif($loan->status === 'Active') {
                                            $statusClasses = 'bg-green-100 text-green-800';
                                        } elseif($loan->status === 'Completed') {
                                            $statusClasses = 'bg-blue-100 text-blue-800';
                                        } else {
                                            $statusClasses = 'bg-red-100 text-red-800';
                                        }
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $loan->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ route('loans.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all my loans →
                        </a>
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-money-bill text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No loans yet</p>
                        <a href="{{ route('loans.create') }}" class="mt-2 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                            Apply for Loan
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Attendance</h3>
        </div>
        <div class="p-6">
            @if($myAttendance->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check In</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check Out</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($myAttendance as $attendance)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClasses = '';
                                            if($attendance->status === 'Present') {
                                                $statusClasses = 'bg-green-100 text-green-800';
                                            } elseif($attendance->status === 'Absent') {
                                                $statusClasses = 'bg-red-100 text-red-800';
                                            } else {
                                                $statusClasses = 'bg-yellow-100 text-yellow-800';
                                            }
                                        @endphp
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                            {{ $attendance->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attendance->hours_worked ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fa-solid fa-clock text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500">No attendance records found</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('leaves.create') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                    <i class="fa-solid fa-calendar-plus text-blue-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-blue-900">Request Leave</span>
                </a>
                
                <a href="{{ route('loans.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-hand-holding-usd text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Apply for Loan</span>
                </a>
                
                <a href="{{ route('attendance.check-in-page') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-clock text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Check In/Out</span>
                </a>
                
                <a href="{{ route('profile') }}" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-user-edit text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Update Profile</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 