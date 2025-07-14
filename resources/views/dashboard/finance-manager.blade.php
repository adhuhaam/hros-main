@extends('layouts.app')

@section('title', 'Finance Manager Dashboard - HR Management System')

@section('header', 'Finance Manager Dashboard')

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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Finance Manager Dashboard</h1>
                <p class="text-emerald-100 mt-1">Manage financial operations and employee benefits</p>
            </div>
            <div class="text-right">
                <p class="text-emerald-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-emerald-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Financial Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Employees -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Employees</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_employees']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">On payroll</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Salary -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Salary</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($salaryStats['total_salary'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Monthly payroll</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-money-bill-wave text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Loans</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_loans']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total: {{ number_format($stats['total_loan_amount'], 2) }}</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
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
    </div>

    <!-- Salary Statistics -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Salary Statistics</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="p-4 bg-blue-50 rounded-lg">
                        <i class="fa-solid fa-money-bill-wave text-blue-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Total Salary</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($salaryStats['total_salary'], 2) }}</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="p-4 bg-green-50 rounded-lg">
                        <i class="fa-solid fa-chart-line text-green-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Average Salary</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($salaryStats['avg_salary'], 2) }}</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="p-4 bg-purple-50 rounded-lg">
                        <i class="fa-solid fa-arrow-up text-purple-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Highest Salary</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($salaryStats['max_salary'], 2) }}</p>
                    </div>
                </div>
                <div class="text-center">
                    <div class="p-4 bg-yellow-50 rounded-lg">
                        <i class="fa-solid fa-arrow-down text-yellow-600 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-gray-600">Lowest Salary</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($salaryStats['min_salary'], 2) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Loan Status Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Loan Status Distribution</h3>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="loanChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Salary Distribution -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Salary Distribution</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Below 10,000</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">25%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">10,000 - 20,000</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: 45%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">45%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">20,000 - 30,000</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: 20%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">20%</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Above 30,000</span>
                        <div class="flex items-center">
                            <div class="w-32 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-yellow-600 h-2 rounded-full" style="width: 10%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">10%</span>
                        </div>
                    </div>
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
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fa-solid fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $loan->employee->name ?? 'Unknown Employee' }}</p>
                                        <p class="text-sm text-gray-600">{{ number_format($loan->amount, 2) }} {{ $loan->currency ?? 'MVR' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Applied: {{ \Carbon\Carbon::parse($loan->created_at)->format('M d, Y') }}
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

        <!-- Recent Financial Activities -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Financial Activities</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-green-100 text-green-600 mr-3">
                                <i class="fa-solid fa-plus text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Salary Payment</p>
                                <p class="text-sm text-gray-600">Monthly payroll processed</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">{{ now()->subDays(2)->format('M d') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-blue-100 text-blue-600 mr-3">
                                <i class="fa-solid fa-hand-holding-usd text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Loan Approved</p>
                                <p class="text-sm text-gray-600">New loan application approved</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">{{ now()->subDays(5)->format('M d') }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-purple-100 text-purple-600 mr-3">
                                <i class="fa-solid fa-chart-line text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Budget Review</p>
                                <p class="text-sm text-gray-600">Monthly budget analysis completed</p>
                            </div>
                        </div>
                        <span class="text-sm text-gray-500">{{ now()->subDays(7)->format('M d') }}</span>
                    </div>
                </div>
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
                    <i class="fa-solid fa-money-bill-wave text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Payroll</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-chart-bar text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Financial Reports</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-cog text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Settings</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Loan Chart
const loanCtx = document.getElementById('loanChart').getContext('2d');
const loanChart = new Chart(loanCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Pending', 'Completed'],
        datasets: [{
            data: [@json($loanChart['active']), @json($loanChart['pending']), @json($loanChart['completed'])],
            backgroundColor: [
                'rgb(34, 197, 94)',
                'rgb(234, 179, 8)',
                'rgb(59, 130, 246)'
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