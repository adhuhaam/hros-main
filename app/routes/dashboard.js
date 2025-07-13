const express = require('express');
const { executeQuery } = require('../config/database');
const { permissionMiddleware } = require('../middleware/role');

const router = express.Router();

// Main dashboard route - redirects based on role
router.get('/', (req, res) => {
    const role = req.user.role_name;
    let dashboardPath = '/dashboard/other';
    
    switch (role) {
        case 'Admin':
            dashboardPath = '/dashboard/admin';
            break;
        case 'Information Officer':
            dashboardPath = '/dashboard/info-officer';
            break;
        case 'Xpat Officer':
            dashboardPath = '/dashboard/xpat-officer';
            break;
        case 'Leave Officer':
            dashboardPath = '/dashboard/leave-officer';
            break;
        case 'HR Manager':
            dashboardPath = '/dashboard/hrm';
            break;
        case 'Payroll Officer':
            dashboardPath = '/dashboard/payroll';
            break;
        case 'Supervisor':
            dashboardPath = '/dashboard/supervisor';
            break;
        case 'reception':
            dashboardPath = '/dashboard/reception';
            break;
        default:
            dashboardPath = '/dashboard/other';
    }
    
    res.redirect(dashboardPath);
});

// Admin Dashboard
router.get('/admin', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get employee counts by status
        const statusCounts = await executeQuery(`
            SELECT 
                employment_status,
                COUNT(*) as count
            FROM employees 
            GROUP BY employment_status
        `);

        // Get recent activities
        const recentActivities = await executeQuery(`
            SELECT 
                'employee_added' as type,
                CONCAT(first_name, ' ', last_name) as description,
                created_at as date
            FROM employees 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY created_at DESC 
            LIMIT 10
        `);

        // Get upcoming birthdays
        const upcomingBirthdays = await executeQuery(`
            SELECT 
                first_name,
                last_name,
                date_of_birth,
                DATEDIFF(
                    DATE_ADD(
                        DATE_FORMAT(date_of_birth, '%Y-%m-%d'),
                        INTERVAL YEAR(CURDATE()) - YEAR(date_of_birth) + IF(DAYOFYEAR(CURDATE()) > DAYOFYEAR(date_of_birth), 1, 0) YEAR
                    ),
                    CURDATE()
                ) as days_until_birthday
            FROM employees 
            WHERE employment_status = 'Active'
            HAVING days_until_birthday BETWEEN 0 AND 30
            ORDER BY days_until_birthday
            LIMIT 5
        `);

        // Get system statistics
        const stats = await executeQuery(`
            SELECT 
                (SELECT COUNT(*) FROM employees WHERE employment_status = 'Active') as active_employees,
                (SELECT COUNT(*) FROM employees WHERE employment_status = 'RESIGNED') as resigned_employees,
                (SELECT COUNT(*) FROM employees WHERE employment_status = 'TERMINATED') as terminated_employees,
                (SELECT COUNT(*) FROM employees WHERE employment_status = 'RETIRED') as retired_employees
        `);

        res.render('dashboard/admin', {
            title: 'Admin Dashboard - HROS',
            user: req.user,
            statusCounts: statusCounts.success ? statusCounts.data : [],
            recentActivities: recentActivities.success ? recentActivities.data : [],
            upcomingBirthdays: upcomingBirthdays.success ? upcomingBirthdays.data : [],
            stats: stats.success ? stats.data[0] : {}
        });

    } catch (error) {
        console.error('Admin dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Information Officer Dashboard
router.get('/info-officer', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get employee information
        const employeeStats = await executeQuery(`
            SELECT 
                COUNT(*) as total_employees,
                COUNT(CASE WHEN employment_status = 'Active' THEN 1 END) as active_employees,
                COUNT(CASE WHEN gender = 'Male' THEN 1 END) as male_employees,
                COUNT(CASE WHEN gender = 'Female' THEN 1 END) as female_employees
            FROM employees
        `);

        // Get recent notices
        const recentNotices = await executeQuery(`
            SELECT title, content, created_at 
            FROM notices 
            ORDER BY created_at DESC 
            LIMIT 5
        `);

        res.render('dashboard/info-officer', {
            title: 'Information Officer Dashboard - HROS',
            user: req.user,
            employeeStats: employeeStats.success ? employeeStats.data[0] : {},
            recentNotices: recentNotices.success ? recentNotices.data : []
        });

    } catch (error) {
        console.error('Info officer dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Xpat Officer Dashboard
router.get('/xpat-officer', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get expat-specific data
        const expatStats = await executeQuery(`
            SELECT 
                COUNT(*) as total_expats,
                COUNT(CASE WHEN visa_status = 'Active' THEN 1 END) as active_visas,
                COUNT(CASE WHEN passport_status = 'Valid' THEN 1 END) as valid_passports
            FROM employees 
            WHERE employee_type = 'Expat'
        `);

        // Get expiring documents
        const expiringDocuments = await executeQuery(`
            SELECT 
                first_name,
                last_name,
                passport_expiry_date,
                visa_expiry_date,
                DATEDIFF(passport_expiry_date, CURDATE()) as days_until_passport_expiry,
                DATEDIFF(visa_expiry_date, CURDATE()) as days_until_visa_expiry
            FROM employees 
            WHERE employee_type = 'Expat'
            AND (passport_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY) 
                 OR visa_expiry_date <= DATE_ADD(CURDATE(), INTERVAL 90 DAY))
            ORDER BY LEAST(passport_expiry_date, visa_expiry_date)
            LIMIT 10
        `);

        res.render('dashboard/xpat-officer', {
            title: 'Xpat Officer Dashboard - HROS',
            user: req.user,
            expatStats: expatStats.success ? expatStats.data[0] : {},
            expiringDocuments: expiringDocuments.success ? expiringDocuments.data : []
        });

    } catch (error) {
        console.error('Xpat officer dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Leave Officer Dashboard
router.get('/leave-officer', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get leave statistics
        const leaveStats = await executeQuery(`
            SELECT 
                COUNT(*) as total_leave_requests,
                COUNT(CASE WHEN status = 'Pending' THEN 1 END) as pending_requests,
                COUNT(CASE WHEN status = 'Approved' THEN 1 END) as approved_requests,
                COUNT(CASE WHEN status = 'Rejected' THEN 1 END) as rejected_requests
            FROM leave_requests
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        `);

        // Get pending leave requests
        const pendingRequests = await executeQuery(`
            SELECT 
                lr.id,
                lr.leave_type,
                lr.start_date,
                lr.end_date,
                lr.reason,
                lr.created_at,
                CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                e.employee_id
            FROM leave_requests lr
            INNER JOIN employees e ON lr.employee_id = e.id
            WHERE lr.status = 'Pending'
            ORDER BY lr.created_at ASC
            LIMIT 10
        `);

        // Get upcoming holidays
        const upcomingHolidays = await executeQuery(`
            SELECT holiday_name, holiday_date
            FROM holidays
            WHERE holiday_date >= CURDATE()
            ORDER BY holiday_date
            LIMIT 5
        `);

        res.render('dashboard/leave-officer', {
            title: 'Leave Officer Dashboard - HROS',
            user: req.user,
            leaveStats: leaveStats.success ? leaveStats.data[0] : {},
            pendingRequests: pendingRequests.success ? pendingRequests.data : [],
            upcomingHolidays: upcomingHolidays.success ? upcomingHolidays.data : []
        });

    } catch (error) {
        console.error('Leave officer dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// HR Manager Dashboard
router.get('/hrm', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get HR statistics
        const hrStats = await executeQuery(`
            SELECT 
                COUNT(*) as total_employees,
                COUNT(CASE WHEN employment_status = 'Active' THEN 1 END) as active_employees,
                COUNT(CASE WHEN employment_status = 'RESIGNED' THEN 1 END) as resigned_employees,
                COUNT(CASE WHEN employment_status = 'TERMINATED' THEN 1 END) as terminated_employees
            FROM employees
        `);

        // Get recent activities
        const recentActivities = await executeQuery(`
            SELECT 
                'employee_added' as type,
                CONCAT(first_name, ' ', last_name) as description,
                created_at as date
            FROM employees 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ORDER BY created_at DESC 
            LIMIT 10
        `);

        res.render('dashboard/hrm', {
            title: 'HR Manager Dashboard - HROS',
            user: req.user,
            hrStats: hrStats.success ? hrStats.data[0] : {},
            recentActivities: recentActivities.success ? recentActivities.data : []
        });

    } catch (error) {
        console.error('HR manager dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Payroll Officer Dashboard
router.get('/payroll', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get payroll statistics
        const payrollStats = await executeQuery(`
            SELECT 
                COUNT(*) as total_employees,
                SUM(basic_salary) as total_salary_budget,
                AVG(basic_salary) as average_salary
            FROM employees 
            WHERE employment_status = 'Active'
        `);

        // Get recent payroll activities
        const recentPayroll = await executeQuery(`
            SELECT 
                'salary_processed' as type,
                CONCAT(e.first_name, ' ', e.last_name) as description,
                p.processed_date as date
            FROM payroll p
            INNER JOIN employees e ON p.employee_id = e.id
            WHERE p.processed_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY p.processed_date DESC 
            LIMIT 10
        `);

        res.render('dashboard/payroll', {
            title: 'Payroll Officer Dashboard - HROS',
            user: req.user,
            payrollStats: payrollStats.success ? payrollStats.data[0] : {},
            recentPayroll: recentPayroll.success ? recentPayroll.data : []
        });

    } catch (error) {
        console.error('Payroll officer dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Other Staff Dashboard
router.get('/other', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get basic employee information
        const employeeInfo = await executeQuery(`
            SELECT 
                employee_id,
                first_name,
                last_name,
                department,
                position,
                employment_status
            FROM employees 
            WHERE id = ?
        `, [req.user.id]);

        // Get recent notices
        const recentNotices = await executeQuery(`
            SELECT title, content, created_at 
            FROM notices 
            ORDER BY created_at DESC 
            LIMIT 5
        `);

        res.render('dashboard/other', {
            title: 'Dashboard - HROS',
            user: req.user,
            employeeInfo: employeeInfo.success ? employeeInfo.data[0] : {},
            recentNotices: recentNotices.success ? recentNotices.data : []
        });

    } catch (error) {
        console.error('Other staff dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Supervisor Dashboard
router.get('/supervisor', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get team members
        const teamMembers = await executeQuery(`
            SELECT 
                employee_id,
                first_name,
                last_name,
                department,
                position,
                employment_status
            FROM employees 
            WHERE supervisor_id = ?
            AND employment_status = 'Active'
        `, [req.user.id]);

        // Get team leave requests
        const teamLeaveRequests = await executeQuery(`
            SELECT 
                lr.id,
                lr.leave_type,
                lr.start_date,
                lr.end_date,
                lr.status,
                CONCAT(e.first_name, ' ', e.last_name) as employee_name
            FROM leave_requests lr
            INNER JOIN employees e ON lr.employee_id = e.id
            WHERE e.supervisor_id = ?
            AND lr.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ORDER BY lr.created_at DESC
            LIMIT 10
        `, [req.user.id]);

        res.render('dashboard/supervisor', {
            title: 'Supervisor Dashboard - HROS',
            user: req.user,
            teamMembers: teamMembers.success ? teamMembers.data : [],
            teamLeaveRequests: teamLeaveRequests.success ? teamLeaveRequests.data : []
        });

    } catch (error) {
        console.error('Supervisor dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

// Reception Dashboard
router.get('/reception', permissionMiddleware(['dashboard']), async (req, res) => {
    try {
        // Get accommodation requests
        const accommodationRequests = await executeQuery(`
            SELECT 
                ar.id,
                ar.request_type,
                ar.status,
                ar.created_at,
                CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                e.employee_id
            FROM accommodation_requests ar
            INNER JOIN employees e ON ar.employee_id = e.id
            WHERE ar.status = 'Pending'
            ORDER BY ar.created_at ASC
            LIMIT 10
        `);

        res.render('dashboard/reception', {
            title: 'Reception Dashboard - HROS',
            user: req.user,
            accommodationRequests: accommodationRequests.success ? accommodationRequests.data : []
        });

    } catch (error) {
        console.error('Reception dashboard error:', error);
        res.render('error', {
            title: 'Error',
            message: 'An error occurred while loading the dashboard.'
        });
    }
});

module.exports = router;