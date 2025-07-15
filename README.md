# HRoS - Human Resource Management System

## Role-Based Access Control (RBAC) & Permissions System

### Overview

This system uses a robust, database-driven role and permission management system. Permissions are stored in a dedicated `permissions` table, and roles are linked to permissions via a `role_permissions` pivot table. Users are assigned a single role, and their access to modules and actions is determined by the permissions attached to their role.

---

### Database Structure

- **permissions**: Stores all possible permissions (e.g., `employees.view`, `leaves.create`).
- **roles**: Stores all user roles (e.g., Admin, HR Manager, Employee).
- **role_permissions**: Pivot table linking roles to permissions (many-to-many).
- **users**: Each user has a `role_id`.

---

### Migrations

- `2025_07_14_232526_create_permissions_table.php`: Creates the `permissions` table with fields for name, display_name, description, module, action, etc.
- `2025_07_14_232532_create_role_permissions_table.php`: Creates the `role_permissions` pivot table.
- `2025_07_14_232738_remove_permissions_column_from_roles_table.php`: Removes the old JSON `permissions` column from the `roles` table.

**To run all migrations:**

```bash
php artisan migrate
```

---

### Seeders

- **PermissionSeeder**: Populates the `permissions` table with all permissions defined in `config/modules.php` and system permissions.
- **RoleSeeder**: Populates the `roles` table and assigns permissions to each role using the pivot table.
- **DatabaseSeeder**: Runs `PermissionSeeder` first, then `RoleSeeder`, then other seeders.

**To seed the database:**

```bash
php artisan db:seed
```

---

### Models

- **Permission**: Represents a single permission. Has relationships to roles and users (via roles).
- **Role**: Represents a user role. Has many users and many permissions (via pivot).
- **User**: Each user belongs to a role. Permission checks are delegated to the user's role.

---

### How Permissions Work

- Each module (e.g., Employees, Leaves, Attendance) and each action (view, create, edit, delete, etc.) is represented as a permission (e.g., `employees.view`).
- Roles are assigned permissions via the `role_permissions` table.
- Users inherit permissions from their assigned role.
- The sidebar and all routes are protected by permission checks, so users only see and access what they are allowed.

---

### Managing Roles & Permissions (Admin)

1. **Login as Admin** (default: username `admin`, password `123456`)
2. Go to **Role Management** from the sidebar.
3. View, create, edit, or delete roles.
4. Click **Manage Permissions** for any role to assign or revoke permissions using a module-based interface.
5. Save changes. The sidebar and all access will update automatically for users with that role.

---

### Adding/Editing Permissions

- To add new permissions, update `config/modules.php` and re-run the `PermissionSeeder`.
- To assign new permissions to roles, update the `RoleSeeder` or use the web interface as admin.

---

### Artisan Commands

- **Run migrations:**
    ```bash
    php artisan migrate
    ```
- **Rollback last migration batch:**
    ```bash
    php artisan migrate:rollback
    ```
- **Seed permissions and roles:**
    ```bash
    php artisan db:seed
    ```
- **Seed only permissions:**
    ```bash
    php artisan db:seed --class=PermissionSeeder
    ```
- **Seed only roles:**
    ```bash
    php artisan db:seed --class=RoleSeeder
    ```

---

### Code Reference

- **Permission checks in code:**
    ```php
    // In controllers or views
    if (auth()->user()->hasPermission('employees.view')) {
        // ...
    }
    ```
- **Sidebar and navigation:**
    - The sidebar is dynamically generated based on the user's permissions.
- **Middleware:**
    - All protected routes use the `CheckPermission` middleware for granular access control.

---

### Troubleshooting

- If you get a `Base table or view already exists` error, drop the table manually or rollback migrations before running them again.
- Always run `PermissionSeeder` before `RoleSeeder` to ensure all permissions exist before assigning them to roles.

---

### Customization

- **To add a new module:**

    1. Add it to `config/modules.php` with its permissions.
    2. Run `php artisan db:seed --class=PermissionSeeder` to add new permissions.
    3. Assign the new permissions to roles via the admin interface or `RoleSeeder`.

- **To add a new role:**
    1. Use the admin interface or add to `RoleSeeder`.
    2. Assign permissions as needed.

---

### Security

- Never edit the `.env` file for permissions or roles.
- All permission logic is enforced at both the UI and route/middleware level.

---

### Credits

- Developed with Laravel, MySQL, and Tailwind CSS.
- For questions or support, contact the system administrator.
