@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6 text-gray-900">HR Reports</h1>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <p class="text-sm text-gray-600">Total Employees</p>
            <p class="text-2xl font-bold text-gray-900">{{ $employeeCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <p class="text-sm text-gray-600">Total Leaves</p>
            <p class="text-2xl font-bold text-gray-900">{{ $leaveCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <p class="text-sm text-gray-600">Attendance Records</p>
            <p class="text-2xl font-bold text-gray-900">{{ $attendanceCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <p class="text-sm text-gray-600">Warnings</p>
            <p class="text-2xl font-bold text-gray-900">{{ $warningCount }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div>
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Leaves</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentLeaves as $leave)
                        <tr>
                            <td class="px-4 py-2">{{ $leave->employee->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $leave->leave_type ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $leave->status }}</td>
                            <td class="px-4 py-2">{{ $leave->start_date }} - {{ $leave->end_date }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div>
            <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Warnings</h2>
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentWarnings as $warning)
                        <tr>
                            <td class="px-4 py-2">{{ $warning->employee->name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $warning->warning_type ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $warning->warning_date ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $warning->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div>
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Attendance</h2>
        <div class="bg-white rounded-lg shadow overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentAttendance as $attendance)
                    <tr>
                        <td class="px-4 py-2">{{ $attendance->employee->name ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $attendance->date }}</td>
                        <td class="px-4 py-2">{{ $attendance->status }}</td>
                        <td class="px-4 py-2">{{ $attendance->check_in_time ?? '-' }}</td>
                        <td class="px-4 py-2">{{ $attendance->check_out_time ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 