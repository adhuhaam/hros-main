<?php

return [
    'employees' => [
        'name' => 'Employees',
        'route' => 'employees.index',
        'permission' => 'employees.view',
        'icon' => 'fa-solid fa-users',
        'description' => 'Manage employee information and records',
        'permissions' => [
            'employees.view' => 'View employees',
            'employees.create' => 'Create employees',
            'employees.edit' => 'Edit employees',
            'employees.delete' => 'Delete employees',
            'employees.export' => 'Export employees',
            'employees.import' => 'Import employees',
        ]
    ],
    
    'leaves' => [
        'name' => 'Leave Management',
        'route' => 'leaves.index',
        'permission' => 'leaves.view',
        'icon' => 'fa-solid fa-calendar',
        'description' => 'Manage employee leave requests and approvals',
        'permissions' => [
            'leaves.view' => 'View leaves',
            'leaves.create' => 'Create leaves',
            'leaves.edit' => 'Edit leaves',
            'leaves.delete' => 'Delete leaves',
            'leaves.approve' => 'Approve leaves',
            'leaves.reject' => 'Reject leaves',
        ]
    ],
    
    'attendance' => [
        'name' => 'Attendance',
        'route' => 'attendance.index',
        'permission' => 'attendance.view',
        'icon' => 'fa-solid fa-clock',
        'description' => 'Track employee attendance and time records',
        'permissions' => [
            'attendance.view' => 'View attendance',
            'attendance.check_in' => 'Check in/out',
            'attendance.check_out' => 'Check out',
            'attendance.edit' => 'Edit attendance',
            'attendance.approve' => 'Approve attendance',
            'attendance.report' => 'View attendance reports',
            'attendance.export' => 'Export attendance',
            'attendance.import' => 'Import attendance',
            'attendance.bulk_approve' => 'Bulk approve attendance',
        ]
    ],
    
    'loans' => [
        'name' => 'Loans',
        'route' => 'loans.index',
        'permission' => 'loans.view',
        'icon' => 'fa-solid fa-money-bill',
        'description' => 'Manage employee loan applications and payments',
        'permissions' => [
            'loans.view' => 'View loans',
            'loans.create' => 'Create loans',
            'loans.edit' => 'Edit loans',
            'loans.delete' => 'Delete loans',
            'loans.approve' => 'Approve loans',
            'loans.reject' => 'Reject loans',
            'loans.pay_installment' => 'Pay loan installments',
        ]
    ],
    
    'users' => [
        'name' => 'User Management',
        'route' => 'users.index',
        'permission' => 'users.view',
        'icon' => 'fa-solid fa-users-cog',
        'description' => 'Manage system users and their accounts',
        'permissions' => [
            'users.view' => 'View users',
            'users.create' => 'Create users',
            'users.edit' => 'Edit users',
            'users.delete' => 'Delete users',
            'users.reset_password' => 'Reset user passwords',
        ]
    ],
    
    'roles' => [
        'name' => 'Role Management',
        'route' => 'roles.index',
        'permission' => 'roles.view',
        'icon' => 'fa-solid fa-user-shield',
        'description' => 'Manage user roles and permissions',
        'permissions' => [
            'roles.view' => 'View roles',
            'roles.create' => 'Create roles',
            'roles.edit' => 'Edit roles',
            'roles.delete' => 'Delete roles',
            'roles.assign_permissions' => 'Assign permissions to roles',
        ]
    ],
    
    'reports' => [
        'name' => 'Reports',
        'route' => 'reports.index',
        'permission' => 'reports.view',
        'icon' => 'fa-solid fa-chart-bar',
        'description' => 'Generate and view various system reports',
        'permissions' => [
            'reports.view' => 'View reports',
            'reports.employee' => 'Employee reports',
            'reports.attendance' => 'Attendance reports',
            'reports.leave' => 'Leave reports',
            'reports.loan' => 'Loan reports',
            'reports.payroll' => 'Payroll reports',
        ]
    ],
    
    'settings' => [
        'name' => 'Settings',
        'route' => 'settings.index',
        'permission' => 'settings.view',
        'icon' => 'fa-solid fa-cog',
        'description' => 'Manage system settings and configurations',
        'permissions' => [
            'settings.view' => 'View settings',
            'settings.edit' => 'Edit settings',
        ]
    ],
    
    'medical_records' => [
        'name' => 'Medical Records',
        'route' => 'medical-records.index',
        'permission' => 'medical_records.view',
        'icon' => 'fa-solid fa-heartbeat',
        'description' => 'Manage employee medical records and examinations',
        'permissions' => [
            'medical_records.view' => 'View medical records',
            'medical_records.create' => 'Create medical records',
            'medical_records.edit' => 'Edit medical records',
            'medical_records.delete' => 'Delete medical records',
        ]
    ],
    
    'warnings' => [
        'name' => 'Warnings',
        'route' => 'warnings.index',
        'permission' => 'warnings.view',
        'icon' => 'fa-solid fa-exclamation-triangle',
        'description' => 'Manage employee warnings and disciplinary actions',
        'permissions' => [
            'warnings.view' => 'View warnings',
            'warnings.create' => 'Create warnings',
            'warnings.edit' => 'Edit warnings',
            'warnings.delete' => 'Delete warnings',
        ]
    ],
    
    'documents' => [
        'name' => 'Documents',
        'route' => 'documents.index',
        'permission' => 'documents.view',
        'icon' => 'fa-solid fa-file-alt',
        'description' => 'Manage employee documents and files',
        'permissions' => [
            'documents.view' => 'View documents',
            'documents.create' => 'Create documents',
            'documents.edit' => 'Edit documents',
            'documents.delete' => 'Delete documents',
            'documents.download' => 'Download documents',
        ]
    ],
    
    'holidays' => [
        'name' => 'Holidays',
        'route' => 'holidays.index',
        'permission' => 'holidays.view',
        'icon' => 'fa-solid fa-calendar-day',
        'description' => 'Manage company holidays and public holidays',
        'permissions' => [
            'holidays.view' => 'View holidays',
            'holidays.create' => 'Create holidays',
            'holidays.edit' => 'Edit holidays',
            'holidays.delete' => 'Delete holidays',
        ]
    ],
    
    'notices' => [
        'name' => 'Notices',
        'route' => 'notices.index',
        'permission' => 'notices.view',
        'icon' => 'fa-solid fa-bullhorn',
        'description' => 'Manage company notices and announcements',
        'permissions' => [
            'notices.view' => 'View notices',
            'notices.create' => 'Create notices',
            'notices.edit' => 'Edit notices',
            'notices.delete' => 'Delete notices',
        ]
    ],
]; 