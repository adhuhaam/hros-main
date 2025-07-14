<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'HR Management System')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons@latest/iconfont/tabler-icons.min.css">
    
    <!-- Custom Styles -->
    <style>
        .sidebar {
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Sidebar -->
    <div id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
        <div class="flex items-center justify-between p-4 border-b">
            <div class="flex items-center">
                <img src="/assets/images/logos/dark-logo.svg" alt="Logo" class="h-8">
                <span class="ml-2 text-lg font-semibold text-gray-800">HRoS</span>
            </div>
            <button id="sidebar-close" class="lg:hidden text-gray-500 hover:text-gray-700">
                <i class="ti ti-x text-xl"></i>
            </button>
        </div>
        
        <nav class="mt-4">
            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                Navigation
            </div>
            
            <ul class="space-y-1">
                @if(auth()->user()->role === 'Admin')
                    <li>
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-house mr-3"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('employees.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-users mr-3"></i>
                            <span>Employees</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('leaves.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-calendar mr-3"></i>
                            <span>Leave Management</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-clock mr-3"></i>
                            <span>Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('loans.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fa-solid fa-money-bill mr-3"></i>
                            <span>Loans</span>
                        </a>
                    </li>
                @endif
                
                <!-- Profile -->
                <li class="border-t mt-4 pt-4">
                    <a href="{{ route('profile') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                        <i class="fa-solid fa-user mr-3"></i>
                        <span>Profile</span>
                    </a>
                </li>
                
                <!-- Logout -->
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full flex items-center px-4 py-2 text-gray-700 hover:bg-red-50 hover:text-red-600">
                            <i class="fa-solid fa-sign-out-alt mr-3"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center">
                    <button id="sidebar-toggle" class="lg:hidden text-gray-500 hover:text-gray-700 mr-3">
                        <i class="ti ti-menu-2 text-xl"></i>
                    </button>
                    <h1 class="text-xl font-semibold text-gray-800">@yield('header', 'Dashboard')</h1>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <span class="text-sm text-gray-600">{{ auth()->user()->name }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="p-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden"></div>

    <!-- Scripts -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebar-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.add('show');
            document.getElementById('sidebar-overlay').classList.remove('hidden');
        });

        document.getElementById('sidebar-close').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        });

        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            document.getElementById('sidebar').classList.remove('show');
            document.getElementById('sidebar-overlay').classList.add('hidden');
        });
    </script>

    @stack('scripts')
</body>
</html>