#!/bin/bash

# HROS Laravel Migration Setup Script
# This script sets up the Laravel project for HROS migration

echo "🚀 Starting HROS Laravel Migration Setup..."
echo "=========================================="

# Check if PHP is installed
if ! command -v php &> /dev/null; then
    echo "❌ PHP is not installed. Please install PHP 8.1+ first."
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "✅ PHP version: $PHP_VERSION"

# Check if Composer is installed
if ! command -v composer &> /dev/null; then
    echo "❌ Composer is not installed. Installing Composer..."
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    echo "✅ Composer installed successfully"
else
    echo "✅ Composer is already installed"
fi

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Copy environment file
if [ ! -f .env ]; then
    echo "📝 Creating .env file..."
    cp .env.example .env
    echo "✅ .env file created"
else
    echo "✅ .env file already exists"
fi

# Generate application key
echo "🔑 Generating application key..."
php artisan key:generate

# Set proper permissions
echo "🔐 Setting proper permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Create storage links
echo "🔗 Creating storage links..."
php artisan storage:link

# Install Node.js dependencies (if Node.js is available)
if command -v npm &> /dev/null; then
    echo "📦 Installing Node.js dependencies..."
    npm install
    npm run build
    echo "✅ Node.js dependencies installed"
else
    echo "⚠️  Node.js not found. Skipping frontend asset compilation."
fi

# Database setup
echo "🗄️  Database setup..."
echo "Please configure your database settings in .env file"
echo "Then run: php artisan migrate"
echo "And: php artisan db:seed"

# Create necessary directories
echo "📁 Creating necessary directories..."
mkdir -p storage/app/public/documents
mkdir -p storage/app/public/employees
mkdir -p storage/app/public/leaves
mkdir -p storage/app/public/payroll
mkdir -p storage/logs

# Set permissions for upload directories
chmod -R 775 storage/app/public
chown -R www-data:www-data storage/app/public

# Create cache directories
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views

# Set permissions for framework directories
chmod -R 775 storage/framework
chown -R www-data:www-data storage/framework

echo ""
echo "🎉 HROS Laravel Migration Setup Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Configure your database settings in .env file"
echo "2. Run: php artisan migrate"
echo "3. Run: php artisan db:seed"
echo "4. Start the development server: php artisan serve"
echo ""
echo "For more information, see README.md and MIGRATION_PLAN.md"
echo ""
echo "Happy coding! 🚀"