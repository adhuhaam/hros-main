<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRoS - HR Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 to-white min-h-screen flex flex-col items-center justify-center">
    <div class="max-w-lg w-full mx-auto p-8 bg-white rounded-xl shadow-lg flex flex-col items-center">
        <img src="/assets/images/logos/dark-logo.svg" alt="HRoS Logo" class="h-16 mb-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome to HRoS</h1>
        <p class="text-gray-600 mb-6 text-center">A modern HR management system for employee records, leave, and attendance. Secure, efficient, and easy to use.</p>
        <a href="{{ route('login') }}" class="w-full inline-block text-center bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg shadow transition mb-2">Login</a>
        @if (Route::has('register'))
            <a href="{{ route('register') }}" class="w-full inline-block text-center bg-gray-100 hover:bg-gray-200 text-blue-700 font-semibold py-3 px-6 rounded-lg shadow">Register</a>
        @endif
    </div>
    <footer class="mt-8 text-gray-400 text-xs text-center">
        &copy; {{ date('Y') }} HRoS. All rights reserved.
    </footer>
</body>
</html>
