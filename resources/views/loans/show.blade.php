@extends('layouts.app')

@section('title', 'Loan Request Details - HR Management System')

@section('header', 'Loan Request Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Loan Request #{{ $loan->id }}</h1>
                <p class="text-sm text-gray-600">Submitted on {{ $loan->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('loans.edit', $loan) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    <i class="fa-solid fa-edit mr-1"></i>
                    Edit
                </a>
                <a href="{{ route('loans.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
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
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClasses }}">
                {{ $loan->status }}
            </span>
        </div>

        <!-- Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Employee Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Employee Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-lg font-medium text-gray-900">{{ $loan->employee->name ?? 'Unknown Employee' }}</div>
                                    <div class="text-sm text-gray-500">Employee ID: {{ $loan->employee->emp_no ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $loan->employee->designation ?? 'Employee' }} - {{ $loan->employee->department ?? 'Department' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Loan Type:</span>
                                <span class="text-sm text-gray-900">{{ $loan->loan_type }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Principal Amount:</span>
                                <span class="text-sm text-gray-900">${{ number_format($loan->amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Interest Rate:</span>
                                <span class="text-sm text-gray-900">{{ $loan->interest_rate }}%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Amount:</span>
                                <span class="text-sm text-gray-900">${{ number_format($loan->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Installment Amount:</span>
                                <span class="text-sm text-gray-900">${{ number_format($loan->installment_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Total Installments:</span>
                                <span class="text-sm text-gray-900">{{ $loan->total_installments }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Paid Installments:</span>
                                <span class="text-sm text-gray-900">{{ $loan->paid_installments }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Remaining Amount:</span>
                                <span class="text-sm text-gray-900">${{ number_format($loan->remaining_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Progress</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>{{ $loan->paid_installments }} of {{ $loan->total_installments }} installments paid</span>
                                <span>{{ $loan->progress_percentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $loan->progress_percentage }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Purpose -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Purpose</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900">{{ $loan->purpose }}</p>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Start Date:</span>
                                <span class="text-sm text-gray-900">{{ $loan->start_date->format('M d, Y') }}</span>
                            </div>
                            @if($loan->end_date)
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700">End Date:</span>
                                    <span class="text-sm text-gray-900">{{ $loan->end_date->format('M d, Y') }}</span>
                                </div>
                            @endif
                            @if($loan->guarantor_name)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Guarantor:</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $loan->guarantor_name }}</p>
                                </div>
                            @endif
                            @if($loan->guarantor_phone)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Guarantor Phone:</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $loan->guarantor_phone }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval Information -->
                    @if($loan->status !== 'Pending')
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700">Status:</span>
                                    <span class="text-sm text-gray-900">{{ $loan->status }}</span>
                                </div>
                                @if($loan->approver)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Approved/Rejected By:</span>
                                        <span class="text-sm text-gray-900">{{ $loan->approver->name }}</span>
                                    </div>
                                @endif
                                @if($loan->approved_at)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Date:</span>
                                        <span class="text-sm text-gray-900">{{ $loan->approved_at->format('M d, Y \a\t g:i A') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Approval Actions (for pending loans) -->
                    @if($loan->status === 'Pending')
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                <form method="POST" action="{{ route('loans.approve', $loan) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
                                            onclick="return confirm('Approve this loan request?')">
                                        <i class="fa-solid fa-check mr-2"></i>
                                        Approve Loan Request
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('loans.reject', $loan) }}">
                                    @csrf
                                    <div class="space-y-2">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason (Required)</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                                  placeholder="Please provide a reason for rejection"></textarea>
                                    </div>
                                    <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md mt-3"
                                            onclick="return confirm('Reject this loan request?')">
                                        <i class="fa-solid fa-times mr-2"></i>
                                        Reject Loan Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <!-- Payment Section (for active loans) -->
                    @if($loan->status === 'Active')
                        <div id="payment">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Make Payment</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <form method="POST" action="{{ route('loans.pay-installment', $loan) }}">
                                    @csrf
                                    <div class="space-y-4">
                                        <div>
                                            <label for="payment_amount" class="block text-sm font-medium text-gray-700 mb-1">Payment Amount ($)</label>
                                            <input type="number" name="payment_amount" id="payment_amount" step="0.01" min="0" required
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                   placeholder="{{ number_format($loan->installment_amount, 2) }}">
                                        </div>
                                        <div>
                                            <label for="payment_date" class="block text-sm font-medium text-gray-700 mb-1">Payment Date</label>
                                            <input type="date" name="payment_date" id="payment_date" required
                                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                   value="{{ date('Y-m-d') }}">
                                        </div>
                                        <div>
                                            <label for="payment_notes" class="block text-sm font-medium text-gray-700 mb-1">Notes (Optional)</label>
                                            <textarea name="payment_notes" id="payment_notes" rows="2"
                                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                                      placeholder="Any additional notes about this payment"></textarea>
                                        </div>
                                        <button type="submit" 
                                                class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md">
                                            <i class="fa-solid fa-credit-card mr-2"></i>
                                            Record Payment
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 