@extends('layouts.app')

@section('title', 'Loan Management - HR Management System')

@section('header', 'Loan Management')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Loan Requests</h1>
            <p class="text-gray-600">Manage employee loan requests and payments</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('loans.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fa-solid fa-plus mr-2"></i>
                New Loan Request
            </a>
            <a href="{{ route('loans.export') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fa-solid fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('loans.index') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Employee name or ID">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            
            <div>
                <label for="loan_type" class="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                <select name="loan_type" id="loan_type" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    <option value="Personal Loan" {{ request('loan_type') == 'Personal Loan' ? 'selected' : '' }}>Personal Loan</option>
                    <option value="Home Loan" {{ request('loan_type') == 'Home Loan' ? 'selected' : '' }}>Home Loan</option>
                    <option value="Vehicle Loan" {{ request('loan_type') == 'Vehicle Loan' ? 'selected' : '' }}>Vehicle Loan</option>
                    <option value="Education Loan" {{ request('loan_type') == 'Education Loan' ? 'selected' : '' }}>Education Loan</option>
                    <option value="Medical Loan" {{ request('loan_type') == 'Medical Loan' ? 'selected' : '' }}>Medical Loan</option>
                    <option value="Other" {{ request('loan_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                    <i class="fa-solid fa-search mr-1"></i>
                    Filter
                </button>
                <a href="{{ route('loans.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Loan Requests Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Loan Requests ({{ $loans->total() }})</h3>
        </div>
        
        @if($loans->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loan Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($loans as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <i class="fa-solid fa-user text-gray-600"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $loan->employee->name ?? 'Unknown' }}</div>
                                            <div class="text-sm text-gray-500">{{ $loan->employee->emp_no ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $loan->loan_type }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($loan->amount, 2) }}</div>
                                    <div class="text-xs text-gray-500">{{ $loan->interest_rate }}% interest</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $loan->paid_installments }}/{{ $loan->total_installments }} installments
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $loan->progress_percentage }}%"></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
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
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        @if($loan->status === 'Pending')
                                            <form method="POST" action="{{ route('loans.approve', $loan) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Approve this loan request?')">
                                                    <i class="fa-solid fa-check"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('loans.reject', $loan) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Reject this loan request?')">
                                                    <i class="fa-solid fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($loan->status === 'Active')
                                            <a href="{{ route('loans.show', $loan) }}#payment" class="text-purple-600 hover:text-purple-900">
                                                <i class="fa-solid fa-credit-card"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('loans.edit', $loan) }}" class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $loans->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-money-bill text-gray-400 text-4xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No loan requests found</h3>
                <p class="text-gray-500 mb-4">Get started by creating a new loan request.</p>
                <a href="{{ route('loans.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Create Loan Request
                </a>
            </div>
        @endif
    </div>
</div>
@endsection 