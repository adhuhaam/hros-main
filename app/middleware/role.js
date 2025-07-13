// Role-based access control middleware
const rolePermissions = {
    'Admin': ['ot', 'users', 'contracts', 'chat', 'new_mail', 'accommodation', 'tasks', 'missing', 'depaturesheet', 'hrreport', 'retire', 'terminate', 'email', 'ticket', 'resign', 'pp_inv', 'workpermit', 'vacancies', 'candidates', 'tickets', 'sick', 'leave', 'dashboard', 'employees', 'cards', 'holidays', 'notices', 'settings', 'bank', 'payroll', 'logout', 'attendance', 'loan', 'passport', 'transfer', 'visa', 'projects', 'medical', 'warning', 'document', 'allocation', 'hospital'],
    'Information Officer': ['ot', 'chat', 'contracts', 'new_mail', 'sick', 'accommodation', 'tasks', 'missing', 'depaturesheet', 'hrreport', 'retire', 'terminate', 'notices', 'holidays', 'settings', 'email', 'resign', 'transfer', 'ticket', 'pp_inv', 'workpermit', 'hospital', 'tickets', 'sick', 'dashboard', 'employees', 'notices', 'cards', 'loan', 'passport', 'attendance', 'bank', 'logout', 'payroll', 'projects', 'medical', 'warning', 'document', 'allocation', 'visa', 'leave'],
    'Xpat Officer': ['tasks', 'bank', 'leave', 'workpermit', 'dashboard', 'employees', 'loan', 'passport', 'visa', 'medical', 'document'],
    'Leave Officer': ['tasks', 'tickets', 'dashboard', 'emp', 'holidays', 'leave', 'loan', 'attendance', 'document', 'cards', 'notices', 'warning', 'logout', 'allocation'],
    'HR Manager': ['sick', 'accommodation', 'tasks', 'missing', 'depaturesheet', 'hrreport', 'retire', 'terminate', 'notices', 'holidays', 'settings', 'email', 'resign', 'transfer', 'ticket', 'pp_inv', 'workpermit', 'hospital', 'tickets', 'sick', 'dashboard', 'employees', 'notices', 'cards', 'loan', 'passport', 'attendance', 'bank', 'logout', 'payroll', 'projects', 'medical', 'warning', 'document', 'allocation', 'visa', 'leave'],
    'Payroll Officer': ['ot', 'leavex', 'depaturesheet', 'loan', 'tasks', 'hrreport', 'transfer', 'hospital', 'dashboard', 'empl', 'payroll', 'attendance', 'sick', 'hospital'],
    'Supervisor': ['dashboard', 'emp', 'allocation'],
    'Other Staff': ['accommodation', 'tasks', 'ticketx', 'leavex', 'dashboard', 'employees', 'transfer', 'medical', 'hospital', 'projects', 'bank', 'sick'],
    'welfare': ['accommodation', 'tasks', 'allocation', 'emp', 'projects'],
    'planing': ['emp', 'projects', 'allocation'],
    'Guest': ['allocation'],
    'reception': ['accommodation', 'emp']
};

// Role middleware
const roleMiddleware = (requiredRole) => {
    return (req, res, next) => {
        if (!req.user || !req.user.role_name) {
            return res.status(403).render('error', {
                title: 'Access Denied',
                message: 'You do not have permission to access this resource.'
            });
        }

        const userRole = req.user.role_name;
        const userPermissions = rolePermissions[userRole] || [];

        // Check if user has the required permission
        if (userPermissions.includes(requiredRole) || userRole === 'Admin') {
            next();
        } else {
            res.status(403).render('error', {
                title: 'Access Denied',
                message: 'You do not have permission to access this resource.'
            });
        }
    };
};

// Permission middleware
const permissionMiddleware = (requiredPermissions) => {
    return (req, res, next) => {
        if (!req.user || !req.user.role_name) {
            return res.status(403).render('error', {
                title: 'Access Denied',
                message: 'You do not have permission to access this resource.'
            });
        }

        const userRole = req.user.role_name;
        const userPermissions = rolePermissions[userRole] || [];

        // Check if user has any of the required permissions
        const hasPermission = requiredPermissions.some(permission => 
            userPermissions.includes(permission) || userRole === 'Admin'
        );

        if (hasPermission) {
            next();
        } else {
            res.status(403).render('error', {
                title: 'Access Denied',
                message: 'You do not have permission to access this resource.'
            });
        }
    };
};

// Get user permissions helper
const getUserPermissions = (roleName) => {
    return rolePermissions[roleName] || [];
};

// Get dashboard link based on role
const getDashboardLink = (roleName) => {
    const dashboardLinks = {
        'Admin': '/dashboard/admin',
        'Information Officer': '/dashboard/info-officer',
        'Xpat Officer': '/dashboard/xpat-officer',
        'Leave Officer': '/dashboard/leave-officer',
        'HR Manager': '/dashboard/hrm',
        'Payroll Officer': '/dashboard/payroll',
        'Other Staff': '/dashboard/other',
        'Supervisor': '/dashboard/supervisor',
        'planing': '/dashboard/other',
        'reception': '/dashboard/reception'
    };
    
    return dashboardLinks[roleName] || '/dashboard/other';
};

module.exports = {
    roleMiddleware,
    permissionMiddleware,
    getUserPermissions,
    getDashboardLink,
    rolePermissions
};