<!-- Sidebar toggle button (mobile) -->
<div class="md:hidden flex items-center justify-between p-4 bg-white dark:bg-neutral-900 border-b border-gray-200 dark:border-neutral-700">
  <button type="button" class="hs-collapse-toggle inline-flex items-center justify-center w-10 h-10 text-gray-800 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" data-hs-collapse="#sidebar" aria-controls="sidebar" aria-label="Toggle navigation">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <path d="M4 6h16M4 12h16M4 18h16" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
  </button>
  <span class="text-lg font-bold text-blue-600 dark:text-white">HRoS</span>
</div>

<!-- Sidebar -->
<aside id="sidebar" class="hs-collapse transition-all duration-300 transform md:translate-x-0 hidden md:block md:w-64 fixed top-0 left-0 z-40 h-screen bg-white dark:bg-neutral-900 border-r border-gray-200 dark:border-neutral-800 shadow-lg">
  <div class="h-full flex flex-col">
    <!-- Logo -->
    <div class="p-4 border-b border-gray-200 dark:border-neutral-800 flex items-center gap-2 text-blue-600 dark:text-white font-semibold text-xl">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
        <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6M4 21h16M4 21v-4a4 4 0 014-4h8a4 4 0 014 4v4" stroke-linecap="round" stroke-linejoin="round" />
      </svg>
      <span>HR Dashboard</span>
    </div>

    <!-- Nav Links -->
    <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-2">
      <a href="dashboard.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-800">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M3 12l2-2m0 0l7-7 7 7M13 5v6h6M4 21h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Dashboard
      </a>
      <a href="employees.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-800">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M5.121 17.804A9 9 0 1112 21a9 9 0 01-6.879-3.196z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
          <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Employees
      </a>
      <a href="vacancies.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-800">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M12 6V4m0 0a8 8 0 110 16v-2m0-14a8 8 0 100 16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Vacancies
      </a>
      <a href="reports.php" class="flex items-center px-4 py-2 text-sm font-medium rounded-lg text-gray-800 hover:bg-gray-100 dark:text-white dark:hover:bg-neutral-800">
        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M9 17v-6h13v6M9 21v-4h13v4M4 6h16M4 10h16M4 14h16" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Reports
      </a>
    </nav>

    <!-- Footer / Logout -->
    <div class="p-4 border-t border-gray-200 dark:border-neutral-800">
      <a href="logout.php" class="flex items-center text-sm text-red-600 dark:text-red-400 hover:underline">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
        </svg>
        Logout
      </a>
    </div>
  </div>
</aside>
