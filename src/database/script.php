<?php

use App\Models\Seeder;
use Models\Migration;
use Dotenv\Dotenv;
require_once __DIR__."/../../vendor/autoload.php";

// Load environment variables using vlucas/phpdotenv
$dotenv = Dotenv::createImmutable(__DIR__."/../../");
$dotenv->load();

// Populate the environment for getenv() to work
foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
}

$command = $argv[1] ?? null;
$action = $argv[2] ?? null;

$migration = new Migration();
$seeder = new Seeder();

switch ($command) {
    case 'make':
        if ($action === 'migration') {
            $migration->make($argv[3] ?? 'new_migration');
        } 
        elseif ($action === 'seeder') {
            $seeder->make($argv[3] ?? 'new_seeder');
        } 
        else {
            echo "Usage: php script.php make migration|seeder [name]\n";
        }
        break;

    case 'migrate':
        $migration->migrate();
        break;

    case 'seed':
        $seeder->seed();
        break;

    default:
        echo "Usage: php script.php make|migrate|seed [name]\n";
        break;
}
