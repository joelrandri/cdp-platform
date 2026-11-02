<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Database\Connection\ConnectData;
use Database\Seeders\DatabaseSeeder;

//retrieve Connection
$pdo = ConnectData::getConnection();
// Exécuter les seeders
$seeder = new DatabaseSeeder($pdo);
$seeder->run();

echo "Database seeding completed successfully!";
