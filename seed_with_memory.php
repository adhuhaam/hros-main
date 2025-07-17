<?php

// Increase memory limit for seeding
ini_set('memory_limit', '512M');

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Run the seeder
use Database\Seeders\TestDataSeeder;

$seeder = new TestDataSeeder();
$seeder->run();

echo "Seeding completed successfully!\n"; 