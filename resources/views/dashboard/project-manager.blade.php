@extends('layouts.app')

@section('title', 'Project Manager Dashboard - HR Management System')

@section('header', 'Project Manager Dashboard')

@push('styles')
<style>
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .stat-card {
        transition: transform 0.2s ease-in-out;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
    }
    
    .project-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold">Project Manager Dashboard</h1>
                <p class="text-orange-100 mt-1">Manage projects and team performance</p>
            </div>
            <div class="text-right">
                <p class="text-orange-100">{{ now()->format('l, F j, Y') }}</p>
                <p class="text-orange-100">{{ now()->format('g:i A') }}</p>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Team Members -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Team Members</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['team_members']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Active team</p>
                </div>
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Projects -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Projects</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['active_projects']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">In progress</p>
                </div>
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fa-solid fa-project-diagram text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Projects -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Completed Projects</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['completed_projects']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Successfully delivered</p>
                </div>
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fa-solid fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Tasks -->
        <div class="stat-card bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Tasks</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($stats['pending_tasks']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting completion</p>
                </div>
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fa-solid fa-tasks text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Team Members -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Team Members</h3>
                <a href="{{ route('employees.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                    View all →
                </a>
            </div>
            <div class="p-6">
                @if($teamMembers->count() > 0)
                    <div class="space-y-4">
                        @foreach($teamMembers as $member)
                            <div class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                        <i class="fa-solid fa-user text-gray-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="font-medium text-gray-900">{{ $member->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $member->designation ?? 'Team Member' }} - {{ $member->department ?? 'Department' }}</p>
                                    <p class="text-xs text-gray-500">
                                        Joined: {{ \Carbon\Carbon::parse($member->date_of_join)->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="ml-4">
                                    @php
                                        $statusClasses = '';
                                        if($member->employment_status === 'Active') {
                                            $statusClasses = 'bg-green-100 text-green-800';
                                        } else {
                                            $statusClasses = 'bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $member->employment_status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-users text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No team members found</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Project Statistics -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Project Statistics</h3>
            </div>
            <div class="p-6">
                @if($projectStats->count() > 0)
                    <div class="space-y-4">
                        @foreach($projectStats as $project)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-project-diagram text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-medium text-gray-900">{{ $project->name ?? 'Project' }}</p>
                                        <p class="text-sm text-gray-600">{{ $project->status ?? 'Status' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Progress: {{ $project->progress ?? 0 }}%
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @php
                                        $statusClasses = '';
                                        if($project->status === 'Active') {
                                            $statusClasses = 'bg-green-100 text-green-800';
                                        } elseif($project->status === 'Completed') {
                                            $statusClasses = 'bg-blue-100 text-blue-800';
                                        } else {
                                            $statusClasses = 'bg-gray-100 text-gray-800';
                                        }
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusClasses }}">
                                        {{ $project->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fa-solid fa-project-diagram text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-500">No project data available</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors group">
                    <i class="fa-solid fa-project-diagram text-blue-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-blue-900">Manage Projects</span>
                </a>
                
                <a href="{{ route('employees.index') }}" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors group">
                    <i class="fa-solid fa-users text-green-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-green-900">Team Members</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors group">
                    <i class="fa-solid fa-tasks text-purple-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-purple-900">Task Management</span>
                </a>
                
                <a href="#" class="flex items-center p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors group">
                    <i class="fa-solid fa-chart-bar text-indigo-600 text-xl mr-3 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-indigo-900">Project Reports</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 