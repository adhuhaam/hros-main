# PostgreSQL Setup Instructions for HROS

## Quick Setup Guide

Follow these steps to configure HROS with PostgreSQL:

### 1. Create Database

First, create the PostgreSQL database:

```bash
# Option A: Using psql command line
createdb hros_db

# Option B: Using SQL
psql -U postgres -c "CREATE DATABASE hros_db;"
```

### 2. Create .env File

Create a `.env` file in the project root with this content:

```env
APP_NAME="HROS - Human Resource Operating System"
APP_ENV=local
APP_KEY=base64:8IBTNhNu1rZPmeN8MSEGRjf1FJmBf4gPT4Bnh6YKkI0=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost:8000

# PostgreSQL Database Configuration
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=hros_db
DB_USERNAME=postgres
DB_PASSWORD=

SESSION_DRIVER=database
SESSION_LIFETIME=120
CACHE_STORE=database
QUEUE_CONNECTION=database

MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@hros.com"
MAIL_FROM_NAME="${APP_NAME}"

AUTH_ALLOW_REGISTRATION=false
```

**Important**: Update `DB_PASSWORD` with your PostgreSQL password if you have one set.

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies (if needed)
npm install
```

### 4. Run Database Setup

```bash
# Generate application key
php artisan key:generate

# Clear config cache
php artisan config:clear

# Check database connection
php artisan tinker --execute="echo 'DB: ' . config('database.default');"

# Run fresh migrations with seeders
php artisan migrate:fresh --seed
```

### 5. Fix Missing Enum Columns (PostgreSQL Specific)

Since we're using PostgreSQL, we need to create the enum types and add the missing columns:

```bash
# Run the pending enum migration
php artisan migrate

# If there are issues, run this SQL manually in PostgreSQL:
```

```sql
-- Connect to your database and run these commands:

-- Create missing enum columns for employees table
ALTER TABLE employees ADD COLUMN IF NOT EXISTS employment_status VARCHAR(20);
ALTER TABLE employees ADD COLUMN IF NOT EXISTS salary_currency VARCHAR(10) DEFAULT 'MVR';
ALTER TABLE employees ADD COLUMN IF NOT EXISTS level VARCHAR(10) DEFAULT 'junior';
ALTER TABLE employees ADD COLUMN IF NOT EXISTS company VARCHAR(100) DEFAULT 'RASHEED CARPENTRY AND CONSTRUCTION PVT LTD';

-- Create missing enum columns for leaves table
ALTER TABLE leaves ADD COLUMN IF NOT EXISTS leave_type VARCHAR(20);
ALTER TABLE leaves ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending';

-- Create missing enum columns for attendance table
ALTER TABLE attendance ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Absent';
ALTER TABLE attendance ADD COLUMN IF NOT EXISTS check_in_status VARCHAR(20);
ALTER TABLE attendance ADD COLUMN IF NOT EXISTS check_out_status VARCHAR(20);

-- Create missing enum columns for loans table
ALTER TABLE loans ADD COLUMN IF NOT EXISTS loan_type VARCHAR(20);
ALTER TABLE loans ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Pending';

-- Create missing enum columns for warnings table
ALTER TABLE warnings ADD COLUMN IF NOT EXISTS warning_type VARCHAR(20);
ALTER TABLE warnings ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Active';
ALTER TABLE warnings ADD COLUMN IF NOT EXISTS severity_level VARCHAR(20) DEFAULT 'Medium';

-- Create missing enum columns for medical_records table
ALTER TABLE medical_records ADD COLUMN IF NOT EXISTS medical_type VARCHAR(20);
ALTER TABLE medical_records ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'Active';
```

### 6. Start the Application

```bash
# Start the development server
php artisan serve
```

Your application should now be running on http://localhost:8000

## Default Login Credentials

| Username       | Password    | Role            |
| -------------- | ----------- | --------------- |
| admin          | admin123    | Admin           |
| hrmanager      | hr123       | HR Manager      |
| hrofficer      | hr123       | HR Officer      |
| financemanager | finance123  | Finance Manager |
| employee       | employee123 | Employee        |

## Troubleshooting

### 1. Database Connection Issues

If you get connection errors:

```bash
# Check if PostgreSQL is running
sudo systemctl status postgresql  # Linux
brew services list | grep postgresql  # macOS

# Test connection manually
psql -U postgres -d hros_db -c "SELECT version();"
```

### 2. Missing Columns Errors

If you see "column does not exist" errors:

```bash
# Run the manual SQL commands above, or
# Create a custom migration:
php artisan make:migration add_missing_enum_columns_for_postgresql
```

### 3. Permission Errors

```bash
# Grant permissions in PostgreSQL
psql -U postgres -c "GRANT ALL PRIVILEGES ON DATABASE hros_db TO postgres;"
psql -U postgres -d hros_db -c "GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO postgres;"
```

### 4. Clear Caches

If configuration isn't updating:

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Production Notes

For production deployment:

1. Set `APP_ENV=production`
2. Set `APP_DEBUG=false`
3. Use strong database passwords
4. Enable SSL connections
5. Set up proper backup strategies
6. Configure PostgreSQL performance settings

## Need Help?

If you encounter issues:

1. Check the Laravel logs: `storage/logs/laravel.log`
2. Check PostgreSQL logs
3. Verify your `.env` configuration
4. Ensure PostgreSQL service is running
5. Check database permissions

The role-based authentication system includes 79 different permissions across all modules, providing granular access control for your HR system.
