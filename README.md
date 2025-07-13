# Laravel Setup and Migration Guide

This project contains a Laravel application set up in the `laravel-app` directory.

## Steps Performed

1. **Composer Installation**
   - Downloaded Composer using the official installer script.
   - Verified Composer installation with `php composer.phar --version`.

2. **Laravel Installation**
   - Installed Laravel using Composer:
     ```bash
     php composer.phar create-project laravel/laravel laravel-app
     ```
   - This created a new Laravel project in the `laravel-app` directory.

3. **Environment Setup**
   - Laravel automatically copied `.env.example` to `.env`.
   - Application key generated automatically.
   - **Database configuration updated for MySQL (Namecheap hosting)**.

4. **Database & Migrations**
   - The project is configured to use a MySQL database hosted on Namecheap.
   - Update the `.env` file in `laravel-app` with your MySQL credentials:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=your-mysql-host
     DB_PORT=3306
     DB_DATABASE=your_database_name
     DB_USERNAME=your_database_user
     DB_PASSWORD=your_database_password
     ```
   - Run migrations with:
     ```bash
     cd laravel-app
     php artisan migrate
     ```

## Next Steps
- Configure your database connection in `laravel-app/.env` as needed.
- Add or modify migrations in `laravel-app/database/migrations/`.
- Run `php artisan migrate` after making changes to migrations.

---

*This README will be updated as more steps are performed.*