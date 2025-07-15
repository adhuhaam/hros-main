@extends('layouts.app')

@section('title', 'Admin Dashboard - HR Management System')

@section('header', 'Admin Dashboard')

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
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Welcome back, {{ Auth::user()->staff_name }}!</h1>
                <p class="text-blue-100 mt-1">Here's what's happening in your organization today</p>
            </div>
            <div class="text-right">
                <p class="text-blue-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-blue-100">{{ now()->format('g:i A') }}</p>
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
                    <p class="text-xs text-gray-500 mt-1">Requires attention</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Present Today</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['today_attendance']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Absent: {{ number_format($stats['absent_today']) }}</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-calendar-check text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Loans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_loans']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total warnings: {{ number_format($stats['total_warnings']) }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-money-bill text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Attendance Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Attendance Trend (Last 7 Days)</h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Leave Status Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Leave Status Distribution</h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="leaveChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Leave Requests -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Recent Leave Requests</h3>
                <a href="{{ route('leaves.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($recentLeaves->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentLeaves as $leave)
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
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-calendar-times text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No recent leave requests</p>
                    </div>
                @endif
            </div>
        </div>

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
    </div>

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
                
                <a href="{{ route('leaves.create') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-calendar-plus text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Create Leave</span>
                </a>
                
                <a href="{{ route('loans.create') }}" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-hand-holding-usd text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">New Loan</span>
                </a>
                
                <a href="{{ route('attendance.index') }}" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-clock text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Attendance</span>
                </a>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    @if($stats['expiring_documents'] > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-center">
            <i class="fa-solid fa-exclamation-triangle text-yellow-600 mr-3"></i>
            <div>
                <h4 class="text-sm font-medium text-yellow-800">Document Expiry Alert</h4>
                <p class="text-sm text-yellow-700 mt-1">
                    {{ $stats['expiring_documents'] }} employee documents are expiring within the next 3 months.
                    <a href="#" class="font-medium underline">Review documents</a>
                </p>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Attendance Chart
const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
const attendanceChart = new Chart(attendanceCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($attendanceChart, 'date')),
        datasets: [{
            label: 'Present',
            data: @json(array_column($attendanceChart, 'present')),
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Absent',
            data: @json(array_column($attendanceChart, 'absent')),
            borderColor: 'rgb(239, 68, 68)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Leave Chart
const leaveCtx = document.getElementById('leaveChart').getContext('2d');
const leaveChart = new Chart(leaveCtx, {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Approved', 'Rejected'],
        datasets: [{
            data: [@json($leaveChart['pending']), @json($leaveChart['approved']), @json($leaveChart['rejected'])],
            backgroundColor: [
                'rgb(234, 179, 8)',
                'rgb(34, 197, 94)',
                'rgb(239, 68, 68)'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});
</script>
@endpush