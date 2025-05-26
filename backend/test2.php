<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';

// Test 1: Database connection
echo "Test 1: Database Connection\n";
try {
    $db = \Ibu\Web\Config\Database::connect();
    echo "✓ Database connected successfully\n\n";
} catch (\Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n\n";
}

// Test 2: Create TeamDao
echo "Test 2: TeamDao\n";
try {
    $teamDao = new \Ibu\Web\Dao\TeamDao();
    echo "✓ TeamDao created successfully\n\n";
} catch (\Exception $e) {
    echo "✗ TeamDao error: " . $e->getMessage() . "\n\n";
}

// Test 3: Create TeamService
echo "Test 3: TeamService\n";
try {
    $teamService = new \Ibu\Web\Services\TeamService();
    echo "✓ TeamService created successfully\n\n";
} catch (\Exception $e) {
    echo "✗ TeamService error: " . $e->getMessage() . "\n\n";
}

// Test 4: ServiceManager
echo "Test 4: ServiceManager\n";
try {
    $sm = \Ibu\Web\Services\ServiceManager::getInstance();
    $teamService = $sm->get('team');
    echo "✓ ServiceManager works\n\n";
} catch (\Exception $e) {
    echo "✗ ServiceManager error: " . $e->getMessage() . "\n\n";
}

// Test 5: Flight
echo "Test 5: Flight\n";
try {
    Flight::route('GET /hello', function() {
        echo "Hello World!";
    });
    echo "✓ Flight works\n\n";
} catch (\Exception $e) {
    echo "✗ Flight error: " . $e->getMessage() . "\n\n";
}