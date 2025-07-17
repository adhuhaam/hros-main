@extends('layouts.app')

@section('title', 'Create Loan Request - HR Management System')

@section('header', 'Create Loan Request')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('loans.store') }}">
            @csrf
            
            <div class="space-y-6">
                <!-- Employee Selection -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
                    <select name="employee_id" id="employee_id" required 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->emp_no }}" {{ old('employee_id') == $employee->emp_no ? 'selected' : '' }}>
                                {{ $employee->name }} ({{ $employee->emp_no }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Type -->
                <div>
                    <label for="loan_type" class="block text-sm font-medium text-gray-700 mb-1">Loan Type</label>
                    <select name="loan_type" id="loan_type" required 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Loan Type</option>
                        @foreach($loanTypes as $type)
                            <option value="{{ $type }}" {{ old('loan_type') == $type ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('loan_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Loan Amount and Terms -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Loan Amount ($)</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" step="0.01" min="0" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00">
                        @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="interest_rate" class="block text-sm font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                        <input type="number" name="interest_rate" id="interest_rate" value="{{ old('interest_rate') }}" step="0.01" min="0" max="100" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="0.00">
                        @error('interest_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="total_installments" class="block text-sm font-medium text-gray-700 mb-1">Total Installments</label>
                        <input type="number" name="total_installments" id="total_installments" value="{{ old('total_installments') }}" min="1" required
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="12">
                        @error('total_installments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Start Date -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" required
                           class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    @error('start_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Purpose -->
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-1">Purpose</label>
                    <textarea name="purpose" id="purpose" rows="4" required
                              class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Please provide a detailed purpose for the loan request">{{ old('purpose') }}</textarea>
                    @error('purpose')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Guarantor Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="guarantor_name" class="block text-sm font-medium text-gray-700 mb-1">Guarantor Name (Optional)</label>
                        <input type="text" name="guarantor_name" id="guarantor_name" value="{{ old('guarantor_name') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Guarantor's full name">
                        @error('guarantor_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="guarantor_phone" class="block text-sm font-medium text-gray-700 mb-1">Guarantor Phone (Optional)</label>
                        <input type="text" name="guarantor_phone" id="guarantor_phone" value="{{ old('guarantor_phone') }}"
                               class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="Guarantor's phone number">
                        @error('guarantor_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('loans.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Create Loan Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 