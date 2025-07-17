@extends('layouts.app')

@section('title', $employee->name . ' - Employee Profile')

@section('header', 'Employee Profile')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .tab-button {
        transition: all 0.2s ease-in-out;
    }
    
    .tab-button.active {
        background-color: #3b82f6;
        color: white;
    }
    
    .info-card {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .info-card h3 {
        color: #374151;
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
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
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fa-solid fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    <!-- Profile Header -->
    <div class="profile-header rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="h-20 w-20 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-6">
                    @if($employee->profile_photo)
                        <img src="{{ asset('storage/employees/photos/' . $employee->profile_photo) }}" 
                             alt="{{ $employee->name }}" 
                             class="h-20 w-20 rounded-full object-cover">
                    @else
                        <i class="fa-solid fa-user text-white text-3xl"></i>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold">{{ $employee->name }}</h1>
                    <p class="text-blue-100 text-lg">{{ $employee->designation ?? 'Employee' }}</p>
                    <p class="text-blue-100">{{ $employee->department ?? 'Department' }} • {{ $employee->emp_no }}</p>
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
                <span class="status-badge {{ $statusClass }} text-white">
                    {{ $employee->employment_status }}
                </span>
                <div class="mt-2">
                    <a href="{{ route('employees.edit', $employee) }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg mr-2">
                        <i class="fa-solid fa-edit mr-1"></i>
                        Edit
                    </a>
                    <a href="{{ route('employees.index') }}" 
                       class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-arrow-left mr-1"></i>
                        Back
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button class="tab-button active py-4 px-1 border-b-2 border-blue-500 font-medium text-sm" 
                        onclick="showTab('basic')">
                    <i class="fa-solid fa-user mr-2"></i>
                    Basic Information
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" 
                        onclick="showTab('contact')">
                    <i class="fa-solid fa-phone mr-2"></i>
                    Contact Information
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" 
                        onclick="showTab('employment')">
                    <i class="fa-solid fa-briefcase mr-2"></i>
                    Employment Information
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" 
                        onclick="showTab('additional')">
                    <i class="fa-solid fa-info-circle mr-2"></i>
                    Additional Information
                </button>
                <button class="tab-button py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700" 
                        onclick="showTab('documents')">
                    <i class="fa-solid fa-file-alt mr-2"></i>
                    Documents
                </button>
            </nav>
        </div>

        <div class="p-6">
            <!-- Basic Information Tab -->
            <div id="basic-tab" class="tab-content">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="info-card">
                        <h3><i class="fa-solid fa-user mr-2"></i>Personal Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Full Name:</span>
                                <span class="font-medium">{{ $employee->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Employee ID:</span>
                                <span class="font-medium">{{ $employee->emp_no }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Gender:</span>
                                <span class="font-medium">{{ $employee->gender ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date of Birth:</span>
                                <span class="font-medium">
                                    {{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Age:</span>
                                <span class="font-medium">
                                    {{ $employee->dob ? \Carbon\Carbon::parse($employee->dob)->age . ' years' : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nationality:</span>
                                <span class="font-medium">{{ $employee->nationality ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3><i class="fa-solid fa-map-marker-alt mr-2"></i>Address Information</h3>
                        <div class="space-y-3">
                            <div>
                                <span class="text-gray-600 block mb-1">Permanent Address:</span>
                                <span class="font-medium">{{ $employee->permanent_address ?? 'Not provided' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600 block mb-1">Present Address:</span>
                                <span class="font-medium">{{ $employee->persent_address ?? 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employment Information Tab -->
            <div id="employment-tab" class="tab-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="info-card">
                        <h3><i class="fa-solid fa-briefcase mr-2"></i>Employment Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Designation:</span>
                                <span class="font-medium">{{ $employee->designation ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Department:</span>
                                <span class="font-medium">{{ $employee->department ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date of Joining:</span>
                                <span class="font-medium">
                                    {{ $employee->date_of_join ? \Carbon\Carbon::parse($employee->date_of_join)->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Service Years:</span>
                                <span class="font-medium">{{ $employee->service_years ?? 0 }} years</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Employment Status:</span>
                                <span class="font-medium">{{ $employee->employment_status ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Work Site:</span>
                                <span class="font-medium">{{ $employee->work_site ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Company:</span>
                                <span class="font-medium">{{ $employee->company ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Level:</span>
                                <span class="font-medium">{{ ucfirst($employee->level ?? 'N/A') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3><i class="fa-solid fa-dollar-sign mr-2"></i>Salary Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Basic Salary:</span>
                                <span class="font-medium">
                                    {{ $employee->basic_salary ? number_format($employee->basic_salary, 2) : 'N/A' }}
                                    {{ $employee->salary_currency ?? 'USD' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Salary Currency:</span>
                                <span class="font-medium">{{ $employee->salary_currency ?? 'N/A' }}</span>
                            </div>
                            @if($employee->termination_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Termination Date:</span>
                                <span class="font-medium text-red-600">
                                    {{ \Carbon\Carbon::parse($employee->termination_date)->format('M d, Y') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Tab -->
            <div id="contact-tab" class="tab-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="info-card">
                        <h3><i class="fa-solid fa-phone mr-2"></i>Contact Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Contact Number:</span>
                                <span class="font-medium">{{ $employee->contact_number ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Foreign Contact:</span>
                                <span class="font-medium">{{ $employee->contact_number_foregn ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Personal Email:</span>
                                <span class="font-medium">{{ $employee->emp_email ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Company Email:</span>
                                <span class="font-medium">{{ $employee->company_email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3><i class="fa-solid fa-exclamation-triangle mr-2"></i>Emergency Contact</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Emergency Contact Name:</span>
                                <span class="font-medium">{{ $employee->emergency_contact_name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Emergency Contact Number:</span>
                                <span class="font-medium">{{ $employee->emergency_contact_number ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Information Tab -->
            <div id="additional-tab" class="tab-content hidden">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="info-card">
                        <h3><i class="fa-solid fa-passport mr-2"></i>Passport & Work Permit</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Passport/NIC Number:</span>
                                <span class="font-medium">{{ $employee->passport_nic_no ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Passport Expiry Date:</span>
                                <span class="font-medium">
                                    {{ $employee->passport_expire_date ? \Carbon\Carbon::parse($employee->passport_expire_date)->format('M d, Y') : 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Work Permit Number:</span>
                                <span class="font-medium">{{ $employee->wp_no ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h3><i class="fa-solid fa-building mr-2"></i>Company Information</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Insurance Provider:</span>
                                <span class="font-medium">{{ $employee->insurance_provider ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Recruiting Agency:</span>
                                <span class="font-medium">{{ $employee->recruiting_agency ?? 'N/A' }}</span>
                            </div>
                            @if($employee->xpat_designation)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Expat Designation:</span>
                                <span class="font-medium">{{ $employee->xpat_designation }}</span>
                            </div>
                            @endif
                            @if($employee->xpat_join_date)
                            <div class="flex justify-between">
                                <span class="text-gray-600">Expat Join Date:</span>
                                <span class="font-medium">
                                    {{ \Carbon\Carbon::parse($employee->xpat_join_date)->format('M d, Y') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div id="documents-tab" class="tab-content hidden">
                <div class="bg-white rounded-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Document Management</h3>
                        <button onclick="openDocumentModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                            <i class="fa-solid fa-plus mr-2"></i>
                            Upload Document
                        </button>
                    </div>
                    
                    <!-- Document Statistics -->
                    <div class="mb-8">
                        <div class="info-card">
                            <h3><i class="fa-solid fa-chart-bar mr-2"></i>Document Statistics</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $employee->documents ? $employee->documents->count() : 0 }}</div>
                                    <div class="text-sm text-gray-600">Total Documents</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $employee->documents ? $employee->documents->where('is_verified', true)->count() : 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600">Verified</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-yellow-600">
                                        {{ $employee->documents ? $employee->documents->where('is_verified', false)->count() : 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600">Pending</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-red-600">
                                        {{ $employee->documents ? $employee->documents->filter(function($doc) { return $doc->isExpired(); })->count() : 0 }}
                                    </div>
                                    <div class="text-sm text-gray-600">Expired</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents List -->
                    <div class="mb-6">
                        <h4 class="text-md font-semibold text-gray-900 mb-4">Uploaded Documents</h4>
                        @if($employee->documents && $employee->documents->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($employee->documents as $document)
                                    <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center">
                                                @php
                                                    $iconClass = 'fa-solid fa-file';
                                                    $iconColor = 'text-gray-500';
                                                    switch(strtolower($document->file_type)) {
                                                        case 'application/pdf':
                                                            $iconClass = 'fa-solid fa-file-pdf';
                                                            $iconColor = 'text-red-500';
                                                            break;
                                                        case 'image/jpeg':
                                                        case 'image/jpg':
                                                        case 'image/png':
                                                            $iconClass = 'fa-solid fa-file-image';
                                                            $iconColor = 'text-blue-500';
                                                            break;
                                                        case 'application/msword':
                                                        case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
                                                            $iconClass = 'fa-solid fa-file-word';
                                                            $iconColor = 'text-blue-600';
                                                            break;
                                                        default:
                                                            $iconClass = 'fa-solid fa-file';
                                                            $iconColor = 'text-gray-500';
                                                    }
                                                @endphp
                                                <i class="{{ $iconClass }} {{ $iconColor }} mr-2"></i>
                                                <span class="font-medium text-sm">{{ $document->title }}</span>
                                            </div>
                                            <span class="bg-{{ $document->is_verified ? 'green' : 'yellow' }}-100 text-{{ $document->is_verified ? 'green' : 'yellow' }}-800 text-xs px-2 py-1 rounded-full">
                                                {{ $document->is_verified ? 'Verified' : 'Pending' }}
                                            </span>
                                        </div>
                                        <p class="text-gray-600 text-sm mb-2">{{ $document->description ?: 'No description' }}</p>
                                        <div class="flex justify-between text-xs text-gray-500 mb-2">
                                            <span>Type: {{ $document->document_type }}</span>
                                            <span>{{ $document->file_size_human }}</span>
                                        </div>
                                        <div class="flex justify-between text-xs text-gray-500 mb-3">
                                            <span>Uploaded: {{ $document->created_at->format('M d, Y') }}</span>
                                            @if($document->expiry_date)
                                                <span class="{{ $document->isExpired() ? 'text-red-600' : '' }}">
                                                    Expires: {{ $document->expiry_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span>No expiry</span>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            <a href="{{ route('employees.documents.download', [$employee, $document]) }}" 
                                               class="text-blue-600 hover:text-blue-800 text-sm">
                                                <i class="fa-solid fa-download mr-1"></i>
                                                Download
                                            </a>
                                            @if(!$document->is_verified)
                                                <form action="{{ route('employees.documents.verify', [$employee, $document]) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                                                        <i class="fa-solid fa-check mr-1"></i>
                                                        Verify
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('employees.documents.delete', [$employee, $document]) }}" method="POST" class="inline" 
                                                  onsubmit="return confirm('Are you sure you want to delete this document?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                    <i class="fa-solid fa-trash mr-1"></i>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fa-solid fa-file-circle-xmark text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">No documents uploaded yet.</p>
                                <button onclick="openDocumentModal()" class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                                    <i class="fa-solid fa-plus mr-2"></i>
                                    Upload First Document
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>

<!-- Document Upload Modal -->
<div id="documentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Upload Document</h3>
                <button onclick="closeDocumentModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <form action="{{ route('employees.documents.upload', $employee) }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-md">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-exclamation-triangle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There were errors with your submission:</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="space-y-4">
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type *</label>
                        <select name="document_type" id="document_type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Document Type</option>
                            <option value="Warning Letter">Warning Letter</option>
                            <option value="Leave Form">Leave Form</option>
                            <option value="Passport">Passport</option>
                            <option value="Work Permit Card">Work Permit Card</option>
                            <option value="Departure Sheet">Departure Sheet</option>
                            <option value="Employee Photo">Employee Photo</option>
                            <option value="Promotion Letter">Promotion Letter</option>
                            <option value="Contract">Contract</option>
                            <option value="Medical Certificate">Medical Certificate</option>
                            <option value="Educational Certificate">Educational Certificate</option>
                            <option value="Experience Certificate">Experience Certificate</option>
                            <option value="Salary Slip">Salary Slip</option>
                            <option value="Bank Details">Bank Details</option>
                            <option value="Insurance Document">Insurance Document</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Document Title *</label>
                        <input type="text" name="title" id="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Enter document title">
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Enter document description (optional)"></textarea>
                    </div>
                    
                    <div>
                        <label for="document_file" class="block text-sm font-medium text-gray-700 mb-1">Document File *</label>
                        <input type="file" name="document_file" id="document_file" required 
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Accepted formats: PDF, JPG, JPEG, PNG, DOC, DOCX (Max: 10MB)</p>
                    </div>
                    
                    <div>
                        <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                        <input type="date" name="expiry_date" id="expiry_date"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Leave empty if document doesn't expire</p>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="is_required" id="is_required" value="1"
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="is_required" class="ml-2 block text-sm text-gray-900">
                            This is a required document
                        </label>
                    </div>
                    
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="2"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="Additional notes (optional)"></textarea>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeDocumentModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Upload Document
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function showTab(tabName) {
    // Hide all tab contents
    const tabContents = document.querySelectorAll('.tab-content');
    tabContents.forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tab buttons
    const tabButtons = document.querySelectorAll('.tab-button');
    tabButtons.forEach(button => {
        button.classList.remove('active', 'border-blue-500', 'text-blue-600');
        button.classList.add('text-gray-500');
    });

    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.remove('hidden');

    // Add active class to clicked tab button
    event.target.classList.add('active', 'border-blue-500', 'text-blue-600');
    event.target.classList.remove('text-gray-500');
}

function openDocumentModal() {
    document.getElementById('documentModal').classList.remove('hidden');
}

function closeDocumentModal() {
    document.getElementById('documentModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('documentModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDocumentModal();
    }
});
</script>
@endsection 