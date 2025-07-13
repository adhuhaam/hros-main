<?php

// Mock user roles and permissions (use your database logic instead)
$roles_permissions = [
    
    'HR Manager' => ['warning'],
    'hod' => ['warning'],
    'director' => ['warning'],
    
];

// Get the current user's role from the session
$current_role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

// Define accessible links based on the role
$accessible_links = $current_role && isset($roles_permissions[$current_role]) ? $roles_permissions[$current_role] : [];

// Determine the role-specific dashboard link
$dashboard_link = '../user_dashboard.php'; // Default dashboard
switch ($current_role) {
    case 'Admin':
        $dashboard_link = '../admin_dashboard.php';
        break;
    case 'Information Officer':
        $dashboard_link = '../info_officer_dashboard.php';
        break;
    case 'Xpat Officer':
        $dashboard_link = '../xpat_officer_dashboard.php';
        break;
    case 'Leave Officer':
        $dashboard_link = '../leave_officer_dashboard.php';
        break;
    case 'HR Manager':
        $dashboard_link = '../hrm_dashboard.php';
        break;
    case 'Payroll Officer':
        $dashboard_link = '../payroll_dashboard.php';
        break;
    case 'Other Staff':
        $dashboard_link = '../other_dashboard.php';
        break;
    case 'Supervisor':
        $dashboard_link = '../supervisor_dashboard.php';
        break;
}
?>
<aside class="left-sidebar">
  <!-- Sidebar scroll-->
  <div class="scroll-sidebar" data-simplebar>
    <!-- Logo Section -->
    <div class="d-flex mb-4 align-items-center justify-content-between">
      <a href="<?php echo $dashboard_link; ?>" class="text-nowrap logo-img ms-0 ms-md-1">
        <img src="../../assets/images/logos/dark-logo.svg" width="180" alt="Company Logo">
      </a>
      <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
        <i class="ti ti-x fs-8"></i>
      </div>
    </div>
    <!-- Sidebar Navigation -->
    <nav class="sidebar-nav">
      <ul id="sidebarnav" class="mb-4 pb-2">
        <!-- Navigation Section -->
        <li class="nav-small-cap">
          <i class="ti ti-dots nav-small-cap-icon fs-5"></i>
          <span class="hide-menu">Navigation</span>
        </li>

        <!-- Dashboard -->
        <?php if (in_array('dashboard', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == $dashboard_link ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="<?php echo $dashboard_link; ?>" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-solid fa-house text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Dashboard</span>
          </a>
        </li>
        <?php endif; ?>

        

        

        <!-- Warning -->
        <?php if (in_array('warning', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../warning/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../warning/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-dollar-sign text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Warnings</span>
          </a>
        </li>
        <?php endif; ?>
        
        

        <!-- Logout -->
        <li class="sidebar-item">
          <a class="sidebar-link danger-hover-bg" href="../../logout.php" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-power-off text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Logout</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  <!-- End Sidebar scroll-->
</aside>
<script src="https://kit.fontawesome.com/aea6da8de7.js" crossorigin="anonymous"></script>
