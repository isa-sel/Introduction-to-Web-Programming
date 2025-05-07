<?php
// Display all errors for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include DAO classes
require_once 'dao/TeamDao.php';
require_once 'dao/PlayerDao.php';
require_once 'dao/VenueDao.php';
require_once 'dao/MatchDao.php';
require_once 'dao/UserDao.php';
require_once 'dao/StatisticsDao.php';

// Create test objects
try {
    echo "<h2>Testing DAO Layer</h2>";
    
    // Test UserDao
    echo "<h3>Testing UserDao</h3>";
    $userDao = new UserDao();
    
    // Check if admin user already exists
    $existingAdmin = $userDao->getByUsername('admin');
    
    if ($existingAdmin) {
        // Delete existing admin user if it exists
        $userDao->delete($existingAdmin['id']);
        echo "Existing admin user deleted.<br>";
    }
    
    // Insert admin user
    $adminId = $userDao->insert([
        'username' => 'admin',
        'email' => 'admin@example.com',
        'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
        'full_name' => 'Administrator',
        'role' => 'admin'
    ]);
    echo "Created admin user with ID: " . $adminId . "<br>";
    
    // Get all users
    $users = $userDao->getAll();
    echo "Number of users: " . count($users) . "<br>";
    
    // Test TeamDao
    echo "<h3>Testing TeamDao</h3>";
    $teamDao = new TeamDao();
    
    // Insert team
    $teamId = $teamDao->insert([
        'name' => 'Test Team',
        'location' => 'Test City',
        'founded_year' => 2023,
        'category' => 'Senior Men',
        'description' => 'A test team'
    ]);
    echo "Created team with ID: " . $teamId . "<br>";
    
    // Get all teams
    $teams = $teamDao->getAll();
    echo "Number of teams: " . count($teams) . "<br>";
    
} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
}


// Simple test to verify PHP is working
echo json_encode([
    'status' => 'success',
    'message' => 'PHP is working',
    'server_info' => [
        'request_uri' => $_SERVER['REQUEST_URI'],
        'script_name' => $_SERVER['SCRIPT_NAME'],
        'php_version' => phpversion(),
        'time' => date('Y-m-d H:i:s')
    ]
]);


?>


