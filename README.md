# Laravel App Migration Documentation

## Overview
This document describes the process of moving Laravel application contents from a subdirectory to the root directory of the project.

## Migration Process

### Prerequisites
- Ensure you have a Laravel application in a subdirectory (e.g., `laravel-app/`)
- Make sure you have proper backups before proceeding
- Verify that the target root directory is clean or that you're prepared to merge files

### Steps to Move Laravel App Contents to Root

1. **Locate the Laravel App Directory**
   ```bash
   find . -name "laravel-app" -type d
   ```

2. **Move All Contents to Root**
   ```bash
   # Move all files and directories from laravel-app to root
   mv laravel-app/* .
   mv laravel-app/.* . 2>/dev/null || true  # Move hidden files (ignore errors for . and ..)
   ```

3. **Remove the Empty Directory**
   ```bash
   rmdir laravel-app
   ```

4. **Verify the Migration**
   ```bash
   # Check if Laravel files are now in root
   ls -la artisan composer.json app/ config/ database/ resources/ routes/ storage/ vendor/
   ```

### Important Considerations

#### File Conflicts
- If there are existing files in the root directory with the same names as Laravel files, you'll need to resolve conflicts manually
- Common conflicts might include:
  - `composer.json`
  - `.env` files
  - `index.php`
  - Configuration files

#### Environment Configuration
- Update `.env` file paths if necessary
- Ensure database connections and other configurations point to correct locations
- Update any hardcoded paths in your application

#### Dependencies
- Run `composer install` after migration to ensure all dependencies are properly installed
- Update any autoload configurations if needed

#### Web Server Configuration
- Update web server configuration (Apache `.htaccess`, Nginx config, etc.)
- Ensure the document root points to the correct location
- Update any virtual host configurations

### Post-Migration Checklist

- [ ] Verify Laravel artisan commands work: `php artisan --version`
- [ ] Test application functionality
- [ ] Check database connections
- [ ] Verify file uploads and storage paths
- [ ] Test authentication and sessions
- [ ] Check log files for errors
- [ ] Update deployment scripts if applicable

### Troubleshooting

#### Common Issues

1. **Permission Errors**
   ```bash
   # Fix storage and cache permissions
   chmod -R 775 storage/
   chmod -R 775 bootstrap/cache/
   ```

2. **Autoload Issues**
   ```bash
   # Regenerate autoload files
   composer dump-autoload
   ```

3. **Cache Issues**
   ```bash
   # Clear all caches
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Path Issues**
   - Check `config/app.php` for correct paths
   - Verify asset compilation paths
   - Update any hardcoded URLs or paths

### Rollback Plan

If issues arise, you can rollback by:
1. Restoring from backup
2. Or moving files back to the original subdirectory structure

## Project Structure After Migration

```
/
├── app/                    # Laravel application logic
├── bootstrap/              # Framework bootstrap files
├── config/                 # Configuration files
├── database/               # Database migrations and seeders
├── public/                 # Web server document root
├── resources/              # Views, assets, language files
├── routes/                 # Route definitions
├── storage/                # Application storage
├── tests/                  # Test files
├── vendor/                 # Composer dependencies
├── artisan                 # Laravel command-line tool
├── composer.json           # Composer configuration
├── .env                    # Environment configuration
└── README.md              # This documentation
```

## Notes

- This migration process should be performed in a development environment first
- Always test thoroughly before deploying to production
- Consider using version control (Git) to track changes
- Document any custom configurations or modifications made during the process

## Support

If you encounter issues during the migration process:
1. Check Laravel documentation for troubleshooting guides
2. Review server error logs
3. Verify file permissions and ownership
4. Ensure all dependencies are properly installed