<?php

use App\Libraries\App;
use App\Libraries\Helpers;
use Dotenv\Dotenv;

// Check if it is a cross-origin preflight request
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Start the session
session_start();
// Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';


// Load environment variables using vlucas/phpdotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Populate the environment for getenv() to work
foreach ($_ENV as $key => $value) {
    putenv("$key=$value");
}
define("APP", getenv('APP_NAME'));
define('DOMAIN', getenv("APP_URL"));
$_SESSION[APP] = $_SESSION[APP] ?? new stdClass;


// Set up custom error handling and logging
set_error_handler([Helpers::class, 'customErrorHandler']);
set_exception_handler([Helpers::class, 'customExceptionHandler']);
register_shutdown_function([Helpers::class, 'customShutdownFunction']);

// Initialize the application
$app = new App;
$app->serve($_SERVER);
