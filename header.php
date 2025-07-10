
<header class="app-header">


  <nav class="navbar navbar-expand-lg navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item d-block d-xl-none">
        <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
          <i class="ti ti-menu-2"></i>
        </a>
      </li>
      <!-- Notifications Dropdown -->
      <li class="nav-item dropdown">
        <a
          class="nav-link nav-icon-hover dropdown-toggle"
          href="#"
          id="notificationDropdown"
          role="button"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="ti ti-bell-ringing"></i>
          <div class="notification bg-primary rounded-circle"></div>
        </a>
        <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="notificationDropdown">
          <li><p class="dropdown-header">Notifications</p></li>
          <li><a class="dropdown-item" href="#"><i class="ti ti-check"></i> New comment on your post</a></li>
          <li><a class="dropdown-item" href="#"><i class="ti ti-mail"></i> You received a new message</a></li>
          <li><a class="dropdown-item" href="#"><i class="ti ti-alert-circle"></i> Server status updated</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item text-center" href="#">View All Notifications</a></li>
        </ul>
      </li>
    </ul>
    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
      <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
        <div id="currentTime" class="text-primary me-3 fw-bold d-none d-md-block"></div>
        <li class="nav-item dropdown">
          <a
            class="nav-link nav-icon-hover dropdown-toggle"
            href="#"
            id="userDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
          >
            <img src="assets/images/default.png" alt="" width="35" height="35" class="rounded-circle">
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
            <li><a href="profile.php" class="dropdown-item"><i class="ti ti-user fs-6"></i> My Profile</a></li>
            <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti ti-mail fs-6"></i> My Account</a></li>
            <li><a href="birthday_calendar.php" class="dropdown-item"><i class="ti ti-mail fs-6"></i> Birthdays</a></li>
            <li><a href="javascript:void(0)" class="dropdown-item"><i class="ti ti-list-check fs-6"></i> My Task</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a href="logout.php" class="dropdown-item">Logout</a></li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>

<script async
  src="https://dsa2p3ct4okjvj5swffl7pmg.agents.do-ai.run/static/chatbot/widget.js"
  data-agent-id="3a90f038-5888-11f0-bf8f-4e013e2ddde4"
  data-chatbot-id="8M4h0UwHtV1WEmjVhl46pxBxXCwsa6-L"
  data-name="hros-x"
  data-primary-color="#006bad"
  data-secondary-color="#E5E8ED"
  data-button-background-color="#0061EB"
  data-starting-message="???"
  data-logo="https://hros.rccmaldives.com/assets/images/logos/dark-logo.svg">
</script>

<script>
  // Ensure Bootstrap dropdown functionality is initialized
  document.addEventListener('DOMContentLoaded', function () {
    const userDropdown = document.querySelector('#userDropdown');
    const notificationDropdown = document.querySelector('#notificationDropdown');
    
    // Handle User Dropdown
    if (userDropdown) {
      userDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
        userDropdown.setAttribute('aria-expanded', userDropdown.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
      });
    }
    
    // Handle Notification Dropdown
    if (notificationDropdown) {
      notificationDropdown.addEventListener('click', function (e) {
        e.stopPropagation();
        notificationDropdown.setAttribute('aria-expanded', notificationDropdown.getAttribute('aria-expanded') === 'true' ? 'false' : 'true');
      });
    }
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function (e) {
      if (!e.target.closest('.dropdown')) {
        const dropdowns = document.querySelectorAll('.dropdown-menu');
        dropdowns.forEach(dropdown => {
          dropdown.classList.remove('show');
        });
        
        const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
          toggle.setAttribute('aria-expanded', 'false');
        });
      }
    });
  });

  // Real-time clock
  function updateClock() {
    const now = new Date();
    const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' };
    const timeElement = document.getElementById('currentTime');
    if (timeElement) {
      timeElement.textContent = now.toLocaleString('en-US', options);
    }
  }
  setInterval(updateClock, 1000);
  updateClock(); // Initialize immediately
</script>
<script src="assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>


