@extends('layouts.app')

@section('title', 'System Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-6">System Settings</h2>
        
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Company Information -->
                <div>
                    <h3 class="text-lg font-medium mb-4">Company Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Company Name</label>
                            <input type="text" name="company_name" value="{{ $settings['company_name'] }}" required class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Company Email</label>
                            <input type="email" name="company_email" value="{{ $settings['company_email'] }}" required class="w-full border rounded px-3 py-2">
                        </div>
                    </div>
                </div>

                <!-- System Configuration -->
                <div>
                    <h3 class="text-lg font-medium mb-4">System Configuration</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Timezone</label>
                            <select name="timezone" required class="w-full border rounded px-3 py-2">
                                <option value="UTC" {{ $settings['timezone'] == 'UTC' ? 'selected' : '' }}>UTC</option>
                                <option value="America/New_York" {{ $settings['timezone'] == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Currency</label>
                            <select name="currency" required class="w-full border rounded px-3 py-2">
                                <option value="USD" {{ $settings['currency'] == 'USD' ? 'selected' : '' }}>USD</option>
                                <option value="EUR" {{ $settings['currency'] == 'EUR' ? 'selected' : '' }}>EUR</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection 