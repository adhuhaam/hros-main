const express = require('express');
const session = require('express-session');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const path = require('path');
const rateLimit = require('express-rate-limit');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

// Security middleware
app.use(helmet({
    contentSecurityPolicy: {
        directives: {
            defaultSrc: ["'self'"],
            styleSrc: ["'self'", "'unsafe-inline'", "https://cdn.tailwindcss.com", "https://cdn.jsdelivr.net"],
            scriptSrc: ["'self'", "'unsafe-inline'", "https://cdn.tailwindcss.com", "https://cdn.jsdelivr.net"],
            imgSrc: ["'self'", "data:", "https:"],
            fontSrc: ["'self'", "https:"],
        },
    },
}));

// Rate limiting
const limiter = rateLimit({
    windowMs: 15 * 60 * 1000, // 15 minutes
    max: 100, // limit each IP to 100 requests per windowMs
    message: 'Too many requests from this IP, please try again later.'
});
app.use('/api/', limiter);

// Body parsing middleware
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Session configuration
app.use(session({
    secret: process.env.SESSION_SECRET || 'hros-secret-key',
    resave: false,
    saveUninitialized: false,
    cookie: {
        secure: process.env.NODE_ENV === 'production',
        httpOnly: true,
        maxAge: 24 * 60 * 60 * 1000 // 24 hours
    }
}));

// CORS configuration
app.use(cors({
    origin: process.env.ALLOWED_ORIGINS ? process.env.ALLOWED_ORIGINS.split(',') : ['http://localhost:3000'],
    credentials: true
}));

// Logging middleware
app.use(morgan('combined'));

// Static files
app.use(express.static(path.join(__dirname, 'public')));

// View engine setup (using EJS for Laravel-like templating)
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, 'views'));

// Database connection
const db = require('./app/config/database');

// Routes
const authRoutes = require('./app/routes/auth');
const dashboardRoutes = require('./app/routes/dashboard');
const employeeRoutes = require('./app/routes/employees');
const leaveRoutes = require('./app/routes/leaves');
const payrollRoutes = require('./app/routes/payroll');
const attendanceRoutes = require('./app/routes/attendance');
const passportRoutes = require('./app/routes/passport');
const visaRoutes = require('./app/routes/visa');
const medicalRoutes = require('./app/routes/medical');
const loanRoutes = require('./app/routes/loan');
const settingsRoutes = require('./app/routes/settings');
const apiRoutes = require('./app/routes/api');

// Middleware
const authMiddleware = require('./app/middleware/auth');
const roleMiddleware = require('./app/middleware/role');

// Route middleware
app.use('/auth', authRoutes);
app.use('/dashboard', authMiddleware, dashboardRoutes);
app.use('/employees', authMiddleware, employeeRoutes);
app.use('/leaves', authMiddleware, leaveRoutes);
app.use('/payroll', authMiddleware, payrollRoutes);
app.use('/attendance', authMiddleware, attendanceRoutes);
app.use('/passport', authMiddleware, passportRoutes);
app.use('/visa', authMiddleware, visaRoutes);
app.use('/medical', authMiddleware, medicalRoutes);
app.use('/loan', authMiddleware, loanRoutes);
app.use('/settings', authMiddleware, settingsRoutes);
app.use('/api', apiRoutes);

// Home route
app.get('/', (req, res) => {
    if (req.session.user) {
        res.redirect('/dashboard');
    } else {
        res.render('auth/login', { 
            title: 'Login - HROS',
            error: null,
            success: null
        });
    }
});

// Error handling middleware
app.use((err, req, res, next) => {
    console.error(err.stack);
    res.status(500).render('error', {
        title: 'Error',
        message: process.env.NODE_ENV === 'development' ? err.message : 'Something went wrong!'
    });
});

// 404 handler
app.use((req, res) => {
    res.status(404).render('error', {
        title: 'Page Not Found',
        message: 'The page you are looking for does not exist.'
    });
});

// Start server
app.listen(PORT, () => {
    console.log(`HROS Server running on port ${PORT}`);
    console.log(`Environment: ${process.env.NODE_ENV || 'development'}`);
});

module.exports = app;