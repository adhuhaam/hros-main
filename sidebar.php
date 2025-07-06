<?php

// Mock user roles and permissions (use your database logic instead)
$roles_permissions = [
    'Admin' => ['ot', 'users', 'contracts','chat',  'new_mail','accommodation', 'tasks', 'missing', 'depaturesheet', 'hrreport', 'retire', 'terminate', 'email', 'ticket', 'resign', 'pp_inv', 'workpermit', 'vacancies', 'candidates', 'tickets', 'sick', 'leave','dashboard', 'employees', 'cards', 'holidays', 'notices', 'settings','bank','payroll', 'logout', 'attendance', 'loan', 'passport', 'transfer','visa', 'projects', 'medical', 'warning', 'document', 'allocation', 'hospital'],
    'Information Officer' => ['ot', 'chat', 'contracts', 'new_mail','sick', 'accommodation', 'tasks', 'missing', 'depaturesheet', 'hrreport', 'retire', 'terminate', 'notices', 'holidays', 'settings', 'email', 'resign', 'transfer', 'ticket', 'pp_inv', 'workpermit', 'hospital','tickets', 'sick','dashboard', 'employees', 'notices', 'cards','loan', 'passport', 'attendance', 'bank', 'logout', 'payroll', 'projects', 'medical', 'warning', 'document', 'allocation', 'visa', 'leave'],
    'Xpat Officer' => ['tasks', 'bank', 'leave',  'workpermit', 'dashboard', 'employees', 'loan', 'passport', 'visa', 'medical', 'document'],
    'Leave Officer' => ['tasks', 'tickets', 'dashboard', 'emp', 'holidays', 'leave', 'loan'. 'attendance', 'document', 'cards', 'notices', 'warning', 'logout', 'allocation'],
    'HR Manager' =>  ['sick', 'accommodation', 'tasks', 'missing', 'depaturesheet', 'hrreport', 'retire', 'terminate', 'notices', 'holidays', 'settings', 'email', 'resign', 'transfer', 'ticket', 'pp_inv', 'workpermit', 'hospital','tickets', 'sick','dashboard', 'employees', 'notices', 'cards','loan', 'passport', 'attendance', 'bank', 'logout', 'payroll', 'projects', 'medical', 'warning', 'document', 'allocation', 'visa', 'leave'],
    'Payroll Officer' => ['ot','leavex', 'depaturesheet', 'loan', 'tasks', 'hrreport', 'transfer', 'hospital', 'dashboard', 'empl', 'payroll', 'attendance', 'sick', 'hospital'],
    'Supervisor' => [ 'dashboard', 'emp', 'allocation'],
    'Other Staff' => ['accommodation', 'tasks', 'ticketx', 'leavex', 'dashboard', 'employees', 'transfer', 'medical', 'hospital', 'projects', 'bank', 'sick'],
    'welfare' => ['accommodation', 'tasks', 'allocation', 'emp', 'projects'], 
    'planing' => ['emp', 'projects', 'allocation'], 
    'Guest' => ['allocation'], 
    'reception' => [ 'accommodation', 'emp' ],
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
    case 'planing':
        $dashboard_link = '../other_dashboard.php';
        break;
    case 'reception':
        $dashboard_link = '../reception_dashboard.php';
        break;
}
?>





 <aside class="left-sidebar">
  <!-- Sidebar scroll-->
  <div class="scroll-sidebar" data-simplebar>
    <!-- Logo Section -->
    <div class="d-flex mb-4 align-items-center justify-content-between">
      <a href="<?php echo $dashboard_link; ?>" class="text-nowrap logo-img ms-0 ms-md-1">
        <img src="../assets/images/logos/dark-logo.svg" width="180" alt="Company Logo">
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
        
        
        
        <!-- tasks -->
        <?php if (in_array('tasks', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../tasks/' ? 'active' : ''; ?>">
          <a class="sidebar-link success-hover-bg" href="../tasks/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-success rounded-3">
             <i class="fa-brands fa-slack text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Task Board</span>
          </a>
        </li>
        <?php endif; ?>



        
         <!-- chat -->
        <?php if (in_array('chat', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../chat/' ? 'active' : ''; ?>">
          <a class="sidebar-link success-hover-bg" href="../chat/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-success rounded-3">
             <i class="fa-solid fa-paper-plane text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Chat</span>
          </a>
        </li>
        <?php endif; ?>
      
        
        

        <!-- Employees -->
        <?php if (in_array('employees', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../employee/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../employee/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
             <i class="fa-solid fa-user text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Employees</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        <!-- FOOD AND NEW EMPLOYEEE MAL -->
        <?php if (in_array('new_mail', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../new_mail/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../new_mail/" aria-expanded="false">
            <span class="aside-icon p-4 bg-light-danger rounded-3">
             <i class="fa-solid fa-bowl-food text-danger"></i>
            </span>
            <small class="hide-menu ms-3 ps-2">New Employee<br>
            Food Mail</small>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- Emp -->
        <?php if (in_array('emp', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../emp/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../emp/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
             <i class="fa-solid fa-user text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Employees</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        <!-- Empl -->
        <?php if (in_array('empl', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../empl/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../empl/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
             <i class="fa-solid fa-user text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Employees</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        
         <!-- termination, resignation, missing, retire -->
        <?php if (in_array('terminate', $accessible_links) || in_array('terminate', $accessible_links)): ?>
        <li class="sidebar-item">
            
          <a class="sidebar-link success-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#EmpotherDropdown">
            <span class="aside-icon p-2 bg-light-success rounded-3">
              <i class="ti ti-settings fs-7 text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Emp Other</span>
            <i class="ti ti-chevron-down ms-auto"></i>
          </a>
          
          <ul id="EmpotherDropdown" class="collapse list-unstyled" style="padding-left: 2rem;">
              
            <?php if (in_array('terminate', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../terminate/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../terminate/">
                <span class="hide-menu">Terminate</span>
              </a>
            </li>
            <?php endif; ?>
            
            
           <?php if (in_array('resign', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../resign/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../resign/">
                <span class="hide-menu">Resignation</span>
              </a>
            </li>
            <?php endif; ?>
            
            
            <?php if (in_array('retire', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../retire/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../retire/">
                <span class="hide-menu">Retirement</span>
              </a>
            </li>
            <?php endif; ?>
            
            <?php if (in_array('missing', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../missing/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../missing/">
                <span class="hide-menu">Missing</span>
              </a>
            </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>
        
        <!-- accommodation -->
        <?php if (in_array('accommodation', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../accommodation/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../accommodation/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
             <i class="w100 fa-duotone fa-solid fa-house text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Accommodation</span>
          </a>
        </li>
        <?php endif; ?> 
        
        
        
        
        
        
        <!-- Email -->
        <?php if (in_array('email', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../email/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../email/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
             <i class="w100 fa-duotone fa-solid fa-envelope text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Email</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        <!-- Contracts -->
        <?php if (in_array('contracts', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../contracts/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../contracts/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
             <i class="w100 fa-doc fa-solid fa-envelope text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Contracts</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
       
        
        
        <!-- Employee Document -->
        <?php if (in_array('document', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../documents/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../documents/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
             <i class="fa-solid fa-file text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">E-Docs</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        

        <!-- Payroll 
        <?php if (in_array('payroll', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../payroll/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../payroll/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-dollar-sign text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Payroll</span>
          </a>
        </li>
        <?php endif; ?>
        
        -->
        
        <?php if (in_array('ot', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../ot/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../ot/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-dollar-sign text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">OT</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        <!-- Payroll Dropdown -->
        <?php if (in_array('payroll', $accessible_links) || in_array('notices', $accessible_links)): ?>
        <li class="sidebar-item">
          <a class="sidebar-link danger-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#payDropdown">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-money-bill-trend-up text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Payroll</span>
            <i class="ti ti-chevron-down ms-auto"></i>
          </a>
          <ul id="payDropdown" class="collapse list-unstyled" style="padding-left: 2rem;">
            <?php if (in_array('payroll', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../payroll/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../payroll/">
                <span class="hide-menu">Payroll Sheet</span>
              </a>
            </li>
            <?php endif; ?>
            <?php if (in_array('leave', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../allowance/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../allowance/">
                <span class="hide-menu">Allowance</span>
              </a>
            </li>
            <?php endif; ?>
            
          </ul>
        </li>
        <?php endif; ?>
        
        
        
        
        
        
        
        
        
         <!-- Workpermit -->
        <?php if (in_array('workpermit', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../workpermit/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../workpermit/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-address-card text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Work Permit</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        <!-- warning -->
        <?php if (in_array('warning', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../warning/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../warning/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-solid fa-file text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Disciplinary Action</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        <!-- attendance -->
        <?php if (in_array('attendance', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../attendance/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../attendance/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-regular fa-clock text-primary-emphasis"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Attendance</span>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- BANK -->
        <?php if (in_array('bank', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../bank/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../bank/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-credit-card text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Bank Accounts</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        <!-- LEAVEX -->
        <?php if (in_array('leavex', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../leavex/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../leavex/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-credit-card text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Leave</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        
        
        
        
        <!-- LEAVE Dropdown -->
        <?php if (in_array('leave', $accessible_links) || in_array('notices', $accessible_links)): ?>
        <li class="sidebar-item">
          <a class="sidebar-link success-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#leaveDropdown">
            <span class="aside-icon p-2 bg-light-success rounded-3">
              <i class="ti ti-settings fs-7 text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Leave</span>
            <i class="ti ti-chevron-down ms-auto"></i>
          </a>
          <ul id="leaveDropdown" class="collapse list-unstyled" style="padding-left: 2rem;">
            <?php if (in_array('leave', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../leave/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../leaves/">
                <span class="hide-menu">Leave</span>
              </a>
            </li>
            <?php endif; ?>
            <?php if (in_array('leave', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../leave/balance.php' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../leaves/balance.php">
                <span class="hide-menu">Leave Balance</span>
              </a>
            </li>
            <?php endif; ?>
            
            
          </ul>
        </li>
        <?php endif; ?>
        
        
        
        
     
            
            <!-- HR Report -->
        <?php if (in_array('hrreport', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../hrreport/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../hrreport/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
             <i class="w100 fa-duotone fa-solid fa-folder text-warning"></i>
            </span>
      <span class="hide-menu ms-2 ps-1">HR Report</span>
          </a>
        </li>
        <?php endif; ?>
        
        
            
        
        <!-- Depatrue Sheet -->
        <?php if (in_array('email', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../depaturesheet/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../depaturesheet/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
             <i class="w100 fa-duotone fa-solid fa-plane text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Depatrue Sheet</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        
        
        <!-- ticket Dropdown -->
        <?php if (in_array('ticket', $accessible_links) || in_array('ticket', $accessible_links)): ?>
        <li class="sidebar-item">
          <a class="sidebar-link primary-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#ticketDropdown">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-solid fa-plane-up fs-7 text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Ticket</span>
            <i class="ti ti-chevron-down ms-auto"></i>
          </a>
          <ul id="ticketDropdown" class="collapse list-unstyled" style="padding-left: 2rem;">
            <?php if (in_array('ticket', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../tickets/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../tickets/">
                <span class="hide-menu">Ticket</span>
              </a>
            </li>
            <?php endif; ?>
            <?php if (in_array('ticket', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../leave_destination/' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../leave_destination/">
                <span class="hide-menu">Destination</span>
              </a>
            </li>
            <?php endif; ?>
            
          </ul>
        </li>
        <?php endif; ?>
        
        
        
        <?php if (in_array('ticketx', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../ticketx/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../ticketx/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
                <i class="fa-solid fa-plane-circle-check text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Tickets</span>
          </a>
        </li>
        <?php endif; ?>  
        
        
        
        
        
        
          <!-- sick
        <?php if (in_array('sick', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../tickets/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../tickets/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
                <i class="fa-solid fa-plane-circle-check text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Tickets</span>
          </a>
        </li>
        <?php endif; ?>  
         -->
        
        
        
        
        
        
        
        <!-- PASSPORT -->
        <?php if (in_array('passport', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../passport/' ? 'active' : ''; ?>">
          <a class="sidebar-link success-hover-bg" href="../passport/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-success rounded-3">
              <i class="fa-solid fa-passport text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Passport</span>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- ADVANCE -->
        <?php if (in_array('loan', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../loan/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../loan/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
              <i class="fa-solid fa-money-bill-1-wave text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Cash Advance</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        <!-- LOAN -->
        <?php if (in_array('loan', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../staff_loan/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../staff_loan/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
              <i class="fa-solid fa-money-bill-1-wave text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Loan</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
         <!-- CARD PRINT -->
        <?php if (in_array('cards', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../cards/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../cards/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-solid fa-id-card text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Card print</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        <!-- Island Transfer -->
        <?php if (in_array('transfer', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../transfer/' ? 'active' : ''; ?>">
          <a class="sidebar-link success-hover-bg" href="../transfer/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-success rounded-3">
              <i class="fa-solid fa-sailboat text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Island Transfer</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
        <!-- Medicals -->
        <?php if (in_array('medical', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../medical/' ? 'active' : ''; ?>">
          <a class="sidebar-link success-hover-bg" href="../medical/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-success rounded-3">
              <i class="fa-solid fa-star-of-life text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">WP Medicals</span>
          </a>
        </li>
        <?php endif; ?>
        
        
        
        
         <!-- Hospital -->
        <?php if (in_array('hospital', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../hospital/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../hospital/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-solid fa-star-of-life text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">General Medicals</span>
          </a>
        </li>
        <?php endif; ?>
        
        
    
        
         <!-- Visa -->
        <?php if (in_array('visa', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../visa/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../visa/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-brands fa-cc-visa text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Visa Stickers</span>
          </a>
        </li>
        <?php endif; ?>
        
         <!-- projects -->
        <?php if (in_array('projects', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../projects/' ? 'active' : ''; ?>">
          <a class="sidebar-link primary-hover-bg" href="../projects/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-primary rounded-3">
              <i class="fa-solid fa-building-user text-primary"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Projects</span>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- allocations -->
        <?php if (in_array('allocation', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../allocations/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../allocations/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
                <i class="fa-solid fa-person-digging text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Allocations</span>
          </a>
        </li>
        <?php endif; ?>
        
        <!-- pp_inv -->
        <?php if (in_array('pp_inv', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../passport_inv/' ? 'active' : ''; ?>">
          <a class="sidebar-link warning-hover-bg" href="../passport_inv/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-warning rounded-3">
                <i class="fa-solid fa-person-digging text-warning"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Passport Inv</span>
          </a>
        </li>
        <?php endif; ?>

        <!-- Sick -->
        <?php if (in_array('sick', $accessible_links)): ?>
        <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../sick/' ? 'active' : ''; ?>">
          <a class="sidebar-link danger-hover-bg" href="../sick/" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
                <i class="fa-solid fa-person-digging text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Sick Leaves</span>
          </a>
        </li>
        <?php endif; ?>  
        
        
       
        

        <!-- Settings with Dropdown -->
        <?php if (in_array('holidays', $accessible_links) || in_array('notices', $accessible_links)): ?>
        <li class="sidebar-item">
          <a class="sidebar-link success-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#settingsDropdown">
            <span class="aside-icon p-2 bg-light-success rounded-3">
              <i class="ti ti-settings fs-7 text-success"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Settings</span>
            <i class="ti ti-chevron-down ms-auto"></i>
          </a>
          <ul id="settingsDropdown" class="collapse list-unstyled" style="padding-left: 2rem;">
            <?php if (in_array('holidays', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../settings/holidays.php' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../settings/holidays.php">
                <span class="hide-menu">Holidays</span>
              </a>
            </li>
            <?php endif; ?>
            <?php if (in_array('notices', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../settings/notice.php' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../settings/notice.php">
                <span class="hide-menu">Notices</span>
              </a>
            </li>
            <?php endif; ?>
            <?php if (in_array('notices', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../settings/fm.php' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../settings/fm.php">
                <span class="hide-menu">Food Money calculator</span>
              </a>
            </li>
            <?php endif; ?>
            <?php if (in_array('users', $accessible_links)): ?>
            <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../settings/users.php' ? 'active' : ''; ?>">
              <a class="sidebar-link" href="../settings/users.php">
                <span class="hide-menu">users</span>
              </a>
            </li>
            <?php endif; ?>
          </ul>
        </li>
        <?php endif; ?>


        <!-- recruit Dropdown -->
            <?php if (in_array('candidates', $accessible_links) || in_array('vacancies', $accessible_links)): ?>
            <li class="sidebar-item">
              <a class="sidebar-link success-hover-bg" href="javascript:void(0)" aria-expanded="false" data-bs-toggle="collapse" data-bs-target="#recDropdown">
                <span class="aside-icon p-2 bg-light-success rounded-3">
                  <i class="ti ti-settings fs-7 text-success"></i>
                </span>
                <span class="hide-menu ms-2 ps-1">Recruitment</span>
                <i class="ti ti-chevron-down ms-auto"></i>
              </a>
              <ul id="recDropdown" class="collapse list-unstyled" style="padding-left: 2rem;">
                <?php if (in_array('candidates', $accessible_links)): ?>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../recruit/view_candidates.php' ? 'active' : ''; ?>">
                  <a class="sidebar-link" href="../recruit/view_candidates.php">
                    <span class="hide-menu">candidates</span>
                  </a>
                </li>
                <?php endif; ?>
                <?php if (in_array('vacancies', $accessible_links)): ?>
                <li class="sidebar-item <?php echo basename($_SERVER['PHP_SELF']) == '../recruit/view_jobs.php' ? 'active' : ''; ?>">
                  <a class="sidebar-link" href="../recruit/view_jobs.php">
                    <span class="hide-menu">Vacancies</span>
                  </a>
                </li>
                <?php endif; ?>
                
              </ul>
            </li>
            <?php endif; ?>










        <!-- Logout -->
        <li class="sidebar-item">
          <a class="sidebar-link danger-hover-bg" href="../logout.php" aria-expanded="false">
            <span class="aside-icon p-2 bg-light-danger rounded-3">
              <i class="fa-solid fa-power-off text-danger"></i>
            </span>
            <span class="hide-menu ms-2 ps-1">Logout</span>
          </a>
        </li>
      </ul>
    </nav>
  </div>
  
  
  
      <div id="lottie-loader" style="background:white; position:fixed; inset:0; z-index:9999; display:flex; justify-content:center; align-items:center;">
  <div id="lottie-animation" style="width:200px; height:200px;"></div>
</div>


  <!-- End Sidebar scroll-->
</aside>






<script src="https://kit.fontawesome.com/aea6da8de7.js" crossorigin="anonymous"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  
 
 
 
 <!---LOADER--->
<script src="https://unpkg.com/lottie-web@5.10.1/build/player/lottie.min.js"></script>
<script>
  const loader = lottie.loadAnimation({
    container: document.getElementById('lottie-animation'),
    renderer: 'svg',
    loop: true,
    autoplay: true,
    path: 'bp.json'
  });

  window.addEventListener('load', () => {
    const loaderEl = document.getElementById('lottie-loader');
    loaderEl.style.opacity = '0';
    setTimeout(() => {
      loaderEl.style.display = 'none';
    }, 500);
  });
</script>


  
  
  
  
  
