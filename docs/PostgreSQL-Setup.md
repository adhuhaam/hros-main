# PostgreSQL Setup for HROS

This document provides instructions for setting up PostgreSQL with the HROS (Human Resource Operating System) application.

## Prerequisites

1. PostgreSQL 12+ installed on your system
2. PHP PostgreSQL extension (php-pgsql)
3. Composer dependencies installed

## Database Setup

### 1. Create Database

```sql
-- Connect to PostgreSQL as superuser
sudo -u postgres psql

-- Create database
CREATE DATABASE hros_db;

-- Create user (optional)
CREATE USER hros_user WITH PASSWORD 'your_password';

-- Grant privileges
GRANT ALL PRIVILEGES ON DATABASE hros_db TO hros_user;

-- Exit
\q
```

### 2. Environment Configuration

Create a `.env` file in your project root with the following PostgreSQL configuration:

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

# Session Configuration
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Cache Configuration
CACHE_STORE=database

# Queue Configuration
QUEUE_CONNECTION=database

# Mail Configuration
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@hros.com"
MAIL_FROM_NAME="${APP_NAME}"

# Authentication Settings
AUTH_ALLOW_REGISTRATION=false
```

### 3. Run Migrations

```bash
# Generate application key
php artisan key:generate

# Run migrations with seeders
php artisan migrate:fresh --seed
```

## PostgreSQL-Specific Features

### Custom ENUM Types

The application uses PostgreSQL custom ENUM types for better data integrity:

- `employment_status_enum`: Employee employment statuses
- `leave_type_enum`: Types of leaves
- `attendance_status_enum`: Attendance statuses
- `loan_status_enum`: Loan statuses
- And many more...

### Database Optimizations

1. **Indexes**: Proper indexing on frequently queried columns
2. **Foreign Keys**: Referential integrity with CASCADE options
3. **Constraints**: Data validation at database level
4. **JSONB**: For flexible permission storage in roles table

## Troubleshooting

### Common Issues

1. **Connection Error**

    ```
    SQLSTATE[08006] [7] could not connect to server
    ```

    - Check if PostgreSQL service is running
    - Verify connection details in `.env`

2. **Permission Denied**

    ```
    SQLSTATE[42501] permission denied for table
    ```

    - Ensure user has proper privileges
    - Grant necessary permissions

3. **ENUM Type Errors**
    ```
    SQLSTATE[42704] type does not exist
    ```
    - Run migrations in correct order
    - Ensure enum migration runs first

### Performance Tuning

For production environments, consider these PostgreSQL settings:

```sql
-- Increase shared buffers
shared_buffers = 256MB

-- Increase work memory
work_mem = 4MB

-- Enable query optimization
random_page_cost = 1.1
```

## Default Users

After running seeders, these users will be available:

| Username       | Password    | Role            |
| -------------- | ----------- | --------------- |
| admin          | admin123    | Admin           |
| hrmanager      | hr123       | HR Manager      |
| hrofficer      | hr123       | HR Officer      |
| financemanager | finance123  | Finance Manager |
| employee       | employee123 | Employee        |

## Backup and Restore

### Backup

```bash
pg_dump -h localhost -U postgres hros_db > hros_backup.sql
```

### Restore

```bash
psql -h localhost -U postgres hros_db < hros_backup.sql
```

## Production Considerations

1. **SSL Connection**: Enable SSL for production
2. **Connection Pooling**: Use pgbouncer for connection management
3. **Regular Backups**: Implement automated backup strategy
4. **Monitoring**: Set up PostgreSQL monitoring
5. **Security**: Use strong passwords and limit access

## Support

For issues related to PostgreSQL setup, please refer to:

- [PostgreSQL Documentation](https://www.postgresql.org/docs/)
- [Laravel Database Documentation](https://laravel.com/docs/database)
