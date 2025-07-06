<?php include 'db.php'; ?>
<?php include '../session.php'; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Task Board</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
  <style>
    .task-card {
      background-color: #f8f9fa;
      border-left: 4px solid #0d6efd;
      padding: 12px;
      border-radius: 6px;
      box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
      transition: transform 0.1s ease-in-out;
    }
    .task-card:hover {
      transform: translateY(-2px);
    }
    .task-column {
      min-height: 300px;
      background-color: #ffffff;
      border-radius: 8px;
      padding: 15px;
      box-shadow: 0 0 10px rgba(0,0,0,0.03);
    }
    .column-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 15px;
    }
    .priority-badge {
      font-size: 0.75rem;
      padding: 3px 6px;
      border-radius: 4px;
      color: #fff;
    }
    .priority-low { background-color: #6c757d; }
    .priority-normal { background-color: #0d6efd; }
    .priority-high { background-color: #dc3545; }
  </style>
</head>
<body>

<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

<div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
     data-sidebartype="full" data-sidebar-position="fixed" data-header-position="fixed">
  <?php include '../sidebar.php'; ?>
  <div class="body-wrapper">
    <?php include '../header.php'; ?>
    <div class="container-fluid" style="max-width:100%;">
      <div class="card">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="card-title fw-semibold">HR Task Management Todo-Board</h5>
            <button onclick="openModal()" class="btn btn-primary">+ New Task</button>
          </div>

          <div id="loader" class="text-center my-4">
            <lottie-player
              src="bp.json"
              background="transparent"
              speed="1"
              style="width: 100px; height: 100px; margin: auto;"
              loop autoplay>
            </lottie-player>
            <p class="text-muted">Loading tasks...</p>
          </div>

          <div id="taskBoard" class="row g-4" style="display:none;"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="taskModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="taskForm" onsubmit="return submitTask(event)">
        <input type="hidden" name="id" id="taskId">
        <div class="modal-header">
          <h5 class="modal-title" id="taskModalTitle">Add New Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Category / Topic</label>
            <select name="category_id" class="form-select" id="categorySelect"></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Assign To</label>
            <select name="assigned_to" class="form-select" id="assignedToSelect"></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_id" class="form-select" id="statusSelect"></select>
          </div>
          <div class="mb-3">
            <label class="form-label">Priority</label>
            <select name="priority" class="form-select">
              <option value="Low">Low</option>
              <option value="Normal" selected>Normal</option>
              <option value="High">High</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Due Date</label>
            <input type="date" name="due_date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Save Task</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Scripts -->
<script src="../assets/libs/jquery/dist/jquery.min.js"></script>
<script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/sidebarmenu.js"></script>
<script src="../assets/js/app.min.js"></script>

<script>
  async function loadBoard() {
    const loader = document.getElementById('loader');
    const board = document.getElementById('taskBoard');

    loader.style.display = 'block';
    board.style.display = 'none';

    const statuses = await fetch('api/get_statuses.php').then(res => res.json());
    const tasks = await fetch('api/get_tasks.php').then(res => res.json());

    board.innerHTML = '';

    const statusColors = {
      1: 'bg-light',
      2: 'bg-warning-subtle',
      3: 'bg-info-subtle',
      4: 'bg-success-subtle'
    };

    statuses.forEach(status => {
      const bgColor = statusColors[status.id] || 'bg-light';
      const col = document.createElement('div');
      col.className = 'col-md-3';
      col.innerHTML = `
        <div class="task-column ${bgColor}">
          <div class="column-title">${status.name}</div>
          <div id="column-${status.id}" class="d-grid gap-3"></div>
        </div>`;
      board.appendChild(col);

      const columnEl = col.querySelector('.d-grid');
      tasks.filter(task => task.status_id == status.id).forEach(task => {
        const card = document.createElement('div');
        card.className = 'task-card';
        card.setAttribute('data-id', task.id);
        card.setAttribute('data-task', JSON.stringify(task));

        const priorityClass = task.priority.toLowerCase() === 'high' ? 'priority-high' :
                              task.priority.toLowerCase() === 'low' ? 'priority-low' : 'priority-normal';
        const formattedDate = formatDateDisplay(task.due_date);

        card.innerHTML = `
          <div class="text-center fw-bold mb-1">${task.title}</div>
          <div class="text-muted small mb-1">${task.description || ''}</div>
          <div class="text-primary small">${task.assignee_name || 'Unassigned'}</div>
          <div class="mb-1"><span class="badge bg-secondary text-light mt-2">${task.category_name || 'Uncategorized'}</span></div>
          <div class="mt-2 d-flex justify-content-between align-items-center">
            <span class="priority-badge ${priorityClass}">${task.priority}</span>
            <div>
              <i class="bi bi-trash-fill text-danger ms-2" style="cursor: pointer;" title="Delete Task"
                 onclick="deleteTask(${task.id})"></i>
              <small class="text-muted ms-2">${formattedDate}</small>
            </div>
          </div>`;

        card.addEventListener('contextmenu', function(e) {
          e.preventDefault();
          const taskData = JSON.parse(this.dataset.task);
          openModal(taskData);
        });

        columnEl.appendChild(card);
      });

      new Sortable(columnEl, {
        group: 'tasks',
        animation: 150,
        onEnd: async evt => {
          const taskId = evt.item.dataset.id;
          const newStatusId = parseInt(evt.to.id.replace('column-', ''));
          await fetch('api/update_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `task_id=${taskId}&status_id=${newStatusId}`
          });
        }
      });
    });

    loader.style.display = 'none';
    board.style.display = 'flex';
  }

  function openModal(task = null) {
    const modal = new bootstrap.Modal(document.getElementById('taskModal'));
    document.getElementById('taskModalTitle').innerText = task ? 'Edit Task' : 'Add New Task';
    document.getElementById('taskId').value = task ? task.id : '';
    document.querySelector('[name="title"]').value = task ? task.title : '';
    document.querySelector('[name="description"]').value = task ? task.description : '';
    document.querySelector('[name="due_date"]').value = task && task.due_date ? formatDateForInput(task.due_date) : '';
    document.querySelector('[name="priority"]').value = task ? task.priority : 'Normal';

    loadUsers().then(() => {
      document.getElementById('assignedToSelect').value = task ? task.assigned_to : '';
    });

    loadStatuses().then(() => {
      document.getElementById('statusSelect').value = task ? task.status_id : '';
    });

    loadCategories().then(() => {
      document.getElementById('categorySelect').value = task ? task.category_id : '';
    });

    modal.show();
  }

  async function loadUsers() {
    const users = await fetch('api/users.php').then(res => res.json());
    const select = document.getElementById('assignedToSelect');
    select.innerHTML = '<option value="">-- Select User --</option>';
    users.forEach(user => {
      const option = document.createElement('option');
      option.value = user.id;
      option.textContent = user.name;
      select.appendChild(option);
    });
  }

  async function loadStatuses() {
    const statuses = await fetch('api/get_statuses.php').then(res => res.json());
    const select = document.getElementById('statusSelect');
    select.innerHTML = '';
    statuses.forEach(status => {
      const option = document.createElement('option');
      option.value = status.id;
      option.textContent = status.name;
      select.appendChild(option);
    });
  }

  async function loadCategories() {
    const categories = await fetch('api/get_categories.php').then(res => res.json());
    const select = document.getElementById('categorySelect');
    select.innerHTML = '<option value="">-- Select Category --</option>';
    categories.forEach(cat => {
      const option = document.createElement('option');
      option.value = cat.id;
      option.textContent = cat.name;
      select.appendChild(option);
    });
  }

  async function deleteTask(id) {
    if (!confirm("Are you sure you want to delete this task?")) return;

    const res = await fetch('api/delete_task.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `id=${id}`
    });

    const data = await res.json();
    if (data.status === 'success') {
      loadBoard();
    } else {
      alert('Error deleting task: ' + data.message);
    }
  }

  async function submitTask(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('taskForm'));
    const isEdit = !!formData.get('id');

    const res = await fetch(isEdit ? 'api/update_task.php' : 'api/add_task.php', {
      method: 'POST',
      body: formData
    });

    const data = await res.json();
    if (data.status === 'success') {
      const modal = bootstrap.Modal.getInstance(document.getElementById('taskModal'));
      modal.hide();
      document.getElementById('taskForm').reset();
      loadBoard();
    } else {
      alert('Error: ' + data.message);
    }
  }

  function formatDateForInput(dateStr) {
    const date = new Date(dateStr);
    if (isNaN(date)) return '';
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  function formatDateDisplay(dateStr) {
    const date = new Date(dateStr);
    if (isNaN(date)) return '';
    const day = String(date.getDate()).padStart(2, '0');
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
  }

  window.onload = loadBoard;
</script>

</body>
</html>
