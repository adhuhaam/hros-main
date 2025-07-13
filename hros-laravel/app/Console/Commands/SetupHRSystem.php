<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class SetupHRSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hr:setup {--fresh : Fresh installation with database reset}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup the HR Management System';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Setting up HR Management System...');

        // Check if .env file exists
        if (!File::exists(base_path('.env'))) {
            $this->error('.env file not found. Please copy .env.example to .env and configure your database settings.');
            return 1;
        }

        // Generate application key
        $this->info('Generating application key...');
        Artisan::call('key:generate');

        // Run migrations
        if ($this->option('fresh')) {
            $this->info('Running fresh migrations...');
            Artisan::call('migrate:fresh');
        } else {
            $this->info('Running migrations...');
            Artisan::call('migrate');
        }

        // Seed the database
        $this->info('Seeding database...');
        Artisan::call('db:seed');

        // Create storage link
        $this->info('Creating storage link...');
        Artisan::call('storage:link');

        // Create necessary directories
        $this->info('Creating storage directories...');
        $directories = [
            'app/public/employees/photos',
            'app/public/documents',
            'app/public/medical-records',
        ];

        foreach ($directories as $directory) {
            $path = storage_path($directory);
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                $this->line("Created directory: {$directory}");
            }
        }

        // Clear caches
        $this->info('Clearing caches...');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');

        $this->info('✅ HR Management System setup completed successfully!');
        $this->newLine();
        
        $this->info('📋 Login Credentials:');
        $this->table(
            ['Role', 'Username', 'Password'],
            [
                ['Admin', 'admin', 'password123'],
                ['HR Manager', 'hrmanager', 'password123'],
                ['Information Officer', 'infoofficer', 'password123'],
                ['Leave Officer', 'leaveofficer', 'password123'],
                ['Payroll Officer', 'payrollofficer', 'password123'],
                ['Staff', 'staff', 'password123'],
            ]
        );

        $this->newLine();
        $this->info('🌐 Start the development server:');
        $this->line('php artisan serve');
        
        $this->newLine();
        $this->info('🔗 Access the application at: http://localhost:8000');
        
        return 0;
    }
}