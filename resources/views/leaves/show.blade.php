@extends('layouts.app')

@section('title', 'Leave Request Details - HR Management System')

@section('header', 'Leave Request Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-gray-900">Leave Request #{{ $leave->id }}</h1>
                <p class="text-sm text-gray-600">Submitted on {{ $leave->created_at->format('M d, Y \a\t g:i A') }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('leaves.edit', $leave) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                    <i class="fa-solid fa-edit mr-1"></i>
                    Edit
                </a>
                <a href="{{ route('leaves.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                    Back to List
                </a>
            </div>
        </div>

        <!-- Status Badge -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
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
            <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full {{ $statusClasses }}">
                {{ $leave->status }}
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
                                    <div class="text-lg font-medium text-gray-900">{{ $leave->employee->name ?? 'Unknown Employee' }}</div>
                                    <div class="text-sm text-gray-500">Employee ID: {{ $leave->employee->emp_no ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $leave->employee->designation ?? 'Employee' }} - {{ $leave->employee->department ?? 'Department' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leave Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Leave Details</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Leave Type:</span>
                                <span class="text-sm text-gray-900">{{ $leave->leave_type }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Start Date:</span>
                                <span class="text-sm text-gray-900">{{ $leave->start_date->format('M d, Y (l)') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">End Date:</span>
                                <span class="text-sm text-gray-900">{{ $leave->end_date->format('M d, Y (l)') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm font-medium text-gray-700">Days Requested:</span>
                                <span class="text-sm text-gray-900">{{ $leave->days_requested }} days</span>
                            </div>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Reason for Leave</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-sm text-gray-900">{{ $leave->reason }}</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Additional Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Information</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            @if($leave->destination)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Destination:</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $leave->destination }}</p>
                                </div>
                            @endif
                            @if($leave->emergency_contact)
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Emergency Contact:</span>
                                    <p class="text-sm text-gray-900 mt-1">{{ $leave->emergency_contact }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Approval Information -->
                    @if($leave->status !== 'Pending')
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Approval Information</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-700">Status:</span>
                                    <span class="text-sm text-gray-900">{{ $leave->status }}</span>
                                </div>
                                @if($leave->approver)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Approved/Rejected By:</span>
                                        <span class="text-sm text-gray-900">{{ $leave->approver->name }}</span>
                                    </div>
                                @endif
                                @if($leave->approved_at)
                                    <div class="flex justify-between">
                                        <span class="text-sm font-medium text-gray-700">Date:</span>
                                        <span class="text-sm text-gray-900">{{ $leave->approved_at->format('M d, Y \a\t g:i A') }}</span>
                                    </div>
                                @endif
                                @if($leave->rejection_reason)
                                    <div>
                                        <span class="text-sm font-medium text-gray-700">Rejection Reason:</span>
                                        <p class="text-sm text-gray-900 mt-1">{{ $leave->rejection_reason }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Approval Actions (for pending leaves) -->
                    @if($leave->status === 'Pending')
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                                <form method="POST" action="{{ route('leaves.approve', $leave) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md"
                                            onclick="return confirm('Approve this leave request?')">
                                        <i class="fa-solid fa-check mr-2"></i>
                                        Approve Leave Request
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('leaves.reject', $leave) }}">
                                    @csrf
                                    <div class="space-y-2">
                                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700">Rejection Reason (Required)</label>
                                        <textarea name="rejection_reason" id="rejection_reason" rows="3" required
                                                  class="w-full border-gray-300 rounded-md shadow-sm focus:ring-red-500 focus:border-red-500"
                                                  placeholder="Please provide a reason for rejection"></textarea>
                                    </div>
                                    <button type="submit" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md mt-3"
                                            onclick="return confirm('Reject this leave request?')">
                                        <i class="fa-solid fa-times mr-2"></i>
                                        Reject Leave Request
                                    </button>
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