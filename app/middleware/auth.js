const { executeQuery } = require('../config/database');

// Authentication middleware
const authMiddleware = async (req, res, next) => {
    try {
        // Check if user is logged in
        if (!req.session.user || !req.session.user.id) {
            return res.redirect('/auth/login');
        }

        // Verify user still exists in database
        const result = await executeQuery(
            'SELECT u.id, u.username, u.email, r.role_name FROM users u INNER JOIN roles r ON u.role_id = r.id WHERE u.id = ? AND u.status = "active"',
            [req.session.user.id]
        );

        if (!result.success || result.data.length === 0) {
            // User no longer exists or is inactive
            req.session.destroy();
            return res.redirect('/auth/login?message=session_expired');
        }

        // Update session with fresh user data
        req.session.user = result.data[0];
        req.user = req.session.user;
        
        next();
    } catch (error) {
        console.error('Auth middleware error:', error);
        req.session.destroy();
        return res.redirect('/auth/login?message=error');
    }
};

// Guest middleware (for login/register pages)
const guestMiddleware = (req, res, next) => {
    if (req.session.user) {
        return res.redirect('/dashboard');
    }
    next();
};

// Rate limiting for login attempts
const loginRateLimit = require('express-rate-limit')({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 5, // limit each IP to 5 requests per windowMs
    message: 'Too many login attempts from this IP, please try again after 15 minutes',
    standardHeaders: true,
    legacyHeaders: false,
});

module.exports = {
    authMiddleware,
    guestMiddleware,
    loginRateLimit
};