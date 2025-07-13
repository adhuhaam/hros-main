# HR Management System (Laravel)

A comprehensive Human Resource Management System built with Laravel, featuring employee management, leave management, attendance tracking, loan management, and more.

## Features

### Core Modules
- **Employee Management**: Complete employee lifecycle management
- **Leave Management**: Request, approve, and track employee leaves
- **Attendance Tracking**: Daily attendance monitoring with check-in/check-out
- **Loan Management**: Employee loan processing and installment tracking
- **Document Management**: Store and manage employee documents
- **Medical Records**: Track employee medical information
- **Warning System**: Manage employee warnings and disciplinary actions

### User Roles & Permissions
- **Admin**: Full system access
- **Information Officer**: Employee and leave management
- **Xpat Officer**: Document and visa management
- **Leave Officer**: Leave approval and management
- **HR Manager**: Comprehensive HR functions
- **Payroll Officer**: Salary and loan management
- **Supervisor**: Team management
- **Other Staff**: Limited access to personal information
- **Reception**: Basic employee and accommodation management

### Dashboard Features
- Role-based dashboards with relevant statistics
- Real-time data visualization
- Quick action buttons
- Recent activity feeds
- Responsive design for mobile devices

## Technology Stack

- **Backend**: Laravel 10.x
- **Frontend**: Tailwind CSS, Alpine.js
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **File Storage**: Laravel Storage
- **PDF Generation**: DomPDF
- **Excel Import/Export**: PhpSpreadsheet
- **Icons**: Font Awesome, Tabler Icons

## Installation

### Prerequisites
- PHP 8.1 or higher
- Composer
- MySQL 5.7 or higher
- Node.js and NPM (for asset compilation)

### Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd hros-laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure database**
   Edit `.env` file with your database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=hros_laravel
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database (optional)**
   ```bash
   php artisan db:seed
   ```

8. **Create storage link**
   ```bash
   php artisan storage:link
   ```

9. **Compile assets**
   ```bash
   npm run dev
   ```

10. **Start the development server**
    ```bash
    php artisan serve
    ```

## Database Structure

### Core Tables
- `users` - System users and authentication
- `employees` - Employee information and details
- `leaves` - Leave requests and approvals
- `attendance` - Daily attendance records
- `loans` - Employee loan information
- `loan_installments` - Loan payment tracking
- `medical_records` - Employee medical information
- `warnings` - Employee warnings and disciplinary actions
- `documents` - Employee document storage
- `holidays` - Company holiday calendar
- `notices` - Company notices and announcements

### Key Relationships
- Users can have one employee record
- Employees can have multiple leaves, attendance records, loans, etc.
- All modules are interconnected for comprehensive reporting

## Usage

### Initial Setup
1. Create an admin user through the database seeder or manually
2. Log in with admin credentials
3. Configure system settings
4. Add departments, positions, and other master data
5. Start adding employees

### Employee Management
- Add new employees with complete information
- Upload profile photos and documents
- Track employment status changes
- Generate employee reports

### Leave Management
- Employees can submit leave requests
- Managers can approve/reject leaves
- Track leave balances and history
- Generate leave reports

### Attendance Tracking
- Daily check-in/check-out system
- Overtime calculation
- Attendance reports by date range
- Absence tracking

### Loan Management
- Process loan applications
- Calculate installments
- Track payment progress
- Generate loan reports

## API Endpoints

The system includes RESTful API endpoints for:
- Employee CRUD operations
- Leave management
- Attendance tracking
- Loan processing
- Document management

## Security Features

- Role-based access control
- CSRF protection
- SQL injection prevention
- XSS protection
- File upload validation
- Secure password hashing
- Session management

## Reporting

The system provides comprehensive reporting for:
- Employee statistics
- Attendance reports
- Leave analysis
- Loan summaries
- Payroll reports
- Custom date range reports

## Customization

### Adding New Modules
1. Create migration for the new table
2. Create model with relationships
3. Create controller with CRUD operations
4. Add routes to `web.php`
5. Create views for the module
6. Update sidebar navigation

### Modifying Existing Features
- Models are designed with relationships and scopes
- Controllers follow Laravel conventions
- Views use Blade templating with Tailwind CSS
- Easy to extend and modify

## Deployment

### Production Setup
1. Set `APP_ENV=production` in `.env`
2. Configure production database
3. Set up file storage (AWS S3 recommended)
4. Configure email settings
5. Set up SSL certificate
6. Configure web server (Apache/Nginx)

### Performance Optimization
- Enable Laravel caching
- Use Redis for sessions and cache
- Optimize database queries
- Use CDN for static assets
- Enable compression

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests if applicable
5. Submit a pull request

## License

This project is licensed under the MIT License.

## Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## Changelog

### Version 1.0.0
- Initial release
- Core HR modules
- Role-based access control
- Responsive design
- Basic reporting

## Roadmap

### Upcoming Features
- Advanced reporting with charts
- Mobile app development
- Integration with payroll systems
- Advanced workflow automation
- Multi-language support
- Advanced analytics dashboard