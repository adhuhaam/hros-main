<!-- sidebar.php -->
<div class="h-screen w-64 text-white flex flex-col justify-between fixed bg-white dark:bg-light-800 rounded-lg px-6 py-8 ring shadow-xl ring-gray-900/5">
  <div>
    <div class="p-4 text-xl font-semibold tracking-wide border-b border-gray-700">
      <img src="../assets/images/logos/dark-logo.svg" width="180" alt="Company Logo">
    </div>
    <nav class="mt-4">
      <a href="index.php" class="block px-6 py-3 hover:bg-gray-700 transition-all <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'bg-gray-800' : '' ?>">
        Recipients
      </a>
      <a href="compose_mail.php" class="block px-6 py-3 hover:bg-gray-700 transition-all <?= basename($_SERVER['PHP_SELF']) == 'compose_mail.php' ? 'bg-gray-800' : '' ?>">
       Compose Mail
      </a>
      <a href="mail_logs.php" class="block px-6 py-3 hover:bg-gray-700 transition-all <?= basename($_SERVER['PHP_SELF']) == 'mail_logs.php' ? 'bg-gray-800' : '' ?>">
        Mail Logs
      </a>
      <a href="add_employee.php" class="block px-6 py-3 hover:bg-gray-700 transition-all <?= basename($_SERVER['PHP_SELF']) == 'add_employee.php' ? 'bg-gray-800' : '' ?>">
        Add Employee
      </a>
    </nav>
  </div>

  <div class="p-4 border-t border-gray-700 text-sm text-gray-400">
    HROS System &copy; <?= date('Y') ?>
  </div>
</div>
