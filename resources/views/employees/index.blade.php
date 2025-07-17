@extends('layouts.app')

@section('title', 'Employees - HR Management System')

@section('header', 'Employees')

@push('styles')
<style>
    .employee-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .employee-card:hover {
        transform: translateY(-2px);
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        border-radius: 9999px;
        font-weight: 600;
    }
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-inactive {
        background-color: #fef2f2;
        color: #991b1b;
    }
    
    .status-resigned {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .view-toggle-btn {
        transition: all 0.2s ease-in-out;
    }
    
    .view-toggle-btn.active {
        background-color: white;
        color: #374151;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    
    .view-toggle-btn:not(.active) {
        color: #6b7280;
    }
    
    .view-toggle-btn:hover:not(.active) {
        color: #374151;
    }
    
    .list-view {
        display: none;
    }
    
    .card-view {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header with Add Button -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Employees</h1>
            <p class="text-gray-600 mt-1">Manage your workforce</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- View Toggle Buttons -->
            <div class="flex bg-gray-200 rounded-lg p-1">
                <button id="card-view-btn" class="view-toggle-btn active px-3 py-1 rounded-md text-sm font-medium" onclick="toggleView('card')">
                    <i class="fa-solid fa-th-large mr-1"></i>
                    Cards
                </button>
                <button id="list-view-btn" class="view-toggle-btn px-3 py-1 rounded-md text-sm font-medium" onclick="toggleView('list')">
                    <i class="fa-solid fa-list mr-1"></i>
                    List
                </button>
            </div>
            <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Employee
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" action="{{ route('employees.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Name, Employee ID, Email, or Phone" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                    <select name="department" id="department" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" id="status" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Button -->
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                        <i class="fa-solid fa-filter mr-2"></i>
                        Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Employees List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $employees->total() }} Employee{{ $employees->total() != 1 ? 's' : '' }} Found
            </h3>
        </div>
        
        <div class="p-6">
            @if($employees->count() > 0)
                <!-- Card View -->
                <div id="card-view" class="card-view">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($employees as $employee)
                            <div class="employee-card bg-gray-50 rounded-lg p-6 hover:shadow-lg transition-all">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                            @if($employee->profile_photo)
                                                <img src="{{ asset('storage/employees/photos/' . $employee->profile_photo) }}" 
                                                     alt="{{ $employee->name }}" 
                                                     class="h-12 w-12 rounded-full object-cover">
                                            @else
                                                <i class="fa-solid fa-user text-gray-600 text-xl"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $employee->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ $employee->designation ?? 'Employee' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @php
                                            $statusClass = '';
                                            switch($employee->employment_status) {
                                                case 'Active':
                                                    $statusClass = 'status-active';
                                                    break;
                                                case 'Inactive':
                                                case 'Terminated':
                                                case 'Dead':
                                                case 'Missing':
                                                    $statusClass = 'status-inactive';
                                                    break;
                                                case 'Resigned':
                                                case 'Retired':
                                                    $statusClass = 'status-resigned';
                                                    break;
                                                default:
                                                    $statusClass = 'status-inactive';
                                            }
                                        @endphp
                                        <span class="status-badge {{ $statusClass }}">
                                            {{ $employee->employment_status }}
                                        </span>
                                    </div>
                                </div>

                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Employee ID:</span>
                                        <span class="font-medium">{{ $employee->emp_no }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Department:</span>
                                        <span class="font-medium">{{ $employee->department ?? 'N/A' }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Join Date:</span>
                                        <span class="font-medium">
                                            {{ $employee->date_of_join ? \Carbon\Carbon::parse($employee->date_of_join)->format('M d, Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Contact:</span>
                                        <span class="font-medium">{{ $employee->contact_number ?? 'N/A' }}</span>
                                    </div>
                                </div>

                                <div class="flex space-x-2">
                                    <a href="{{ route('employees.show', $employee) }}" 
                                       class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 px-3 rounded-md text-sm">
                                        <i class="fa-solid fa-eye mr-1"></i>
                                        View
                                    </a>
                                    <a href="{{ route('employees.edit', $employee) }}" 
                                       class="flex-1 bg-green-600 hover:bg-green-700 text-white text-center py-2 px-3 rounded-md text-sm">
                                        <i class="fa-solid fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="flex-1" 
                                          onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-md text-sm">
                                            <i class="fa-solid fa-trash mr-1"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- List View -->
                <div id="list-view" class="list-view">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Join Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($employees as $employee)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                                    @if($employee->profile_photo)
                                                        <img src="{{ asset('storage/employees/photos/' . $employee->profile_photo) }}" 
                                                             alt="{{ $employee->name }}" 
                                                             class="h-10 w-10 rounded-full object-cover">
                                                    @else
                                                        <i class="fa-solid fa-user text-gray-600"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $employee->name }}</div>
                                                    <div class="text-sm text-gray-500">{{ $employee->designation ?? 'Employee' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->emp_no }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->department ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee->contact_number ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $statusClass = '';
                                                switch($employee->employment_status) {
                                                    case 'Active':
                                                        $statusClass = 'status-active';
                                                        break;
                                                    case 'Inactive':
                                                    case 'Terminated':
                                                    case 'Dead':
                                                    case 'Missing':
                                                        $statusClass = 'status-inactive';
                                                        break;
                                                    case 'Resigned':
                                                    case 'Retired':
                                                        $statusClass = 'status-resigned';
                                                        break;
                                                    default:
                                                        $statusClass = 'status-inactive';
                                                }
                                            @endphp
                                            <span class="status-badge {{ $statusClass }}">
                                                {{ $employee->employment_status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $employee->date_of_join ? \Carbon\Carbon::parse($employee->date_of_join)->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('employees.show', $employee) }}" 
                                                   class="text-blue-600 hover:text-blue-900">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="{{ route('employees.edit', $employee) }}" 
                                                   class="text-green-600 hover:text-green-900">
                                                    <i class="fa-solid fa-edit"></i>
                                                </a>
                                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this employee?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $employees->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fa-solid fa-users text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No employees found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search criteria or add a new employee.</p>
                    <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Add First Employee
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function toggleView(viewType) {
    const cardView = document.getElementById('card-view');
    const listView = document.getElementById('list-view');
    const cardBtn = document.getElementById('card-view-btn');
    const listBtn = document.getElementById('list-view-btn');
    
    if (viewType === 'card') {
        cardView.classList.remove('list-view');
        cardView.classList.add('card-view');
        listView.classList.remove('card-view');
        listView.classList.add('list-view');
        
        cardBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        listView.classList.remove('list-view');
        listView.classList.add('card-view');
        cardView.classList.remove('card-view');
        cardView.classList.add('list-view');
        
        listBtn.classList.add('active');
        cardBtn.classList.remove('active');
    }
}
</script>
@endsection 