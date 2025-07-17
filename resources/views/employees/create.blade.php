@extends('layouts.app')

@section('title', 'Add Employee - HR Management System')

@section('header', 'Add Employee')

@push('styles')
<style>
    .form-section {
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-section h3 {
        color: #374151;
        font-size: 1.125rem;
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e5e7eb;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Add New Employee</h1>
            <p class="text-gray-600 mt-1">Enter employee information to create a new record</p>
        </div>
        <a href="{{ route('employees.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Back to List
        </a>
    </div>

    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Basic Information -->
        <div class="form-section">
            <h3><i class="fa-solid fa-user mr-2"></i>Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="emp_no" class="block text-sm font-medium text-gray-700 mb-1">Employee ID *</label>
                    <input type="text" name="emp_no" id="emp_no" value="{{ old('emp_no') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('emp_no') border-red-500 @enderror"
                           placeholder="Enter employee ID">
                    @error('emp_no')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Enter full name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender *</label>
                    <select name="gender" id="gender" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('gender') border-red-500 @enderror">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="dob" class="block text-sm font-medium text-gray-700 mb-1">Date of Birth *</label>
                    <input type="date" name="dob" id="dob" value="{{ old('dob') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('dob') border-red-500 @enderror">
                    @error('dob')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nationality" class="block text-sm font-medium text-gray-700 mb-1">Nationality *</label>
                    <select name="nationality" id="nationality" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('nationality') border-red-500 @enderror">
                        <option value="">Select Nationality</option>
                        @foreach($nationalities as $nationality)
                            <option value="{{ $nationality }}" {{ old('nationality') == $nationality ? 'selected' : '' }}>
                                {{ $nationality }}
                            </option>
                        @endforeach
                    </select>
                    @error('nationality')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-1">Profile Photo</label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('profile_photo') border-red-500 @enderror">
                    @error('profile_photo')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="form-section">
            <h3><i class="fa-solid fa-phone mr-2"></i>Contact Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_number" class="block text-sm font-medium text-gray-700 mb-1">Contact Number *</label>
                    <input type="text" name="contact_number" id="contact_number" value="{{ old('contact_number') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_number') border-red-500 @enderror"
                           placeholder="Enter contact number">
                    @error('contact_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="contact_number_foregn" class="block text-sm font-medium text-gray-700 mb-1">Foreign Contact</label>
                    <input type="text" name="contact_number_foregn" id="contact_number_foregn" value="{{ old('contact_number_foregn') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('contact_number_foregn') border-red-500 @enderror"
                           placeholder="Enter foreign contact number">
                    @error('contact_number_foregn')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="emp_email" class="block text-sm font-medium text-gray-700 mb-1">Personal Email</label>
                    <input type="email" name="emp_email" id="emp_email" value="{{ old('emp_email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('emp_email') border-red-500 @enderror"
                           placeholder="Enter personal email">
                    @error('emp_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_email" class="block text-sm font-medium text-gray-700 mb-1">Company Email</label>
                    <input type="email" name="company_email" id="company_email" value="{{ old('company_email') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('company_email') border-red-500 @enderror"
                           placeholder="Enter company email">
                    @error('company_email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="permanent_address" class="block text-sm font-medium text-gray-700 mb-1">Permanent Address</label>
                    <textarea name="permanent_address" id="permanent_address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('permanent_address') border-red-500 @enderror"
                              placeholder="Enter permanent address">{{ old('permanent_address') }}</textarea>
                    @error('permanent_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="persent_address" class="block text-sm font-medium text-gray-700 mb-1">Present Address</label>
                    <textarea name="persent_address" id="persent_address" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('persent_address') border-red-500 @enderror"
                              placeholder="Enter present address">{{ old('persent_address') }}</textarea>
                    @error('persent_address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Employment Information -->
        <div class="form-section">
            <h3><i class="fa-solid fa-briefcase mr-2"></i>Employment Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="designation" class="block text-sm font-medium text-gray-700 mb-1">Designation *</label>
                    <select name="designation" id="designation" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('designation') border-red-500 @enderror">
                        <option value="">Select Designation</option>
                        @foreach($positions as $position)
                            <option value="{{ $position }}" {{ old('designation') == $position ? 'selected' : '' }}>
                                {{ $position }}
                            </option>
                        @endforeach
                    </select>
                    @error('designation')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department *</label>
                    <select name="department" id="department" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('department') border-red-500 @enderror">
                        <option value="">Select Department</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>
                    @error('department')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_of_join" class="block text-sm font-medium text-gray-700 mb-1">Date of Joining *</label>
                    <input type="date" name="date_of_join" id="date_of_join" value="{{ old('date_of_join') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('date_of_join') border-red-500 @enderror">
                    @error('date_of_join')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="employment_status" class="block text-sm font-medium text-gray-700 mb-1">Employment Status *</label>
                    <select name="employment_status" id="employment_status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('employment_status') border-red-500 @enderror">
                        <option value="">Select Status</option>
                        <option value="Active" {{ old('employment_status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('employment_status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="Resigned" {{ old('employment_status') == 'Resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="Terminated" {{ old('employment_status') == 'Terminated' ? 'selected' : '' }}>Terminated</option>
                        <option value="Retired" {{ old('employment_status') == 'Retired' ? 'selected' : '' }}>Retired</option>
                    </select>
                    @error('employment_status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="basic_salary" class="block text-sm font-medium text-gray-700 mb-1">Basic Salary *</label>
                    <input type="number" name="basic_salary" id="basic_salary" value="{{ old('basic_salary') }}" step="0.01" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('basic_salary') border-red-500 @enderror"
                           placeholder="Enter basic salary">
                    @error('basic_salary')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="salary_currency" class="block text-sm font-medium text-gray-700 mb-1">Salary Currency</label>
                    <select name="salary_currency" id="salary_currency"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('salary_currency') border-red-500 @enderror">
                        <option value="USD" {{ old('salary_currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('salary_currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ old('salary_currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                        <option value="MVR" {{ old('salary_currency') == 'MVR' ? 'selected' : '' }}>MVR</option>
                    </select>
                    @error('salary_currency')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="form-section">
            <h3><i class="fa-solid fa-info-circle mr-2"></i>Additional Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="passport_nic_no" class="block text-sm font-medium text-gray-700 mb-1">Passport/NIC Number</label>
                    <input type="text" name="passport_nic_no" id="passport_nic_no" value="{{ old('passport_nic_no') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('passport_nic_no') border-red-500 @enderror"
                           placeholder="Enter passport or NIC number">
                    @error('passport_nic_no')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="passport_expire_date" class="block text-sm font-medium text-gray-700 mb-1">Passport Expiry Date</label>
                    <input type="date" name="passport_expire_date" id="passport_expire_date" value="{{ old('passport_expire_date') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('passport_expire_date') border-red-500 @enderror">
                    @error('passport_expire_date')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="wp_no" class="block text-sm font-medium text-gray-700 mb-1">Work Permit Number</label>
                    <input type="text" name="wp_no" id="wp_no" value="{{ old('wp_no') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('wp_no') border-red-500 @enderror"
                           placeholder="Enter work permit number">
                    @error('wp_no')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="work_site" class="block text-sm font-medium text-gray-700 mb-1">Work Site</label>
                    <input type="text" name="work_site" id="work_site" value="{{ old('work_site') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('work_site') border-red-500 @enderror"
                           placeholder="Enter work site">
                    @error('work_site')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="emergency_contact_name" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Name</label>
                    <input type="text" name="emergency_contact_name" id="emergency_contact_name" value="{{ old('emergency_contact_name') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('emergency_contact_name') border-red-500 @enderror"
                           placeholder="Enter emergency contact name">
                    @error('emergency_contact_name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="emergency_contact_number" class="block text-sm font-medium text-gray-700 mb-1">Emergency Contact Number</label>
                    <input type="text" name="emergency_contact_number" id="emergency_contact_number" value="{{ old('emergency_contact_number') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('emergency_contact_number') border-red-500 @enderror"
                           placeholder="Enter emergency contact number">
                    @error('emergency_contact_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('employees.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                <i class="fa-solid fa-save mr-2"></i>
                Create Employee
            </button>
        </div>
    </form>
</div>
@endsection 