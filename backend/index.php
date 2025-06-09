<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer autoload
require_once __DIR__ . '/vendor/autoload.php';

// Try to load .env file with error handling
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    error_log("ENV loaded successfully. JWT_SECRET exists: " . (isset($_ENV['JWT_SECRET']) ? 'YES' : 'NO'));
} catch (Exception $e) {
    error_log("Error loading .env: " . $e->getMessage());
    // Fallback for testing
    $_ENV['JWT_SECRET'] = 'MedinRodjendan1';
}

// CORS Headers - Add this BEFORE initializing routes
Flight::before('start', function(&$params, &$output){
    // Allow from your frontend origin (VS Code Live Server)
    header('Access-Control-Allow-Origin: http://127.0.0.1:5500');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Access-Control-Allow-Credentials: true');
    
    // Handle preflight requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        // Return 200 OK for preflight
        Flight::halt(200);
        exit();
    }
});

// Get ServiceManager instance
$serviceManager = \Ibu\Web\Services\ServiceManager::getInstance();

// Only register services that aren't already handled by the ServiceManager
$serviceManager->register('user', function() {
    return new \Ibu\Web\Services\UserService();
});

// Initialize all routes
try {
    new \Ibu\Web\Routes\AuthRoute(Flight::app(), $serviceManager);
    new \Ibu\Web\Routes\UserRoute(Flight::app(), $serviceManager);
    new \Ibu\Web\Routes\TeamRoute(Flight::app(), $serviceManager);
    new \Ibu\Web\Routes\PlayerRoute(Flight::app(), $serviceManager);
    new \Ibu\Web\Routes\VenueRoute(Flight::app(), $serviceManager);
    new \Ibu\Web\Routes\MatchRoute(Flight::app(), $serviceManager);
    new \Ibu\Web\Routes\StatisticsRoute(Flight::app(), $serviceManager);
} catch (\Exception $e) {
    die("Error initializing routes: " . $e->getMessage());
}

Flight::start();