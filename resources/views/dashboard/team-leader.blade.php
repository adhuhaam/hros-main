@extends('layouts.app')

@section('title', 'Team Leader Dashboard - HR Management System')

@section('header', 'Team Leader Dashboard')

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
    
    .team-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-cyan-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Team Leader Dashboard</h1>
                <p class="text-cyan-100 mt-1">Lead your team to success</p>
            </div>
            <div class="text-right">
                <p class="text-cyan-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-cyan-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Team Members -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team Members</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['team_members']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Under your leadership</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Present Today -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Present Today</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['present_today']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Currently at work</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Absent Today -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Absent Today</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['absent_today']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Not present</p>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-times-circle text-2xl"></i>
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
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Team Attendance -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Team Attendance</h3>
                <a href="{{ route('attendance.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($teamAttendance->count() > 0)
                    <div class="space-y-4">
                        @foreach($teamAttendance as $attendance)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $attendance->employee->name ?? 'Unknown Employee' }}</p>
                                        <p class="text-sm text-gray-600">{{ $attendance->status ?? 'Status' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ \Carbon\Carbon::parse($attendance->date)->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
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
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-calendar-check text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No attendance data available</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Team Leaves -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Team Leave Requests</h3>
                <a href="{{ route('leaves.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($teamLeaves->count() > 0)
                    <div class="space-y-4">
                        @foreach($teamLeaves as $leave)
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
                                    @php
                                        $statusClasses = '';
                                        if($leave->status === 'Approved') {
                                            $statusClasses = 'bg-green-100 text-green-800';
                                        } elseif($leave->status === 'Rejected') {
                                            $statusClasses = 'bg-red-100 text-red-800';
                                        } else {
                                            $statusClasses = 'bg-yellow-100 text-yellow-800';
                                        }
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $leave->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No leave requests found</p>
                    </div>
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
                <a href="{{ route('attendance.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                    <i class="fa-solid fa-calendar-check text-blue-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-blue-900">Attendance</span>
                </a>
                
                <a href="{{ route('leaves.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-calendar-times text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Leave Requests</span>
                </a>
                
                <a href="{{ route('employees.index') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-users text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Team Members</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-chart-bar text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Team Reports</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 