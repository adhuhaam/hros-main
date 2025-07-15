@extends('layouts.app')

@section('title', 'Finance Officer Dashboard - HR Management System')

@section('header', 'Finance Officer Dashboard')

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
    
    .finance-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Finance Officer Dashboard</h1>
                <p class="text-emerald-100 mt-1">Manage loans, salaries, and financial records</p>
            </div>
            <div class="text-right">
                <p class="text-emerald-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-emerald-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Loans -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Loans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_loans']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Currently active</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-hand-holding-usd text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Loans -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Loans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_loans']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting approval</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Salary -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Salary</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_salary']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Monthly payroll</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-dollar-sign text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Employees -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Employees</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">On payroll</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Active Loans -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Active Loans</h3>
                <a href="{{ route('loans.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($activeLoans->count() > 0)
                    <div class="space-y-4">
                        @foreach($activeLoans as $loan)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-hand-holding-usd text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $loan->employee->name ?? 'Unknown Employee' }}</p>
                                        <p class="text-sm text-gray-600">${{ number_format($loan->amount) }} - {{ $loan->loan_type ?? 'Loan' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Remaining: ${{ number_format($loan->remaining_amount ?? $loan->amount) }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-hand-holding-usd text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No active loans</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pending Loans -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Pending Loans</h3>
                <a href="{{ route('loans.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($pendingLoans->count() > 0)
                    <div class="space-y-4">
                        @foreach($pendingLoans as $loan)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <i class="fa-solid fa-clock text-yellow-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $loan->employee->name ?? 'Unknown Employee' }}</p>
                                        <p class="text-sm text-gray-600">${{ number_format($loan->amount) }} - {{ $loan->loan_type ?? 'Loan' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Requested: {{ \Carbon\Carbon::parse($loan->created_at)->format('M d, Y') }}
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
                        <i class="fa-solid fa-clock text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No pending loans</p>
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
                <a href="{{ route('loans.index') }}" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                    <i class="fa-solid fa-hand-holding-usd text-blue-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-blue-900">Manage Loans</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-dollar-sign text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Process Payroll</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-file-invoice-dollar text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Financial Reports</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-chart-line text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Analytics</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 