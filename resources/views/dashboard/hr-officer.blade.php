@extends('layouts.app')

@section('title', 'HR Officer Dashboard - HR Management System')

@section('header', 'HR Officer Dashboard')

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
    
    .leave-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">HR Officer Dashboard</h1>
                <p class="text-purple-100 mt-1">Manage leave requests and employee records</p>
            </div>
            <div class="text-right">
                <p class="text-purple-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-purple-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pending Leaves -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_leaves']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Requires review</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Approved Leaves -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Approved Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['approved_leaves']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">This period</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Rejected Leaves -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Rejected Leaves</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['rejected_leaves']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">This period</p>
                </div>
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fa-solid fa-times-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Employees</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Currently employed</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiring Documents Alert -->
    @if($expiringDocuments->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Expiring Documents</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($expiringDocuments->take(5) as $document)
                    <div class="flex items-center justify-between p-4 bg-red-50 rounded-lg border border-red-200">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                    <i class="fa-solid fa-file-alt text-red-600"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="font-medium text-gray-900">{{ $document->employee->name ?? 'Unknown Employee' }}</p>
                                <p class="text-sm text-gray-600">{{ $document->document_type ?? 'Document' }}</p>
                                <p class="text-xs text-red-600">
                                    Expires: {{ \Carbon\Carbon::parse($document->expiry_date)->format('M d, Y') }}
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
            @if($expiringDocuments->count() > 5)
                <div class="mt-4 text-center">
                    <a href="#" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        View all {{ $expiringDocuments->count() }} expiring documents →
                    </a>
                </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
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

        <!-- Recent Employees -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Recent Employees</h3>
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
                                        Joined: {{ \Carbon\Carbon::parse($employee->date_of_join)->format('M d, Y') }}
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
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('leaves.index') }}" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors group">
                    <i class="fa-solid fa-calendar-check text-yellow-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-yellow-900">Review Leaves</span>
                </a>
                
                <a href="{{ route('employees.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                    <i class="fa-solid fa-users text-blue-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-blue-900">Employee Records</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-file-alt text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Documents</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-chart-bar text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Reports</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 