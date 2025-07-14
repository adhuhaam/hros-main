@extends('layouts.app')

@section('title', 'Create Attendance Record')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Create Attendance Record</h1>
            <a href="{{ route('attendance.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to List
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="{{ route('attendance.store') }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Employee Selection -->
                    <div class="md:col-span-2">
                        <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Employee <span class="text-red-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('employee_id') border-red-500 @enderror">
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->emp_no }}" {{ old('employee_id') == $employee->emp_no ? 'selected' : '' }}>
                                    {{ $employee->name }} ({{ $employee->emp_no }}) - {{ $employee->department }}
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date -->
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                            Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date" id="date" value="{{ old('date', today()->format('Y-m-d')) }}" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date') border-red-500 @enderror">
                        @error('date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                            <option value="">Select Status</option>
                            <option value="Present" {{ old('status') == 'Present' ? 'selected' : '' }}>Present</option>
                            <option value="Absent" {{ old('status') == 'Absent' ? 'selected' : '' }}>Absent</option>
                            <option value="Late" {{ old('status') == 'Late' ? 'selected' : '' }}>Late</option>
                            <option value="Early Departure" {{ old('status') == 'Early Departure' ? 'selected' : '' }}>Early Departure</option>
                            <option value="Half Day" {{ old('status') == 'Half Day' ? 'selected' : '' }}>Half Day</option>
                            <option value="Leave" {{ old('status') == 'Leave' ? 'selected' : '' }}>Leave</option>
                            <option value="Holiday" {{ old('status') == 'Holiday' ? 'selected' : '' }}>Holiday</option>
                            <option value="Weekend" {{ old('status') == 'Weekend' ? 'selected' : '' }}>Weekend</option>
                            <option value="Remote" {{ old('status') == 'Remote' ? 'selected' : '' }}>Remote</option>
                            <option value="Business Trip" {{ old('status') == 'Business Trip' ? 'selected' : '' }}>Business Trip</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Check In Time -->
                    <div>
                        <label for="check_in" class="block text-sm font-medium text-gray-700 mb-2">
                            Check In Time
                        </label>
                        <input type="time" name="check_in" id="check_in" value="{{ old('check_in') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('check_in') border-red-500 @enderror">
                        @error('check_in')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Check Out Time -->
                    <div>
                        <label for="check_out" class="block text-sm font-medium text-gray-700 mb-2">
                            Check Out Time
                        </label>
                        <input type="time" name="check_out" id="check_out" value="{{ old('check_out') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('check_out') border-red-500 @enderror">
                        @error('check_out')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('notes') border-red-500 @enderror"
                                  placeholder="Enter any additional notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Quick Time Buttons -->
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Quick Time Selection</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                        <button type="button" onclick="setTime('check_in', '09:00')" class="px-3 py-2 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                            9:00 AM
                        </button>
                        <button type="button" onclick="setTime('check_in', '08:30')" class="px-3 py-2 text-sm bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                            8:30 AM
                        </button>
                        <button type="button" onclick="setTime('check_out', '17:00')" class="px-3 py-2 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200">
                            5:00 PM
                        </button>
                        <button type="button" onclick="setTime('check_out', '18:00')" class="px-3 py-2 text-sm bg-green-100 text-green-700 rounded hover:bg-green-200">
                            6:00 PM
                        </button>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                    <a href="{{ route('attendance.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md flex items-center">
                        <i class="fas fa-save mr-2"></i>
                        Create Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function setTime(fieldId, time) {
    document.getElementById(fieldId).value = time;
}

// Auto-fill current time for check-in if not set
document.addEventListener('DOMContentLoaded', function() {
    const checkInField = document.getElementById('check_in');
    if (!checkInField.value) {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        checkInField.value = `${hours}:${minutes}`;
    }
});

// Validate check-out time is after check-in time
document.getElementById('check_out').addEventListener('change', function() {
    const checkIn = document.getElementById('check_in').value;
    const checkOut = this.value;
    
    if (checkIn && checkOut && checkOut <= checkIn) {
        alert('Check-out time must be after check-in time.');
        this.value = '';
    }
});
</script>
@endpush 