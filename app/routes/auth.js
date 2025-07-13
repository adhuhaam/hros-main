const express = require('express');
const bcrypt = require('bcryptjs');
const { body, validationResult } = require('express-validator');
const { executeQuery } = require('../config/database');
const { guestMiddleware, loginRateLimit } = require('../middleware/auth');
const { getDashboardLink } = require('../middleware/role');

const router = express.Router();

// Login page
router.get('/login', guestMiddleware, (req, res) => {
    const message = req.query.message;
    let error = null;
    
    if (message === 'session_expired') {
        error = 'Your session has expired. Please login again.';
    } else if (message === 'error') {
        error = 'An error occurred. Please try again.';
    }
    
    res.render('auth/login', {
        title: 'Login - HROS',
        error: error,
        success: null
    });
});

// Login process
router.post('/login', guestMiddleware, loginRateLimit, [
    body('username').notEmpty().withMessage('Username is required'),
    body('password').notEmpty().withMessage('Password is required')
], async (req, res) => {
    try {
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.render('auth/login', {
                title: 'Login - HROS',
                error: errors.array()[0].msg,
                success: null
            });
        }

        const { username, password } = req.body;

        // Get user with role
        const result = await executeQuery(
            `SELECT u.id, u.username, u.password, u.email, r.role_name 
             FROM users u 
             INNER JOIN roles r ON u.role_id = r.id 
             WHERE u.username = ? AND u.status = 'active'`,
            [username]
        );

        if (!result.success || result.data.length === 0) {
            return res.render('auth/login', {
                title: 'Login - HROS',
                error: 'Invalid Employee No or password!',
                success: null
            });
        }

        const user = result.data[0];

        // Verify password
        const isValidPassword = await bcrypt.compare(password, user.password);
        if (!isValidPassword) {
            return res.render('auth/login', {
                title: 'Login - HROS',
                error: 'Invalid Employee No or password!',
                success: null
            });
        }

        // Set session
        req.session.user = {
            id: user.id,
            username: user.username,
            email: user.email,
            role_name: user.role_name
        };

        // Log successful login
        await executeQuery(
            'INSERT INTO login_logs (user_id, login_time, ip_address) VALUES (?, NOW(), ?)',
            [user.id, req.ip]
        );

        // Redirect based on role
        const dashboardLink = getDashboardLink(user.role_name);
        res.redirect(dashboardLink);

    } catch (error) {
        console.error('Login error:', error);
        res.render('auth/login', {
            title: 'Login - HROS',
            error: 'An error occurred during login. Please try again.',
            success: null
        });
    }
});

// Logout
router.get('/logout', (req, res) => {
    req.session.destroy((err) => {
        if (err) {
            console.error('Logout error:', err);
        }
        res.redirect('/auth/login');
    });
});

// Forgot password page
router.get('/forgot-password', guestMiddleware, (req, res) => {
    res.render('auth/forgot-password', {
        title: 'Forgot Password - HROS',
        error: null,
        success: null
    });
});

// Forgot password process
router.post('/forgot-password', guestMiddleware, [
    body('email').isEmail().withMessage('Please enter a valid email address')
], async (req, res) => {
    try {
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.render('auth/forgot-password', {
                title: 'Forgot Password - HROS',
                error: errors.array()[0].msg,
                success: null
            });
        }

        const { email } = req.body;

        // Check if user exists
        const result = await executeQuery(
            'SELECT id, username, email FROM users WHERE email = ? AND status = "active"',
            [email]
        );

        if (!result.success || result.data.length === 0) {
            return res.render('auth/forgot-password', {
                title: 'Forgot Password - HROS',
                error: 'No account found with this email address.',
                success: null
            });
        }

        const user = result.data[0];

        // Generate reset token
        const resetToken = require('crypto').randomBytes(32).toString('hex');
        const resetTokenExpiry = new Date(Date.now() + 3600000); // 1 hour

        // Save reset token
        await executeQuery(
            'UPDATE users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?',
            [resetToken, resetTokenExpiry, user.id]
        );

        // TODO: Send email with reset link
        // For now, just show success message
        res.render('auth/forgot-password', {
            title: 'Forgot Password - HROS',
            error: null,
            success: 'Password reset instructions have been sent to your email.'
        });

    } catch (error) {
        console.error('Forgot password error:', error);
        res.render('auth/forgot-password', {
            title: 'Forgot Password - HROS',
            error: 'An error occurred. Please try again.',
            success: null
        });
    }
});

// Reset password page
router.get('/reset-password/:token', guestMiddleware, async (req, res) => {
    try {
        const { token } = req.params;

        // Check if token is valid
        const result = await executeQuery(
            'SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() AND status = "active"',
            [token]
        );

        if (!result.success || result.data.length === 0) {
            return res.render('auth/login', {
                title: 'Login - HROS',
                error: 'Invalid or expired reset token.',
                success: null
            });
        }

        res.render('auth/reset-password', {
            title: 'Reset Password - HROS',
            token: token,
            error: null,
            success: null
        });

    } catch (error) {
        console.error('Reset password error:', error);
        res.render('auth/login', {
            title: 'Login - HROS',
            error: 'An error occurred. Please try again.',
            success: null
        });
    }
});

// Reset password process
router.post('/reset-password/:token', guestMiddleware, [
    body('password').isLength({ min: 6 }).withMessage('Password must be at least 6 characters long'),
    body('confirm_password').custom((value, { req }) => {
        if (value !== req.body.password) {
            throw new Error('Password confirmation does not match password');
        }
        return true;
    })
], async (req, res) => {
    try {
        const errors = validationResult(req);
        if (!errors.isEmpty()) {
            return res.render('auth/reset-password', {
                title: 'Reset Password - HROS',
                token: req.params.token,
                error: errors.array()[0].msg,
                success: null
            });
        }

        const { token } = req.params;
        const { password } = req.body;

        // Check if token is valid
        const result = await executeQuery(
            'SELECT id FROM users WHERE reset_token = ? AND reset_token_expiry > NOW() AND status = "active"',
            [token]
        );

        if (!result.success || result.data.length === 0) {
            return res.render('auth/login', {
                title: 'Login - HROS',
                error: 'Invalid or expired reset token.',
                success: null
            });
        }

        const user = result.data[0];

        // Hash new password
        const hashedPassword = await bcrypt.hash(password, 12);

        // Update password and clear reset token
        await executeQuery(
            'UPDATE users SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE id = ?',
            [hashedPassword, user.id]
        );

        res.render('auth/login', {
            title: 'Login - HROS',
            error: null,
            success: 'Password has been reset successfully. You can now login with your new password.'
        });

    } catch (error) {
        console.error('Reset password error:', error);
        res.render('auth/reset-password', {
            title: 'Reset Password - HROS',
            token: req.params.token,
            error: 'An error occurred. Please try again.',
            success: null
        });
    }
});

module.exports = router;